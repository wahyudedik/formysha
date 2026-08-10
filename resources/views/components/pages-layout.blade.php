@props(['pageTitle' => 'Halaman'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }} — ForMysha</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'media',
            theme: {
                extend: {
                    colors: {
                        skyBlue: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9' },
                        mintGreen: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#22c55e' },
                        softPink: { 50: '#fdf2f8', 100: '#fce7f3', 200: '#fbcfe8', 300: '#f9a8d4', 400: '#f472b6', 500: '#ec4899' },
                        lavender: { 50: '#faf5ff', 100: '#f3e8ff', 200: '#e9d5ff', 300: '#d8b4fe', 400: '#c084fc', 500: '#a855f7' },
                        warmYellow: { 50: '#fefce8', 100: '#fef9c3', 200: '#fef08a', 300: '#fde047', 400: '#facc15', 500: '#eab308' },
                        peach: { 50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74', 400: '#fb923c', 500: '#f97316' },
                        cream: { 50: '#FFFDF7', 100: '#FFF9ED', 200: '#FFF3DB' },
                    },
                    fontFamily: { sans: ['Nunito', 'sans-serif'] },
                },
            },
        }
    </script>

    <style>
        body { font-family: 'Nunito', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-[#1a1025] dark:via-[#111827] dark:to-[#0f172a] min-h-screen text-gray-700 dark:text-gray-300">
    <header class="w-full px-4 sm:px-6 lg:px-8">
        <nav class="max-w-4xl mx-auto flex items-center justify-between py-4 sm:py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2 no-underline">
                <img src="{{ asset('logo.png') }}" alt="ForMysha" class="h-9 sm:h-10 w-auto" />
                <span class="font-extrabold text-lg sm:text-xl text-gray-800 dark:text-gray-100">ForMysha</span>
            </a>
            <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors no-underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
            <a href="{{ url('/') }}" class="sm:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-white/60 dark:hover:bg-gray-700/60 transition-colors no-underline" aria-label="Kembali ke Beranda">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
        </nav>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-soft p-6 sm:p-10 lg:p-12">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 mb-6">{{ $pageTitle }}</h1>
            {{ $slot }}
        </div>
    </main>

    <footer class="text-center py-8 text-sm text-gray-400 dark:text-gray-500">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'ForMysha') }}. Hak cipta dilindungi.</p>
    </footer>
</body>
</html>
