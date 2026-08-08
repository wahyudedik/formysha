<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🧩 Plugin Management
            </h2>
            <button
                @click="showCreateModal = true"
                class="btn-primary text-sm"
            >
                ➕ Daftarkan Plugin
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0" x-data="{ showCreateModal: false }">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Plugins'],
                ]" />

                <div class="bg-white rounded-2xl shadow-soft border border-gray-100">
                    @if($plugins->isEmpty())
                        <div class="p-8">
                            <x-empty-state
                                icon="🧩"
                                title="Belum ada plugin"
                                description="Daftarkan plugin pertama untuk mulai membangun marketplace."
                            />
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plugin</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Versi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Install</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($plugins as $plugin)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-2xl">{{ $plugin->icon ?? '🧩' }}</span>
                                                    <div>
                                                        <p class="font-semibold text-gray-800 text-sm">{{ $plugin->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $plugin->slug }}</p>
                                                    </div>
                                                    @if($plugin->is_official)
                                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-skyBlue-100 text-skyBlue-700">Resmi</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">v{{ $plugin->version }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $plugin->author }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $plugin->install_count }}</td>
                                            <td class="px-6 py-4">
                                                @if($plugin->is_active)
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-mintGreen-100 text-mintGreen-700">Aktif</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('super-admin.plugins.show', $plugin) }}" class="text-skyBlue-600 hover:text-skyBlue-800 text-sm font-medium">
                                                    Lihat →
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 border-t border-gray-100">
                            {{ $plugins->links() }}
                        </div>
                    @endif
                </div>

                {{-- Create Plugin Modal --}}
                <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showCreateModal = false"></div>
                        <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">➕ Daftarkan Plugin Baru</h3>

                            <form method="POST" action="{{ route('super-admin.plugins.store') }}">
                                @csrf

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                        <input type="text" name="name" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                        <input type="text" name="slug" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm" />
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="description" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Versi</label>
                                        <input type="text" name="version" required placeholder="1.0.0" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                                        <input type="text" name="author" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm" />
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (emoji)</label>
                                    <input type="text" name="icon" placeholder="🧩" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm" />
                                </div>

                                <div class="flex items-center gap-4 mb-4">
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-skyBlue-600 focus:ring-skyBlue-500" />
                                        Aktif
                                    </label>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="is_official" value="1" class="rounded border-gray-300 text-skyBlue-600 focus:ring-skyBlue-500" />
                                        Resmi
                                    </label>
                                </div>

                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showCreateModal = false" class="btn-secondary text-sm">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn-primary text-sm">
                                        💾 Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
