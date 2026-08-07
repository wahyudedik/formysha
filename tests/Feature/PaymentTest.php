<?php

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;

describe('Payment Management', function () {
    it('allows user to view payment upload page', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Sejahtera',
            'slug' => 'keluarga-sejahtera',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

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

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->get(route('subscription.payment.upload', $subscription))
            ->assertOk();
    });

    it('stores payment with pending status after upload', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Rumah Sakit Ibu',
            'slug' => 'rs-ibu',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

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

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_PENDING,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        // Create a fake image for upload
        $proof = UploadedFile::fake()->image('bukti-transfer.jpg', 200, 200);

        $this->actingAs($user)
            ->post(route('subscription.payment.store'), [
                'subscription_id' => $subscription->id,
                'bank_name' => 'BRI',
                'amount' => 59000,
                'proof' => $proof,
            ])
            ->assertRedirect(route('subscription.current'));

        $this->assertDatabaseHas('payments', [
            'subscription_id' => $subscription->id,
            'tenant_id' => $tenant->id,
            'amount' => 59000,
            'bank_name' => 'BRI',
            'status' => Payment::STATUS_PENDING,
        ]);
    });

    it('allows super admin to view payment list', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.payments.index'))
            ->assertOk();
    });

    it('allows super admin to approve a payment', function () {
        $admin = User::factory()->superAdmin()->create();
        $tenant = Tenant::create([
            'name' => 'TK Pelita',
            'slug' => 'tk-pelita',
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
            'bank_name' => 'BRI',
            'status' => Payment::STATUS_PENDING,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('super-admin.payments.approve', $payment))
            ->assertRedirect(route('super-admin.payments.index'));

        $payment->refresh();
        expect($payment->status)->toBe(Payment::STATUS_APPROVED);
        expect($payment->verified_by)->toBe($admin->id);
        expect($payment->verified_at)->not->toBeNull();

        $subscription->refresh();
        expect($subscription->status)->toBe(Subscription::STATUS_ACTIVE);
    });

    it('allows super admin to reject a payment with reason', function () {
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

        $this->actingAs($admin)
            ->post(route('super-admin.payments.reject', $payment), [
                'notes' => 'Bukti transfer tidak jelas',
            ])
            ->assertRedirect(route('super-admin.payments.index'));

        $payment->refresh();
        expect($payment->status)->toBe(Payment::STATUS_REJECTED);
        expect($payment->notes)->toBe('Bukti transfer tidak jelas');

        $subscription->refresh();
        expect($subscription->status)->toBe(Subscription::STATUS_INACTIVE);
    });

    it('prevents non-super admin from accessing payment verification routes', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('super-admin.payments.index'))
            ->assertForbidden();
    });

    it('prevents tenant admin from accessing payment verification routes', function () {
        $admin = User::factory()->tenantAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.payments.index'))
            ->assertForbidden();
    });
});
