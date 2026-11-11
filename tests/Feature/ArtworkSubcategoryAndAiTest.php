<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ArtworkSubcategoryAndAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_artwork_can_be_created_with_subcategory_and_ai_flag(): void
    {
        $user = User::factory()->create([
            'onboarding_completed_at' => now(),
            'status' => 'active',
        ]);

        Http::fake([
            'www.youtube.com/oembed*' => Http::response([
                'title' => 'Speedpaint Concept Art',
                'thumbnail_url' => 'https://img.youtube.com/vi/test/hqdefault.jpg',
                'html' => '<iframe src="https://www.youtube.com/embed/test"></iframe>',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('artworks.store'), [
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => 'AI Generated Cyberpunk City',
            'description' => 'A demonstration of generative art techniques.',
            'category' => 'video',
            'subcategory' => 'Concept Art',
            'is_ai_generated' => '1',
        ]);

        $response->assertRedirect(route('artworks.index'));

        $this->assertDatabaseHas('artworks', [
            'user_id' => $user->id,
            'title' => 'AI Generated Cyberpunk City',
            'subcategory' => 'Concept Art',
            'is_ai_generated' => 1,
        ]);
    }

    public function test_artwork_ai_flag_defaults_to_false_when_not_provided(): void
    {
        $user = User::factory()->create([
            'onboarding_completed_at' => now(),
            'status' => 'active',
        ]);

        Http::fake([
            'www.youtube.com/oembed*' => Http::response([
                'title' => 'Traditional Painting',
                'thumbnail_url' => 'https://img.youtube.com/vi/test2/hqdefault.jpg',
                'html' => '<iframe src="https://www.youtube.com/embed/test2"></iframe>',
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('artworks.store'), [
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => 'Oil Painting Process',
            'description' => 'Real brush on canvas.',
            'category' => 'video',
        ]);

        $response->assertRedirect(route('artworks.index'));

        $this->assertDatabaseHas('artworks', [
            'user_id' => $user->id,
            'title' => 'Oil Painting Process',
            'is_ai_generated' => 0,
        ]);
    }

    public function test_artwork_can_be_updated_with_subcategory_and_ai_flag(): void
    {
        $user = User::factory()->create([
            'onboarding_completed_at' => now(),
            'status' => 'active',
        ]);

        $artwork = Artwork::create([
            'user_id' => $user->id,
            'title' => 'Initial Title',
            'description' => 'Initial Description',
            'external_url' => 'https://www.deviantart.com/artist/art/test-1',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'subcategory' => 'Sketch',
            'is_ai_generated' => false,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->put(route('artworks.update', $artwork), [
            'external_url' => $artwork->external_url,
            'title' => 'Updated AI Artwork',
            'description' => 'Updated Description',
            'category' => 'image',
            'subcategory' => 'Digital Matte Painting',
            'is_ai_generated' => '1',
        ]);

        $response->assertRedirect(route('artworks.index'));

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'title' => 'Updated AI Artwork',
            'subcategory' => 'Digital Matte Painting',
            'is_ai_generated' => 1,
        ]);
    }

    public function test_model_ai_scopes(): void
    {
        $user = User::factory()->create([
            'onboarding_completed_at' => now(),
            'status' => 'active',
        ]);

        $aiArtwork = Artwork::create([
            'user_id' => $user->id,
            'title' => 'AI Artwork',
            'description' => 'AI Description',
            'external_url' => 'https://www.deviantart.com/artist/art/ai-1',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'is_ai_generated' => true,
            'status' => 'published',
        ]);

        $humanArtwork = Artwork::create([
            'user_id' => $user->id,
            'title' => 'Human Artwork',
            'description' => 'Human Description',
            'external_url' => 'https://www.deviantart.com/artist/art/human-1',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'is_ai_generated' => false,
            'status' => 'published',
        ]);

        $notAiResults = Artwork::notAi()->pluck('id')->all();
        $this->assertContains($humanArtwork->id, $notAiResults);
        $this->assertNotContains($aiArtwork->id, $notAiResults);

        $onlyAiResults = Artwork::onlyAi()->pluck('id')->all();
        $this->assertContains($aiArtwork->id, $onlyAiResults);
        $this->assertNotContains($humanArtwork->id, $onlyAiResults);
    }
}
