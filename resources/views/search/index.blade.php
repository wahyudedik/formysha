<x-app-layout>
    <x-slot name="header">
        <x-page-header title="🔍 Pencarian" subtitle="Cari di seluruh modul ForMysha" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Search Form --}}
            <form action="{{ route('search.index') }}" method="GET" class="mb-8">
                <div class="relative">
                    <input type="text"
                           name="q"
                           value="{{ $query }}"
                           placeholder="Cari kenangan, dokumen, catatan kesehatan..."
                           class="w-full px-5 py-4 pl-12 text-sm bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-skyBlue-500 focus:border-skyBlue-500 shadow-sm transition dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200"
                           autofocus />
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-lg">
                        🔍
                    </div>
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 px-4 py-2 min-h-[44px] bg-skyBlue-500 text-white text-sm font-medium rounded-xl hover:bg-skyBlue-600 transition">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Filter Tabs --}}
            @if (strlen($query) >= 2)
                <div class="mb-6 flex flex-wrap gap-2">
                    @php
                        $modules = [
                            'all' => ['label' => 'Semua', 'icon' => '🔍'],
                            'timeline' => ['label' => 'Timeline', 'icon' => '📸'],
                            'diary' => ['label' => 'Diary', 'icon' => '📔'],
                            'document' => ['label' => 'Dokumen', 'icon' => '📄'],
                            'health' => ['label' => 'Kesehatan', 'icon' => '🏥'],
                            'growth' => ['label' => 'Pertumbuhan', 'icon' => '📏'],
                        ];
                    @endphp
                    @foreach ($modules as $moduleKey => $moduleInfo)
                        <a href="{{ route('search.index', array_merge(['q' => $query], $module === $moduleKey ? [] : ['module' => $moduleKey])) }}"
                           class="px-4 py-2 min-h-[44px] inline-flex items-center rounded-xl text-sm font-medium transition {{ $module === $moduleKey ? 'bg-skyBlue-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:border-gray-600' }}">
                            {{ $moduleInfo['icon'] }} {{ $moduleInfo['label'] }}
                            @if (isset($counts[$moduleKey]) && $counts[$moduleKey] > 0)
                                <span class="ml-1 text-xs opacity-75">({{ $counts[$moduleKey] }})</span>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Results --}}
                @if ($results->isEmpty())
                    <x-empty-state
                        icon="🔍"
                        title="Tidak Ada Hasil"
                        description="Tidak ditemukan hasil untuk '{{ $query }}'. Coba kata kunci yang berbeda."
                    />
                @else
                    <div class="space-y-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Ditemukan <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $module === 'all' ? $counts['all'] : $counts[$module] }}</span> hasil untuk "<span class="font-medium text-gray-700 dark:text-gray-200">{{ $query }}</span>"
                        </p>

                        @foreach ($results as $result)
                            <a href="{{ $result['url'] }}" class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $result['color'] }} flex items-center justify-center text-lg">
                                    {{ $result['icon'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $result['title'] }}</h3>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $result['child'] }}</span>
                                    </div>
                                    @if ($result['description'])
                                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-1">{{ $result['description'] }}</p>
                                    @endif
                                    @if ($result['date'])
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $result['date'] }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $result['color'] }}">
                                        {{ ucfirst($result['type']) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @else
                <x-empty-state
                    icon="🔍"
                    title="Mulai Mencari"
                    description="Ketik minimal 2 karakter untuk mulai mencari di seluruh modul ForMysha."
                />
            @endif
        </div>
    </div>
</x-app-layout>
