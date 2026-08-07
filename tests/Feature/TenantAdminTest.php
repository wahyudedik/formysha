<?php

use App\Models\Tenant;
use App\Models\User;

describe('Tenant Admin Panel', function () {
    it('allows tenant admin to access dashboard', function () {
        $tenant = Tenant::create([
            'name' => 'Klinik Sehat',
            'slug' => 'klinik-sehat',
            'is_active' => true,
        ]);
        $admin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    });

    it('allows tenant admin to edit branding', function () {
        $tenant = Tenant::create([
            'name' => 'Daycare Ceria',
            'slug' => 'daycare-ceria',
            'is_active' => true,
        ]);
        $admin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.branding.edit'))
            ->assertOk();
    });

    it('allows tenant admin to edit settings', function () {
        $tenant = Tenant::create([
            'name' => 'TK Melati',
            'slug' => 'tk-melati',
            'is_active' => true,
        ]);
        $admin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk();
    });

    it('allows tenant admin to view usage', function () {
        $tenant = Tenant::create([
            'name' => 'PAUD Ceria',
            'slug' => 'paud-ceria',
            'is_active' => true,
        ]);
        $admin = User::factory()->tenantAdmin()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.usage.index'))
            ->assertOk();
    });

    it('prevents non-admin from accessing tenant admin routes', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    });
});
