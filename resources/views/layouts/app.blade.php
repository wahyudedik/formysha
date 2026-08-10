<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Digital Life Book — Untuk Buah Hatiku. Simpan setiap momen, kenangan, dan perjalanan hidup anak sejak lahir hingga dewasa.">

        <title>{{ config('app.name', 'ForMysha') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- PWA Meta Tags -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#f9a8d4">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="ForMysha">
        <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

        <!-- Fonts: Nunito — friendly & rounded -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-cream-50 dark:bg-gray-900">
        <div class="min-h-screen bg-cream-50 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-soft">
                    <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-t border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="h-7 w-auto" />
                            <span class="font-bold text-sm text-gray-600 dark:text-gray-300">ForMysha</span>
                        </div>

                        <div class="flex items-center gap-5 text-xs text-gray-400 dark:text-gray-500">
                            <a href="{{ route('pages.about') }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Tentang Kami</a>
                            <a href="{{ route('pages.privacy') }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Kebijakan Privasi</a>
                            <a href="{{ route('pages.terms') }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Syarat & Ketentuan</a>
                        </div>

                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            &copy; {{ date('Y') }} {{ config('app.name', 'ForMysha') }}
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Toast Notifications -->
        <x-toast />

        <!-- Keyboard Shortcuts -->
        <div x-data="{
            showHelp: false,
            shortcuts: [
                { keys: 'Ctrl + K', label: 'Pencarian' },
                { keys: 'Ctrl + N', label: 'Tambah Anak' },
                { keys: 'Esc', label: 'Tutup modal/dropdown' },
                { keys: '?', label: 'Tampilkan bantuan ini' }
            ]
        }" x-init="document.addEventListener('keydown', (e) => {
            // Ignore if user is typing in an input/textarea
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) return;

            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                window.location.href = '{{ route('search.index') }}';
            }
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                window.location.href = '{{ route('children.create') }}';
            }
            if (e.key === '?' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                showHelp = !showHelp;
            }
            if (e.key === 'Escape') {
                showHelp = false;
            }
        })" x-cloak>
            <!-- Keyboard Shortcuts Help Modal -->
            <div x-show="showHelp" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
                <div class="absolute inset-0 bg-gray-500/70 dark:bg-gray-900/70" @click="showHelp = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">⌨️ Pintasan Keyboard</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Navigasi lebih cepat dengan pintasan keyboard</p>
                    <div class="space-y-3">
                        <template x-for="shortcut in shortcuts" :key="shortcut.keys">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-300" x-text="shortcut.label"></span>
                                <kbd class="px-2 py-1 text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-gray-600" x-text="shortcut.keys"></kbd>
                            </div>
                        </template>
                    </div>
                    <button @click="showHelp = false" class="mt-5 w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl text-sm font-medium transition min-h-[44px]">Tutup</button>
                </div>
            </div>
        </div>

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </body>
</html>
