<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('dashboard')],
                    ['label' => $child->name, 'url' => route('children.show', $child)],
                    ['label' => 'Kesehatan'],
                ]" />
                <x-page-header title="Kesehatan" subtitle="Riwayat kesehatan {{ $child->name }}" />
            </div>
            <a href="{{ route('health.create', $child) }}" class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-skyBlue-500 text-white rounded-xl hover:bg-skyBlue-600 transition font-medium text-sm min-h-[44px]">
                ➕ Tambah Catatan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <x-child-nav :child="$child" active="health" />

            {{-- Filter Tabs --}}
            <div class="mb-6 flex flex-wrap gap-2">
                <a href="{{ route('health.index', $child) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition {{ !$activeType ? 'bg-skyBlue-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600' }}">
                    Semua
                </a>
                @php
                    $types = [
                        'immunization' => ['label' => 'Imunisasi', 'icon' => '💉'],
                        'illness' => ['label' => 'Penyakit', 'icon' => '🤒'],
                        'medication' => ['label' => 'Obat', 'icon' => '💊'],
                        'allergy' => ['label' => 'Alergi', 'icon' => '⚠️'],
                        'checkup' => ['label' => 'Pemeriksaan', 'icon' => '🩺'],
                        'other' => ['label' => 'Lainnya', 'icon' => '📋'],
                    ];
                @endphp
                @foreach ($types as $typeKey => $typeInfo)
                    <a href="{{ route('health.index', array_merge(['child' => $child], $activeType === $typeKey ? [] : ['type' => $typeKey])) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $activeType === $typeKey ? 'bg-skyBlue-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600' }}">
                        {{ $typeInfo['icon'] }} {{ $typeInfo['label'] }}
                        @if (isset($typeCounts[$typeKey]))
                            <span class="ml-1 text-xs opacity-75">({{ $typeCounts[$typeKey] }})</span>
                        @endif
                    </a>
                @endforeach
            </div>

            @if ($healthRecords->isEmpty())
                <x-empty-state
                    icon="🏥"
                    title="Belum Ada Catatan Kesehatan"
                    description="Mulai catat riwayat kesehatan {{ $child->name }}, seperti imunisasi, riwayat penyakit, dan pemeriksaan rutin."
                >
                    <x-primary-button tag="a" href="{{ route('health.create', $child) }}">
                        ➕ Tambah Catatan Kesehatan
                    </x-primary-button>
                </x-empty-state>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach ($healthRecords as $record)
                            <a href="{{ route('health.show', [$child, $record]) }}" class="flex items-start gap-3 sm:gap-4 p-4 sm:p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl {{ $record->type_color }} flex items-center justify-center text-lg sm:text-xl">
                                    {{ $record->type_icon }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $record->name }}</h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $record->type_color }}">
                                            {{ $record->type_label }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record->formatted_date }}</p>
                                    @if ($record->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-1">{{ $record->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        @if ($record->doctor)
                                            <span>🩺 {{ $record->doctor }}</span>
                                        @endif
                                        @if ($record->hospital)
                                            <span>🏥 {{ $record->hospital }}</span>
                                        @endif
                                    </div>
                                    @if ($record->next_date)
                                        <div class="mt-1 text-xs text-warmYellow-600 dark:text-warmYellow-400 font-medium">
                                            📅 Jadwal berikutnya: {{ $record->formatted_next_date }}
                                        </div>
                                    @endif
                                </div>
                                <div class="shrink-0 flex items-center gap-1">
                                    <a href="{{ route('health.edit', [$child, $record]) }}" class="inline-flex items-center justify-center w-9 h-9 text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 hover:bg-skyBlue-50 dark:hover:bg-skyBlue-950/30 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('health.destroy', [$child, $record]) }}" x-data>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition" title="Hapus" x-on:click.prevent="if(confirm('Yakin ingin menghapus catatan kesehatan ini?')) $el.closest('form').submit()">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6">
                    {{ $healthRecords->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
