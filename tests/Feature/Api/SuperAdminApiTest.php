<?php

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;

describe('Super Admin API', function () {
    it('can list tenants as super admin', function () {
        $admin = User::factory()->superAdmin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/tenants');

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Test Tenant']);
    });

    it('cannot access admin routes as regular user', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/tenants');

        $response->assertForbidden();
    });

    it('can toggle tenant status', function () {
        $admin = User::factory()->superAdmin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $tenant = Tenant::create([
            'name' => 'Active Tenant',
            'slug' => 'active-tenant',
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/tenants/'.$tenant->id.'/toggle-status');

        $response->assertOk();

        $tenant->refresh();
        expect($tenant->is_active)->toBeFalse();
    });

    it('can list payments', function () {
        $admin = User::factory()->superAdmin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/payments');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    });

    it('can approve payment', function () {
        $admin = User::factory()->superAdmin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $tenant = Tenant::create([
            'name' => 'Payment Tenant',
            'slug' => 'payment-tenant',
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic-plan',
            'price_monthly' => 29000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_PENDING,
        ]);

        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
            'amount' => 29000,
            'currency' => 'IDR',
            'payment_method' => 'bank_transfer',
            'bank_name' => 'BRI',
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/admin/payments/'.$payment->id.'/approve');

        $response->assertOk();
    });

    it('can list plans as super admin', function () {
        $admin = User::factory()->superAdmin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        Plan::create([
            'name' => 'Admin Plan',
            'slug' => 'admin-plan',
            'price_monthly' => 49000,
            'max_children' => 5,
            'max_photos' => 500,
            'max_videos' => 100,
            'max_storage_mb' => 5120,
            'max_export_per_day' => 15,
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/plans');

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Admin Plan']);
    });

    it('can access analytics endpoint', function () {
        $admin = User::factory()->superAdmin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/analytics');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_tenants',
                    'active_tenants',
                    'revenue_this_month',
                    'revenue_total',
                ],
            ]);
    });

    it('can access monitoring endpoint', function () {
        $admin = User::factory()->superAdmin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/monitoring');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_tenants',
                    'active_tenants',
                    'total_users',
                    'database_health',
                ],
            ]);
    });
});
