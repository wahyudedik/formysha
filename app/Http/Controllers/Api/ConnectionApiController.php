<?php

namespace App\Http\Controllers\Api;

use App\Enums\ConnectionPermission;
use App\Http\Resources\ConnectionResource;
use App\Models\Child;
use App\Models\Connection;
use App\Models\Tenant;
use App\Services\ConnectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConnectionApiController extends ApiController
{
    public function __construct(
        private ConnectionService $connectionService,
    ) {}

    /**
     * List connections for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $connections = $this->connectionService->getByChild($child);

        return $this->successResponse(
            ConnectionResource::collection($connections),
            'Daftar koneksi berhasil diambil'
        );
    }

    /**
     * Store a new connection.
     */
    public function store(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'permission' => 'required|string|in:view,comment,edit,manage',
            'notes' => 'nullable|string|max:500',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $permission = ConnectionPermission::from($validated['permission']);

        $connection = $this->connectionService->create(
            $child,
            $tenant,
            $permission,
            $request->user(),
        );

        if (isset($validated['notes']) && $validated['notes']) {
            $connection->update(['notes' => $validated['notes']]);
        }

        return $this->successResponse(
            new ConnectionResource($connection->load(['tenant', 'invitedBy'])),
            'Koneksi berhasil dibuat',
            201
        );
    }

    /**
     * Show a specific connection.
     */
    public function show(Request $request, Child $child, Connection $connection): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($connection->child_id === $child->id, 404);

        $connection->load(['tenant', 'invitedBy']);

        return $this->successResponse(
            new ConnectionResource($connection),
            'Detail koneksi berhasil diambil'
        );
    }

    /**
     * Update a specific connection.
     */
    public function update(Request $request, Child $child, Connection $connection): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($connection->child_id === $child->id, 404);

        $validated = $request->validate([
            'permission' => 'required|string|in:view,comment,edit,manage',
            'notes' => 'nullable|string|max:500',
        ]);

        $permission = ConnectionPermission::from($validated['permission']);

        $this->connectionService->updatePermission($connection, $permission);

        if (array_key_exists('notes', $validated)) {
            $connection->update(['notes' => $validated['notes']]);
        }

        return $this->successResponse(
            new ConnectionResource($connection->fresh()->load(['tenant', 'invitedBy'])),
            'Koneksi berhasil diperbarui'
        );
    }

    /**
     * Delete a specific connection.
     */
    public function destroy(Request $request, Child $child, Connection $connection): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($connection->child_id === $child->id, 404);

        $this->connectionService->logActivity(
            $connection,
            $request->user(),
            'connection.revoked',
            null,
            'Koneksi dicabut oleh pemilik data'
        );

        $this->connectionService->revoke($connection);

        return $this->successResponse(null, 'Koneksi berhasil dihapus');
    }

    /**
     * Approve a pending connection.
     */
    public function approve(Request $request, Child $child, Connection $connection): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($connection->child_id === $child->id, 404);
        abort_unless($connection->isPending(), 400);

        $this->connectionService->approve($connection);

        return $this->successResponse(
            new ConnectionResource($connection->fresh()->load(['tenant', 'invitedBy'])),
            'Koneksi berhasil disetujui'
        );
    }

    /**
     * Reject a pending connection.
     */
    public function reject(Request $request, Child $child, Connection $connection): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($connection->child_id === $child->id, 404);
        abort_unless($connection->isPending(), 400);

        $this->connectionService->logActivity(
            $connection,
            $request->user(),
            'connection.rejected',
            null,
            'Koneksi ditolak oleh pemilik data'
        );

        $this->connectionService->reject($connection);

        return $this->successResponse(null, 'Koneksi berhasil ditolak');
    }

    /**
     * Revoke an active connection.
     */
    public function revoke(Request $request, Child $child, Connection $connection): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($connection->child_id === $child->id, 404);

        $this->connectionService->logActivity(
            $connection,
            $request->user(),
            'connection.revoked',
            null,
            'Koneksi dicabut oleh pemilik data'
        );

        $this->connectionService->revoke($connection);

        return $this->successResponse(null, 'Koneksi berhasil dicabut');
    }

    /**
     * Get activity history for a connection.
     */
    public function activities(Request $request, Child $child, Connection $connection): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($connection->child_id === $child->id, 404);

        $limit = (int) $request->input('limit', 50);
        $activities = $this->connectionService->getActivityHistory($connection, $limit);

        return $this->successResponse(
            $activities,
            'Riwayat aktivitas berhasil diambil'
        );
    }
}
