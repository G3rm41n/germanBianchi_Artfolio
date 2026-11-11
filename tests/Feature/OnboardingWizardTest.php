<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_uncompleted_user_is_redirected_to_step_1_when_accessing_dashboard_or_artworks(): void
    {
        $user = User::factory()->unonboarded(1)->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('onboarding.step', ['step' => 1]));

        $artworkResponse = $this->actingAs($user)->get(route('artworks.index'));
        $artworkResponse->assertRedirect(route('onboarding.step', ['step' => 1]));
    }

    public function test_user_cannot_skip_to_step_2_without_step_1(): void
    {
        $user = User::factory()->unonboarded(1)->create([
            'headline' => null,
            'country' => null,
        ]);

        $response = $this->actingAs($user)->get(route('onboarding.step', ['step' => 2]));

        $response->assertRedirect(route('onboarding.step', ['step' => 1]));
    }

    public function test_step_1_validation_and_advancing_to_step_2(): void
    {
        $user = User::factory()->unonboarded(1)->create();

        // Missing required fields
        $invalidResponse = $this->actingAs($user)->post(route('onboarding.step1.save'), []);
        $invalidResponse->assertSessionHasErrors(['headline', 'country']);

        // Valid submission
        $response = $this->actingAs($user)->post(route('onboarding.step1.save'), [
            'headline' => 'Senior Concept Artist & 3D Generalist',
            'city' => 'Vancouver',
            'country' => 'Canada',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb',
            'crop_x' => 10,
            'crop_y' => -5,
            'crop_zoom' => 1.5,
        ]);

        $response->assertRedirect(route('onboarding.step', ['step' => 2]));

        $user->refresh();
        $this->assertEquals('Senior Concept Artist & 3D Generalist', $user->headline);
        $this->assertEquals('Vancouver', $user->city);
        $this->assertEquals('Canada', $user->country);
        $this->assertEquals(2, $user->onboarding_step);
        $this->assertNull($user->onboarding_completed_at);
    }

    public function test_step_2_validation_and_advancing_to_step_3(): void
    {
        $user = User::factory()->unonboarded(2)->create([
            'headline' => 'Character Designer',
            'country' => 'United States',
        ]);

        // Validation fails if required fields missing
        $invalidResponse = $this->actingAs($user)->post(route('onboarding.step2.save'), []);
        $invalidResponse->assertSessionHasErrors(['slug', 'bio', 'skills', 'commission_status']);

        // Valid submission
        $response = $this->actingAs($user)->post(route('onboarding.step2.save'), [
            'slug' => 'art_wizard_99',
            'bio' => 'Digital character designer with 8 years of industry experience crafting worlds and fantasy creatures.',
            'skills' => ['Character Design', 'Concept Art', 'Digital Painting'],
            'software' => ['Photoshop', 'Blender', 'ZBrush'],
            'commission_status' => 'open',
            'hiring_types' => ['full-time', 'freelance'],
            'public_email_enabled' => true,
        ]);

        $response->assertRedirect(route('onboarding.step', ['step' => 3]));

        $user->refresh();
        $this->assertEquals('art_wizard_99', $user->slug);
        $this->assertEquals(3, $user->onboarding_step);
        $this->assertContains('Character Design', $user->skills);
        $this->assertContains('Blender', $user->software);
        $this->assertEquals('open', $user->commission_status);
        $this->assertTrue($user->public_email_enabled);
        $this->assertNull($user->onboarding_completed_at);
    }

    public function test_username_availability_endpoint(): void
    {
        $user = User::factory()->create(['slug' => 'taken_handle']);
        $anotherUser = User::factory()->unonboarded(2)->create();

        // Checking existing slug
        $takenResponse = $this->actingAs($anotherUser)->getJson(route('onboarding.checkUsername', ['slug' => 'taken_handle']));
        $takenResponse->assertOk()->assertJson(['available' => false]);

        // Checking free slug
        $freeResponse = $this->actingAs($anotherUser)->getJson(route('onboarding.checkUsername', ['slug' => 'brand_new_handle']));
        $freeResponse->assertOk()->assertJson(['available' => true]);

        // Checking user's own current slug
        $ownResponse = $this->actingAs($user)->getJson(route('onboarding.checkUsername', ['slug' => 'taken_handle']));
        $ownResponse->assertOk()->assertJson(['available' => true]);
    }

    public function test_step_3_completion_sets_completed_at_and_redirects_to_homepage(): void
    {
        $user = User::factory()->unonboarded(3)->create([
            'headline' => 'Illustrator',
            'country' => 'Japan',
        ]);

        $response = $this->actingAs($user)->post(route('onboarding.complete'));

        $response->assertRedirect(route('home'));

        $user->refresh();
        $this->assertTrue($user->isOnboarded());
        $this->assertNotNull($user->onboarding_completed_at);

        // User can now freely access dashboard without interception
        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashboardResponse->assertOk();
    }

    public function test_relogin_keeps_uncompleted_user_locked_into_onboarding(): void
    {
        $user = User::factory()->unonboarded(2)->create();

        // User logs out
        $this->actingAs($user)->post(route('logout'));

        // User logs in again and tries to visit gallery or dashboard
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('onboarding.step', ['step' => 2]));
    }

    public function test_admin_users_bypass_onboarding(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'onboarding_completed_at' => null,
            'onboarding_step' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
    }
}

