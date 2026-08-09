<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;

describe('Subscription Lifecycle Commands', function () {
    describe('CheckExpiredSubscriptions', function () {
        it('expires overdue active subscriptions', function () {
            $tenant = Tenant::create([
                'name' => 'Test Tenant',
                'slug' => 'test-sub-lifecycle',
                'is_active' => true,
            ]);
            $plan = Plan::create([
                'name' => 'Basic',
                'slug' => 'basic',
                'price_monthly' => 50000,
                'max_children' => 3,
                'max_photos' => 100,
                'max_videos' => 10,
                'max_storage_mb' => 1024,
                'is_active' => true,
            ]);

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->subDay(),
            ]);

            $this->artisan('subscription:check-expired')
                ->assertExitCode(0);

            $subscription->refresh();
            expect($subscription->status)->toBe(Subscription::STATUS_PAST_DUE);
        });

        it('does not expire active subscriptions with future end date', function () {
            $tenant = Tenant::create([
                'name' => 'Future Tenant',
                'slug' => 'future-tenant',
                'is_active' => true,
            ]);
            $plan = Plan::create([
                'name' => 'Premium',
                'slug' => 'premium',
                'price_monthly' => 100000,
                'max_children' => 10,
                'max_photos' => 500,
                'max_videos' => 50,
                'max_storage_mb' => 5120,
                'is_active' => true,
            ]);

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);

            $this->artisan('subscription:check-expired')
                ->assertExitCode(0);

            $subscription->refresh();
            expect($subscription->status)->toBe(Subscription::STATUS_ACTIVE);
        });

        it('deactivates past_due subscriptions after grace period', function () {
            $tenant = Tenant::create([
                'name' => 'Grace Tenant',
                'slug' => 'grace-tenant',
                'is_active' => true,
            ]);
            $plan = Plan::create([
                'name' => 'Basic',
                'slug' => 'basic-grace',
                'price_monthly' => 50000,
                'max_children' => 3,
                'max_photos' => 100,
                'max_videos' => 10,
                'max_storage_mb' => 1024,
                'is_active' => true,
            ]);

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_PAST_DUE,
                'starts_at' => now()->subMonths(2),
                'ends_at' => now()->subDays(8),
            ]);

            $this->artisan('subscription:check-expired')
                ->assertExitCode(0);

            $subscription->refresh();
            expect($subscription->status)->toBe(Subscription::STATUS_INACTIVE);
        });

        it('does not deactivate past_due within grace period', function () {
            $tenant = Tenant::create([
                'name' => 'Within Grace',
                'slug' => 'within-grace',
                'is_active' => true,
            ]);
            $plan = Plan::create([
                'name' => 'Basic',
                'slug' => 'basic-within-grace',
                'price_monthly' => 50000,
                'max_children' => 3,
                'max_photos' => 100,
                'max_videos' => 10,
                'max_storage_mb' => 1024,
                'is_active' => true,
            ]);

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_PAST_DUE,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->subDays(3),
            ]);

            $this->artisan('subscription:check-expired')
                ->assertExitCode(0);

            $subscription->refresh();
            expect($subscription->status)->toBe(Subscription::STATUS_PAST_DUE);
        });
    });

    describe('SendSubscriptionReminders', function () {
        it('runs successfully with no subscriptions to remind', function () {
            $this->artisan('subscription:send-reminders')
                ->assertExitCode(0);
        });

        it('runs successfully with subscriptions expiring within 7 days', function () {
            $tenant = Tenant::create([
                'name' => 'Reminder Tenant',
                'slug' => 'reminder-tenant',
                'is_active' => true,
            ]);
            $plan = Plan::create([
                'name' => 'Basic',
                'slug' => 'basic-reminder',
                'price_monthly' => 50000,
                'max_children' => 3,
                'max_photos' => 100,
                'max_videos' => 10,
                'max_storage_mb' => 1024,
                'is_active' => true,
            ]);
            User::factory()->create([
                'tenant_id' => $tenant->id,
                'role' => 'tenant_admin',
            ]);

            Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addDays(5),
            ]);

            $this->artisan('subscription:send-reminders')
                ->assertExitCode(0);
        });
    });
});
