<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P1List2FeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_artist_can_save_and_display_social_links(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'social_links' => [
                'artstation' => 'https://www.artstation.com/creativeartist',
                'instagram' => 'https://instagram.com/creativeartist',
                'behance' => 'https://behance.net/creativeartist',
                'twitter' => 'https://x.com/creativeartist',
                'linkedin' => 'https://linkedin.com/in/creativeartist',
                'website' => 'https://myportfolio.art',
            ],
        ]);

        $user->refresh();
        $this->assertEquals('https://www.artstation.com/creativeartist', $user->social_links['artstation']);
        $this->assertEquals('https://myportfolio.art', $user->social_links['website']);

        $response = $this->get(route('profile.public', $user->slug));
        $response->assertStatus(200);
        $response->assertSee('https://www.artstation.com/creativeartist');
        $response->assertSee('https://myportfolio.art');
    }

    public function test_views_counter_increments_atomically_with_session_deduplication(): void
    {
        $artist = User::factory()->create(['status' => 'active']);
        $artwork = Artwork::factory()->create([
            'user_id' => $artist->id,
            'status' => 'published',
            'views_count' => 0,
        ]);

        // First view increments
        $res1 = $this->postJson(route('artworks.view', $artwork));
        $res1->assertStatus(200);
        $res1->assertJson(['success' => true, 'views_count' => 1]);
        $this->assertEquals(1, $artwork->fresh()->views_count);

        // Second view in the same session does not increment
        $res2 = $this->postJson(route('artworks.view', $artwork));
        $res2->assertStatus(200);
        $res2->assertJson(['success' => true, 'views_count' => 1]);
        $this->assertEquals(1, $artwork->fresh()->views_count);
    }

    public function test_tags_are_synchronized_and_filter_explorer_catalog(): void
    {
        $artist = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
            'email_verified_at' => now(),
        ]);

        $artworkA = Artwork::factory()->create([
            'user_id' => $artist->id,
            'status' => 'published',
            'title' => 'Cyberpunk Hovercar',
        ]);
        $tagBlender = Tag::create(['name' => 'blender', 'slug' => 'blender']);
        $artworkA->tags()->attach($tagBlender->id);

        $artworkB = Artwork::factory()->create([
            'user_id' => $artist->id,
            'status' => 'published',
            'title' => 'Fantasy Oil Painting',
        ]);

        // Unfiltered catalog sees both
        $resAll = $this->get('/');
        $resAll->assertStatus(200);
        $resAll->assertSee('Cyberpunk Hovercar');
        $resAll->assertSee('Fantasy Oil Painting');

        // Filtered by tag ?tag=blender sees only artworkA
        $resTag = $this->get('/?tag=blender');
        $resTag->assertStatus(200);
        $resTag->assertSee('Cyberpunk Hovercar');
        $resTag->assertDontSee('Fantasy Oil Painting');
        $resTag->assertSee('#blender');
    }

    public function test_artist_can_pin_up_to_three_artworks_and_pins_sort_first(): void
    {
        $artist = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
            'email_verified_at' => now(),
        ]);

        $artworks = Artwork::factory()->count(4)->create([
            'user_id' => $artist->id,
            'status' => 'published',
            'is_pinned' => false,
        ]);

        $this->actingAs($artist);

        // Pin 1, 2, and 3
        for ($i = 0; $i < 3; $i++) {
            $res = $this->postJson(route('artworks.toggle-pin', $artworks[$i]));
            $res->assertStatus(200);
            $res->assertJson(['success' => true, 'is_pinned' => true]);
        }

        $this->assertEquals(3, $artist->artworks()->where('is_pinned', true)->count());

        // Attempting to pin the 4th artwork must fail
        $res4 = $this->postJson(route('artworks.toggle-pin', $artworks[3]));
        $res4->assertStatus(422);
        $res4->assertJson(['success' => false]);

        // Unpin works cleanly
        $resUnpin = $this->postJson(route('artworks.toggle-pin', $artworks[0]));
        $resUnpin->assertStatus(200);
        $resUnpin->assertJson(['success' => true, 'is_pinned' => false]);
        $this->assertEquals(2, $artist->artworks()->where('is_pinned', true)->count());

        // Pinned works are highlighted on the public portfolio
        $resProfile = $this->get(route('profile.public', $artist->slug));
        $resProfile->assertStatus(200);
        $resProfile->assertSee('Pinned');
    }

    public function test_admin_can_toggle_verified_badge_for_artist(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $artist = User::factory()->create([
            'is_admin' => false,
            'is_verified' => false,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Non-admin cannot toggle verify
        $this->actingAs($artist);
        $nonAdminRes = $this->post(route('admin.users.toggleVerify', $artist->id));
        $nonAdminRes->assertStatus(403);

        // Admin grants verification
        $this->actingAs($admin);
        $adminRes = $this->post(route('admin.users.toggleVerify', $artist->id));
        $adminRes->assertStatus(302);
        $this->assertTrue($artist->fresh()->is_verified);

        // Badge is visible on public portfolio
        $publicRes = $this->get(route('profile.public', $artist->slug));
        $publicRes->assertStatus(200);
        $publicRes->assertSee('Verified Artist');

        // Admin can revoke verification
        $adminRevokeRes = $this->post(route('admin.users.toggleVerify', $artist->id));
        $adminRevokeRes->assertStatus(302);
        $this->assertFalse($artist->fresh()->is_verified);
    }
}

