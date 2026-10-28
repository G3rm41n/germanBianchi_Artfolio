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
        'external_url', 'provider', 'category', 'render_type',
        'thumbnail_url', 'embed_html', 'status', 'is_featured',
    ];

    protected $appends = ['preview_url', 'high_res_url'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
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

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // Query Scopes

    /** Filter published artworks */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /** Filter by category */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
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
