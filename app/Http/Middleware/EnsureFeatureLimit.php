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
     * Parameterized: feature:children, feature:photos, feature:videos, feature:storage
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = $this->tenantService->getCurrentTenant();

        if (! $tenant) {
            return $next($request);
        }

        $allowed = $this->tenantService->checkFeatureLimit($tenant, $feature);

        if (! $allowed) {
            $featureLabel = match ($feature) {
                'children', 'add_child' => 'jumlah anak',
                'photos', 'upload_photo' => 'unggah foto',
                'videos', 'upload_video' => 'unggah video',
                'storage' => 'penyimpanan',
                default => $feature,
            };

            return back()->with('error', "Batas {$featureLabel} telah tercapai. Silakan upgrade paket Anda.");
        }

        return $next($request);
    }
}
