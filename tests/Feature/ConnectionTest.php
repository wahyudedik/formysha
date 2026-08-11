<?php

use App\Enums\ConnectionPermission;
use App\Enums\ConnectionStatus;
use App\Models\Child;
use App\Models\Connection;
use App\Models\Tenant;
use App\Models\User;

describe('Connection Management', function () {
    it('can list connections for a child', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Sehat', 'slug' => 'rs-sehat-'.rand(1, 9999)]);
        Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->get(route('connections.index', $child))
            ->assertOk()
            ->assertSee('Koneksi');
    });

    it('can show connection create form', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('connections.create', $child))
            ->assertOk()
            ->assertSee('Buat Koneksi');
    });

    it('can create a new connection', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'Klinik A', 'slug' => 'klinik-a-'.rand(1, 9999)]);

        $this->actingAs($user)
            ->post(route('connections.store', $child), [
                'tenant_id' => $tenant->id,
                'permission' => 'view',
            ])
            ->assertRedirect(route('connections.index', $child));

        $this->assertDatabaseHas('connections', [
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
            'status' => 'pending',
            'permission' => 'view',
        ]);
    });

    it('can show a connection detail', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Bunda', 'slug' => 'rs-bunda-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->get(route('connections.show', [$child, $connection]))
            ->assertOk()
            ->assertSee('RS Bunda');
    });

    it('can update connection permission', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'Klinik B', 'slug' => 'klinik-b-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
            'permission' => ConnectionPermission::View,
        ]);

        $this->actingAs($user)
            ->put(route('connections.update', [$child, $connection]), [
                'permission' => 'edit',
            ])
            ->assertRedirect(route('connections.show', [$child, $connection]));

        $this->assertDatabaseHas('connections', [
            'id' => $connection->id,
            'permission' => 'edit',
        ]);
    });

    it('can delete a connection', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'Klinik C', 'slug' => 'klinik-c-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->delete(route('connections.destroy', [$child, $connection]))
            ->assertRedirect(route('connections.index', $child));

        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('can approve a pending connection', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Damai', 'slug' => 'rs-damai-'.rand(1, 9999)]);
        $connection = Connection::factory()->pending()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->post(route('connections.approve', [$child, $connection]))
            ->assertRedirect(route('connections.show', [$child, $connection]));

        $this->assertDatabaseHas('connections', [
            'id' => $connection->id,
            'status' => 'active',
        ]);

        $connection->refresh();
        expect($connection->accepted_at)->not->toBeNull();
    });

    it('can reject a pending connection', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'Klinik D', 'slug' => 'klinik-d-'.rand(1, 9999)]);
        $connection = Connection::factory()->pending()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->post(route('connections.reject', [$child, $connection]))
            ->assertRedirect(route('connections.index', $child));

        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('can revoke an active connection', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Sejahtera', 'slug' => 'rs-sejahtera-'.rand(1, 9999)]);
        $connection = Connection::factory()->active()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->post(route('connections.revoke', [$child, $connection]))
            ->assertRedirect(route('connections.index', $child));

        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('prevents non-owner from managing connections', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $otherUser->id]);
        $tenant = Tenant::create(['name' => 'RS Lain', 'slug' => 'rs-lain-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->get(route('connections.index', $child))
            ->assertForbidden();
    });

    it('prevents duplicate connections between same child and tenant', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Duplikat', 'slug' => 'rs-duplikat-'.rand(1, 9999)]);

        Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        // Create through service which checks for existing connections
        $this->actingAs($user)
            ->post(route('connections.store', $child), [
                'tenant_id' => $tenant->id,
                'permission' => 'view',
            ]);

        // Should not create duplicate — unique constraint on child_id + tenant_id
        expect(Connection::where('child_id', $child->id)
            ->where('tenant_id', $tenant->id)
            ->count())->toBe(1);
    });

    it('sets default status to pending on creation', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Pending', 'slug' => 'rs-pending-'.rand(1, 9999)]);

        $this->actingAs($user)
            ->post(route('connections.store', $child), [
                'tenant_id' => $tenant->id,
                'permission' => 'view',
            ]);

        $this->assertDatabaseHas('connections', [
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
            'status' => 'pending',
        ]);
    });

    it('sets accepted_at when approving', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Accept', 'slug' => 'rs-accept-'.rand(1, 9999)]);
        $connection = Connection::factory()->pending()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->post(route('connections.approve', [$child, $connection]));

        $connection->refresh();
        expect($connection->status)->toBe(ConnectionStatus::Active);
        expect($connection->accepted_at)->not->toBeNull();
    });

    it('shows empty state when no connections exist', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('connections.index', $child))
            ->assertOk()
            ->assertSee('Belum Ada Koneksi');
    });

    it('validates required fields on store', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('connections.store', $child), [])
            ->assertSessionHasErrors(['tenant_id', 'permission']);
    });
});
