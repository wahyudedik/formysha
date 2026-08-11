<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TenantType;
use App\Models\ClinicalNote;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index(): View
    {
        $tenants = Tenant::withCount('users', 'children')
            ->latest()
            ->paginate(20);

        return view('super-admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create(): View
    {
        $tenantTypes = TenantType::cases();

        return view('super-admin.tenants.create', compact('tenantTypes'));
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request): RedirectResponse
    {
        $validTypes = collect(TenantType::cases())->pluck('value')->implode(',');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tenants,slug'],
            'type' => ['required', 'string', 'in:'.$validTypes],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Tenant::create($validated);

        return redirect()->route('super-admin.tenants.index')
            ->with('status', __('status.tenant_created'));
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant): View
    {
        $tenant->loadCount('users', 'children', 'subscriptions');

        // Load B2B data if this is a B2B tenant
        $b2bData = null;
        if ($tenant->isB2B()) {
            $staff = Staff::where('tenant_id', $tenant->id)
                ->with('user')
                ->get();

            $patientLinks = $tenant->patientLinks()->with('user')->get();

            $clinicalNotes = ClinicalNote::where('tenant_id', $tenant->id)
                ->latest()
                ->take(10)
                ->get();

            $referralsFrom = Referral::where('from_tenant_id', $tenant->id)
                ->with('toTenant')
                ->latest()
                ->take(10)
                ->get();

            $referralsTo = Referral::where('to_tenant_id', $tenant->id)
                ->with('fromTenant')
                ->latest()
                ->take(10)
                ->get();

            $b2bData = [
                'staff' => $staff,
                'staff_count' => $staff->count(),
                'active_staff_count' => $staff->where('is_active', true)->count(),
                'patient_links' => $patientLinks,
                'patient_link_count' => $patientLinks->count(),
                'active_patient_count' => $patientLinks->where('status', 'active')->count(),
                'clinical_notes' => $clinicalNotes,
                'clinical_note_count' => ClinicalNote::where('tenant_id', $tenant->id)->count(),
                'referrals_from' => $referralsFrom,
                'referrals_to' => $referralsTo,
                'referral_count' => Referral::where('from_tenant_id', $tenant->id)
                    ->orWhere('to_tenant_id', $tenant->id)
                    ->count(),
                'pending_referral_count' => Referral::where(function ($query) use ($tenant) {
                    $query->where('from_tenant_id', $tenant->id)
                        ->orWhere('to_tenant_id', $tenant->id);
                })->where('status', 'pending')->count(),
            ];
        }

        return view('super-admin.tenants.show', compact('tenant', 'b2bData'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant): View
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $tenant->update($validated);

        return redirect()->route('super-admin.tenants.index')
            ->with('status', __('status.tenant_updated'));
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()->route('super-admin.tenants.index')
            ->with('status', __('status.tenant_deleted'));
    }

    /**
     * Toggle tenant active status.
     */
    public function toggleStatus(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);

        $status = $tenant->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('super-admin.tenants.index')
            ->with('success', "Tenant berhasil {$status}.");
    }
}
