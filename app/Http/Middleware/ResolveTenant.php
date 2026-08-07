<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Resolve tenant from URL segment, session, or user context.
     * Set app('tenant') singleton. If no tenant, set to null (for super admin).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant($request);

        if ($tenant && ! $tenant->is_active) {
            abort(403, 'Akun tenant tidak aktif.');
        }

        // Set tenant in service and as singleton
        $this->tenantService->switchTenant($tenant);
        app()->singleton('tenant', fn () => $tenant);

        return $next($request);
    }

    /**
     * Resolve the tenant from various sources.
     */
    private function resolveTenant(Request $request): ?Tenant
    {
        // 1. Check route parameter 'tenant'
        if ($tenantId = $request->route('tenant')) {
            return Tenant::find($tenantId);
        }

        // 2. Check session
        $sessionTenantId = $this->tenantService->getCurrentTenant()?->id;

        if ($sessionTenantId) {
            return Tenant::find($sessionTenantId);
        }

        // 3. Check user's tenant
        $user = $request->user();

        if ($user && $user->tenant_id) {
            return $user->tenant;
        }

        return null;
    }
}
