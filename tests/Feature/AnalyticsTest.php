<?php

use App\Models\User;

describe('Analytics & Monitoring (Super Admin)', function () {
    it('allows super admin to access analytics page', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.analytics.index'))
            ->assertOk();
    });

    it('allows super admin to access monitoring page', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.monitoring.index'))
            ->assertOk();
    });

    it('prevents non-super admin from accessing analytics and monitoring', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('super-admin.analytics.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('super-admin.monitoring.index'))
            ->assertForbidden();
    });
});
