<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsHubTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);
    }

    public function test_settings_hub_page_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Settings & Profile Hub', false);
        $response->assertSee('Profile');
        $response->assertSee('Resume');
        $response->assertSee('Account');
        $response->assertSee('Notifications');
        $response->assertSee('Password');
        $response->assertSee('Blocking');
    }

    public function test_profile_tab_updates_personal_and_banner_data(): void
    {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'active_tab' => 'profile',
            'name' => 'Renowned Concept Artist',
            'headline' => 'Senior Environment Designer',
            'city' => 'Kyoto',
            'country' => 'Japan',
            'avatar_url' => 'https://example.com/avatar.jpg',
            'crop_x' => 10,
            'crop_y' => -5,
            'crop_zoom' => 1.5,
            'banner_url' => 'https://example.com/panoramic-banner.jpg',
            'banner_crop_x' => -15,
            'banner_crop_y' => 20,
            'banner_crop_zoom' => 1.35,
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'profile']));

        $this->user->refresh();
        $this->assertEquals('Renowned Concept Artist', $this->user->name);
        $this->assertEquals('Senior Environment Designer', $this->user->headline);
        $this->assertEquals('Kyoto', $this->user->city);
        $this->assertEquals('Japan', $this->user->country);
        $this->assertEquals('https://example.com/panoramic-banner.jpg', $this->user->banner_url);
        $this->assertEquals(-15, $this->user->banner_crop_x);
        $this->assertEquals(20, $this->user->banner_crop_y);
        $this->assertEquals(1.35, $this->user->banner_crop_zoom);

        $publicView = $this->get(route('profile.public', $this->user->slug));
        $publicView->assertOk();
        $publicView->assertSee('https://example.com/panoramic-banner.jpg', false);
        $publicView->assertSee('translate(-15%, 20%) scale(1.35)', false);
    }

    public function test_resume_tab_updates_professional_skills_and_handle(): void
    {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'active_tab' => 'resume',
            'name' => $this->user->name,
            'email' => $this->user->email,
            'slug' => 'master_artist_2026',
            'bio' => 'Experienced visual development artist with over 10 years in the gaming industry.',
            'commission_status' => 'closed',
            'skills' => ['Concept Art', '3D Modeling', 'Lighting'],
            'software' => ['Blender', 'Photoshop', 'Unreal Engine'],
            'hiring_types' => ['full-time', 'contract'],
            'public_email_enabled' => '1',
            'contact_email' => 'business@masterartist.com',
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'resume']));

        $this->user->refresh();
        $this->assertEquals('master_artist_2026', $this->user->slug);
        $this->assertEquals('closed', $this->user->commission_status);
        $this->assertEquals(['Concept Art', '3D Modeling', 'Lighting'], $this->user->skills);
        $this->assertEquals(['Blender', 'Photoshop', 'Unreal Engine'], $this->user->software);
        $this->assertEquals(['full-time', 'contract'], $this->user->hiring_types);
        $this->assertTrue($this->user->public_email_enabled);
        $this->assertEquals('business@masterartist.com', $this->user->contact_email);
    }

    public function test_account_tab_updates_content_filters_and_danger_zone(): void
    {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'active_tab' => 'account',
            'name' => $this->user->name,
            'email' => $this->user->email,
            'show_mature_content' => '1',
            'hide_ai_content' => '1',
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'account']));

        $this->user->refresh();
        $this->assertTrue($this->user->show_mature_content);
        $this->assertTrue($this->user->hide_ai_content);
    }

    public function test_notifications_tab_updates_preferences(): void
    {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'active_tab' => 'notifications',
            'name' => $this->user->name,
            'email' => $this->user->email,
            'notify_new_artworks' => '0',
            'notify_messages' => '1',
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'notifications']));

        $this->user->refresh();
        $this->assertFalse($this->user->notify_new_artworks);
        $this->assertTrue($this->user->notify_messages);
    }

    public function test_user_can_block_and_unblock_other_users(): void
    {
        $otherUser = User::factory()->create([
            'name' => 'Spam Artist',
            'slug' => 'spam_artist',
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        // Block user
        $response = $this->actingAs($this->user)->post(route('profile.blocking.store'), [
            'username' => '@spam_artist',
        ]);

        $response->assertRedirect();
        $this->assertTrue($this->user->isBlocking($otherUser));

        // Unblock user
        $unblockResponse = $this->actingAs($this->user)->delete(route('profile.blocking.destroy', $otherUser));
        $unblockResponse->assertRedirect();
        $this->assertFalse($this->user->isBlocking($otherUser));
    }

    public function test_user_cannot_block_themselves(): void
    {
        $response = $this->actingAs($this->user)->post(route('profile.blocking.store'), [
            'username' => $this->user->slug,
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertFalse($this->user->isBlocking($this->user));
    }

    public function test_explorer_hides_artworks_from_blocked_users(): void
    {
        $blockedArtist = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $allowedArtist = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $blockedArtwork = Artwork::create([
            'user_id' => $blockedArtist->id,
            'title' => 'Artwork From Blocked Artist',
            'description' => 'Should not appear',
            'external_url' => 'https://www.deviantart.com/art/blocked',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        $allowedArtwork = Artwork::create([
            'user_id' => $allowedArtist->id,
            'title' => 'Artwork From Allowed Artist',
            'description' => 'Should appear',
            'external_url' => 'https://www.deviantart.com/art/allowed',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        // Block artist
        $this->user->block($blockedArtist);

        $response = $this->actingAs($this->user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('Artwork From Allowed Artist');
        $response->assertDontSee('Artwork From Blocked Artist');
    }

    public function test_explorer_hides_ai_artworks_when_preference_is_enabled(): void
    {
        $artist = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $humanArtwork = Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Authentic Oil Painting',
            'description' => 'Real traditional medium',
            'external_url' => 'https://www.deviantart.com/art/human-1',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'is_ai_generated' => false,
            'status' => 'published',
        ]);

        $aiArtwork = Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Midjourney Cyberpunk Vision',
            'description' => 'AI prompted piece',
            'external_url' => 'https://www.deviantart.com/art/ai-1',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'is_ai_generated' => true,
            'status' => 'published',
        ]);

        // Without AI filtering: both visible
        $this->user->update(['hide_ai_content' => false]);
        $response1 = $this->actingAs($this->user)->get(route('home'));
        $response1->assertSee('Authentic Oil Painting');
        $response1->assertSee('Midjourney Cyberpunk Vision');

        // With AI filtering enabled: only human artwork visible
        $this->user->update(['hide_ai_content' => true]);
        $response2 = $this->actingAs($this->user)->get(route('home'));
        $response2->assertSee('Authentic Oil Painting');
        $response2->assertDontSee('Midjourney Cyberpunk Vision');
    }
}
