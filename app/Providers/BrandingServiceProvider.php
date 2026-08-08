<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BrandingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->registerBladeComponents();
    }

    /**
     * Register custom Blade directives.
     */
    private function registerBladeDirectives(): void
    {
        // @branding('key', 'default') — Get branding value with optional default
        Blade::directive('branding', function (string $expression): string {
            return "<?php
                \$__branding_key = {$expression};
                \$__branding_default = is_array(\$__branding_key) ? (\$__branding_key[1] ?? null) : null;
                \$__branding_key = is_array(\$__branding_key) ? \$__branding_key[0] : \$__branding_key;
                \$__branding_tenant = auth()->user() ? auth()->user()->tenant : null;
                \$__branding_value = null;
                if (\$__branding_tenant) {
                    \$__branding_data = \App\Models\TenantBranding::where('tenant_id', \$__branding_tenant->id)->first();
                    if (\$__branding_data) {
                        \$__branding_value = \$__branding_data->\$__branding_key ?? \$__branding_default;
                    }
                }
                echo \$__branding_value ?? \$__branding_default;
            ?>";
        });

        // @brandingCss — Output custom CSS if branding is enabled
        Blade::directive('brandingCss', function (): string {
            return "<?php
                \$__branding_tenant = auth()->user() ? auth()->user()->tenant : null;
                if (\$__branding_tenant) {
                    \$__branding_service = app(\App\Services\WhiteLabelService::class);
                    \$__branding_css = \$__branding_service->getCustomCss(\$__branding_tenant);
                    if (\$__branding_css) {
                        echo '<style data-tenant-branding=\"true\">'.PHP_EOL.\$__branding_css.PHP_EOL.'</style>';
                    }
                }
            ?>";
        });
    }

    /**
     * Register custom Blade components.
     */
    private function registerBladeComponents(): void
    {
        Blade::anonymousComponentPath(resource_path('views/components/branding'), 'branding');
    }
}
