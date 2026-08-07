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
    </style>
</head>
<body>
    <header class="w-full px-4 sm:px-6 lg:px-8">
        <nav class="max-w-4xl mx-auto flex items-center justify-between py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2" style="text-decoration: none;">
                <img src="{{ asset('logo.png') }}" alt="ForMysha" class="h-10 w-auto" />
                <span class="font-extrabold text-xl text-gray-800">ForMysha</span>
            </a>
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Beranda</a>
        </nav>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20">
        <div class="bg-white rounded-3xl shadow-sm p-8 sm:p-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ $pageTitle }}</h1>
            {{ $slot }}
        </div>
    </main>

    <footer class="text-center py-8 text-sm text-gray-400">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'ForMysha') }}. Hak cipta dilindungi.</p>
    </footer>
</body>
</html>
