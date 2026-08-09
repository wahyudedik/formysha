<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                🧩 {{ $plugin->name }}
            </h2>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('super-admin.plugins.destroy', $plugin) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus plugin ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-medium hover:bg-red-600 transition-colors">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Plugins', 'url' => route('super-admin.plugins.index')],
                    ['label' => $plugin->name],
                ]" />

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Plugin Info --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="text-4xl">{{ $plugin->icon ?? '🧩' }}</span>
                                <div>
                                    <h3 class="font-bold text-gray-800 dark:text-gray-100 text-xl">{{ $plugin->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">v{{ $plugin->version }} · {{ $plugin->author }}</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Deskripsi</h4>
                                <p class="text-sm text-gray-800 dark:text-gray-100">{{ $plugin->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>

                            @if($plugin->hooks)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Hooks</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($plugin->hooks as $hook)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-lavender-100 text-lavender-700 dark:bg-lavender-950/30 dark:text-lavender-400">{{ $hook }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($plugin->permissions)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Permissions</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($plugin->permissions as $permission)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-warmYellow-100 text-warmYellow-700 dark:bg-warmYellow-950/30 dark:text-warmYellow-400">{{ $permission }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Recent Logs --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700 mt-6">
                            <h4 class="font-bold text-gray-800 dark:text-gray-100 mb-4">📋 Log Aktivitas Terbaru</h4>

                            @if($recentLogs->isEmpty())
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada log aktivitas.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($recentLogs as $log)
                                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                            <span class="text-sm mt-0.5">
                                                @if($log->action === 'install') 📥
                                                @elseif($log->action === 'uninstall') 🗑️
                                                @elseif($log->action === 'enable') ▶️
                                                @elseif($log->action === 'disable') ⏸️
                                                @elseif($log->action === 'error') ❌
                                                @else 📝
                                                @endif
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 dark:text-gray-100">{{ $log->message ?? $log->action }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $log->tenant?->name ?? '-' }} · {{ $log->created_at->format('d M Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Sidebar Stats --}}
                    <div>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="font-bold text-gray-800 dark:text-gray-100 mb-4">📊 Statistik</h4>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Status</span>
                                    @if($plugin->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-mintGreen-100 text-mintGreen-700 dark:bg-mintGreen-950/30 dark:text-mintGreen-400">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Nonaktif</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Resmi</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plugin->is_official ? '✅ Ya' : '❌ Tidak' }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Install</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plugin->install_count }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Tenant Plugins</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plugin->tenant_plugins_count }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Dibuat</span>
                                    <span class="text-sm text-gray-800 dark:text-gray-100">{{ $plugin->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
