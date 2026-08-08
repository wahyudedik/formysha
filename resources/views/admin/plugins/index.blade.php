<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🧩 Marketplace Plugin
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Marketplace Plugin'],
                ]" />

                {{-- Installed Plugins --}}
                <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📦 Plugin Terinstall</h3>

                    @if($installedPlugins->isEmpty())
                        <x-empty-state
                            icon="🧩"
                            title="Belum ada plugin terinstall"
                            description="Install plugin dari marketplace untuk menambah fitur ke tenant Anda."
                        />
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($installedPlugins as $tenantPlugin)
                                <div class="border border-gray-200 rounded-xl p-4 {{ $tenantPlugin->is_enabled ? 'bg-white' : 'bg-gray-50 opacity-75' }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">{{ $tenantPlugin->plugin->icon ?? '🧩' }}</span>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 text-sm">{{ $tenantPlugin->plugin->name }}</h4>
                                                <p class="text-xs text-gray-500">v{{ $tenantPlugin->plugin->version }}</p>
                                            </div>
                                        </div>
                                        @if($tenantPlugin->is_enabled)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-mintGreen-100 text-mintGreen-700">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $tenantPlugin->plugin->description ?? 'Tidak ada deskripsi.' }}</p>

                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.plugins.toggle', $tenantPlugin->plugin) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $tenantPlugin->is_enabled ? 'bg-warmYellow-100 text-warmYellow-700 hover:bg-warmYellow-200' : 'bg-mintGreen-100 text-mintGreen-700 hover:bg-mintGreen-200' }} transition-colors">
                                                {{ $tenantPlugin->is_enabled ? '⏸️ Nonaktifkan' : '▶️ Aktifkan' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.plugins.uninstall', $tenantPlugin->plugin) }}" onsubmit="return confirm('Apakah Anda yakin ingin menguninstall plugin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Available Plugins --}}
                <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">🏪 Tersedia di Marketplace</h3>

                    @if($availablePlugins->isEmpty())
                        <x-empty-state
                            icon="🏪"
                            title="Belum ada plugin tersedia"
                            description="Plugin akan muncul di sini setelah didaftarkan oleh Super Admin."
                        />
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availablePlugins as $plugin)
                                @php
                                    $isInstalled = in_array($plugin->id, $installedPluginIds);
                                @endphp
                                <div class="border border-gray-200 rounded-xl p-4 {{ $isInstalled ? 'bg-mintGreen-50 border-mintGreen-200' : 'bg-white' }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">{{ $plugin->icon ?? '🧩' }}</span>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 text-sm">{{ $plugin->name }}</h4>
                                                <p class="text-xs text-gray-500">v{{ $plugin->version }} · {{ $plugin->author }}</p>
                                            </div>
                                        </div>
                                        @if($plugin->is_official)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-skyBlue-100 text-skyBlue-700">Resmi</span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $plugin->description ?? 'Tidak ada deskripsi.' }}</p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">📥 {{ $plugin->install_count }} install</span>

                                        @if($isInstalled)
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-medium bg-mintGreen-100 text-mintGreen-700">
                                                ✅ Terinstall
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('admin.plugins.install', $plugin) }}">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-skyBlue-100 text-skyBlue-700 hover:bg-skyBlue-200 transition-colors">
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
