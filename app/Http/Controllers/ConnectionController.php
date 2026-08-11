<?php

namespace App\Http\Controllers;

use App\Enums\ConnectionPermission;
use App\Models\Child;
use App\Models\Connection;
use App\Models\Tenant;
use App\Services\ConnectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConnectionController extends Controller
{
    public function __construct(
        private ConnectionService $connectionService,
    ) {}

    /**
     * Display a listing of connections for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $connections = $this->connectionService->getByChild($child);

        return view('connections.index', [
            'child' => $child,
            'connections' => $connections,
        ]);
    }

    /**
     * Show the form for creating a new connection.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get();

        return view('connections.create', [
            'child' => $child,
            'children' => $children,
            'tenants' => $tenants,
            'permissions' => ConnectionPermission::options(),
        ]);
    }

    /**
     * Store a newly created connection in storage.
     */
    public function store(Request $request, Child $child): RedirectResponse
    {
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

        return redirect()->route('connections.index', $child)
            ->with('status', __('status.connection_created'));
    }

    /**
     * Display the specified connection.
     */
    public function show(Request $request, Child $child, Connection $connection): View
    {
        abort_unless($connection->child_id === $child->id, 403);

        $connection->load(['tenant', 'invitedBy']);
        $activities = $this->connectionService->getActivityHistory($connection);

        return view('connections.show', [
            'child' => $child,
            'connection' => $connection,
            'activities' => $activities,
        ]);
    }

    /**
     * Show the form for editing the specified connection.
     */
    public function edit(Request $request, Child $child, Connection $connection): View
    {
        abort_unless($connection->child_id === $child->id, 403);

        $connection->load(['tenant', 'invitedBy']);

        return view('connections.edit', [
            'child' => $child,
            'connection' => $connection,
            'permissions' => ConnectionPermission::options(),
        ]);
    }

    /**
     * Update the specified connection in storage.
     */
    public function update(Request $request, Child $child, Connection $connection): RedirectResponse
    {
        abort_unless($connection->child_id === $child->id, 403);

        $validated = $request->validate([
            'permission' => 'required|string|in:view,comment,edit,manage',
            'notes' => 'nullable|string|max:500',
        ]);

        $permission = ConnectionPermission::from($validated['permission']);

        $this->connectionService->updatePermission($connection, $permission);

        if (array_key_exists('notes', $validated)) {
            $connection->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('connections.show', [$child, $connection])
            ->with('status', __('status.connection_updated'));
    }

    /**
     * Remove the specified connection from storage.
     */
    public function destroy(Request $request, Child $child, Connection $connection): RedirectResponse
    {
        abort_unless($connection->child_id === $child->id, 403);

        $this->connectionService->logActivity(
            $connection,
            $request->user(),
            'connection.revoked',
            null,
            __('connection.revoked_by_owner')
        );

        $this->connectionService->revoke($connection);

        return redirect()->route('connections.index', $child)
            ->with('status', __('status.connection_revoked'));
    }

    /**
     * Approve a pending connection.
     */
    public function approve(Request $request, Child $child, Connection $connection): RedirectResponse
    {
        abort_unless($connection->child_id === $child->id, 403);
        abort_unless($connection->isPending(), 400);

        $this->connectionService->approve($connection);

        return redirect()->route('connections.show', [$child, $connection])
            ->with('status', __('status.connection_approved'));
    }

    /**
     * Reject a pending connection.
     */
    public function reject(Request $request, Child $child, Connection $connection): RedirectResponse
    {
        abort_unless($connection->child_id === $child->id, 403);
        abort_unless($connection->isPending(), 400);

        $this->connectionService->logActivity(
            $connection,
            $request->user(),
            'connection.rejected',
            null,
            __('connection.rejected_by_owner')
        );

        $this->connectionService->reject($connection);

        return redirect()->route('connections.index', $child)
            ->with('status', __('status.connection_rejected'));
    }

    /**
     * Revoke an active connection.
     */
    public function revoke(Request $request, Child $child, Connection $connection): RedirectResponse
    {
        abort_unless($connection->child_id === $child->id, 403);

        $this->connectionService->logActivity(
            $connection,
            $request->user(),
            'connection.revoked',
            null,
            __('connection.revoked_by_owner')
        );

        $this->connectionService->revoke($connection);

        return redirect()->route('connections.index', $child)
            ->with('status', __('status.connection_revoked'));
    }
}
