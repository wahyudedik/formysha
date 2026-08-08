<?php

namespace App\Services;

use App\Models\Plugin;
use App\Models\PluginLog;
use App\Models\Tenant;
use App\Models\TenantPlugin;
use Illuminate\Support\Collection;

class PluginService
{
    /**
     * Available hooks that plugins can register to.
     *
     * @var array<int, string>
     */
    const AVAILABLE_HOOKS = [
        'before.login',
        'after.login',
        'before.register',
        'after.register',
        'before.create.child',
        'after.create.child',
        'before.create.timeline',
        'after.create.timeline',
        'before.create.diary',
        'after.create.diary',
        'before.upload.media',
        'after.upload.media',
        'dashboard.widgets',
        'navigation.items',
        'settings.sections',
        'export.formats',
    ];

    /**
     * Get all available (active) plugins.
     */
    public function getAvailablePlugins(): Collection
    {
        return Plugin::active()->get();
    }

    /**
     * Install a plugin for a tenant.
     */
    public function installPlugin(Tenant $tenant, Plugin $plugin): TenantPlugin
    {
        $existing = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $tenantPlugin = TenantPlugin::create([
            'tenant_id' => $tenant->id,
            'plugin_id' => $plugin->id,
            'is_enabled' => true,
            'settings' => null,
            'installed_at' => now(),
        ]);

        $plugin->increment('install_count');

        $this->logAction($tenant, $plugin, 'install', 'Plugin berhasil diinstall.');

        return $tenantPlugin;
    }

    /**
     * Uninstall a plugin from a tenant.
     */
    public function uninstallPlugin(Tenant $tenant, Plugin $plugin): bool
    {
        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        if (! $tenantPlugin) {
            return false;
        }

        $tenantPlugin->delete();

        if ($plugin->install_count > 0) {
            $plugin->decrement('install_count');
        }

        $this->logAction($tenant, $plugin, 'uninstall', 'Plugin berhasil diuninstall.');

        return true;
    }

    /**
     * Enable a plugin for a tenant.
     */
    public function enablePlugin(Tenant $tenant, Plugin $plugin): bool
    {
        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        if (! $tenantPlugin) {
            return false;
        }

        $tenantPlugin->update(['is_enabled' => true]);

        $this->logAction($tenant, $plugin, 'enable', 'Plugin berhasil diaktifkan.');

        return true;
    }

    /**
     * Disable a plugin for a tenant.
     */
    public function disablePlugin(Tenant $tenant, Plugin $plugin): bool
    {
        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        if (! $tenantPlugin) {
            return false;
        }

        $tenantPlugin->update(['is_enabled' => false]);

        $this->logAction($tenant, $plugin, 'disable', 'Plugin berhasil dinonaktifkan.');

        return true;
    }

    /**
     * Get all installed plugins for a tenant.
     */
    public function getTenantPlugins(Tenant $tenant): Collection
    {
        return TenantPlugin::where('tenant_id', $tenant->id)
            ->with('plugin')
            ->get();
    }

    /**
     * Update plugin settings for a tenant.
     */
    public function updatePluginSettings(Tenant $tenant, Plugin $plugin, array $settings): bool
    {
        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        if (! $tenantPlugin) {
            return false;
        }

        $tenantPlugin->update(['settings' => $settings]);

        $this->logAction($tenant, $plugin, 'settings_updated', 'Pengaturan plugin berhasil diperbarui.');

        return true;
    }

    /**
     * Get plugin settings for a tenant.
     */
    public function getPluginSettings(Tenant $tenant, Plugin $plugin): array
    {
        $tenantPlugin = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('plugin_id', $plugin->id)
            ->first();

        return $tenantPlugin?->settings ?? [];
    }

    /**
     * Fire a hook and collect results from all enabled plugins.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, mixed>
     */
    public function fireHook(string $hook, Tenant $tenant, array $data = []): array
    {
        if (! in_array($hook, self::AVAILABLE_HOOKS)) {
            return [];
        }

        $tenantPlugins = TenantPlugin::where('tenant_id', $tenant->id)
            ->where('is_enabled', true)
            ->with('plugin')
            ->get()
            ->filter(fn (TenantPlugin $tp) => in_array($hook, $tp->plugin->hooks ?? []));

        $results = [];
        foreach ($tenantPlugins as $tenantPlugin) {
            $results[] = [
                'plugin_id' => $tenantPlugin->plugin_id,
                'plugin_slug' => $tenantPlugin->plugin->slug,
                'hook' => $hook,
                'data' => $data,
                'settings' => $tenantPlugin->settings ?? [],
            ];
        }

        return $results;
    }

    /**
     * Log a plugin action.
     */
    public function logAction(Tenant $tenant, Plugin $plugin, string $action, ?string $message = null): void
    {
        PluginLog::create([
            'tenant_id' => $tenant->id,
            'plugin_id' => $plugin->id,
            'action' => $action,
            'message' => $message,
        ]);
    }

    /**
     * Get plugin logs for a tenant.
     */
    public function getPluginLogs(Tenant $tenant, int $limit = 50): Collection
    {
        return PluginLog::where('tenant_id', $tenant->id)
            ->with('plugin')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
