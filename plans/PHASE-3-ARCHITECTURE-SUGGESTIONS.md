# ForMysha — Architecture Suggestions & Feature Ideas

**Tanggal:** 2026-08-07
**Status:** Rekomendasi (belum diimplementasi)

---

## Bagian 1: Saran Arsitektur

### 1. Service Layer Pattern

**Masalah:** Controllers saat ini mengandung semua logika bisnis — query building, data transformation, dan business rules. Ini membuat code sulit diuji dan direuse.

**Solusi:** Extract logika bisnis ke Service Classes.

```
app/Services/
├── ChildService.php          → createChild, getChildStats, getRecentActivity
├── TimelineService.php       → createTimeline, getTimelineWithMedia
├── GrowthService.php         → recordGrowth, getGrowthChart, getPercentile
├── HealthService.php         → recordHealthCheck, getUpcomingImmunizations
├── DashboardService.php      → getDashboardData, getRecentActivity
└── ExportService.php         → exportToPdf, exportToZip
```

**Manfaat:**
- Controllers jadi tipis dan fokus pada HTTP concern
- Logic bisa diuji di unit test tanpa HTTP layer
- Logic bisa direuse di CLI commands atau API

**Contoh implementasi:**

```php
// app/Services/GrowthService.php
class GrowthService
{
    public function recordGrowth(Child $child, array $data): Growth
    {
        $growth = $child->growths()->create($data);

        // Auto-trigger notification if growth is concerning
        if ($this->isGrowthConcerning($child, $growth)) {
            GrowthConcernedNotification::dispatch($child, $growth);
        }

        return $growth;
    }

    public function getGrowthChart(Child $child): array
    {
        return $child->growths()
            ->orderBy('measured_at', 'asc')
            ->get()
            ->map(fn (Growth $g) => [
                'date' => $g->measured_at->format('Y-m-d'),
                'weight' => $g->weight,
                'height' => $g->height,
            ])
            ->toArray();
    }
}
```

---

### 2. Eloquent API Resources

**Masalah:** Dashboard dan public profile mengirim data mentah dari model ke view. Jika format berubah, semua view harus diupdate.

**Solusi:** Gunakan API Resources untuk transformasi data terpusat.

```
app/Http/Resources/
├── ChildResource.php
├── TimelineResource.php
├── GrowthResource.php
├── HealthRecordResource.php
├── DiaryResource.php
└── DashboardResource.php
```

**Manfaat:**
- Transformasi data terpusat
- Konsisten antara Blade views dan API endpoint masa depan
- Mudah ditambahkan field computed

---

### 3. Event-Driven Architecture

**Masalah:** Saat ini semua notifikasi dan side-effect dijalankan inline di controller.

**Solusi:** Gunakan Laravel Events & Listeners untuk loose coupling.

```
app/Events/
├── ChildCreated.php
├── TimelineCreated.php
├── GrowthRecorded.php
├── HealthRecordCreated.php
├── DocumentUploaded.php
└── MilestoneAchieved.php

app/Listeners/
├── SendWelcomeNotification.php
├── UpdateChildStats.php
├── CheckGrowthPercentile.php
├── ScheduleImmunizationReminder.php
└── LogActivityToTimeline.php
```

**Contoh:**

```php
// Event
class GrowthRecorded extends Event
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Child $child,
        public Growth $growth,
    ) {}
}

// Listener
class CheckGrowthPercentile extends ShouldQueue
{
    public function handle(GrowthRecorded $event): void
    {
        // Check WHO growth standards and notify if outside normal range
    }
}
```

**Manfaat:**
- Side-effect terpisah dari logic utama
- Mudah ditambahkan listener baru tanpa mengubah code existing
- Bisa di-queue untuk performa

---

### 4. Repository Pattern (Optional)

**Kapan dibutuhkan:** Saat aplikasi mulai memiliki multiple data sources atau perlu mengganti database driver.

```
app/Repositories/
├── ChildRepository.php
├── GrowthRepository.php
└── HealthRecordRepository.php
```

**Catatan:** Pattern ini opsional. Laravel Eloquent sudah cukup powerful untuk SaaS MVP. Pertimbangkan hanya jika ada kebutuhan spesifik.

---

### 5. Form Request Enhancement

**Masalah:** Beberapa controller masih menggunakan `$request->validated()` langsung.

**Solusi:** Konsistenkan dengan Form Request classes yang sudah ada.

```
app/Http/Requests/
├── StoreChildRequest.php      ✅ sudah ada
├── UpdateChildRequest.php     ✅ sudah ada
├── StoreTimelineRequest.php   ✅ sudah ada
├── UpdateTimelineRequest.php  ✅ sudah ada
├── StoreDiaryRequest.php      ✅ sudah ada
├── UpdateDiaryRequest.php     → perlu dibuat
├── StoreAlbumRequest.php      ✅ sudah ada
├── UpdateAlbumRequest.php     → perlu dibuat
├── StoreDocumentRequest.php   → perlu dibuat
├── UpdateDocumentRequest.php  → perlu dibuat
├── StoreEventRequest.php      → perlu dibuat
├── UpdateEventRequest.php     → perlu dibuat
├── StoreGrowthRequest.php     → perlu dibuat
├── UpdateGrowthRequest.php    → perlu dibuat
├── StoreHealthRecordRequest.php → perlu dibuat
├── UpdateHealthRecordRequest.php → perlu dibuat
├── StoreFamilyMemberRequest.php  → perlu dibuat
└── UpdateFamilyMemberRequest.php → perlu dibuat
```

---

### 6. Activity Log / Audit Trail

**Kapan dibutuhkan:** Phase 3 SaaS — untuk compliance dan debugging.

**Solusi:** Gunakan `spatie/laravel-activitylog` atau custom implementation.

```
app/Models/ActivityLog.php
database/migrations/xxx_create_activity_logs_table.php
```

**Yang perlu dilog:**
- Create/Update/Delete pada semua modul
- Login/Logout
- Export/Download
- Permission changes

---

### 7. Caching Strategy

**Masalah:** Dashboard melakukan banyak query setiap request.

**Solusi:** Implementasi caching untuk data yang jarang berubah.

```php
// DashboardService.php
public function getDashboardData(User $user): array
{
    $children = Cache::remember(
        "user:{$user->id}:children",
        now()->addMinutes(30),
        fn () => $user->children()->withCount([...])->get()
    );

    return compact('children', ...);
}
```

**Cache targets:**
- Child list (30 min)
- Growth chart data (15 min)
- Health record counts (15 min)
- Dashboard statistics (10 min)

---

## Bagian 2: Ide Fitur Lanjutan

### 1. Activity Feed

**Deskripsi:** Timeline aktivitas yang menunjukkan semua aktivitas terkini di satu tempat — mirip social media feed.

**Fitur:**
- Semua aktivitas (timeline, diary, growth, health, document) dalam satu feed
- Filter berdasarkan tipe aktivitas
- Filter berdasarkan anak
- Infinite scroll

**Prioritas:** Medium — menambah engagement pengguna.

---

### 2. Export PDF

**Deskripsi:** Export profil anak, riwayat kesehatan, atau pertumbuhan ke format PDF.

**Fitur:**
- Export profil lengkap anak
- Export riwayat kesehatan (untuk dokter)
- Export grafik pertumbuhan
- Export rapor / sertifikat

**Package:** `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf`

**Prioritas:** High — sangat dibutuhkan untuk kunjungan dokter.

---

### 3. Growth Percentile (WHO Standards)

**Deskripsi:** Bandingkan data pertumbuhan anak dengan standar WHO (World Health Organization).

**Fitur:**
- Grafik percentile WHO untuk BB/U, TB/U, BB/TB
- Highlight apakah anak di atas/bawah normal
- Rekomendasi otomatis berdasarkan data
- Perbandingan dengan anak seumuran

**Package:** Custom implementation dengan data WHO

**Prioritas:** High — value proposition utama untuk modul kesehatan.

---

### 4. Milestone Tracker

**Deskripsi:** Track pencapaian milestone perkembangan anak.

**Fitur:**
- Template milestone berdasarkan usia (0-12 bulan, 1-3 tahun, 3-6 tahun)
- Custom milestone
- Foto/video pencapaian
- Grafik perkembangan
- Reminder milestone yang akan datang

**Template milestone:**
- Bayi: mengangkat kepala, berguling, duduk, merangkak, berdiri
- Toddler: berjalan, berlari, berbicara kata pertama
- Preschool: mengenal warna, menulis nama, bersepeda

**Prioritas:** Medium — menambah nilai edukatif.

---

### 5. Medical Records Integration

**Deskripsi:** Dokumen medis terstruktur yang bisa dibagikan ke dokter.

**Fitur:**
- Medical summary card (format standar)
- Riwayat imunisasi lengkap
- Riwayat alergi
- Daftar obat yang sedang dikonsumsi
- QR code untuk sharing ke dokter

**Prioritas:** Medium — memudahkan kunjungan dokter.

---

### 6. Family Member Roles & Permissions

**Deskripsi:** Role-based access untuk anggota keluarga yang berbeda.

**Roles:**
- **Admin (Orang tua utama):** Full access
- **Parent (Ayah/Ibu):** Full access
- **Guardian (Wali):** Akses terbatas (hanya lihat + tambah timeline)
- **Grandparent:** Akses read-only + komentar
- **Doctor:** Akses read-only ke data kesehatan saja

**Prioritas:** High — diperlukan untuk Phase 3 SaaS.

---

### 7. Smart Reminders

**Deskripsi:** Reminder cerdas yang tidak hanya mengingatkan tetapi juga memberikan konteks.

**Fitur:**
- Reminder imunisasi dengan info vaksin yang dibutuhkan
- Reminder ulang tahun dengan suggestion hadiah
- Reminder check-up berkala
- Reminder dokumen yang akan expired (paspor, KIA)
- Notification via email + push (untuk mobile)

**Prioritas:** Medium — meningkatkan retensi pengguna.

---

### 8. Data Import/Export

**Deskripsi:** Import data dari aplikasi lain dan export data dari ForMysha.

**Import:**
- Import dari Google Photos (JSON/ZIP)
- Import dari Apple Health (CSV)
- Import dari Baby Tracker apps
- Import manual (CSV/JSON)

**Export:**
- Export semua data anak ke ZIP
- Export timeline ke PDF
- Export growth chart ke PNG
- Export seluruh profile ke JSON

**Prioritas:** Medium — memudahkan migrasi pengguna baru.

---

### 9. Multi-Language Support

**Deskripsi:** Dukungan bahasa Indonesia dan English.

**Implementasi:**
- Gunakan Laravel localization (`lang/` directory)
- Semua string UI menggunakan `__('key')` atau `@lang('key')`
- Language switcher di header/footer
- Default: Bahasa Indonesia

**Prioritas:** Low — untuk ekspansi internasional.

---

### 10. Mobile App (Future)

**Deskripsi:** Aplikasi mobile native atau hybrid untuk akses lebih mudah.

**Opsi:**
- **React Native / Flutter** — native apps
- **Laravel + Livewire** — PWA (Progressive Web App)
- **Capacitor** — wrap web app ke mobile

**Rekomendasi:** Mulai dengan PWA (Livewire), karena:
- Tidak perlu codebase terpisah
- Bisa langsung deploy ke Play Store / App Store
- Investasi minimal dengan ROI tinggi

**Prioritas:** Low — fokus web SaaS dulu.

---

## Bagian 3: Prioritas Implementasi

### Short Term (1-2 bulan)

1. ✅ **Form Request completion** — buat semua Update*Request yang belum ada
2. ✅ **Service Layer** — extract logika bisnis dari controllers
3. ✅ **Growth Percentile** — implementasi WHO standards
4. ✅ **Export PDF** — untuk profil anak dan riwayat kesehatan

### Medium Term (3-6 bulan)

5. **Activity Feed** — timeline aktivitas terpadu
6. **Milestone Tracker** — pencapaian perkembangan anak
7. **Family Roles & Permissions** — role-based access
8. **Smart Reminders** — reminder cerdas berbasis context

### Long Term (6-12 bulan)

9. **Multi-Language** — dukungan bahasa Indonesia + English
10. **Data Import/Export** — migrasi dari aplikasi lain
11. **Mobile PWA** — Progressive Web App untuk mobile
12. **Medical Records Integration** — QR code sharing ke dokter

---

## Bagian 4: Tech Debt yang Perlu Diselesaikan

### 4.1 Missing Form Requests

Buat Form Request classes untuk semua endpoint yang belum punya:

```bash
php artisan make:request UpdateDiaryRequest
php artisan make:request UpdateAlbumRequest
php artisan make:request StoreDocumentRequest
php artisan make:request UpdateDocumentRequest
php artisan make:request StoreEventRequest
php artisan make:request UpdateEventRequest
php artisan make:request StoreGrowthRequest
php artisan make:request UpdateGrowthRequest
php artisan make:request StoreHealthRecordRequest
php artisan make:request UpdateHealthRecordRequest
php artisan make:request StoreFamilyMemberRequest
php artisan make:request UpdateFamilyMemberRequest
```

### 4.2 Unused Model Imports

Bersihkan import yang tidak terpakai di controllers setelah refactoring middleware.

### 4.3 Database Indexing

Pastikan semua foreign keys dan frequently queried columns memiliki index:

```php
// Sudah ada di migrations, tapi perlu diverifikasi:
Schema::table('timelines', function (Blueprint $table) {
    $table->index('child_id');
    $table->index('event_date');
});
```

### 4.4 N+1 Query Prevention

Gunakan eager loading di semua view yang membutuhkan relationship:

```php
// Contoh yang sudah benar:
$child->load('familyMembers');

// Contoh yang perlu diperhatikan:
$timelines = $child->timelines()->with('media')->get(); // ✅ eager loading
```

---

## Kesimpulan

ForMysha memiliki fondasi yang solid dengan 299 tests passing. Fokus utama untuk pengembangan selanjutnya:

1. **Extract Service Layer** — untuk maintainability jangka panjang
2. **Growth Percentile (WHO)** — value proposition utama
3. **Export PDF** — fitur paling dibutuhkan pengguna
4. **Family Roles** — diperlukan untuk multi-user access

Semua rekomendasi ini bersifat **iterative** — bisa diimplementasikan satu per satu tanpa mengubah arsitektur yang sudah ada.
