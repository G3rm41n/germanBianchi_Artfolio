<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'name', 'email', 'password', 'slug', 'bio', 'commission_status',
    'is_admin', 'status', 'avatar_url', 'crop_x', 'crop_y', 'crop_zoom',
    'headline', 'city', 'country', 'skills', 'software', 'hiring_types',
    'public_email_enabled', 'contact_email', 'onboarding_step', 'onboarding_completed_at'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'is_admin'                => 'boolean',
            'skills'                  => 'array',
            'software'                => 'array',
            'hiring_types'            => 'array',
            'public_email_enabled'    => 'boolean',
            'onboarding_step'         => 'integer',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function isOnboarded(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function getPublicEmail(): ?string
    {
        if (! $this->public_email_enabled) {
            return null;
        }

        return $this->contact_email ?: $this->email;
    }

    // --- Slug auto-generado al crear ---
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->slug)) {
                $user->slug = static::generateUniqueSlug($user->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name, '_');
        $slug = $base;
        $i    = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '_' . $i++;
        }
        return $slug;
    }

    // --- Scope: solo administradores ---
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    // --- Helpers ---
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // ── Relaciones ───────────────────────────────────────────────────────────────
    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Artwork::class, 'bookmarks')
                    ->withTimestamps();
    }
}
