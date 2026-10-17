<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureLimit
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Check if the tenant is within a feature's limit.
     *
     * Parameterized: feature:children, feature:photos, feature:videos, feature:storage,
     * feature:staff, feature:patients, feature:clinical_notes, feature:referrals
     *
     * Returns a JSON 422 response for API requests (expectsJson) and a
     * redirect-back with flash error for web requests.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = $this->tenantService->getCurrentTenant();

        if (! $tenant) {
            return $next($request);
        }

        $allowed = $this->tenantService->checkFeatureLimit($tenant, $feature);

        if (! $allowed) {
            $message = __('limits.'.$this->resolveLangKey($feature));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->with('error', $message);
        }

        return $next($request);
    }

    /**
     * Map the route feature parameter to its lang/{locale}/limits.php key.
     */
    private function resolveLangKey(string $feature): string
    {
        return match ($feature) {
            'add_child' => 'children',
            'upload_photo' => 'photos',
            'upload_video' => 'videos',
            'children', 'photos', 'videos', 'storage', 'family_members',
            'staff', 'patients', 'clinical_notes', 'referrals' => $feature,
            default => 'children',
        };
    }
}
