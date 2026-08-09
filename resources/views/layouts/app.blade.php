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
        <div class="min-h-screen bg-cream-50 dark:bg-gray-900 has-bottom-nav">
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
            <main class="pb-20">
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

        <!-- Keyboard Shortcut: Ctrl+K → Search -->
        <div x-data x-init="document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                window.location.href = '{{ route('search.index') }}';
            }
        })" x-cloak></div>

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
