<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CuratedTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'slug',
        'is_suggested',
        'sort_order',
    ];

    protected $casts = [
        'is_suggested' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function (CuratedTag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function scopeDisciplines(Builder $query): Builder
    {
        return $query->where('type', 'discipline');
    }

    public function scopeSoftware(Builder $query): Builder
    {
        return $query->where('type', 'software');
    }

    public function scopeSuggested(Builder $query): Builder
    {
        return $query->where('is_suggested', true);
    }

    /**
     * Rename a tag across all user profile JSON arrays in cascade.
     */
    public static function syncCascadeRename(string $type, string $oldName, string $newName): int
    {
        if (trim($oldName) === trim($newName)) {
            return 0;
        }

        $column = $type === 'software' ? 'software' : 'skills';
        $affectedUsersCount = 0;

        // Fetch users where the column is not null
        User::whereNotNull($column)->chunkById(100, function ($users) use ($column, $oldName, $newName, &$affectedUsersCount) {
            foreach ($users as $user) {
                $raw = $user->{$column};
                $items = is_array($raw) ? $raw : (json_decode($raw ?? '[]', true) ?: []);
                
                $changed = false;
                $updatedItems = array_map(function ($item) use ($oldName, $newName, &$changed) {
                    if (strcasecmp(trim($item), trim($oldName)) === 0) {
                        $changed = true;
                        return $newName;
                    }
                    return $item;
                }, $items);

                if ($changed) {
                    $user->{$column} = array_values(array_unique($updatedItems));
                    $user->save();
                    $affectedUsersCount++;
                }
            }
        });

        return $affectedUsersCount;
    }

    /**
     * Remove a tag from all user profile JSON arrays in cascade.
     */
    public static function syncCascadeRemove(string $type, string $name): int
    {
        $column = $type === 'software' ? 'software' : 'skills';
        $affectedUsersCount = 0;

        User::whereNotNull($column)->chunkById(100, function ($users) use ($column, $name, &$affectedUsersCount) {
            foreach ($users as $user) {
                $raw = $user->{$column};
                $items = is_array($raw) ? $raw : (json_decode($raw ?? '[]', true) ?: []);

                $filtered = array_values(array_filter($items, function ($item) use ($name) {
                    return strcasecmp(trim($item), trim($name)) !== 0;
                }));

                if (count($filtered) !== count($items)) {
                    $user->{$column} = $filtered;
                    $user->save();
                    $affectedUsersCount++;
                }
            }
        });

        return $affectedUsersCount;
    }

    /**
     * Compute count of users using this tag.
     */
    public function getUsersCountAttribute(): int
    {
        $column = $this->type === 'software' ? 'software' : 'skills';
        $name = $this->name;

        // Query users whose json array contains this value or match in PHP
        $count = 0;
        User::whereNotNull($column)->select(['id', $column])->chunkById(200, function ($users) use ($column, $name, &$count) {
            foreach ($users as $user) {
                $items = is_array($user->{$column}) ? $user->{$column} : (json_decode($user->{$column} ?? '[]', true) ?: []);
                foreach ($items as $item) {
                    if (strcasecmp(trim($item), trim($name)) === 0) {
                        $count++;
                        break;
                    }
                }
            }
        });

        return $count;
    }
}

