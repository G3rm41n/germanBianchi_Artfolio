<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Almacena un nuevo reporte de obra enviado por un usuario autenticado (RF-09).
     */
    public function store(Request $request, int|string $id): JsonResponse
    {
        $artwork = Artwork::findOrFail($id);

        $validated = $request->validate([
            'reason'  => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = auth()->id();

        // Prevención estricta de reportes duplicados pendientes (RF-09 / DB unique constraint)
        $existing = Report::where('user_id', $userId)
            ->where('artwork_id', $artwork->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Ya has enviado un reporte para esta obra que se encuentra pendiente de revisión.',
            ], 422);
        }

        $report = Report::create([
            'user_id'    => $userId,
            'artwork_id' => $artwork->id,
            'reason'     => $validated['reason'],
            'details'    => $validated['details'] ?? null,
            'status'     => 'pending',
        ]);

        return response()->json([
            'message' => 'Reporte enviado con éxito. Nuestro equipo de moderación revisará la obra a la brevedad.',
            'report'  => $report,
        ], 201);
    }
}
