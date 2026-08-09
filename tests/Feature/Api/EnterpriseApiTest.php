<?php

use App\Models\ImportJob;
use App\Models\Tenant;
use App\Models\TenantAnalytic;
use App\Models\TenantInvitation;
use App\Models\User;

describe('Enterprise API', function () {
    it('can get analytics', function () {
        $tenant = Tenant::create([
            'name' => 'Enterprise Tenant',
            'slug' => 'enterprise-analytics',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        TenantAnalytic::create([
            'tenant_id' => $tenant->id,
            'metric' => 'active_users',
            'value' => 5.0,
            'recorded_date' => now()->toDateString(),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/tenant-admin/enterprise/analytics');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'active_users',
                    'total_children',
                    'total_media',
                    'storage_used_mb',
                    'api_calls_today',
                    'recent_metrics',
                ],
            ]);
    });

    it('can list invitations', function () {
        $tenant = Tenant::create([
            'name' => 'Enterprise Tenant',
            'slug' => 'enterprise-invitations',
            'is_active' => true,
        ]);

        $inviter = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);

        TenantInvitation::create([
            'tenant_id' => $tenant->id,
            'email' => 'newuser@example.com',
            'role' => 'parent',
            'token' => str_repeat('a', 64),
            'expires_at' => now()->addDays(7),
            'invited_by' => $inviter->id,
        ]);

        $token = $inviter->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/tenant-admin/enterprise/invitations');

        $response->assertOk()
            ->assertJsonFragment(['email' => 'newuser@example.com']);
    });

    it('can send invitation', function () {
        $tenant = Tenant::create([
            'name' => 'Enterprise Tenant',
            'slug' => 'enterprise-send-invite',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/tenant-admin/enterprise/invitations', [
                'email' => 'invitee@example.com',
                'role' => 'parent',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'invitee@example.com');

        $invitation = TenantInvitation::where('email', 'invitee@example.com')->first();
        expect($invitation)->not->toBeNull();
        expect($invitation->tenant_id)->toBe($tenant->id);
        expect($invitation->role)->toBe('parent');
    });

    it('can revoke invitation', function () {
        $tenant = Tenant::create([
            'name' => 'Enterprise Tenant',
            'slug' => 'enterprise-revoke-invite',
            'is_active' => true,
        ]);

        $inviter = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $inviter->createToken('test-token')->plainTextToken;

        $invitation = TenantInvitation::create([
            'tenant_id' => $tenant->id,
            'email' => 'revoke@example.com',
            'role' => 'parent',
            'token' => str_repeat('b', 64),
            'expires_at' => now()->addDays(7),
            'invited_by' => $inviter->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/tenant-admin/enterprise/invitations/'.$invitation->id);

        $response->assertOk();

        $this->assertDatabaseMissing('tenant_invitations', ['id' => $invitation->id]);
    });

    it('can list import jobs', function () {
        $tenant = Tenant::create([
            'name' => 'Enterprise Tenant',
            'slug' => 'enterprise-import-jobs',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        ImportJob::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'type' => 'photos',
            'status' => 'completed',
            'total_items' => 100,
            'processed_items' => 95,
            'failed_items' => 5,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/tenant-admin/enterprise/import-jobs');

        $response->assertOk()
            ->assertJsonPath('data.0.type', 'photos')
            ->assertJsonPath('data.0.status', 'completed');
    });

    it('requires tenant for enterprise operations', function () {
        $user = User::factory()->tenantAdmin()->create(['tenant_id' => null]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/tenant-admin/enterprise/analytics');

        $response->assertNotFound();
    });

    it('requires authentication', function () {
        $response = $this->getJson('/api/v1/tenant-admin/enterprise/analytics');

        $response->assertUnauthorized();
    });

    it('requires tenant_admin role', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/tenant-admin/enterprise/analytics');

        $response->assertForbidden();
    });
});
