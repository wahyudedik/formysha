<?php

namespace App\Http\Controllers\Api\TenantAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Plugin;
use App\Services\PluginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluginController extends ApiController
{
    public function __construct(
        private readonly PluginService $pluginService,
    ) {}

    /**
     * List available and installed plugins.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $availablePlugins = Plugin::active()->get()->map(function (Plugin $plugin) use ($tenant) {
            $installed = $this->pluginService->getTenantPlugins($tenant)
                ->firstWhere('plugin_id', $plugin->id);

            return [
                'id' => $plugin->id,
                'name' => $plugin->name,
                'slug' => $plugin->slug,
                'description' => $plugin->description,
                'version' => $plugin->version,
                'author' => $plugin->author,
                'icon' => $plugin->icon,
                'hooks' => $plugin->hooks,
                'is_official' => $plugin->is_official,
                'install_count' => $plugin->install_count,
                'is_installed' => (bool) $installed,
                'is_enabled' => $installed?->is_enabled ?? false,
            ];
        });

        return $this->successResponse($availablePlugins, 'Daftar plugin berhasil diambil.');
    }

    /**
     * Install a plugin.
     */
    public function install(Request $request, Plugin $plugin): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        if (! $plugin->is_active) {
            return $this->errorResponse('Plugin ini tidak aktif.', 422);
        }

        $tenantPlugin = $this->pluginService->installPlugin($tenant, $plugin);

        return $this->successResponse($tenantPlugin->toArray(), 'Plugin berhasil diinstall.', 201);
    }

    /**
     * Uninstall a plugin.
     */
    public function uninstall(Request $request, Plugin $plugin): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $result = $this->pluginService->uninstallPlugin($tenant, $plugin);

        if (! $result) {
            return $this->errorResponse('Plugin belum diinstall.', 422);
        }

        return $this->successResponse(null, 'Plugin berhasil diuninstall.');
    }

    /**
     * Toggle plugin enabled/disabled.
     */
    public function toggle(Request $request, Plugin $plugin): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $tenantPlugin = $this->pluginService->getTenantPlugins($tenant)
            ->firstWhere('plugin_id', $plugin->id);

        if (! $tenantPlugin) {
            return $this->errorResponse('Plugin belum diinstall.', 422);
        }

        if ($tenantPlugin->is_enabled) {
            $this->pluginService->disablePlugin($tenant, $plugin);
            $status = 'disabled';
        } else {
            $this->pluginService->enablePlugin($tenant, $plugin);
            $status = 'enabled';
        }

        return $this->successResponse(['status' => $status], "Plugin berhasil {$status}.");
    }

    /**
     * Get plugin settings.
     */
    public function settings(Request $request, Plugin $plugin): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $settings = $this->pluginService->getPluginSettings($tenant, $plugin);

        return $this->successResponse($settings, 'Pengaturan plugin berhasil diambil.');
    }

    /**
     * Save plugin settings.
     */
    public function updateSettings(Request $request, Plugin $plugin): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Tenant tidak ditemukan.', 404);
        }

        $validated = $request->validate([
            'settings' => ['nullable', 'array'],
        ]);

        $result = $this->pluginService->updatePluginSettings($tenant, $plugin, $validated['settings'] ?? []);

        if (! $result) {
            return $this->errorResponse('Plugin belum diinstall.', 422);
        }

        return $this->successResponse(null, 'Pengaturan plugin berhasil disimpan.');
    }
}
