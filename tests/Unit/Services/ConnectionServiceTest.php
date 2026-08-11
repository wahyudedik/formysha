<?php

use App\Enums\ConnectionPermission;
use App\Enums\ConnectionStatus;
use App\Models\Child;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ConnectionService;

describe('ConnectionService', function () {
    it('creates a connection with correct defaults', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Unit', 'slug' => 'rs-unit-'.rand(1, 9999)]);

        $connection = $service->create($child, $tenant);

        expect($connection)->not->toBeNull();
        expect($connection->child_id)->toBe($child->id);
        expect($connection->tenant_id)->toBe($tenant->id);
        expect($connection->status)->toBe(ConnectionStatus::Pending);
        expect($connection->permission)->toBe(ConnectionPermission::View);
        expect($connection->invited_at)->not->toBeNull();
    });

    it('approves a pending connection', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Approve Unit', 'slug' => 'rs-approve-unit-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);

        $service->approve($connection);

        $connection->refresh();
        expect($connection->status)->toBe(ConnectionStatus::Active);
        expect($connection->accepted_at)->not->toBeNull();
    });

    it('rejects a pending connection by deleting it', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Reject Unit', 'slug' => 'rs-reject-unit-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);
        $connectionId = $connection->id;

        $service->reject($connection);

        $this->assertDatabaseMissing('connections', ['id' => $connectionId]);
    });

    it('revokes an active connection', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Revoke Unit', 'slug' => 'rs-revoke-unit-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);
        $service->approve($connection);
        $connectionId = $connection->id;

        $service->revoke($connection);

        $this->assertDatabaseMissing('connections', ['id' => $connectionId]);
    });

    it('updates connection permission', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Perm Unit', 'slug' => 'rs-perm-unit-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant, ConnectionPermission::View);

        $service->updatePermission($connection, ConnectionPermission::Manage);

        $connection->refresh();
        expect($connection->permission)->toBe(ConnectionPermission::Manage);
    });

    it('gets connections by child', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant1 = Tenant::create(['name' => 'RS Child1', 'slug' => 'rs-child1-'.rand(1, 9999)]);
        $tenant2 = Tenant::create(['name' => 'RS Child2', 'slug' => 'rs-child2-'.rand(1, 9999)]);
        $service->create($child, $tenant1);
        $service->create($child, $tenant2);

        $connections = $service->getByChild($child);

        expect($connections->count())->toBe(2);
    });

    it('gets connections by tenant', function () {
        $service = app(ConnectionService::class);
        $tenant = Tenant::create(['name' => 'RS Tenant', 'slug' => 'rs-tenant-'.rand(1, 9999)]);
        $child1 = Child::factory()->create();
        $child2 = Child::factory()->create();
        $service->create($child1, $tenant);
        $service->create($child2, $tenant);

        $connections = $service->getByTenant($tenant);

        expect($connections->count())->toBe(2);
    });

    it('checks if connection exists', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Exists', 'slug' => 'rs-exists-'.rand(1, 9999)]);
        $service->create($child, $tenant);

        expect($service->hasConnection($child, $tenant))->toBeTrue();

        $otherTenant = Tenant::create(['name' => 'RS No', 'slug' => 'rs-no-'.rand(1, 9999)]);
        expect($service->hasConnection($child, $otherTenant))->toBeFalse();
    });

    it('logs activity correctly', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Log', 'slug' => 'rs-log-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);
        $user = User::factory()->create();

        $activity = $service->logActivity(
            $connection,
            $user,
            'connection.created',
            null,
            'Test activity log'
        );

        expect($activity)->not->toBeNull();
        expect($activity->action)->toBe('connection.created');
        expect($activity->description)->toBe('Test activity log');
        expect($activity->connection_id)->toBe($connection->id);
        expect($activity->user_id)->toBe($user->id);
    });

    it('checks expired connections', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Expire', 'slug' => 'rs-expire-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);
        $service->approve($connection);

        // Set expires_at to the past
        $connection->update(['expires_at' => now()->subDay()]);

        $count = $service->checkExpiredConnections();

        expect($count)->toBe(1);
        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });
});
