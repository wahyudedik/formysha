<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\PatientLink;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
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
     *
     * NOTE: Shows ALL children and parents (not just already-linked ones)
     * to avoid circular dependency where new patients can't be registered.
     */
    public function create(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();

        // Show all children that have a parent user account
        $children = Child::with('user')
            ->whereHas('user')
            ->get();

        // Show all parent users (users with role 'parent')
        $parents = User::where('role', 'parent')
            ->get();

        return view('facility-admin.patients.create', compact('tenant', 'children', 'parents'));
    }

    /**
     * Store a newly created patient link.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'parent_user_id' => 'required|exists:users,id',
            'permissions' => 'nullable|array',
        ]);

        // Prevent duplicate active links for the same child at this facility
        $existing = PatientLink::where('facility_tenant_id', $tenant->id)
            ->where('child_id', $validated['child_id'])
            ->where('status', '!=', 'revoked')
            ->first();

        if ($existing) {
            return back()->withErrors([
                'child_id' => __('Anak ini sudah terdaftar di fasilitas ini.'),
            ]);
        }

        $patientLink = PatientLink::create([
            'facility_tenant_id' => $tenant->id,
            'child_id' => $validated['child_id'],
            'parent_user_id' => $validated['parent_user_id'],
            'permissions' => $validated['permissions'] ?? ['view_timeline', 'view_growth', 'view_health'],
        ]);

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('status', __('status.patient_link_created', ['code' => $patientLink->link_code]));
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
    public function update(Request $request, PatientLink $patientLink): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $patientLink->update($validated);

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('status', __('status.patient_link_updated'));
    }

    /**
     * Remove (revoke) the specified patient link.
     */
    public function destroy(PatientLink $patientLink): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $patientLink->revoke();

        return redirect()->route('facility.patients.index')
            ->with('status', __('status.patient_link_revoked'));
    }

    /**
     * Revoke the patient link.
     */
    public function revoke(PatientLink $patientLink): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $patientLink->revoke();

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('status', __('status.patient_link_revoked'));
    }

    /**
     * Send invitation to the parent for the patient link.
     */
    public function sendInvitation(PatientLink $patientLink): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $patientLink->sendInvitation();

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('status', __('Undangan berhasil dikirim! Kode: :code', ['code' => $patientLink->link_code]));
    }

    /**
     * Claim the patient profile — activate link and create connection.
     */
    public function claimProfile(PatientLink $patientLink): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($patientLink->facility_tenant_id === $tenant->id, 403);

        $parent = $patientLink->parentUser;

        if (! $parent) {
            return back()->withErrors([
                'parent' => __('Profil orang tua belum tersedia untuk tautan ini.'),
            ]);
        }

        $patientLink->claimProfile($parent);

        return redirect()->route('facility.patients.show', $patientLink)
            ->with('status', __('Profil berhasil diklaim! Koneksi telah dibuat.'));
    }
}
