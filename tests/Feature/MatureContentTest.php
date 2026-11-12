<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MatureContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_artist_can_create_artwork_flagged_as_mature(): void
    {
        $user = User::factory()->create([
            'onboarding_completed_at' => now(),
            'status' => 'active',
        ]);

        Http::fake([
            'www.youtube.com/oembed*' => Http::response([
                'title' => 'Dark Fantasy Animation',
                'thumbnail_url' => 'https://img.youtube.com/vi/test/hqdefault.jpg',
                'html' => '<iframe src="https://www.youtube.com/embed/test"></iframe>',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('artworks.store'), [
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => 'Dark Fantasy Creature',
            'description' => 'A mature art piece',
            'category' => 'video',
            'status' => 'published',
            'is_mature' => '1',
            'is_ai_generated' => '0',
        ]);

        $response->assertRedirect(route('artworks.index'));

        $this->assertDatabaseHas('artworks', [
            'title' => 'Dark Fantasy Creature',
            'is_mature' => true,
        ]);
    }

    public function test_user_can_update_mature_filter_preference_in_account_settings(): void
    {
        $user = User::factory()->create([
            'mature_filter_preference' => 'blur',
        ]);

        // Change to 'hide'
        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'mature_filter_preference' => 'hide',
            'active_tab' => 'account',
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'account']));
        $user->refresh();

        $this->assertEquals('hide', $user->mature_filter_preference);
        $this->assertTrue($user->wantsMatureHidden());

        // Change to 'show'
        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'mature_filter_preference' => 'show',
            'active_tab' => 'account',
        ]);

        $user->refresh();
        $this->assertEquals('show', $user->mature_filter_preference);
        $this->assertTrue($user->wantsMatureShown());
    }

    public function test_explorer_hides_mature_artworks_when_user_preference_is_hide(): void
    {
        $user = User::factory()->create([
            'mature_filter_preference' => 'hide',
        ]);

        $regularArt = Artwork::factory()->create([
            'title' => 'Sunset Painting',
            'is_mature' => false,
            'status' => 'published',
        ]);

        $matureArt = Artwork::factory()->create([
            'title' => 'Gory Battle Scene',
            'is_mature' => true,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Sunset Painting');
        $response->assertDontSee('Gory Battle Scene');
    }

    public function test_explorer_shows_mature_artworks_with_blur_overlay_for_guest(): void
    {
        $matureArt = Artwork::factory()->create([
            'title' => 'Artistic Anatomy',
            'is_mature' => true,
            'status' => 'published',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Artistic Anatomy');
        $response->assertSee('Click to reveal');
        $response->assertSee('18+');
    }

    public function test_artwork_show_page_includes_mature_blur_and_reveal_overlay(): void
    {
        $user = User::factory()->create([
            'mature_filter_preference' => 'blur',
        ]);

        $matureArt = Artwork::factory()->create([
            'title' => 'Gothic Vampire',
            'is_mature' => true,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('artworks.show', $matureArt));

        $response->assertStatus(200);
        $response->assertSee('Mature');
        $response->assertSee('Sensitive Content');
        $response->assertSee('View Content (Reveal)');
    }
}
