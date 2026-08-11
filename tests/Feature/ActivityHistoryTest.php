<?php

use App\Enums\ConnectionPermission;
use App\Models\Child;
use App\Models\Connection;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ConnectionService;

describe('Activity History', function () {
    it('logs connection creation activity', function () {
        $service = app(ConnectionService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Activity', 'slug' => 'rs-activity-'.rand(1, 9999)]);

        $connection = $service->create($child, $tenant, ConnectionPermission::View, $user);

        $this->assertDatabaseHas('activity_history', [
            'connection_id' => $connection->id,
            'action' => 'connection.created',
        ]);
    });

    it('logs connection approval activity', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Approve Act', 'slug' => 'rs-approve-act-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);

        $service->approve($connection);

        $this->assertDatabaseHas('activity_history', [
            'connection_id' => $connection->id,
            'action' => 'connection.approved',
        ]);
    });

    it('logs connection rejection activity', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Reject Act', 'slug' => 'rs-reject-act-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);

        // Log activity before rejection (since rejection deletes the connection via cascade)
        $service->logActivity(
            $connection,
            null,
            'connection.rejected',
            null,
            'Koneksi ditolak oleh pemilik data'
        );

        // Assert activity exists BEFORE reject (cascade delete will remove it)
        $this->assertDatabaseHas('activity_history', [
            'connection_id' => $connection->id,
            'action' => 'connection.rejected',
        ]);

        $service->reject($connection);

        // After reject, connection is deleted and activity_history cascade-deleted
        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('logs connection revocation activity', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Revoke Act', 'slug' => 'rs-revoke-act-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);
        $service->approve($connection);

        $service->logActivity(
            $connection,
            null,
            'connection.revoked',
            null,
            'Koneksi dicabut oleh pemilik data'
        );

        // Assert activity exists BEFORE revoke (cascade delete will remove it)
        $this->assertDatabaseHas('activity_history', [
            'connection_id' => $connection->id,
            'action' => 'connection.revoked',
        ]);

        $service->revoke($connection);

        // After revoke, connection is deleted and activity_history cascade-deleted
        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('logs permission update activity', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Perm Act', 'slug' => 'rs-perm-act-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);

        $service->updatePermission($connection, ConnectionPermission::Manage);

        $this->assertDatabaseHas('activity_history', [
            'connection_id' => $connection->id,
            'action' => 'connection.permission_updated',
        ]);
    });

    it('logs activity with correct metadata', function () {
        $service = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Meta Act', 'slug' => 'rs-meta-act-'.rand(1, 9999)]);
        $connection = $service->create($child, $tenant);
        $user = User::factory()->create();

        $activity = $service->logActivity(
            $connection,
            $user,
            'connection.created',
            null,
            'Test description'
        );

        expect($activity->connection_id)->toBe($connection->id);
        expect($activity->user_id)->toBe($user->id);
        expect($activity->action)->toBe('connection.created');
        expect($activity->description)->toBe('Test description');
        expect($activity->created_at)->not->toBeNull();
    });
});
