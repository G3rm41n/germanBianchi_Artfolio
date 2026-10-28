<?php

namespace App\Jobs;

use App\Mail\LinkDownAlert;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLinkDownAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Artwork $artwork;
    public User $artist;
    public ?string $adminNote;

    /**
     * El número de intentos permitidos para el trabajo.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Artwork $artwork, User $artist, ?string $adminNote = null)
    {
        $this->artwork = $artwork;
        $this->artist = $artist;
        $this->adminNote = $adminNote;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->artist->email)->send(new LinkDownAlert(
            $this->artwork,
            $this->artist,
            $this->adminNote
        ));
    }
}
