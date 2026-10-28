<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendLinkDownAlert;
use App\Models\Artwork;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Bandeja de reportes de moderación con filtros y paginación (RF-11).
     */
    public function index(Request $request): View
    {
        $query = Report::with(['user', 'artwork.user'])->latest();

        // Filtro por estado
        $status = $request->get('status', 'pending');
        if ($status !== 'all' && in_array($status, ['pending', 'resolved'])) {
            $query->where('status', $status);
        }

        // Filtro por motivo
        if ($reason = $request->get('reason')) {
            $query->where('reason', $reason);
        }

        // Búsqueda por término (título de obra o usuario)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('artwork', function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%");
                })->orWhereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $reports = $query->paginate(12)->withQueryString();

        $counts = [
            'pending'  => Report::where('status', 'pending')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'all'      => Report::count(),
        ];

        return view('admin.reports.index', compact('reports', 'counts', 'status'));
    }

    /**
     * Ficha detallada de moderación del reporte.
     */
    public function show(int|string $id): View
    {
        $report = Report::with(['user', 'artwork.user'])->findOrFail($id);

        // Otros reportes asociados a la misma obra
        $otherReports = Report::with('user')
            ->where('artwork_id', $report->artwork_id)
            ->where('id', '!=', $report->id)
            ->latest()
            ->get();

        return view('admin.reports.show', compact('report', 'otherReports'));
    }

    /**
     * Actualiza la nota interna del administrador sin alterar el estado.
     */
    public function updateNote(Request $request, int|string $id): RedirectResponse
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $report->update([
            'admin_note' => $validated['admin_note'],
        ]);

        return back()->with('success', 'Nota interna actualizada correctamente.');
    }

    /**
     * Resuelve el reporte manualmente sin ocultar la obra.
     */
    public function resolve(Request $request, int|string $id): RedirectResponse
    {
        $report = Report::findOrFail($id);

        $report->update([
            'status'     => 'resolved',
            'admin_note' => $request->input('admin_note', $report->admin_note),
        ]);

        return back()->with('success', 'El reporte ha sido marcado como resuelto.');
    }

    /**
     * Desestima el reporte si se considera infundado o spam.
     */
    public function dismiss(int|string $id): RedirectResponse
    {
        $report = Report::findOrFail($id);

        $note = $report->admin_note ? $report->admin_note . " | [Desestimado por Admin]" : "[Reporte desestimado por el administrador]";

        $report->update([
            'status'     => 'resolved',
            'admin_note' => $note,
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Reporte desestimado.');
    }

    /**
     * Ejecuta la moderación por enlace caído (Regla RN-02):
     * 1. Oculta la obra (`status = 'hidden'`).
     * 2. Resuelve el reporte (`status = 'resolved'`).
     * 3. Despacha Job asíncrono para enviar correo al artista autor.
     */
    public function sendLinkDownAlert(Request $request, int|string $id): RedirectResponse
    {
        $report = Report::with(['artwork.user'])->findOrFail($id);
        $artwork = $report->artwork;

        if (!$artwork) {
            return back()->with('error', 'La obra vinculada no existe o ya fue eliminada permanentemente.');
        }

        // 1. Oculta la obra (RN-02)
        $artwork->update(['status' => 'hidden']);

        // 2. Guarda nota y marca reporte como resuelto
        $customNote = $request->input('admin_note', $report->admin_note);
        $report->update([
            'status'     => 'resolved',
            'admin_note' => $customNote ?: 'Enlace caído confirmado. Publicación ocultada preventivamente.',
        ]);

        // 3. Despacha Job asíncrono al artista autor si existe
        if ($artwork->user) {
            SendLinkDownAlert::dispatch($artwork, $artwork->user, $customNote);
        }

        return redirect()->route('admin.reports.index')->with(
            'success',
            "La obra \"{$artwork->title}\" ha sido ocultada y se ha encolado la alerta por correo al artista autor."
        );
    }
}
