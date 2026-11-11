<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show($slug)
    {
        $user = User::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $viewer = auth()->user();
        $isOwner = $viewer && $viewer->id === $user->id;

        // Load visible collections for tabs
        $collectionsQuery = $user->collections()->has('artworks');
        if (!$isOwner) {
            $collectionsQuery->where('visibility', 'public');
        }
        $collections = $collectionsQuery->get();

        $selectedCollectionId = request('collection_id');
        $selectedCollection = null;

        if ($selectedCollectionId) {
            $selectedCollection = $user->collections()->findOrFail($selectedCollectionId);

            if (! $selectedCollection->canBeViewedBy($viewer)) {
                abort(403, 'This collection is private.');
            }

            // If it's an unlisted collection accessed via direct link, prepend to tabs so active state works
            if (! $collections->contains('id', $selectedCollection->id)) {
                $collections->push($selectedCollection);
            }
        }

        $query = $user->artworks()
            ->with(['user', 'collections'])
            ->where('status', 'published')
            ->latest();

        if ($selectedCollectionId) {
            $query->whereHas('collections', function($q) use ($selectedCollectionId) {
                $q->where('collections.id', $selectedCollectionId);
            });
        }

        $artworks = $query->paginate(12)->withQueryString();

        return view('profile.show', compact('user', 'artworks', 'collections', 'selectedCollectionId', 'selectedCollection'));
    }
}
