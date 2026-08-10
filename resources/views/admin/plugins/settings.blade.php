<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                ⚙️ Pengaturan: {{ $plugin->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Marketplace Plugin', 'url' => route('admin.plugins.index')],
                    ['label' => 'Pengaturan: ' . $plugin->name],
                ]" />

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-3xl">{{ $plugin->icon ?? '🧩' }}</span>
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ $plugin->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">v{{ $plugin->version }} · {{ $plugin->author }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.plugins.settings.update', $plugin) }}" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="settings" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pengaturan (JSON)</label>
                            <textarea
                                id="settings"
                                name="settings"
                                rows="10"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm font-mono"
                                placeholder='{"key": "value"}'
                            >{{ json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                            @error('settings')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="submit" x-bind:disabled="loading" class="btn-primary text-sm min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                                <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="loading ? 'Menyimpan...' : '💾 Simpan Pengaturan'"></span>
                            </button>
                            <a href="{{ route('admin.plugins.index') }}" class="btn-secondary text-sm min-h-[44px] inline-flex items-center">
                                ← Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
