<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Models\ImportJob;
use App\Models\TenantInvitation;
use App\Services\EnterpriseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnterpriseController extends Controller
{
    public function __construct(
        private readonly EnterpriseService $enterpriseService,
    ) {}

    /**
     * Show analytics dashboard.
     */
    public function analytics(Request $request): View
    {
        $tenant = $request->user()->tenant;
        $summary = $this->enterpriseService->getAnalyticsSummary($tenant);

        $days = (int) $request->get('days', 30);
        $metrics = [
            'active_users' => $this->enterpriseService->getMetrics($tenant, 'active_users', $days),
            'api_calls' => $this->enterpriseService->getMetrics($tenant, 'api_calls', $days),
            'storage_used_mb' => $this->enterpriseService->getMetrics($tenant, 'storage_used_mb', $days),
            'children_count' => $this->enterpriseService->getMetrics($tenant, 'children_count', $days),
        ];

        return view('admin.enterprise.analytics', [
            'summary' => $summary,
            'metrics' => $metrics,
            'days' => $days,
        ]);
    }

    /**
     * List invitations.
     */
    public function invitations(Request $request): View
    {
        $tenant = $request->user()->tenant;
        $invitations = $this->enterpriseService->getPendingInvitations($tenant);

        return view('admin.enterprise.invitations', [
            'invitations' => $invitations,
        ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 404);
        }

        $invitation = $this->enterpriseService->inviteUser(
            $tenant,
            $validated['email'],
            $validated['role'],
            $request->user(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Undangan berhasil dikirim.',
            'data' => $invitation,
        ], 201);
    }

    /**
     * Revoke an invitation.
     */
    public function revokeInvitation(Request $request, TenantInvitation $invitation): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if ($invitation->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Undangan tidak ditemukan.',
            ], 404);
        }

        $this->enterpriseService->revokeInvitation($invitation);

        return response()->json([
            'success' => true,
            'message' => 'Undangan berhasil dibatalkan.',
        ]);
    }

    /**
     * Show import page.
     */
    public function import(Request $request): View
    {
        $tenant = $request->user()->tenant;

        $jobs = ImportJob::where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(10);

        return view('admin.enterprise.import', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Process import.
     */
    public function processImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:family_members,growth_records,backup_restore'],
            'file' => ['required', 'file', 'mimes:csv,json,txt', 'max:10240'],
        ]);

        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 404);
        }

        // Store the uploaded file
        $filePath = $request->file('file')->store('imports', 'local');

        $job = $this->enterpriseService->createImportJob(
            $tenant,
            $request->user(),
            $validated['type'],
        );

        // Process the import based on type
        try {
            $result = $this->enterpriseService->processImportFile($job, $filePath, $validated['type']);

            return response()->json([
                'success' => true,
                'message' => "Import berhasil diproses. {$result['created']} data dibuat, {$result['failed']} gagal.",
                'data' => $job->fresh(),
            ], 201);
        } catch (\Exception $e) {
            $this->enterpriseService->failImport($job, $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Import gagal: '.$e->getMessage(),
            ], 422);
        }
    }
}
