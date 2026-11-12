<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P3ImprovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_legacy_spanish_urls_redirect_permanently_to_english_standard(): void
    {
        $user = User::factory()->create([
            'slug' => 'test-artist',
            'status' => 'active',
        ]);

        // 1. /artista/{slug} -> /artist/{slug}
        $artistRedirect = $this->get('/artista/test-artist');
        $artistRedirect->assertStatus(301);
        $artistRedirect->assertRedirect('/artist/test-artist');

        // 2. /favoritos -> /favorites (requires auth or redirects to login)
        $this->actingAs($user);
        $favoritesRedirect = $this->get('/favoritos');
        $favoritesRedirect->assertStatus(301);
        $favoritesRedirect->assertRedirect('/favorites');

        // 3. /papelera -> /trash
        $trashRedirect = $this->get('/papelera');
        $trashRedirect->assertStatus(301);
        $trashRedirect->assertRedirect('/trash');
    }

    public function test_explorer_active_filters_count_and_clear_action(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        Artwork::factory()->create([
            'user_id' => $user->id,
            'status' => 'published',
            'title' => 'Futuristic Cyberpunk Skyline',
            'category' => 'image',
        ]);

        // No filters
        $resNoFilter = $this->get('/');
        $resNoFilter->assertStatus(200);
        $resNoFilter->assertViewHas('activeFiltersCount', 0);
        $resNoFilter->assertDontSee('Clear all filters');

        // Search filter only
        $resSearch = $this->get('/?search=Cyberpunk');
        $resSearch->assertStatus(200);
        $resSearch->assertViewHas('activeFiltersCount', 1);
        $resSearch->assertSee('Clear all filters');
        $resSearch->assertSee('Search:');

        // Category filter only
        $resCat = $this->get('/?category=image');
        $resCat->assertStatus(200);
        $resCat->assertViewHas('activeFiltersCount', 1);
        $resCat->assertSee('Clear all filters');
        $resCat->assertSee('Category:');

        // Both search and category filters
        $resBoth = $this->get('/?search=Cyberpunk&category=image');
        $resBoth->assertStatus(200);
        $resBoth->assertViewHas('activeFiltersCount', 2);
        $resBoth->assertSee('2 active filters');
        $resBoth->assertSee('Clear all filters');
    }

    public function test_trashed_collections_eager_load_artworks_count(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $collection = Collection::create([
            'user_id' => $user->id,
            'name' => 'Archived Moodboard',
            'visibility' => 'private',
        ]);

        $artwork = Artwork::factory()->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        $collection->artworks()->attach($artwork->id);
        $collection->delete();

        $response = $this->get(route('trash.index'));
        $response->assertStatus(200);
        $response->assertSee('Archived Moodboard');
        $response->assertSee('1 artwork');
    }
}

