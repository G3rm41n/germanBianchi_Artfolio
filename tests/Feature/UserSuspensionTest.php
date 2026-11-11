<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSuspensionTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_access_authenticated_routes(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_suspended_user_is_logged_out_and_redirected_when_accessing_routes(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_suspended_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'status' => 'suspended',
            'email' => 'banned@artfolio.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'banned@artfolio.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_suspended_user_public_profile_returns_404(): void
    {
        $user = User::factory()->create([
            'status' => 'suspended',
            'slug' => 'suspended_artist',
        ]);

        $response = $this->get(route('profile.public', 'suspended_artist'));
        $response->assertNotFound();
    }

    public function test_active_user_public_profile_is_accessible(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'slug' => 'active_artist',
        ]);

        $response = $this->get(route('profile.public', 'active_artist'));
        $response->assertOk();
    }

    public function test_suspended_user_artworks_do_not_appear_in_explorer(): void
    {
        $activeUser = User::factory()->create(['status' => 'active']);
        $suspendedUser = User::factory()->create(['status' => 'suspended']);

        $visibleArtwork = Artwork::create([
            'user_id' => $activeUser->id,
            'title' => 'Visible Artwork Title',
            'status' => 'published',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'external_url' => 'https://example.com/art1',
        ]);

        $hiddenArtwork = Artwork::create([
            'user_id' => $suspendedUser->id,
            'title' => 'Suspended Hidden Artwork',
            'status' => 'published',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'external_url' => 'https://example.com/art2',
        ]);

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('Visible Artwork Title');
        $response->assertDontSee('Suspended Hidden Artwork');
    }
}
