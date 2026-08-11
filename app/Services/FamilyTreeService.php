<?php

namespace App\Services;

use App\Enums\ConnectionStatus;
use App\Models\ActivityHistory;
use App\Models\Child;
use App\Models\Tenant;
use Illuminate\Support\Collection;

class FamilyTreeService
{
    /**
     * Get the full family tree data for a child.
     * Returns hierarchical data: owner, family members, connections to orgs.
     */
    public function getTree(Child $child): array
    {
        $child->loadMissing(['user', 'familyMembers', 'connections.tenant']);

        return [
            'owner' => [
                'id' => $child->user->id,
                'name' => $child->user->name,
                'email' => $child->user->email,
                'role' => 'Pemilik',
            ],
            'child' => [
                'id' => $child->id,
                'name' => $child->name,
                'nickname' => $child->nickname,
                'slug' => $child->slug,
                'photo' => $child->photo,
                'date_of_birth' => $child->date_of_birth?->format('d M Y'),
            ],
            'family_members' => $child->familyMembers->map(fn ($member) => [
                'id' => $member->id,
                'name' => $member->name,
                'relationship' => $member->relationship,
                'relationship_label' => $member->relationship_label,
                'photo' => $member->photo,
                'permission_level' => $member->permission_level?->value ?? 'view',
                'permission_label' => $member->permission_level?->label() ?? 'Lihat Saja',
            ]),
            'connections' => $child->connections->map(fn ($connection) => [
                'id' => $connection->id,
                'tenant_name' => $connection->tenant?->name ?? '-',
                'tenant_type' => $connection->tenant?->type ?? '-',
                'status' => $connection->status->value,
                'status_label' => $connection->status->label(),
                'permission' => $connection->permission->value,
                'permission_label' => $connection->permission->label(),
                'accepted_at' => $connection->accepted_at?->format('d M Y'),
            ]),
        ];
    }

    /**
     * Get family members for a child.
     */
    public function getFamilyMembers(Child $child): Collection
    {
        return $child->familyMembers()
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active connections for a child.
     */
    public function getConnections(Child $child): Collection
    {
        return $child->connections()
            ->where('status', ConnectionStatus::Active)
            ->with('tenant')
            ->latest()
            ->get();
    }

    /**
     * Get organizations (tenants) connected to a child.
     */
    public function getOrganizations(Child $child): Collection
    {
        $tenantIds = $child->connections()
            ->where('status', ConnectionStatus::Active)
            ->pluck('tenant_id');

        return Tenant::whereIn('id', $tenantIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get recent timeline entries for a child.
     */
    public function getTimeline(Child $child, int $limit = 10): Collection
    {
        return $child->timelines()
            ->latest('event_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get access history from activity_history for a child's connections.
     */
    public function getAccessHistory(Child $child, int $limit = 20): Collection
    {
        $connectionIds = $child->connections()->pluck('id');

        if ($connectionIds->isEmpty()) {
            return collect();
        }

        return ActivityHistory::whereIn('connection_id', $connectionIds)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
