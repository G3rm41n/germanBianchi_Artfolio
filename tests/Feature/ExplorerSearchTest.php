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
}
