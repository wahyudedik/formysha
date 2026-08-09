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

                    <form method="POST" action="{{ route('admin.plugins.settings.update', $plugin) }}">
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
                            <button type="submit" class="btn-primary text-sm min-h-[44px]">
                                💾 Simpan Pengaturan
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
