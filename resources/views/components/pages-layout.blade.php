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

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Nunito', sans-serif; -webkit-font-smoothing: antialiased; background: linear-gradient(135deg, #fdf2f8 0%, #FFFDF7 50%, #faf5ff 100%); color: #374151; min-height: 100vh; }
        a { text-decoration: none; color: #a855f7; }
        a:hover { text-decoration: underline; }
        @media (prefers-color-scheme: dark) {
            body { background: linear-gradient(135deg, #1a1025 0%, #111827 50%, #0f172a 100%); color: #e5e7eb; }
            a { color: #c084fc; }
        }
    </style>
</head>
<body>
    <header class="w-full px-4 sm:px-6 lg:px-8">
        <nav class="max-w-4xl mx-auto flex items-center justify-between py-4 sm:py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2" style="text-decoration: none;">
                <img src="{{ asset('logo.png') }}" alt="ForMysha" class="h-9 sm:h-10 w-auto" />
                <span class="font-extrabold text-lg sm:text-xl text-gray-800 dark:text-gray-100">ForMysha</span>
            </a>
            <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
            <a href="{{ url('/') }}" class="sm:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-white/60 dark:hover:bg-white/10 transition-colors" aria-label="Kembali ke Beranda">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
        </nav>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-8 sm:p-12">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-6">{{ $pageTitle }}</h1>
            {{ $slot }}
        </div>
    </main>

    <footer class="text-center py-8 text-sm text-gray-400 dark:text-gray-500">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'ForMysha') }}. Hak cipta dilindungi.</p>
    </footer>
</body>
</html>
