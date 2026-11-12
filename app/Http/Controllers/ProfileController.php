<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $blockedUsers = $user->blockedUsers()->orderBy('name')->get();

        return view('profile.edit', [
            'user' => $user,
            'blockedUsers' => $blockedUsers,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        foreach (['avatar_url', 'banner_url'] as $imageField) {
            if (!empty($validated[$imageField])) {
                $validated[$imageField] = $this->resolveDirectImageUrl($validated[$imageField]);
            }
        }

        $user->fill($validated);

        $dirtyFields = array_keys($user->getDirty());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if (!empty($dirtyFields)) {
            AuditLogger::log(
                'user.profile_updated',
                $user,
                $user->name,
                ['updated_fields' => $dirtyFields],
                "User updated profile fields: " . implode(', ', $dirtyFields),
                $user,
                'user'
            );
        }

        $redirect = $request->filled('active_tab')
            ? Redirect::route('profile.edit', ['tab' => $request->input('active_tab')])
            : Redirect::route('profile.edit');

        return $redirect
            ->with('status', 'profile-updated')
            ->with('active_tab', $request->input('active_tab', 'profile'));
    }

    /**
     * Resolve page URLs (e.g. Pinterest pin, DeviantArt post, Imgur) to direct image URLs when possible.
     */
    protected function resolveDirectImageUrl(string $url): string
    {
        if (preg_match('/\.(jpe?g|png|gif|webp|avif|svg)(\?.*)?$/i', $url)) {
            return $url;
        }

        try {
            $meta = app(\App\Services\EmbedService::class)->processUrl($url);
            if (!empty($meta['image_url'])) {
                return $meta['image_url'];
            }
        } catch (\Throwable $e) {
            // Keep original URL if not a supported embed provider
        }

        return $url;
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userName = $user->name;
        $userEmail = $user->email;

        Auth::logout();

        $user->delete();

        AuditLogger::log(
            'user.account_deleted',
            null,
            $userName,
            null,
            "User {$userName} ({$userEmail}) deleted their account",
            null,
            'user'
        );

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
