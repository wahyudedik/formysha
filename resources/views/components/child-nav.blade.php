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
        ['route' => 'family.index', 'label' => 'Keluarga', 'icon' => '👨‍👩‍👧‍👦', 'param' => 'child'],
    ];
@endphp

<!-- Desktop Sidebar Navigation -->
<aside class="hidden lg:block w-56 shrink-0">
    <div class="sticky top-24 space-y-1">
        @foreach ($modules as $module)
            @php
                $isActive = request()->routeIs($module['route']);
            @endphp
            <a href="{{ route($module['route'], $child) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                      {{ $isActive
                          ? 'bg-softPink-50 text-softPink-600 shadow-soft'
                          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <span class="text-lg">{{ $module['icon'] }}</span>
                {{ $module['label'] }}
            </a>
        @endforeach
    </div>
</aside>

<!-- Mobile Bottom Navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-100 z-50 safe-bottom">
    <div class="flex items-center justify-around py-2 px-1">
        @foreach (array_slice($modules, 0, 5) as $module)
            @php
                $isActive = request()->routeIs($module['route']);
            @endphp
            <a href="{{ route($module['route'], $child) }}"
               class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl text-xs transition-all duration-200
                      {{ $isActive
                          ? 'text-softPink-600'
                          : 'text-gray-400' }}">
                <span class="text-lg leading-none">{{ $module['icon'] }}</span>
                <span class="font-medium leading-tight">{{ $module['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
