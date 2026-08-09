@php
    $tenant = auth()->user() ? auth()->user()->tenant : null;
    $footerText = null;

    if ($tenant) {
        $cacheService = app(\App\Services\CacheService::class);
        $branding = $cacheService->getTenantBranding($tenant);
        if ($branding) {
            $footerText = $branding->footer_text;
        }
    }

    /**
     * Sanitize footer text to prevent XSS while allowing safe HTML tags.
     * Allowed tags: basic formatting (b, i, u, em, strong, a, br, span).
     */
    $allowedTags = '<b><i><u><em><strong><a><br><span>';
    $sanitizedFooter = $footerText ? strip_tags($footerText, $allowedTags) : null;
@endphp

<footer class="py-4 text-center text-sm text-gray-400 dark:text-gray-500 border-t border-gray-100 dark:border-gray-700 mt-auto">
    @if($sanitizedFooter)
        {!! $sanitizedFooter !!}
    @else
        &copy; {{ date('Y') }} {{ config('app.name', 'ForMysha') }}. Semua hak dilindungi.
    @endif
</footer>
