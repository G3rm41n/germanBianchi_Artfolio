<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Devuelve las notificaciones recientes en formato JSON para el menú desplegable.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'info',
                    'message' => $notification->data['message'] ?? '',
                    'artwork_title' => $notification->data['artwork_title'] ?? null,
                    'artwork_preview' => $notification->data['artwork_preview'] ?? null,
                    'actor_name' => $notification->data['actor_name'] ?? null,
                    'actor_avatar' => $notification->data['actor_avatar'] ?? null,
                    'url' => $notification->data['url'] ?? null,
                    'read_at' => $notification->read_at ? $notification->read_at->toIso8601String() : null,
                    'is_read' => $notification->read(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Marca una notificación individual como leída.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'status' => 'success',
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marca todas las notificaciones como leídas.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'unread_count' => 0,
        ]);
    }
}

