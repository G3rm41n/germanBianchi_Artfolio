<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\CuratedTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTaxonomyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    public function test_non_admin_cannot_access_curated_tags_management(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.taxonomy.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_curated_tags_list(): void
    {
        CuratedTag::firstOrCreate([
            'name' => 'Cyberpunk Art',
            'type' => 'discipline',
        ], [
            'is_suggested' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.taxonomy.index'));
        $response->assertStatus(200);
        $response->assertSee('Cyberpunk Art');
        $response->assertSeeText('Disciplines & Software Toolkit');
    }

    public function test_admin_can_create_a_curated_tag(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.taxonomy.store'), [
            'name' => 'Octane Render Engine',
            'type' => 'software',
            'is_suggested' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('curated_tags', [
            'name' => 'Octane Render Engine',
            'type' => 'software',
            'slug' => 'octane-render-engine',
            'is_suggested' => true,
        ]);

        // Audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'actor_type' => 'admin',
            'action' => 'admin.tag_created',
            'target_label' => 'Octane Render Engine',
        ]);
    }

    public function test_cannot_create_duplicate_tag_in_same_category(): void
    {
        $tag = CuratedTag::firstOrCreate([
            'name' => 'Custom Matte Painting',
            'type' => 'discipline',
        ], [
            'is_suggested' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.taxonomy.store'), [
            'name' => 'custom matte painting', // case insensitive duplicate
            'type' => 'discipline',
            'is_suggested' => true,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, CuratedTag::where('name', 'like', 'custom matte painting')->count());
    }

    public function test_renaming_a_tag_cascades_and_updates_all_user_profiles(): void
    {
        $artist1 = User::factory()->create([
            'skills' => ['Illustration', 'Specialized Concept Art', 'Storyboarding'],
            'software' => ['Photoshop', 'Blender'],
        ]);

        $artist2 = User::factory()->create([
            'skills' => ['specialized concept art', '3D Modeling'],
            'software' => ['ZBrush'],
        ]);

        $artist3 = User::factory()->create([
            'skills' => ['Sculpting'],
            'software' => ['Blender'],
        ]);

        $tag = CuratedTag::firstOrCreate([
            'name' => 'Specialized Concept Art',
            'type' => 'discipline',
        ], [
            'is_suggested' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.taxonomy.update', $tag), [
            'name' => 'Visual Concept Design',
            'is_suggested' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('curated_tags', [
            'id' => $tag->id,
            'name' => 'Visual Concept Design',
            'slug' => 'visual-concept-design',
        ]);

        // Verify users' profiles were updated
        $artist1->refresh();
        $artist2->refresh();
        $artist3->refresh();

        $this->assertContains('Visual Concept Design', $artist1->skills);
        $this->assertNotContains('Specialized Concept Art', $artist1->skills);

        $this->assertContains('Visual Concept Design', $artist2->skills);
        $this->assertNotContains('specialized concept art', $artist2->skills);

        $this->assertNotContains('Visual Concept Design', $artist3->skills);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'actor_type' => 'admin',
            'action' => 'admin.tag_renamed',
            'target_label' => 'Visual Concept Design',
        ]);
    }

    public function test_deleting_a_tag_cascades_and_purges_from_all_user_profiles(): void
    {
        $artist1 = User::factory()->create([
            'skills' => ['Sculpting', '3D Modeling'],
            'software' => ['CustomRenderer', 'Cinema 4D'],
        ]);

        $artist2 = User::factory()->create([
            'skills' => ['Animation'],
            'software' => ['customrenderer', 'Maya'],
        ]);

        $tag = CuratedTag::firstOrCreate([
            'name' => 'CustomRenderer',
            'type' => 'software',
        ], [
            'is_suggested' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.taxonomy.destroy', $tag));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('curated_tags', [
            'id' => $tag->id,
        ]);

        // Verify users' profiles have purged 'CustomRenderer'
        $artist1->refresh();
        $artist2->refresh();

        $this->assertNotContains('CustomRenderer', $artist1->software);
        $this->assertContains('Cinema 4D', $artist1->software);

        $this->assertNotContains('customrenderer', $artist2->software);
        $this->assertContains('Maya', $artist2->software);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'actor_type' => 'admin',
            'action' => 'admin.tag_deleted',
            'target_label' => 'CustomRenderer',
        ]);
    }

    public function test_admin_can_toggle_suggested_status(): void
    {
        $tag = CuratedTag::create([
            'name' => 'Niche Tool',
            'type' => 'software',
            'is_suggested' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.taxonomy.toggleSuggested', $tag));
        $response->assertRedirect();

        $tag->refresh();
        $this->assertFalse($tag->is_suggested);

        $this->assertDatabaseHas('audit_logs', [
            'actor_type' => 'admin',
            'action' => 'admin.tag_toggle_suggested',
            'target_label' => 'Niche Tool',
        ]);
    }
}
