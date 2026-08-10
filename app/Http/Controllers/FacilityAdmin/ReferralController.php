<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Enums\ReferralStatus;
use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferralController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display a listing of referrals.
     */
    public function index(Request $request): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $filter = $request->get('status');

        $query = Referral::where(function ($q) use ($tenant) {
            $q->where('from_tenant_id', $tenant->id)
                ->orWhere('to_tenant_id', $tenant->id);
        })->with(['child', 'referringStaff', 'toTenant', 'fromTenant']);

        if ($filter) {
            $query->where('status', $filter);
        }

        $referrals = $query->latest()->paginate(15);

        return view('facility-admin.referrals.index', compact('tenant', 'referrals', 'filter'));
    }

    /**
     * Show the form for creating a new referral.
     */
    public function create(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $facilities = Tenant::where('id', '!=', $tenant->id)
            ->where('type', '!=', 'family')
            ->where('is_active', true)
            ->get();
        $children = Child::where('tenant_id', $tenant->id)->get();

        return view('facility-admin.referrals.create', compact('tenant', 'facilities', 'children'));
    }

    /**
     * Store a newly created referral.
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'to_tenant_id' => 'required|exists:tenants,id',
            'reason' => 'required|string',
            'clinical_summary' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        /** @var Staff|null $staff */
        $staff = $tenant->staff()
            ->where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->first();

        $referral = Referral::create([
            'from_tenant_id' => $tenant->id,
            'to_tenant_id' => $validated['to_tenant_id'],
            'child_id' => $validated['child_id'],
            'referring_staff_id' => $staff?->id,
            'reason' => $validated['reason'],
            'clinical_summary' => $validated['clinical_summary'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => ReferralStatus::Pending,
        ]);

        return redirect()->route('facility.referrals.show', $referral)
            ->with('success', 'Rujukan berhasil dibuat.');
    }

    /**
     * Display the specified referral.
     */
    public function show(Referral $referral): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless(
            $referral->from_tenant_id === $tenant->id || $referral->to_tenant_id === $tenant->id,
            403
        );
        $referral->load(['child', 'referringStaff', 'toTenant', 'fromTenant']);

        return view('facility-admin.referrals.show', compact('tenant', 'referral'));
    }

    /**
     * Accept the specified referral.
     */
    public function accept(Referral $referral)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($referral->to_tenant_id === $tenant->id, 403);

        $referral->accept();

        return redirect()->route('facility.referrals.show', $referral)
            ->with('success', 'Rujukan berhasil diterima.');
    }

    /**
     * Complete the specified referral.
     */
    public function complete(Referral $referral)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless(
            $referral->from_tenant_id === $tenant->id || $referral->to_tenant_id === $tenant->id,
            403
        );

        $referral->complete();

        return redirect()->route('facility.referrals.show', $referral)
            ->with('success', 'Rujukan berhasil diselesaikan.');
    }

    /**
     * Cancel the specified referral.
     */
    public function cancel(Referral $referral)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless(
            $referral->from_tenant_id === $tenant->id || $referral->to_tenant_id === $tenant->id,
            403
        );

        $referral->cancel();

        return redirect()->route('facility.referrals.show', $referral)
            ->with('success', 'Rujukan berhasil dibatalkan.');
    }
}
