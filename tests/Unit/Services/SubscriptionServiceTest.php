<?php

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SubscriptionService;

describe('SubscriptionService', function () {
    it('approves payment and activates subscription', function () {
        $service = app(SubscriptionService::class);
        $admin = User::factory()->superAdmin()->create();

        $tenant = Tenant::create([
            'name' => 'Klinik Sehat',
            'slug' => 'klinik-sehat',
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'price_monthly' => 29000,
            'price_yearly' => 290000,
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
            'subscription_id' => $subscription->id,
            'tenant_id' => $tenant->id,
            'amount' => 29000,
            'currency' => 'IDR',
            'payment_method' => Payment::METHOD_BANK_TRANSFER,
            'bank_name' => 'BRI',
            'status' => Payment::STATUS_PENDING,
            'paid_at' => now(),
        ]);

        $service->approvePayment($payment, $admin);

        $payment->refresh();
        expect($payment->status)->toBe(Payment::STATUS_APPROVED);
        expect($payment->verified_by)->toBe($admin->id);

        $subscription->refresh();
        expect($subscription->status)->toBe(Subscription::STATUS_ACTIVE);
        expect($subscription->starts_at)->not->toBeNull();
        expect($subscription->ends_at)->not->toBeNull();
    });

    it('rejects payment and keeps subscription inactive', function () {
        $service = app(SubscriptionService::class);
        $admin = User::factory()->superAdmin()->create();

        $tenant = Tenant::create([
            'name' => 'Daycare Aman',
            'slug' => 'daycare-aman',
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
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
            'subscription_id' => $subscription->id,
            'tenant_id' => $tenant->id,
            'amount' => 29000,
            'currency' => 'IDR',
            'payment_method' => Payment::METHOD_BANK_TRANSFER,
            'bank_name' => 'JAGO',
            'status' => Payment::STATUS_PENDING,
            'paid_at' => now(),
        ]);

        $service->rejectPayment($payment, $admin, 'Bukti tidak valid');

        $payment->refresh();
        expect($payment->status)->toBe(Payment::STATUS_REJECTED);
        expect($payment->notes)->toBe('Bukti tidak valid');

        $subscription->refresh();
        expect($subscription->status)->toBe(Subscription::STATUS_INACTIVE);
    });

    it('returns correct active subscription', function () {
        $service = app(SubscriptionService::class);

        $tenant = Tenant::create([
            'name' => 'TK Pelita',
            'slug' => 'tk-pelita',
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'price_monthly' => 59000,
            'max_children' => 10,
            'max_photos' => 1000,
            'max_videos' => 200,
            'max_storage_mb' => 10240,
            'max_export_per_day' => 50,
            'is_active' => true,
        ]);

        // No active subscription yet
        expect($service->getActiveSubscription($tenant))->toBeNull();

        // Create active subscription
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        // Refresh tenant to clear cached relationship
        $tenant->refresh();

        $active = $service->getActiveSubscription($tenant);
        expect($active)->not->toBeNull();
        expect($active->status)->toBe(Subscription::STATUS_ACTIVE);
    });

    it('returns correct boolean for subscription active check', function () {
        $service = app(SubscriptionService::class);

        $tenant = Tenant::create([
            'name' => 'PAUD Ceria',
            'slug' => 'paud-ceria',
            'is_active' => true,
        ]);

        expect($service->isSubscriptionActive($tenant))->toBeFalse();

        $plan = Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'price_monthly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        expect($service->isSubscriptionActive($tenant))->toBeTrue();
    });
});
