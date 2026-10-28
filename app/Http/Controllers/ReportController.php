<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Store a new report submitted by an authenticated user.
     */
    public function store(Request $request, int|string $id): JsonResponse
    {
        $artwork = Artwork::findOrFail($id);

        $validated = $request->validate([
            'reason'  => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = auth()->id();

        // Prevent duplicate pending reports from the same user
        $existing = Report::where('user_id', $userId)
            ->where('artwork_id', $artwork->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'You already have a pending report for this artwork.',
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
            'message' => 'Report submitted successfully. Our team will review this artwork shortly.',
            'report'  => $report,
        ], 201);
    }
}
