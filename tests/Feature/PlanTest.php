<?php

use App\Models\Plan;
use App\Models\User;
use Database\Seeders\PlanSeeder;

describe('Plan Management (Super Admin)', function () {
    it('allows super admin to view plan list', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.plans.index'))
            ->assertOk();
    });

    it('allows super admin to create a new plan', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('super-admin.plans.store'), [
                'name' => 'Starter',
                'slug' => 'starter',
                'price_monthly' => 15000,
                'max_children' => 2,
                'max_photos' => 100,
                'max_videos' => 25,
                'max_storage_mb' => 1024,
                'max_export_per_day' => 5,
            ])
            ->assertRedirect(route('super-admin.plans.index'));

        $this->assertDatabaseHas('plans', [
            'name' => 'Starter',
            'slug' => 'starter',
            'price_monthly' => 15000,
        ]);
    });

    it('allows super admin to edit a plan', function () {
        $admin = User::factory()->superAdmin()->create();
        $plan = Plan::create([
            'name' => 'Old Plan',
            'slug' => 'old-plan',
            'price_monthly' => 10000,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_export_per_day' => 3,
        ]);

        $this->actingAs($admin)
            ->put(route('super-admin.plans.update', $plan), [
                'name' => 'Updated Plan',
                'price_monthly' => 20000,
            ])
            ->assertRedirect(route('super-admin.plans.index'));

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'price_monthly' => 20000,
        ]);
    });

    it('allows super admin to delete a plan', function () {
        $admin = User::factory()->superAdmin()->create();
        $plan = Plan::create([
            'name' => 'Disposable Plan',
            'slug' => 'disposable',
            'price_monthly' => 5000,
            'max_children' => 1,
            'max_photos' => 10,
            'max_videos' => 5,
            'max_storage_mb' => 100,
            'max_export_per_day' => 1,
        ]);

        $this->actingAs($admin)
            ->delete(route('super-admin.plans.destroy', $plan))
            ->assertRedirect(route('super-admin.plans.index'));

        $this->assertSoftDeleted('plans', ['id' => $plan->id]);
    });

    it('has correct number of seeded plans', function () {
        // Run the seeder
        (new PlanSeeder)->run();

        // B2C plans: free, family, family-plus, family-pro
        // B2B plans: b2b-basic, b2b-growth, b2b-pro, enterprise
        expect(Plan::count())->toBe(8);
        expect(Plan::where('slug', 'free')->exists())->toBeTrue();
        expect(Plan::where('slug', 'family')->exists())->toBeTrue();
        expect(Plan::where('slug', 'family-plus')->exists())->toBeTrue();
        expect(Plan::where('slug', 'family-pro')->exists())->toBeTrue();
        expect(Plan::where('slug', 'b2b-basic')->exists())->toBeTrue();
        expect(Plan::where('slug', 'b2b-growth')->exists())->toBeTrue();
        expect(Plan::where('slug', 'b2b-pro')->exists())->toBeTrue();
        expect(Plan::where('slug', 'enterprise')->exists())->toBeTrue();
    });

    it('prevents non-super admin from accessing plan routes', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('super-admin.plans.index'))
            ->assertForbidden();
    });

    it('prevents tenant admin from accessing plan routes', function () {
        $admin = User::factory()->tenantAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.plans.index'))
            ->assertForbidden();
    });
});
