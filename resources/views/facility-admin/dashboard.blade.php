<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                🏥 {{ __('Dashboard Fasilitas') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('facility.staff.index') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('Staf') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('facility-admin.partials.sidebar')
            <div class="flex-1 min-w-0">
        <!-- Welcome Section -->
        <div class="mb-6 bg-gradient-to-br from-skyBlue-50 to-mintGreen-50 dark:from-skyBlue-950/30 dark:to-mintGreen-950/30 rounded-3xl p-6 sm:p-8 border border-skyBlue-100 dark:border-skyBlue-900/30">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Selamat datang, ') }}{{ $tenant->name }} 🏥</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Kelola fasilitas kesehatan Anda dari sini.') }}</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Staff Count -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-xl">👨‍⚕️</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $usage['staff'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Staf Aktif') }}</p>
                    </div>
                </div>
            </div>

            <!-- Patient Links -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-xl">👶</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $usage['patients'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Tautan Pasien') }}</p>
                    </div>
                </div>
            </div>

            <!-- Clinical Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-mintGreen-100 dark:bg-mintGreen-950/30 flex items-center justify-center text-xl">📋</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $usage['clinical_notes'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Catatan Klinis') }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Referrals -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-warmYellow-100 dark:bg-warmYellow-950/30 flex items-center justify-center text-xl">🔄</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $pendingReferrals }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Rujukan Pending') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <a href="{{ route('facility.staff.create') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-lg group-hover:scale-110 transition">➕</div>
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ __('Tambah Staf') }}</span>
                </div>
            </a>

            <a href="{{ route('facility.patients.create') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-lg group-hover:scale-110 transition">👶</div>
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ __('Tambah Pasien') }}</span>
                </div>
            </a>

            <a href="{{ route('facility.clinical-notes.create') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-mintGreen-100 dark:bg-mintGreen-950/30 flex items-center justify-center text-lg group-hover:scale-110 transition">📝</div>
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ __('Catatan Klinis') }}</span>
                </div>
            </a>

            <a href="{{ route('facility.referrals.create') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-lavender-100 dark:bg-lavender-950/30 flex items-center justify-center text-lg group-hover:scale-110 transition">🔄</div>
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ __('Buat Rujukan') }}</span>
                </div>
            </a>

            <a href="{{ route('facility.patients.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-peach-100 dark:bg-peach-950/30 flex items-center justify-center text-lg group-hover:scale-110 transition">📋</div>
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ __('Daftar Pasien') }}</span>
                </div>
            </a>

            <a href="{{ route('facility.referrals.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-softOrange-100 dark:bg-softOrange-950/30 flex items-center justify-center text-lg group-hover:scale-110 transition">📨</div>
                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ __('Daftar Rujukan') }}</span>
                </div>
            </a>
        </div>

        <!-- Recent Patients -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-soft border border-gray-100 dark:border-gray-700 mb-6">
            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">👶 {{ __('Pasien Terbaru') }}</h3>
                    <a href="{{ route('facility.patients.index') }}" class="text-sm text-skyBlue-500 hover:text-skyBlue-600 dark:text-skyBlue-400 font-medium">{{ __('Lihat Semua') }}</a>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                @if ($recentPatients->isEmpty())
                    <div class="text-center py-8">
                        <div class="text-4xl mb-3">👶</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('empty_states.no_patients') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($recentPatients as $link)
                            <a href="{{ route('facility.patients.show', $link) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <div class="w-10 h-10 rounded-full bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-lg">👶</div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 dark:text-gray-100 truncate">{{ $link->child->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $link->parentUser->name ?? '-' }} · {{ $link->link_code }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $link->status->value === 'active' ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    {{ $link->status->label() }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Clinical Notes -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-soft border border-gray-100 dark:border-gray-700">
            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('Catatan Klinis Terbaru') }}</h3>
                    <a href="{{ route('facility.clinical-notes.index') }}" class="text-sm text-skyBlue-500 hover:text-skyBlue-600 dark:text-skyBlue-400 font-medium">{{ __('Lihat Semua') }}</a>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                @if ($recentClinicalNotes->isEmpty())
                    <div class="text-center py-8">
                        <div class="text-4xl mb-3">📋</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('empty_states.no_clinical_notes') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($recentClinicalNotes as $note)
                            <a href="{{ route('facility.clinical-notes.show', $note) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <div class="w-10 h-10 rounded-xl bg-mintGreen-100 dark:bg-mintGreen-950/30 flex items-center justify-center text-lg">📋</div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 dark:text-gray-100 truncate">{{ $note->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $note->child->name ?? '-' }} · {{ $note->staffUser->name ?? '-' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400">
                                    {{ $note->type->label() }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
            </div>
        </div>
    </div>
</x-app-layout>
