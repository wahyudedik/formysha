@php
    $tenant = auth()->user() ? auth()->user()->tenant : null;
    $footerText = null;

    if ($tenant) {
        $branding = \App\Models\TenantBranding::where('tenant_id', $tenant->id)->first();
        if ($branding) {
            $footerText = $branding->footer_text;
        }
    }
@endphp

<footer class="py-4 text-center text-sm text-gray-400 border-t border-gray-100 mt-auto">
    @if($footerText)
        {!! $footerText !!}
    @else
        &copy; {{ date('Y') }} {{ config('app.name', 'ForMysha') }}. Semua hak dilindungi.
    @endif
</footer>
