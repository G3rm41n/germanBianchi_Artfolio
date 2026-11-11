<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_call_keep_alive_endpoint(): void
    {
        $response = $this->postJson('/session/keep-alive');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_extend_session_via_keep_alive(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/session/keep-alive');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Session extended successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'lifetime_minutes',
            ]);
    }

    public function test_login_screen_displays_session_expired_notice(): void
    {
        $response = $this->get('/login?status=session-expired');

        $response->assertStatus(200);
        $response->assertSee('Your session expired due to 10 minutes of inactivity');
    }

    public function test_session_timeout_modal_is_rendered_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Are you still there?');
        $response->assertSee('Keep me signed in (+10m)');
        $response->assertSee('Sign out now');
    }
}
