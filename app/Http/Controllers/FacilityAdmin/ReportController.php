<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Http\Controllers\Controller;
use App\Models\ClinicalNote;
use App\Models\PatientLink;
use App\Models\Referral;
use App\Models\Staff;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display facility reports overview.
     */
    public function index(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $stats = [
            'total_staff' => Staff::where('tenant_id', $tenant->id)->count(),
            'active_staff' => Staff::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
            'total_patients' => PatientLink::where('facility_tenant_id', $tenant->id)->count(),
            'active_patients' => PatientLink::where('facility_tenant_id', $tenant->id)->where('status', 'active')->count(),
            'total_notes' => ClinicalNote::where('tenant_id', $tenant->id)->count(),
            'notes_this_month' => ClinicalNote::where('tenant_id', $tenant->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_referrals_outgoing' => Referral::where('from_tenant_id', $tenant->id)->count(),
            'total_referrals_incoming' => Referral::where('to_tenant_id', $tenant->id)->count(),
            'pending_referrals' => Referral::where('to_tenant_id', $tenant->id)->where('status', 'pending')->count(),
        ];

        // Notes by type
        $notesByType = ClinicalNote::where('tenant_id', $tenant->id)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        // Monthly notes (last 6 months) — DB-agnostic date formatting
        $dateExpr = match (DB::getDriverName()) {
            'sqlite' => "strftime('%%Y-%%m', created_at)",
            default => "DATE_FORMAT(created_at, '%%Y-%%m')",
        };
        $monthlyNotes = ClinicalNote::where('tenant_id', $tenant->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("{$dateExpr} as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return view('facility-admin.reports.index', compact('tenant', 'stats', 'notesByType', 'monthlyNotes'));
    }

    /**
     * Display clinical notes report with filters.
     */
    public function clinicalNotes(Request $request): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $from = $request->get('from', now()->subMonth()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

        $notes = ClinicalNote::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$from, $to.' 23:59:59'])
            ->with(['child', 'staffUser'])
            ->latest()
            ->paginate(20);

        $summary = [
            'total' => $notes->total(),
            'by_type' => ClinicalNote::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$from, $to.' 23:59:59'])
                ->selectRaw('type, count(*) as total')
                ->groupBy('type')
                ->pluck('total', 'type'),
        ];

        return view('facility-admin.reports.clinical-notes', compact('tenant', 'notes', 'summary', 'from', 'to'));
    }

    /**
     * Display patient report.
     */
    public function patients(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $patients = PatientLink::where('facility_tenant_id', $tenant->id)
            ->with(['child', 'parentUser'])
            ->latest()
            ->paginate(20);

        $summary = [
            'total' => $patients->total(),
            'active' => PatientLink::where('facility_tenant_id', $tenant->id)->where('status', 'active')->count(),
            'revoked' => PatientLink::where('facility_tenant_id', $tenant->id)->where('status', 'revoked')->count(),
        ];

        return view('facility-admin.reports.patients', compact('tenant', 'patients', 'summary'));
    }
}
