<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['label' => __('Catatan Klinis')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📋 {{ __('Catatan Klinis') }}
                </h2>
            </div>
            <a href="{{ route('facility.clinical-notes.create') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Catatan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                @include('facility-admin.partials.sidebar')
                <div class="flex-1 min-w-0">
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($clinicalNotes->isEmpty())
                        <x-empty-state
                            icon="📋"
                            :title="__('Belum Ada Catatan Klinis')"
                            :description="__('Mulai tambahkan catatan klinis untuk pasien.')"
                            :action-url="route('facility.clinical-notes.create')"
                            :action-text="__('Tambah Catatan Pertama')"
                        />
                    @else
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                            <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Pasien') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Judul') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tipe') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Staf') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aksi') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($clinicalNotes as $note)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-sm">
                                                            👶
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $note->child->name ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <p class="text-sm text-gray-800 dark:text-gray-100 max-w-xs truncate">{{ $note->title }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-lavender-100 dark:bg-lavender-950/30 text-lavender-700 dark:text-lavender-400">
                                                        {{ $note->type->label() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $note->staffUser->name ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $note->created_at->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a href="{{ route('facility.clinical-notes.show', $note) }}" class="text-skyBlue-500 hover:text-skyBlue-600 dark:text-skyBlue-400">{{ __('Lihat') }}</a>
                                                        <a href="{{ route('facility.clinical-notes.edit', $note) }}" class="text-warmYellow-500 hover:text-warmYellow-600 dark:text-warmYellow-400">{{ __('Edit') }}</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                                {{ $clinicalNotes->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
