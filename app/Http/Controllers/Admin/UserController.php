<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Collection;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List and manage registered users and artists.
     */
    public function index(Request $request): View
    {
        $query = $this->buildFilteredQuery($request);

        $users = $query->paginate(12)->withQueryString();

        $counts = [
            'all'       => User::count(),
            'active'    => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'admins'    => User::where('is_admin', true)->count(),
        ];

        $status = $request->get('status', 'all');
        $role = $request->get('role');

        return view('admin.users.index', compact('users', 'counts', 'status', 'role'));
    }

    /**
     * Export a single artist/user's complete information (profile, artworks, collections) as JSON.
     */
    public function exportOne(int|string $id): Response
    {
        $user = User::with([
            'artworks' => fn ($q) => $q->withTrashed()->latest(),
            'collections' => fn ($q) => $q->withTrashed()->with('artworks:id,title')->withCount('artworks')->latest(),
        ])->withCount(['bookmarks', 'reports'])->findOrFail($id);

        $payload = [
            'export_type' => 'single_artist_dossier',
            'exported_at' => now()->toIso8601String(),
            'exported_by' => auth()->user()?->email,
            'artist'      => $this->formatArtistExportPayload($user),
        ];

        $safeSlug = $user->slug ?: 'user_' . $user->id;
        $filename = "artist_{$safeSlug}_export_" . now()->format('Y-m-d') . ".json";

        return response(
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            200,
            [
                'Content-Type'        => 'application/json; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        );
    }

    /**
     * Export all (or filtered) users/artists with their complete artworks and collections as JSON.
     */
    public function exportAll(Request $request): Response
    {
        $users = $this->buildFilteredQuery($request)
            ->with([
                'artworks' => fn ($q) => $q->withTrashed()->latest(),
                'collections' => fn ($q) => $q->withTrashed()->with('artworks:id,title')->withCount('artworks')->latest(),
            ])
            ->withCount(['bookmarks', 'reports'])
            ->get();

        $payload = [
            'export_type'     => 'users_and_artists_collection',
            'exported_at'     => now()->toIso8601String(),
            'exported_by'     => auth()->user()?->email,
            'filters_applied' => [
                'status' => $request->get('status', 'all'),
                'role'   => $request->get('role') ?: 'all',
                'search' => $request->get('search') ?: null,
            ],
            'total_exported'  => $users->count(),
            'users'           => $users->map(fn (User $u) => $this->formatArtistExportPayload($u))->values()->all(),
        ];

        $filename = 'artfolio_users_export_' . now()->format('Y-m-d_His') . '.json';

        return response(
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            200,
            [
                'Content-Type'        => 'application/json; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        );
    }

    /**
     * Check if an artist already exists by ID or email.
     */
    public function importCheck(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $email = $request->input('email');

        $user = null;
        if (!empty($id)) {
            $user = User::withTrashed()->find($id);
        }
        if (!$user && !empty($email)) {
            $user = User::withTrashed()->where('email', $email)->first();
        }

        if ($user) {
            return response()->json([
                'exists' => true,
                'user'   => [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'slug'              => $user->slug,
                    'artworks_count'    => $user->artworks()->count(),
                    'collections_count' => $user->collections()->count(),
                    'status'            => $user->status,
                ],
            ]);
        }

        return response()->json([
            'exists' => false,
            'user'   => null,
        ]);
    }

    /**
     * Import a single artist from JSON (supporting conflict modes: 'update' or 'replace').
     */
    public function importOne(Request $request): RedirectResponse
    {
        $rawJson = $this->extractJsonContent($request);
        if (!$rawJson) {
            return back()->with('error', 'Por favor selecciona un archivo JSON válido o proporciona los datos en formato JSON.');
        }

        $data = json_decode($rawJson, true);
        if (!is_array($data)) {
            return back()->with('error', 'El archivo no contiene un formato JSON válido.');
        }

        $conflictMode = in_array($request->input('conflict_mode'), ['replace', 'update'])
            ? $request->input('conflict_mode')
            : 'update';

        // Extract single artist payload
        $artistData = $data['artist'] ?? (isset($data['users'][0]) ? $data['users'][0] : (isset($data['account']) ? $data : null));

        if (!$artistData || (!isset($artistData['account']) && !isset($artistData['name']) && !isset($artistData['email']))) {
            return back()->with('error', 'El archivo JSON no contiene la estructura de perfil de un artista.');
        }

        try {
            $user = DB::transaction(function () use ($artistData, $conflictMode) {
                return $this->processArtistImport($artistData, $conflictMode);
            });

            $modeLabel = $conflictMode === 'replace' ? 'reemplazado' : 'actualizado';
            return back()->with('success', "Artista \"{$user->name}\" importado con éxito ({$modeLabel}).");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al importar artista: ' . $e->getMessage());
        }
    }

    /**
     * Batch import users/artists from JSON (with confirmation).
     */
    public function importAll(Request $request): RedirectResponse
    {
        $rawJson = $this->extractJsonContent($request);
        if (!$rawJson) {
            return back()->with('error', 'Por favor selecciona un archivo JSON válido.');
        }

        $data = json_decode($rawJson, true);
        if (!is_array($data)) {
            return back()->with('error', 'El archivo no contiene un formato JSON válido.');
        }

        $conflictMode = in_array($request->input('conflict_mode'), ['replace', 'update'])
            ? $request->input('conflict_mode')
            : 'update';

        $usersList = $data['users'] ?? (isset($data['artist']) ? [$data['artist']] : (is_array($data) && isset($data[0]) ? $data : []));

        if (empty($usersList)) {
            return back()->with('error', 'No se encontraron registros de usuarios en el archivo JSON cargado.');
        }

        try {
            $importedCount = 0;
            DB::transaction(function () use ($usersList, $conflictMode, &$importedCount) {
                foreach ($usersList as $artistData) {
                    if (is_array($artistData)) {
                        $this->processArtistImport($artistData, $conflictMode);
                        $importedCount++;
                    }
                }
            });

            return back()->with('success', "Se importaron/sincronizaron {$importedCount} usuarios y artistas exitosamente.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error durante la importación masiva: ' . $e->getMessage());
        }
    }

    /**
     * Extract raw JSON content from either an uploaded file or form input.
     */
    protected function extractJsonContent(Request $request): ?string
    {
        if ($request->hasFile('json_file') && $request->file('json_file')->isValid()) {
            return file_get_contents($request->file('json_file')->getRealPath());
        }

        if ($request->filled('json_data')) {
            return (string) $request->input('json_data');
        }

        return null;
    }

    /**
     * Core business logic to import or update an artist and their artworks & collections.
     */
    protected function processArtistImport(array $artistData, string $conflictMode): User
    {
        $account = $artistData['account'] ?? [];
        $profile = $artistData['professional_profile'] ?? [];
        $visual = $artistData['visual_identity'] ?? [];
        $prefs = $artistData['preferences'] ?? [];

        $id = $account['id'] ?? ($artistData['id'] ?? null);
        $email = $account['email'] ?? ($artistData['email'] ?? null);
        $name = $account['name'] ?? ($artistData['name'] ?? 'Imported Artist');
        $slug = $account['slug'] ?? ($artistData['slug'] ?? null);

        // Find existing user by ID first, then by email
        $user = null;
        if (!empty($id)) {
            $user = User::withTrashed()->find($id);
        }
        if (!$user && !empty($email)) {
            $user = User::withTrashed()->where('email', $email)->first();
        }

        if ($user && $user->trashed()) {
            $user->restore();
        }

        $attributes = [
            'name'                 => $name,
            'email'                => $email ?: ($user?->email ?: 'user_' . uniqid() . '@artfolio.test'),
            'headline'             => $profile['headline'] ?? ($artistData['headline'] ?? null),
            'bio'                  => $profile['bio'] ?? ($artistData['bio'] ?? null),
            'city'                 => $profile['city'] ?? ($artistData['city'] ?? null),
            'country'              => $profile['country'] ?? ($artistData['country'] ?? null),
            'commission_status'    => $profile['commission_status'] ?? ($artistData['commission_status'] ?? 'open'),
            'price_guide'          => $profile['price_guide'] ?? ($artistData['price_guide'] ?? null),
            'skills'               => $profile['skills'] ?? ($artistData['skills'] ?? []),
            'software'             => $profile['software'] ?? ($artistData['software'] ?? []),
            'hiring_types'         => $profile['hiring_types'] ?? ($artistData['hiring_types'] ?? []),
            'avatar_url'           => $visual['avatar_url'] ?? ($artistData['avatar_url'] ?? null),
            'crop_x'               => $visual['avatar_framing']['crop_x'] ?? ($artistData['crop_x'] ?? 0),
            'crop_y'               => $visual['avatar_framing']['crop_y'] ?? ($artistData['crop_y'] ?? 0),
            'crop_zoom'            => $visual['avatar_framing']['crop_zoom'] ?? ($artistData['crop_zoom'] ?? 1),
            'banner_url'           => $visual['banner_url'] ?? ($artistData['banner_url'] ?? null),
            'banner_crop_x'        => $visual['banner_framing']['banner_crop_x'] ?? ($artistData['banner_crop_x'] ?? 0),
            'banner_crop_y'        => $visual['banner_framing']['banner_crop_y'] ?? ($artistData['banner_crop_y'] ?? 0),
            'banner_crop_zoom'     => $visual['banner_framing']['banner_crop_zoom'] ?? ($artistData['banner_crop_zoom'] ?? 1),
            'contact_email'        => $account['contact_email'] ?? ($artistData['contact_email'] ?? null),
            'public_email_enabled' => $account['public_email_enabled'] ?? ($artistData['public_email_enabled'] ?? false),
            'show_mature_content'  => $prefs['show_mature_content'] ?? ($artistData['show_mature_content'] ?? false),
            'hide_ai_content'      => $prefs['hide_ai_content'] ?? ($artistData['hide_ai_content'] ?? false),
            'notify_new_artworks'  => $prefs['notify_new_artworks'] ?? ($artistData['notify_new_artworks'] ?? true),
            'notify_messages'      => $prefs['notify_messages'] ?? ($artistData['notify_messages'] ?? true),
            'status'               => $account['status'] ?? ($artistData['status'] ?? 'active'),
        ];

        // Slug handling
        if (!empty($slug)) {
            $slugCollision = User::withTrashed()
                ->where('slug', $slug)
                ->when($user, fn ($q) => $q->where('id', '!=', $user->id))
                ->exists();

            $attributes['slug'] = $slugCollision ? User::generateUniqueSlug($slug) : $slug;
        }

        if ($user) {
            if ($conflictMode === 'replace') {
                // Wipe user's previous collections and artworks
                foreach ($user->collections()->withTrashed()->get() as $col) {
                    $col->artworks()->detach();
                    $col->forceDelete();
                }
                $user->artworks()->withTrashed()->forceDelete();
            }

            $user->update($attributes);
        } else {
            $attributes['password'] = Hash::make('password');
            $attributes['email_verified_at'] = now();
            $attributes['onboarding_step'] = 3;
            $attributes['onboarding_completed_at'] = now();

            $user = new User();
            if (!empty($id) && !User::withTrashed()->where('id', $id)->exists()) {
                $user->id = $id;
            }
            $user->fill($attributes);
            $user->save();
        }

        // Import Artworks
        $artworksData = $artistData['artworks'] ?? [];
        $artworkIdMap = [];

        foreach ($artworksData as $artItem) {
            $artExtUrl = $artItem['external_url'] ?? null;
            $artTitle = $artItem['title'] ?? 'Artwork';

            $artAttrs = [
                'user_id'         => $user->id,
                'title'           => $artTitle,
                'description'     => $artItem['description'] ?? null,
                'category'        => $artItem['category'] ?? 'image',
                'provider'        => $artItem['provider'] ?? 'pinterest',
                'render_type'     => $artItem['render_type'] ?? 'image',
                'external_url'    => $artExtUrl ?: 'https://artfolio.test/artwork/' . uniqid(),
                'thumbnail_url'   => $artItem['thumbnail_url'] ?? null,
                'price_hint'      => $artItem['price_hint'] ?? null,
                'is_featured'     => (bool) ($artItem['is_featured'] ?? false),
                'is_ai_generated' => (bool) ($artItem['is_ai_generated'] ?? false),
                'status'          => $artItem['status'] ?? 'published',
            ];

            $artwork = null;
            if ($conflictMode === 'update') {
                $query = $user->artworks()->withTrashed();
                if ($artExtUrl) {
                    $query->where('external_url', $artExtUrl);
                } else {
                    $query->where('title', $artTitle);
                }
                $artwork = $query->first();

                if ($artwork) {
                    if ($artwork->trashed()) {
                        $artwork->restore();
                    }
                    $artwork->update($artAttrs);
                }
            }

            if (!$artwork) {
                $artwork = Artwork::create($artAttrs);
            }

            if (isset($artItem['id'])) {
                $artworkIdMap[$artItem['id']] = $artwork->id;
            }
        }

        // Import Collections
        $collectionsData = $artistData['collections'] ?? [];
        foreach ($collectionsData as $colItem) {
            $colName = $colItem['name'] ?? 'Collection';
            $colAttrs = [
                'user_id'     => $user->id,
                'name'        => $colName,
                'description' => $colItem['description'] ?? null,
                'visibility'  => $colItem['visibility'] ?? 'public',
            ];

            $collection = null;
            if ($conflictMode === 'update') {
                $collection = $user->collections()->withTrashed()->where('name', $colName)->first();
                if ($collection) {
                    if ($collection->trashed()) {
                        $collection->restore();
                    }
                    $collection->update($colAttrs);
                }
            }

            if (!$collection) {
                $collection = Collection::create($colAttrs);
            }

            // Sync collection artworks
            $rawArtworkIds = $colItem['artwork_ids'] ?? [];
            $mappedArtworkIds = [];
            foreach ($rawArtworkIds as $oldArtId) {
                if (isset($artworkIdMap[$oldArtId])) {
                    $mappedArtworkIds[] = $artworkIdMap[$oldArtId];
                } elseif (Artwork::where('id', $oldArtId)->where('user_id', $user->id)->exists()) {
                    $mappedArtworkIds[] = $oldArtId;
                }
            }

            if (!empty($mappedArtworkIds)) {
                $collection->artworks()->syncWithoutDetaching($mappedArtworkIds);
            }
        }

        return $user;
    }

    /**
     * Build the base filtered query for listing or exporting users.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = User::withCount(['artworks', 'collections'])->latest();

        $status = $request->get('status', 'all');
        if (in_array($status, ['active', 'suspended'])) {
            $query->where('status', $status);
        }

        $role = $request->get('role');
        if ($role === 'admin') {
            $query->where('is_admin', true);
        } elseif ($role === 'artist') {
            $query->where('is_admin', false);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Format a user's full account, professional profile, artworks, and collections into an array.
     */
    protected function formatArtistExportPayload(User $user): array
    {
        $artworks = $user->relationLoaded('artworks') ? $user->artworks : collect();
        $collections = $user->relationLoaded('collections') ? $user->collections : collect();

        return [
            'account' => [
                'id'                      => $user->id,
                'name'                    => $user->name,
                'slug'                    => $user->slug,
                'portfolio_url'           => $user->slug ? route('profile.public', $user->slug) : null,
                'email'                   => $user->email,
                'contact_email'           => $user->contact_email,
                'public_email_enabled'    => (bool) $user->public_email_enabled,
                'role'                    => $user->is_admin ? 'administrator' : 'artist',
                'is_admin'                => (bool) $user->is_admin,
                'status'                  => $user->status,
                'email_verified_at'       => $user->email_verified_at?->toIso8601String(),
                'onboarding_completed_at' => $user->onboarding_completed_at?->toIso8601String(),
                'created_at'              => $user->created_at?->toIso8601String(),
                'updated_at'              => $user->updated_at?->toIso8601String(),
            ],
            'professional_profile' => [
                'headline'          => $user->headline,
                'bio'               => $user->bio,
                'city'              => $user->city,
                'country'           => $user->country,
                'commission_status' => $user->commission_status,
                'price_guide'       => $user->price_guide,
                'skills'            => is_array($user->skills) ? $user->skills : [],
                'software'          => is_array($user->software) ? $user->software : [],
                'hiring_types'      => is_array($user->hiring_types) ? $user->hiring_types : [],
            ],
            'visual_identity' => [
                'avatar_url'     => $user->avatar_url,
                'avatar_framing' => [
                    'crop_x'    => (float) ($user->crop_x ?? 0),
                    'crop_y'    => (float) ($user->crop_y ?? 0),
                    'crop_zoom' => (float) ($user->crop_zoom ?? 1),
                ],
                'banner_url'     => $user->banner_url,
                'banner_framing' => [
                    'banner_crop_x'    => (float) ($user->banner_crop_x ?? 0),
                    'banner_crop_y'    => (float) ($user->banner_crop_y ?? 0),
                    'banner_crop_zoom' => (float) ($user->banner_crop_zoom ?? 1),
                ],
            ],
            'preferences' => [
                'show_mature_content' => (bool) $user->show_mature_content,
                'hide_ai_content'     => (bool) $user->hide_ai_content,
                'notify_new_artworks' => (bool) $user->notify_new_artworks,
                'notify_messages'     => (bool) $user->notify_messages,
            ],
            'summary_counts' => [
                'total_artworks'          => $artworks->count(),
                'published_artworks'      => $artworks->whereNull('deleted_at')->where('status', 'published')->count(),
                'trashed_artworks'        => $artworks->whereNotNull('deleted_at')->count(),
                'ai_assisted_artworks'    => $artworks->where('is_ai_generated', true)->count(),
                'total_collections'       => $collections->count(),
                'bookmarks_saved_count'   => (int) ($user->bookmarks_count ?? 0),
                'reports_submitted_count' => (int) ($user->reports_count ?? 0),
            ],
            'artworks' => $artworks->map(fn ($artwork) => [
                'id'              => $artwork->id,
                'title'           => $artwork->title,
                'description'     => $artwork->description,
                'category'        => $artwork->category,
                'provider'        => $artwork->provider,
                'render_type'     => $artwork->render_type,
                'external_url'    => $artwork->external_url,
                'thumbnail_url'   => $artwork->thumbnail_url,
                'price_hint'      => $artwork->price_hint,
                'is_featured'     => (bool) $artwork->is_featured,
                'is_ai_generated' => (bool) $artwork->is_ai_generated,
                'status'          => $artwork->status,
                'created_at'      => $artwork->created_at?->toIso8601String(),
                'deleted_at'      => $artwork->deleted_at?->toIso8601String(),
            ])->values()->all(),
            'collections' => $collections->map(fn ($collection) => [
                'id'             => $collection->id,
                'name'           => $collection->name,
                'description'    => $collection->description,
                'visibility'     => $collection->visibility ?? 'public',
                'share_token'    => $collection->share_token,
                'artworks_count' => (int) ($collection->artworks_count ?? $collection->artworks->count()),
                'artwork_ids'    => $collection->relationLoaded('artworks') ? $collection->artworks->pluck('id')->values()->all() : [],
                'created_at'     => $collection->created_at?->toIso8601String(),
                'deleted_at'     => $collection->deleted_at?->toIso8601String(),
            ])->values()->all(),
        ];
    }

    /**
     * Temporarily suspend a user.
     */
    public function suspend(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own admin account.');
        }

        $user->update(['status' => 'suspended']);

        AuditLogger::log(
            'admin.user_suspended',
            $user,
            $user->name,
            ['status' => ['old' => 'active', 'new' => 'suspended']],
            "Admin suspended user account {$user->name} ({$user->email})"
        );

        return back()->with('success', "User \"{$user->name}\" has been suspended.");
    }

    /**
     * Reactivate a suspended user account.
     */
    public function activate(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->update(['status' => 'active']);

        AuditLogger::log(
            'admin.user_activated',
            $user,
            $user->name,
            ['status' => ['old' => 'suspended', 'new' => 'active']],
            "Admin reactivated user account {$user->name} ({$user->email})"
        );

        return back()->with('success', "User \"{$user->name}\" has been reactivated.");
    }

    /**
     * Toggle verified artist status.
     */
    public function toggleVerify(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $oldState = (bool) $user->is_verified;
        $user->update(['is_verified' => ! $oldState]);

        AuditLogger::log(
            $user->is_verified ? 'admin.user_verified' : 'admin.user_unverified',
            $user,
            $user->name,
            ['is_verified' => ['old' => $oldState, 'new' => (bool) $user->is_verified]],
            $user->is_verified
                ? "Admin granted Verified Artist badge to {$user->name}"
                : "Admin revoked Verified Artist badge from {$user->name}"
        );

        $message = $user->is_verified
            ? "User \"{$user->name}\" has been marked as a Verified Artist."
            : "Verification badge removed for user \"{$user->name}\".";

        return back()->with('success', $message);
    }

    /**
     * Delete user account (soft delete).
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();

        AuditLogger::log(
            'admin.user_deleted',
            $user,
            $userName,
            null,
            "Admin deleted user account {$userName} ({$userEmail})"
        );

        return back()->with('success', "User \"{$user->name}\" has been deleted.");
    }
}
