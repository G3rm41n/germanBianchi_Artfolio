<?php

namespace App\Notifications;

use App\Models\Artwork;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ArtworkCommentedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Artwork $artwork,
        public Comment $comment,
        public User $actor
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment',
            'artwork_id' => $this->artwork->id,
            'artwork_title' => $this->artwork->title,
            'artwork_preview' => $this->artwork->preview_url,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'actor_avatar' => $this->actor->avatar_url,
            'comment_id' => $this->comment->id,
            'message' => "{$this->actor->name} comentó en tu obra \"{$this->artwork->title}\".",
            'url' => route('artworks.show', $this->artwork->id) . '#comments',
            'created_at' => now()->toIso8601String(),
        ];
    }
}

