<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Models\TenantBranding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandingController extends Controller
{
    /**
     * Tampilkan form edit branding.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $branding = TenantBranding::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'organization_name' => $tenant->name,
                'primary_color' => '#7DD3FC',
                'secondary_color' => '#6EE7B7',
            ]
        );

        return view('admin.branding.edit', compact('tenant', 'branding'));
    }

    /**
     * Simpan perubahan branding.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'secondary_color' => ['nullable', 'string', 'max:7'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'custom_css' => ['nullable', 'string', 'max:10000'],
        ]);

        $branding = TenantBranding::firstOrCreate(
            ['tenant_id' => $tenant->id],
        );

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($branding->logo_path) {
                Storage::disk('public')->delete($branding->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('branding/'.$tenant->id, 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($branding->favicon_path) {
                Storage::disk('public')->delete($branding->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')->store('branding/'.$tenant->id, 'public');
        }

        // Remove file keys that aren't columns
        unset($validated['logo'], $validated['favicon']);

        $branding->update($validated);

        return redirect()->route('admin.branding.edit')
            ->with('success', 'Branding berhasil diperbarui.');
    }
}
