<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\User;
use App\Notifications\ArtworkBookmarkedNotification;
use App\Notifications\ArtworkLikedNotification;
use App\Services\EmbedService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class P2List2FeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function createVerifiedUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email_verified_at' => now(),
            'status' => 'active',
        ], $attributes));
    }

    public function test_authenticated_user_can_like_and_unlike_artwork_and_author_is_notified(): void
    {
        $author = $this->createVerifiedUser(['name' => 'Author Artist']);
        $fan = $this->createVerifiedUser(['name' => 'Fan User']);

        $artwork = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Masterpiece',
            'likes_count' => 0,
        ]);

        Notification::fake();

        // 1. Fan likes artwork
        $response = $this->actingAs($fan)->postJson(route('artworks.like', $artwork));
        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'is_liked' => true,
                'likes_count' => 1,
            ]);

        $this->assertEquals(1, $artwork->fresh()->likes_count);
        $this->assertTrue($artwork->isLikedBy($fan));

        // Author should receive ArtworkLikedNotification
        Notification::assertSentTo($author, ArtworkLikedNotification::class);

        // 2. Fan unlikes artwork
        $response2 = $this->actingAs($fan)->postJson(route('artworks.like', $artwork));
        $response2->assertOk()
            ->assertJson([
                'status' => 'success',
                'is_liked' => false,
                'likes_count' => 0,
            ]);

        $this->assertEquals(0, $artwork->fresh()->likes_count);
        $this->assertFalse($artwork->isLikedBy($fan));
    }

    public function test_liking_own_artwork_does_not_send_notification_to_self(): void
    {
        $author = $this->createVerifiedUser(['name' => 'Self Liker']);
        $artwork = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'My Artwork',
            'likes_count' => 0,
        ]);

        Notification::fake();

        $this->actingAs($author)->postJson(route('artworks.like', $artwork))
            ->assertOk()
            ->assertJson(['is_liked' => true, 'likes_count' => 1]);

        Notification::assertNothingSent();
    }

    public function test_bookmarking_artwork_notifies_the_artist(): void
    {
        $author = $this->createVerifiedUser();
        $fan = $this->createVerifiedUser();

        $artwork = Artwork::factory()->create(['user_id' => $author->id]);

        Notification::fake();

        $this->actingAs($fan)->postJson(route('bookmarks.toggle', $artwork->id))
            ->assertOk()
            ->assertJson(['is_bookmarked' => true]);

        Notification::assertSentTo($author, ArtworkBookmarkedNotification::class);
    }

    public function test_software_used_and_license_are_stored_on_artwork_creation_and_update(): void
    {
        $user = $this->createVerifiedUser();

        $response = $this->actingAs($user)->post(route('artworks.store'), [
            'title' => 'Cyberpunk Alley',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'category' => 'video',
            'software_used' => 'Blender, Unreal Engine, DaVinci Resolve',
            'license' => 'cc_by_sa',
        ]);

        $response->assertRedirect(route('artworks.index'));

        $artwork = Artwork::where('title', 'Cyberpunk Alley')->first();
        $this->assertNotNull($artwork);
        $this->assertEquals(['Blender', 'Unreal Engine', 'DaVinci Resolve'], $artwork->software_used);
        $this->assertEquals('cc_by_sa', $artwork->license);

        // Update artwork
        $updateResponse = $this->actingAs($user)->put(route('artworks.update', $artwork), [
            'title' => 'Cyberpunk Alley Remastered',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'category' => 'video',
            'software_used' => ['Blender', 'Photoshop'],
            'license' => 'cc0',
        ]);

        $updateResponse->assertRedirect(route('artworks.index'));
        $artwork->refresh();
        $this->assertEquals('Cyberpunk Alley Remastered', $artwork->title);
        $this->assertEquals(['Blender', 'Photoshop'], $artwork->software_used);
        $this->assertEquals('cc0', $artwork->license);
    }

    public function test_commission_sheet_packages_can_be_saved_up_to_10_services(): void
    {
        $user = $this->createVerifiedUser();

        $services = [
            [
                'title' => 'Digital Bust Portrait',
                'description' => 'Full color with simple background',
                'price_starting_at' => '50',
                'currency' => 'USD',
                'turnaround_time' => '3-5 business days',
                'is_active' => true,
            ],
            [
                'title' => 'Full Body 3D Rig',
                'description' => 'Game-ready topology and FK/IK controls',
                'price_starting_at' => '250',
                'currency' => 'USD',
                'turnaround_time' => '2 weeks',
                'is_active' => true,
            ],
        ];

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'commission_services' => json_encode($services),
            'active_tab' => 'resume',
        ]);

        $response->assertRedirect(route('profile.edit', ['tab' => 'resume']));

        $user->refresh();
        $this->assertIsArray($user->commission_services);
        $this->assertCount(2, $user->commission_services);
        $this->assertEquals('Digital Bust Portrait', $user->commission_services[0]['title']);
        $this->assertEquals('50', $user->commission_services[0]['price_starting_at']);
    }

    public function test_notifications_api_returns_unread_list_and_marks_as_read(): void
    {
        $user = $this->createVerifiedUser();
        $actor = $this->createVerifiedUser(['name' => 'Friendly Artist']);
        $artwork = Artwork::factory()->create(['user_id' => $user->id]);

        // Send a real database notification
        $user->notify(new ArtworkLikedNotification($artwork, $actor));

        $this->assertEquals(1, $user->unreadNotifications()->count());
        $notificationId = $user->unreadNotifications()->first()->id;

        // Fetch notifications endpoint
        $response = $this->actingAs($user)->getJson(route('notifications.index'));
        $response->assertOk()
            ->assertJson([
                'unread_count' => 1,
            ])
            ->assertJsonFragment([
                'type' => 'like',
                'actor_name' => 'Friendly Artist',
            ]);

        // Mark single as read
        $readResponse = $this->actingAs($user)->postJson(route('notifications.read', $notificationId));
        $readResponse->assertOk()
            ->assertJson(['status' => 'success', 'unread_count' => 0]);

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());

        // Send another notification and test markAllRead
        $user->notify(new ArtworkLikedNotification($artwork, $actor));
        $this->assertEquals(1, $user->fresh()->unreadNotifications()->count());

        $this->actingAs($user)->postJson(route('notifications.readAll'))
            ->assertOk()
            ->assertJson(['status' => 'success', 'unread_count' => 0]);

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_embed_service_supports_expanded_providers(): void
    {
        $embedService = app(EmbedService::class);

        // 1. Vimeo
        $vimeoResult = $embedService->processUrl('https://vimeo.com/76979871');
        $this->assertNotNull($vimeoResult);
        $this->assertEquals('vimeo', $vimeoResult['provider']);
        $this->assertEquals('video', $vimeoResult['category']);
        $this->assertEquals('iframe', $vimeoResult['render_type']);
        $this->assertStringContainsString('player.vimeo.com/video/76979871', $vimeoResult['embed_html']);

        // 2. SoundCloud
        $scResult = $embedService->processUrl('https://soundcloud.com/forss/flickermood');
        $this->assertNotNull($scResult);
        $this->assertEquals('soundcloud', $scResult['provider']);
        $this->assertEquals('audio', $scResult['category']);
        $this->assertEquals('iframe', $scResult['render_type']);
        $this->assertStringContainsString('soundcloud.com/player', $scResult['embed_html']);

        // 3. CodePen
        $cpResult = $embedService->processUrl('https://codepen.io/geoffgraham/pen/OxNbaq');
        $this->assertNotNull($cpResult);
        $this->assertEquals('codepen', $cpResult['provider']);
        $this->assertEquals('doc', $cpResult['category']);
        $this->assertEquals('iframe', $cpResult['render_type']);
        $this->assertStringContainsString('codepen.io/geoffgraham/embed/OxNbaq', $cpResult['embed_html']);

        // 4. Bandcamp
        $bcResult = $embedService->processUrl('https://bandcamp.com/album/test');
        $this->assertNotNull($bcResult);
        $this->assertEquals('bandcamp', $bcResult['provider']);
        $this->assertEquals('audio', $bcResult['category']);

        // 5. ArtStation
        $asResult = $embedService->processUrl('https://www.artstation.com/artwork/example');
        $this->assertNotNull($asResult);
        $this->assertEquals('artstation', $asResult['provider']);
        $this->assertEquals('image', $asResult['render_type']);
    }
}

