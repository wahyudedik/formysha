<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends ApiController
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * List all tenants with pagination, search, and status filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tenant::withCount('users', 'children');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('is_active', $status === 'active');
        }

        $tenants = $query->latest()->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($tenants, 'Daftar tenant berhasil diambil');
    }

    /**
     * Create a new tenant.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tenants,slug'],
            'domain' => ['nullable', 'string', 'max:255'],
        ]);

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'domain' => $validated['domain'] ?? null,
            'is_active' => true,
        ]);

        return $this->successResponse($tenant, 'Tenant berhasil dibuat', 201);
    }

    /**
     * Show a specific tenant.
     */
    public function show(Tenant $tenant): JsonResponse
    {
        $tenant->loadCount('users', 'children', 'subscriptions');

        return $this->successResponse($tenant, 'Detail tenant berhasil diambil');
    }

    /**
     * Update a specific tenant.
     */
    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'domain' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $tenant->update($validated);

        return $this->successResponse($tenant->fresh(), 'Tenant berhasil diperbarui');
    }

    /**
     * Toggle tenant active status.
     */
    public function toggleStatus(Tenant $tenant): JsonResponse
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);

        $status = $tenant->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return $this->successResponse($tenant->fresh(), "Tenant berhasil {$status}");
    }

    /**
     * Soft delete a specific tenant.
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return $this->successResponse(null, 'Tenant berhasil dihapus');
    }
}
