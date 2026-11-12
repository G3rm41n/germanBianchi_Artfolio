<?php

namespace App\Notifications;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ArtworkBookmarkedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Artwork $artwork,
        public User $actor
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bookmark',
            'artwork_id' => $this->artwork->id,
            'artwork_title' => $this->artwork->title,
            'artwork_preview' => $this->artwork->preview_url,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'actor_avatar' => $this->actor->avatar_url,
            'message' => "{$this->actor->name} guardó tu obra \"{$this->artwork->title}\" en sus favoritos.",
            'url' => route('profile.public', ['slug' => $this->artwork->user->username ?? $this->artwork->user->id]) . '#artwork-' . $this->artwork->id,
            'created_at' => now()->toIso8601String(),
        ];
    }
}

