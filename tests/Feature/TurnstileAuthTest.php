<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TurnstileAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        config(['services.turnstile.enabled' => false]);
        parent::tearDown();
    }

    public function test_login_works_without_turnstile_when_disabled(): void
    {
        config(['services.turnstile.enabled' => false]);

        $user = User::factory()->create([
            'email' => 'artist@example.com',
            'password' => bcrypt('password123'),
        ]);

        $viewResponse = $this->get(route('login'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertDontSee('cf-turnstile');

        $loginResponse = $this->post(route('login'), [
            'email' => 'artist@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_renders_turnstile_widget_when_enabled(): void
    {
        config(['services.turnstile.enabled' => true]);

        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('cf-turnstile');
        $response->assertSee('challenges.cloudflare.com/turnstile');
    }

    public function test_login_fails_when_turnstile_response_is_missing_and_enabled(): void
    {
        config(['services.turnstile.enabled' => true]);

        $user = User::factory()->create([
            'email' => 'artist@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'artist@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertGuest();
    }

    public function test_login_fails_when_cloudflare_rejects_turnstile_token(): void
    {
        config(['services.turnstile.enabled' => true]);

        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => false], 200),
        ]);

        $user = User::factory()->create([
            'email' => 'artist@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'artist@example.com',
            'password' => 'password123',
            'cf-turnstile-response' => 'invalid-bot-token',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertGuest();
    }

    public function test_login_succeeds_when_cloudflare_verifies_turnstile_token(): void
    {
        config(['services.turnstile.enabled' => true]);

        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => true], 200),
        ]);

        $user = User::factory()->create([
            'email' => 'artist@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'artist@example.com',
            'password' => 'password123',
            'cf-turnstile-response' => 'valid-human-token',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}

