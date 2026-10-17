<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantStatus
{
    /**
     * Block access when the user's tenant is suspended (is_active = false).
     *
     * Super admins and users without a tenant always pass.
     * Web: 403 abort with an Indonesian message.
     * API: JSON 403 response following the project's {success, message, data} format.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super admins always pass
        if ($user && $user->role === 'super_admin') {
            return $next($request);
        }

        // Users without a tenant always pass
        if (! $user || ! $user->tenant_id) {
            return $next($request);
        }

        $tenant = Tenant::find($user->tenant_id);

        if ($tenant && ! $tenant->is_active) {
            $message = __('common.tenant_suspended');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data' => null,
                ], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
