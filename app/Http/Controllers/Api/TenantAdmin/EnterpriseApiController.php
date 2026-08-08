<?php

namespace App\Http\Controllers\Api\TenantAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\ImportJob;
use App\Models\TenantInvitation;
use App\Services\EnterpriseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnterpriseApiController extends ApiController
{
    public function __construct(
        private readonly EnterpriseService $enterpriseService,
    ) {}

    /**
     * Get analytics data for the tenant.
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $summary = $this->enterpriseService->getAnalyticsSummary($tenant);

        return $this->successResponse($summary, 'Analytics berhasil diambil.');
    }

    /**
     * Get specific metric data.
     */
    public function getMetrics(Request $request, string $metric): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $days = (int) $request->get('days', 30);
        $metrics = $this->enterpriseService->getMetrics($tenant, $metric, $days);

        return $this->successResponse([
            'metric' => $metric,
            'days' => $days,
            'data' => $metrics,
        ], 'Metric berhasil diambil.');
    }

    /**
     * List invitations.
     */
    public function listInvitations(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $invitations = $this->enterpriseService->getPendingInvitations($tenant);

        return $this->successResponse($invitations, 'Undangan berhasil diambil.');
    }

    /**
     * Send invitation.
     */
    public function invite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'string', 'in:parent,tenant_admin'],
        ]);

        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $invitation = $this->enterpriseService->inviteUser(
            $tenant,
            $validated['email'],
            $validated['role'],
            $request->user(),
        );

        return $this->successResponse($invitation->toArray(), 'Undangan berhasil dikirim.', 201);
    }

    /**
     * Revoke an invitation.
     */
    public function revokeInvitation(Request $request, TenantInvitation $invitation): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if ($invitation->tenant_id !== $tenant->id) {
            return $this->errorResponse('Undangan tidak ditemukan.', 404);
        }

        $this->enterpriseService->revokeInvitation($invitation);

        return $this->successResponse(null, 'Undangan berhasil dibatalkan.');
    }

    /**
     * List import jobs.
     */
    public function getImportJobs(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $jobs = ImportJob::where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($jobs, 'Import jobs berhasil diambil.');
    }
}
