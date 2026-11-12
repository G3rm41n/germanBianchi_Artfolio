<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
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

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_audit_logs_separated_by_actor_type(): void
    {
        // Generate an admin action
        AuditLogger::log(
            'admin.user_suspended',
            $this->regularUser,
            $this->regularUser->name,
            ['status' => 'suspended'],
            "Admin suspended user {$this->regularUser->name}",
            $this->admin,
            'admin'
        );

        // Generate a regular user action
        AuditLogger::log(
            'user.profile_updated',
            $this->regularUser,
            $this->regularUser->name,
            ['bio' => 'New bio'],
            "User updated bio",
            $this->regularUser,
            'user'
        );

        // 1. Visit Administrator tab (default)
        $adminView = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', ['actor' => 'admin']));
        $adminView->assertStatus(200);
        $adminView->assertSee('admin.user_suspended');
        $adminView->assertDontSee('user.profile_updated');

        // 2. Visit User Activity tab
        $userView = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', ['actor' => 'user']));
        $userView->assertStatus(200);
        $userView->assertSee('user.profile_updated');
        $userView->assertDontSee('admin.user_suspended');
    }

    public function test_admin_can_filter_audit_logs_by_action_and_search(): void
    {
        AuditLogger::log(
            'admin.user_suspended',
            $this->regularUser,
            'JohnDoeArtist',
            null,
            "Suspended for policy violation",
            $this->admin,
            'admin'
        );

        AuditLogger::log(
            'admin.tag_created',
            null,
            'Blender 4.0',
            null,
            "Added Blender 4.0",
            $this->admin,
            'admin'
        );

        // Search for 'JohnDoeArtist'
        $searchResponse = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'actor' => 'admin',
            'search' => 'JohnDoeArtist',
        ]));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('JohnDoeArtist');
        $searchResponse->assertDontSee('Blender 4.0');

        // Filter by action
        $actionResponse = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'actor' => 'admin',
            'action' => 'admin.tag_created',
        ]));

        $actionResponse->assertStatus(200);
        $actionResponse->assertSee('Blender 4.0');
        $actionResponse->assertDontSee('JohnDoeArtist');
    }

    public function test_admin_suspending_user_records_admin_audit_log(): void
    {
        $targetUser = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->admin)->post(route('admin.users.suspend', $targetUser->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'actor_type' => 'admin',
            'user_id' => $this->admin->id,
            'action' => 'admin.user_suspended',
            'target_id' => $targetUser->id,
            'target_label' => $targetUser->name,
        ]);
    }

    public function test_user_updating_profile_records_user_audit_log(): void
    {
        $response = $this->actingAs($this->regularUser)->patch(route('profile.update'), [
            'name' => 'Renamed Artist',
            'email' => $this->regularUser->email,
            'slug' => $this->regularUser->slug,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'actor_type' => 'user',
            'user_id' => $this->regularUser->id,
            'action' => 'user.profile_updated',
        ]);
    }
}

