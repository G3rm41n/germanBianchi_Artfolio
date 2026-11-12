<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_collection_board_can_be_viewed_by_anyone(): void
    {
        $user = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $user->id,
            'name' => 'Arte Futurista',
            'description' => 'Mi colección de arte futurista',
            'visibility' => 'public',
        ]);

        $artwork = Artwork::factory()->create([
            'user_id' => $user->id,
            'title' => 'Cyber City 2077',
            'status' => 'published',
        ]);

        $collection->artworks()->attach($artwork->id);

        $response = $this->get($collection->url);

        $response->assertStatus(200);
        $response->assertSee('Arte Futurista');
        $response->assertSee('Cyber City 2077');
        $response->assertSee('Board');
        $response->assertSee('Exhibition');

        // URL contains artist slug first (Pinterest style: /{artist}/collections/{collection})
        $this->assertStringContainsString("/{$user->slug}/collections/{$collection->id}", $collection->url);
    }

    public function test_unlisted_collection_board_can_be_viewed_by_guest_with_direct_link(): void
    {
        $user = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $user->id,
            'name' => 'Tablero Oculto',
            'visibility' => 'unlisted',
        ]);

        $response = $this->get($collection->url);

        $response->assertStatus(200);
        $response->assertSee('Tablero Oculto');
        $response->assertSee('Unlisted');
    }

    public function test_private_collection_board_cannot_be_viewed_by_guest_or_other_users(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $owner->id,
            'name' => 'Tablero Secreto',
            'visibility' => 'private',
        ]);

        // Guest is denied (403)
        $responseGuest = $this->get($collection->url);
        $responseGuest->assertStatus(403);

        // Other authenticated user is denied (403)
        $responseOther = $this->actingAs($otherUser)->get($collection->url);
        $responseOther->assertStatus(403);

        // Owner can view their own private collection
        $responseOwner = $this->actingAs($owner)->get($collection->url);
        $responseOwner->assertStatus(200);
        $responseOwner->assertSee('Tablero Secreto');
        $responseOwner->assertSee('Private Board');
    }

    public function test_legacy_coleccion_route_redirects_301_to_collections_show(): void
    {
        $user = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $user->id,
            'name' => 'Coleccion Redirigida',
            'visibility' => 'public',
        ]);

        $response = $this->get("/coleccion/{$collection->id}");

        $response->assertStatus(301);
        $response->assertRedirect($collection->url);
    }

    public function test_legacy_collections_id_redirects_301_to_artist_collections_show(): void
    {
        $user = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $user->id,
            'name' => 'Legacy Coleccion',
            'visibility' => 'public',
        ]);

        $response = $this->get("/collections/{$collection->id}");

        $response->assertStatus(301);
        $response->assertRedirect($collection->url);
    }

    public function test_admin_can_view_private_collection_board(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $owner->id,
            'name' => 'Colección Privada Revisable',
            'visibility' => 'private',
        ]);

        $response = $this->actingAs($admin)->get($collection->url);
        $response->assertStatus(200);
        $response->assertSee('Colección Privada Revisable');
    }

    public function test_wrong_artist_in_url_redirects_301_to_actual_owner_url(): void
    {
        $owner = User::factory()->create();
        $imposter = User::factory()->create();

        $collection = Collection::create([
            'user_id' => $owner->id,
            'name' => 'Authentic Board',
            'visibility' => 'public',
        ]);

        // Attempting to access the board using the imposter's slug in the URL
        $response = $this->get("/{$imposter->slug}/collections/{$collection->id}");

        $response->assertStatus(301);
        $response->assertRedirect($collection->url);
    }
}
