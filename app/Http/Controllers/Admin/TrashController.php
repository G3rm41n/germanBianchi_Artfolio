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
     * Auditoría y visualización de la papelera global (RF-11).
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
     * Restaura una obra eliminada aplicando la regla RN-03.
     */
    public function restoreArtwork(int|string $id): RedirectResponse
    {
        $artwork = Artwork::onlyTrashed()->findOrFail($id);

        // Regla RN-03: Desvincular de colecciones que estén eliminadas o no existan
        $associatedCollections = $artwork->collections()->withTrashed()->get();
        foreach ($associatedCollections as $col) {
            if ($col->trashed()) {
                $artwork->collections()->detach($col->id);
            }
        }

        $artwork->restore();

        return back()->with('success', "La obra \"{$artwork->title}\" ha sido restaurada con éxito.");
    }

    /**
     * Eliminación permanente e irreversible de una obra.
     */
    public function destroyArtwork(int|string $id): RedirectResponse
    {
        $artwork = Artwork::onlyTrashed()->findOrFail($id);
        $title = $artwork->title;

        $artwork->forceDelete();

        return back()->with('success', "La obra \"{$title}\" ha sido eliminada permanentemente del sistema.");
    }

    /**
     * Restaura una colección eliminada.
     */
    public function restoreCollection(int|string $id): RedirectResponse
    {
        $collection = Collection::onlyTrashed()->findOrFail($id);
        $collection->restore();

        return back()->with('success', "La colección \"{$collection->name}\" ha sido restaurada con éxito.");
    }

    /**
     * Eliminación permanente de una colección.
     */
    public function destroyCollection(int|string $id): RedirectResponse
    {
        $collection = Collection::onlyTrashed()->findOrFail($id);
        $name = $collection->name;

        $collection->forceDelete();

        return back()->with('success', "La colección \"{$name}\" ha sido eliminada definitivamente.");
    }

    /**
     * Forzar la ejecución inmediata del comando de purga de 30 días (model:prune / RN-04).
     */
    public function pruneNow(): RedirectResponse
    {
        Artisan::call('model:prune');

        return back()->with('success', 'Se ha ejecutado la purga global de obras y colecciones con más de 30 días en la papelera.');
    }
}
