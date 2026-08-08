<?php

namespace App\Http\Controllers\Api\TenantAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\TenantBranding;
use App\Services\WhiteLabelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WhiteLabelController extends ApiController
{
    public function __construct(
        private readonly WhiteLabelService $whiteLabelService,
    ) {}

    /**
     * Get current tenant branding.
     */
    public function getBranding(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $branding = $this->whiteLabelService->getActiveBranding($tenant);

        if (! $branding) {
            return $this->errorResponse('Branding belum dikonfigurasi.', 404);
        }

        return $this->successResponse([
            'id' => $branding->id,
            'organization_name' => $branding->organization_name,
            'login_heading' => $branding->login_heading,
            'login_subheading' => $branding->login_subheading,
            'logo_path' => $branding->logo_path,
            'favicon_path' => $branding->favicon_path,
            'primary_color' => $branding->primary_color,
            'secondary_color' => $branding->secondary_color,
            'accent_color' => $branding->accent_color,
            'footer_text' => $branding->footer_text,
            'email_sender_name' => $branding->email_sender_name,
            'email_sender_email' => $branding->email_sender_email,
            'is_white_label_enabled' => $branding->is_white_label_enabled,
            'custom_css' => $branding->custom_css,
            'custom_domain' => $branding->custom_domain,
            'favicon_url' => $this->whiteLabelService->getCustomFavicon($tenant),
            'email_sender_config' => $this->whiteLabelService->getEmailSenderConfig($tenant),
            'footer_text_value' => $this->whiteLabelService->getFooterText($tenant),
            'login_customization' => $this->whiteLabelService->getLoginCustomization($tenant),
        ], 'Branding berhasil diambil.');
    }

    /**
     * Update branding settings.
     */
    public function updateBranding(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'login_heading' => ['nullable', 'string', 'max:255'],
            'login_subheading' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'secondary_color' => ['nullable', 'string', 'max:7'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'custom_css' => ['nullable', 'string', 'max:10000'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'email_sender_name' => ['nullable', 'string', 'max:100'],
            'email_sender_email' => ['nullable', 'email', 'max:100'],
            'is_white_label_enabled' => ['nullable', 'boolean'],
        ]);

        $branding = TenantBranding::firstOrCreate(
            ['tenant_id' => $tenant->id]
        );

        $branding->update($validated);

        return $this->successResponse($branding->toArray(), 'Branding berhasil diperbarui.');
    }

    /**
     * Upload custom favicon.
     */
    public function uploadFavicon(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $request->validate([
            'favicon' => ['required', 'image', 'max:512'],
        ]);

        $branding = TenantBranding::firstOrCreate(
            ['tenant_id' => $tenant->id]
        );

        // Delete old favicon
        if ($branding->favicon_path) {
            Storage::disk('public')->delete($branding->favicon_path);
        }

        $path = $request->file('favicon')->store('branding/'.$tenant->id, 'public');
        $branding->update(['favicon_path' => $path]);

        return $this->successResponse([
            'favicon_path' => $path,
            'favicon_url' => Storage::disk('public')->url($path),
        ], 'Favicon berhasil diunggah.');
    }

    /**
     * Preview custom CSS applied to tenant branding.
     */
    public function getCssPreview(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $css = $this->whiteLabelService->getCustomCss($tenant);

        return $this->successResponse([
            'css' => $css,
            'has_custom_css' => $css !== '',
        ], 'Preview CSS berhasil diambil.');
    }
}
