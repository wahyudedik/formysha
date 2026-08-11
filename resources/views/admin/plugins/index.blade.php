<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                🧩 Marketplace Plugin
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Marketplace Plugin'],
                ]" />

                {{-- Installed Plugins --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📦 Plugin Terinstall</h3>

                    @if($installedPlugins->isEmpty())
                        <x-empty-state
                            icon="🧩"
                            title="{{ __('empty_states.no_plugins_installed') }}"
                            description="{{ __('empty_states.no_plugins_installed_desc') }}"
                        />
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($installedPlugins as $tenantPlugin)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 {{ $tenantPlugin->is_enabled ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700/50 opacity-75' }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">{{ $tenantPlugin->plugin->icon ?? '🧩' }}</span>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $tenantPlugin->plugin->name }}</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">v{{ $tenantPlugin->plugin->version }}</p>
                                            </div>
                                        </div>
                                        @if($tenantPlugin->is_enabled)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">Nonaktif</span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">{{ $tenantPlugin->plugin->description ?? 'Tidak ada deskripsi.' }}</p>

                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.plugins.toggle', $tenantPlugin->plugin) }}">
                                            @csrf
                                            <button type="submit" class="min-h-[44px] px-3 py-1.5 rounded-lg text-xs font-medium {{ $tenantPlugin->is_enabled ? 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-700 dark:text-warmYellow-400 hover:bg-warmYellow-200 dark:hover:bg-warmYellow-950/50' : 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400 hover:bg-mintGreen-200 dark:hover:bg-mintGreen-950/50' }} transition-colors">
                                                {{ $tenantPlugin->is_enabled ? '⏸️ Nonaktifkan' : '▶️ Aktifkan' }}
                                            </button>
                                        </form>

                                        <button type="button"
                                            x-data
                                            x-on:click.prevent="$dispatch('delete-confirm', 'delete-plugin-{{ $tenantPlugin->plugin->id }}')"
                                            class="min-h-[44px] px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-950/50 transition-colors">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Delete Confirmation Modals --}}
                        @foreach($installedPlugins as $tenantPlugin)
                            <x-confirm-delete
                                id="delete-plugin-{{ $tenantPlugin->plugin->id }}"
                                title="{{ __('Hapus Plugin') }}"
                                message="{{ __('Apakah Anda yakin ingin menguninstall plugin') }} '{{ $tenantPlugin->plugin->name }}'? {{ __('Tindakan ini tidak dapat dibatalkan.') }}"
                                action="{{ route('admin.plugins.uninstall', $tenantPlugin->plugin) }}"
                            />
                        @endforeach
                    @endif
                </div>

                {{-- Available Plugins --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">🏪 Tersedia di Marketplace</h3>

                    @if($availablePlugins->isEmpty())
                        <x-empty-state
                            icon="🏪"
                            title="{{ __('empty_states.no_plugins_available') }}"
                            description="{{ __('empty_states.no_plugins_available_desc') }}"
                        />
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availablePlugins as $plugin)
                                @php
                                    $isInstalled = in_array($plugin->id, $installedPluginIds);
                                @endphp
                                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 {{ $isInstalled ? 'bg-mintGreen-50 dark:bg-mintGreen-950/20 border-mintGreen-200 dark:border-mintGreen-900/30' : 'bg-white dark:bg-gray-800' }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">{{ $plugin->icon ?? '🧩' }}</span>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $plugin->name }}</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">v{{ $plugin->version }} · {{ $plugin->author }}</p>
                                            </div>
                                        </div>
                                        @if($plugin->is_official)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400">Resmi</span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">{{ $plugin->description ?? 'Tidak ada deskripsi.' }}</p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">📥 {{ $plugin->install_count }} install</span>

                                        @if($isInstalled)
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-medium bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400">
                                                ✅ Terinstall
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('admin.plugins.install', $plugin) }}">
                                                @csrf
                                                <button type="submit" class="min-h-[44px] px-3 py-1.5 rounded-lg text-xs font-medium bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400 hover:bg-skyBlue-200 dark:hover:bg-skyBlue-950/50 transition-colors">
                                                    📥 Install
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
