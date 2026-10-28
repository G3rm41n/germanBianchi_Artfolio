<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LinkDownAlert;
use App\Models\Artwork;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display moderation reports with status/reason filtering and search.
     */
    public function index(Request $request): View
    {
        $query = Report::with(['user', 'artwork.user'])->latest();

        // Status filter
        $status = $request->get('status', 'pending');
        if ($status !== 'all' && in_array($status, ['pending', 'resolved'])) {
            $query->where('status', $status);
        }

        // Reason filter
        if ($reason = $request->get('reason')) {
            $query->where('reason', $reason);
        }

        // Search term (artwork title or reporting user)
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
     * Show report details.
     */
    public function show(int|string $id): View
    {
        $report = Report::with(['user', 'artwork.user'])->findOrFail($id);

        $otherReports = Report::with('user')
            ->where('artwork_id', $report->artwork_id)
            ->where('id', '!=', $report->id)
            ->latest()
            ->get();

        return view('admin.reports.show', compact('report', 'otherReports'));
    }

    /**
     * Update admin internal note.
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

        return back()->with('success', 'Admin note updated successfully.');
    }

    /**
     * Manually mark report as resolved without hiding artwork.
     */
    public function resolve(Request $request, int|string $id): RedirectResponse
    {
        $report = Report::findOrFail($id);

        $report->update([
            'status'     => 'resolved',
            'admin_note' => $request->input('admin_note', $report->admin_note),
        ]);

        return back()->with('success', 'Report marked as resolved.');
    }

    /**
     * Dismiss the report.
     */
    public function dismiss(int|string $id): RedirectResponse
    {
        $report = Report::findOrFail($id);

        $note = $report->admin_note ? $report->admin_note . " | [Dismissed by Admin]" : "[Report dismissed by admin]";

        $report->update([
            'status'     => 'resolved',
            'admin_note' => $note,
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Report dismissed.');
    }

    /**
     * Handle link down moderation:
     * 1. Hide the artwork.
     * 2. Mark report as resolved.
     * 3. Send email notification to artist.
     */
    public function sendLinkDownAlert(Request $request, int|string $id): RedirectResponse
    {
        $report = Report::with(['artwork.user'])->findOrFail($id);
        $artwork = $report->artwork;

        if (!$artwork) {
            return back()->with('error', 'The linked artwork does not exist or was deleted.');
        }

        // Hide the artwork
        $artwork->update(['status' => 'hidden']);

        // Save note and resolve report
        $customNote = $request->input('admin_note', $report->admin_note);
        $report->update([
            'status'     => 'resolved',
            'admin_note' => $customNote ?: 'Broken link confirmed. Artwork hidden preventively.',
        ]);

        // Send email to artist if exists
        if ($artwork->user) {
            Mail::to($artwork->user->email)->send(new LinkDownAlert($artwork, $artwork->user, $customNote));
        }

        return redirect()->route('admin.reports.index')->with(
            'success',
            "Artwork \"{$artwork->title}\" has been hidden and an alert email was sent to the artist."
        );
    }
}
