<?php

namespace App\Services;

use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\Growth;
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

    /**
     * Process an import file based on type.
     *
     * @return array{created: int, failed: int}
     */
    public function processImportFile(ImportJob $job, string $filePath, string $type): array
    {
        $absolutePath = storage_path('app/'.$filePath);

        if (! file_exists($absolutePath)) {
            throw new \RuntimeException('File import tidak ditemukan.');
        }

        $this->updateImportProgress($job, 0, 0);

        return match ($type) {
            'family_members' => $this->processFamilyMembersCsv($job, $absolutePath),
            'growth_records' => $this->processGrowthRecordsCsv($job, $absolutePath),
            'backup_restore' => $this->processBackupRestore($job, $absolutePath),
            default => throw new \RuntimeException('Tipe import tidak didukung: '.$type),
        };
    }

    /**
     * Process family members CSV import.
     *
     * Expected CSV columns: child_name, name, relationship, phone, email
     *
     * @return array{created: int, failed: int}
     */
    private function processFamilyMembersCsv(ImportJob $job, string $filePath): array
    {
        $rows = $this->readCsvFile($filePath);
        $created = 0;
        $failed = 0;
        $total = count($rows);

        foreach ($rows as $index => $row) {
            try {
                if (empty($row['name']) || empty($row['relationship']) || empty($row['child_name'])) {
                    $failed++;

                    continue;
                }

                // Find child by name within tenant
                $child = Child::where('tenant_id', $job->tenant_id)
                    ->where('name', 'like', '%'.$row['child_name'].'%')
                    ->first();

                if (! $child) {
                    $failed++;

                    continue;
                }

                FamilyMember::create([
                    'tenant_id' => $job->tenant_id,
                    'user_id' => $job->user_id,
                    'child_id' => $child->id,
                    'name' => trim($row['name']),
                    'relationship' => trim($row['relationship']),
                    'phone' => $row['phone'] ?? null,
                    'email' => $row['email'] ?? null,
                    'is_primary' => false,
                ]);

                $created++;
            } catch (\Exception $e) {
                $failed++;
            }

            if (($index + 1) % 10 === 0) {
                $this->updateImportProgress($job, $created, $failed);
            }
        }

        $this->updateImportProgress($job, $created, $failed);
        $this->completeImport($job);

        return ['created' => $created, 'failed' => $failed];
    }

    /**
     * Process growth records CSV import.
     *
     * Expected CSV columns: child_name, measured_at, weight_kg, height_cm, head_circumference_cm, notes
     *
     * @return array{created: int, failed: int}
     */
    private function processGrowthRecordsCsv(ImportJob $job, string $filePath): array
    {
        $rows = $this->readCsvFile($filePath);
        $created = 0;
        $failed = 0;
        $total = count($rows);

        foreach ($rows as $index => $row) {
            try {
                if (empty($row['child_name']) || empty($row['measured_at'])) {
                    $failed++;

                    continue;
                }

                // Find child by name within tenant
                $child = Child::where('tenant_id', $job->tenant_id)
                    ->where('name', 'like', '%'.$row['child_name'].'%')
                    ->first();

                if (! $child) {
                    $failed++;

                    continue;
                }

                Growth::create([
                    'child_id' => $child->id,
                    'user_id' => $job->user_id,
                    'measured_at' => $row['measured_at'],
                    'weight_kg' => $row['weight_kg'] ?? null,
                    'height_cm' => $row['height_cm'] ?? null,
                    'head_circumference_cm' => $row['head_circumference_cm'] ?? null,
                    'notes' => $row['notes'] ?? null,
                ]);

                $created++;
            } catch (\Exception $e) {
                $failed++;
            }

            if (($index + 1) % 10 === 0) {
                $this->updateImportProgress($job, $created, $failed);
            }
        }

        $this->updateImportProgress($job, $created, $failed);
        $this->completeImport($job);

        return ['created' => $created, 'failed' => $failed];
    }

    /**
     * Process backup restore (JSON import).
     *
     * @return array{created: int, failed: int}
     */
    private function processBackupRestore(ImportJob $job, string $filePath): array
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('File JSON tidak valid: '.json_last_error_msg());
        }

        $created = 0;
        $failed = 0;

        // Import family members from backup
        if (isset($data['family_members']) && is_array($data['family_members'])) {
            foreach ($data['family_members'] as $member) {
                try {
                    $child = Child::where('tenant_id', $job->tenant_id)
                        ->where('name', 'like', '%'.($member['child_name'] ?? '').'%')
                        ->first();

                    if (! $child || empty($member['name']) || empty($member['relationship'])) {
                        $failed++;

                        continue;
                    }

                    FamilyMember::create([
                        'tenant_id' => $job->tenant_id,
                        'user_id' => $job->user_id,
                        'child_id' => $child->id,
                        'name' => $member['name'],
                        'relationship' => $member['relationship'],
                        'phone' => $member['phone'] ?? null,
                        'email' => $member['email'] ?? null,
                        'is_primary' => $member['is_primary'] ?? false,
                    ]);

                    $created++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }
        }

        // Import growth records from backup
        if (isset($data['growth_records']) && is_array($data['growth_records'])) {
            foreach ($data['growth_records'] as $record) {
                try {
                    $child = Child::where('tenant_id', $job->tenant_id)
                        ->where('name', 'like', '%'.($record['child_name'] ?? '').'%')
                        ->first();

                    if (! $child || empty($record['measured_at'])) {
                        $failed++;

                        continue;
                    }

                    Growth::create([
                        'child_id' => $child->id,
                        'user_id' => $job->user_id,
                        'measured_at' => $record['measured_at'],
                        'weight_kg' => $record['weight_kg'] ?? null,
                        'height_cm' => $record['height_cm'] ?? null,
                        'head_circumference_cm' => $record['head_circumference_cm'] ?? null,
                        'notes' => $record['notes'] ?? null,
                    ]);

                    $created++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }
        }

        $this->updateImportProgress($job, $created, $failed);
        $this->completeImport($job);

        return ['created' => $created, 'failed' => $failed];
    }

    /**
     * Read a CSV file and return rows as associative arrays.
     *
     * @return list<array<string, string>>
     */
    private function readCsvFile(string $filePath): array
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Gagal membuka file CSV.');
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            return [];
        }

        // Normalize headers to snake_case
        $headers = array_map(fn ($h) => strtolower(trim(str_replace(' ', '_', $h))), $headers);

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $rows[] = array_combine($headers, $row);
            }
        }

        fclose($handle);

        return $rows;
    }
}
