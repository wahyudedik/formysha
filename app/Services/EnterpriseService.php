<?php

namespace App\Services;

use App\Models\ImportJob;
use App\Models\Tenant;
use App\Models\TenantAnalytic;
use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EnterpriseService
{
    /**
     * Record a metric for a tenant.
     */
    public function recordMetric(Tenant $tenant, string $metric, float $value, ?array $metadata = null): TenantAnalytic
    {
        return TenantAnalytic::create([
            'tenant_id' => $tenant->id,
            'metric' => $metric,
            'value' => $value,
            'recorded_date' => now()->toDateString(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get metric values for a tenant within a date range.
     */
    public function getMetrics(Tenant $tenant, string $metric, int $days = 30): Collection
    {
        $startDate = now()->subDays($days)->toDateString();

        return TenantAnalytic::where('tenant_id', $tenant->id)
            ->where('metric', $metric)
            ->where('recorded_date', '>=', $startDate)
            ->orderBy('recorded_date')
            ->get();
    }

    /**
     * Get analytics summary for a tenant.
     *
     * @return array{
     *     active_users: int,
     *     total_children: int,
     *     total_media: int,
     *     storage_used_mb: float,
     *     api_calls_today: int,
     *     recent_metrics: array<string, float>,
     * }
     */
    public function getAnalyticsSummary(Tenant $tenant): array
    {
        $activeUsers = $tenant->users()->count();
        $totalChildren = $tenant->getChildCount();
        $totalMedia = $tenant->media()->count();
        $storageUsedBytes = $tenant->getStorageUsed();
        $storageUsedMb = round($storageUsedBytes / (1024 * 1024), 2);

        // Get latest value for each metric type
        $metrics = ['active_users', 'api_calls', 'storage_used_mb', 'children_count'];
        $recentMetrics = [];

        foreach ($metrics as $metric) {
            $latest = TenantAnalytic::where('tenant_id', $tenant->id)
                ->where('metric', $metric)
                ->latest('recorded_date')
                ->value('value');

            $recentMetrics[$metric] = $latest !== null ? (float) $latest : 0.0;
        }

        // API calls today
        $apiCallsToday = TenantAnalytic::where('tenant_id', $tenant->id)
            ->where('metric', 'api_calls')
            ->where('recorded_date', now()->toDateString())
            ->sum('value');

        return [
            'active_users' => $activeUsers,
            'total_children' => $totalChildren,
            'total_media' => $totalMedia,
            'storage_used_mb' => $storageUsedMb,
            'api_calls_today' => (float) $apiCallsToday,
            'recent_metrics' => $recentMetrics,
        ];
    }

    /**
     * Send an invitation to join a tenant.
     */
    public function inviteUser(Tenant $tenant, string $email, string $role, User $invitedBy): TenantInvitation
    {
        return TenantInvitation::create([
            'tenant_id' => $tenant->id,
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'invited_by' => $invitedBy->id,
        ]);
    }

    /**
     * Accept an invitation.
     */
    public function acceptInvitation(TenantInvitation $invitation, User $user): bool
    {
        if ($invitation->isExpired() || $invitation->isAccepted()) {
            return false;
        }

        $invitation->update([
            'accepted_at' => now(),
        ]);

        // Assign tenant and role to user
        $user->update([
            'tenant_id' => $invitation->tenant_id,
            'role' => $invitation->role,
        ]);

        return true;
    }

    /**
     * Revoke an invitation.
     */
    public function revokeInvitation(TenantInvitation $invitation): bool
    {
        return $invitation->delete();
    }

    /**
     * Get pending invitations for a tenant.
     */
    public function getPendingInvitations(Tenant $tenant): Collection
    {
        return TenantInvitation::where('tenant_id', $tenant->id)
            ->pending()
            ->with('invitedBy')
            ->latest()
            ->get();
    }

    /**
     * Create an import job.
     */
    public function createImportJob(Tenant $tenant, User $user, string $type): ImportJob
    {
        return ImportJob::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'type' => $type,
            'status' => ImportJob::STATUS_PENDING,
        ]);
    }

    /**
     * Update import job progress.
     */
    public function updateImportProgress(ImportJob $job, int $processed, int $failed = 0): void
    {
        $job->update([
            'status' => ImportJob::STATUS_PROCESSING,
            'processed_items' => $processed,
            'failed_items' => $failed,
        ]);
    }

    /**
     * Mark an import job as completed.
     */
    public function completeImport(ImportJob $job): void
    {
        $job->update([
            'status' => ImportJob::STATUS_COMPLETED,
        ]);
    }

    /**
     * Mark an import job as failed.
     */
    public function failImport(ImportJob $job, string $error): void
    {
        $job->update([
            'status' => ImportJob::STATUS_FAILED,
            'error_message' => $error,
        ]);
    }
}
