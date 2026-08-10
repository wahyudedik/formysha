<?php

use App\Models\Tenant;
use App\Models\User;

describe('Tenant Management (Super Admin)', function () {
    it('allows super admin to view tenant list', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.tenants.index'))
            ->assertOk();
    });

    it('allows super admin to create a new tenant', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('super-admin.tenants.store'), [
                'name' => 'Rumah Sakit Anak',
                'slug' => 'rs-anak',
                'type' => 'hospital',
            ])
            ->assertRedirect(route('super-admin.tenants.index'));

        $this->assertDatabaseHas('tenants', [
            'name' => 'Rumah Sakit Anak',
            'slug' => 'rs-anak',
            'type' => 'hospital',
        ]);
    });

    it('validates type is required when creating tenant', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('super-admin.tenants.store'), [
                'name' => 'Tanpa Tipe',
            ])
            ->assertSessionHasErrors('type');
    });

    it('validates type must be a valid TenantType enum', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('super-admin.tenants.store'), [
                'name' => 'Tipe Invalid',
                'type' => 'invalid_type',
            ])
            ->assertSessionHasErrors('type');
    });

    it('shows tenant type dropdown on create form', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('super-admin.tenants.create'))
            ->assertOk()
            ->assertSee('Tipe Tenant')
            ->assertSee('Keluarga')
            ->assertSee('Rumah Sakit')
            ->assertSee('Klinik');
    });

    it('allows super admin to create a B2C family tenant', function () {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('super-admin.tenants.store'), [
                'name' => 'Keluarga Budi',
                'type' => 'family',
            ])
            ->assertRedirect(route('super-admin.tenants.index'));

        $this->assertDatabaseHas('tenants', [
            'name' => 'Keluarga Budi',
            'type' => 'family',
        ]);
    });

    it('allows super admin to view tenant detail', function () {
        $admin = User::factory()->superAdmin()->create();
        $tenant = Tenant::create([
            'name' => 'Klinik Sehat',
            'slug' => 'klinik-sehat',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('super-admin.tenants.show', $tenant))
            ->assertOk();
    });

    it('allows super admin to edit a tenant', function () {
        $admin = User::factory()->superAdmin()->create();
        $tenant = Tenant::create([
            'name' => 'Klinik Lama',
            'slug' => 'klinik-lama',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('super-admin.tenants.update', $tenant), [
                'name' => 'Klinik Baru',
            ])
            ->assertRedirect(route('super-admin.tenants.index'));

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Klinik Baru',
        ]);
    });

    it('allows super admin to toggle tenant status', function () {
        $admin = User::factory()->superAdmin()->create();
        $tenant = Tenant::create([
            'name' => 'Daycare Ceria',
            'slug' => 'daycare-ceria',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('super-admin.tenants.toggle-status', $tenant))
            ->assertRedirect(route('super-admin.tenants.index'));

        $tenant->refresh();
        expect($tenant->is_active)->toBeFalse();
    });

    it('allows super admin to delete a tenant', function () {
        $admin = User::factory()->superAdmin()->create();
        $tenant = Tenant::create([
            'name' => 'TK Melati',
            'slug' => 'tk-melati',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('super-admin.tenants.destroy', $tenant))
            ->assertRedirect(route('super-admin.tenants.index'));

        $this->assertSoftDeleted('tenants', ['id' => $tenant->id]);
    });

    it('prevents non-super admin from accessing tenant routes', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('super-admin.tenants.index'))
            ->assertForbidden();
    });
});
