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
    'banner_url', 'banner_crop_x', 'banner_crop_y', 'banner_crop_zoom',
    'headline', 'city', 'country', 'skills', 'software', 'hiring_types',
    'public_email_enabled', 'contact_email', 'mature_filter_preference', 'show_mature_content', 'hide_ai_content',
    'notify_new_artworks', 'notify_messages', 'onboarding_step', 'onboarding_completed_at',
    'social_links', 'is_verified', 'commission_services'
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
            'banner_crop_x'           => 'float',
            'banner_crop_y'           => 'float',
            'banner_crop_zoom'        => 'float',
            'skills'                  => 'array',
            'software'                => 'array',
            'hiring_types'            => 'array',
            'public_email_enabled'    => 'boolean',
            'show_mature_content'     => 'boolean',
            'hide_ai_content'         => 'boolean',
            'notify_new_artworks'     => 'boolean',
            'notify_messages'         => 'boolean',
            'onboarding_step'         => 'integer',
            'onboarding_completed_at' => 'datetime',
            'social_links'            => 'array',
            'is_verified'             => 'boolean',
            'commission_services'     => 'array',
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

    public function wantsMatureHidden(): bool
    {
        return $this->mature_filter_preference === 'hide';
    }

    public function wantsMatureBlurred(): bool
    {
        return ($this->mature_filter_preference ?? 'blur') === 'blur';
    }

    public function wantsMatureShown(): bool
    {
        return $this->mature_filter_preference === 'show';
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

    public function likedArtworks()
    {
        return $this->belongsToMany(Artwork::class, 'artwork_likes')
                    ->withTimestamps();
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'user_id', 'blocked_user_id')
                    ->withTimestamps();
    }

    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_user_id', 'user_id')
                    ->withTimestamps();
    }

    public function isBlocking(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->blockedUsers()->where('blocked_user_id', $userId)->exists();
    }

    public function block(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        if ($userId !== $this->id && !$this->isBlocking($userId)) {
            $this->blockedUsers()->attach($userId);
        }
    }

    public function unblock(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        $this->blockedUsers()->detach($userId);
    }
}
