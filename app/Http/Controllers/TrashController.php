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
     * Restaurar una obra de la papelera con regla RN-03.
     */
    public function restore($id)
    {
        return $this->restoreArtwork($id);
    }

    /**
     * Restaurar una obra de la papelera.
     * RN-03: Si la colección asociada fue eliminada o no existe, se desvincula.
     */
    public function restoreArtwork($id)
    {
        $artwork = Auth::user()->artworks()->onlyTrashed()->findOrFail($id);

        // RN-03: Desvincular de colecciones que se encuentren en papelera o hayan sido eliminadas
        $trashedCollectionIds = $artwork->collections()->withTrashed()
            ->whereNotNull('collections.deleted_at')
            ->pluck('collections.id')
            ->toArray();

        if (!empty($trashedCollectionIds)) {
            $artwork->collections()->detach($trashedCollectionIds);
        }

        $artwork->restore();

        $message = !empty($trashedCollectionIds)
            ? 'Obra restaurada en el catálogo general (desvinculada de su colección eliminada).'
            : 'Obra restaurada correctamente.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Eliminar definitivamente una obra.
     */
    public function destroy($id)
    {
        return $this->destroyArtwork($id);
    }

    /**
     * Eliminar permanentemente una obra.
     */
    public function destroyArtwork($id)
    {
        $artwork = Auth::user()->artworks()->onlyTrashed()->findOrFail($id);
        $artwork->collections()->detach();
        $artwork->forceDelete();

        return redirect()->back()->with('success', 'Obra eliminada permanentemente.');
    }

    /**
     * Restaurar una colección de la papelera.
     */
    public function restoreCollection($id)
    {
        $collection = Auth::user()->collections()->onlyTrashed()->findOrFail($id);
        $collection->restore();

        return redirect()->back()->with('success', 'Colección restaurada correctamente.');
    }

    /**
     * Eliminar permanentemente una colección.
     */
    public function destroyCollection($id)
    {
        $collection = Auth::user()->collections()->onlyTrashed()->findOrFail($id);
        $collection->artworks()->detach();
        $collection->forceDelete();

        return redirect()->back()->with('success', 'Colección eliminada permanentemente.');
    }

    /**
     * Vaciar por completo la papelera del usuario (obras y colecciones).
     */
    public function emptyTrash()
    {
        $user = Auth::user();

        // Eliminar definitivamente obras
        $trashedArtworks = $user->artworks()->onlyTrashed()->get();
        foreach ($trashedArtworks as $art) {
            $art->collections()->detach();
            $art->forceDelete();
        }

        // Eliminar definitivamente colecciones
        $trashedCollections = $user->collections()->onlyTrashed()->get();
        foreach ($trashedCollections as $col) {
            $col->artworks()->detach();
            $col->forceDelete();
        }

        return redirect()->back()->with('success', 'La papelera ha sido vaciada por completo.');
    }
}
