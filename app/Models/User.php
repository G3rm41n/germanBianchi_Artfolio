<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'slug', 'bio', 'commission_status', 'is_admin', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
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
}
