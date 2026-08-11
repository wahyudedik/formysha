<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Connection;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AuditService
{
    /**
     * Log an audit event.
     */
    public function log(
        string $action,
        ?Model $subject = null,
        ?User $user = null,
        array $oldValues = [],
        array $newValues = [],
    ): AuditLog {
        $request = request();

        if (! $user && $request instanceof Request) {
            $user = $request->user();
        }

        $tenantId = null;

        if ($subject && $subject->tenant_id ?? false) {
            $tenantId = $subject->tenant_id;
        } elseif ($user && $user->tenant_id ?? false) {
            $tenantId = $user->tenant_id;
        } elseif ($request instanceof Request) {
            $tenantId = $request->user()?->tenant_id;
        }

        return AuditLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'event' => $action,
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id' => $subject?->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? $request->userAgent() : null,
        ]);
    }

    /**
     * Get audit logs for a tenant.
     */
    public function getTenantLogs(Tenant $tenant, int $limit = 50): Collection
    {
        return AuditLog::where('tenant_id', $tenant->id)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Log tenant creation.
     */
    public function tenantCreated(Tenant $tenant): AuditLog
    {
        return $this->log('tenant.created', $tenant, null, [], $tenant->toArray());
    }

    /**
     * Log subscription activation.
     */
    public function subscriptionActivated(Tenant $tenant): AuditLog
    {
        return $this->log('subscription.activated', $tenant);
    }

    /**
     * Log payment approval.
     */
    public function paymentApproved(Model $payment, User $admin): AuditLog
    {
        return $this->log('payment.approved', $payment, $admin, [], [
            'status' => 'approved',
        ]);
    }

    /**
     * Log user invited.
     */
    public function userInvited(User $user): AuditLog
    {
        return $this->log('user.invited', $user, null, [], $user->toArray());
    }

    /**
     * Log an audit event with connection context.
     */
    public function logWithConnection(
        string $event,
        string $description,
        ?Model $auditable = null,
        ?Connection $connection = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        $request = request();
        $user = $request instanceof Request ? $request->user() : null;

        $tenantId = null;

        if ($connection && $connection->tenant_id) {
            $tenantId = $connection->tenant_id;
        } elseif ($auditable && $auditable->tenant_id ?? false) {
            $tenantId = $auditable->tenant_id;
        } elseif ($user && $user->tenant_id ?? false) {
            $tenantId = $user->tenant_id;
        } elseif ($request instanceof Request) {
            $tenantId = $request->user()?->tenant_id;
        }

        return AuditLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'connection_id' => $connection?->id,
            'event' => $event,
            'description' => $description,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? $request->userAgent() : null,
        ]);
    }
}
