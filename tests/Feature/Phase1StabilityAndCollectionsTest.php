<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1StabilityAndCollectionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
            'commission_status' => 'open',
        ]);
    }

    public function test_dashboard_displays_three_cards_and_kpis(): void
    {
        // Create an artwork and a collection for KPIs
        Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Dashboard Test Artwork',
            'description' => 'Testing KPIs',
            'external_url' => 'https://www.deviantart.com/art/test-kpi',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        Collection::create([
            'user_id' => $this->user->id,
            'name' => 'Featured 3D',
            'visibility' => 'public',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Artist Dashboard');
        $response->assertSee('My Artworks');
        $response->assertSee('Collections & Favorites', false);
        $response->assertSee('Settings & Profile Hub', false);
        $response->assertSee('Published Artworks');
        $response->assertSee('Saved Favorites');
        $response->assertSee('Open for Work');
    }

    public function test_custom_404_error_view_is_rendered(): void
    {
        $response = $this->get('/non-existent-page-xyz-123');

        $response->assertStatus(404);
        $response->assertSee('Artwork or Page Not Found');
        $response->assertSee('Error 404');
    }

    public function test_artwork_policy_prevents_unauthorized_editing(): void
    {
        $otherUser = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $artwork = Artwork::create([
            'user_id' => $otherUser->id,
            'title' => 'Other Artist Piece',
            'external_url' => 'https://www.deviantart.com/art/other-1',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        // Attempting to edit other user's artwork should return 403
        $response = $this->actingAs($this->user)->get(route('artworks.edit', $artwork));
        $response->assertStatus(403);
    }

    public function test_collection_policy_prevents_unauthorized_editing(): void
    {
        $otherUser = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        $collection = Collection::create([
            'user_id' => $otherUser->id,
            'name' => 'Private Vault',
            'visibility' => 'private',
        ]);

        // Attempting to edit other user's collection should return 403
        $response = $this->actingAs($this->user)->get(route('collections.edit', $collection));
        $response->assertStatus(403);
    }

    public function test_user_can_create_and_update_collection_with_visibility(): void
    {
        // 1. Create unlisted collection
        $response = $this->actingAs($this->user)->post(route('collections.store'), [
            'name' => 'WIP Sketches',
            'description' => 'Unlisted work in progress',
            'visibility' => 'unlisted',
        ]);

        $response->assertRedirect(route('collections.index'));
        $this->assertDatabaseHas('collections', [
            'user_id' => $this->user->id,
            'name' => 'WIP Sketches',
            'visibility' => 'unlisted',
        ]);

        $collection = Collection::where('name', 'WIP Sketches')->first();

        // 2. Update to private collection
        $updateResponse = $this->actingAs($this->user)->put(route('collections.update', $collection), [
            'name' => 'Top Secret Concepts',
            'description' => 'Strictly private',
            'visibility' => 'private',
        ]);

        $updateResponse->assertRedirect(route('collections.index'));
        $collection->refresh();
        $this->assertEquals('private', $collection->visibility);
        $this->assertTrue($collection->isPrivate());
    }

    public function test_public_profile_collection_visibility_rules(): void
    {
        $visitor = User::factory()->create([
            'status' => 'active',
            'onboarding_completed_at' => now(),
        ]);

        // Create 3 collections for $this->user
        $publicCol = Collection::create([
            'user_id' => $this->user->id,
            'name' => 'Public Highlights',
            'visibility' => 'public',
        ]);

        $unlistedCol = Collection::create([
            'user_id' => $this->user->id,
            'name' => 'Unlisted Client Demos',
            'visibility' => 'unlisted',
        ]);

        $privateCol = Collection::create([
            'user_id' => $this->user->id,
            'name' => 'Private Archives',
            'visibility' => 'private',
        ]);

        // Create an artwork in each collection
        $art1 = Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Public Art 1',
            'external_url' => 'https://www.deviantart.com/art/p1',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);
        $art1->collections()->attach($publicCol);

        $art2 = Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Unlisted Art 2',
            'external_url' => 'https://www.deviantart.com/art/u2',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);
        $art2->collections()->attach($unlistedCol);

        $art3 = Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Private Art 3',
            'external_url' => 'https://www.deviantart.com/art/pr3',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);
        $art3->collections()->attach($privateCol);

        // 1. Visitor viewing profile tabs sees Public, but NOT Unlisted or Private
        $responseVisitor = $this->actingAs($visitor)->get(route('profile.public', $this->user->slug));
        $responseVisitor->assertOk();
        $responseVisitor->assertSee('Public Highlights');
        $responseVisitor->assertDontSee('Unlisted Client Demos');
        $responseVisitor->assertDontSee('Private Archives');

        // 2. Visitor accessing Unlisted collection via direct link CAN view it
        $responseUnlisted = $this->actingAs($visitor)->get(route('profile.public', [
            'slug' => $this->user->slug,
            'collection_id' => $unlistedCol->id,
        ]));
        $responseUnlisted->assertOk();
        $responseUnlisted->assertSee('Unlisted Art 2');

        // 3. Visitor accessing Private collection via direct link gets 403 Forbidden
        $responsePrivate = $this->actingAs($visitor)->get(route('profile.public', [
            'slug' => $this->user->slug,
            'collection_id' => $privateCol->id,
        ]));
        $responsePrivate->assertStatus(403);

        // 4. Owner viewing their own profile can see all tabs including Unlisted and Private badges
        $responseOwner = $this->actingAs($this->user)->get(route('profile.public', $this->user->slug));
        $responseOwner->assertOk();
        $responseOwner->assertSee('Public Highlights');
        $responseOwner->assertSee('Unlisted Client Demos');
        $responseOwner->assertSee('Private Archives');
    }

    public function test_artwork_collection_status_endpoint(): void
    {
        $artwork = Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Sample Status Artwork',
            'external_url' => 'https://www.deviantart.com/art/sample-status',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        $col1 = Collection::create([
            'user_id' => $this->user->id,
            'name' => 'Collection One',
            'visibility' => 'public',
        ]);

        $col2 = Collection::create([
            'user_id' => $this->user->id,
            'name' => 'Collection Two',
            'visibility' => 'unlisted',
        ]);

        // Attach artwork to col1 only
        $artwork->collections()->attach($col1);

        // 1. Guest request is redirected
        $guestResponse = $this->getJson(route('collections.artworkStatus', $artwork));
        $guestResponse->assertUnauthorized();

        // 2. Authenticated user receives collection list with is_in_collection states
        $authResponse = $this->actingAs($this->user)->getJson(route('collections.artworkStatus', $artwork));
        $authResponse->assertOk();
        $authResponse->assertJsonStructure([
            'collections' => [
                '*' => ['id', 'name', 'visibility', 'is_in_collection']
            ]
        ]);

        $data = $authResponse->json('collections');
        $this->assertCount(2, $data);

        $col1Data = collect($data)->firstWhere('id', $col1->id);
        $col2Data = collect($data)->firstWhere('id', $col2->id);

        $this->assertTrue($col1Data['is_in_collection']);
        $this->assertFalse($col2Data['is_in_collection']);
    }

    public function test_toggle_artwork_in_collection(): void
    {
        $artwork = Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Toggle Artwork Piece',
            'external_url' => 'https://www.deviantart.com/art/toggle-art',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        $collection = Collection::create([
            'user_id' => $this->user->id,
            'name' => 'Cyberpunk Art',
            'visibility' => 'public',
        ]);

        // 1. Toggle to attach
        $response1 = $this->actingAs($this->user)->postJson(route('collections.toggleArtwork', $collection), [
            'artwork_id' => $artwork->id,
        ]);

        $response1->assertOk();
        $response1->assertJson([
            'success' => true,
            'attached' => true,
        ]);
        $this->assertTrue($collection->artworks()->where('artworks.id', $artwork->id)->exists());

        // 2. Toggle to detach
        $response2 = $this->actingAs($this->user)->postJson(route('collections.toggleArtwork', $collection), [
            'artwork_id' => $artwork->id,
        ]);

        $response2->assertOk();
        $response2->assertJson([
            'success' => true,
            'attached' => false,
        ]);
        $this->assertFalse($collection->artworks()->where('artworks.id', $artwork->id)->exists());

        // 3. Another user cannot toggle into someone else's collection
        $otherUser = User::factory()->create(['status' => 'active', 'onboarding_completed_at' => now()]);
        $responseForbidden = $this->actingAs($otherUser)->postJson(route('collections.toggleArtwork', $collection), [
            'artwork_id' => $artwork->id,
        ]);
        $responseForbidden->assertStatus(403);
    }

    public function test_quick_create_collection_with_artwork(): void
    {
        $artwork = Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Quick Add Artwork',
            'external_url' => 'https://www.deviantart.com/art/quick-add',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('collections.quickCreateWithArtwork'), [
            'name' => 'Brand New Quick Collection',
            'visibility' => 'unlisted',
            'artwork_id' => $artwork->id,
        ]);

        $response->assertCreated();
        $response->assertJson([
            'success' => true,
            'collection' => [
                'name' => 'Brand New Quick Collection',
                'visibility' => 'unlisted',
                'is_in_collection' => true,
            ],
        ]);

        $createdCollection = Collection::where('name', 'Brand New Quick Collection')->first();
        $this->assertNotNull($createdCollection);
        $this->assertEquals('unlisted', $createdCollection->visibility);
        $this->assertTrue($createdCollection->artworks()->where('artworks.id', $artwork->id)->exists());
    }

    public function test_artwork_card_and_guest_modal_integration(): void
    {
        Artwork::create([
            'user_id' => $this->user->id,
            'title' => 'Card Display Art',
            'external_url' => 'https://www.deviantart.com/art/display-art',
            'category' => 'image',
            'provider' => 'deviantart',
            'render_type' => 'image',
            'status' => 'published',
        ]);

        // When a guest visits the home page, the artwork card renders with the collection picker trigger
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Card Display Art');
        $response->assertSee('title="Add to Collection"', false);
        $response->assertSee('context === \'coleccion\'', false);
    }
}
