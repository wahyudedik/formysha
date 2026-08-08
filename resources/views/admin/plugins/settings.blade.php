<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ⚙️ Pengaturan: {{ $plugin->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Marketplace Plugin', 'url' => route('admin.plugins.index')],
                    ['label' => 'Pengaturan: ' . $plugin->name],
                ]" />

                <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-3xl">{{ $plugin->icon ?? '🧩' }}</span>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $plugin->name }}</h3>
                            <p class="text-sm text-gray-500">v{{ $plugin->version }} · {{ $plugin->author }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.plugins.settings.update', $plugin) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="settings" class="block text-sm font-medium text-gray-700 mb-1">Pengaturan (JSON)</label>
                            <textarea
                                id="settings"
                                name="settings"
                                rows="10"
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm font-mono"
                                placeholder='{"key": "value"}'
                            >{{ json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                            @error('settings')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="btn-primary text-sm">
                                💾 Simpan Pengaturan
                            </button>
                            <a href="{{ route('admin.plugins.index') }}" class="btn-secondary text-sm">
                                ← Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
