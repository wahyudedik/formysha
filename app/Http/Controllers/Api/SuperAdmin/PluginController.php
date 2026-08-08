<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Plugin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PluginController extends ApiController
{
    /**
     * List all plugins.
     */
    public function index(Request $request): JsonResponse
    {
        $plugins = Plugin::withCount('tenantPlugins')
            ->latest()
            ->paginate(20);

        return $this->paginatedResponse($plugins, 'Daftar plugin berhasil diambil.');
    }

    /**
     * Store a newly created plugin.
     */
    public function store(Request $request): JsonResponse
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

        $plugin = Plugin::create($validated);

        return $this->successResponse($plugin->toArray(), 'Plugin berhasil didaftarkan.', 201);
    }

    /**
     * Display the specified plugin.
     */
    public function show(Plugin $plugin): JsonResponse
    {
        $plugin->loadCount('tenantPlugins');

        return $this->successResponse($plugin->toArray(), 'Plugin berhasil diambil.');
    }

    /**
     * Update the specified plugin.
     */
    public function update(Request $request, Plugin $plugin): JsonResponse
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

        return $this->successResponse($plugin->fresh()->toArray(), 'Plugin berhasil diperbarui.');
    }

    /**
     * Remove the specified plugin (soft delete).
     */
    public function destroy(Plugin $plugin): JsonResponse
    {
        $plugin->delete();

        return $this->successResponse(null, 'Plugin berhasil dihapus.');
    }
}
