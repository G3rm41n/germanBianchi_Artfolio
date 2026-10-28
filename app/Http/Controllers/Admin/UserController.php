<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Listado y gestión de usuarios y artistas registrados (RF-11).
     */
    public function index(Request $request): View
    {
        $query = User::withCount(['artworks', 'collections'])->latest();

        // Filtro por estado
        $status = $request->get('status', 'all');
        if (in_array($status, ['active', 'suspended'])) {
            $query->where('status', $status);
        }

        // Filtro por rol
        $role = $request->get('role');
        if ($role === 'admin') {
            $query->where('is_admin', true);
        } elseif ($role === 'artist') {
            $query->where('is_admin', false);
        }

        // Buscador
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(12)->withQueryString();

        $counts = [
            'all'       => User::count(),
            'active'    => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'admins'    => User::where('is_admin', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'counts', 'status', 'role'));
    }

    /**
     * Suspende temporalmente a un artista o usuario.
     */
    public function suspend(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes suspender tu propia cuenta de administrador.');
        }

        $user->update(['status' => 'suspended']);

        return back()->with('success', "El usuario \"{$user->name}\" ha sido suspendido.");
    }

    /**
     * Reactiva la cuenta de un usuario suspendido.
     */
    public function activate(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->update(['status' => 'active']);

        return back()->with('success', "La cuenta de \"{$user->name}\" ha sido reactivada.");
    }

    /**
     * Eliminación suave (Soft Delete) de la cuenta de un usuario.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $user->delete();

        return back()->with('success', "La cuenta de \"{$user->name}\" ha sido eliminada.");
    }
}
