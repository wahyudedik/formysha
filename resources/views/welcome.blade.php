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

                /* Custom component fallbacks */
                .btn-primary { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; background: #f9a8d4; color: #fff; font-weight: 600; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: all 0.2s; border: none; cursor: pointer; }
                .btn-primary:hover { background: #f472b6; }
                .btn-secondary { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; background: #fff; color: #374151; font-weight: 600; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: all 0.2s; cursor: pointer; }
                .btn-secondary:hover { background: #f9fafb; }
                .card-hover { background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: all 0.2s; }
                .card-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); transform: translateY(-2px); }
                .text-gradient-brand { background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-image: linear-gradient(to right, #f472b6, #c084fc, #7dd3fc); }
                .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
                .animate-slide-up { animation: slideUp 0.5s ease-out; }
                .shadow-soft { box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
                .shadow-soft-md { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
                @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            </style>
        @endif

        <style>
            /* Decorative elements for playful design */
            .cloud {
                position: absolute;
                background: white;
                border-radius: 50%;
                opacity: 0.6;
            }
            .cloud::before,
            .cloud::after {
                content: '';
                position: absolute;
                background: white;
                border-radius: 50%;
            }
            .star {
                position: absolute;
                color: #fde047;
                font-size: 1.5rem;
                animation: twinkle 2s ease-in-out infinite;
            }
            @keyframes twinkle {
                0%, 100% { opacity: 0.4; transform: scale(1); }
                50% { opacity: 1; transform: scale(1.2); }
            }
            .float {
                animation: float 3s ease-in-out infinite;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
        </style>
    </head>
    <body class="bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50 min-h-screen overflow-x-hidden">

        {{-- Navigation --}}
        <header class="w-full px-4 sm:px-6 lg:px-8 relative">
            <nav class="max-w-6xl mx-auto flex items-center justify-between py-6">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="h-10 w-auto" />
                    <span class="font-extrabold text-xl text-gray-800">ForMysha</span>
                </div>

                <div class="hidden sm:flex items-center gap-6 text-sm text-gray-500">
                    <a href="{{ route('pages.about') }}" class="hover:text-gray-700 transition-colors">Tentang</a>
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

        {{-- Hero Section with Decorative Elements --}}
        <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 sm:pt-16 sm:pb-24 relative">
            {{-- Decorative Clouds --}}
            <div class="absolute top-0 left-10 w-16 h-10 bg-white/60 rounded-full hidden lg:block float" style="animation-delay: 0.5s;"></div>
            <div class="absolute top-8 right-20 w-12 h-8 bg-white/50 rounded-full hidden lg:block float" style="animation-delay: 1s;"></div>

            {{-- Decorative Stars --}}
            <div class="absolute top-20 left-1/4 hidden lg:block star" style="animation-delay: 0.3s;">⭐</div>
            <div class="absolute top-32 right-1/4 hidden lg:block star" style="animation-delay: 0.8s;">⭐</div>
            <div class="absolute bottom-20 left-1/3 hidden lg:block star" style="animation-delay: 1.2s;">✨</div>

            <div class="text-center relative z-10">
                {{-- Logo --}}
                <div class="mb-8 animate-fade-in">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="w-28 h-28 sm:w-36 sm:h-36 mx-auto drop-shadow-lg" />
                </div>

                {{-- Tagline --}}
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-gray-800 mb-4 leading-tight animate-slide-up">
                    Untuk <span class="text-gradient-brand">Buah Hatiku</span>
                </h1>

                <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto mb-4 leading-relaxed animate-slide-up" style="animation-delay: 0.1s;">
                    Simpan setiap momen, kenangan, dan perjalanan hidup anak sejak lahir hingga dewasa dalam satu tempat yang aman, terstruktur, dan mudah diakses.
                </p>

                <p class="text-sm text-gray-400 mb-8 animate-slide-up" style="animation-delay: 0.15s;">
                    Digital Life Book untuk Setiap Anak, Kenangan untuk Selamanya 💕
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up" style="animation-delay: 0.2s;">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-4 shadow-soft-md hover:shadow-soft-lg">
                            Mulai Gratis Sekarang 💕
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
        <section class="bg-white/60 backdrop-blur-sm py-16 sm:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
                        Semua Kenangan dalam Satu Tempat
                    </h2>
                    <p class="text-gray-500 text-base sm:text-lg max-w-xl mx-auto">
                        ForMysha bukan sekadar album foto. Ini adalah Digital Life Book untuk perjalanan hidup anak Anda.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
                    {{-- Feature 1: Timeline --}}
                    <div class="card-hover text-center">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-2xl bg-softPink-100 flex items-center justify-center">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-softPink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-1">My Timeline</h3>
                        <p class="text-gray-500 text-xs sm:text-sm leading-relaxed">
                            Abadikan setiap momen penting dalam timeline.
                        </p>
                    </div>

                    {{-- Feature 2: Gallery --}}
                    <div class="card-hover text-center">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-2xl bg-skyBlue-100 flex items-center justify-center">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-skyBlue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-1">My Gallery</h3>
                        <p class="text-gray-500 text-xs sm:text-sm leading-relaxed">
                            Kumpulkan foto dan video dalam album rapi.
                        </p>
                    </div>

                    {{-- Feature 3: Growth --}}
                    <div class="card-hover text-center">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-2xl bg-mintGreen-100 flex items-center justify-center">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-mintGreen-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-1">My Growth</h3>
                        <p class="text-gray-500 text-xs sm:text-sm leading-relaxed">
                            Pantau pertumbuhan dengan grafik mudah dipahami.
                        </p>
                    </div>

                    {{-- Feature 4: Health --}}
                    <div class="card-hover text-center">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-2xl bg-lavender-100 flex items-center justify-center">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-lavender-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-1">My Health</h3>
                        <p class="text-gray-500 text-xs sm:text-sm leading-relaxed">
                            Catat riwayat imunisasi dan kesehatan.
                        </p>
                    </div>

                    {{-- Feature 5: Family --}}
                    <div class="card-hover text-center sm:col-span-2 lg:col-span-1">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-2xl bg-peach-100 flex items-center justify-center">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-peach-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-1">My Family</h3>
                        <p class="text-gray-500 text-xs sm:text-sm leading-relaxed">
                            Berbagi kenangan dengan keluarga tercinta.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Benefits Section --}}
        <section class="py-16 sm:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">
                        💕 Satu Aplikasi, Banyak Manfaat 💕
                    </h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
                    {{-- Benefit 1 --}}
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-softPink-100 flex items-center justify-center">
                            <span class="text-2xl">🔒</span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-800 mb-1">Aman & Privat</h3>
                        <p class="text-gray-500 text-xs leading-relaxed">Data keluarga Anda aman bersama kami</p>
                    </div>

                    {{-- Benefit 2 --}}
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-skyBlue-100 flex items-center justify-center">
                            <span class="text-2xl">☁️</span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-800 mb-1">Backup Otomatis</h3>
                        <p class="text-gray-500 text-xs leading-relaxed">Kenangan tidak akan pernah hilang</p>
                    </div>

                    {{-- Benefit 3 --}}
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-mintGreen-100 flex items-center justify-center">
                            <span class="text-2xl">📱</span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-800 mb-1">Akses di Mana Saja</h3>
                        <p class="text-gray-500 text-xs leading-relaxed">Bisa diakses dari berbagai perangkat</p>
                    </div>

                    {{-- Benefit 4 --}}
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-warmYellow-100 flex items-center justify-center">
                            <span class="text-2xl">📤</span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-800 mb-1">Unduh & Bagikan</h3>
                        <p class="text-gray-500 text-xs leading-relaxed">Ekspor kenangan dalam PDF</p>
                    </div>

                    {{-- Benefit 5 --}}
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-lavender-100 flex items-center justify-center">
                            <span class="text-2xl">😊</span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-800 mb-1">Mudah Digunakan</h3>
                        <p class="text-gray-500 text-xs leading-relaxed">Desain lucu, sederhana, untuk semua orang tua</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Brand Identity Section --}}
        <section class="bg-white/60 backdrop-blur-sm py-16 sm:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4">
                        Mengapa <span class="text-gradient-brand">ForMysha</span>?
                    </h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                    {{-- For --}}
                    <div class="bg-white rounded-2xl p-6 text-center shadow-soft hover:shadow-soft-md transition">
                        <div class="text-3xl font-extrabold text-softPink-400 mb-2">For</div>
                        <p class="text-gray-600 text-sm">Untuk</p>
                        <p class="text-gray-400 text-xs mt-1">Dibuat untuk setiap keluarga</p>
                    </div>

                    {{-- My --}}
                    <div class="bg-white rounded-2xl p-6 text-center shadow-soft hover:shadow-soft-md transition">
                        <div class="text-3xl font-extrabold text-skyBlue-400 mb-2">My</div>
                        <p class="text-gray-600 text-sm">Milikku</p>
                        <p class="text-gray-400 text-xs mt-1">Cinta dan kenangan yang tak tergantikan</p>
                    </div>

                    {{-- Sha --}}
                    <div class="bg-white rounded-2xl p-6 text-center shadow-soft hover:shadow-soft-md transition">
                        <div class="text-3xl font-extrabold text-lavender-400 mb-2">Sha</div>
                        <p class="text-gray-600 text-sm">Simbol Anak</p>
                        <p class="text-gray-400 text-xs mt-1">Setiap anak, setiap cerita</p>
                    </div>

                    {{-- .my.id --}}
                    <div class="bg-white rounded-2xl p-6 text-center shadow-soft hover:shadow-soft-md transition">
                        <div class="text-3xl font-extrabold text-mintGreen-400 mb-2">.my.id</div>
                        <p class="text-gray-600 text-sm">Identitas Indonesia</p>
                        <p class="text-gray-400 text-xs mt-1">Dekat, personal, lebih bermakna</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="py-16 sm:py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="bg-white rounded-3xl shadow-soft-lg p-10 sm:p-16 relative overflow-hidden">
                    {{-- Decorative hearts --}}
                    <div class="absolute top-4 right-4 text-softPink-200 text-2xl float" style="animation-delay: 0.5s;">💕</div>
                    <div class="absolute bottom-4 left-4 text-lavender-200 text-xl float" style="animation-delay: 1s;">✨</div>

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
                        Simpan Hari ini, Kenang Selamanya 💕
                    </h2>
                    <p class="text-gray-500 text-base sm:text-lg mb-8 max-w-lg mx-auto">
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

                    <p class="text-xs text-gray-400 mt-6 italic">
                        For My Child. For My Love. For Mysha. 💕
                    </p>
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
                        "Every Moment, Every Memory, One Lifetime." 💕
                    </p>
                </div>
            </div>
        </footer>

    </body>
</html>
