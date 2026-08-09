<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Digital Life Book — Untuk Buah Hatiku">

        <title>{{ config('app.name', 'ForMysha') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts: Nunito — friendly & rounded -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .auth-split-gradient {
                background: linear-gradient(135deg, #fdf2f8 0%, #FFFDF7 40%, #faf5ff 70%, #e0f2fe 100%);
            }
            @media (prefers-color-scheme: dark) {
                .auth-split-gradient {
                    background: linear-gradient(135deg, #1a1025 0%, #111827 40%, #0f172a 70%, #0c1929 100%);
                }
            }
            .auth-float {
                animation: authFloat 4s ease-in-out infinite;
            }
            @keyframes authFloat {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-8px); }
            }
            .auth-float-delayed {
                animation: authFloat 4s ease-in-out infinite;
                animation-delay: 1s;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- Left Panel: Branding (hidden on mobile, visible on lg+) --}}
            <div class="hidden lg:flex lg:w-1/2 xl:w-[45%] auth-split-gradient relative overflow-hidden flex-col items-center justify-center p-12">
                {{-- Decorative elements --}}
                <div class="absolute top-10 left-10 w-20 h-14 bg-white/30 rounded-full auth-float" style="animation-delay: 0.5s;"></div>
                <div class="absolute top-24 right-16 w-14 h-10 bg-white/20 rounded-full auth-float-delayed"></div>
                <div class="absolute bottom-20 left-16 text-3xl auth-float" style="animation-delay: 0.3s;">⭐</div>
                <div class="absolute bottom-32 right-20 text-2xl auth-float-delayed">✨</div>
                <div class="absolute top-1/3 left-8 text-xl auth-float" style="animation-delay: 1.2s;">💕</div>

                {{-- Brand Content --}}
                <div class="relative z-10 text-center max-w-md">
                    <a href="{{ url('/') }}" class="inline-block mb-8">
                        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="w-32 h-32 mx-auto drop-shadow-lg" />
                    </a>

                    <h1 class="text-3xl xl:text-4xl font-extrabold text-gray-800 dark:text-gray-100 mb-4 leading-tight">
                        Untuk <span style="background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-image: linear-gradient(to right, #f472b6, #c084fc, #7dd3fc);">Buah Hatiku</span>
                    </h1>

                    <p class="text-gray-500 dark:text-gray-400 text-base leading-relaxed mb-6">
                        Simpan setiap momen, kenangan, dan perjalanan hidup anak sejak lahir hingga dewasa dalam satu tempat yang aman.
                    </p>

                    <div class="flex items-center justify-center gap-4 mb-8">
                        <div class="flex items-center gap-2 text-sm text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4 text-softPink-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            Aman & Privat
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4 text-mintGreen-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            Gratis
                        </div>
                    </div>

                    <p class="text-xs text-gray-300 dark:text-gray-600 italic">
                        "Every Moment, Every Memory, One Lifetime." 💕
                    </p>
                </div>
            </div>

            {{-- Right Panel: Form --}}
            <div class="flex-1 lg:w-1/2 xl:w-[55%] flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50 dark:from-[#1a1025] dark:via-[#111827] dark:to-[#0f172a] min-h-screen lg:min-h-0">

                {{-- Mobile-only logo --}}
                <div class="lg:hidden mb-6 text-center">
                    <a href="{{ url('/') }}" class="inline-block">
                        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'ForMysha') }}" class="w-20 h-20 mx-auto drop-shadow-md" />
                    </a>
                </div>

                <div class="w-full sm:max-w-md">
                    <div class="bg-white dark:bg-gray-800 shadow-soft-md rounded-3xl p-6 sm:p-8">
                        {{ $slot }}
                    </div>

                    {{-- Footer links --}}
                    <div class="mt-6 text-center">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
