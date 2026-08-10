<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilitySettingsController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display facility settings.
     */
    public function edit(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $facility = Facility::where('tenant_id', $tenant->id)->first();

        return view('facility-admin.settings.edit', compact('tenant', 'facility'));
    }

    /**
     * Update facility settings.
     */
    public function update(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email_institution' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'license_number' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        // Update tenant
        $tenant->update([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email_institution' => $validated['email_institution'] ?? null,
            'website' => $validated['website'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        // Update or create facility record
        $facility = Facility::where('tenant_id', $tenant->id)->first();

        if ($facility) {
            $facility->update([
                'name' => $validated['name'],
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email_institution'] ?? null,
                'website' => $validated['website'] ?? null,
            ]);
        } else {
            Facility::create([
                'tenant_id' => $tenant->id,
                'facility_type' => $tenant->facility_type?->value ?? 'clinic',
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email_institution' => $validated['email_institution'] ?? null,
                'website' => $validated['website'] ?? null,
            ]);
        }

        return redirect()->route('facility.settings.edit')
            ->with('success', 'Pengaturan fasilitas berhasil diperbarui.');
    }
}
