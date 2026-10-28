<?php

namespace App\Jobs;

use App\Mail\ContactArtist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendContactEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $senderName;
    public string $senderEmail;
    public string $recipientEmail;
    public string $recipientName;
    public string $subjectText;
    public string $messageText;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $senderName,
        string $senderEmail,
        string $recipientEmail,
        string $recipientName,
        string $subjectText,
        string $messageText
    ) {
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->recipientEmail = $recipientEmail;
        $this->recipientName = $recipientName;
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->recipientEmail)->send(new ContactArtist(
            $this->senderName,
            $this->senderEmail,
            $this->recipientName,
            $this->subjectText,
            $this->messageText
        ));
    }
}
