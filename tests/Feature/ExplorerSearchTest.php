<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExplorerSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The public explorer renders only published artworks.
     */
    public function test_public_explorer_renders_published_artworks_only(): void
    {
        $artist = User::factory()->create(['name' => 'Artista Principal']);

        // Obra publicada
        $published = Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Obra Publica Visible',
            'description' => 'Visible para todos los visitantes.',
            'external_url' => 'https://www.youtube.com/watch?v=111111',
            'provider' => 'youtube',
            'category' => 'video',
            'render_type' => 'iframe',
            'status' => 'published',
        ]);

        // Obra oculta / draft
        $hidden = Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Obra Oculta Privada',
            'description' => 'No debe aparecer en la galería pública.',
            'external_url' => 'https://www.youtube.com/watch?v=222222',
            'provider' => 'youtube',
            'category' => 'video',
            'render_type' => 'iframe',
            'status' => 'hidden',
        ]);

        // Obra eliminada lógicamente (soft deleted)
        $trashed = Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Obra en Papelera',
            'description' => 'Enviada a papelera.',
            'external_url' => 'https://www.youtube.com/watch?v=333333',
            'provider' => 'youtube',
            'category' => 'video',
            'render_type' => 'iframe',
            'status' => 'published',
        ]);
        $trashed->delete();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Obra Publica Visible');
        $response->assertDontSee('Obra Oculta Privada');
        $response->assertDontSee('Obra en Papelera');
    }

    /**
     * Search by title, description, and artist name.
     */
    public function test_explorer_search_by_title_description_and_artist_name(): void
    {
        $artistA = User::factory()->create(['name' => 'Leonardo da Vinci']);
        $artistB = User::factory()->create(['name' => 'Vincent van Gogh']);

        $artwork1 = Artwork::factory()->create([
            'user_id' => $artistA->id,
            'title' => 'Retrato Renacentista',
            'description' => 'Técnica de sfumato al óleo.',
            'category' => 'image',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        $artwork2 = Artwork::factory()->create([
            'user_id' => $artistB->id,
            'title' => 'Noche Estrellada',
            'description' => 'Pinceladas vibrantes postimpresionistas.',
            'category' => 'image',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        // 1. Búsqueda por título
        $searchTitle = $this->get('/?search=Renacentista');
        $searchTitle->assertStatus(200);
        $searchTitle->assertSee('Retrato Renacentista');
        $searchTitle->assertDontSee('Noche Estrellada');

        // 2. Búsqueda por palabra clave en descripción
        $searchDesc = $this->get('/?search=postimpresionistas');
        $searchDesc->assertStatus(200);
        $searchDesc->assertSee('Noche Estrellada');
        $searchDesc->assertDontSee('Retrato Renacentista');

        // 3. Búsqueda por nombre del artista
        $searchArtist = $this->get('/?search=Leonardo');
        $searchArtist->assertStatus(200);
        $searchArtist->assertSee('Retrato Renacentista');
        $searchArtist->assertDontSee('Noche Estrellada');
    }

    /**
     * Category filtering in explorer.
     */
    public function test_explorer_filters_by_category(): void
    {
        $artist = User::factory()->create();

        Artwork::factory()->create([
            'user_id' => $artist->id,
            'title' => 'Escultura Cyberpunk',
            'category' => '3d',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        Artwork::factory()->create([
            'user_id' => $artist->id,
            'title' => 'Banda Sonora Épica',
            'category' => 'audio',
            'render_type' => 'iframe',
            'status' => 'published',
        ]);

        // Filtrar solo 3D
        $filter3D = $this->get('/?category=3d');
        $filter3D->assertStatus(200);
        $filter3D->assertSee('Escultura Cyberpunk');
        $filter3D->assertDontSee('Banda Sonora Épica');

        // Filtrar solo Audio
        $filterAudio = $this->get('/?category=audio');
        $filterAudio->assertStatus(200);
        $filterAudio->assertSee('Banda Sonora Épica');
        $filterAudio->assertDontSee('Escultura Cyberpunk');
    }

    /**
     * Explorer pagination.
     */
    public function test_explorer_paginates_properly(): void
    {
        $artist = User::factory()->create();

        for ($i = 1; $i <= 15; $i++) {
            Artwork::factory()->create([
                'user_id' => $artist->id,
                'title' => "Obra Paginada Numero {$i}",
                'status' => 'published',
            ]);
        }

        $page1 = $this->get('/');
        $page1->assertStatus(200);
        $page2 = $this->get('/?page=2');
        $page2->assertStatus(200);
    }

    public function test_explorer_filters_by_multiple_tags_with_match_or(): void
    {
        $artist = User::factory()->create();

        $artwork1 = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title'   => 'Sci-Fi Vehicle Concept',
            'status'  => 'published',
        ]);
        $tagConcept = \App\Models\Tag::create(['name' => 'Concept Art', 'slug' => 'concept-art']);
        $artwork1->tags()->attach($tagConcept->id);

        $artwork2 = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title'   => '3D Robot Sculpture',
            'status'  => 'published',
        ]);
        $tag3D = \App\Models\Tag::create(['name' => '3D Modeling', 'slug' => '3d-modeling']);
        $artwork2->tags()->attach($tag3D->id);

        $artwork3 = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title'   => 'Acoustic Guitar Track',
            'status'  => 'published',
        ]);

        // Filter with tags=['Concept Art', '3D Modeling'] and match=or
        $response = $this->get(route('home', [
            'tags'  => ['Concept Art', '3D Modeling'],
            'match' => 'or',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Sci-Fi Vehicle Concept');
        $response->assertSee('3D Robot Sculpture');
        $response->assertDontSee('Acoustic Guitar Track');
    }

    public function test_explorer_filters_by_multiple_tags_with_match_all(): void
    {
        $artist = User::factory()->create();

        $artworkBoth = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title'   => 'Concept Art 3D Modeling Duo',
            'status'  => 'published',
        ]);
        $tagConcept = \App\Models\Tag::firstOrCreate(['name' => 'Concept Art', 'slug' => 'concept-art']);
        $tag3D = \App\Models\Tag::firstOrCreate(['name' => '3D Modeling', 'slug' => '3d-modeling']);
        $artworkBoth->tags()->attach([$tagConcept->id, $tag3D->id]);

        $artworkConceptOnly = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title'   => 'Solo Concept Art Piece',
            'status'  => 'published',
        ]);
        $artworkConceptOnly->tags()->attach($tagConcept->id);

        // In match=all mode, only artwork with BOTH tags must appear
        $response = $this->get(route('home', [
            'tags'  => ['Concept Art', '3D Modeling'],
            'match' => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Concept Art 3D Modeling Duo');
        $response->assertDontSee('Solo Concept Art Piece');
    }

    public function test_explorer_filters_by_software_tools_with_all_and_or_mode(): void
    {
        $artist = User::factory()->create();

        $artBlender = Artwork::factory()->create([
            'user_id'       => $artist->id,
            'title'         => 'Blender Only Scene',
            'software_used' => ['Blender'],
            'status'        => 'published',
        ]);

        $artZBrush = Artwork::factory()->create([
            'user_id'       => $artist->id,
            'title'         => 'ZBrush Only Bust',
            'software_used' => ['ZBrush'],
            'status'        => 'published',
        ]);

        $artBoth = Artwork::factory()->create([
            'user_id'       => $artist->id,
            'title'         => 'Blender And ZBrush Masterpiece',
            'software_used' => ['Blender', 'ZBrush'],
            'status'        => 'published',
        ]);

        // OR mode: matches all 3
        $responseOr = $this->get(route('home', [
            'tools' => ['Blender', 'ZBrush'],
            'match' => 'or',
        ]));
        $responseOr->assertStatus(200);
        $responseOr->assertSee('Blender Only Scene');
        $responseOr->assertSee('ZBrush Only Bust');
        $responseOr->assertSee('Blender And ZBrush Masterpiece');

        // ALL mode: only matches artBoth
        $responseAll = $this->get(route('home', [
            'tools' => ['Blender', 'ZBrush'],
            'match' => 'all',
        ]));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Blender And ZBrush Masterpiece');
        $responseAll->assertDontSee('Blender Only Scene');
        $responseAll->assertDontSee('ZBrush Only Bust');
    }

    public function test_explorer_sorts_by_likes_views_and_oldest(): void
    {
        $artist = User::factory()->create();

        $artUnpopular = Artwork::factory()->create([
            'user_id'     => $artist->id,
            'title'       => 'Low Stats Work',
            'likes_count' => 1,
            'views_count' => 10,
            'status'      => 'published',
            'created_at'  => now()->subDays(5),
        ]);

        $artPopular = Artwork::factory()->create([
            'user_id'     => $artist->id,
            'title'       => 'High Stats Work',
            'likes_count' => 500,
            'views_count' => 9000,
            'status'      => 'published',
            'created_at'  => now()->subDays(1),
        ]);

        // Sort by likes
        $responseLikes = $this->get(route('home', ['sort' => 'likes']));
        $responseLikes->assertStatus(200);
        $responseLikes->assertSeeInOrder(['High Stats Work', 'Low Stats Work']);

        // Sort by views
        $responseViews = $this->get(route('home', ['sort' => 'views']));
        $responseViews->assertStatus(200);
        $responseViews->assertSeeInOrder(['High Stats Work', 'Low Stats Work']);

        // Sort oldest
        $responseOldest = $this->get(route('home', ['sort' => 'oldest']));
        $responseOldest->assertStatus(200);
        $responseOldest->assertSeeInOrder(['Low Stats Work', 'High Stats Work']);
    }

    public function test_ai_dropdown_is_removed_from_explorer_and_multiplatform_artworks_display(): void
    {
        $artist = User::factory()->create();

        // 1. Pinterest artwork with mature flag
        $artPinterest = Artwork::factory()->create([
            'user_id'         => $artist->id,
            'title'           => 'Dark Gothic Knight Pin',
            'provider'        => 'pinterest',
            'category'        => 'image',
            'render_type'     => 'image',
            'is_mature'       => true,
            'is_ai_generated' => false,
            'status'          => 'published',
        ]);

        // 2. DeviantArt artwork with AI flag
        $artDeviant = Artwork::factory()->create([
            'user_id'         => $artist->id,
            'title'           => 'Synthwave Grid Neon',
            'provider'        => 'deviantart',
            'category'        => 'image',
            'render_type'     => 'image',
            'is_mature'       => false,
            'is_ai_generated' => true,
            'status'          => 'published',
        ]);

        // 3. Sketchfab 3D artwork
        $artSketchfab = Artwork::factory()->create([
            'user_id'         => $artist->id,
            'title'           => 'Gargoyle Statue 3D',
            'provider'        => 'sketchfab',
            'category'        => '3d',
            'render_type'     => 'iframe',
            'is_mature'       => true,
            'is_ai_generated' => true,
            'status'          => 'published',
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Ensure AI dropdown filter is not in DOM
        $response->assertDontSee('name="ai"', false);
        $response->assertDontSee('AI Content: All');

        // Ensure artworks from Pinterest, DeviantArt, and Sketchfab are shown
        $response->assertSee('Dark Gothic Knight Pin');
        $response->assertSee('Synthwave Grid Neon');
        $response->assertSee('Gargoyle Statue 3D');
        $response->assertSee('pinterest');
        $response->assertSee('deviantart');
        $response->assertSee('sketchfab');
    }
}
