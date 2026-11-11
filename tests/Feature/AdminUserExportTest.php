<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $artist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'                    => 'Admin Evaluator',
            'email'                   => 'admin@artfolio.test',
            'is_admin'                => true,
            'status'                  => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $this->artist = User::factory()->create([
            'name'                    => 'Elena Rostova',
            'slug'                    => 'elena_rostova',
            'email'                   => 'elena@artfolio.test',
            'headline'                => 'Senior 3D Character Artist',
            'city'                    => 'Prague',
            'country'                 => 'Czechia',
            'bio'                     => 'Digital sculptor specializing in museum scans.',
            'commission_status'       => 'open',
            'skills'                  => ['3D Modeling', 'Sculpting'],
            'software'                => ['ZBrush', 'Blender'],
            'hiring_types'            => ['freelance', 'contract'],
            'banner_url'              => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675',
            'banner_crop_x'           => -10,
            'banner_crop_y'           => 15,
            'banner_crop_zoom'        => 1.25,
            'is_admin'                => false,
            'status'                  => 'active',
            'onboarding_completed_at' => now(),
        ]);
    }

    public function test_admin_can_export_single_artist_complete_json_dossier(): void
    {
        $artwork = Artwork::create([
            'user_id'         => $this->artist->id,
            'title'           => 'The Rosetta Stone 3D',
            'description'     => 'Photogrammetry scan',
            'external_url'    => 'https://sketchfab.com/3d-models/rosetta',
            'category'        => '3d',
            'provider'        => 'sketchfab',
            'render_type'     => 'iframe',
            'is_ai_generated' => true,
            'status'          => 'published',
        ]);

        $collection = Collection::create([
            'user_id'     => $this->artist->id,
            'name'        => 'Antiquities Vault',
            'description' => 'Curated 3D scans',
            'visibility'  => 'unlisted',
        ]);
        $collection->artworks()->attach($artwork->id);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.exportOne', $this->artist->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename="artist_elena_rostova_export_', $response->headers->get('Content-Disposition'));

        $data = $response->json();

        $this->assertEquals('single_artist_dossier', $data['export_type']);
        $this->assertEquals('Elena Rostova', $data['artist']['account']['name']);
        $this->assertEquals('elena@artfolio.test', $data['artist']['account']['email']);
        $this->assertEquals('Senior 3D Character Artist', $data['artist']['professional_profile']['headline']);
        $this->assertEquals(['3D Modeling', 'Sculpting'], $data['artist']['professional_profile']['skills']);
        $this->assertEquals(['ZBrush', 'Blender'], $data['artist']['professional_profile']['software']);
        $this->assertEquals(1.25, $data['artist']['visual_identity']['banner_framing']['banner_crop_zoom']);

        // Summary counts & detailed artworks/collections
        $this->assertEquals(1, $data['artist']['summary_counts']['total_artworks']);
        $this->assertEquals(1, $data['artist']['summary_counts']['ai_assisted_artworks']);
        $this->assertEquals(1, $data['artist']['summary_counts']['total_collections']);

        $this->assertCount(1, $data['artist']['artworks']);
        $this->assertEquals('The Rosetta Stone 3D', $data['artist']['artworks'][0]['title']);
        $this->assertTrue($data['artist']['artworks'][0]['is_ai_generated']);

        $this->assertCount(1, $data['artist']['collections']);
        $this->assertEquals('Antiquities Vault', $data['artist']['collections'][0]['name']);
        $this->assertEquals('unlisted', $data['artist']['collections'][0]['visibility']);
        $this->assertEquals([$artwork->id], $data['artist']['collections'][0]['artwork_ids']);
    }

    public function test_admin_can_export_filtered_users_list_as_json(): void
    {
        $suspendedArtist = User::factory()->create([
            'name'     => 'Suspended Creator',
            'status'   => 'suspended',
            'is_admin' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.exportAll', ['status' => 'suspended', 'role' => 'artist']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json; charset=UTF-8');

        $data = $response->json();

        $this->assertEquals('users_and_artists_collection', $data['export_type']);
        $this->assertEquals('suspended', $data['filters_applied']['status']);
        $this->assertEquals(1, $data['total_exported']);
        $this->assertEquals('Suspended Creator', $data['users'][0]['account']['name']);
    }

    public function test_non_admin_cannot_export_artist_data(): void
    {
        $this->actingAs($this->artist)
            ->get(route('admin.users.exportOne', $this->artist->id))
            ->assertForbidden();

        $this->actingAs($this->artist)
            ->get(route('admin.users.exportAll'))
            ->assertForbidden();
    }

    public function test_admin_can_check_if_artist_id_exists(): void
    {
        // Existing artist
        $response1 = $this->actingAs($this->admin)
            ->postJson(route('admin.users.importCheck'), [
                'id' => $this->artist->id,
            ]);

        $response1->assertOk();
        $this->assertTrue($response1->json('exists'));
        $this->assertEquals('Elena Rostova', $response1->json('user.name'));

        // Non-existing artist ID
        $response2 = $this->actingAs($this->admin)
            ->postJson(route('admin.users.importCheck'), [
                'id'    => 99999,
                'email' => 'nobody@unknown.test',
            ]);

        $response2->assertOk();
        $this->assertFalse($response2->json('exists'));
    }

    public function test_admin_can_import_and_update_existing_artist(): void
    {
        // Existing artwork for artist
        $initialArtwork = Artwork::create([
            'user_id'         => $this->artist->id,
            'title'           => 'Initial Sculpture',
            'external_url'    => 'https://sketchfab.com/3d-models/initial',
            'category'        => '3d',
            'provider'        => 'sketchfab',
            'render_type'     => 'iframe',
            'status'          => 'published',
            'is_ai_generated' => false,
        ]);

        $payload = [
            'export_type' => 'single_artist_dossier',
            'artist' => [
                'account' => [
                    'id'    => $this->artist->id,
                    'name'  => 'Elena Rostova Updated',
                    'email' => $this->artist->email,
                ],
                'professional_profile' => [
                    'headline' => 'Lead Character Director',
                    'skills'   => ['3D Modeling', 'Photogrammetry', 'Rigging'],
                ],
                'artworks' => [
                    [
                        'title'           => 'Imported New Artwork',
                        'external_url'    => 'https://sketchfab.com/3d-models/imported',
                        'category'        => '3d',
                        'provider'        => 'sketchfab',
                        'render_type'     => 'iframe',
                        'is_ai_generated' => true,
                        'status'          => 'published',
                    ],
                ],
                'collections' => [
                    [
                        'name'        => 'New Masterpieces',
                        'description' => 'Collection from JSON',
                        'visibility'  => 'public',
                    ],
                ],
            ],
        ];

        // Mode: 'update' (keeps initial artwork, adds imported one)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.importOne'), [
                'conflict_mode' => 'update',
                'json_data'     => json_encode($payload),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->artist->refresh();
        $this->assertEquals('Elena Rostova Updated', $this->artist->name);
        $this->assertEquals('Lead Character Director', $this->artist->headline);
        $this->assertCount(2, $this->artist->artworks);
        $this->assertCount(1, $this->artist->collections);
    }

    public function test_admin_can_import_and_replace_existing_artist(): void
    {
        // Old artwork that should be removed when replaced
        Artwork::create([
            'user_id'         => $this->artist->id,
            'title'           => 'Old Obsolete Piece',
            'external_url'    => 'https://sketchfab.com/3d-models/old',
            'category'        => '3d',
            'provider'        => 'sketchfab',
            'render_type'     => 'iframe',
            'status'          => 'published',
            'is_ai_generated' => false,
        ]);

        $payload = [
            'export_type' => 'single_artist_dossier',
            'artist' => [
                'account' => [
                    'id'    => $this->artist->id,
                    'name'  => 'Elena Replaced Profile',
                    'email' => $this->artist->email,
                ],
                'professional_profile' => [
                    'headline' => 'Completely Overwritten Bio',
                ],
                'artworks' => [
                    [
                        'title'           => 'Sole New Artwork',
                        'external_url'    => 'https://sketchfab.com/3d-models/sole',
                        'category'        => '3d',
                        'provider'        => 'sketchfab',
                        'render_type'     => 'iframe',
                        'status'          => 'published',
                        'is_ai_generated' => false,
                    ],
                ],
                'collections' => [],
            ],
        ];

        // Mode: 'replace'
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.importOne'), [
                'conflict_mode' => 'replace',
                'json_data'     => json_encode($payload),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->artist->refresh();
        $this->assertEquals('Elena Replaced Profile', $this->artist->name);
        $this->assertCount(1, $this->artist->artworks);
        $this->assertEquals('Sole New Artwork', $this->artist->artworks->first()->title);
    }

    public function test_admin_can_batch_import_users_list(): void
    {
        $batchPayload = [
            'export_type' => 'users_and_artists_collection',
            'users' => [
                [
                    'account' => [
                        'name'  => 'Batch Artist Alpha',
                        'email' => 'alpha@batch.test',
                        'slug'  => 'alpha_artist',
                    ],
                    'artworks' => [],
                    'collections' => [],
                ],
                [
                    'account' => [
                        'name'  => 'Batch Artist Beta',
                        'email' => 'beta@batch.test',
                        'slug'  => 'beta_artist',
                    ],
                    'artworks' => [],
                    'collections' => [],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.importAll'), [
                'conflict_mode' => 'update',
                'json_data'     => json_encode($batchPayload),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['email' => 'alpha@batch.test']);
        $this->assertDatabaseHas('users', ['email' => 'beta@batch.test']);
    }

    public function test_non_admin_cannot_access_import_routes(): void
    {
        $this->actingAs($this->artist)
            ->postJson(route('admin.users.importCheck'), ['id' => 1])
            ->assertForbidden();

        $this->actingAs($this->artist)
            ->post(route('admin.users.importOne'), [])
            ->assertForbidden();

        $this->actingAs($this->artist)
            ->post(route('admin.users.importAll'), [])
            ->assertForbidden();
    }
}

