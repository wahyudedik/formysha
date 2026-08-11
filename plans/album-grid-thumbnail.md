# Album Grid Thumbnail — Grid Preview di Album Card

## Goal

Album card di halaman Galeri menampilkan **grid preview 2x2** dari foto-foto yang ada di dalamnya, alih-alih hanya icon kamera placeholder. Ini membuat halaman galeri lebih visual dan menarik.

## Current Behavior

Di [`albums/index.blade.php`](resources/views/albums/index.blade.php:72-81), album card menampilkan:

- Jika ada `cover_photo` → tampilkan cover photo
- Jika tidak ada `cover_photo` → tampilkan icon kamera 📸 + teks "X foto"

## Desired Behavior

- Jika ada `cover_photo` → tampilkan cover photo (tidak berubah)
- Jika tidak ada `cover_photo` tapi ada foto → tampilkan **grid 2x2** dari foto pertama di album
- Jika tidak ada foto sama sekali → tampilkan icon kamera placeholder (tidak berubah)

## Implementation Plan

### Step 1 — Update Controller: Eager-Load Media untuk Grid Preview

**File:** [`app/Http/Controllers/AlbumController.php`](app/Http/Controllers/AlbumController.php:23-45)

Saat ini controller hanya melakukan `withCount('media')`. Perlu ditambahkan eager-load untuk 4 foto pertama yang akan digunakan sebagai grid preview.

**Perubahan di `index()` method:**

```php
// Sebelum:
$query = $child->albums()->withCount('media');

// Sesudah:
$query = $child->albums()
    ->withCount('media')
    ->with(['media' => function ($q) {
        $q->where('file_type', 'photo')
          ->orderBy('created_at', 'desc')
          ->limit(4);
    }]);
```

**Catatan Penting:**
- Hanya load foto (bukan video/audio) untuk grid preview
- Limit 4 karena grid 2x2
- Order by `created_at` desc untuk menampilkan foto terbaru
- `media_count` tetap dihitung dari semua media (termasuk video/audio) via `withCount`

### Step 2 — Update View: Grid 2x2 di Album Card

**File:** [`resources/views/albums/index.blade.php`](resources/views/albums/index.blade.php:72-81)

Ganti section "Cover Photo" dengan 3 kondisi:

```blade
<!-- Cover Photo / Grid Preview -->
<div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 mb-4">
    @if ($album->cover_photo)
        {{-- Kondisi 1: Ada cover photo --}}
        <img src="{{ asset('storage/' . $album->cover_photo) }}" alt="{{ $album->name }}" class="w-full h-full object-cover" />
    @elseif ($album->media->isNotEmpty())
        {{-- Kondisi 2: Tidak ada cover, tapi ada foto — tampilkan grid 2x2 --}}
        <div class="w-full h-full grid grid-cols-2 grid-rows-2 gap-0.5">
            @foreach ($album->media as $index => $item)
                <div class="overflow-hidden bg-gray-100 dark:bg-gray-700">
                    @if ($item->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->thumbnail_path))
                        <img src="{{ asset('storage/' . $item->thumbnail_path) }}" alt="" class="w-full h-full object-cover" loading="lazy" />
                    @elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($item->file_path))
                        <img src="{{ asset('storage/' . $item->file_path) }}" alt="" class="w-full h-full object-cover" loading="lazy" />
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-lg">📸</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        {{-- Kondisi 3: Tidak ada foto sama sekali --}}
        <div class="w-full h-full flex flex-col items-center justify-center">
            <span class="text-4xl mb-2">📸</span>
            <span class="text-sm text-gray-400 dark:text-gray-500">{{ $album->media_count ?? 0 }} {{ __('foto') }}</span>
        </div>
    @endif
</div>
```

### Step 3 — Handle Jumlah Foto Kurang dari 4

Grid 2x2 selalu memiliki 4 cell. Jika album hanya punya 1-3 foto, cell yang kosong akan menampilkan gradient background dari parent div. Ini sudah natural karena `gap-0.5` dan `bg-gradient` dari parent.

**Alternatif — Dynamic Grid:**

Jika ingin lebih rapi, bisa gunakan dynamic grid berdasarkan jumlah foto:

```blade
@php
    $photoCount = $album->media->count();
    $gridClass = match (true) {
        $photoCount === 1 => 'grid-cols-1 grid-rows-1',
        $photoCount === 2 => 'grid-cols-2 grid-rows-1',
        $photoCount === 3 => 'grid-cols-2 grid-rows-2',
        default => 'grid-cols-2 grid-rows-2',
    };
@endphp

<div class="w-full h-full grid {{ $gridClass }} gap-0.5">
    ...
</div>
```

**Rekomendasi:** Gunakan pendekatan sederhana — selalu `grid-cols-2 grid-rows-2`. Cell kosong akan menampilkan gradient background yang sudah ada. Ini lebih konsisten dan predictable.

### Step 4 — Optimization: Preload Media di Controller

Untuk menghindari N+1 query, eager loading sudah ditangani di Step 1. Namun pastikan `media_count` tetap akurat:

```php
$query = $child->albums()
    ->withCount('media')
    ->with(['media' => function ($q) {
        $q->where('file_type', 'photo')
          ->orderBy('created_at', 'desc')
          ->limit(4);
    }]);
```

**Behavior:**
- `$album->media_count` → jumlah total media (foto + video + audio) via `withCount`
- `$album->media` → 4 foto terbaru untuk grid preview

### Step 5 — Responsive Considerations

Grid 2x2 sudah konsisten di semua breakpoint karena album card menggunakan `aspect-square`. Tidak perlu responsive adjustment tambahan.

## Visual Mockup

### Saat Ini
```
┌──────────────┐
│              │
│     📸       │
│   4 foto     │
│              │
└──────────────┘
 Irma Alston
 4 media
```

### Sesudahnya
```
┌──────────────┐
│ ┌───┬───┐    │
│ │ 📷│ 📷│    │
│ ├───┼───┤    │
│ │ 📷│ 📷│    │
│ └───┴───┘    │
└──────────────┘
 Irma Alston
 4 media
```

## Files yang Perlu Diubah

1. [`app/Http/Controllers/AlbumController.php`](app/Http/Controllers/AlbumController.php:23-45) — Tambah eager-load media untuk grid preview
2. [`resources/views/albums/index.blade.php`](resources/views/albums/index.blade.php:72-81) — Ganti placeholder dengan grid 2x2

## Testing

- Pastikan album dengan cover_photo tetap menampilkan cover photo
- Pastikan album tanpa cover_photo tapi ada foto menampilkan grid 2x2
- Pastikan album tanpa foto menampilkan placeholder
- Pastikan grid tetap rapi untuk 1, 2, 3, atau 4+ foto
- Pastikan tidak ada N+1 query (cek dengan `preventLazyLoading`)
- Pastikan responsive di mobile dan desktop
