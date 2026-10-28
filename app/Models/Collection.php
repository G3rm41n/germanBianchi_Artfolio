<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    protected $fillable = ['user_id', 'name', 'description'];

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
}
