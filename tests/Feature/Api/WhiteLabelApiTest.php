<?php

use App\Models\Tenant;
use App\Models\TenantBranding;
use App\Models\User;
use Illuminate\Http\UploadedFile;

describe('White Label API', function () {
    it('can get branding', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-branding',
            'is_active' => true,
        ]);

        TenantBranding::create([
            'tenant_id' => $tenant->id,
            'organization_name' => 'Klinik Sehat',
            'primary_color' => '#4A90D9',
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/tenant-admin/branding');

        $response->assertOk()
            ->assertJsonPath('data.organization_name', 'Klinik Sehat')
            ->assertJsonPath('data.primary_color', '#4A90D9');
    });

    it('can update branding', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-branding-update',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/tenant-admin/branding/advanced', [
                'organization_name' => 'Klinik Baru',
                'primary_color' => '#FF5733',
                'login_heading' => 'Selamat Datang',
                'footer_text' => '© 2026 Klinik Baru',
            ]);

        $response->assertOk();

        $branding = TenantBranding::where('tenant_id', $tenant->id)->first();
        expect($branding->organization_name)->toBe('Klinik Baru');
        expect($branding->primary_color)->toBe('#FF5733');
    });

    it('can upload favicon', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-favicon',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        TenantBranding::create([
            'tenant_id' => $tenant->id,
        ]);

        $file = UploadedFile::fake()->image('favicon.png', 32, 32);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/tenant-admin/branding/favicon', [
                'favicon' => $file,
            ]);

        $response->assertOk();
    });

    it('returns 404 when branding not configured', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-no-branding',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/tenant-admin/branding');

        $response->assertNotFound();
    });

    it('requires authentication', function () {
        $response = $this->getJson('/api/tenant-admin/branding');

        $response->assertUnauthorized();
    });
});
