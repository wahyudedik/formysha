<?php

use App\Enums\ConnectionPermission;
use App\Enums\ConnectionStatus;
use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ConnectionService;
use App\Services\FamilyTreeService;
use Illuminate\Support\Collection;

describe('FamilyTreeService', function () {
    it('gets tree structure with owner info', function () {
        $service = app(FamilyTreeService::class);
        $user = User::factory()->create(['name' => 'Budi Santoso']);
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mysha',
            'nickname' => 'Sha',
        ]);

        $tree = $service->getTree($child);

        expect($tree)->toHaveKey('owner');
        expect($tree['owner']['id'])->toBe($user->id);
        expect($tree['owner']['name'])->toBe('Budi Santoso');
        expect($tree['owner']['role'])->toBe('Pemilik');
        expect($tree)->toHaveKey('child');
        expect($tree['child']['name'])->toBe('Mysha');
    });

    it('gets family members for a child', function () {
        $service = app(FamilyTreeService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        FamilyMember::factory()->create(['child_id' => $child->id, 'name' => 'Ayah']);
        FamilyMember::factory()->create(['child_id' => $child->id, 'name' => 'Ibu']);

        $members = $service->getFamilyMembers($child);

        expect($members->count())->toBe(2);
    });

    it('gets active connections for a child', function () {
        $service = app(FamilyTreeService::class);
        $connectionService = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant = Tenant::create(['name' => 'RS Tree', 'slug' => 'rs-tree-'.rand(1, 9999)]);
        $connection = $connectionService->create($child, $tenant);
        $connectionService->approve($connection);

        $connections = $service->getConnections($child);

        expect($connections->count())->toBe(1);
        expect($connections->first()->status)->toBe(ConnectionStatus::Active);
    });

    it('gets organizations connected to a child', function () {
        $service = app(FamilyTreeService::class);
        $connectionService = app(ConnectionService::class);
        $child = Child::factory()->create();
        $tenant1 = Tenant::create(['name' => 'RS Org1', 'slug' => 'rs-org1-'.rand(1, 9999)]);
        $tenant2 = Tenant::create(['name' => 'Klinik Org2', 'slug' => 'klinik-org2-'.rand(1, 9999)]);
        $c1 = $connectionService->create($child, $tenant1);
        $connectionService->approve($c1);
        $c2 = $connectionService->create($child, $tenant2);
        $connectionService->approve($c2);

        $organizations = $service->getOrganizations($child);

        expect($organizations->count())->toBe(2);
    });

    it('gets timeline for a child', function () {
        $service = app(FamilyTreeService::class);
        $child = Child::factory()->create();

        $timeline = $service->getTimeline($child);

        expect($timeline)->toBeInstanceOf(Collection::class);
    });

    it('gets access history for a child', function () {
        $service = app(FamilyTreeService::class);
        $connectionService = app(ConnectionService::class);
        $child = Child::factory()->create();
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'RS History', 'slug' => 'rs-history-'.rand(1, 9999)]);
        $connection = $connectionService->create($child, $tenant, ConnectionPermission::View, $user);

        $history = $service->getAccessHistory($child);

        expect($history->count())->toBeGreaterThan(0);
    });

    it('returns empty tree for child with no connections', function () {
        $service = app(FamilyTreeService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $tree = $service->getTree($child);

        expect($tree['family_members'])->toHaveCount(0);
        expect($tree['connections'])->toHaveCount(0);
    });

    it('returns tree with family and organizations', function () {
        $service = app(FamilyTreeService::class);
        $connectionService = app(ConnectionService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        FamilyMember::factory()->create(['child_id' => $child->id, 'name' => 'Ayah']);
        $tenant = Tenant::create(['name' => 'RS Full', 'slug' => 'rs-full-'.rand(1, 9999)]);
        $connection = $connectionService->create($child, $tenant);
        $connectionService->approve($connection);

        $tree = $service->getTree($child);

        expect($tree['family_members'])->toHaveCount(1);
        expect($tree['connections'])->toHaveCount(1);
        expect($tree['connections'][0]['tenant_name'])->toBe('RS Full');
    });
});
