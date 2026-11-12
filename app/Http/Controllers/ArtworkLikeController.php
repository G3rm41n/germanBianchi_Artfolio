<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Notifications\ArtworkLikedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtworkLikeController extends Controller
{
    /**
     * Alternar "Me gusta" (aprecio público) para una obra.
     */
    public function toggle(Request $request, Artwork $artwork): JsonResponse
    {
        $user = auth()->user();

        $toggled = $user->likedArtworks()->toggle($artwork->id);
        $isLiked = count($toggled['attached']) > 0;

        if ($isLiked) {
            $artwork->increment('likes_count');

            if ($artwork->user_id !== $user->id && $artwork->user) {
                $artwork->user->notify(new ArtworkLikedNotification($artwork, $user));
            }
        } else {
            if ($artwork->likes_count > 0) {
                $artwork->decrement('likes_count');
            }
        }

        return response()->json([
            'status' => 'success',
            'is_liked' => $isLiked,
            'likes_count' => (int) $artwork->fresh()->likes_count,
            'message' => $isLiked ? __('Apreciaste esta obra') : __('Eliminaste tu aprecio'),
        ]);
    }
}

