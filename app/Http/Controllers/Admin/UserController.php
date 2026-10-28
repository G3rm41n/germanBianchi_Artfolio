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
     * List and manage registered users and artists.
     */
    public function index(Request $request): View
    {
        $query = User::withCount(['artworks', 'collections'])->latest();

        // Status filter
        $status = $request->get('status', 'all');
        if (in_array($status, ['active', 'suspended'])) {
            $query->where('status', $status);
        }

        // Role filter
        $role = $request->get('role');
        if ($role === 'admin') {
            $query->where('is_admin', true);
        } elseif ($role === 'artist') {
            $query->where('is_admin', false);
        }

        // Search
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
     * Temporarily suspend a user.
     */
    public function suspend(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own admin account.');
        }

        $user->update(['status' => 'suspended']);

        return back()->with('success', "User \"{$user->name}\" has been suspended.");
    }

    /**
     * Reactivate a suspended user account.
     */
    public function activate(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->update(['status' => 'active']);

        return back()->with('success', "User \"{$user->name}\" has been reactivated.");
    }

    /**
     * Delete user account (soft delete).
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $user->delete();

        return back()->with('success', "User \"{$user->name}\" has been deleted.");
    }
}
