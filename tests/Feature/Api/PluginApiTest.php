<?php

use App\Models\Plugin;
use App\Models\Tenant;
use App\Models\TenantPlugin;
use App\Models\User;

describe('Plugin API', function () {
    it('can list plugins', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-plugins',
            'is_active' => true,
        ]);

        Plugin::create([
            'name' => 'WhatsApp Integration',
            'slug' => 'whatsapp-integration',
            'description' => 'Integrasi WhatsApp API',
            'version' => '1.0.0',
            'author' => 'ForMysha',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/tenant-admin/plugins');

        $response->assertOk()
            ->assertJsonFragment(['slug' => 'whatsapp-integration']);
    });

    it('can install plugin', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-install',
            'is_active' => true,
        ]);

        $plugin = Plugin::create([
            'name' => 'SMS Gateway',
            'slug' => 'sms-gateway',
            'description' => 'Integrasi SMS',
            'version' => '1.0.0',
            'author' => 'ForMysha',
            'is_active' => true,
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/tenant-admin/plugins/{$plugin->id}/install");

        $response->assertCreated();

        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        expect($tenantPlugin)->not->toBeNull();
        expect($tenantPlugin->is_enabled)->toBeTrue();
    });

    it('can toggle plugin', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-toggle',
            'is_active' => true,
        ]);

        $plugin = Plugin::create([
            'name' => 'Email SMTP',
            'slug' => 'email-smtp',
            'description' => 'Konfigurasi email SMTP',
            'version' => '1.0.0',
            'author' => 'ForMysha',
            'is_active' => true,
        ]);

        TenantPlugin::create([
            'tenant_id' => $tenant->id,
            'plugin_id' => $plugin->id,
            'is_enabled' => true,
            'installed_at' => now(),
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/tenant-admin/plugins/{$plugin->id}/toggle");

        $response->assertOk();

        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        expect($tenantPlugin->is_enabled)->toBeFalse();
    });

    it('can get plugin settings', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-settings',
            'is_active' => true,
        ]);

        $plugin = Plugin::create([
            'name' => 'Cloud Storage',
            'slug' => 'cloud-storage',
            'description' => 'Integrasi cloud storage',
            'version' => '1.0.0',
            'author' => 'ForMysha',
            'is_active' => true,
        ]);

        TenantPlugin::create([
            'tenant_id' => $tenant->id,
            'plugin_id' => $plugin->id,
            'settings' => ['api_key' => 'test-key', 'bucket' => 'my-bucket'],
            'is_enabled' => true,
            'installed_at' => now(),
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/tenant-admin/plugins/{$plugin->id}/settings");

        $response->assertOk()
            ->assertJsonPath('data.api_key', 'test-key');
    });

    it('can update plugin settings', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-update-settings',
            'is_active' => true,
        ]);

        $plugin = Plugin::create([
            'name' => 'Webhook Pro',
            'slug' => 'webhook-pro',
            'description' => 'Webhook lanjutan',
            'version' => '1.0.0',
            'author' => 'ForMysha',
            'is_active' => true,
        ]);

        TenantPlugin::create([
            'tenant_id' => $tenant->id,
            'plugin_id' => $plugin->id,
            'settings' => ['endpoint' => 'https://old.com'],
            'is_enabled' => true,
            'installed_at' => now(),
        ]);

        $user = User::factory()->tenantAdmin()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/tenant-admin/plugins/{$plugin->id}/settings", [
                'settings' => ['endpoint' => 'https://new.com'],
            ]);

        $response->assertOk();

        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        expect($tenantPlugin->settings['endpoint'])->toBe('https://new.com');
    });
});
