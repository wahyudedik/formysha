<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Check if the tenant has an active subscription.
     * Skip for super_admin and tenant_admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip for super admin and tenant admin
        if ($user && in_array($user->role, ['super_admin', 'tenant_admin'])) {
            return $next($request);
        }

        $tenant = $this->tenantService->getCurrentTenant();

        if ($tenant && ! $this->tenantService->isSubscriptionActive($tenant)) {
            return redirect()->route('subscription.plans')
                ->with('warning', 'Anda perlu mengaktifkan paket berlangganan.');
        }

        return $next($request);
    }
}
