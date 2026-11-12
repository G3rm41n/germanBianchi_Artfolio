<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artwork extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    protected $fillable = [
        'user_id', 'title', 'description', 'price_hint',
        'external_url', 'provider', 'category', 'subcategory', 'is_ai_generated', 'is_mature', 'render_type',
        'thumbnail_url', 'embed_html', 'status', 'is_featured',
        'views_count', 'is_pinned', 'pinned_at',
        'likes_count', 'software_used', 'license',
    ];

    protected $appends = ['preview_url', 'high_res_url'];

    protected function casts(): array
    {
        return [
            'is_featured'     => 'boolean',
            'is_ai_generated' => 'boolean',
            'is_mature'       => 'boolean',
            'is_pinned'       => 'boolean',
            'pinned_at'       => 'datetime',
            'views_count'     => 'integer',
            'likes_count'     => 'integer',
            'software_used'   => 'array',
            'license'         => 'string',
        ];
    }

    /**
     * URL optimizada y ligera para las tarjetas de la galería (ahorro drástico de ancho de banda).
     */
    public function getPreviewUrlAttribute(): ?string
    {
        $url = $this->thumbnail_url ?: $this->external_url;
        if (!$url) return null;

        // Pinterest CDN: usar versión compacta /474x/ (~25 KB en lugar de originals de varios megabytes)
        if (str_contains($url, 'pinimg.com')) {
            return preg_replace('#/(originals|736x|564x)/#', '/474x/', $url);
        }

        // Unsplash: redimensionar a ancho óptimo de 600px en WebP con compresión balanceada
        if (str_contains($url, 'unsplash.com')) {
            $base = strtok($url, '?');
            return "{$base}?auto=format&fit=crop&w=600&q=75";
        }

        // YouTube: usar hqdefault para carga instantánea de miniaturas en la cuadrícula
        if (str_contains($url, 'img.youtube.com') && str_contains($url, 'maxresdefault.jpg')) {
            return str_replace('maxresdefault.jpg', 'hqdefault.jpg', $url);
        }

        return $url;
    }

    /**
     * URL en máxima resolución para el visor modal interactivo.
     */
    public function getHighResUrlAttribute(): ?string
    {
        $url = $this->thumbnail_url ?: $this->external_url;
        if (!$url) return null;

        // Pinterest: asegurar máxima resolución /originals/
        if (str_contains($url, 'pinimg.com')) {
            return preg_replace('#/(236x|474x|564x)/#', '/originals/', $url);
        }

        // Unsplash: versión nítida en 1920px a calidad 90%
        if (str_contains($url, 'unsplash.com')) {
            $base = strtok($url, '?');
            return "{$base}?auto=format&fit=crop&w=1920&q=90";
        }

        return $url;
    }

    // Auto-prune soft deleted artworks after 30 days
    public function prunable()
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(30));
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'artwork_likes')->withTimestamps();
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Motor de recomendación de obras similares (Estilo Pinterest).
     * Pondera etiquetas coincidentes, misma subcategoría/categoría y obras destacadas.
     */
    public function getSimilarArtworks(int $limit = 16, ?User $currentUser = null)
    {
        $currentId = $this->id;
        $currentCategory = $this->category;
        $currentSubcategory = $this->subcategory;
        
        $tagIds = $this->relationLoaded('tags') 
            ? $this->tags->pluck('id')->all()
            : $this->tags()->pluck('tags.id')->all();

        $selectedIds = collect([$currentId]);

        // 1. Obras que coinciden en tags (ordenadas por mayor coincidencia de etiquetas)
        $withMatchingTags = collect();
        if (!empty($tagIds)) {
            $withMatchingTags = static::published()
                ->where('id', '!=', $currentId)
                ->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagIds))
                ->with(['user', 'tags', 'likes'])
                ->withCount(['tags as match_score' => fn($q) => $q->whereIn('tags.id', $tagIds)])
                ->orderByDesc('match_score')
                ->orderByDesc('likes_count')
                ->take($limit)
                ->get();

            $selectedIds = $selectedIds->merge($withMatchingTags->pluck('id'));
        }

        // 2. Si faltan obras para el límite, complementar con misma subcategoría o categoría
        $withSameCategory = collect();
        $needed = $limit - $withMatchingTags->count();
        if ($needed > 0) {
            $categoryQuery = static::published()
                ->whereNotIn('id', $selectedIds)
                ->where('category', $currentCategory)
                ->with(['user', 'tags', 'likes']);

            if (!empty($currentSubcategory)) {
                $categoryQuery->orderByRaw("CASE WHEN subcategory = ? THEN 0 ELSE 1 END", [$currentSubcategory]);
            }

            $withSameCategory = $categoryQuery
                ->orderByDesc('likes_count')
                ->orderByDesc('views_count')
                ->take($needed)
                ->get();

            $selectedIds = $selectedIds->merge($withSameCategory->pluck('id'));
        }

        // 3. Si aún faltan, complementar con obras populares generales
        $fallback = collect();
        $stillNeeded = $limit - ($withMatchingTags->count() + $withSameCategory->count());
        if ($stillNeeded > 0) {
            $fallback = static::published()
                ->whereNotIn('id', $selectedIds)
                ->with(['user', 'tags', 'likes'])
                ->orderByDesc('likes_count')
                ->orderByDesc('created_at')
                ->take($stillNeeded)
                ->get();
        }

        return $withMatchingTags->concat($withSameCategory)->concat($fallback);
    }

    // Query Scopes

    /** Filter published artworks */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /** Filter pinned artworks */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /** Filter by tag slug */
    public function scopeByTag($query, string $tagSlug)
    {
        return $query->whereHas('tags', fn($q) => $q->where('slug', $tagSlug));
    }

    /** Filter by category */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /** Exclude AI-generated artworks */
    public function scopeNotAi($query)
    {
        return $query->where('is_ai_generated', false);
    }

    /** Include only AI-generated artworks */
    public function scopeOnlyAi($query)
    {
        return $query->where('is_ai_generated', true);
    }

    /** Exclude mature / sensitive 18+ artworks */
    public function scopeNotMature($query)
    {
        return $query->where('is_mature', false);
    }

    /** Include only mature / sensitive 18+ artworks */
    public function scopeOnlyMature($query)
    {
        return $query->where('is_mature', true);
    }

    /** Search by title, description, or artist name */
    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
        });
    }
}
