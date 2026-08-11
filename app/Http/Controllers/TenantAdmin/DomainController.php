<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Services\DomainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function __construct(
        private readonly DomainService $domainService,
    ) {}

    /**
     * Tampilkan halaman pengaturan custom domain.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $domainStatus = $this->domainService->getDomainStatus($tenant);

        return view('admin.domain.index', compact('tenant', 'domainStatus'));
    }

    /**
     * Set custom domain untuk tenant.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'custom_domain' => ['required', 'string', 'max:255'],
        ]);

        $domain = $validated['custom_domain'];

        // Normalize the domain
        $domain = $this->domainService->normalizeDomain($domain);

        // Check for reserved words
        if ($this->domainService->isReservedDomain($domain)) {
            return back()->withErrors([
                'custom_domain' => 'Domain ini adalah domain reserved dan tidak dapat digunakan.',
            ]);
        }

        // Check if domain is already in use by another tenant
        if (! $this->domainService->isDomainAvailable($domain, $tenant->id)) {
            return back()->withErrors([
                'custom_domain' => 'Domain ini sudah digunakan oleh tenant lain.',
            ]);
        }

        $this->domainService->setDomain($tenant, $domain);

        return redirect()->route('admin.domain.index')
            ->with('status', __('status.domain_saved'));
    }

    /**
     * Verifikasi DNS records untuk custom domain.
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        if (! $tenant->custom_domain) {
            return back()->withErrors([
                'domain' => 'Tidak ada custom domain yang dikonfigurasi.',
            ]);
        }

        $expectedIp = $this->domainService->getExpectedIp();
        $isVerified = $this->domainService->verifyDns($tenant->custom_domain, $expectedIp);

        if ($isVerified) {
            $this->domainService->markDomainVerified($tenant);

            return redirect()->route('admin.domain.index')
                ->with('status', __('status.domain_verified'));
        }

        return redirect()->route('admin.domain.index')
            ->withErrors([
                'domain' => 'Verifikasi DNS gagal. Pastikan records sudah benar dan sudah beberapa menit sebelum dicoba lagi.',
            ]);
    }

    /**
     * Hapus custom domain dari tenant.
     */
    public function remove(Request $request): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $this->domainService->removeDomain($tenant);

        return redirect()->route('admin.domain.index')
            ->with('status', __('status.domain_deleted'));
    }
}
