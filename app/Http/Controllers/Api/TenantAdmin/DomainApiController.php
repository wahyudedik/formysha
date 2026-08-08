<?php

namespace App\Http\Controllers\Api\TenantAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Services\DomainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DomainApiController extends ApiController
{
    public function __construct(
        private readonly DomainService $domainService,
    ) {}

    /**
     * Show current domain settings.
     */
    public function show(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $status = $this->domainService->getDomainStatus($tenant);

        return $this->successResponse($status, 'Domain status berhasil diambil.');
    }

    /**
     * Set custom domain.
     */
    public function update(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'custom_domain' => ['required', 'string', 'max:255'],
        ]);

        $domain = $this->domainService->normalizeDomain($validated['custom_domain']);

        if ($this->domainService->isReservedDomain($domain)) {
            return $this->errorResponse('Domain ini adalah domain reserved dan tidak dapat digunakan.', 422);
        }

        if (! $this->domainService->isDomainAvailable($domain, $tenant->id)) {
            return $this->errorResponse('Domain ini sudah digunakan oleh tenant lain.', 422);
        }

        $this->domainService->setDomain($tenant, $domain);

        $status = $this->domainService->getDomainStatus($tenant->fresh());

        return $this->successResponse($status, 'Custom domain berhasil disimpan.');
    }

    /**
     * Verify DNS records.
     */
    public function verify(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        if (! $tenant->custom_domain) {
            return $this->errorResponse('Tidak ada custom domain yang dikonfigurasi.', 422);
        }

        $expectedIp = $this->domainService->getExpectedIp();
        $isVerified = $this->domainService->verifyDns($tenant->custom_domain, $expectedIp);

        if ($isVerified) {
            $this->domainService->markDomainVerified($tenant);

            return $this->successResponse(
                $this->domainService->getDomainStatus($tenant->fresh()),
                'Domain berhasil diverifikasi!'
            );
        }

        return $this->errorResponse('Verifikasi DNS gagal. Pastikan records sudah benar.', 422);
    }

    /**
     * Remove custom domain.
     */
    public function remove(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $this->domainService->removeDomain($tenant);

        return $this->successResponse(null, 'Custom domain berhasil dihapus.');
    }
}
