<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Http\Requests\ArtworkRequest;
use App\Services\EmbedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtworkController extends Controller
{
    protected EmbedService $embedService;

    public function __construct(EmbedService $embedService)
    {
        $this->embedService = $embedService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $artworks = Auth::user()->artworks()->latest()->get();
        return view('artworks.index', compact('artworks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('artworks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArtworkRequest $request)
    {
        $url = $request->input('external_url');
        
        $embedData = $this->embedService->processUrl($url);

        if (!$embedData) {
            return back()->withInput()->withErrors([
                'external_url' => 'La URL proporcionada no pertenece a nuestra lista blanca de sitios permitidos, o el enlace no es válido.'
            ]);
        }

        $artwork = Auth::user()->artworks()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price_hint' => $request->input('price_hint'),
            'status' => $request->input('status', 'public'),
            'external_url' => $url,
            'provider' => $embedData['provider'],
            'category' => $embedData['category'],
            'render_type' => $embedData['render_type'],
            'embed_html' => $embedData['embed_html'],
            'thumbnail_url' => $embedData['thumbnail_url'],
        ]);

        return redirect()->route('artworks.index')->with('success', 'Obra añadida exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artwork $artwork)
    {
        abort_if($artwork->user_id !== Auth::id(), 403, 'No tienes permiso para editar esta obra.');
        
        return view('artworks.edit', compact('artwork'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArtworkRequest $request, Artwork $artwork)
    {
        abort_if($artwork->user_id !== Auth::id(), 403, 'No tienes permiso para editar esta obra.');

        $artwork->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price_hint' => $request->input('price_hint'),
            'status' => $request->input('status', 'public'),
        ]);

        return redirect()->route('artworks.index')->with('success', 'Obra actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artwork $artwork)
    {
        abort_if($artwork->user_id !== Auth::id(), 403, 'No tienes permiso para eliminar esta obra.');

        $artwork->delete(); // Soft delete

        return redirect()->route('artworks.index')->with('success', 'La obra ha sido enviada a la papelera.');
    }
}
