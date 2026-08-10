<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\PatientLink;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientLinkController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display a listing of patient links.
     */
    public function index(Request $request): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $patientLinks = PatientLink::where('facility_tenant_id', $tenant->id)
            ->with(['child', 'parentUser'])
            ->latest()
            ->paginate(15);

        return view('facility-admin.patients.index', compact('tenant', 'patientLinks'));
    }

    /**
     * Show the form for creating a new patient link.
     */
    public function create(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        // Children are linked to facilities through PatientLink, not by tenant_id
        $linkedChildIds = PatientLink::where('facility_tenant_id', $tenant->id)
            ->pluck('child_id');
        $children = Child::whereIn('id', $linkedChildIds)->with('user')->get();
        $parents = User::where('role', 'parent')->get();

        return view('facility-admin.patients.create', compact('tenant', 'children', 'parents'));
    }

    /**
     * Store a newly created patient link.
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'parent_user_id' => 'required|exists:users,id',
            'permissions' => 'nullable|array',
        ]);

        $patientLink = PatientLink::create([
            'facility_tenant_id' => $tenant->id,
            'child_id' => $validated['child_id'],
            'parent_user_id' => $validated['parent_user_id'],
            'permissions' => $validated['permissions'] ?? ['view_timeline', 'view_growth', 'view_health'],
        ]);

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('success', 'Tautan pasien berhasil dibuat. Kode: '.$patientLink->link_code);
    }

    /**
     * Display the specified patient link.
     */
    public function show(PatientLink $patientLink): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);
        $patientLink->load(['child', 'parentUser']);

        return view('facility-admin.patients.show', compact('tenant', 'patientLink'));
    }

    /**
     * Update the specified patient link.
     */
    public function update(Request $request, PatientLink $patientLink)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $patientLink->update($validated);

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Remove (revoke) the specified patient link.
     */
    public function destroy(PatientLink $patientLink)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $patientLink->revoke();

        return redirect()->route('facility.patients.index')
            ->with('success', 'Tautan pasien berhasil dicabut.');
    }

    /**
     * Revoke the patient link.
     */
    public function revoke(PatientLink $patientLink)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $patientLink->revoke();

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('success', 'Tautan pasien berhasil dicabut.');
    }
}
