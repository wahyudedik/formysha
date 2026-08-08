@php
    $tenant = auth()->user() ? auth()->user()->tenant : null;
    $faviconUrl = null;

    if ($tenant) {
        $branding = \App\Models\TenantBranding::where('tenant_id', $tenant->id)->first();
        if ($branding && $branding->favicon_path) {
            $faviconUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($branding->favicon_path);
        }
    }
@endphp

@if($faviconUrl)
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
@endif
