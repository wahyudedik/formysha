<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Display a listing of plans.
     */
    public function index(): View
    {
        $plans = Plan::orderBy('sort_order')->get();

        return view('super-admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create(): View
    {
        return view('super-admin.plans.create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:plans,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'price_yearly' => ['nullable', 'integer', 'min:0'],
            'max_children' => ['required', 'integer', 'min:-1'],
            'max_photos' => ['required', 'integer', 'min:-1'],
            'max_videos' => ['required', 'integer', 'min:-1'],
            'max_storage_mb' => ['required', 'integer', 'min:-1'],
            'max_family_members' => ['nullable', 'integer', 'min:-1'],
            'max_export_per_day' => ['required', 'integer', 'min:-1'],
            'features' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        Plan::create($validated);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Paket berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan): View
    {
        return view('super-admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_monthly' => ['sometimes', 'integer', 'min:0'],
            'price_yearly' => ['nullable', 'integer', 'min:0'],
            'max_children' => ['sometimes', 'integer', 'min:-1'],
            'max_photos' => ['sometimes', 'integer', 'min:-1'],
            'max_videos' => ['sometimes', 'integer', 'min:-1'],
            'max_storage_mb' => ['sometimes', 'integer', 'min:-1'],
            'max_family_members' => ['nullable', 'integer', 'min:-1'],
            'max_export_per_day' => ['sometimes', 'integer', 'min:-1'],
            'features' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $plan->update($validated);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
