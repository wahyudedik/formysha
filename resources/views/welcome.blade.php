<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="ForMysha — Digital Life Book. Simpan setiap momen, kenangan, dan perjalanan hidup anak sejak lahir hingga dewasa.">

        <title>{{ config('app.name', 'ForMysha') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts: Nunito — friendly & rounded -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Fallback styles when Vite build is not available */
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: 'Nunito', sans-serif; -webkit-font-smoothing: antialiased; background: linear-gradient(135deg, #fdf2f8 0%, #FFFDF7 50%, #faf5ff 100%); color: #374151; min-height: 100vh; }
                a { text-decoration: none; }
            </style>
        @endif
    </head>
    <body class="bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50 min-h-screen">

        {{-- Navigation --}}
        <header class="w-full px-4 sm:px-6 lg:px-8">
            <nav class="max-w-6xl mx-auto flex items-center justify-between py-6">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="h-10 w-auto" />
                    <span class="font-extrabold text-xl text-gray-800">ForMysha</span>
                </div>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary text-sm">
                                Masuk
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary text-sm">
                                    Daftar Gratis
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>
        </header>

        {{-- Hero Section --}}
        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-20 sm:pt-20 sm:pb-32">
            <div class="text-center">
                {{-- Logo --}}
                <div class="mb-8 animate-fade-in">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="w-32 h-32 mx-auto drop-shadow-lg" />
                </div>

                {{-- Tagline --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-800 mb-6 leading-tight animate-slide-up">
                    Untuk <span class="text-gradient-brand">Buah Hatiku</span>
                </h1>

                <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed animate-slide-up" style="animation-delay: 0.1s;">
                    Simpan setiap momen, kenangan, dan perjalanan hidup anak sejak lahir hingga dewasa dalam satu tempat yang aman, terstruktur, dan mudah diakses.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up" style="animation-delay: 0.2s;">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-4 shadow-soft-md hover:shadow-soft-lg">
                            Mulai Gratis Sekarang
                        </a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn-secondary text-base px-8 py-4">
                            Masuk ke Akun
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Features Section --}}
        <section class="bg-white/60 backdrop-blur-sm py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">
                        Semua Kenangan dalam Satu Tempat
                    </h2>
                    <p class="text-gray-500 text-lg max-w-xl mx-auto">
                        ForMysha bukan sekadar album foto. Ini adalah Digital Life Book untuk perjalanan hidup anak Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- Feature 1: Timeline --}}
                    <div class="card-hover text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-softPink-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-softPink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">My Timeline</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Dokumentasikan setiap momen penting dari hari pertama lahir hingga pertumbuhan si kecil.
                        </p>
                    </div>

                    {{-- Feature 2: Gallery --}}
                    <div class="card-hover text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-skyBlue-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-skyBlue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">My Gallery</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Kumpulkan foto dan video dalam album yang rapi dan mudah dicari kapan saja.
                        </p>
                    </div>

                    {{-- Feature 3: Growth --}}
                    <div class="card-hover text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-mintGreen-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-mintGreen-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">My Growth</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Pantau pertumbuhan tinggi badan dan berat badan dengan grafik yang mudah dipahami.
                        </p>
                    </div>

                    {{-- Feature 4: Health --}}
                    <div class="card-hover text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-lavender-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">My Health</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Catat riwayat imunisasi, kesehatan, dan alergi anak dalam satu tempat terpercaya.
                        </p>
                    </div>

                    {{-- Feature 5: Documents --}}
                    <div class="card-hover text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-warmYellow-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-warmYellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">My Documents</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Simpan akta lahir, KK, KIA, BPJS, dan dokumen penting lainnya dengan aman.
                        </p>
                    </div>

                    {{-- Feature 6: Family Sharing --}}
                    <div class="card-hover text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-peach-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-peach-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">My Family</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Berbagi kenangan dengan keluarga tercinta — ayah, ibu, kakek, nenek, dan wali.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="bg-white rounded-3xl shadow-soft-lg p-10 sm:p-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">
                        Mulai Simpan Kenangan Hari Ini
                    </h2>
                    <p class="text-gray-500 text-lg mb-8 max-w-lg mx-auto">
                        Setiap anak memiliki cerita. Setiap momen layak dikenang. Buat akun gratis Anda sekarang.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-4 shadow-soft-md hover:shadow-soft-lg">
                                Daftar Gratis
                            </a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn-secondary text-base px-8 py-4">
                                Masuk
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="bg-white/60 backdrop-blur-sm border-t border-gray-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="h-8 w-auto" />
                        <span class="font-bold text-gray-700">ForMysha</span>
                    </div>

                    <div class="flex items-center gap-6 text-sm text-gray-400">
                        <a href="{{ route('pages.about') }}" class="hover:text-gray-600 transition-colors">Tentang Kami</a>
                        <a href="{{ route('pages.privacy') }}" class="hover:text-gray-600 transition-colors">Kebijakan Privasi</a>
                        <a href="{{ route('pages.terms') }}" class="hover:text-gray-600 transition-colors">Syarat & Ketentuan</a>
                    </div>

                    <p class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} {{ config('app.name', 'ForMysha') }}. Hak cipta dilindungi.
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-300 italic">
                        "Every Moment, Every Memory, One Lifetime."
                    </p>
                </div>
            </div>
        </footer>

    </body>
</html>
