<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtworkViewController extends Controller
{
    /**
     * Atomically increment the views counter for an artwork with session deduplication.
     */
    public function store(Request $request, Artwork $artwork): JsonResponse
    {
        $sessionKey = 'viewed_artwork_' . $artwork->id;

        if (! $request->session()->has($sessionKey)) {
            $artwork->increment('views_count');
            $request->session()->put($sessionKey, true);
        }

        return response()->json([
            'success' => true,
            'views_count' => (int) $artwork->views_count,
        ]);
    }
}

