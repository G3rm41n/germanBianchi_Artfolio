<?php

namespace App\Mail;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LinkDownAlert extends Mailable
{
    use Queueable, SerializesModels;

    public Artwork $artwork;
    public User $artist;
    public ?string $adminNote;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Artwork $artwork,
        User $artist,
        ?string $adminNote = null
    ) {
        $this->artwork = $artwork;
        $this->artist = $artist;
        $this->adminNote = $adminNote;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Artfolio] Notificación importante: Enlace no disponible en tu obra \"{$this->artwork->title}\"",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.link-down-alert',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
