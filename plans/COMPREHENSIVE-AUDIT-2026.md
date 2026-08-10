# Audit Komprehensif Proyek ForMysha

**Tanggal:** 10 Agustus 2026
**Status:** Phase 13 selesai — 614 tests, 1404 assertions

---

## Ringkasan Temuan

Kategori | Jumlah | Status
--- | --- | ---
🐛 Bug Kritis | 4 | Perlu perbaikan segera
⚡ UX Improvement | 2 | Ringan, bisa langsung dikerjakan
✅ Responsive Design | Lolos | Semua view sudah benar
✅ Clean Code | Lolos | Tidak ada dd/dump/console.log
✅ Navigation | Lolos | Semua route terhubung
✅ RBAC | Lolos | Middleware dan ownership check lengkap

---

## 🐛 BUG KRITIS

### Bug 1: ReportController pakai SQLite syntax, bukan MySQL

**File:** `app/Http/Controllers/FacilityAdmin/ReportController.php:51`
**Masalah:** Menggunakan `strftime('%Y-%m', created_at)` yang merupakan syntax SQLite. Proyek ini pakai MySQL.
**Dampak:** Halaman Laporan Fasilitas akan crash/error di production.
**Fix:** Ganti dengan `DATE_FORMAT(created_at, '%Y-%m')` atau pakai Laravel query builder.

```php
// Salah (SQLite)
->selectRaw("strftime('%Y-%m', created_at) as month, count(*) as total")

// Benar (MySQL)
->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
```

### Bug 2: StaffController buat user dengan role tenant_admin

**File:** `app/Http/Controllers/FacilityAdmin/StaffController.php:66`
**Masalah:** Saat membuat staf baru, user dibuat dengan `'role' => 'tenant_admin'`. Ini memberikan akses penuh ke Tenant Admin panel kepada semua staf.
**Dampak:** Semua staf bisa akses halaman admin, mengubah pengaturan, branding, dll.
**Fix:** Ganti role menjadi `'role' => 'parent'` atau buat role dedicated `'staff'`.

```php
// Salah
'role' => 'tenant_admin',

// Benar — staf tidak seharusnya punya akses tenant admin
'role' => 'parent',
```

### Bug 3: Facility Admin child queries salah untuk B2B

**File:** Beberapa controller Facility Admin
**Masalah:** Query `Child::where('tenant_id', $tenant->id)` hanya mengambil anak yang belonging ke tenant fasilitas sendiri. Untuk B2B, pasien adalah anak dari tenant orang tua (parent), bukan dari tenant fasilitas.
**Dampak:** Dropdown pasien kosong saat buat catatan klinis, tautan pasien, atau rujukan.

**File yang terdampak:**
- `PatientLinkController.php:39` — `$children = Child::where('tenant_id', $tenant->id)`
- `ClinicalNoteController.php:40` — `$children = Child::where('tenant_id', $tenant->id)`
- `ClinicalNoteController.php:104` — `$children = Child::where('tenant_id', $tenant->id)`
- `ReferralController.php:53` — `$children = Child::where('tenant_id', $tenant->id)`

**Fix:** Ambil anak dari tenant yang terhubung via PatientLink, atau ambil semua anak dari parent users yang terdaftar.

```php
// Salah
$children = Child::where('tenant_id', $tenant->id)->get();

// Benar — ambil anak dari pasien yang terhubung
$linkedChildIds = PatientLink::where('facility_tenant_id', $tenant->id)
    ->where('status', 'active')
    ->pluck('child_id');
$children = Child::whereIn('id', $linkedChildIds)->with('user')->get();
```

### Bug 4: Public profile tampilkan placeholder "Fitur penghargaan segera hadir"

**File:** `resources/views/public/profile.blade.php:183`
**Masalah:** Sistem Achievement sudah ada (model, controller, service) tapi halaman publik masih menampilkan placeholder.
**Dampak:** UX buruk untuk pengunjung halaman publik.
**Fix:** Query data achievement dan tampilkan di public profile.

---

## ⚡ UX IMPROVEMENT

### UX 1: Profile edit tidak ada breadcrumb

**File:** `resources/views/profile/edit.blade.php`
**Masalah:** Halaman lain sudah pakai `<x-breadcrumb>` tapi profile edit belum.
**Fix:** Tambahkan breadcrumb component.

### UX 2: Subscription history mobile cards status kurang jelas

**File:** `resources/views/subscription/history.blade.php:107`
**Masalah:** Mobile cards menampilkan `ucfirst($sub->status)` tanpa emoji/formatting, sedangkan desktop table sudah lengkap.
**Fix:** Tambahkan emoji dan label yang sama dengan desktop table.

---

## ✅ YANG SUDAH BENAR

### Responsive Design
- Header flex responsive: `flex-col sm:flex-row sm:items-center sm:justify-between gap-3` ✅
- Container padding: `px-4 sm:px-6 lg:px-8` ✅
- Card/Form padding: `p-4 sm:p-6 lg:p-8` ✅
- Submit buttons stack: `flex flex-col sm:flex-row items-stretch sm:items-center gap-3` ✅
- Touch targets: `min-h-[44px]` pada semua tombol ✅
- Table wrapper: `overflow-x-auto` ✅
- Mobile bottom nav di child-nav ✅
- Subscription history: desktop table + mobile cards ✅

### Clean Code
- Tidak ada `dd()`, `dump()`, `ray()`, `var_dump()`, `print_r()` ✅
- Tidak ada `console.log()` di blade templates ✅
- Tidak ada `TODO`, `FIXME`, `HACK` comments ✅
- Laravel Pint formatting bersih ✅

### Navigation
- Super Admin sidebar: 9 menu items terhubung ke routes ✅
- Facility Admin sidebar: 7 menu items terhubung ke routes ✅
- Child nav: 12 modules terhubung ke routes ✅
- Landing page: B2B section terhubung ke `register.facility` ✅
- Login/Register: B2B links terhubung ✅
- Public profile route di posisi terakhir di `routes/web.php` ✅

### RBAC & Security
- `role:super_admin` middleware di Super Admin routes ✅
- `role:tenant_admin` middleware di Tenant Admin routes ✅
- `tenant.active` middleware di Facility Admin routes ✅
- `staff.role` middleware untuk role-based access ✅
- `child.ownership` middleware untuk child routes ✅
- `abort_unless()` ownership check di semua controllers ✅
- Feature limit middleware aktif ✅

---

## Rencana Perbaikan

| No | Prioritas | Deskripsi | File |
|----|-----------|-----------|------|
| 1 | 🔴 Bug | Ganti `strftime` ke `DATE_FORMAT` di ReportController | `app/Http/Controllers/FacilityAdmin/ReportController.php` |
| 2 | 🔴 Bug | Ganti role `tenant_admin` ke `parent` di StaffController | `app/Http/Controllers/FacilityAdmin/StaffController.php` |
| 3 | 🔴 Bug | Fix child queries di Facility Admin controllers | `PatientLinkController.php`, `ClinicalNoteController.php`, `ReferralController.php` |
| 4 | 🟡 Feature | Tampilkan achievement di public profile | `resources/views/public/profile.blade.php` |
| 5 | 🟢 UX | Tambah breadcrumb di profile edit | `resources/views/profile/edit.blade.php` |
| 6 | 🟢 UX | Perbaiki status label di subscription history mobile | `resources/views/subscription/history.blade.php` |
| 7 | 🔵 Test | Tambah/update tests untuk perbaikan di atas | `tests/Feature/FacilityAdminTest.php` |
| 8 | 🔵 Docs | Update FEATURES.md dan ROADMAP.md | `FEATURES.md`, `ROADMAP.md` |
