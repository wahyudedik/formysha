<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📥 {{ __('Import Data') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Enterprise'],
                    ['label' => 'Import'],
                ]" />

                {{-- Import Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Import Data Baru</h3>
                    <form x-data="{ uploading: false, progress: 0 }" @submit.prevent="
                        uploading = true;
                        const formData = new FormData();
                        formData.append('type', $refs.type.value);
                        formData.append('file', $refs.file.files[0]);
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route('enterprise.process-import') }}', {
                            method: 'POST',
                            headers: { 'Accept': 'application/json' },
                            body: formData
                        })
                        .then(r => r.json())
                        .then(data => {
                            uploading = false;
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Gagal membuat import job.');
                            }
                        })
                        .catch(err => {
                            uploading = false;
                            alert('Terjadi kesalahan.');
                        })
                    ">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Import</label>
                                <select
                                    x-ref="type"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm"
                                >
                                    <option value="photos">Foto</option>
                                    <option value="csv_data">CSV Data</option>
                                    <option value="backup_restore">Restore Backup</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File</label>
                                <input
                                    x-ref="file"
                                    type="file"
                                    required
                                    accept=".csv,.zip,.tar.gz,.jpg,.jpeg,.png"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-skyBlue-50 dark:file:bg-skyBlue-950/30 file:text-skyBlue-700 dark:file:text-skyBlue-400 hover:file:bg-skyBlue-100 dark:hover:file:bg-skyBlue-950/50"
                                />
                            </div>
                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    :disabled="uploading"
                                    class="w-full px-4 py-2 bg-gradient-to-r from-skyBlue-500 to-mintGreen-500 text-white rounded-xl font-medium text-sm hover:from-skyBlue-600 hover:to-mintGreen-600 transition-all disabled:opacity-50"
                                >
                                    <span x-show="!uploading">📥 Mulai Import</span>
                                    <span x-show="uploading">Mengupload...</span>
                                </button>
                            </div>
                        </div>
                        @if (uploading)
                            <div class="mt-4">
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-skyBlue-400 to-mintGreen-400 transition-all duration-500 animate-pulse" style="width: 100%"></div>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mengupload file...</p>
                            </div>
                        @endif
                    </form>
                </div>

                {{-- Import History --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Riwayat Import</h3>

                    @if ($jobs->isEmpty())
                        <div class="text-center py-8">
                            <div class="text-4xl mb-3">📭</div>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat import.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Tipe</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Progress</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobs as $job)
                                        <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="py-3 px-4">
                                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $job->type)) }}</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($job->status === 'completed')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400">
                                                        ✅ Selesai
                                                    </span>
                                                @elseif ($job->status === 'processing')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400">
                                                        ⏳ Diproses
                                                    </span>
                                                @elseif ($job->status === 'failed')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400">
                                                        ❌ Gagal
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        ⏸️ Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($job->total_items > 0)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-20 bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                                            <div
                                                                class="h-2 rounded-full bg-gradient-to-r from-skyBlue-400 to-mintGreen-400"
                                                                style="width: {{ $job->getProgressPercentage() }}%"
                                                            ></div>
                                                        </div>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $job->processed_items }}/{{ $job->total_items }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $job->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $jobs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
