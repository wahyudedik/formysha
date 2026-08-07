{{-- Tenant Admin Sidebar --}}
<aside class="w-full lg:w-64 shrink-0" x-data="{ sidebarOpen: false }">
    {{-- Mobile Toggle --}}
    <button
        @click="sidebarOpen = !sidebarOpen"
        class="lg:hidden w-full flex items-center justify-between p-4 bg-white rounded-2xl shadow-soft mb-4"
    >
        <span class="font-semibold text-gray-800">🏢 Menu Admin</span>
        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': sidebarOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Sidebar Nav --}}
    <nav
        class="bg-white rounded-2xl shadow-soft overflow-hidden transition-all duration-300"
        :class="{ 'hidden lg:block': !sidebarOpen }"
    >
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-skyBlue-400 to-mintGreen-400 flex items-center justify-center text-white text-lg">
                    🏢
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Tenant Admin</h3>
                    <p class="text-xs text-gray-500">Panel Pengelolaan</p>
                </div>
            </div>
        </div>

        <div class="p-2">
            <a
                href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-skyBlue-50 text-skyBlue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a
                href="{{ route('admin.branding.edit') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.branding.*') ? 'bg-skyBlue-50 text-skyBlue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                Branding
            </a>

            <a
                href="{{ route('admin.settings.edit') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-skyBlue-50 text-skyBlue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pengaturan
            </a>

            <a
                href="{{ route('admin.usage.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.usage.*') ? 'bg-skyBlue-50 text-skyBlue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Penggunaan
            </a>
        </div>
    </nav>
</aside>
