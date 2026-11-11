<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    protected $fillable = ['user_id', 'name', 'description', 'visibility'];

    // Auto-prune soft deleted collections after 30 days
    public function prunable()
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(30));
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
