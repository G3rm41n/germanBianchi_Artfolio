<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestInterceptorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guests cannot bookmark artworks and receive 401.
     */
    public function test_guest_cannot_bookmark_artwork(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/obras/{$artwork->id}/bookmark");
        $response->assertStatus(401); // Unauthorized for JSON
    }

    /**
     * Guests cannot send contact messages to artists.
     */
    public function test_guest_cannot_send_contact_message(): void
    {
        $artist = User::factory()->create();

        $response = $this->postJson("/artista/{$artist->slug}/contact", [
            'subject' => 'Commission inquiry',
            'message' => 'Hello, I would like to request a custom illustration.',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Guests cannot report artworks.
     */
    public function test_guest_cannot_report_artwork(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/obras/{$artwork->id}/report", [
            'reason' => 'Broken or inaccessible link',
            'details' => 'Video fails to load.',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Public views include the guest modal component.
     */
    public function test_public_views_contain_guest_modal_component(): void
    {
        $artist = User::factory()->create();
        $artwork = Artwork::factory()->create(['user_id' => $artist->id]);

        // 1. Galería pública
        $responseExplorer = $this->get('/');
        $responseExplorer->assertStatus(200);
        $responseExplorer->assertSee('open-guest-modal');
        $responseExplorer->assertSee('handleOpen');

        // 2. Perfil público de artista
        $responseProfile = $this->get("/artista/{$artist->slug}");
        $responseProfile->assertStatus(200);
        $responseProfile->assertSee('open-guest-modal');
        $responseProfile->assertSee('handleOpen');
    }
}
