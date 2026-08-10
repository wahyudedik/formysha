# Audit Report: B2B Facility Flow

**Tanggal:** 2026-08-10
**Status:** Audit selesai, 2 bug ditemukan

---

## Ringkasan Audit

### Komponen yang Dicek

| Komponen | Jumlah | Status |
|----------|--------|--------|
| Routes | 1 file (22 routes) | ✅ Aman |
| Controllers | 7 files | ✅ Aman |
| Middleware | 4 files | ✅ Aman |
| Views (Dark Mode) | 21 files | ✅ Konsisten |
| Views (Responsive) | 21 files | ✅ Konsisten |
| Views (Components) | 21 files | ✅ Konsisten |
| Views (Loading States) | 7 form files | ✅ Lengkap |
| Registration Flow | 1 controller | ✅ Aman |
| Tests | 2 files (819 + 248 lines) | ✅ Komprehensif |

---

## Temuan Positif

### 1. Routes (`routes/facility-admin.php`)
- Middleware stack lengkap: `auth`, `verified`, `tenant.active`
- Staff role middleware diterapkan per route group sesuai kebutuhan
- Dashboard: semua role bisa akses
- Staff Management: `staff_admin` dan `doctor` saja
- Patient Links: `staff_admin`, `doctor`, `midwife`, `nurse`
- Clinical Notes: `doctor`, `midwife`, `nurse` (bukan staff_admin)
- Referrals: `staff_admin`, `doctor`, `midwife`
- Reports & Settings: `staff_admin` saja

### 2. Controllers
Semua 7 controller memiliki tenant isolation yang benar:
- [`StaffController`](app/Http/Controllers/FacilityAdmin/StaffController.php): `abort_unless($staff->tenant_id === $tenant->id, 403)`
- [`PatientLinkController`](app/Http/Controllers/FacilityAdmin/PatientLinkController.php): `abort_unless($patientLink->facility_tenant_id === $tenant->id, 403)`
- [`ClinicalNoteController`](app/Http/Controllers/FacilityAdmin/ClinicalNoteController.php): `abort_unless($clinicalNote->tenant_id === $tenant->id, 403)`
- [`ReferralController`](app/Http/Controllers/FacilityAdmin/ReferralController.php): Dual check `from_tenant_id || to_tenant_id`
- [`DashboardController`](app/Http/Controllers/FacilityAdmin/DashboardController.php): Query scoped ke tenant
- [`ReportController`](app/Http/Controllers/FacilityAdmin/ReportController.php): Query scoped ke tenant
- [`FacilitySettingsController`](app/Http/Controllers/FacilityAdmin/FacilitySettingsController.php): Query scoped ke tenant

### 3. Middleware
- [`EnsureFacilityAccess`](app/Http/Middleware/EnsureFacilityAccess.php): Cek super_admin, tenant_admin, atau staff aktif
- [`EnsureStaffRole`](app/Http/Middleware/EnsureStaffRole.php): Cek role staf, super_admin bypass
- [`EnsureActiveSubscription`](app/Http/Middleware/EnsureActiveSubscription.php): Cek subscription aktif, skip super_admin dan tenant_admin
- [`ResolveTenant`](app/Http/Middleware/ResolveTenant.php): Resolve tenant dari route/session/user

### 4. Views - Dark Mode
Semua 21 view B2B menggunakan dark mode classes dengan custom color variants yang benar:
- `dark:bg-gray-800`, `dark:text-gray-100`, `dark:text-gray-400`
- Custom colors: `dark:bg-skyBlue-950/30`, `dark:bg-softPink-950/30`, `dark:bg-mintGreen-950/30`, dll.

### 5. Views - Responsive Design
Semua view mengikuti responsive patterns:
- Header: `flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3`
- Container: `px-4 sm:px-6 lg:px-8`
- Card/Form: `p-4 sm:p-6 lg:p-8`
- Submit buttons: `flex flex-col sm:flex-row items-stretch sm:items-center gap-3`
- Touch targets: `min-h-[44px]`
- Tables: `overflow-x-auto`
- Sidebar: `flex-col lg:flex-row`

### 6. Views - Components & Loading States
Semua view menggunakan Blade components yang benar:
- `<x-app-layout>`, `<x-breadcrumb>`, `<x-empty-state>`, `<x-confirm-delete>`
- `<x-input-label>`, `<x-text-input>`, `<x-input-error>`
- Semua 7 form memiliki Alpine.js loading states

### 7. Registration Flow
[`FacilityRegisteredUserController`](app/Http/Controllers/Auth/FacilityRegisteredUserController.php):
- Validasi lengkap (name, email, password, facility_name, facility_type)
- Membuat user + tenant B2B + staff record
- Set tenant di session setelah registrasi
- Redirect ke facility dashboard

### 8. Test Coverage
[`FacilityAdminTest.php`](tests/Feature/FacilityAdminTest.php) (819 lines):
- Dashboard access (3 tests)
- Staff CRUD + authorization (8 tests)
- Patient Links CRUD + authorization (7 tests)
- Clinical Notes CRUD + authorization (8 tests)
- Referrals CRUD + authorization (8 tests)
- Settings CRUD + authorization (4 tests)
- Reports access + authorization (5 tests)
- Cross-facility access prevention (3 tests)

[`FacilityRegistrationTest.php`](tests/Feature/FacilityRegistrationTest.php) (248 lines):
- Registration form display
- Multiple facility types (clinic, hospital, midwifery, daycare, school)
- Validation (required fields, email uniqueness, facility type)
- Staff record creation
- Role assignment
- Session setup

---

## Bug yang Ditemukan

### Bug 1: Referral Form Method Mismatch (PATCH vs POST) 🔴 KRITIS

**Lokasi:**
- Routes: [`routes/facility-admin.php`](routes/facility-admin.php:66-68) — menggunakan `Route::post()`
- View: [`resources/views/facility-admin/referrals/show.blade.php`](resources/views/facility-admin/referrals/show.blade.php:16-22) — menggunakan `@method('PATCH')`

**Masalah:**
Form referral (accept, complete, cancel) menggunakan `@method('PATCH')` tetapi route didaftarkan sebagai `Route::post()`. Ketika form disubmit:
1. Browser mengirim POST request dengan `_method=PATCH`
2. Laravel middleware mengkonversi method ke PATCH
3. Router mencoba match PATCH routes
4. Tidak ada route PATCH yang terdaftar
5. Hasil: **405 Method Not Allowed**

**Dampak:**
- User tidak bisa menerima, menyelesaikan, atau membatalkan rujukan melalui UI
- Test passing karena menggunakan `->post()` langsung tanpa method spoofing

**Fix:**
Hapus `@method('PATCH')` dari ketiga form dan ubah ke POST (atau ubah route ke `Route::patch()`)

### Bug 2: Facility Create Missing Name 🟡 MEDIUM

**Lokasi:**
- [`app/Http/Controllers/FacilityAdmin/FacilitySettingsController.php`](app/Http/Controllers/FacilityAdmin/FacilitySettingsController.php:74-84)

**Masalah:**
Ketika membuat Facility baru (else branch), field `name` tidak di-include:
```php
Facility::create([
    'tenant_id' => $tenant->id,
    'facility_type' => $tenant->facility_type?->value ?? 'clinic',
    'address' => $validated['address'] ?? null,
    // 'name' TIDAK ADA di sini!
]);
```

Tetapi ketika update, `name` di-include:
```php
$facility->update([
    'name' => $validated['name'],
    // ...
]);
```

**Dampak:**
- Facility record baru tidak memiliki nama
- Inconsistency antara create dan update path

**Fix:**
Tambahkan `'name' => $validated['name']` ke Facility::create()

---

## Catatan Tambahan

### Middleware `facility.access` Tidak Digunakan
[`EnsureFacilityAccess`](app/Http/Middleware/EnsureFacilityAccess.php) terdaftar di `bootstrap/app.php` sebagai `facility.access` tetapi TIDAK diterapkan ke rute facility-admin. Ini bukan bug karena controller memiliki authorization sendiri via `abort_unless`, tetapi bisa ditambahkan sebagai defense-in-depth.

### `EnsureActiveSubscription` Skip Tenant Admin
[`EnsureActiveSubscription`](app/Http/Middleware/EnsureActiveSubscription.php:27) skip check untuk `tenant_admin`. Ini mungkin disengaja (memungkinkan setup sebelum subscribe), tetapi worth noting.
