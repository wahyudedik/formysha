<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;

describe('Subscription Management', function () {
    it('allows authenticated user to view plans page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('subscription.plans'))
            ->assertOk();
    });

    it('redirects to payment upload after subscribing to paid plan', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Bahagia',
            'slug' => 'keluarga-bahagia',
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

        // Set tenant in session
        $this->app['session']->put('tenant_id', $tenant->id);

        $response = $this->actingAs($user)
            ->post(route('subscription.subscribe', $plan));

        // Paid plan should redirect to payment upload page
        $subscription = Subscription::where('tenant_id', $tenant->id)->first();
        $response->assertRedirect(route('subscription.payment.upload', $subscription));

        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_PENDING,
        ]);
    });

    it('activates free plan immediately without payment', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Keluarga Gratis',
            'slug' => 'keluarga-gratis',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

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

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->post(route('subscription.subscribe', $freePlan))
            ->assertRedirect(route('subscription.current'));

        // Free plan should be active immediately, not pending
        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    });

    it('creates subscription with pending status after subscribing', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'Daycare Happy',
            'slug' => 'daycare-happy',
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

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->post(route('subscription.subscribe', $plan));

        $subscription = Subscription::where('tenant_id', $tenant->id)->first();
        expect($subscription)->not->toBeNull();
        expect($subscription->status)->toBe(Subscription::STATUS_PENDING);
    });

    it('allows user to view current subscription', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'TK Melati',
            'slug' => 'tk-melati',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->get(route('subscription.current'))
            ->assertOk();
    });

    it('allows user to view subscription history', function () {
        $user = User::factory()->create();
        $tenant = Tenant::create([
            'name' => 'PAUD Ceria',
            'slug' => 'paud-ceria',
            'is_active' => true,
        ]);
        $user->update(['tenant_id' => $tenant->id]);

        $this->app['session']->put('tenant_id', $tenant->id);

        $this->actingAs($user)
            ->get(route('subscription.history'))
            ->assertOk();
    });
});
