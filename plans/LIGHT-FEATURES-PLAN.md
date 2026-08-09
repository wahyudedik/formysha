# Prioritas 2 — Light Features Implementation Plan

**Tanggal:** 2026-08-08
**Status:** Ready for Implementation

---

## Ringkasan

4 light features yang diidentifikasi dari audit komprehensif, akan diimplementasi berurutan.

---

## Feature 1: Standarisasi Media Relationship ke MorphMany

### Masalah
[`Timeline.php`](app/Models/Timeline.php:87) dan [`Diary.php`](app/Models/Diary.php:76) menggunakan `HasMany` dengan manual `where('mediable_type', static::class)`, sedangkan [`Child.php`](app/Models/Child.php:131) dan [`Album.php`](app/Models/Album.php:63) sudah menggunakan `MorphMany`.

### Perubahan

#### [`app/Models/Timeline.php`](app/Models/Timeline.php)
1. Ganti import `use Illuminate\Database\Eloquent\Relations\HasMany;` → `use Illuminate\Database\Eloquent\Relations\MorphMany;`
2. Ganti method `media()` dari `HasMany` ke `MorphMany`:
   ```php
   public function media(): MorphMany
   {
       return $this->morphMany(Media::class, 'mediable');
   }
   ```

#### [`app/Models/Diary.php`](app/Models/Diary.php)
1. Ganti import yang sama
2. Ganti method `media()` dari `HasMany` ke `MorphMany`

### Impact Analysis
- `MediaService::upload()` menggunakan `get_class($mediable)` → kompatibel dengan MorphMany
- Views (`timeline/show.blade.php`, `diaries/show.blade.php`) menggunakan `$timeline->media` → MorphMany return Collection yang sama
- Tidak ada test yang langsung test `->media()` pada Timeline/Diary
- `MediaController::storeForTimeline/Diary` passing model ke `MediaService::uploadMultiple()` → kompatibel
- **Aman tanpa perubahan database** — data sudah benar di DB

---

## Feature 2: Tambahkan Growth Show Route + Controller Method + View

### Masalah
Modul Growth adalah satu-satunya modul yang tidak memiliki `show` route, `show()` method di controller, dan `show.blade.php` view. Semua modul lain (Health, Calendar, Diary, Album, Timeline) sudah memiliki show.

### Perubahan

#### [`routes/web.php`](routes/web.php)
Tambahkan route show SEBELUM edit route:
```php
Route::get('/children/{child}/growth/{growth}', [GrowthController::class, 'show'])->name('growth.show');
```
Jadi urutan growth routes menjadi:
```
index → create → store → show → edit → update → destroy
```

#### [`app/Http/Controllers/GrowthController.php`](app/Http/Controllers/GrowthController.php)
Tambahkan method `show()`:
```php
public function show(Request $request, Child $child, Growth $growth): View
{
    abort_unless($growth->child_id === $child->id, 403);

    $assessment = $this->growthService->assessGrowth($child, $growth);

    return view('growth.show', [
        'child' => $child,
        'growth' => $growth,
        'assessment' => $assessment,
    ]);
}
```

#### [`resources/views/growth/show.blade.php`](resources/views/growth/show.blade.php)
Buat view baru mengikuti pola [`health/show.blade.php`](resources/views/health/show.blade.php):
- `<x-app-layout>` wrapper
- Breadcrumb: Dashboard → Child → Pertumbuhan → Detail
- Page header dengan tanggal pengukuran
- Card detail: Berat Badan, Tinggi Badan, Lingkar Kepala, Catatan
- Growth assessment section (jika ada)
- Link kembali ke index
- Tombol Edit dan Hapus

### Impact Analysis
- Tidak ada test yang perlu diubah — test existing hanya test index, create, store, edit, update, destroy
- Route baru, method baru, view baru — pure addition

---

## Feature 3: Standarisasi Confirm Delete di Growth Index

### Masalah
[`growth/index.blade.php`](resources/views/growth/index.blade.php:158) menggunakan native `confirm()` dengan Alpine.js, sedangkan modul lain menggunakan [`<x-confirm-delete>`](resources/views/components/confirm-delete.blade.php) component.

### Perubahan

#### [`resources/views/growth/index.blade.php`](resources/views/growth/index.blade.php)
Ganti native confirm dengan event dispatch ke `<x-confirm-delete>`:

Sebelumnya:
```blade
<form method="POST" action="{{ route('growth.destroy', [$child, $growth]) }}" x-data>
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-500 hover:text-red-600 text-xs font-medium" x-data x-on:click.prevent="if(confirm('Yakin ingin menghapus data pengukuran ini?')) $el.closest('form').submit()">
        🗑️ {{ __('Hapus') }}
    </button>
</form>
```

Sesudahnya:
```blade
<button type="button" class="text-red-500 hover:text-red-600 text-xs font-medium"
    x-data
    x-on:click.prevent="$dispatch('delete-confirm', 'delete-growth-{{ $growth->id }}')">
    🗑️ {{ __('Hapus') }}
</button>
```

Dan tambahkan `<x-confirm-delete>` component di akhir konten (di luar loop):
```blade
@foreach ($growths as $growth)
    <x-confirm-delete
        id="delete-growth-{{ $growth->id }}"
        title="Hapus Data Pengukuran"
        message="Apakah Anda yakin ingin menghapus data pengukuran ini? Tindakan ini tidak dapat dibatalkan."
        action="{{ route('growth.destroy', [$child, $growth]) }}"
    />
@endforeach
```

### Impact Analysis
- UI change only — tidak ada backend yang berubah
- Menggunakan component yang sudah ada dan terbukti works di modul lain

---

## Feature 4: Tambahkan Head Circumference Tab di Growth Chart

### Masalah
[`growth-chart.blade.php`](resources/views/components/growth-chart.blade.php) hanya memiliki 2 tab (Berat Badan & Tinggi Badan), padahal:
- Model `Growth` sudah memiliki field `head_circumference_cm`
- `GrowthService` sudah memiliki data WHO untuk head circumference (`WHO_HEAD_BOYS`, `WHO_HEAD_GIRLS`)
- Growth index view sudah menampilkan head circumference di tabel

### Perubahan

#### [`resources/views/components/growth-chart.blade.php`](resources/views/components/growth-chart.blade.php)
1. Tambahkan `whoHead` ke `@props`:
   ```blade
   @props(['growths', 'whoWeight' => null, 'whoHeight' => null, 'whoHead' => null, 'childGender' => null])
   ```

2. Tambahkan extract data head circumference:
   ```php
   $headCircumferences = collect($data)->pluck('weight')->filter()->values();
   // + data extraction untuk head circumference
   ```

3. Tambahkan tab button ketiga: 🧠 Lingkar Kepala

4. Tambahkan chart section untuk head circumference (mirip weight/height chart)

#### [`app/Http/Controllers/GrowthController.php`](app/Http/Controllers/GrowthController.php) — method `index()`
Tambahkan `$whoHead` ke data yang dikirim ke view:
```php
$whoHead = $this->growthService->getWhoHeadPercentiles($child->gender);
```
Dan tambahkan ke compact/view data.

#### [`resources/views/growth/index.blade.php`](resources/views/growth/index.blade.php)
Update pemanggilan component untuk include `whoHead`:
```blade
<x-growth-chart :growths="$growthHistory" :whoWeight="$whoWeight" :whoHeight="$whoHeight" :whoHead="$whoHead" :childGender="$child->gender" />
```

### Impact Analysis
- Component prop tambahan — backward compatible (nullable default)
- Data WHO sudah tersedia di GrowthService
- Tidak ada database change

---

## Testing Plan

Setelah semua 4 features diimplementasi:
1. Jalankan `.\vendor\bin\pint --dirty --format agent`
2. Jalankan `php artisan test --compact`
3. Verify semua 461+ tests pass
4. Buat test baru untuk growth show:
   - `GET /children/{child}/growth/{growth}` → 200
   - `GET /children/{child}/growth/{growth}` (wrong child) → 403

---

## Execution Order

1. Feature 1: MorphMany (Timeline + Diary models) — model only, no view/controller
2. Feature 2: Growth show (route + controller + view) — addition only
3. Feature 3: Confirm delete standardization (view only) — UI change
4. Feature 4: Head circumference chart (component + controller + view) — enhancement
5. Run Pint + Tests
