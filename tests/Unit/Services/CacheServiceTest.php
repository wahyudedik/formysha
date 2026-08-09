<?php

use App\Models\Tenant;
use App\Models\TenantBranding;
use App\Services\CacheService;

describe('CacheService', function () {
    beforeEach(function () {
        $this->cacheService = new CacheService;
    });

    it('caches tenant branding', function () {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant-cache',
            'is_active' => true,
        ]);
        TenantBranding::create([
            'tenant_id' => $tenant->id,
            'footer_text' => 'Test Footer',
        ]);

        $result = $this->cacheService->getTenantBranding($tenant);

        expect($result)->not->toBeNull();
        expect($result->footer_text)->toBe('Test Footer');
    });

    it('returns null branding when tenant has no branding', function () {
        $tenant = Tenant::create([
            'name' => 'No Branding',
            'slug' => 'no-branding',
            'is_active' => true,
        ]);

        $result = $this->cacheService->getTenantBranding($tenant);

        expect($result)->toBeNull();
    });

    it('invalidates branding cache', function () {
        $tenant = Tenant::create([
            'name' => 'Invalidate Test',
            'slug' => 'invalidate-test',
            'is_active' => true,
        ]);
        TenantBranding::create([
            'tenant_id' => $tenant->id,
            'footer_text' => 'Original',
        ]);

        // Cache it
        $this->cacheService->getTenantBranding($tenant);

        // Invalidate
        $this->cacheService->invalidateBranding($tenant);

        // Should still work (fresh query)
        $result = $this->cacheService->getTenantBranding($tenant);
        expect($result->footer_text)->toBe('Original');
    });

    it('caches subscription active status', function () {
        $tenant = Tenant::create([
            'name' => 'Sub Status',
            'slug' => 'sub-status',
            'is_active' => true,
        ]);

        $result = $this->cacheService->isSubscriptionActive($tenant);

        expect($result)->toBeBool();
    });

    it('caches tenant usage stats', function () {
        $tenant = Tenant::create([
            'name' => 'Usage Stats',
            'slug' => 'usage-stats',
            'is_active' => true,
        ]);

        $result = $this->cacheService->getTenantUsage($tenant);

        expect($result)->toBeArray();
        expect($result)->toHaveKeys(['children', 'photos', 'videos', 'storage_used']);
    });

    it('invalidates all tenant caches', function () {
        $tenant = Tenant::create([
            'name' => 'Invalidate All',
            'slug' => 'invalidate-all',
            'is_active' => true,
        ]);

        // Cache some data
        $this->cacheService->getTenantUsage($tenant);
        $this->cacheService->isSubscriptionActive($tenant);

        // Invalidate all
        $this->cacheService->invalidateTenant($tenant);

        // Should still work (fresh queries)
        $usage = $this->cacheService->getTenantUsage($tenant);
        expect($usage)->toBeArray();
    });
});
