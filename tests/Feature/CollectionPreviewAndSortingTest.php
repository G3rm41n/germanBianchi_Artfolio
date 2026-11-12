<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionPreviewAndSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_select_custom_preview_artwork_ids_when_updating_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $user->id,
            'name' => 'Colección 3D',
            'visibility' => 'public',
        ]);

        $art1 = Artwork::factory()->create(['user_id' => $user->id, 'title' => 'Obra 1', 'status' => 'published']);
        $art2 = Artwork::factory()->create(['user_id' => $user->id, 'title' => 'Obra 2', 'status' => 'published']);
        $art3 = Artwork::factory()->create(['user_id' => $user->id, 'title' => 'Obra 3', 'status' => 'published']);

        $collection->artworks()->attach([$art1->id, $art2->id, $art3->id]);

        $response = $this->actingAs($user)->put(route('collections.update', $collection), [
            'name' => 'Colección 3D Actualizada',
            'visibility' => 'public',
            'preview_artwork_ids' => [$art3->id, $art1->id, $art2->id],
        ]);

        $response->assertRedirect(route('collections.index'));
        $collection->refresh();

        $this->assertEquals([$art3->id, $art1->id, $art2->id], $collection->preview_artwork_ids);
        
        $previews = $collection->preview_artworks;
        $this->assertCount(3, $previews);
        $this->assertEquals($art3->id, $previews->first()->id);
    }

    public function test_collections_index_renders_pinterest_3_image_mosaic_and_sort_options(): void
    {
        $user = User::factory()->create();
        $col1 = Collection::create([
            'user_id' => $user->id,
            'name' => 'Alpha Board',
            'visibility' => 'public',
            'sort_order' => 1,
        ]);
        $col2 = Collection::create([
            'user_id' => $user->id,
            'name' => 'Beta Board',
            'visibility' => 'private',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('collections.index'));

        $response->assertStatus(200);
        $response->assertSee('Alpha Board');
        $response->assertSee('Beta Board');
        $response->assertSee('A to Z');
        $response->assertSee('Custom');
        $response->assertSee('Last artwork added');
    }

    public function test_user_can_filter_and_sort_collections(): void
    {
        $user = User::factory()->create();
        Collection::create([
            'user_id' => $user->id,
            'name' => 'Zeta Collection',
            'visibility' => 'public',
        ]);
        Collection::create([
            'user_id' => $user->id,
            'name' => 'Alpha Collection',
            'visibility' => 'private',
        ]);

        // Filter public only
        $responsePublic = $this->actingAs($user)->get(route('collections.index', ['filter' => 'public']));
        $responsePublic->assertStatus(200);
        $responsePublic->assertSee('Zeta Collection');
        $responsePublic->assertDontSee('Alpha Collection');

        // Sort A to Z
        $responseAz = $this->actingAs($user)->get(route('collections.index', ['sort' => 'az']));
        $responseAz->assertStatus(200);
        $responseAz->assertSeeInOrder(['Alpha Collection', 'Zeta Collection']);
    }

    public function test_user_can_reorder_collections_via_drag_and_drop_endpoint(): void
    {
        $user = User::factory()->create();
        $col1 = Collection::create([
            'user_id' => $user->id,
            'name' => 'Col 1',
            'sort_order' => 0,
        ]);
        $col2 = Collection::create([
            'user_id' => $user->id,
            'name' => 'Col 2',
            'sort_order' => 1,
        ]);

        // Reorder putting Col 2 first
        $response = $this->actingAs($user)->postJson(route('collections.reorder'), [
            'ordered_ids' => [$col2->id, $col1->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $col1->refresh();
        $col2->refresh();

        $this->assertEquals(0, $col2->sort_order);
        $this->assertEquals(1, $col1->sort_order);
    }

    public function test_owner_can_set_specific_artwork_cover_slot_via_modal_endpoint(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $owner->id,
            'name' => 'Tablero Cover Test',
            'visibility' => 'public',
        ]);

        $art1 = Artwork::factory()->create(['user_id' => $owner->id, 'title' => 'Obra Slot 0', 'status' => 'published']);
        $art2 = Artwork::factory()->create(['user_id' => $owner->id, 'title' => 'Obra Slot 1', 'status' => 'published']);
        $collection->artworks()->attach([$art1->id, $art2->id]);

        // Other user cannot change cover
        $responseOther = $this->actingAs($otherUser)->postJson(route('collections.setPreviewArtwork', $collection), [
            'artwork_id' => $art1->id,
            'slot' => 0,
        ]);
        $responseOther->assertStatus(403);

        // Owner sets art1 as main slot (0)
        $responseOwner = $this->actingAs($owner)->postJson(route('collections.setPreviewArtwork', $collection), [
            'artwork_id' => $art1->id,
            'slot' => 0,
        ]);
        $responseOwner->assertStatus(200);
        $responseOwner->assertJson([
            'success' => true,
            'slot' => 0,
        ]);

        $collection->refresh();
        $this->assertEquals($art1->id, $collection->preview_artwork_ids[0]);
    }
}

