<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.clinical-notes.index'), 'label' => __('Catatan Klinis')],
                    ['label' => __('Detail Catatan')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📋 {{ __('Detail Catatan Klinis') }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('facility.clinical-notes.edit', $clinicalNote) }}" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-warmYellow-500 hover:bg-warmYellow-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
                <button type="button" @click="$dispatch('delete-confirm', { id: 'delete-clinical-note-{{ $clinicalNote->id }}' })" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                    {{ __('Hapus') }}
                </button>
            </div>
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

                    <!-- Header Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $clinicalNote->title }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-lavender-100 dark:bg-lavender-950/30 text-lavender-700 dark:text-lavender-400">
                                        {{ $clinicalNote->type->label() }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $clinicalNote->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Patient & Staff Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Pasien') }}</h4>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-lg">
                                    👶
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $clinicalNote->child->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $clinicalNote->child->date_of_birth?->format('d M Y') ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Staf Penulis') }}</h4>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-lg">
                                    👨‍⚕️
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $clinicalNote->staffUser->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $clinicalNote->staffUser->email ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Isi Catatan') }}</h4>
                        <div class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $clinicalNote->content }}</div>
                    </div>

                    <!-- Diagnosis -->
                    @if ($clinicalNote->diagnosis)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Diagnosis') }}</h4>
                            <p class="text-gray-800 dark:text-gray-200">{{ $clinicalNote->diagnosis }}</p>
                        </div>
                    @endif

                    <!-- Vitals -->
                    @if ($clinicalNote->vitals && count($clinicalNote->vitals) > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Vital Signs') }}</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($clinicalNote->vitals as $key => $value)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $value }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Medications -->
                    @if ($clinicalNote->medications && count($clinicalNote->medications) > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Obat') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($clinicalNote->medications as $medication)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400">
                                        {{ $medication }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-confirm-delete
        id="delete-clinical-note-{{ $clinicalNote->id }}"
        :title="__('Hapus Catatan Klinis')"
        :message="__('Yakin ingin menghapus catatan klinis ini? Tindakan ini tidak dapat dibatalkan.')"
        :action="route('facility.clinical-notes.destroy', $clinicalNote)"
        method="DELETE"
    />
</x-app-layout>
