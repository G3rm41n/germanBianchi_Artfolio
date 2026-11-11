<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Collection;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collections = Auth::user()->collections()->withCount('artworks')->latest()->paginate(12);
        return view('collections.index', compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Collection::class);
        return view('collections.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Collection::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'nullable|in:public,unlisted,private',
        ]);

        $validated['visibility'] = $validated['visibility'] ?? 'public';

        Auth::user()->collections()->create($validated);

        return redirect()->route('collections.index')->with('success', 'Collection created successfully.');
    }

    /**
     * Display the specified collection.
     */
    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);

        return redirect()->route('profile.public', [
            'slug' => $collection->user->slug,
            'collection_id' => $collection->id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        $this->authorize('update', $collection);

        return view('collections.form', compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'required|in:public,unlisted,private',
        ]);

        $collection->update($validated);

        return redirect()->route('collections.index')->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return redirect()->route('collections.index')->with('success', 'Collection moved to trash.');
    }

    /**
     * Returns the user's collections and whether the given artwork belongs to each.
     */
    public function artworkStatus(\App\Models\Artwork $artwork)
    {
        $user = Auth::user();
        $userCollections = $user->collections()->orderBy('name')->get();
        
        $artworkCollectionIds = $artwork->collections()
            ->where('collections.user_id', $user->id)
            ->pluck('collections.id')
            ->flip();

        $data = $userCollections->map(function ($collection) use ($artworkCollectionIds) {
            return [
                'id' => $collection->id,
                'name' => $collection->name,
                'visibility' => $collection->visibility,
                'is_in_collection' => isset($artworkCollectionIds[$collection->id]),
            ];
        });

        return response()->json([
            'collections' => $data,
        ]);
    }

    /**
     * Toggles an artwork inside a user's collection.
     */
    public function toggleArtwork(Collection $collection, Request $request)
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'artwork_id' => ['required', 'exists:artworks,id'],
        ]);

        $changes = $collection->artworks()->toggle($validated['artwork_id']);
        $attached = count($changes['attached']) > 0;

        return response()->json([
            'success' => true,
            'attached' => $attached,
            'message' => $attached 
                ? "Artwork added to '{$collection->name}'." 
                : "Artwork removed from '{$collection->name}'.",
        ]);
    }

    /**
     * Creates a new collection and immediately attaches the artwork to it.
     */
    public function quickCreateWithArtwork(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'visibility' => ['nullable', 'in:public,unlisted,private'],
            'artwork_id' => ['required', 'exists:artworks,id'],
        ]);

        $collection = Auth::user()->collections()->create([
            'name' => $validated['name'],
            'visibility' => $validated['visibility'] ?? 'public',
        ]);

        $collection->artworks()->attach($validated['artwork_id']);

        return response()->json([
            'success' => true,
            'collection' => [
                'id' => $collection->id,
                'name' => $collection->name,
                'visibility' => $collection->visibility,
                'is_in_collection' => true,
            ],
            'message' => "Collection '{$collection->name}' created and artwork added!",
        ], 201);
    }
}
