<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display the facility admin dashboard.
     */
    public function index(Request $request): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $usage = $this->tenantService->getTenantUsage($tenant);

        $recentPatients = $tenant->patientLinks()
            ->with(['child', 'parentUser'])
            ->latest()
            ->take(5)
            ->get();

        $recentClinicalNotes = $tenant->clinicalNotes()
            ->with(['child', 'staffUser'])
            ->latest()
            ->take(5)
            ->get();

        $pendingReferrals = $tenant->referralsTo()
            ->where('status', 'pending')
            ->count();

        return view('facility-admin.dashboard', compact(
            'tenant',
            'usage',
            'recentPatients',
            'recentClinicalNotes',
            'pendingReferrals',
        ));
    }
}
