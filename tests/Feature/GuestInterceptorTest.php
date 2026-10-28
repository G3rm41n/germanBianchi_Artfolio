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
     * RF-08: Intentar marcar favoritos como visitante no autenticado es interceptado / redirige a login.
     */
    public function test_guest_cannot_bookmark_artwork(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/obras/{$artwork->id}/bookmark");
        $response->assertStatus(401); // No autenticado para JSON
    }

    /**
     * RF-08 / RN-05: Intentar contactar a un artista como visitante no autenticado es interceptado.
     */
    public function test_guest_cannot_send_contact_message(): void
    {
        $artist = User::factory()->create();

        $response = $this->postJson("/artista/{$artist->slug}/contact", [
            'subject' => 'Consulta de comisión',
            'message' => 'Hola, quisiera encargar una ilustración personalizada.',
        ]);

        $response->assertStatus(401);
    }

    /**
     * RF-08 / RF-09: Intentar reportar una obra como visitante no autenticado es interceptado.
     */
    public function test_guest_cannot_report_artwork(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/obras/{$artwork->id}/report", [
            'reason' => 'Enlace caído o inaccesible',
            'details' => 'No carga el video.',
        ]);

        $response->assertStatus(401);
    }

    /**
     * RF-08: Las vistas públicas integran el componente x-guest-modal para visitantes.
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
