<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Dns;

class DomainService
{
    /**
     * Reserved domain prefixes that cannot be used as custom domains.
     */
    private const RESERVED_WORDS = [
        'api',
        'admin',
        'mail',
        'www',
        'ftp',
        'smtp',
        'imap',
        'pop',
        'ns1',
        'ns2',
        'dns',
        'mx',
        'cpanel',
        'webmail',
        'ftp',
        'staging',
        'dev',
        'test',
    ];

    /**
     * Verify DNS records for a custom domain.
     *
     * Checks that the domain has a valid A record or CNAME pointing to the expected IP.
     */
    public function verifyDns(string $domain, string $expectedIp): bool
    {
        try {
            $ips = dns_get_record($domain, DNS_A);
            foreach ($ips as $record) {
                if ($record['ip'] === $expectedIp) {
                    return true;
                }
            }

            $cnames = dns_get_record($domain, DNS_CNAME);
            foreach ($cnames as $record) {
                if (isset($record['target']) && $record['target'] === $expectedIp) {
                    return true;
                }
            }

            return false;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Check if a domain is available (not used by another tenant).
     */
    public function isDomainAvailable(string $domain, ?string $excludeTenantId = null): bool
    {
        $domain = $this->normalizeDomain($domain);

        $query = Tenant::where('custom_domain', $domain);

        if ($excludeTenantId) {
            $query->where('id', '!=', $excludeTenantId);
        }

        return ! $query->exists();
    }

    /**
     * Get tenant by custom domain.
     */
    public function getTenantByDomain(string $domain): ?Tenant
    {
        $domain = $this->normalizeDomain($domain);

        $cacheKey = 'tenant_domain:'.$domain;

        return Cache::tags(['tenant_domains'])->remember($cacheKey, 3600, function () use ($domain) {
            return Tenant::where('custom_domain', $domain)
                ->where('domain_dns_verified', true)
                ->where('is_active', true)
                ->first();
        });
    }

    /**
     * Normalize a domain string.
     *
     * Converts to lowercase, removes protocol, trailing slashes, and ports.
     */
    public function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = parse_url('https://'.$domain, PHP_URL_HOST) ?? $domain;
        $domain = rtrim($domain, '/');

        return $domain;
    }

    /**
     * Check if a domain is already in use (conflict check).
     */
    public function isDomainConflict(string $domain, ?string $currentTenantId = null): bool
    {
        return ! $this->isDomainAvailable($domain, $currentTenantId);
    }

    /**
     * Check if the domain is a reserved word.
     */
    public function isReservedDomain(string $domain): bool
    {
        $domain = $this->normalizeDomain($domain);
        $parts = explode('.', $domain);

        return in_array($parts[0], self::RESERVED_WORDS);
    }

    /**
     * Set custom domain for a tenant.
     */
    public function setDomain(Tenant $tenant, string $domain): Tenant
    {
        $domain = $this->normalizeDomain($domain);

        $tenant->update([
            'custom_domain' => $domain,
            'domain_verified_at' => null,
            'domain_dns_verified' => false,
        ]);

        $this->clearDomainCache($domain);

        return $tenant->fresh();
    }

    /**
     * Mark a domain as verified.
     */
    public function markDomainVerified(Tenant $tenant): Tenant
    {
        $tenant->update([
            'domain_verified_at' => now(),
            'domain_dns_verified' => true,
        ]);

        $this->clearDomainCache($tenant->custom_domain);

        return $tenant->fresh();
    }

    /**
     * Remove custom domain from a tenant.
     */
    public function removeDomain(Tenant $tenant): Tenant
    {
        $domain = $tenant->custom_domain;

        $tenant->update([
            'custom_domain' => null,
            'domain_verified_at' => null,
            'domain_dns_verified' => false,
        ]);

        if ($domain) {
            $this->clearDomainCache($domain);
        }

        return $tenant->fresh();
    }

    /**
     * Get the expected server IP for DNS verification.
     */
    public function getExpectedIp(): string
    {
        return config('services.domain.expected_ip', '127.0.0.1');
    }

    /**
     * Get domain status for a tenant.
     *
     * @return array{custom_domain: string|null, is_verified: bool, verified_at: string|null, dns_verified: bool}
     */
    public function getDomainStatus(Tenant $tenant): array
    {
        return [
            'custom_domain' => $tenant->custom_domain,
            'is_verified' => (bool) $tenant->domain_dns_verified,
            'verified_at' => $tenant->domain_verified_at?->toIso8601String(),
            'dns_verified' => (bool) $tenant->domain_dns_verified,
        ];
    }

    /**
     * Clear domain cache for a specific domain.
     */
    public function clearDomainCache(string $domain): void
    {
        $cacheKey = 'tenant_domain:'.$domain;
        Cache::tags(['tenant_domains'])->forget($cacheKey);
    }
}
