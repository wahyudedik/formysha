<nav x-data="{ open: false }" class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="h-9 w-auto" />
                        <span class="font-bold text-lg text-gray-800 dark:text-gray-100 hidden sm:inline">ForMysha</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (auth()->user()->role === 'super_admin')
                        <x-nav-link :href="route('super-admin.dashboard')" :active="request()->routeIs('super-admin.*')">
                            {{ __('Super Admin') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('children.index')" :active="request()->routeIs('children.*')">
                        {{ __('Anak Saya') }}
                    </x-nav-link>
                    @if (auth()->user()->isFacilityAdmin())
                        <x-nav-link :href="route('facility.dashboard')" :active="request()->routeIs('facility.*')">
                            {{ __('Fasilitas') }}
                        </x-nav-link>
                    @endif
                    @if (in_array(auth()->user()->role, ['parent', 'tenant_admin']))
                        <x-nav-link :href="route('subscription.plans')" :active="request()->routeIs('subscription.*')">
                            {{ __('Paket Langganan') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right Side: Search + Language + Notification + Settings -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Search -->
                <a href="{{ route('search.index') }}" class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors" title="{{ __('app.search.title') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </a>

                <!-- Language Switcher -->
                <div x-data="{ langOpen: false }" class="relative">
                    <button @click="langOpen = !langOpen" class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors flex items-center gap-1 text-sm font-medium">
                        @if(app()->getLocale() === 'id')
                            <span>🇮🇩</span>
                        @else
                            <span>🇬🇧</span>
                        @endif
                    </button>
                    <div x-show="langOpen" @click.away="langOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50">
                        <form method="POST" action="{{ route('language.switch', 'id') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() === 'id' ? 'text-skyBlue-600 bg-skyBlue-50 dark:bg-skyBlue-950/30 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} flex items-center gap-2">
                                🇮🇩 {{ __('app.language.indonesian') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('language.switch', 'en') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() === 'en' ? 'text-skyBlue-600 bg-skyBlue-50 dark:bg-skyBlue-950/30 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} flex items-center gap-2">
                                🇬🇧 {{ __('app.language.english') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Notification Badge -->
                <x-notification-badge :count="auth()->user()->unread_notifications_count ?? 0" />

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-xl text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('children.index')">
                            {{ __('👶 Anak Saya') }}
                        </x-dropdown-link>

                        @if (in_array(auth()->user()->role, ['parent', 'tenant_admin']))
                            <x-dropdown-link :href="route('subscription.current')">
                                {{ __('📦 Langganan Saya') }}
                            </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('search.index')">
                            {{ __('🔍 Pencarian') }}
                        </x-dropdown-link>

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('👤 Profil Saya') }}
                        </x-dropdown-link>

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                        @if (auth()->user()->role === 'super_admin')
                            <x-dropdown-link :href="route('super-admin.dashboard')">
                                {{ __('🛡️ Super Admin') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        @endif

                        <x-dropdown-link :href="route('erasure.index')" class="text-red-600 dark:text-red-400">
                            {{ __('🗑️ Hapus Akun') }}
                        </x-dropdown-link>

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('🚪 Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden gap-1">
                <!-- Search (mobile) -->
                <a href="{{ route('search.index') }}" class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </a>
                <!-- Notification Badge (mobile) -->
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if((auth()->user()->unread_notifications_count ?? 0) > 0)
                        <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-softPink rounded-full ring-2 ring-white dark:ring-gray-800">
                            {{ auth()->user()->unread_notifications_count > 99 ? '99+' : auth()->user()->unread_notifications_count }}
                        </span>
                    @endif
                </a>
                <!-- Language Switcher (mobile) -->
                <div x-data="{ langOpenMobile: false }" class="relative">
                    <button @click="langOpenMobile = !langOpenMobile" class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded-xl text-sm">
                        @if(app()->getLocale() === 'id')
                            🇮🇩
                        @else
                            🇬🇧
                        @endif
                    </button>
                    <div x-show="langOpenMobile" @click.away="langOpenMobile = false"
                         x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50">
                        <form method="POST" action="{{ route('language.switch', 'id') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() === 'id' ? 'text-skyBlue-600 bg-skyBlue-50 dark:bg-skyBlue-950/30 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} flex items-center gap-2">
                                🇮🇩 {{ __('app.language.indonesian') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('language.switch', 'en') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ app()->getLocale() === 'en' ? 'text-skyBlue-600 bg-skyBlue-50 dark:bg-skyBlue-950/30 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} flex items-center gap-2">
                                🇬🇧 {{ __('app.language.english') }}
                            </button>
                        </form>
                    </div>
                </div>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden dark:bg-gray-800">
        <div class="pt-2 pb-3 space-y-1">
            @if (auth()->user()->role === 'super_admin')
                <x-responsive-nav-link :href="route('super-admin.dashboard')" :active="request()->routeIs('super-admin.*')">
                    {{ __('🛡️ Super Admin') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('🏠 Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('children.index')" :active="request()->routeIs('children.*')">
                {{ __('👶 Anak Saya') }}
            </x-responsive-nav-link>
            @if (auth()->user()->isFacilityAdmin())
                <x-responsive-nav-link :href="route('facility.dashboard')" :active="request()->routeIs('facility.*')">
                    {{ __('🏥 Fasilitas') }}
                </x-responsive-nav-link>
            @endif
            @if (in_array(auth()->user()->role, ['parent', 'tenant_admin']))
                <x-responsive-nav-link :href="route('subscription.plans')" :active="request()->routeIs('subscription.*')">
                    {{ __('💳 Paket Langganan') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-100">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if (in_array(auth()->user()->role, ['parent', 'tenant_admin']))
                    <x-responsive-nav-link :href="route('subscription.current')" :active="request()->routeIs('subscription.current')">
                        {{ __('📦 Langganan Saya') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('search.index')" :active="request()->routeIs('search.*')">
                    {{ __('🔍 Pencarian') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    {{ __('🔔 Notifikasi') }}
                    @if((auth()->user()->unread_notifications_count ?? 0) > 0)
                        <span class="ms-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-softPink rounded-full ring-2 ring-white dark:ring-gray-800">
                            {{ auth()->user()->unread_notifications_count > 99 ? '99+' : auth()->user()->unread_notifications_count }}
                        </span>
                    @endif
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('👤 Profil Saya') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('erasure.index')" class="text-red-600 dark:text-red-400">
                    {{ __('🗑️ Hapus Akun') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('🚪 Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
