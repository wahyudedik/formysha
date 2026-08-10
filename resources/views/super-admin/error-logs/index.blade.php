<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Error Logs') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('super-admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Error Logs'],
                ]" />

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 rounded-xl text-sm text-mintGreen-700 dark:text-mintGreen-300">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-950/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalErrors }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Errors</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-warmYellow-50 dark:bg-warmYellow-950/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-warmYellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalWarnings }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Warnings</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-skyBlue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalInfo }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Info</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-lavender-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($fileSize / 1024, 1) }} KB</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Ukuran Log</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters & Actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <form method="GET" action="{{ route('super-admin.error-logs.index') }}" class="flex flex-col sm:flex-row gap-3">
                        {{-- Search --}}
                        <div class="flex-1">
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari error message..."
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm focus:ring-2 focus:ring-skyBlue-300 dark:focus:ring-skyBlue-600 focus:border-skyBlue-400 dark:focus:border-skyBlue-500 min-h-[44px]"
                            />
                        </div>

                        {{-- Level Filter --}}
                        <div>
                            <select
                                name="level"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm focus:ring-2 focus:ring-skyBlue-300 dark:focus:ring-skyBlue-600 focus:border-skyBlue-400 dark:focus:border-skyBlue-500 min-h-[44px]"
                            >
                                <option value="all" {{ $level === 'all' ? 'selected' : '' }}>Semua Level</option>
                                <option value="error" {{ $level === 'error' ? 'selected' : '' }}>Error</option>
                                <option value="warning" {{ $level === 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="info" {{ $level === 'info' ? 'selected' : '' }}>Info</option>
                                <option value="debug" {{ $level === 'debug' ? 'selected' : '' }}>Debug</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-2">
                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-skyBlue-500 hover:bg-skyBlue-600 text-white rounded-xl text-sm font-medium transition min-h-[44px]"
                            >
                                Filter
                            </button>

                            {{-- Copy All --}}
                            <button
                                type="button"
                                onclick="copyAllLogs()"
                                class="px-5 py-2.5 bg-mintGreen-500 hover:bg-mintGreen-600 text-white rounded-xl text-sm font-medium transition min-h-[44px]"
                            >
                                Copy Semua
                            </button>

                            {{-- Clear Log --}}
                            <button
                                type="button"
                                x-data
                                x-on:click.prevent="$dispatch('delete-confirm', 'delete-error-logs')"
                                class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-medium transition min-h-[44px]"
                            >
                                Hapus Log
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Log Entries --}}
                <div class="space-y-4" id="log-entries">
                    @forelse ($logs as $index => $log)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700 overflow-hidden log-entry"
                             x-data="{ expanded: false }"
                             data-raw="{{ e($log['raw']) }}"
                        >
                            {{-- Log Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-750 transition"
                                 @click="expanded = !expanded"
                            >
                                {{-- Level Badge --}}
                                <div class="shrink-0">
                                    @if ($log['level'] === 'ERROR')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-950/50 text-red-700 dark:text-red-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                            ERROR
                                        </span>
                                    @elseif ($log['level'] === 'WARNING')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-warmYellow-100 dark:bg-warmYellow-950/50 text-warmYellow-700 dark:text-warmYellow-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                            WARNING
                                        </span>
                                    @elseif ($log['level'] === 'INFO')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-skyBlue-100 dark:bg-skyBlue-950/50 text-skyBlue-700 dark:text-skyBlue-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                            INFO
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                            {{ strtoupper($log['level']) }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Message Preview --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                                        {{ Str::limit($log['message'], 120) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $log['timestamp'] }} · {{ $log['environment'] }}
                                    </p>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 shrink-0">
                                    <button
                                        type="button"
                                        onclick="event.stopPropagation(); copyLogEntry(this.closest('.log-entry'))"
                                        class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-medium transition min-h-[44px] flex items-center gap-1"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        Copy
                                    </button>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Expanded Content --}}
                            <div x-show="expanded" x-transition class="border-t border-gray-100 dark:border-gray-700">
                                <div class="p-4">
                                    {{-- Full Message --}}
                                    <div class="mb-3">
                                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Pesan Lengkap</h4>
                                        <p class="text-sm text-gray-800 dark:text-gray-100 whitespace-pre-wrap break-words">{{ $log['message'] }}</p>
                                    </div>

                                    {{-- Context --}}
                                    @if ($log['context'])
                                        <div class="mb-3">
                                            <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Context</h4>
                                            <pre class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-xl p-3 overflow-x-auto whitespace-pre-wrap break-words">{{ $log['context'] }}</pre>
                                        </div>
                                    @endif

                                    {{-- Stack Trace --}}
                                    @if ($log['stack'])
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Stack Trace</h4>
                                            <pre class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-xl p-3 overflow-x-auto whitespace-pre-wrap break-words max-h-64 overflow-y-auto">{{ $log['stack'] }}</pre>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft border border-gray-100 dark:border-gray-700 p-12 text-center">
                            <div class="w-16 h-16 rounded-full bg-mintGreen-50 dark:bg-mintGreen-950/30 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-mintGreen-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Tidak ada error log</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Log file kosong atau belum ada error yang tercatat.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Total entries --}}
                @if (count($logs) > 0)
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Menampilkan {{ count($logs) }} log entries
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Hidden textarea for copy --}}
    <textarea id="copy-buffer" class="fixed opacity-0 pointer-events-none" style="top: -9999px;"></textarea>

    <script>
        function copyLogEntry(element) {
            const raw = element.getAttribute('data-raw');
            const buffer = document.getElementById('copy-buffer');
            buffer.value = raw;
            buffer.select();
            document.execCommand('copy');
            showToast('Log entry berhasil dicopy!');
        }

        function copyAllLogs() {
            const entries = document.querySelectorAll('.log-entry');
            let allLogs = '';
            entries.forEach(function(entry) {
                const raw = entry.getAttribute('data-raw');
                if (raw) {
                    allLogs += raw + '\n\n';
                }
            });

            if (allLogs) {
                const buffer = document.getElementById('copy-buffer');
                buffer.value = allLogs;
                buffer.select();
                document.execCommand('copy');
                showToast('Semua log berhasil dicopy!');
            } else {
                showToast('Tidak ada log untuk dicopy.');
            }
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-50 px-5 py-3 bg-gray-800 dark:bg-gray-100 text-white dark:text-gray-800 rounded-xl shadow-lg text-sm font-medium transition-all transform translate-y-0 opacity-100';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(function() {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(function() { toast.remove(); }, 300);
            }, 2000);
        }
    </script>

    {{-- Delete Confirmation for Clear Log --}}
    <x-confirm-delete
        id="delete-error-logs"
        title="{{ __('Hapus Semua Log') }}"
        message="{{ __('Apakah Anda yakin ingin mengosongkan semua log file? Tindakan ini tidak dapat dibatalkan.') }}"
        action="{{ route('super-admin.error-logs.clear') }}"
        method="POST"
    />
</x-app-layout>
