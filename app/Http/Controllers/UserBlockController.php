<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserBlockController extends Controller
{
    /**
     * Block a user by username / slug.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
        ]);

        $search = trim($validated['username']);
        // Strip leading @ if user typed @handle
        $search = ltrim($search, '@');

        $currentUser = $request->user();

        // Search by slug, name or email
        $targetUser = User::where('slug', $search)
            ->orWhere('name', $search)
            ->orWhere('email', $search)
            ->first();

        if (! $targetUser) {
            return back()->withErrors(['username' => "No user found matching '{$search}'."])->with('active_tab', 'blocking');
        }

        if ($targetUser->id === $currentUser->id) {
            return back()->withErrors(['username' => 'You cannot block your own account.'])->with('active_tab', 'blocking');
        }

        if ($currentUser->isBlocking($targetUser)) {
            return back()->with('info', "User @{$targetUser->name} is already blocked.")->with('active_tab', 'blocking');
        }

        $currentUser->block($targetUser);

        return back()->with('status', "User @{$targetUser->name} has been blocked.")->with('active_tab', 'blocking');
    }

    /**
     * Unblock a user.
     */
    public function destroy(User $blockedUser, Request $request): RedirectResponse
    {
        $currentUser = $request->user();
        $currentUser->unblock($blockedUser);

        return back()->with('status', "User @{$blockedUser->name} has been unblocked.")->with('active_tab', 'blocking');
    }
}

