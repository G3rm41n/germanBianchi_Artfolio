<?php

namespace Tests\Feature;

use App\Mail\LinkDownAlert;
use App\Models\User;
use App\Models\Artwork;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminManualReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An authenticated user can report an artwork and cannot send duplicate pending reports.
     */
    public function test_user_can_report_artwork_and_prevent_pending_duplicates(): void
    {
        $reporter = User::factory()->create();
        $artwork = Artwork::factory()->create();

        // 1. Submit first report
        $response1 = $this->actingAs($reporter)->postJson("/obras/{$artwork->id}/report", [
            'reason' => 'Broken or inaccessible link',
            'details' => 'The YouTube video is marked as private.',
        ]);

        $response1->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'user_id' => $reporter->id,
            'artwork_id' => $artwork->id,
            'reason' => 'Broken or inaccessible link',
            'status' => 'pending',
        ]);

        // 2. Duplicate report attempt while first is still pending
        $response2 = $this->actingAs($reporter)->postJson("/obras/{$artwork->id}/report", [
            'reason' => 'Broken or inaccessible link',
            'details' => 'Retrying same report.',
        ]);

        $response2->assertStatus(422);
        $response2->assertJsonFragment([
            'message' => 'You already have a pending report for this artwork.',
        ]);
    }

    /**
     * Admin panel routes require administrator privileges.
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
     * Admin broken link alert moderation:
     * - Artwork is immediately hidden (status = 'hidden')
     * - Report marked as resolved (status = 'resolved')
     * - Email sent to artist
     */
    public function test_admin_link_down_alert_moderation_flow(): void
    {
        Mail::fake();

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
            'reason' => 'Broken or inaccessible link',
            'details' => 'The link no longer responds.',
            'status' => 'pending',
        ]);

        // Admin views report in dashboard
        $responseIndex = $this->actingAs($admin)->get('/admin/reports');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Video Musical Caído');

        // Admin triggers broken link alert
        $responseAction = $this->actingAs($admin)->post("/admin/reports/{$report->id}/link-down-alert", [
            'admin_note' => 'Confirmed broken link. Please update the URL.',
        ]);

        $responseAction->assertRedirect();

        // 1. Artwork status changed to 'hidden'
        $this->assertEquals('hidden', $artwork->fresh()->status);

        // 2. Report marked as resolved
        $refreshedReport = $report->fresh();
        $this->assertEquals('resolved', $refreshedReport->status);
        $this->assertStringContainsString('Confirmed broken link', $refreshedReport->admin_note);

        // 3. Email sent to artist
        Mail::assertSent(LinkDownAlert::class, function ($mail) use ($artist) {
            return $mail->hasTo($artist->email);
        });
    }
}
