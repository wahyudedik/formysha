<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Models\TenantSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Tampilkan form edit settings.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        // Ambil settings sebagai key-value array
        $settings = TenantSetting::where('tenant_id', $tenant->id)
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.settings.edit', compact('tenant', 'settings'));
    }

    /**
     * Simpan perubahan settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        // Simpan atau update setiap setting
        foreach ($validated as $key => $value) {
            TenantSetting::updateOrCreate(
                ['tenant_id' => $tenant->id, 'key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        // Update nama organisasi di branding juga jika ada
        if (! empty($validated['organization_name'])) {
            $tenant->branding()->updateOrCreate(
                ['tenant_id' => $tenant->id],
                ['organization_name' => $validated['organization_name']]
            );
        }

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
