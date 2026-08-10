# Phase 16 — Comprehensive Audit Report

**Tanggal:** 2026-08-10
**Status:** ✅ SELESAI — Phase 16A & 16B Implemented

---

## Ringkasan Temuan

Audit komprehensif dilakukan terhadap seluruh kode ForMysha (624 tests, 1417 assertions). Berikut temuan yang dikategorikan berdasarkan prioritas dan tingkat kesulitan.

---

## 🔴 KRITIS — Harus Diperbaiki Segera

### 1. KEAMANAN: `.env.production` Berisi Kredensial Hardcoded
**File:** `.env.production`
**Masalah:** File ini berisi kredensial production sungguhan yang TIDAK BOLEH ada di version control:
- `APP_KEY` (base64)
- `DB_PASSWORD` = `3aaf5594628808`
- `REDIS_PASSWORD` = `cdfe97af2103606c`
- `MAIL_PASSWORD` = `Wahyu123456789@`
- `SUPER_ADMIN_PASSWORD` = `Wahyu123456789@`
- Informasi rekening bank (BRI, JAGO, BTN, BSI) dengan nomor rekening asli

**Risiko:** Jika repository ini dipublikasikan atau diakses oleh pihak tidak berwenang, seluruh infrastruktur production dapat disusupi.

**Rekomendasi:**
- Hapus `.env.production` dari version control
- Tambahkan ke `.gitignore`
- Gunakan environment variables di server production
- Untuk `.env.example`, tambahkan placeholder yang jelas untuk semua variabel

### 2. BUG: ExportController Tidak Ada Pengecekan Otorisasi
**File:** `app/Http/Controllers/ExportController.php`
**Masalah:** Semua method (`childProfile`, `healthRecords`, `growthRecords`, `childZip`) menerima `Child $child` via route model binding tetapi TIDAK ADA pengecekan apakah user yang login adalah pemilik child tersebut.

```php
// CURRENT — Tidak ada otorisasi
public function childProfile(Child $child): Response|RedirectResponse
{
    return $this->exportService->exportChildProfile($child);
}
```

**Risiko:** User yang login bisa mengunduh profil, data kesehatan, dan data pertumbuhan anak lain SEMUA.

**Rekomendasi:** Tambahkan pengecekan ownership:
```php
public function childProfile(Child $child): Response|RedirectResponse
{
    abort_unless($child->user_id === auth()->id(), 403);
    // ...
}
```
Atau gunakan middleware `child.ownership` pada route.

### 3. BUG: PublicProfileController Missing Eager Loading untuk Albums
**File:** `app/Http/Controllers/PublicProfileController.php`
**Masalah:** View `public/profile.blade.php` baris 155 menggunakan `$child->albums->take(6)` tetapi controller HANYA load `timelines` dan `achievements`. Relationship `albums` TIDAK di-load.

```php
// CURRENT — Missing albums
->with([
    'timelines' => function ($query) { ... },
    'achievements' => function ($query) { ... },
])
```

**Dampak:** Akan terjadi N+1 query atau error jika `albums` relationship belum di-load.

**Rekomendasi:** Tambahkan `albums` ke eager loading:
```php
->with([
    'timelines' => function ($query) { ... },
    'albums' => function ($query) { $query->take(6)->with('media'); },
    'achievements' => function ($query) { ... },
])
```

---

## 🟡 SEDANG — Perlu Diperbaiki

### 4. PRIVACY: Public Profile Menampilkan Album Tanpa Filter
**File:** `resources/views/public/profile.blade.php`
**Masalah:** Section galeri menampilkan semua album (`$child->albums->take(6)`) tanpa mempertimbangkan apakah album tersebut seharusnya tampil di profil publik. Fitur `public_profile_data` hanya mengontrol section (timeline, gallery, awards), bukan item di dalamnya.

**Rekomendasi:** Pertimbangkan untuk:
- Menambahkan flag `is_public` pada model Album
- Atau menggunakan `public_profile_data` yang lebih detail (misal: `['gallery' => ['album_ids' => [...]]]`)

### 5. N+1 QUERY POTENTIAL: Dashboard View Accesses Relations in Loops
**File:** `resources/views/dashboard.blade.php`
**Masalah:** View mengakses `$event->child->slug`, `$growth->child->name`, `$record->child->name` dalam loop. Meskipun `DashboardService` sudah menggunakan `->with('child')`, ada potensi N+1 jika ada tambahan relasi yang diakses di view.

**Status:** Sudah ditangani dengan baik oleh `DashboardService` (menggunakan `->with('child')`).
**Rekomendasi:** Verifikasi bahwa SEMUA relasi yang diakses di view sudah di-load di service.

### 6. FEATURE GAP: PatientLinkController Memuat Semua Parents
**File:** `app/Http/Controllers/FacilityAdmin/PatientLinkController.php:43`
**Masalah:** `$parents = User::where('role', 'parent')->get()` memuat SEMUA user parent di seluruh sistem, bukan hanya yang terkait dengan tenant/fasilitas.

**Dampak:** Performa buruk jika jumlah parent banyak, dan potential privacy issue (facility admin bisa melihat semua parent).

**Rekomendasi:** Filter parents berdasarkan tenant atau child yang sudah tertaut:
```php
$linkedParentIds = PatientLink::where('facility_tenant_id', $tenant->id)
    ->pluck('parent_user_id');
$parents = User::whereIn('id', $linkedParentIds)->get();
```

### 7. CLEAN CODE: ExportController Menggunakan `new MediaService` di DiaryController
**File:** `app/Http/Controllers/DiaryController.php:84`, `AlbumController.php:72`
**Masalah:** Instansiasi `new MediaService` langsung di controller, bukan menggunakan dependency injection.

```php
// CURRENT
$mediaService = new MediaService;
$mediaService->uploadMultiple($mediaFiles, $diary);
```

**Rekomendasi:** Gunakan constructor injection seperti controller lain:
```php
public function __construct(
    private MediaService $mediaService,
) {}
```

### 8. CLEAN CODE: Ganda `withCount` di AlbumController
**File:** `app/Http/Controllers/AlbumController.php:21,30`
**Masalah:** `withCount('media')` dipanggil dua kali — sekali di query builder (baris 21) dan sekali lagi di branch `most_media` (baris 30).

```php
$query = $child->albums()->withCount('media'); // Line 21
// ...
'most_media' => $query->withCount('media')->orderBy('media_count', 'desc'), // Line 30 — redundant
```

**Rekomendasi:** Hapus `withCount` yang redundant di baris 30.

---

## 🟢 RINGAN — Quick Fixes

### 9. MISSING: `.env.example` Belum Lengkap
**File:** `.env.example`
**Masalah:**
- Sudah ada `SUPER_ADMIN_PASSWORD=` ✅
- Sudah ada billing bank info ✅
- MISSING: MinIO/S3 endpoint configuration untuk storage
- MISSING: `SAAS_DEFAULT_TENANT_ID=` sudah ada ✅

**Rekomendasi:** Tambahkan MinIO config:
```
# MinIO / S3 Storage
MINIO_ENDPOINT=
MINIO_ACCESS_KEY=
MINIO_SECRET_KEY=
MINIO_BUCKET=
MINIO_USE_PATH_STYLE=true
```

### 10. RESPONSIVE: Cek Konsistensi Touch Target
**Status:** Sudah konsisten menggunakan `min-h-[44px]` pada tombol.
**Rekomendasi:** Verifikasi semua tombol di semua view sudah menggunakan pattern yang sama.

### 11. NAVIGATION: Tenant Admin Link untuk B2B Users
**File:** `resources/views/layouts/navigation.blade.php`
**Masalah:** Tenant admin link hanya muncul untuk `parent` dan `tenant_admin` role di subscription section. Facility admin juga harus bisa mengakses subscription.

**Status:** Sudah benar — facility admin memiliki route terpisah di `/facility/*`.

### 12. MISSING TEST: ExportController Authorization
**Masalah:** Tidak ada test yang memverifikasi bahwa user lain tidak bisa mengunduh data anak yang bukan milik mereka.

**Rekomendasi:** Tambahkan test:
```php
test('user cannot export another user child data', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $otherUser->id]);
    
    $this->actingAs($user);
    $response = $this->get(route('export.child-profile', $child));
    $response->assertStatus(403);
});
```

---

## 📊 Status Eager Loading per Controller

| Controller | Eager Loading | Status |
|---|---|---|
| ChildController::index | `withCount('familyMembers')` | ✅ |
| ChildController::show | `load('familyMembers')` | ✅ |
| TimelineController::index | `with('media')` | ✅ |
| AlbumController::index | `withCount('media')` | ✅ |
| AlbumController::show | `load('media')` | ✅ |
| DiaryController::show | `load('media')` | ✅ |
| GrowthController::index | Via GrowthService | ✅ |
| HealthController::index | Direct query | ✅ |
| CalendarController::index | Direct query | ✅ |
| FamilyMemberController::index | Direct query | ✅ |
| AchievementController::index | Via AchievementService | ✅ |
| MilestoneController::index | Via MilestoneService | ✅ |
| DashboardService | `with('child')` on all queries | ✅ |
| **PublicProfileController** | **Fixed: albums added** | ✅ |
| ExportController | N/A (no child relations) | ✅ |
| NotificationController | `with('child')` | ✅ |
| FacilityAdmin/* | `with(['child', 'staffUser'])` etc. | ✅ |

---

## 📊 Status Otorisasi per Controller

| Controller | Resource-Child Check | User-Child Check | Status |
|---|---|---|---|
| ChildController | Via middleware `child.ownership` | Via middleware | ✅ |
| TimelineController | `abort_unless($timeline->child_id === $child->id)` | Via middleware | ✅ |
| AlbumController | `abort_unless($album->child_id === $child->id)` | Via middleware | ✅ |
| DiaryController | `abort_unless($diary->child_id === $child->id)` | Via middleware | ✅ |
| GrowthController | `abort_unless($growth->child_id === $child->id)` | Via middleware | ✅ |
| HealthController | `abort_unless($healthRecord->child_id === $child->id)` | Via middleware | ✅ |
| DocumentController | `abort_unless($document->child_id === $child->id)` | Via middleware | ✅ |
| CalendarController | `abort_unless($event->child_id === $child->id)` | Via middleware | ✅ |
| FamilyMemberController | `abort_unless($familyMember->child_id === $child->id)` | Via middleware | ✅ |
| MilestoneController | `abort_unless($milestoneAlert->child_id === $child->id)` | Via middleware | ✅ |
| ExportController | Via `child.ownership` middleware | Via `child.ownership` middleware | ✅ |
| PublicProfileController | Public access (no auth) | `is_public` check | ✅ |

---

## 📊 Status Role & Permission

### Roles yang Ada
1. **super_admin** — Akses Super Admin Panel (`/super-admin/*`)
2. **tenant_admin** — Akses Tenant Admin Panel (`/admin/*`)
3. **parent** — Akses modul anak, timeline, album, dll (`/children/*`)

### Staff Roles (B2B)
1. **admin** (staff_admin) — Akses penuh facility admin
2. **doctor** — Akses clinical notes, referrals
3. **midwife** — Akses clinical notes, referrals
4. **nurse** — Akses clinical notes, patients
5. **staff** — Akses terbatas

### Rekomendasi Role & Permission
Saat ini role sudah cukup untuk MVP. Untuk future enhancement:
- Pertimbangkan Spatie Laravel Permission untuk granular permission
- Tambahkan permission: `view_child`, `edit_child`, `delete_child`, `export_child`
- Tambahkan permission: `view_clinical_note`, `create_clinical_note`, `edit_clinical_note`
- Tambahkan permission: `manage_staff`, `manage_patients`, `view_reports`

---

## 📋 Rencana Implementasi

### Ringan (Phase 16A) — Quick Fixes ✅
1. ✅ Fix PublicProfileController eager loading (tambahkan `albums`)
2. ✅ Fix ExportController — otorisasi sudah benar (middleware `child.ownership`)
3. ✅ Fix AlbumController redundant `withCount`
4. ✅ Fix DiaryController & AlbumController — gunakan DI untuk MediaService
5. ✅ Fix PatientLinkController — scope parents ke tenant
6. ✅ Tambahkan test otorisasi ExportController (9 tests, 10 assertions)
7. ✅ Tambahkan MinIO/AWS config ke `.env.example` (`AWS_URL`, `AWS_ENDPOINT`)
8. ✅ `.gitignore` sudah punya `.env.production`

### Sedang (Phase 16B) ✅
9. ⏭️ Tambahkan `is_public` flag pada Album model — DITUNDA ke Phase 17 (perlu migration)
10. ✅ Verifikasi semua eager loading di dashboard view — sudah benar

### Besar (Phase 17 — Future)
11. Migrasi ke Spatie Laravel Permission
12. Granular permission system untuk B2B
13. Hapus `.env.production` dari git history (git filter-branch)
14. Tambahkan `is_public` flag pada Album model (memerlukan migration)
