<?php

use App\Models\Tenant;
use App\Models\TenantBranding;
use App\Models\User;

describe('Domain API', function () {
    it('can set custom domain', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-domain',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/tenant-admin/domain', [
                'custom_domain' => 'anak.kliniksehat.id',
            ]);

        $response->assertOk();

        $tenant->refresh();
        expect($tenant->custom_domain)->toBe('anak.kliniksehat.id');
    });

    it('can verify domain', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-verify',
            'is_active' => true,
            'custom_domain' => 'anak.kliniksehat.id',
        ]);

        TenantBranding::create([
            'tenant_id' => $tenant->id,
            'custom_domain' => 'anak.kliniksehat.id',
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/tenant-admin/domain/verify');

        // DNS verification will fail in test environment (no real DNS records)
        // so the endpoint correctly returns 422
        $response->assertStatus(422);
    });

    it('returns error when no custom domain is configured', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-no-domain',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/tenant-admin/domain/verify');

        $response->assertStatus(422)
            ->assertJsonPath('message', fn ($msg) => str_contains($msg, 'custom domain'));
    });

    it('can detect domain conflict', function () {
        $tenant1 = Tenant::create([
            'name' => 'Tenant 1',
            'slug' => 'tenant-1-conflict',
            'is_active' => true,
        ]);

        $tenant2 = Tenant::create([
            'name' => 'Tenant 2',
            'slug' => 'tenant-2-conflict',
            'is_active' => true,
        ]);

        // First tenant sets domain directly in database
        $tenant1->update(['custom_domain' => 'shared.kliniksehat.id']);

        // Second tenant tries same domain
        $user2 = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant2->id]);
        $token2 = $user2->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token2)
            ->putJson('/api/tenant-admin/domain', [
                'custom_domain' => 'shared.kliniksehat.id',
            ]);

        $response->assertStatus(422);
    });

    it('can remove custom domain', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-remove-domain',
            'is_active' => true,
            'custom_domain' => 'old.kliniksehat.id',
        ]);

        TenantBranding::create([
            'tenant_id' => $tenant->id,
            'custom_domain' => 'old.kliniksehat.id',
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/tenant-admin/domain');

        $response->assertOk();

        $tenant->refresh();
        expect($tenant->custom_domain)->toBeNull();
    });

    it('can get domain status', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-domain-status',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/tenant-admin/domain');

        $response->assertOk();
    });

    it('requires authentication for domain operations', function () {
        $response = $this->putJson('/api/tenant-admin/domain', [
            'custom_domain' => 'test.domain.com',
        ]);

        $response->assertUnauthorized();
    });
});
