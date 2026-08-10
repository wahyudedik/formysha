@props(['child'])

@php
    $modules = [
        ['route' => 'children.show', 'label' => 'Profil', 'icon' => '👶', 'param' => 'child'],
        ['route' => 'timeline.index', 'label' => 'Timeline', 'icon' => '📸', 'param' => 'child'],
        ['route' => 'albums.index', 'label' => 'Album', 'icon' => '🖼️', 'param' => 'child'],
        ['route' => 'diaries.index', 'label' => 'Diary', 'icon' => '📔', 'param' => 'child'],
        ['route' => 'growth.index', 'label' => 'Pertumbuhan', 'icon' => '📏', 'param' => 'child'],
        ['route' => 'health.index', 'label' => 'Kesehatan', 'icon' => '🏥', 'param' => 'child'],
        ['route' => 'documents.index', 'label' => 'Dokumen', 'icon' => '📄', 'param' => 'child'],
        ['route' => 'calendar.index', 'label' => 'Kalender', 'icon' => '📅', 'param' => 'child'],
        ['route' => 'achievements.index', 'label' => 'Pencapaian', 'icon' => '🏆', 'param' => 'child'],
        ['route' => 'milestones.index', 'label' => 'Milestone', 'icon' => '🎯', 'param' => 'child'],
        ['route' => 'family.index', 'label' => 'Keluarga', 'icon' => '👨‍👩‍👧‍👦', 'param' => 'child'],
        ['route' => 'profile.edit', 'label' => 'Pengaturan', 'icon' => '⚙️', 'param' => null],
    ];

    $visibleModules = array_slice($modules, 0, 5);
    $overflowModules = array_slice($modules, 5);
    $hasOverflowActive = collect($overflowModules)->contains(fn ($m) => request()->routeIs($m['route']));
@endphp

<!-- Desktop Sidebar Navigation -->
<aside class="hidden lg:block w-56 shrink-0">
    <div class="sticky top-24 space-y-1">
        {{-- Dashboard Back Link --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 mb-2">
            <span class="text-lg">🏠</span>
            Dashboard
        </a>

        <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>

        @foreach ($modules as $module)
            @php
                $isActive = request()->routeIs($module['route']);
            @endphp
            <a href="{{ $module['param'] === null ? route($module['route']) : route($module['route'], $child) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                      {{ $isActive
                          ? 'bg-softPink-50 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400 shadow-soft'
                          : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                <span class="text-lg">{{ $module['icon'] }}</span>
                {{ $module['label'] }}
            </a>
        @endforeach
    </div>
</aside>

<!-- Mobile Bottom Navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-t border-gray-100 dark:border-gray-700 z-50 safe-bottom"
     x-data="{ moreOpen: false }" @click.outside="moreOpen = false">
    <div class="flex items-center justify-around py-2 px-1 safe-bottom">
        @foreach ($visibleModules as $module)
            @php
                $isActive = request()->routeIs($module['route']);
            @endphp
            <a href="{{ route($module['route'], $child) }}"
               class="flex flex-col items-center gap-0.5 px-2 py-1 min-h-[44px] rounded-xl text-xs transition-all duration-200
                      {{ $isActive
                          ? 'text-softPink-600 dark:text-softPink-400'
                          : 'text-gray-400 dark:text-gray-500' }}">
                <span class="text-lg leading-none">{{ $module['icon'] }}</span>
                <span class="font-medium leading-tight">{{ $module['label'] }}</span>
            </a>
        @endforeach

        <!-- More / Overflow Button -->
        <button @click="moreOpen = !moreOpen"
                class="flex flex-col items-center gap-0.5 px-2 py-1 min-h-[44px] rounded-xl text-xs transition-all duration-200
                       {{ $hasOverflowActive ? 'text-softPink-600 dark:text-softPink-400' : 'text-gray-400 dark:text-gray-500' }}">
            <span class="text-lg leading-none">⋯</span>
            <span class="font-medium leading-tight">Lainnya</span>
        </button>
    </div>

    <!-- Overflow Dropdown -->
    <div x-show="moreOpen" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
         class="absolute bottom-full left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 shadow-lg p-2 pb-3">
        <div class="grid grid-cols-3 gap-1">
            @foreach ($overflowModules as $module)
                @php
                    $isActive = request()->routeIs($module['route']);
                @endphp
                <a href="{{ $module['param'] === null ? route($module['route']) : route($module['route'], $child) }}"
                   @click="moreOpen = false"
                   class="flex flex-col items-center gap-1 p-2 rounded-xl text-xs transition-all duration-200
                          {{ $isActive
                              ? 'bg-softPink-50 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400'
                              : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                    <span class="text-lg leading-none">{{ $module['icon'] }}</span>
                    <span class="font-medium leading-tight">{{ $module['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</nav>

<!-- Spacer to prevent content overlap on mobile -->
<div class="h-20 lg:hidden"></div>
