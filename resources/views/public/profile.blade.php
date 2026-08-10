<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->nickname ?? $child->name }} — ForMysha</title>
    <meta name="description" content="Profil publik {{ $child->nickname ?? $child->name }} di ForMysha">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="profile">
    <meta property="og:title" content="{{ $child->nickname ?? $child->name }} — ForMysha">
    <meta property="og:description" content="Profil publik {{ $child->nickname ?? $child->name }} di ForMysha — Digital Life Book">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($child->photo)
        <meta property="og:image" content="{{ asset('storage/'.$child->photo) }}">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $child->nickname ?? $child->name }} — ForMysha">
    <meta name="twitter:description" content="Profil publik {{ $child->nickname ?? $child->name }} di ForMysha — Digital Life Book">
    @if($child->photo)
        <meta name="twitter:image" content="{{ asset('storage/'.$child->photo) }}">
    @endif

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
                    },
                    borderRadius: { '2xl': '1rem', '3xl': '1.5rem' },
                    boxShadow: { 'soft': '0 4px 20px rgba(0, 0, 0, 0.05)' },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #faf5ff 0%, #fdf2f8 50%, #e0f2fe 100%); }
        .card { background: white; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @media (prefers-color-scheme: dark) {
            .gradient-bg { background: linear-gradient(135deg, #1a1025 0%, #111827 50%, #0f172a 100%); }
            .card { background: #1f2937; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        {{-- Header / Profile Card --}}
        <div class="card p-4 sm:p-6 lg:p-8 mb-8 text-center fade-in">
            {{-- Photo --}}
            <div class="mb-6">
                @if($child->photo)
                    <img src="{{ asset('storage/'.$child->photo) }}"
                         alt="{{ $child->nickname ?? $child->name }}"
                         class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-white dark:border-gray-700 shadow-soft">
                @else
                    <div class="w-32 h-32 rounded-full mx-auto border-4 border-white dark:border-gray-700 shadow-soft bg-gradient-to-br from-softPink-200 to-lavender-200 dark:from-softPink-800 dark:to-lavender-800 flex items-center justify-center">
                        <span class="text-5xl">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Name & Nickname --}}
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                {{ $child->nickname ?? $child->name }}
            </h1>
            @if($child->nickname)
                <p class="text-gray-500 dark:text-gray-400 mb-3">{{ $child->name }}</p>
            @endif

            {{-- Bio --}}
            @if($child->bio)
                <p class="text-gray-600 dark:text-gray-300 max-w-md mx-auto mb-4 leading-relaxed">
                    {{ $child->bio }}
                </p>
            @endif

            {{-- Info Chips --}}
            <div class="flex flex-wrap justify-center gap-3 mt-4">
                @if($child->age)
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-500 dark:text-warmYellow-400 text-sm font-medium">
                        🎂 {{ $child->age }}
                    </span>
                @endif
                @if($child->place_of_birth)
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-500 dark:text-mintGreen-400 text-sm font-medium">
                        📍 {{ $child->place_of_birth }}
                    </span>
                @endif
                @if($child->gender)
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-softPink-100 dark:bg-softPink-950/30 text-softPink-500 dark:text-softPink-400 text-sm font-medium">
                        {{ $child->gender === 'female' ? '👧 Perempuan' : '👦 Laki-laki' }}
                    </span>
                @endif
                @if($child->blood_type)
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-softPink-100 dark:bg-softPink-950/30 text-red-500 dark:text-red-400 text-sm font-medium">
                        🩸 Gol. {{ $child->blood_type }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Timeline Section --}}
        @if($showTimeline && count($child->timelines) > 0)
            <div class="card p-4 sm:p-6 mb-8 fade-in" style="animation-delay: 0.1s;">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
                    📸 Timeline
                </h2>
                <div class="space-y-4">
                    @foreach($child->timelines as $timeline)
                        <div class="flex gap-4 p-4 rounded-xl bg-gradient-to-r from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-lavender-200 dark:bg-lavender-800 flex items-center justify-center text-sm">
                                    @if($timeline->event_date)
                                        {{ \Carbon\Carbon::parse($timeline->event_date)->format('d M') }}
                                    @else
                                        📅
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $timeline->title }}</h3>
                                @if($timeline->description)
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1 line-clamp-2">{{ $timeline->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Gallery Section --}}
        @if($showGallery)
            <div class="card p-4 sm:p-6 mb-8 fade-in" style="animation-delay: 0.2s;">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
                    🖼️ Galeri
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @forelse($child->albums->take(6) as $album)
                        <div class="aspect-square rounded-xl overflow-hidden bg-gradient-to-br from-peach-100 to-warmYellow-100 dark:from-peach-950/30 dark:to-warmYellow-950/30 flex items-center justify-center">
                            @if($album->cover_photo)
                                <img src="{{ asset('storage/'.$album->cover_photo) }}"
                                     alt="{{ $album->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl">📁</span>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-400 dark:text-gray-500">
                            <span class="text-4xl block mb-2">📷</span>
                            <p>Belum ada album</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- Awards Section --}}
        @if($showAwards)
            <div class="card p-4 sm:p-6 mb-8 fade-in" style="animation-delay: 0.3s;">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
                    🏆 Penghargaan
                </h2>
                <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                    <span class="text-4xl block mb-2">⭐</span>
                    <p>Fitur penghargaan segera hadir</p>
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
            <p>Dibuat dengan ❤️ oleh <strong class="text-lavender-400">ForMysha</strong></p>
            <p class="mt-1">Every Moment, Every Memory, One Lifetime.</p>
        </div>
    </div>
</body>
</html>
