<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Comment;
use App\Notifications\ArtworkCommentedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArtworkCommentController extends Controller
{
    /**
     * Publica un nuevo comentario en la obra.
     */
    public function store(Request $request, Artwork $artwork): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:1000'],
        ], [
            'content.required' => __('El comentario no puede estar vacío.'),
            'content.max' => __('El comentario no puede superar los 1000 caracteres.'),
        ]);

        $user = auth()->user();

        $comment = $artwork->comments()->create([
            'user_id' => $user->id,
            'content' => trim($validated['content']),
        ]);

        // Notificar al artista si quien comenta no es el propio autor
        if ($artwork->user_id !== $user->id && $artwork->user) {
            $artwork->user->notify(new ArtworkCommentedNotification($artwork, $comment, $user));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Comentario publicado con éxito.'),
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'slug' => $user->slug,
                        'avatar_url' => $user->avatar_url,
                        'is_verified' => (bool)$user->is_verified,
                    ],
                ],
            ]);
        }

        return back()->with('success', __('Comentario publicado con éxito.'));
    }

    /**
     * Elimina un comentario (solo el autor del comentario, el artista o un admin).
     */
    public function destroy(Request $request, Artwork $artwork, Comment $comment): JsonResponse|RedirectResponse
    {
        $user = auth()->user();

        $canDelete = $user->id === $comment->user_id 
                  || $user->id === $artwork->user_id 
                  || $user->is_admin;

        if (!$canDelete) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => __('No autorizado.')], 403);
            }
            abort(403);
        }

        $comment->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Comentario eliminado.'),
            ]);
        }

        return back()->with('success', __('Comentario eliminado.'));
    }
}

