<?php

namespace Tests\Feature;

use App\Jobs\SendLinkDownAlert;
use App\Models\User;
use App\Models\Artwork;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminManualReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * RF-09: Un usuario autenticado puede reportar una obra; no se permiten reportes duplicados pendientes.
     */
    public function test_user_can_report_artwork_and_prevent_pending_duplicates(): void
    {
        $reporter = User::factory()->create();
        $artwork = Artwork::factory()->create();

        // 1. Envío de primer reporte
        $response1 = $this->actingAs($reporter)->postJson("/obras/{$artwork->id}/report", [
            'reason' => 'Enlace caído o inaccesible',
            'details' => 'El video en YouTube fue marcado como privado.',
        ]);

        $response1->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'user_id' => $reporter->id,
            'artwork_id' => $artwork->id,
            'reason' => 'Enlace caído o inaccesible',
            'status' => 'pending',
        ]);

        // 2. Intento de reporte duplicado mientras el primero sigue pendiente (RF-09 / RN-02)
        $response2 = $this->actingAs($reporter)->postJson("/obras/{$artwork->id}/report", [
            'reason' => 'Enlace caído o inaccesible',
            'details' => 'Reintentando enviar el mismo reporte.',
        ]);

        $response2->assertStatus(422);
        $response2->assertJsonFragment([
            'message' => 'Ya has enviado un reporte para esta obra que se encuentra pendiente de revisión.',
        ]);
    }

    /**
     * RF-09 / RF-12: Las rutas del panel administrativo exigen rol de administrador (is_admin = true).
     */
    public function test_non_admin_users_are_forbidden_from_admin_panel(): void
    {
        $regularUser = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($regularUser)->get('/admin');
        $response->assertStatus(403);

        $responseReports = $this->actingAs($regularUser)->get('/admin/reports');
        $responseReports->assertStatus(403);
    }

    /**
     * RF-09 / RN-02: Moderación manual con alerta de enlace caído:
     * - Oculta la obra inmediatamente (status = 'hidden')
     * - Marca el reporte como resuelto (status = 'resolved')
     * - Encola el Job asíncrono SendLinkDownAlert
     */
    public function test_admin_link_down_alert_moderation_flow(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $artist = User::factory()->create(['email' => 'artista@artfolio.test']);
        $reporter = User::factory()->create();

        $artwork = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title' => 'Video Musical Caído',
            'status' => 'published',
        ]);

        $report = Report::create([
            'user_id' => $reporter->id,
            'artwork_id' => $artwork->id,
            'reason' => 'Enlace caído o inaccesible',
            'details' => 'El enlace ya no responde.',
            'status' => 'pending',
        ]);

        // El administrador visualiza el reporte en la bandeja
        $responseIndex = $this->actingAs($admin)->get('/admin/reports');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Video Musical Caído');

        // El administrador envía la alerta de enlace caído
        $responseAction = $this->actingAs($admin)->post("/admin/reports/{$report->id}/link-down-alert", [
            'admin_note' => 'Confirmado enlace caído. Se solicita reemplazo de URL.',
        ]);

        $responseAction->assertRedirect();

        // 1. La obra pasó automáticamente a status 'hidden'
        $this->assertEquals('hidden', $artwork->fresh()->status);

        // 2. El reporte se marcó como 'resolved'
        $refreshedReport = $report->fresh();
        $this->assertEquals('resolved', $refreshedReport->status);
        $this->assertStringContainsString('Confirmado enlace caído', $refreshedReport->admin_note);

        // 3. Se encoló el Job SendLinkDownAlert para el artista
        Queue::assertPushed(SendLinkDownAlert::class, function ($job) use ($artist, $artwork) {
            return $job->artist->id === $artist->id && $job->artwork->id === $artwork->id;
        });
    }
}
