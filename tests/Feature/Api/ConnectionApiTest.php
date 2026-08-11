<?php

use App\Enums\ConnectionPermission;
use App\Models\ActivityHistory;
use App\Models\Child;
use App\Models\Connection;
use App\Models\Tenant;
use App\Models\User;

describe('Connection API', function () {
    it('can list connections via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS API', 'slug' => 'rs-api-'.rand(1, 9999)]);
        Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/connections');

        $response->assertOk()
            ->assertJsonFragment(['tenant_id' => $tenant->id]);
    });

    it('can create a connection via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'Klinik API', 'slug' => 'klinik-api-'.rand(1, 9999)]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/connections', [
                'tenant_id' => $tenant->id,
                'permission' => 'view',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.tenant_id', $tenant->id)
            ->assertJsonPath('data.status', 'pending');
    });

    it('can show a connection via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Show', 'slug' => 'rs-show-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/connections/'.$connection->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $connection->id);
    });

    it('can update connection permission via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Update', 'slug' => 'rs-update-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
            'permission' => ConnectionPermission::View,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/children/'.$child->slug.'/connections/'.$connection->id, [
                'permission' => 'manage',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.permission', 'manage');

        $this->assertDatabaseHas('connections', [
            'id' => $connection->id,
            'permission' => 'manage',
        ]);
    });

    it('can delete a connection via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Delete', 'slug' => 'rs-delete-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/children/'.$child->slug.'/connections/'.$connection->id);

        $response->assertOk();

        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('can approve a connection via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Approve', 'slug' => 'rs-approve-'.rand(1, 9999)]);
        $connection = Connection::factory()->pending()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/connections/'.$connection->id.'/approve');

        $response->assertOk()
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('connections', [
            'id' => $connection->id,
            'status' => 'active',
        ]);
    });

    it('can reject a connection via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Reject', 'slug' => 'rs-reject-'.rand(1, 9999)]);
        $connection = Connection::factory()->pending()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/connections/'.$connection->id.'/reject');

        $response->assertOk();

        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('can revoke a connection via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Revoke', 'slug' => 'rs-revoke-'.rand(1, 9999)]);
        $connection = Connection::factory()->active()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/connections/'.$connection->id.'/revoke');

        $response->assertOk();

        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    });

    it('can get activity history via API', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::create(['name' => 'RS Activities', 'slug' => 'rs-activities-'.rand(1, 9999)]);
        $connection = Connection::factory()->create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
        ]);
        ActivityHistory::factory()->create([
            'connection_id' => $connection->id,
            'user_id' => $user->id,
            'action' => 'connection.created',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/connections/'.$connection->id.'/activities');

        $response->assertOk()
            ->assertJsonFragment(['action' => 'connection.created']);
    });

    it('returns 403 for unauthorized user via API', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/connections');

        $response->assertForbidden();
    });
});
