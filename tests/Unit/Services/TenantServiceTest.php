<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;

describe('TenantService', function () {
    it('creates tenant with owner and default subscription', function () {
        $service = app(TenantService::class);
        $owner = User::factory()->create();

        // Seed free plan
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

        $tenant = $service->createTenant([
            'name' => 'Klinik Sehat',
        ], $owner);

        // Tenant was created
        expect($tenant)->not->toBeNull();
        expect($tenant->name)->toBe('Klinik Sehat');
        expect($tenant->is_active)->toBeTrue();

        // Owner was assigned as tenant_admin
        $owner->refresh();
        expect($owner->tenant_id)->toBe($tenant->id);
        expect($owner->role)->toBe('tenant_admin');

        // Free subscription was created and activated
        $subscription = Subscription::where('tenant_id', $tenant->id)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->first();
        expect($subscription)->not->toBeNull();
        expect($subscription->status)->toBe(Subscription::STATUS_ACTIVE);
        expect($subscription->plan_id)->toBe($freePlan->id);
    });

    it('returns correct tenant usage counts', function () {
        $service = app(TenantService::class);

        $tenant = Tenant::create([
            'name' => 'TK Melati',
            'slug' => 'tk-melati',
            'is_active' => true,
        ]);

        $usage = $service->getTenantUsage($tenant);

        expect($usage['children'])->toBe(0);
        expect($usage['photos'])->toBe(0);
        expect($usage['videos'])->toBe(0);
        expect($usage['storage_used'])->toBe(0);
    });

    it('checks feature limit correctly', function () {
        $service = app(TenantService::class);

        $tenant = Tenant::create([
            'name' => 'Daycare Ceria',
            'slug' => 'daycare-ceria',
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
            'ends_at' => now()->addCentury(),
        ]);

        // No children — can add
        expect($service->checkFeatureLimit($tenant, 'children'))->toBeTrue();

        // Unknown feature — always allowed
        expect($service->checkFeatureLimit($tenant, 'unknown_feature'))->toBeTrue();
    });
});
