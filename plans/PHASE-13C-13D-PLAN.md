# Phase 13C-13D & Quality Improvement Plan

## Status Saat Ini

### Phase 13A — Foundation ✅
### Phase 13B — Facility Admin Panel ✅
- Controllers: 7 (Dashboard, Staff, PatientLink, ClinicalNote, Referral, Report, Settings)
- Views: 22 blade files
- Tests: 67 tests (14 Registration + 53 Admin), 157 assertions
- Bug fixes: route registration, facility_type, DATE_FORMAT, patientLinks FK, email_institution

---

## Phase 13C — Clinical Features Polish

### Gap Analysis

| Item | Status | Catatan |
|------|--------|---------|
| Clinical Notes CRUD | ✅ Done | Controller + views ada |
| Referrals CRUD | ✅ Done | Controller + views ada |
| Reports | ✅ Done | Index, clinical-notes, patients |
| Patient-Parent Linking | ✅ Done | PatientLinkController ada |

### Yang Perlu Diperbaiki di Phase 13C

1. **Loading States** — Semua form facility-admin belum punya Alpine.js loading states
   - Staff: create, edit
   - Patient: create
   - Clinical Notes: create, edit
   - Referrals: create
   - Settings: edit

2. **Confirm Delete Modals** — Beberapa view belum punya `<x-confirm-delete>`
   - Staff index/show: perlu delete confirmation
   - Clinical Notes index/show: perlu delete confirmation
   - Referrals: perlu cancel confirmation

3. **Sidebar Navigation** — Facility-admin belum punya sidebar seperti super-admin
   - Perlu buat `facility-admin/partials/sidebar.blade.php`
   - Sidebar harus responsive (mobile drawer)

4. **Breadcrumb Navigation** — Belum ada breadcrumb di facility-admin views

---

## Phase 13D — Super Admin B2B

### 13D.1 B2B Dashboard Section

Super Admin perlu melihat data B2B terpisah dari B2C:

**File yang perlu diubah:**
- `app/Http/Controllers/SuperAdmin/DashboardController.php` — tambah stats B2B
- `resources/views/super-admin/dashboard.blade.php` — tambah section B2B

**Data yang perlu ditampilkan:**
- Total B2B tenants (klinik, rumah sakit, bidan, daycare, sekolah)
- Total staff aktif di semua fasilitas
- Total patient links aktif
- Total clinical notes bulan ini
- Total referrals pending
- Revenue dari B2B vs B2C

### 13D.2 B2B Tenant Detail

Super Admin perlu melihat detail B2B tenant:

**File baru:**
- `resources/views/super-admin/tenants/b2b-show.blade.php`

**Data yang perlu ditampilkan:**
- Info fasilitas (type, alamat, telepon, email)
- Daftar staff dengan role
- Statistik patient links
- Statistik clinical notes
- Statistik referrals

### 13D.3 B2B Analytics

**File yang perlu diubah:**
- `app/Http/Controllers/SuperAdmin/AnalyticsController.php` — tambah data B2B
- `resources/views/super-admin/analytics/index.blade.php` — tambah chart B2B

**Metrik:**
- Growth B2B tenants per bulan
- Clinical notes per fasilitas
- Patient links per fasilitas
- Referrals per bulan

### 13D.4 B2B Monitoring

**File yang perlu diubah:**
- `app/Http/Controllers/SuperAdmin/MonitoringController.php` — tambah B2B monitoring
- `resources/views/super-admin/monitoring/index.blade.php` — tambah section B2B

**Data:**
- Fasilitas dengan staff paling aktif
- Fasilitas dengan clinical notes paling banyak
- Fasilitas dengan referral pending terbanyak

---

## Quality Improvement — Ringan

### Q1. Loading States untuk Facility Admin Forms

Tambahkan `x-data="{ loading: false }"` + `@submit="loading = true"` + `:disabled="loading"` pada semua form:

| View | Status |
|------|--------|
| staff/create.blade.php | Perlu |
| staff/edit.blade.php | Perlu |
| patients/create.blade.php | Perlu |
| clinical-notes/create.blade.php | Perlu |
| clinical-notes/edit.blade.php | Perlu |
| referrals/create.blade.php | Perlu |
| settings/edit.blade.php | Perlu |

### Q2. Confirm Delete untuk Facility Admin

Tambahkan `<x-confirm-delete>` pada:

| View | Action | Status |
|------|--------|--------|
| staff/index.blade.php | Hapus staf | Perlu |
| staff/show.blade.php | Hapus staf | Perlu |
| clinical-notes/index.blade.php | Hapus catatan | Perlu |
| clinical-notes/show.blade.php | Hapus catatan | Perlu |
| referrals/show.blade.php | Batalkan rujukan | Perlu |

### Q3. Sidebar Navigation untuk Facility Admin

Buat `resources/views/facility-admin/partials/sidebar.blade.php`:

Menu items:
- 🏥 Dashboard (`facility.dashboard`)
- 👨‍⚕️ Staf (`facility.staff.index`)
- 👶 Pasien (`facility.patients.index`)
- 📋 Catatan Klinis (`facility.clinical-notes.index`)
- 🔄 Rujukan (`facility.referrals.index`)
- 📊 Laporan (`facility.reports.index`)
- ⚙️ Pengaturan (`facility.settings.edit`)

Mobile: hamburger menu / drawer
Desktop: sidebar kiri

### Q4. Breadcrumb Navigation

Tambahkan breadcrumb component pada sub-halaman:

| View | Breadcrumb |
|------|------------|
| staff/create | Dashboard > Staf > Tambah Baru |
| staff/show | Dashboard > Staf > [Nama] |
| staff/edit | Dashboard > Staf > [Nama] > Edit |
| patients/create | Dashboard > Pasien > Tambah Baru |
| patients/show | Dashboard > Pasien > [Nama] |
| clinical-notes/* | Dashboard > Catatan Klinis > ... |
| referrals/* | Dashboard > Rujukan > ... |
| reports/* | Dashboard > Laporan > ... |
| settings/edit | Dashboard > Pengaturan |

---

## Quality Improvement — Besar

### Q5. Responsive Design Audit Facility Admin

Pastikan semua view遵循 responsive patterns:
- Header: `flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3`
- Container: `px-4 sm:px-6 lg:px-8`
- Card/Form: `p-4 sm:p-6 lg:p-8`
- Tables: `overflow-x-auto`
- Buttons: `min-h-[44px]`

### Q6. Empty State Standarisasi

Gunakan `<x-empty-state>` component untuk semua empty states di facility-admin.

### Q7. Navigation Mobile untuk Facility Admin

Pastikan sidebar berubah jadi hamburger menu di mobile.

---

## Documentation Updates

### FEATURES.md
Tambahkan section:
- B2B Facility Admin Panel ✅ (Phase 13B)
- B2B Clinical Features ✅ (Phase 13C)
- B2B Super Admin Monitoring ✅ (Phase 13D)

### ROADMAP.md
Tambahkan section:
- Phase 13A ✅
- Phase 13B ✅
- Phase 13C ✅
- Phase 13D ✅

### AGENTS.md
Update:
- Testing Conventions: tambah 67 tests baru
- Quality Assurance: update total tests
- SaaS File Locations: tambah facility-admin files

---

## Execution Order

### Wave 1 — Ringan (Phase 13C Polish)
1. [ ] Buat sidebar facility-admin + responsive mobile
2. [ ] Tambah loading states ke 7 form
3. [ ] Tambah confirm-delete ke 5 view
4. [ ] Tambah breadcrumb ke sub-halaman
5. [ ] Standarisasi empty states

### Wave 2 — Besar (Phase 13D)
6. [ ] Update DashboardController + view untuk B2B stats
7. [ ] Buat b2b-show view untuk tenant detail
8. [ ] Update AnalyticsController + view untuk B2B analytics
9. [ ] Update MonitoringController + view untuk B2B monitoring

### Wave 3 — Documentation & QA
10. [ ] Update FEATURES.md
11. [ ] Update ROADMAP.md
12. [ ] Update AGENTS.md
13. [ ] Jalankan full test suite
14. [ ] Jalankan Pint formatter
15. [ ] Responsive design audit
