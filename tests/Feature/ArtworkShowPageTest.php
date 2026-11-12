<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\ArtworkCommentedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ArtworkShowPageTest extends TestCase
{
    use RefreshDatabase;

    protected function createVerifiedUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email_verified_at' => now(),
            'status' => 'active',
        ], $attributes));
    }

    public function test_guest_and_user_can_view_artwork_show_page_and_views_count_increments(): void
    {
        $author = $this->createVerifiedUser(['name' => 'Leonardo da Vinci']);
        $artwork = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Mona Lisa Digital',
            'status' => 'published',
            'views_count' => 5,
            'software_used' => ['Photoshop', 'Procreate'],
            'license' => 'cc_by',
        ]);

        // 1. Guest visits page
        $response = $this->get(route('artworks.show', $artwork));
        $response->assertOk()
            ->assertSee('Mona Lisa Digital')
            ->assertSee('Leonardo da Vinci')
            ->assertSee('Photoshop')
            ->assertSee('Procreate')
            ->assertSee('Creative Commons CC BY');

        $this->assertEquals(6, $artwork->fresh()->views_count);

        // 2. Legacy alias /obra/{id} redirects with 301
        $aliasResponse = $this->get('/obra/' . $artwork->id);
        $aliasResponse->assertStatus(301)
            ->assertRedirect(route('artworks.show', $artwork));
    }

    public function test_reserved_rights_is_hidden_from_artwork_badges(): void
    {
        $author = $this->createVerifiedUser();
        $artwork = Artwork::factory()->create([
            'user_id' => $author->id,
            'title'   => 'Artwork With Reserved Rights',
            'status'  => 'published',
            'license' => 'all_rights_reserved',
        ]);

        $response = $this->get(route('artworks.show', $artwork));
        $response->assertOk()
            ->assertDontSee('Todos los derechos reservados');
    }

    public function test_hidden_artwork_is_not_accessible_to_guests_or_other_users(): void
    {
        $author = $this->createVerifiedUser();
        $stranger = $this->createVerifiedUser();
        $artwork = Artwork::factory()->create([
            'user_id' => $author->id,
            'status' => 'hidden',
        ]);

        // Guest -> 404
        $this->get(route('artworks.show', $artwork))->assertNotFound();

        // Stranger -> 404
        $this->actingAs($stranger)->get(route('artworks.show', $artwork))->assertNotFound();

        // Author -> 200
        $this->actingAs($author)->get(route('artworks.show', $artwork))->assertOk();
    }

    public function test_similar_artworks_algorithm_prioritizes_matching_tags_and_category(): void
    {
        $author = $this->createVerifiedUser();
        
        $tagFantasy = Tag::create(['name' => 'Fantasy', 'slug' => 'fantasy']);
        $tag3D = Tag::create(['name' => '3D', 'slug' => '3d']);

        // Main artwork
        $mainArtwork = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Dragon Lord',
            'category' => '3d',
            'status' => 'published',
        ]);
        $mainArtwork->tags()->attach([$tagFantasy->id, $tag3D->id]);

        // Artwork A: Matches both tags
        $artworkA = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Ancient Dragon',
            'category' => '3d',
            'status' => 'published',
            'likes_count' => 10,
        ]);
        $artworkA->tags()->attach([$tagFantasy->id, $tag3D->id]);

        // Artwork B: Matches 1 tag
        $artworkB = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Fantasy Castle',
            'category' => 'image',
            'status' => 'published',
            'likes_count' => 5,
        ]);
        $artworkB->tags()->attach([$tagFantasy->id]);

        // Artwork C: Same category (3d) but no tags
        $artworkC = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Futuristic Car',
            'category' => '3d',
            'status' => 'published',
            'likes_count' => 2,
        ]);

        // Artwork D: Different category, no tags
        $artworkD = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Podcast Episode',
            'category' => 'audio',
            'status' => 'published',
        ]);

        $similar = $mainArtwork->getSimilarArtworks(10);

        // Check results
        $this->assertFalse($similar->contains('id', $mainArtwork->id));
        $this->assertTrue($similar->contains('id', $artworkA->id));
        $this->assertTrue($similar->contains('id', $artworkB->id));
        $this->assertTrue($similar->contains('id', $artworkC->id));

        // Artwork A (2 matching tags) should rank higher than Artwork B (1 matching tag)
        $ids = $similar->pluck('id')->all();
        $indexA = array_search($artworkA->id, $ids);
        $indexB = array_search($artworkB->id, $ids);
        $this->assertLessThan($indexB, $indexA);
    }

    public function test_authenticated_user_can_comment_and_author_is_notified(): void
    {
        $author = $this->createVerifiedUser(['name' => 'Artist One']);
        $fan = $this->createVerifiedUser(['name' => 'Fan Two']);

        $artwork = Artwork::factory()->create([
            'user_id' => $author->id,
            'title' => 'Neon City',
            'status' => 'published',
        ]);

        Notification::fake();

        // 1. Post comment
        $response = $this->actingAs($fan)->postJson(route('artworks.comments.store', $artwork), [
            'content' => 'Incredible lighting and composition!',
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonPath('comment.content', 'Incredible lighting and composition!')
            ->assertJsonPath('comment.user.name', 'Fan Two');

        $this->assertDatabaseHas('artwork_comments', [
            'artwork_id' => $artwork->id,
            'user_id' => $fan->id,
            'content' => 'Incredible lighting and composition!',
        ]);

        // Notification should be sent to artwork author
        Notification::assertSentTo($author, ArtworkCommentedNotification::class);

        // 2. Author can delete comment
        $comment = Comment::where('artwork_id', $artwork->id)->first();
        $this->actingAs($author)->deleteJson(route('artworks.comments.destroy', [$artwork, $comment]))
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('artwork_comments', ['id' => $comment->id]);
    }
}

