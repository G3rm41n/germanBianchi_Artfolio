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

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    // ── Purga automática a los 30 días (RN-04) ──────────────────────────────
    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }

    // ── Relaciones ───────────────────────────────────────────────────────────
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

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Filtra solo obras publicadas */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /** Filtra por categoría */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Buscador de texto libre multi-campo (RF-07)
     * Busca en: título, descripción y nombre del artista
     */
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
