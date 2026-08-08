<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends ApiController
{
    /**
     * List all plans.
     */
    public function index(): JsonResponse
    {
        $plans = Plan::orderBy('sort_order')->get();

        return $this->successResponse($plans, 'Daftar paket berhasil diambil');
    }

    /**
     * Create a new plan.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:plans,slug'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'price_yearly' => ['nullable', 'integer', 'min:0'],
            'max_children' => ['required', 'integer', 'min:1'],
            'max_photos' => ['required', 'integer', 'min:1'],
            'max_videos' => ['required', 'integer', 'min:1'],
            'max_storage_mb' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'is_free' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

        $plan = Plan::create($validated);

        return $this->successResponse($plan, 'Paket berhasil dibuat', 201);
    }

    /**
     * Show a specific plan.
     */
    public function show(Plan $plan): JsonResponse
    {
        return $this->successResponse($plan, 'Detail paket berhasil diambil');
    }

    /**
     * Update a specific plan.
     */
    public function update(Request $request, Plan $plan): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'unique:plans,slug,'.$plan->id],
            'price_monthly' => ['sometimes', 'required', 'integer', 'min:0'],
            'price_yearly' => ['nullable', 'integer', 'min:0'],
            'max_children' => ['sometimes', 'required', 'integer', 'min:1'],
            'max_photos' => ['sometimes', 'required', 'integer', 'min:1'],
            'max_videos' => ['sometimes', 'required', 'integer', 'min:1'],
            'max_storage_mb' => ['sometimes', 'required', 'integer', 'min:1'],
            'features' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'is_free' => ['nullable', 'boolean'],
        ]);

        $plan->update($validated);

        return $this->successResponse($plan->fresh(), 'Paket berhasil diperbarui');
    }

    /**
     * Soft delete a specific plan.
     */
    public function destroy(Plan $plan): JsonResponse
    {
        $plan->delete();

        return $this->successResponse(null, 'Paket berhasil dihapus');
    }
}
