<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return view('super-admin.tenants.create');
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tenants,slug'],
        ]);

        Tenant::create($validated);

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant berhasil dibuat.');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant): View
    {
        $tenant->loadCount('users', 'children', 'subscriptions');

        return view('super-admin.tenants.show', compact('tenant'));
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
            ->with('success', 'Tenant berhasil diperbarui.');
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant berhasil dihapus.');
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
