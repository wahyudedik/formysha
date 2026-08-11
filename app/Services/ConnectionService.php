<?php

namespace App\Services;

use App\Enums\ConnectionPermission;
use App\Enums\ConnectionStatus;
use App\Models\ActivityHistory;
use App\Models\Child;
use App\Models\Connection;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ConnectionService
{
    // ─── CRUD Operations ──────────────────────────────────────

    /**
     * Create a new connection between a child and a tenant (organization).
     */
    public function create(
        Child $child,
        Tenant $tenant,
        ConnectionPermission $permission = ConnectionPermission::View,
        ?User $invitedBy = null,
    ): Connection {
        // Check if connection already exists
        $existing = Connection::where('child_id', $child->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $connection = Connection::create([
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
            'status' => ConnectionStatus::Pending,
            'permission' => $permission,
            'invited_by' => $invitedBy?->id,
            'invited_at' => now(),
            'notes' => null,
        ]);

        // Log activity
        $this->logActivity(
            $connection,
            $invitedBy,
            'connection.created',
            null,
            'Koneksi baru dibuat antara '.$child->name.' dan '.$tenant->name
        );

        return $connection;
    }

    /**
     * Approve a pending connection — set status to Active and record accepted_at.
     */
    public function approve(Connection $connection): void
    {
        $connection->approve();

        $this->logActivity(
            $connection,
            null,
            'connection.approved',
            null,
            'Koneksi disetujui'
        );
    }

    /**
     * Reject a pending connection — delete it.
     */
    public function reject(Connection $connection): void
    {
        $childName = $connection->child?->name ?? 'Unknown';
        $tenantName = $connection->tenant?->name ?? 'Unknown';

        $connection->reject();

        // Note: connection is deleted, so we can't log activity on it
        // Activity is logged before rejection in the controller
    }

    /**
     * Revoke an active connection — delete it (terminated by owner).
     */
    public function revoke(Connection $connection): void
    {
        $connection->revoke();
    }

    /**
     * Update the permission level for a connection.
     */
    public function updatePermission(Connection $connection, ConnectionPermission $permission): void
    {
        $oldPermission = $connection->permission;

        $connection->updatePermission($permission);

        $this->logActivity(
            $connection,
            null,
            'connection.permission_updated',
            null,
            'Permission diubah dari '.$oldPermission->label().' ke '.$permission->label()
        );
    }

    // ─── Query Methods ────────────────────────────────────────

    /**
     * Get all connections for a child.
     */
    public function getByChild(Child $child): Collection
    {
        return Connection::where('child_id', $child->id)
            ->with(['tenant', 'invitedBy'])
            ->latest()
            ->get();
    }

    /**
     * Get all connections for a tenant (organization).
     */
    public function getByTenant(Tenant $tenant): Collection
    {
        return Connection::where('tenant_id', $tenant->id)
            ->with(['child', 'invitedBy'])
            ->latest()
            ->get();
    }

    /**
     * Get active connections for a child.
     */
    public function getActiveByChild(Child $child): Collection
    {
        return Connection::where('child_id', $child->id)
            ->where('status', ConnectionStatus::Active)
            ->with(['tenant', 'invitedBy'])
            ->latest()
            ->get();
    }

    /**
     * Get active connections for a tenant (organization).
     */
    public function getActiveByTenant(Tenant $tenant): Collection
    {
        return Connection::where('tenant_id', $tenant->id)
            ->where('status', ConnectionStatus::Active)
            ->with(['child', 'invitedBy'])
            ->latest()
            ->get();
    }

    /**
     * Check if a connection exists between a child and a tenant.
     */
    public function hasConnection(Child $child, Tenant $tenant): bool
    {
        return Connection::where('child_id', $child->id)
            ->where('tenant_id', $tenant->id)
            ->exists();
    }

    // ─── Activity Logging ─────────────────────────────────────

    /**
     * Log an activity for a connection.
     */
    public function logActivity(
        Connection $connection,
        ?User $user,
        string $action,
        ?Model $entity = null,
        ?string $description = null,
    ): ActivityHistory {
        $request = request();

        return ActivityHistory::create([
            'connection_id' => $connection->id,
            'user_id' => $user?->id ?? ($request instanceof Request ? $request->user()?->id : null),
            'action' => $action,
            'entity_type' => $entity?->getMorphClass(),
            'entity_id' => $entity?->getKey(),
            'description' => $description,
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? $request->userAgent() : null,
        ]);
    }

    /**
     * Get activity history for a connection.
     */
    public function getActivityHistory(Connection $connection, int $limit = 50): Collection
    {
        return ActivityHistory::where('connection_id', $connection->id)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    // ─── Scheduled Tasks ──────────────────────────────────────

    /**
     * Check and remove expired connections.
     * Returns the count of expired connections removed.
     */
    public function checkExpiredConnections(): int
    {
        $expiredConnections = Connection::where('status', ConnectionStatus::Active)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        $count = $expiredConnections->count();

        foreach ($expiredConnections as $connection) {
            $this->logActivity(
                $connection,
                null,
                'connection.expired',
                null,
                'Koneksi kedaluwarsa dan dihapus otomatis'
            );

            $connection->delete();
        }

        return $count;
    }

    // ─── B2B Assisted Registration ────────────────────────────

    /**
     * Create a connection for B2B assisted registration flow.
     * Used when a facility registers a patient and the parent claims the profile.
     */
    public function assistedRegistration(
        Child $child,
        Tenant $facility,
        User $parent,
        ConnectionPermission $permission = ConnectionPermission::View,
    ): Connection {
        $connection = $this->create($child, $facility, $permission, $parent);

        // Auto-approve the connection since the parent is claiming it
        if ($connection->isPending()) {
            $this->approve($connection);
        }

        return $connection;
    }
}
