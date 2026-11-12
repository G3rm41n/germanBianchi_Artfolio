<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArtworkRequest;
use App\Models\Artwork;
use App\Services\AuditLogger;
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
        $artworks = Auth::user()->artworks()->with('collections')->latest()->paginate(12);
        return view('artworks.index', compact('artworks'));
    }

    /**
     * Muestra la página dedicada de la obra estilo Pinterest con recomendaciones y comentarios.
     */
    public function show(Artwork $artwork)
    {
        if ($artwork->status === 'hidden') {
            $user = Auth::user();
            if (!$user || ($user->id !== $artwork->user_id && !$user->is_admin)) {
                abort(404);
            }
        }

        $viewKey = 'viewed_art_' . $artwork->id;
        if (!session()->has($viewKey)) {
            $artwork->increment('views_count');
            session()->put($viewKey, true);
        }

        $artwork->load([
            'user',
            'tags',
            'comments.user',
        ]);

        $user = Auth::user();
        $isLiked = $user ? $artwork->isLikedBy($user) : false;
        $isBookmarked = $user ? $user->bookmarks()->where('artwork_id', $artwork->id)->exists() : false;

        $similarArtworks = $artwork->getSimilarArtworks(16, $user);

        return view('artworks.show', compact('artwork', 'similarArtworks', 'isLiked', 'isBookmarked'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Artwork::class);
        $collections = Auth::user()->collections()->get();
        return view('artworks.create', compact('collections'));
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
                'external_url' => 'The provided URL does not belong to supported platforms or is invalid.'
            ]);
        }

        $artwork = Auth::user()->artworks()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price_hint' => $request->input('price_hint'),
            'status' => $request->input('status', 'published'),
            'external_url' => $url,
            'provider' => $embedData['provider'],
            'category' => $request->input('category'),
            'subcategory' => $request->input('subcategory'),
            'is_ai_generated' => $request->boolean('is_ai_generated'),
            'is_mature' => $request->boolean('is_mature'),
            'software_used' => $request->input('software_used', []),
            'license' => $request->input('license', 'all_rights_reserved'),
            'render_type' => $embedData['render_type'],
            'embed_html' => $embedData['embed_html'],
            'thumbnail_url' => $embedData['thumbnail_url'],
        ]);

        if ($request->has('collections')) {
            $validCollections = Auth::user()->collections()->whereIn('id', $request->input('collections'))->pluck('id');
            $artwork->collections()->sync($validCollections);
        }

        $this->syncTags($artwork, $request->input('tags'));

        AuditLogger::log(
            'user.artwork_created',
            $artwork,
            $artwork->title,
            null,
            "User published artwork '{$artwork->title}'",
            Auth::user(),
            'user'
        );

        return redirect()->route('artworks.index')->with('success', 'Artwork created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artwork $artwork)
    {
        $this->authorize('update', $artwork);
        
        $collections = Auth::user()->collections()->get();
        $artworkCollections = $artwork->collections()->pluck('collections.id')->toArray();
        $artworkTags = $artwork->tags()->pluck('name')->implode(', ');

        return view('artworks.edit', compact('artwork', 'collections', 'artworkCollections', 'artworkTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArtworkRequest $request, Artwork $artwork)
    {
        $this->authorize('update', $artwork);

        $url = $request->input('external_url');
        
        // Reprocess external URL
        $embedData = $this->embedService->processUrl($url);

        if (!$embedData) {
            return back()->withInput()->withErrors([
                'external_url' => 'The provided URL does not belong to supported platforms or is invalid.'
            ]);
        }

        $artwork->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price_hint' => $request->input('price_hint'),
            'status' => $request->input('status', 'published'),
            'external_url' => $url,
            'provider' => $embedData['provider'],
            'category' => $request->input('category'),
            'subcategory' => $request->input('subcategory'),
            'is_ai_generated' => $request->boolean('is_ai_generated'),
            'is_mature' => $request->boolean('is_mature'),
            'software_used' => $request->input('software_used', []),
            'license' => $request->input('license', 'all_rights_reserved'),
            'render_type' => $embedData['render_type'],
            'embed_html' => $embedData['embed_html'],
            'thumbnail_url' => $embedData['thumbnail_url'],
        ]);

        $validCollections = Auth::user()->collections()->whereIn('id', $request->input('collections', []))->pluck('id');
        $artwork->collections()->sync($validCollections);

        $this->syncTags($artwork, $request->input('tags'));

        AuditLogger::log(
            'user.artwork_updated',
            $artwork,
            $artwork->title,
            null,
            "User updated artwork '{$artwork->title}'",
            Auth::user(),
            'user'
        );

        return redirect()->route('artworks.index')->with('success', 'Artwork updated successfully.');
    }

    /**
     * Toggle pinned status of artwork in public portfolio (max 3).
     */
    public function togglePin(Artwork $artwork)
    {
        $this->authorize('update', $artwork);

        if ($artwork->is_pinned) {
            $artwork->update([
                'is_pinned' => false,
                'pinned_at' => null,
            ]);
            $isPinned = false;
            $message = 'Artwork unpinned from portfolio showcase.';
        } else {
            $pinnedCount = Auth::user()->artworks()->where('is_pinned', true)->count();
            if ($pinnedCount >= 3) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can pin a maximum of 3 artworks. Unpin another artwork first.'
                    ], 422);
                }
                return back()->with('error', 'You can pin a maximum of 3 artworks. Unpin another artwork first.');
            }

            $artwork->update([
                'is_pinned' => true,
                'pinned_at' => now(),
            ]);
            $isPinned = true;
            $message = 'Artwork pinned to the top of your portfolio showcase.';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_pinned' => $isPinned,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Synchronize tags for an artwork.
     */
    protected function syncTags(Artwork $artwork, ?array $tags): void
    {
        if ($tags === null) {
            return;
        }

        $tagIds = [];
        foreach ($tags as $rawName) {
            $clean = ltrim(trim($rawName), '#');
            if ($clean === '') {
                continue;
            }
            $slug = \Illuminate\Support\Str::slug($clean);
            if ($slug === '') {
                continue;
            }

            $tag = \App\Models\Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $clean]
            );
            $tagIds[] = $tag->id;
        }

        $artwork->tags()->sync(array_slice($tagIds, 0, 15));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artwork $artwork)
    {
        $this->authorize('delete', $artwork);

        $artworkTitle = $artwork->title;
        $artwork->delete(); // Soft delete

        AuditLogger::log(
            'user.artwork_deleted',
            $artwork,
            $artworkTitle,
            null,
            "User moved artwork '{$artworkTitle}' to trash",
            Auth::user(),
            'user'
        );

        return redirect()->route('artworks.index')->with('success', 'Artwork moved to trash.');
    }

    /**
     * Returns preview metadata for an external URL.
     */
    public function previewUrl(Request $request)
    {
        $url = trim((string) $request->input('url', ''));

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid URL.',
            ], 422);
        }

        $embedData = $this->embedService->processUrl($url);

        if (!$embedData) {
            return response()->json([
                'success' => false,
                'message' => 'The URL domain is not supported or could not be resolved.',
            ], 422);
        }

        return response()->json([
            'success'       => true,
            'provider'      => $embedData['provider'],
            'category'      => $embedData['category'],
            'render_type'   => $embedData['render_type'],
            'thumbnail_url' => $embedData['thumbnail_url'],
            'title'         => $embedData['title'] ?? null,
        ]);
    }
}
