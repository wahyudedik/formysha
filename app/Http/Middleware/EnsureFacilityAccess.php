<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFacilityAccess
{
    /**
     * Check if the authenticated user has access to the current facility (tenant).
     *
     * Requires the user to be a staff member or tenant_admin of the tenant.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan masuk terlebih dahulu.');
        }

        $tenantId = $request->route('tenant')?->id ?? $request->user()?->tenant_id;

        if (! $tenantId) {
            abort(403, 'Anda tidak memiliki akses ke fasilitas ini.');
        }

        // Super admin has access to everything
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Tenant admin has access to their own tenant
        if ($user->role === 'tenant_admin' && $user->tenant_id === $tenantId) {
            return $next($request);
        }

        // Check if user is a staff member of this tenant
        $staff = Staff::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->first();

        if (! $staff) {
            abort(403, 'Anda tidak memiliki akses ke fasilitas ini.');
        }

        return $next($request);
    }
}
