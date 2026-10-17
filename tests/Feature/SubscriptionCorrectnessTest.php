<?php

use App\Enums\TenantType;
use App\Mail\SubscriptionReminder;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

/**
 * Regresi audit SUBTASK I2 — Bug A sampai E.
 *
 * Bug A: plan price_monthly=0 tapi bukan gratis tidak boleh mengaktifkan
 *        paket free (dan tidak boleh men-downgrade paket berbayar aktif).
 * Bug B: upload bukti bayar tidak boleh lintas tenant, subscription harus
 *        pending, dan amount minimal = harga plan.
 * Bug C: API subscribe harus punya cabang free plan, dedup pending, dan
 *        guard audience tenant ↔ plan.
 * Bug D: command reminder harus benar-benar mengirim email.
 * Bug E: grace period diambil dari config (dilindungi lewat config test).
 */
describe('Subscription Correctness — Bug A: custom pricing & free plan', function () {
    it('rejects custom pricing plan and keeps existing paid subscription active', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Bug A',
            'slug' => 'keluarga-bug-a',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $paidPlan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-paid',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        // Subscription berbayar sedang aktif.
        $activeSubscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $paidPlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        // Plan custom pricing: harga 0 tetapi bukan plan gratis.
        $customPlan = Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise-custom',
            'price_monthly' => 0,
            'is_free' => false,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => -1,
            'max_export_per_day' => -1,
            'is_active' => true,
            'sort_order' => 99,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->post(route('subscription.subscribe', $customPlan))
            ->assertRedirect(route('subscription.plans'));

        // Tidak ada subscription baru untuk plan custom.
        $this->assertDatabaseMissing('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $customPlan->id,
        ]);

        // Subscription berbayar yang aktif TIDAK boleh ter-downgrade/dibatalkan.
        $activeSubscription->refresh();
        expect($activeSubscription->status)->toBe(Subscription::STATUS_ACTIVE);
    });

    it('activates the clicked free plan (is_free=true) even when slug is not "free"', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Free Clicked',
            'slug' => 'keluarga-free-clicked',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        // Plan gratis dengan slug non-standar — membuktikan aktivasi
        // berbasis flag is_free, bukan hardcode slug 'free'.
        $freePlan = Plan::create([
            'name' => 'Gratis Spesial',
            'slug' => 'gratis-spesial',
            'price_monthly' => 0,
            'is_free' => true,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->post(route('subscription.subscribe', $freePlan))
            ->assertRedirect(route('subscription.current'));

        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    });
});

describe('Subscription Correctness — Bug C: API free plan, audience & dedup', function () {
    it('activates free plan immediately via API subscribe', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'API Family',
            'slug' => 'api-family-free',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $freePlan = Plan::create([
            'name' => 'Gratis',
            'slug' => 'free-api',
            'price_monthly' => 0,
            'is_free' => true,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
            'is_active' => true,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $this->postJson(route('api.subscription.subscribe', $freePlan), [], [
            'Authorization' => 'Bearer '.$token,
        ])->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    });

    it('rejects family tenant subscribing to a facility (B2B) plan', function () {
        $user = User::factory()->create();
        // Tenant tanpa type → default 'family' (B2C).
        $tenant = Tenant::create([
            'name' => 'Keluarga B2C',
            'slug' => 'keluarga-b2c-guard',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $facilityPlan = Plan::create([
            'name' => 'B2B Basic',
            'slug' => 'b2b-basic-test',
            'price_monthly' => 299000,
            'audience' => Plan::AUDIENCE_FACILITY,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => -1,
            'max_export_per_day' => -1,
            'is_active' => true,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->post(route('subscription.subscribe', $facilityPlan))
            ->assertRedirect(route('subscription.plans'));

        $this->assertDatabaseMissing('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $facilityPlan->id,
        ]);
    });

    it('reuses existing pending subscription when subscribing to the same plan again', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Dedup',
            'slug' => 'keluarga-dedup',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $plan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-dedup',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)->post(route('subscription.subscribe', $plan));
        $this->actingAs($user)->post(route('subscription.subscribe', $plan));

        // Tidak boleh ada duplikasi pending subscription untuk plan yang sama.
        expect(Subscription::where('tenant_id', $tenant->id)
            ->where('plan_id', $plan->id)
            ->where('status', Subscription::STATUS_PENDING)->count())->toBe(1);
    });

    it('cancels previous pending subscription when switching to a different plan', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Switch',
            'slug' => 'keluarga-switch',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $planA = Plan::create([
            'name' => 'Family',
            'slug' => 'family-switch',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        $planB = Plan::create([
            'name' => 'Family Plus',
            'slug' => 'family-plus-switch',
            'price_monthly' => 39000,
            'max_children' => 5,
            'max_photos' => 500,
            'max_videos' => 100,
            'max_storage_mb' => 5120,
            'max_export_per_day' => 20,
            'is_active' => true,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)->post(route('subscription.subscribe', $planA));
        $this->actingAs($user)->post(route('subscription.subscribe', $planB));

        // Pending lama (plan A) harus dibatalkan.
        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $planA->id,
            'status' => Subscription::STATUS_CANCELLED,
        ]);

        // Pending baru (plan B) harus berstatus pending.
        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $planB->id,
            'status' => Subscription::STATUS_PENDING,
        ]);
    });
});

describe('Subscription Correctness — Bug B: payment proof guards', function () {
    it('forbids API payment upload for another tenant subscription', function () {
        $userA = User::factory()->create();
        $tenantA = Tenant::create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a-cross',
            'is_active' => true,
        ]);
        $userA->update(['tenant_id' => $tenantA->id]);

        $tenantB = Tenant::create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b-cross',
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-cross',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        // Subscription milik tenant B.
        $subscriptionB = Subscription::create([
            'tenant_id' => $tenantB->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_PENDING,
        ]);

        $token = $userA->createToken('test-token')->plainTextToken;

        $this->postJson(route('api.subscription.payment'), [
            'subscription_id' => $subscriptionB->id,
            'bank_name' => 'BRI',
            'amount' => 19000,
            'proof' => UploadedFile::fake()->image('bukti.jpg', 200, 200),
        ], [
            'Authorization' => 'Bearer '.$token,
        ])->assertForbidden();

        $this->assertDatabaseMissing('payments', [
            'subscription_id' => $subscriptionB->id,
        ]);
    });

    it('rejects payment proof for non-pending subscription', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga NonPending',
            'slug' => 'keluarga-nonpending',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $plan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-nonpending',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        // Subscription sudah aktif (bukan pending).
        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->post(route('subscription.payment.store'), [
                'subscription_id' => $subscription->id,
                'bank_name' => 'BRI',
                'amount' => 19000,
                'proof' => UploadedFile::fake()->image('bukti.jpg', 200, 200),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('payments', [
            'subscription_id' => $subscription->id,
        ]);
    });

    it('rejects payment proof with amount below plan price', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Kurang Bayar',
            'slug' => 'keluarga-kurang-bayar',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $plan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-underpay',
            'price_monthly' => 19000,
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
            ->post(route('subscription.payment.store'), [
                'subscription_id' => $subscription->id,
                'bank_name' => 'BRI',
                'amount' => 10000, // kurang dari 19000
                'proof' => UploadedFile::fake()->image('bukti.jpg', 200, 200),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('payments', [
            'subscription_id' => $subscription->id,
        ]);
    });

    it('cancels other pending subscriptions after payment approval', function () {
        $admin = User::factory()->superAdmin()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Approve Cleanup',
            'slug' => 'keluarga-approve-cleanup',
            'is_active' => true,
        ]);

        $planA = Plan::create([
            'name' => 'Family',
            'slug' => 'family-approve-a',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        $planB = Plan::create([
            'name' => 'Family Plus',
            'slug' => 'family-approve-b',
            'price_monthly' => 39000,
            'max_children' => 5,
            'max_photos' => 500,
            'max_videos' => 100,
            'max_storage_mb' => 5120,
            'max_export_per_day' => 20,
            'is_active' => true,
        ]);

        $subA = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $planA->id,
            'status' => Subscription::STATUS_PENDING,
        ]);

        // Pending kedua untuk plan berbeda — harus dibersihkan saat approve.
        $subB = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $planB->id,
            'status' => Subscription::STATUS_PENDING,
        ]);

        $payment = Payment::create([
            'subscription_id' => $subA->id,
            'tenant_id' => $tenant->id,
            'amount' => 19000,
            'currency' => 'IDR',
            'payment_method' => Payment::METHOD_BANK_TRANSFER,
            'bank_name' => 'BRI',
            'status' => Payment::STATUS_PENDING,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('super-admin.payments.approve', $payment))
            ->assertRedirect(route('super-admin.payments.index'));

        $subA->refresh();
        $subB->refresh();

        expect($subA->status)->toBe(Subscription::STATUS_ACTIVE);
        expect($subB->status)->toBe(Subscription::STATUS_CANCELLED);
    });
});

describe('Subscription Correctness — Bug D: reminder email', function () {
    it('sends renewal reminder email to tenant admin', function () {
        Mail::fake();

        $tenant = Tenant::create([
            'name' => 'Keluarga Pengingat',
            'slug' => 'keluarga-pengingat',
            'is_active' => true,
        ]);

        $admin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $tenant->id,
            'email' => 'admin-pengingat@example.com',
        ]);

        $plan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-reminder',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        // Subscription aktif akan berakhir 3 hari lagi (di dalam jendela reminder).
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addDays(3),
        ]);

        $this->artisan('subscription:send-reminders')->assertSuccessful();

        Mail::assertSent(SubscriptionReminder::class, function (SubscriptionReminder $mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    });

    it('does not send reminder for subscriptions outside the reminder window', function () {
        Mail::fake();

        $tenant = Tenant::create([
            'name' => 'Keluarga Jauh',
            'slug' => 'keluarga-jauh',
            'is_active' => true,
        ]);

        User::factory()->tenantAdmin()->create([
            'tenant_id' => $tenant->id,
            'email' => 'admin-jauh@example.com',
        ]);

        $plan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-reminder-far',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        // Berakhir 30 hari lagi — di luar jendela reminder (7 hari).
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->artisan('subscription:send-reminders')->assertSuccessful();

        Mail::assertNotSent(SubscriptionReminder::class);
    });
});

describe('Subscription Correctness — Bug E: grace period from config', function () {
    it('uses configured grace period when expiring subscriptions', function () {
        // Set grace period ke 10 hari untuk membuktikan nilai diambil
        // dari config, bukan hardcode.
        config(['saas.subscription.grace_period_days' => 10]);

        $tenant = Tenant::create([
            'name' => 'Keluarga Grace',
            'slug' => 'keluarga-grace',
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Family',
            'slug' => 'family-grace',
            'price_monthly' => 19000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        // Lewat grace period (12 hari lalu > 10 hari grace) → inactive.
        $overdue = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDays(12),
        ]);

        // Lewat 8 hari — melebihi grace default 7 tetapi masih di dalam
        // grace 10 dari config. Jika grace masih hardcode 7, subscription
        // ini akan ikut inactive. Dengan config 10 → tetap past_due.
        $withinGrace = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDays(8),
        ]);

        $this->artisan('subscription:check-expired')->assertSuccessful();

        $overdue->refresh();
        $withinGrace->refresh();

        expect($overdue->status)->toBe(Subscription::STATUS_INACTIVE);
        expect($withinGrace->status)->toBe(Subscription::STATUS_PAST_DUE);
    });

    it('does not activate enterprise custom pricing plan via API', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Tenant B2B Enterprise',
            'slug' => 'tenant-b2b-enterprise',
            'type' => TenantType::Hospital->value,
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $enterprisePlan = Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise-api',
            'price_monthly' => 0,
            'is_free' => false,
            'audience' => Plan::AUDIENCE_FACILITY,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => -1,
            'max_export_per_day' => -1,
            'is_active' => true,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson(route('api.subscription.subscribe', $enterprisePlan), [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(422);

        // Tidak ada subscription yang dibuat/diaktifkan.
        $this->assertDatabaseMissing('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $enterprisePlan->id,
        ]);
    });
});
