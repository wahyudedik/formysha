<?php

namespace App\Http\Middleware;

use App\Enums\ConnectionPermission;
use App\Models\Child;
use App\Models\Connection;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConnectionPermission
{
    /**
     * Check if the authenticated user has the required connection permission level
     * for the child's data.
     *
     * Parameterized: connection.permission:view, connection.permission:comment,
     * connection.permission:edit, connection.permission:manage
     *
     * Logic:
     * 1. Super admin / tenant admin → bypass
     * 2. Child owner → full access
     * 3. Family member → check FamilyMemberPermission (existing)
     * 4. Organization staff → check ConnectionPermission via active connection
     * 5. Otherwise → 403
     */
    public function handle(Request $request, Closure $next, string $requiredLevel): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan masuk terlebih dahulu.');
        }

        // Super admin and tenant admin bypass permission check
        if (in_array($user->role, ['super_admin', 'tenant_admin'])) {
            return $next($request);
        }

        /** @var Child|null $child */
        $child = $request->route('child');

        if (! $child) {
            return $next($request);
        }

        // If user owns the child, they have full access
        if ($child->user_id === $user->id) {
            return $next($request);
        }

        // Check if the user is a family member (delegated to EnsureFamilyPermission for family context)
        // Here we focus on connection-based access for organization staff

        $required = ConnectionPermission::tryFrom($requiredLevel);

        if (! $required) {
            abort(400, 'Level permission tidak valid.');
        }

        // Find active connection where the user's tenant is connected to the child
        $tenantId = $user->tenant_id;

        if (! $tenantId) {
            // Try to find tenant from staff membership
            $staff = $user->staff;
            if ($staff) {
                $tenantId = $staff->tenant_id;
            }
        }

        if (! $tenantId) {
            abort(403, 'Anda tidak memiliki akses ke data anak ini.');
        }

        $connection = Connection::where('child_id', $child->id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();

        if (! $connection) {
            abort(403, 'Tidak ada koneksi aktif ke data anak ini.');
        }

        if (! $connection->hasPermission($required)) {
            abort(403, 'Permission tidak cukup. Diperlukan: '.$required->label());
        }

        // Attach connection to request for downstream use
        $request->attributes->set('connection', $connection);

        return $next($request);
    }
}
