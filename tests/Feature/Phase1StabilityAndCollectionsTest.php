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
}
