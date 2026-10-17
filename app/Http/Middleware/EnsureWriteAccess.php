<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWriteAccess
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Block write operations (create/update/delete) when the tenant's
     * subscription is not active. Read & download (GET/HEAD) are always
     * allowed so families never lose access to their own data.
     *
     * Web: redirect to the plans page with a flash notice.
     * API: JSON 402 response following the project's {success, message, data} format.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Read/download is always allowed
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return $next($request);
        }

        // Super admins bypass subscription checks
        $user = $request->user();
        if ($user && $user->role === 'super_admin') {
            return $next($request);
        }

        // Enforcement can be disabled (e.g. for testing)
        if (! config('saas.subscription.enforce_write_access', true)) {
            return $next($request);
        }

        $tenant = $this->tenantService->getCurrentTenant();

        // Users without a tenant are not subject to subscription checks
        if (! $tenant) {
            return $next($request);
        }

        if ($this->hasActiveSubscription($tenant)) {
            return $next($request);
        }

        $message = __('subscription.write_inactive_notice');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
            ], 402);
        }

        return redirect()->route('subscription.plans')
            ->with('subscription_notice', $message);
    }

    /**
     * A subscription counts as active only when its status is "active"
     * (the activeSubscription relation filters on status) and its
     * "ends_at" date — when present — has not passed yet.
     */
    private function hasActiveSubscription(Tenant $tenant): bool
    {
        $subscription = $tenant->activeSubscription;

        if (! $subscription) {
            return false;
        }

        if ($subscription->ends_at && $subscription->ends_at->isPast()) {
            return false;
        }

        return true;
    }
}
