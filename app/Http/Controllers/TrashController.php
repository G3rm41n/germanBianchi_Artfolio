<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    /**
     * Mostrar la papelera de obras y colecciones.
     */
    public function index()
    {
        $user = Auth::user();
        $artworks = $user->artworks()->onlyTrashed()->latest('deleted_at')->get();
        $collections = $user->collections()->onlyTrashed()->latest('deleted_at')->get();

        return view('trash.index', compact('artworks', 'collections'));
    }

    /**
     * Restore an artwork from trash.
     */
    public function restore($id)
    {
        return $this->restoreArtwork($id);
    }

    /**
     * Restore an artwork from trash.
     * Detach from collections if the collection was also deleted.
     */
    public function restoreArtwork($id)
    {
        $artwork = Auth::user()->artworks()->onlyTrashed()->findOrFail($id);

        $trashedCollectionIds = $artwork->collections()->withTrashed()
            ->whereNotNull('collections.deleted_at')
            ->pluck('collections.id')
            ->toArray();

        if (!empty($trashedCollectionIds)) {
            $artwork->collections()->detach($trashedCollectionIds);
        }

        $artwork->restore();

        $message = !empty($trashedCollectionIds)
            ? 'Artwork restored to general catalog (detached from deleted collection).'
            : 'Artwork restored successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete an artwork permanently.
     */
    public function destroy($id)
    {
        return $this->destroyArtwork($id);
    }

    /**
     * Permanently delete an artwork.
     */
    public function destroyArtwork($id)
    {
        $artwork = Auth::user()->artworks()->onlyTrashed()->findOrFail($id);
        $artwork->collections()->detach();
        $artwork->forceDelete();

        return redirect()->back()->with('success', 'Artwork permanently deleted.');
    }

    /**
     * Restore a collection from trash.
     */
    public function restoreCollection($id)
    {
        $collection = Auth::user()->collections()->onlyTrashed()->findOrFail($id);
        $collection->restore();

        return redirect()->back()->with('success', 'Collection restored successfully.');
    }

    /**
     * Permanently delete a collection.
     */
    public function destroyCollection($id)
    {
        $collection = Auth::user()->collections()->onlyTrashed()->findOrFail($id);
        $collection->artworks()->detach();
        $collection->forceDelete();

        return redirect()->back()->with('success', 'Collection permanently deleted.');
    }

    /**
     * Empty user trash completely.
     */
    public function emptyTrash()
    {
        $user = Auth::user();

        $trashedArtworks = $user->artworks()->onlyTrashed()->get();
        foreach ($trashedArtworks as $art) {
            $art->collections()->detach();
            $art->forceDelete();
        }

        $trashedCollections = $user->collections()->onlyTrashed()->get();
        foreach ($trashedCollections as $col) {
            $col->artworks()->detach();
            $col->forceDelete();
        }

        return redirect()->back()->with('success', 'Trash emptied successfully.');
    }
}
