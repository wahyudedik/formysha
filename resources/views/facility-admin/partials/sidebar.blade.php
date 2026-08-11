@php
    // Get current user's staff role to conditionally show/hide menu items
    $currentStaff = \App\Models\Staff::where('user_id', auth()->id())
        ->where('tenant_id', $tenant->id ?? null)
        ->where('is_active', true)
        ->first();
    $staffRole = $currentStaff?->staff_role?->value ?? '';
    $canAccessClinicalNotes = in_array($staffRole, ['doctor', 'midwife', 'nurse']);
    $canAccessReferrals = in_array($staffRole, ['doctor', 'midwife', 'staff_admin']);
    $canAccessReports = $staffRole === 'staff_admin';
    $canAccessSettings = $staffRole === 'staff_admin';
@endphp

{{-- Facility Admin Sidebar --}}
<aside class="w-full lg:w-64 shrink-0" x-data="{ sidebarOpen: false }">
    {{-- Mobile Toggle --}}
    <button
        @click="sidebarOpen = !sidebarOpen"
        class="lg:hidden w-full flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-soft mb-4 min-h-[44px]"
    >
        <span class="font-semibold text-gray-800 dark:text-gray-100">🏥 Menu Fasilitas</span>
        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform" :class="{ 'rotate-180': sidebarOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Sidebar Nav --}}
    <nav
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden transition-all duration-300"
        :class="{ 'hidden lg:block': !sidebarOpen }"
    >
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-skyBlue-400 to-mintGreen-400 flex items-center justify-center text-white text-lg">
                    🏥
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm">{{ $tenant->name ?? 'Fasilitas' }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Panel Fasilitas</p>
                </div>
            </div>
        </div>

        <div class="p-2">
            {{-- Dashboard — semua staf --}}
            <a
                href="{{ route('facility.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] {{ request()->routeIs('facility.dashboard') ? 'bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            {{-- Staf — staff_admin & doctor --}}
            <a
                href="{{ route('facility.staff.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] {{ request()->routeIs('facility.staff.*') ? 'bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Staf
            </a>

            {{-- Pasien — staff_admin, doctor, midwife, nurse --}}
            <a
                href="{{ route('facility.patients.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] {{ request()->routeIs('facility.patients.*') ? 'bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Pasien
            </a>

            {{-- Catatan Klinis — HANYA doctor, midwife, nurse (BUKAN staff_admin) --}}
            @if($canAccessClinicalNotes)
                <a
                    href="{{ route('facility.clinical-notes.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] {{ request()->routeIs('facility.clinical-notes.*') ? 'bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Catatan Klinis
                </a>
            @endif

            {{-- Rujukan — doctor, midwife, staff_admin --}}
            @if($canAccessReferrals)
                <a
                    href="{{ route('facility.referrals.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] {{ request()->routeIs('facility.referrals.*') ? 'bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Rujukan
                </a>
            @endif

            {{-- Laporan — staff_admin saja --}}
            @if($canAccessReports)
                <a
                    href="{{ route('facility.reports.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] {{ request()->routeIs('facility.reports.*') ? 'bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Laporan
                </a>
            @endif

            <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>

            {{-- Pengaturan — staff_admin saja --}}
            @if($canAccessSettings)
                <a
                    href="{{ route('facility.settings.edit') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] {{ request()->routeIs('facility.settings.*') ? 'bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </a>
            @endif
        </div>
    </nav>
</aside>
