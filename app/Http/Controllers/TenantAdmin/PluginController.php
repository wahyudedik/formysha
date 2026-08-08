<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Models\Plugin;
use App\Services\PluginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PluginController extends Controller
{
    public function __construct(
        private readonly PluginService $pluginService,
    ) {}

    /**
     * List available and installed plugins for the tenant.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $availablePlugins = Plugin::active()->get();

        $installedPlugins = $this->pluginService->getTenantPlugins($tenant);

        $installedPluginIds = $installedPlugins->pluck('plugin_id')->toArray();

        return view('admin.plugins.index', compact('tenant', 'availablePlugins', 'installedPlugins', 'installedPluginIds'));
    }

    /**
     * Install a plugin for the tenant.
     */
    public function install(Request $request, Plugin $plugin): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        if (! $plugin->is_active) {
            return back()->withErrors([
                'plugin' => 'Plugin ini tidak aktif.',
            ]);
        }

        $this->pluginService->installPlugin($tenant, $plugin);

        return redirect()->route('admin.plugins.index')
            ->with('success', "Plugin \"{$plugin->name}\" berhasil diinstall.");
    }

    /**
     * Uninstall a plugin from the tenant.
     */
    public function uninstall(Request $request, Plugin $plugin): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $result = $this->pluginService->uninstallPlugin($tenant, $plugin);

        if (! $result) {
            return back()->withErrors([
                'plugin' => 'Plugin belum diinstall.',
            ]);
        }

        return redirect()->route('admin.plugins.index')
            ->with('success', "Plugin \"{$plugin->name}\" berhasil diuninstall.");
    }

    /**
     * Toggle plugin enabled/disabled status.
     */
    public function toggle(Request $request, Plugin $plugin): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $tenantPlugin = $this->pluginService->getTenantPlugins($tenant)
            ->firstWhere('plugin_id', $plugin->id);

        if (! $tenantPlugin) {
            return back()->withErrors([
                'plugin' => 'Plugin belum diinstall.',
            ]);
        }

        if ($tenantPlugin->is_enabled) {
            $this->pluginService->disablePlugin($tenant, $plugin);
            $status = 'dinonaktifkan';
        } else {
            $this->pluginService->enablePlugin($tenant, $plugin);
            $status = 'diaktifkan';
        }

        return redirect()->route('admin.plugins.index')
            ->with('success', "Plugin \"{$plugin->name}\" berhasil {$status}.");
    }

    /**
     * Show plugin settings form.
     */
    public function settings(Request $request, Plugin $plugin): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $settings = $this->pluginService->getPluginSettings($tenant, $plugin);

        return view('admin.plugins.settings', compact('tenant', 'plugin', 'settings'));
    }

    /**
     * Save plugin settings.
     */
    public function updateSettings(Request $request, Plugin $plugin): RedirectResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'settings' => ['nullable', 'array'],
        ]);

        $this->pluginService->updatePluginSettings($tenant, $plugin, $validated['settings'] ?? []);

        return redirect()->route('admin.plugins.settings', $plugin)
            ->with('success', 'Pengaturan plugin berhasil disimpan.');
    }
}
