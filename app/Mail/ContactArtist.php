<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactArtist extends Mailable
{
    use Queueable, SerializesModels;

    public string $senderName;
    public string $senderEmail;
    public string $recipientName;
    public string $subjectText;
    public string $messageText;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $senderName,
        string $senderEmail,
        string $recipientName,
        string $subjectText,
        string $messageText
    ) {
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->recipientName = $recipientName;
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Artfolio] Mensaje de {$this->senderName}: {$this->subjectText}",
            replyTo: [
                new Address($this->senderEmail, $this->senderName)
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-artist',
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
