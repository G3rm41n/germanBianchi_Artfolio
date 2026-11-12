<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'visibility',
        'preview_artwork_ids',
        'sort_order',
        'last_artwork_added_at',
    ];

    protected function casts(): array
    {
        return [
            'preview_artwork_ids' => 'array',
            'sort_order' => 'integer',
            'last_artwork_added_at' => 'datetime',
        ];
    }

    /**
     * Get the 3 preview artworks for the card mosaic (Customized + Smart Fallback).
     */
    public function getPreviewArtworksAttribute()
    {
        $allCollectionArtworks = $this->relationLoaded('artworks')
            ? $this->artworks
            : $this->artworks()->published()->get();

        $byKey = $allCollectionArtworks->keyBy('id');
        $previews = collect();

        // 1. If user chose customized preview IDs
        if (!empty($this->preview_artwork_ids) && is_array($this->preview_artwork_ids)) {
            foreach ($this->preview_artwork_ids as $id) {
                if ($artwork = $byKey->get($id)) {
                    $previews->push($artwork);
                }
            }
        }

        // 2. If fewer than 3, complete with newest artworks in collection
        if ($previews->count() < 3) {
            $existingIds = $previews->pluck('id')->all();
            $remainings = $allCollectionArtworks->reject(fn($art) => in_array($art->id, $existingIds))->take(3 - $previews->count());
            $previews = $previews->concat($remainings);
        }

        return $previews->take(3);
    }

    // Auto-prune soft deleted collections after 30 days
    public function prunable()
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(30));
    }

    /**
     * Get the public board URL with artist prefix (Pinterest style: /{artist}/collections/{collection}).
     */
    public function getUrlAttribute(): string
    {
        $artistSlug = $this->relationLoaded('user') && $this->user?->slug
            ? $this->user->slug
            : ($this->user?->slug ?: $this->user_id);

        return route('collections.show', [
            'artist' => $artistSlug,
            'collection' => $this->id,
        ]);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class);
    }

    // Visibility Helpers
    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isUnlisted(): bool
    {
        return $this->visibility === 'unlisted';
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }

    public function canBeViewedBy(?User $user): bool
    {
        if ($this->isPublic() || $this->isUnlisted()) {
            return true;
        }

        // Private collections are only visible to the owner (or admin)
        return $user !== null && ($user->id === $this->user_id || $user->isAdmin());
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeListedForUser($query, ?User $viewer, int $ownerId)
    {
        if ($viewer && $viewer->id === $ownerId) {
            return $query;
        }

        return $query->where('visibility', 'public');
    }
}
