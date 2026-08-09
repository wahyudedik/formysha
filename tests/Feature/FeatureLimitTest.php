<?php

use App\Models\Child;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;

describe('Feature Limit Middleware', function () {
    it('rejects action exceeding free plan child limit via service', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Kecil',
            'slug' => 'keluarga-kecil',
            'is_active' => true,
        ]);

        $freePlan = Plan::create([
            'name' => 'Gratis',
            'slug' => 'free',
            'price_monthly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        // Create one child (free plan allows max 1)
        Child::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
        ]);

        // Set tenant in session
        $this->app['session']->put('tenant_id', $tenant->id);

        $tenantService = app(TenantService::class);

        expect($tenantService->checkFeatureLimit($tenant, 'children'))->toBeFalse();
    });

    it('allows adding child within free plan limit via service', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Baru',
            'slug' => 'keluarga-baru',
            'is_active' => true,
        ]);

        $freePlan = Plan::create([
            'name' => 'Gratis',
            'slug' => 'free',
            'price_monthly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        $tenantService = app(TenantService::class);

        // No children yet — should be allowed
        expect($tenantService->checkFeatureLimit($tenant, 'children'))->toBeTrue();
    });

    it('blocks child creation via HTTP when limit is exceeded', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Kecil',
            'slug' => 'keluarga-kecil-http',
            'is_active' => true,
        ]);

        $freePlan = Plan::create([
            'name' => 'Gratis',
            'slug' => 'free',
            'price_monthly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        // Create one child (free plan allows max 1)
        Child::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        // Try to create a second child via HTTP — should be blocked by feature.limit middleware
        $this->actingAs($user)
            ->post(route('children.store'), [
                'name' => 'Anak Kedua',
                'gender' => 'female',
                'date_of_birth' => '2024-01-15',
            ])
            ->assertRedirect();

        // Should NOT create a second child
        expect($tenant->children()->count())->toBe(1);
    });

    it('allows child creation via HTTP within plan limit', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Baru',
            'slug' => 'keluarga-baru-http',
            'is_active' => true,
        ]);

        $freePlan = Plan::create([
            'name' => 'Gratis',
            'slug' => 'free',
            'price_monthly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        // No children yet — should be allowed
        $this->actingAs($user)
            ->post(route('children.store'), [
                'name' => 'Anak Pertama',
                'gender' => 'male',
                'date_of_birth' => '2024-01-15',
            ])
            ->assertRedirect();

        expect($tenant->children()->count())->toBe(1);
    });

    it('verifies child count matches plan limit', function () {
        $tenant = Tenant::create([
            'name' => 'TK Pintar',
            'slug' => 'tk-pintar',
            'is_active' => true,
        ]);

        $freePlan = Plan::create([
            'name' => 'Gratis',
            'slug' => 'free',
            'price_monthly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        Child::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
        ]);

        $tenantService = app(TenantService::class);

        // Limit reached — service returns false
        expect($tenantService->checkFeatureLimit($tenant, 'children'))->toBeFalse();

        // Verify the child count matches the plan limit
        expect($tenant->children()->count())->toBe($freePlan->max_children);
    });
});
