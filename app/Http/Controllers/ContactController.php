<?php

namespace App\Http\Controllers;

use App\Jobs\SendContactEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Enviar mensaje de contacto seguro hacia un artista (RF-06, RN-05, RNF-01).
     */
    public function send(Request $request, string $slug)
    {
        $sender = Auth::user();
        $artist = User::where('slug', $slug)->firstOrFail();

        // ── RN-05: Regla Anti-Auto-Contacto ──────────────────────────────────
        if ($sender->id === $artist->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes enviarte un mensaje de contacto a ti mismo.',
                ], 403);
            }

            return redirect()->back()->with('error', 'No puedes enviarte un mensaje de contacto a ti mismo.');
        }

        // ── Validación de entrada ───────────────────────────────────────────
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2500'],
        ], [
            'subject.required' => 'Debes indicar el motivo o asunto del contacto.',
            'subject.max' => 'El motivo no puede superar los 150 caracteres.',
            'message.required' => 'Debes escribir un mensaje para el artista.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'message.max' => 'El mensaje no puede superar los 2500 caracteres.',
        ]);

        // ── Despacho Asíncrono a la Cola de BD (RNF-01: respuesta < 100 ms) ──
        SendContactEmail::dispatch(
            $sender->name,
            $sender->email,
            $artist->email,
            $artist->name,
            $validated['subject'],
            $validated['message']
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '¡Tu mensaje ha sido enviado exitosamente al artista!',
            ]);
        }

        return redirect()->back()->with('success', '¡Tu mensaje ha sido enviado exitosamente al artista!');
    }
}
