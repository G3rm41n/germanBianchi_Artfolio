<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'custom'); // custom, az, recent
        $filter = $request->get('filter', 'all'); // all, public, private

        $query = Auth::user()->collections()
            ->withCount('artworks')
            ->with(['artworks' => function ($q) {
                $q->published();
            }]);

        if ($filter === 'public') {
            $query->where('visibility', 'public');
        } elseif ($filter === 'private') {
            $query->where('visibility', 'private');
        }

        if ($sort === 'az') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'recent') {
            $query->orderByRaw('COALESCE(last_artwork_added_at, updated_at, created_at) DESC');
        } else {
            // Default: Custom sort order, then fallback to updated_at
            $query->orderBy('sort_order', 'asc')->orderBy('updated_at', 'desc');
        }

        $collections = $query->paginate(24)->withQueryString();

        return view('collections.index', compact('collections', 'sort', 'filter'));
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
     * Display the specified collection as a Pinterest-style board.
     */
    public function show(string $artist, Collection $collection)
    {
        $artistUser = User::where('slug', $artist)->orWhere('id', $artist)->first();

        if (!$artistUser) {
            abort(404);
        }

        // Ensure the collection belongs to the artist specified in the URL
        if ($collection->user_id !== $artistUser->id) {
            $correctArtist = $collection->user?->slug ?: $collection->user_id;
            return redirect()->route('collections.show', [
                'artist' => $correctArtist,
                'collection' => $collection,
            ], 301);
        }

        // Canonicalize URL to slug if accessed by numeric ID
        if ($artistUser->slug && $artist !== $artistUser->slug) {
            return redirect()->route('collections.show', [
                'artist' => $artistUser->slug,
                'collection' => $collection,
            ], 301);
        }

        $this->authorize('view', $collection);

        $collection->load('user');

        $artworks = $collection->artworks()
            ->published()
            ->with(['user', 'tags', 'likes'])
            ->latest('artworks.created_at')
            ->get();

        $slideshowItems = $artworks->map(fn($art) => [
            'id' => $art->id,
            'title' => $art->title,
            'description' => $art->description,
            'provider' => $art->provider,
            'category' => $art->category,
            'render_type' => $art->render_type,
            'preview_url' => $art->preview_url,
            'high_res_url' => $art->high_res_url,
            'embed_html' => $art->embed_html,
            'external_url' => $art->external_url,
            'likes_count' => (int)$art->likes_count,
            'software_used' => $art->software_used ?? [],
            'license' => $art->license ?? 'all_rights_reserved',
        ])->values();

        return view('collections.show', compact('collection', 'artworks', 'slideshowItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        $this->authorize('update', $collection);

        $artworks = $collection->artworks()->published()->get();

        return view('collections.form', compact('collection', 'artworks'));
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
            'preview_artwork_ids' => 'nullable|array|max:3',
            'preview_artwork_ids.*' => 'nullable|integer|exists:artworks,id',
        ]);

        // Filter null or empty values
        if (isset($validated['preview_artwork_ids'])) {
            $validated['preview_artwork_ids'] = array_values(array_filter($validated['preview_artwork_ids']));
        }

        $collection->update($validated);

        return redirect()->route('collections.index')->with('success', 'Collection updated successfully.');
    }

    /**
     * Reorder collections via Drag-and-Drop (SortableJS).
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:collections,id',
        ]);

        $userId = Auth::id();

        foreach ($validated['ordered_ids'] as $index => $id) {
            Collection::where('id', $id)
                ->where('user_id', $userId)
                ->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Collections reordered successfully.',
        ]);
    }

    /**
     * Set a specific artwork as a cover preview slot (1, 2, or 3) for this collection.
     */
    public function setPreviewArtwork(Collection $collection, Request $request)
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'artwork_id' => 'required|integer|exists:artworks,id',
            'slot' => 'required|integer|in:0,1,2', // 0 = Principal, 1 = Secundaria sup, 2 = Secundaria inf
        ]);

        // Ensure artwork is part of this collection
        if (!$collection->artworks()->where('artworks.id', $validated['artwork_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This artwork does not belong to this board.',
            ], 422);
        }

        $current = $collection->preview_artwork_ids ?? [];
        // Ensure array of 3 elements
        $slots = [
            $current[0] ?? null,
            $current[1] ?? null,
            $current[2] ?? null,
        ];

        $targetSlot = (int)$validated['slot'];
        $artworkId = (int)$validated['artwork_id'];

        // If this artwork is already in another slot, remove it from there
        foreach ($slots as $idx => $id) {
            if ($id === $artworkId && $idx !== $targetSlot) {
                $slots[$idx] = null;
            }
        }

        $slots[$targetSlot] = $artworkId;

        $collection->update([
            'preview_artwork_ids' => $slots,
        ]);

        $slotNames = [
            0 => 'Main (Large)',
            1 => 'Upper Secondary',
            2 => 'Lower Secondary',
        ];

        return response()->json([
            'success' => true,
            'slot' => $targetSlot,
            'slot_name' => $slotNames[$targetSlot],
            'message' => "Artwork set as {$slotNames[$targetSlot]} on the board cover.",
        ]);
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

        if ($attached) {
            $collection->update(['last_artwork_added_at' => now()]);
        }

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
        $collection->update(['last_artwork_added_at' => now()]);

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
