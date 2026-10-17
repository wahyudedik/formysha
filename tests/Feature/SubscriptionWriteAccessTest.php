<?php

use App\Models\Child;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Timeline;
use App\Models\User;

/**
 * Helper: create a B2C tenant with a user and a subscription whose status
 * (and optionally "ends_at") can be controlled per test.
 *
 * @return array{tenant: Tenant, plan: Plan, subscription: Subscription, user: User}
 */
function createWriteAccessTenant(string $subscriptionStatus = Subscription::STATUS_ACTIVE, array $subscriptionOverrides = [], array $planOverrides = []): array
{
    $tenant = Tenant::create([
        'name' => 'Keluarga Write '.fake()->unique()->bothify('####'),
        'slug' => 'keluarga-write-'.fake()->unique()->bothify('####'),
        'is_active' => true,
    ]);

    $plan = Plan::create(array_merge([
        'name' => 'Paket Write '.fake()->unique()->bothify('####'),
        'slug' => 'paket-write-'.fake()->unique()->bothify('####'),
        'price_monthly' => 0,
        'max_children' => -1,
        'max_photos' => -1,
        'max_videos' => -1,
        'max_storage_mb' => -1,
        'max_family_members' => -1,
        'is_active' => true,
        'sort_order' => 99,
    ], $planOverrides));

    $subscription = Subscription::create(array_merge([
        'tenant_id' => $tenant->id,
        'plan_id' => $plan->id,
        'status' => $subscriptionStatus,
        'starts_at' => now()->subMonth(),
        'ends_at' => null,
    ], $subscriptionOverrides));

    $user = User::factory()->create(['tenant_id' => $tenant->id]);

    return ['tenant' => $tenant, 'plan' => $plan, 'subscription' => $subscription, 'user' => $user];
}

/**
 * Enable write-access enforcement explicitly for a test. The phpunit.xml
 * default (SAAS_ENFORCE_WRITE_ACCESS=false) keeps the existing suite green.
 */
function enableWriteAccessEnforcement(): void
{
    config()->set(['saas.subscription.enforce_write_access' => true]);
}

describe('Subscription Write Access — Web Read Always Allowed', function () {
    beforeEach(fn () => enableWriteAccessEnforcement());

    it('allows reading children index when subscription is inactive', function () {
        ['user' => $user, 'tenant' => $tenant] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);

        $this->actingAs($user)
            ->get(route('children.index'))
            ->assertOk();
    });

    it('allows reading timeline index when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->get(route('timeline.index', $child))
            ->assertOk();
    });

    it('allows exporting child profile when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->get(route('export.profile', $child))
            ->assertOk();
    });
});

describe('Subscription Write Access — Web Writes Blocked', function () {
    beforeEach(fn () => enableWriteAccessEnforcement());

    it('redirects timeline store to plans when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->post(route('timeline.store', $child), [
                'title' => 'Hari Pertama Sekolah',
                'event_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('subscription.plans'))
            ->assertSessionHas('subscription_notice');

        $this->assertDatabaseMissing('timelines', ['title' => 'Hari Pertama Sekolah']);
    });

    it('redirects child create (store) to plans when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);

        $this->actingAs($user)
            ->post(route('children.store'), [
                'name' => 'Qaireen Ahmad',
                'nickname' => 'Qai',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
            ])
            ->assertRedirect(route('subscription.plans'))
            ->assertSessionHas('subscription_notice');

        $this->assertDatabaseMissing('children', ['name' => 'Qaireen Ahmad']);
    });

    it('redirects child update (PUT) to plans when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id, 'name' => 'Nama Lama']);

        $this->actingAs($user)
            ->put(route('children.update', $child), [
                'name' => 'Nama Baru',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
            ])
            ->assertRedirect(route('subscription.plans'))
            ->assertSessionHas('subscription_notice');

        $this->assertDatabaseHas('children', ['id' => $child->id, 'name' => 'Nama Lama']);
    });

    it('redirects child delete (DELETE) to plans when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->delete(route('children.destroy', $child))
            ->assertRedirect(route('subscription.plans'))
            ->assertSessionHas('subscription_notice');

        $this->assertDatabaseHas('children', ['id' => $child->id]);
    });

    it('redirects timeline update and delete to plans when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'title' => 'Judul Asli']);

        $this->actingAs($user)
            ->put(route('timeline.update', [$child, $timeline]), [
                'title' => 'Judul Baru',
                'event_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('subscription.plans'));

        $this->actingAs($user)
            ->delete(route('timeline.destroy', [$child, $timeline]))
            ->assertRedirect(route('subscription.plans'));

        $this->assertDatabaseHas('timelines', ['id' => $timeline->id, 'title' => 'Judul Asli']);
    });

    it('blocks writes when subscription is past_due', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_PAST_DUE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->post(route('timeline.store', $child), ['title' => 'Tes', 'event_date' => now()->toDateString()])
            ->assertRedirect(route('subscription.plans'));
    });

    it('blocks writes when subscription is pending (awaiting payment)', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_PENDING);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->post(route('timeline.store', $child), ['title' => 'Tes', 'event_date' => now()->toDateString()])
            ->assertRedirect(route('subscription.plans'));
    });

    it('blocks writes when active subscription has already ended', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_ACTIVE, ['ends_at' => now()->subDay()]);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->post(route('timeline.store', $child), ['title' => 'Tes', 'event_date' => now()->toDateString()])
            ->assertRedirect(route('subscription.plans'));
    });
});

describe('Subscription Write Access — Exempt Web Routes', function () {
    beforeEach(fn () => enableWriteAccessEnforcement());

    it('still allows visiting plans and uploading payment proof when inactive', function () {
        ['user' => $user, 'subscription' => $subscription] = createWriteAccessTenant(Subscription::STATUS_PENDING);

        $this->actingAs($user)
            ->get(route('subscription.plans'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('subscription.payment.upload', $subscription))
            ->assertOk();
    });

    it('still allows subscribing to a plan when tenant has no active subscription', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);

        $paidPlan = Plan::create([
            'name' => 'Paket Bayar '.fake()->unique()->bothify('####'),
            'slug' => 'paket-bayar-'.fake()->unique()->bothify('####'),
            'price_monthly' => 29000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_family_members' => 10,
            'is_active' => true,
            'sort_order' => 98,
        ]);

        $this->actingAs($user)
            ->post(route('subscription.subscribe', $paidPlan))
            ->assertRedirect(route('subscription.payment.upload', Subscription::where('plan_id', $paidPlan->id)->firstOrFail()));

        $this->assertDatabaseHas('subscriptions', [
            'tenant_id' => $user->tenant_id,
            'plan_id' => $paidPlan->id,
            'status' => Subscription::STATUS_PENDING,
        ]);
    });

    it('still allows profile update when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name]);
    });

    it('still allows marking notifications as read when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $notification = Notification::factory()->create(['user_id' => $user->id, 'read_at' => null]);

        $this->actingAs($user)
            ->post(route('notifications.markAsRead', $notification))
            ->assertRedirect();

        $this->assertNotNull($notification->refresh()->read_at);
    });

    it('still allows logout when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    });

    it('still allows child erasure (compliance) when subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyChild', $child), ['password' => 'password'])
            ->assertRedirect(route('erasure.index'));

        $this->assertDatabaseMissing('children', ['id' => $child->id]);
    });
});

describe('Subscription Write Access — API', function () {
    beforeEach(fn () => enableWriteAccessEnforcement());

    it('returns 402 JSON when creating a child via API while subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);

        $response = $this->actingAs($user)->postJson(route('api.children.store'), [
            'name' => 'Qaireen Ahmad',
            'gender' => 'male',
            'date_of_birth' => '2025-01-20',
        ]);

        $response->assertStatus(402)
            ->assertJson(['success' => false]);

        $this->assertDatabaseMissing('children', ['name' => 'Qaireen Ahmad']);
    });

    it('still allows listing children via API while subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->getJson(route('api.children.index'))
            ->assertOk()
            ->assertJson(['success' => true]);
    });

    it('returns 402 JSON for timeline writes via API while subscription is inactive', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->postJson(route('api.timelines.store', $child), [
                'title' => 'Kenangan',
                'event_date' => now()->toDateString(),
            ])
            ->assertStatus(402)
            ->assertJson(['success' => false]);
    });

    it('allows API writes when subscription is active (regression)', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_ACTIVE);

        $this->actingAs($user)
            ->postJson(route('api.children.store'), [
                'name' => 'Qaireen Ahmad',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('children', ['name' => 'Qaireen Ahmad']);
    });
});

describe('Subscription Write Access — Active & Free Plan Regression', function () {
    beforeEach(fn () => enableWriteAccessEnforcement());

    it('allows child creation when subscription is active', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_ACTIVE);

        $this->actingAs($user)
            ->post(route('children.store'), [
                'name' => 'Qaireen Ahmad',
                'nickname' => 'Qai',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('children', ['name' => 'Qaireen Ahmad']);
    });

    it('allows timeline creation when subscription is active', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_ACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        $this->actingAs($user)
            ->post(route('timeline.store', $child), [
                'title' => 'Hari Pertama Sekolah',
                'event_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('timeline.index', $child));

        $this->assertDatabaseHas('timelines', ['child_id' => $child->id, 'title' => 'Hari Pertama Sekolah']);
    });

    it('allows writes with an active free plan (is_free)', function () {
        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_ACTIVE, [], ['is_free' => true, 'price_monthly' => 0]);

        $this->actingAs($user)
            ->post(route('children.store'), [
                'name' => 'Qaireen Ahmad',
                'nickname' => 'Qai',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('children', ['name' => 'Qaireen Ahmad']);
    });

    it('allows writes for super admin without any subscription', function () {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('children.store'), [
                'name' => 'Qaireen Ahmad',
                'nickname' => 'Qai',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('children', ['name' => 'Qaireen Ahmad']);
    });

    it('does not block writes when enforcement is disabled (phpunit default)', function () {
        // Explicitly disable — the beforeEach above enables enforcement for this describe
        config()->set(['saas.subscription.enforce_write_access' => false]);

        ['user' => $user] = createWriteAccessTenant(Subscription::STATUS_INACTIVE);
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $user->tenant_id]);

        expect(config('saas.subscription.enforce_write_access'))->toBeFalse();

        $this->actingAs($user)
            ->post(route('timeline.store', $child), [
                'title' => 'Tetap Bisa Menulis',
                'event_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('timeline.index', $child));
    });
});

describe('Suspended Tenant Enforcement', function () {
    it('aborts with 403 on web when tenant is suspended', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Suspend '.fake()->unique()->bothify('####'),
            'slug' => 'keluarga-suspend-'.fake()->unique()->bothify('####'),
            'is_active' => false,
        ]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user)
            ->get(route('children.index'))
            ->assertForbidden();
    });

    it('returns 403 JSON when tenant is suspended (API)', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Suspend '.fake()->unique()->bothify('####'),
            'slug' => 'keluarga-suspend-'.fake()->unique()->bothify('####'),
            'is_active' => false,
        ]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user)
            ->getJson(route('api.children.index'))
            ->assertStatus(403)
            ->assertJson(['success' => false]);
    });

    it('allows super admin even when their tenant is suspended', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Suspend '.fake()->unique()->bothify('####'),
            'slug' => 'keluarga-suspend-'.fake()->unique()->bothify('####'),
            'is_active' => false,
        ]);
        $superAdmin = User::factory()->superAdmin()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($superAdmin)
            ->get(route('children.index'))
            ->assertOk();
    });

    it('allows users without a tenant', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('children.index'))
            ->assertOk();
    });
});
