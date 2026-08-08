<?php

namespace App\Http\Middleware;

use App\Services\DomainService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantByDomain
{
    public function __construct(
        private readonly DomainService $domainService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * Check if the request hostname matches a tenant's custom_domain.
     * If a match is found, resolve the tenant and set it in the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Skip for the main application domain
        $appDomain = config('app.domain', '');
        if ($host === $appDomain || $host === '') {
            return $next($request);
        }

        // Resolve tenant from custom domain
        $tenant = $this->domainService->getTenantByDomain($host);

        if (! $tenant) {
            abort(404, 'Domain tidak dikenali.');
        }

        // Set tenant in request attributes for downstream use
        $request->attributes->set('tenant_id', $tenant->id);
        $request->attributes->set('tenant', $tenant);

        // Also set in session for web routes
        session()->put('tenant_id', $tenant->id);

        return $next($request);
    }
}
