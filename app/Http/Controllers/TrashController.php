<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    /**
     * Mostrar la papelera de obras.
     */
    public function index()
    {
        $artworks = Auth::user()->artworks()->onlyTrashed()->latest('deleted_at')->get();
        
        return view('trash.index', compact('artworks'));
    }

    /**
     * Restaurar una obra de la papelera.
     */
    public function restore($id)
    {
        $artwork = Auth::user()->artworks()->onlyTrashed()->findOrFail($id);
        
        $artwork->restore();
        
        return redirect()->back()->with('success', 'Obra restaurada correctamente.');
    }

    /**
     * Eliminar definitivamente una obra.
     */
    public function destroy($id)
    {
        $artwork = Auth::user()->artworks()->onlyTrashed()->findOrFail($id);
        
        $artwork->forceDelete();
        
        return redirect()->back()->with('success', 'Obra eliminada permanentemente.');
    }
}
