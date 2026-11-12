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

        // Load visible collections with artworks for preview cards
        $collectionsQuery = $user->collections()
            ->has('artworks')
            ->withCount('artworks')
            ->with(['artworks' => function ($q) {
                $q->published();
            }]);

        if (!$isOwner) {
            $collectionsQuery->where('visibility', 'public');
        }

        // Default sort by custom order, then recent
        $collections = $collectionsQuery->orderBy('sort_order', 'asc')->orderBy('updated_at', 'desc')->get();

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
            ->with(['user', 'collections', 'tags'])
            ->where('status', 'published')
            ->orderByDesc('is_pinned')
            ->latest('pinned_at')
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
