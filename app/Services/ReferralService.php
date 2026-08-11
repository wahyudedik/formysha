<?php

namespace App\Services;

use App\Enums\ReferralStatus;
use App\Enums\ReferralType;
use App\Models\Child;
use App\Models\Referral;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    public function __construct(
        private AuditService $auditService,
    ) {}

    /**
     * Create a facility-to-facility referral.
     */
    public function createFacilityReferral(
        Child $child,
        Tenant $from,
        Tenant $to,
        User $staff,
        string $reason,
        ?string $clinicalSummary = null,
        ?string $notes = null,
    ): Referral {
        return DB::transaction(function () use ($child, $from, $to, $staff, $reason, $clinicalSummary, $notes) {
            $referral = Referral::create([
                'child_id' => $child->id,
                'from_tenant_id' => $from->id,
                'to_tenant_id' => $to->id,
                'referring_staff_id' => $staff->id,
                'reason' => $reason,
                'clinical_summary' => $clinicalSummary,
                'notes' => $notes,
                'status' => ReferralStatus::Pending,
                'type' => ReferralType::FacilityToFacility,
            ]);

            $this->auditService->log(
                'referral.created',
                $referral,
                null,
                ['description' => "Referral dibuat dari {$from->name} ke {$to->name} untuk {$child->name}"],
            );

            return $referral;
        });
    }

    /**
     * Create a facility-to-family referral.
     */
    public function createFamilyReferral(
        Child $child,
        Tenant $from,
        string $email,
        string $phone,
        string $reason,
        ?string $notes = null,
    ): Referral {
        return DB::transaction(function () use ($child, $from, $email, $phone, $reason, $notes) {
            $referral = Referral::create([
                'child_id' => $child->id,
                'from_tenant_id' => $from->id,
                'to_tenant_id' => null,
                'referring_staff_id' => null,
                'reason' => $reason,
                'notes' => $notes,
                'status' => ReferralStatus::Pending,
                'type' => ReferralType::FacilityToFamily,
                'metadata' => [
                    'email' => $email,
                    'phone' => $phone,
                ],
            ]);

            $this->auditService->log(
                'referral.created_family',
                $referral,
                null,
                ['description' => "Referral keluarga dibuat dari {$from->name} untuk {$child->name} ({$email})"],
            );

            return $referral;
        });
    }

    /**
     * Accept a referral.
     */
    public function acceptReferral(Referral $referral): void
    {
        $referral->accept();

        $this->auditService->log(
            'referral.accepted',
            $referral,
            null,
            ['description' => "Referral #{$referral->id} diterima"],
        );
    }

    /**
     * Complete a referral.
     */
    public function completeReferral(Referral $referral): void
    {
        $referral->complete();

        $this->auditService->log(
            'referral.completed',
            $referral,
            null,
            ['description' => "Referral #{$referral->id} selesai"],
        );
    }

    /**
     * Cancel a referral.
     */
    public function cancelReferral(Referral $referral): void
    {
        $referral->cancel();

        $this->auditService->log(
            'referral.cancelled',
            $referral,
            null,
            ['description' => "Referral #{$referral->id} dibatalkan"],
        );
    }

    /**
     * Get referral statistics for a tenant.
     */
    public function getReferralStats(Tenant $tenant): array
    {
        $sent = Referral::where('from_tenant_id', $tenant->id)->count();
        $received = Referral::where('to_tenant_id', $tenant->id)->count();
        $pending = Referral::where('from_tenant_id', $tenant->id)
            ->where('status', ReferralStatus::Pending)
            ->count();
        $accepted = Referral::where('from_tenant_id', $tenant->id)
            ->where('status', ReferralStatus::Accepted)
            ->count();
        $completed = Referral::where('from_tenant_id', $tenant->id)
            ->where('status', ReferralStatus::Completed)
            ->count();

        return [
            'sent' => $sent,
            'received' => $received,
            'pending' => $pending,
            'accepted' => $accepted,
            'completed' => $completed,
            'acceptance_rate' => $sent > 0 ? round(($accepted + $completed) / $sent * 100, 1) : 0,
        ];
    }

    /**
     * Get reward milestones for a tenant based on referral count.
     */
    public function getRewardMilestones(Tenant $tenant): array
    {
        $completedCount = Referral::where('from_tenant_id', $tenant->id)
            ->where('status', ReferralStatus::Completed)
            ->count();

        $milestones = [
            ['threshold' => 5, 'title' => 'Pemula', 'description' => '5 rujukan selesai', 'unlocked' => false],
            ['threshold' => 15, 'title' => 'Aktif', 'description' => '15 rujukan selesai', 'unlocked' => false],
            ['threshold' => 50, 'title' => 'Profesional', 'description' => '50 rujukan selesai', 'unlocked' => false],
            ['threshold' => 100, 'title' => 'Ahli', 'description' => '100 rujukan selesai', 'unlocked' => false],
        ];

        foreach ($milestones as &$milestone) {
            $milestone['unlocked'] = $completedCount >= $milestone['threshold'];
        }

        return [
            'total_completed' => $completedCount,
            'milestones' => $milestones,
        ];
    }

    /**
     * Get recent referrals for a tenant.
     */
    public function getRecentReferrals(Tenant $tenant, int $limit = 10): Collection
    {
        return Referral::where(function ($query) use ($tenant) {
            $query->where('from_tenant_id', $tenant->id)
                ->orWhere('to_tenant_id', $tenant->id);
        })
            ->with(['child', 'fromTenant', 'toTenant', 'referringStaff'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
