<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantBranding;
use Illuminate\Support\Facades\Storage;

class WhiteLabelService
{
    /**
     * Get the active branding for a tenant.
     */
    public function getActiveBranding(Tenant $tenant): ?TenantBranding
    {
        return TenantBranding::where('tenant_id', $tenant->id)->first();
    }

    /**
     * Apply branding styles to blade content.
     */
    public function applyBranding(string $bladeContent, Tenant $tenant): string
    {
        $branding = $this->getActiveBranding($tenant);

        if (! $branding || ! $branding->isWhiteLabel()) {
            return $bladeContent;
        }

        $customCss = $this->getCustomCss($tenant);
        if ($customCss) {
            $styleTag = '<style data-tenant-branding="true">'.PHP_EOL.$customCss.PHP_EOL.'</style>';
            $bladeContent = str_replace('</head>', $styleTag.PHP_EOL.'</head>', $bladeContent);
        }

        return $bladeContent;
    }

    /**
     * Get custom CSS for a tenant.
     */
    public function getCustomCss(Tenant $tenant): string
    {
        $branding = $this->getActiveBranding($tenant);

        if (! $branding) {
            return '';
        }

        $css = $branding->custom_css ?? '';

        // Generate CSS variables from colors
        $variables = [];
        if ($branding->primary_color) {
            $variables[] = '--brand-primary: '.$branding->primary_color.';';
        }
        if ($branding->secondary_color) {
            $variables[] = '--brand-secondary: '.$branding->secondary_color.';';
        }
        if ($branding->accent_color) {
            $variables[] = '--brand-accent: '.$branding->accent_color.';';
        }

        if ($variables !== []) {
            $css = ':root { '.implode(' ', $variables).' }'.PHP_EOL.$css;
        }

        return $css;
    }

    /**
     * Get the custom favicon path for a tenant.
     */
    public function getCustomFavicon(Tenant $tenant): ?string
    {
        $branding = $this->getActiveBranding($tenant);

        if (! $branding || ! $branding->favicon_path) {
            return null;
        }

        return Storage::disk('public')->url($branding->favicon_path);
    }

    /**
     * Get email sender configuration for a tenant.
     */
    public function getEmailSenderConfig(Tenant $tenant): array
    {
        $branding = $this->getActiveBranding($tenant);

        if (! $branding) {
            return [
                'name' => config('app.name', 'ForMysha'),
                'email' => config('mail.from.address', 'noreply@formysha.my.id'),
            ];
        }

        return [
            'name' => $branding->email_sender_name ?? config('app.name', 'ForMysha'),
            'email' => $branding->email_sender_email ?? config('mail.from.address', 'noreply@formysha.my.id'),
        ];
    }

    /**
     * Get custom footer text for a tenant.
     */
    public function getFooterText(Tenant $tenant): ?string
    {
        $branding = $this->getActiveBranding($tenant);

        if (! $branding) {
            return null;
        }

        return $branding->footer_text;
    }

    /**
     * Get login page customization for a tenant.
     */
    public function getLoginCustomization(Tenant $tenant): array
    {
        $branding = $this->getActiveBranding($tenant);

        if (! $branding) {
            return [
                'heading' => null,
                'subheading' => null,
                'logo_path' => null,
                'primary_color' => null,
                'custom_css' => null,
            ];
        }

        return [
            'heading' => $branding->login_heading,
            'subheading' => $branding->login_subheading,
            'logo_path' => $branding->logo_path
                ? Storage::disk('public')->url($branding->logo_path)
                : null,
            'primary_color' => $branding->primary_color,
            'custom_css' => $branding->custom_css,
        ];
    }
}
