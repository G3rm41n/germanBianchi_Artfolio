<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertSoftDeleted($user);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_update_banner_with_positioning_and_zoom(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'banner_url' => 'https://images.unsplash.com/photo-banner-sample-123.jpg',
                'banner_crop_x' => -15.5,
                'banner_crop_y' => 22.0,
                'banner_crop_zoom' => 1.35,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('https://images.unsplash.com/photo-banner-sample-123.jpg', $user->banner_url);
        $this->assertEquals(-15.5, $user->banner_crop_x);
        $this->assertEquals(22.0, $user->banner_crop_y);
        $this->assertEquals(1.35, $user->banner_crop_zoom);
    }

    public function test_public_profile_renders_banner_with_custom_framing(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
            'banner_url' => 'https://images.unsplash.com/photo-heroic-banner.jpg',
            'banner_crop_x' => 10.0,
            'banner_crop_y' => -5.0,
            'banner_crop_zoom' => 1.25,
        ]);

        $response = $this->get(route('profile.public', $user->slug));

        $response->assertOk();
        $response->assertSee('https://images.unsplash.com/photo-heroic-banner.jpg');
        $response->assertSee('transform: translate(10%, -5%) scale(1.25)', false);
    }
}
