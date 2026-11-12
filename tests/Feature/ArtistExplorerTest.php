<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\CuratedTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_artists_directory_can_be_viewed_by_guest_and_displays_artists_by_default(): void
    {
        $artist = User::factory()->create([
            'name' => 'Elena Rostova',
            'status' => 'active',
            'headline' => 'Senior Concept Artist',
        ]);

        $response = $this->get(route('artists.index'));

        $response->assertStatus(200);
        $response->assertSee('Elena Rostova');
        $response->assertSee('Senior Concept Artist');
        $response->assertSee('Artworks');
        $response->assertSee('Artists');
    }

    public function test_search_by_artist_name_or_headline(): void
    {
        $artist1 = User::factory()->create([
            'name' => 'Kaelen Thorne',
            'status' => 'active',
            'headline' => 'VFX & Shader Specialist',
        ]);

        $artist2 = User::factory()->create([
            'name' => 'Sarah Connor',
            'status' => 'active',
            'headline' => 'Character Animator',
        ]);

        $response = $this->get(route('artists.index', ['search' => 'Kaelen']));

        $response->assertStatus(200);
        $response->assertSee('Kaelen Thorne');
        $response->assertDontSee('Sarah Connor');
    }

    public function test_filter_by_artistic_skill_or_discipline(): void
    {
        $artist1 = User::factory()->create([
            'name' => 'Digital Sculptor',
            'status' => 'active',
            'skills' => ['Sculpting', '3D Modeling'],
        ]);

        $artist2 = User::factory()->create([
            'name' => '2D Illustrator',
            'status' => 'active',
            'skills' => ['Watercolor', 'Illustration'],
        ]);

        $response = $this->get(route('artists.index', ['skill' => 'Sculpting']));

        $response->assertStatus(200);
        $response->assertSee('Digital Sculptor');
        $response->assertDontSee('2D Illustrator');
    }

    public function test_filter_by_multiple_artistic_skills_array(): void
    {
        $artist1 = User::factory()->create([
            'name' => 'Concept Guy',
            'status' => 'active',
            'skills' => ['Concept Art'],
        ]);

        $artist2 = User::factory()->create([
            'name' => 'Animation Pro',
            'status' => 'active',
            'skills' => ['2D Animation'],
        ]);

        $artist3 = User::factory()->create([
            'name' => 'Photographer',
            'status' => 'active',
            'skills' => ['Photography'],
        ]);

        $response = $this->get(route('artists.index', [
            'skills' => ['Concept Art', '2D Animation'],
        ]));

        $response->assertStatus(200);
        $response->assertSee('Concept Guy');
        $response->assertSee('Animation Pro');
        $response->assertDontSee('Photographer');
    }

    public function test_filter_by_software_tool(): void
    {
        $artist1 = User::factory()->create([
            'name' => 'Blender Wizard',
            'status' => 'active',
            'software' => ['Blender', 'Substance Painter'],
        ]);

        $artist2 = User::factory()->create([
            'name' => 'Vector Designer',
            'status' => 'active',
            'software' => ['Illustrator', 'Figma'],
        ]);

        $response = $this->get(route('artists.index', ['tool' => 'Blender']));

        $response->assertStatus(200);
        $response->assertSee('Blender Wizard');
        $response->assertDontSee('Vector Designer');
    }

    public function test_filter_by_multiple_software_tools_array(): void
    {
        $artist1 = User::factory()->create([
            'name' => 'ZBrush Sculptor',
            'status' => 'active',
            'software' => ['ZBrush'],
        ]);

        $artist2 = User::factory()->create([
            'name' => 'Houdini FX Artist',
            'status' => 'active',
            'software' => ['Houdini'],
        ]);

        $artist3 = User::factory()->create([
            'name' => 'Procreate Painter',
            'status' => 'active',
            'software' => ['Procreate'],
        ]);

        $response = $this->get(route('artists.index', [
            'tools' => ['ZBrush', 'Houdini'],
        ]));

        $response->assertStatus(200);
        $response->assertSee('ZBrush Sculptor');
        $response->assertSee('Houdini FX Artist');
        $response->assertDontSee('Procreate Painter');
    }

    public function test_match_mode_or_returns_artists_with_at_least_one_selected_skill_or_tool(): void
    {
        // Artist 1 only has 3D Modeling skill
        $artist1 = User::factory()->create([
            'name' => 'Modeler Only',
            'status' => 'active',
            'skills' => ['3D Modeling'],
            'software' => [],
        ]);

        // Artist 2 only uses Blender software
        $artist2 = User::factory()->create([
            'name' => 'Blender Only',
            'status' => 'active',
            'skills' => [],
            'software' => ['Blender'],
        ]);

        // Artist 3 has neither
        $artist3 = User::factory()->create([
            'name' => 'Photographer Only',
            'status' => 'active',
            'skills' => ['Photography'],
            'software' => ['Lightroom'],
        ]);

        $response = $this->get(route('artists.index', [
            'skills' => ['3D Modeling'],
            'tools'  => ['Blender'],
            'match'  => 'or',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Modeler Only');
        $response->assertSee('Blender Only');
        $response->assertDontSee('Photographer Only');
    }

    public function test_match_mode_all_requires_artist_to_match_all_selected_skills_and_tools(): void
    {
        // Artist 1 has both 3D Modeling AND Concept Art
        $artist1 = User::factory()->create([
            'name' => 'Full Generalist',
            'status' => 'active',
            'skills' => ['3D Modeling', 'Concept Art'],
            'software' => ['Blender', 'Photoshop'],
        ]);

        // Artist 2 has 3D Modeling but lacks Concept Art
        $artist2 = User::factory()->create([
            'name' => 'Partial Match Artist',
            'status' => 'active',
            'skills' => ['3D Modeling'],
            'software' => ['Blender'],
        ]);

        // When matching ALL: selecting both 3D Modeling AND Concept Art
        $response = $this->get(route('artists.index', [
            'skills' => ['3D Modeling', 'Concept Art'],
            'match'  => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Full Generalist');
        $response->assertDontSee('Partial Match Artist');

        // When matching ALL across skills AND software:
        $responseWithTool = $this->get(route('artists.index', [
            'skills' => ['3D Modeling'],
            'tools'  => ['Photoshop'],
            'match'  => 'all',
        ]));

        $responseWithTool->assertStatus(200);
        $responseWithTool->assertSee('Full Generalist');
        $responseWithTool->assertDontSee('Partial Match Artist'); // Partial Match Artist has 3D Modeling but not Photoshop
    }

    public function test_filter_by_city_and_country(): void
    {
        $artist1 = User::factory()->create([
            'name' => 'Tokyo Creator',
            'status' => 'active',
            'city' => 'Tokyo',
            'country' => 'Japan',
        ]);

        $artist2 = User::factory()->create([
            'name' => 'London Creator',
            'status' => 'active',
            'city' => 'London',
            'country' => 'United Kingdom',
        ]);

        $response = $this->get(route('artists.index', ['country' => 'Japan']));

        $response->assertStatus(200);
        $response->assertSee('Tokyo Creator');
        $response->assertDontSee('London Creator');
    }

    public function test_filter_by_commission_availability(): void
    {
        $artist1 = User::factory()->create([
            'name' => 'Open For Work',
            'status' => 'active',
            'commission_status' => 'open',
        ]);

        $artist2 = User::factory()->create([
            'name' => 'Booked Out',
            'status' => 'active',
            'commission_status' => 'closed',
        ]);

        $response = $this->get(route('artists.index', ['availability' => 'commissions_open']));

        $response->assertStatus(200);
        $response->assertSee('Open For Work');
        $response->assertDontSee('Booked Out');
    }

    public function test_suspended_artists_are_never_displayed_in_directory(): void
    {
        $suspended = User::factory()->create([
            'name' => 'Banned User Specialist',
            'status' => 'suspended',
            'headline' => 'Unobtainium Sculptor',
        ]);

        $response = $this->get(route('artists.index', ['search' => 'Banned User Specialist']));

        $response->assertStatus(200);
        $response->assertDontSee('Unobtainium Sculptor');
        $response->assertSee('No artists found');
    }

    public function test_legacy_artistas_route_redirects_301_to_artists_index(): void
    {
        $response = $this->get('/artistas');

        $response->assertStatus(301);
        $response->assertRedirect(route('artists.index'));
    }

    public function test_empty_search_shows_no_artists_found_state(): void
    {
        $response = $this->get(route('artists.index', ['search' => 'NonExistentArtistxyz123']));

        $response->assertStatus(200);
        $response->assertSee('No artists found');
        $response->assertSee('Clear all filters');
    }
}
