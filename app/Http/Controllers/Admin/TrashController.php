<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class TrashController extends Controller
{
    /**
     * Display global trash items (artworks and collections).
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'artworks');
        $search = $request->get('search');

        // Consulta de obras en papelera
        $artworksQuery = Artwork::onlyTrashed()->with('user')->latest('deleted_at');
        if ($search) {
            $artworksQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }
        $artworks = $artworksQuery->paginate(12, ['*'], 'artworks_page')->withQueryString();

        // Consulta de colecciones en papelera
        $collectionsQuery = Collection::onlyTrashed()->with('user')->latest('deleted_at');
        if ($search) {
            $collectionsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }
        $collections = $collectionsQuery->paginate(12, ['*'], 'collections_page')->withQueryString();

        $counts = [
            'artworks'    => Artwork::onlyTrashed()->count(),
            'collections' => Collection::onlyTrashed()->count(),
        ];

        return view('admin.trash.index', compact('artworks', 'collections', 'counts', 'tab', 'search'));
    }

    /**
     * Restore a deleted artwork from trash.
     */
    public function restoreArtwork(int|string $id): RedirectResponse
    {
        $artwork = Artwork::onlyTrashed()->findOrFail($id);

        // Detach collections that are still in trash
        $associatedCollections = $artwork->collections()->withTrashed()->get();
        foreach ($associatedCollections as $col) {
            if ($col->trashed()) {
                $artwork->collections()->detach($col->id);
            }
        }

        $artwork->restore();

        return back()->with('success', "Artwork \"{$artwork->title}\" restored successfully.");
    }

    /**
     * Permanently delete an artwork.
     */
    public function destroyArtwork(int|string $id): RedirectResponse
    {
        $artwork = Artwork::onlyTrashed()->findOrFail($id);
        $title = $artwork->title;

        $artwork->forceDelete();

        return back()->with('success', "Artwork \"{$title}\" permanently deleted.");
    }

    /**
     * Restore a deleted collection.
     */
    public function restoreCollection(int|string $id): RedirectResponse
    {
        $collection = Collection::onlyTrashed()->findOrFail($id);
        $collection->restore();

        return back()->with('success', "Collection \"{$collection->name}\" restored successfully.");
    }

    /**
     * Permanently delete a collection.
     */
    public function destroyCollection(int|string $id): RedirectResponse
    {
        $collection = Collection::onlyTrashed()->findOrFail($id);
        $name = $collection->name;

        $collection->forceDelete();

        return back()->with('success', "Collection \"{$name}\" permanently deleted.");
    }

    /**
     * Run model prune command to clean items older than 30 days.
     */
    public function pruneNow(): RedirectResponse
    {
        Artisan::call('model:prune');

        return back()->with('success', 'Global 30-day trash purge executed successfully.');
    }
}
