<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Plugin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PluginController extends Controller
{
    /**
     * Display a listing of all plugins.
     */
    public function index(): View
    {
        $plugins = Plugin::withCount('tenantPlugins')
            ->latest()
            ->paginate(20);

        return view('super-admin.plugins.index', compact('plugins'));
    }

    /**
     * Store a newly created plugin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:plugins,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'version' => ['required', 'string', 'max:20'],
            'author' => ['required', 'string', 'max:255'],
            'author_url' => ['nullable', 'url', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'hooks' => ['nullable', 'array'],
            'hooks.*' => ['string'],
            'routes' => ['nullable', 'array'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'is_active' => ['nullable', 'boolean'],
            'is_official' => ['nullable', 'boolean'],
        ]);

        Plugin::create($validated);

        return redirect()->route('super-admin.plugins.index')
            ->with('success', 'Plugin berhasil didaftarkan.');
    }

    /**
     * Display the specified plugin.
     */
    public function show(Plugin $plugin): View
    {
        $plugin->loadCount('tenantPlugins');

        $recentLogs = $plugin->pluginLogs()
            ->with('tenant')
            ->latest()
            ->limit(20)
            ->get();

        return view('super-admin.plugins.show', compact('plugin', 'recentLogs'));
    }

    /**
     * Update the specified plugin.
     */
    public function update(Request $request, Plugin $plugin): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'version' => ['sometimes', 'required', 'string', 'max:20'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'author_url' => ['nullable', 'url', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'hooks' => ['nullable', 'array'],
            'hooks.*' => ['string'],
            'routes' => ['nullable', 'array'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'is_active' => ['sometimes', 'boolean'],
            'is_official' => ['sometimes', 'boolean'],
        ]);

        $plugin->update($validated);

        return redirect()->route('super-admin.plugins.show', $plugin)
            ->with('success', 'Plugin berhasil diperbarui.');
    }

    /**
     * Remove the specified plugin (soft delete).
     */
    public function destroy(Plugin $plugin): RedirectResponse
    {
        $plugin->delete();

        return redirect()->route('super-admin.plugins.index')
            ->with('success', 'Plugin berhasil dihapus.');
    }
}
