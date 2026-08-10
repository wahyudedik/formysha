<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('dashboard')],
                    ['label' => $child->name, 'url' => route('children.show', $child)],
                    ['label' => 'Pencapaian'],
                ]" />
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">🏆 Pencapaian</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pencapaian {{ $child->name }} dalam menggunakan ForMysha</p>
            </div>
            <form method="POST" action="{{ route('achievements.check', $child) }}">
                @csrf
                <button type="submit" class="btn-accent text-sm">
                    🔄 Periksa Pencapaian
                </button>
            </form>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-8">
        {{-- Progress Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Ringkasan Pencapaian</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $earnedCount }} dari {{ $totalCount }} pencapaian terbuka</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Progress Bar --}}
                    <div class="flex-1 sm:flex-initial w-full sm:w-48 bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-softPink-400 to-lavender-400 h-full rounded-full transition-all duration-500"
                             style="width: {{ $totalCount > 0 ? round(($earnedCount / $totalCount) * 100) : 0 }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-softPink-500 dark:text-softPink-400 whitespace-nowrap">
                        {{ $totalCount > 0 ? round(($earnedCount / $totalCount) * 100) : 0 }}%
                    </span>
                </div>
            </div>
        </div>

        {{-- Achievement Grid --}}
        @if(count($achievements) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($achievements as $achievement)
                    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-soft border transition-all duration-300
                                {{ $achievement['earned']
                                    ? 'border-warmYellow-200 dark:border-warmYellow-800 hover:shadow-soft-md'
                                    : 'border-gray-100 dark:border-gray-700 opacity-60' }}">
                        {{-- Earned Badge --}}
                        @if($achievement['earned'])
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-warmYellow-400 text-white rounded-full flex items-center justify-center text-sm shadow-md">
                                ✓
                            </div>
                        @endif

                        <div class="p-4 sm:p-6">
                            {{-- Icon --}}
                            <div class="text-4xl mb-3 {{ $achievement['earned'] ? '' : 'grayscale' }}">
                                {{ $achievement['icon'] }}
                            </div>

                            {{-- Name --}}
                            <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-1">
                                {{ $achievement['name'] }}
                            </h3>

                            {{-- Description --}}
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $achievement['description'] }}
                            </p>

                            {{-- Status --}}
                            <div class="mt-3">
                                @if($achievement['earned'])
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-700 dark:text-warmYellow-400">
                                        ✅ Terbuka
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                        🔒 Terkunci
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-empty-state
                icon="🏆"
                title="Belum Ada Pencapaian"
                description="Mulai mencatat kenangan, mengunggah foto, dan memantau pertumbuhan untuk membuka pencapaian pertama!"
                :actionUrl="route('timeline.create', $child)"
                actionText="Mulai Menulis Kenangan"
            />
        @endif
    </div>
</x-app-layout>
