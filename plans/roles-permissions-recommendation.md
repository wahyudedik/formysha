# Rekomendasi Roles & Permissions — ForMysha

**Tanggal:** 10 Agustus 2026
**Status:** Rekomendasi untuk Phase 19+

---

## 1. Roles Saat Ini

### System-Level Roles (User.role)

| Role | Label | Keterangan |
|------|-------|------------|
| `super_admin` | Super Admin | Full akses ke semua tenant, billing, monitoring |
| `tenant_admin` | Tenant Admin | Kelola tenant sendiri (branding, staf, settings) |
| `parent` | Orang Tua | Default role untuk pengguna B2C dan B2B |

### Staff-Level Roles (Staff.staff_role) — B2B Only

| Role | Label | Level | Clinical Notes | Referrals | Admin |
|------|-------|-------|---------------|-----------|-------|
| `staff` | Staf | 1 | ❌ | ❌ | ❌ |
| `nurse` | Perawat | 2 | ✅ | ❌ | ❌ |
| `midwife` | Bidan | 3 | ✅ | ✅ | ❌ |
| `doctor` | Dokter | 4 | ✅ | ✅ | ❌ |
| `staff_admin` | Admin Fasilitas | 5 | ✅ | ✅ | ✅ |

---

## 2. Rekomendasi: Granular Permission System

### 2.1 Mengapa Perlu Granular Permission?

Saat ini sistem menggunakan **role-based access** sederhana dengan 3 user roles dan 5 staff roles. Untuk skala enterprise, diperlukan **permission-based access** yang lebih fleksibel.

**Masalah saat ini:**
- Role `parent` memiliki akses yang sama ke semua fitur (anak, timeline, album, dokumen, dll)
- Tidak ada kontrol granular untuk fitur tertentu (misal: parent hanya boleh lihat tapi tidak boleh edit)
- Family sharing tidak memiliki permission level (read-only vs full access)
- B2B staff permission hanya berdasarkan role, bukan per-fitur

### 2.2 Proposed Permission Matrix

#### B2C (Keluarga)

| Permission | Owner (Orang Tua) | Family Member (View) | Family Member (Edit) |
|-----------|-------------------|---------------------|---------------------|
| children.view | ✅ | ✅ | ✅ |
| children.create | ✅ | ❌ | ✅ |
| children.edit | ✅ | ❌ | ✅ |
| children.delete | ✅ | ❌ | ❌ |
| timeline.view | ✅ | ✅ | ✅ |
| timeline.create | ✅ | ❌ | ✅ |
| timeline.edit | ✅ | ❌ | ✅ |
| timeline.delete | ✅ | ❌ | ❌ |
| album.view | ✅ | ✅ | ✅ |
| album.create | ✅ | ❌ | ✅ |
| album.delete | ✅ | ❌ | ❌ |
| diary.view | ✅ | ✅ | ✅ |
| diary.create | ✅ | ❌ | ✅ |
| document.view | ✅ | ✅ | ❌ |
| document.create | ✅ | ❌ | ✅ |
| health.view | ✅ | ✅ | ❌ |
| health.create | ✅ | ❌ | ✅ |
| growth.view | ✅ | ✅ | ✅ |
| growth.create | ✅ | ❌ | ✅ |
| export.view | ✅ | ❌ | ❌ |
| profile.view | ✅ | ✅ | ✅ |
| public_profile.edit | ✅ | ❌ | ❌ |

#### B2B (Fasilitas)

| Permission | Staff Admin | Doctor | Midwife | Nurse | Staff |
|-----------|-------------|--------|---------|-------|-------|
| patients.view | ✅ | ✅ | ✅ | ✅ | ✅ |
| patients.create | ✅ | ❌ | ❌ | ❌ | ❌ |
| patients.link | ✅ | ✅ | ✅ | ❌ | ❌ |
| clinical_notes.view | ✅ | ✅ | ✅ | ✅ | ❌ |
| clinical_notes.create | ✅ | ✅ | ✅ | ✅ | ❌ |
| clinical_notes.delete | ✅ | ❌ | ❌ | ❌ | ❌ |
| referrals.view | ✅ | ✅ | ✅ | ❌ | ❌ |
| referrals.create | ✅ | ✅ | ✅ | ❌ | ❌ |
| referrals.approve | ✅ | ❌ | ❌ | ❌ | ❌ |
| staff.view | ✅ | ❌ | ❌ | ❌ | ❌ |
| staff.create | ✅ | ❌ | ❌ | ❌ | ❌ |
| staff.edit | ✅ | ❌ | ❌ | ❌ | ❌ |
| settings.view | ✅ | ❌ | ❌ | ❌ | ❌ |
| settings.edit | ✅ | ❌ | ❌ | ❌ | ❌ |
| reports.view | ✅ | ✅ | ✅ | ✅ | ❌ |
| reports.export | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## 3. Rekomendasi Implementasi

### 3.1 Phase 19A — Family Sharing Permission Level

**Prioritas: TINGGI** — Ini adalah gap terbesar saat ini.

**Masalah:** Anggota keluarga yang diundang memiliki akses penuh ke semua data anak, sama seperti owner.

**Solusi:** Tambahkan field `permission_level` ke model `FamilyMember`:

```php
// Migration baru
Schema::table('family_members', function (Blueprint $table) {
    $table->string('permission_level')->default('view'); // 'view', 'edit', 'admin'
});
```

**Permission Levels:**
- `view` — Hanya bisa melihat data (default untuk kakek/nenek/teman)
- `edit` — Bisa menambah/mengedit data (default untuk pasangan/saudara)
- `admin` — Bisa mengelola semua data (hanya untuk co-parent)

**Middleware baru:**
```php
// FamilyMemberPermission middleware
// Check: family_member.permission_level >= required_level
```

### 3.2 Phase 19B — B2B Granular Permissions

**Prioritas: SEDANG**

**Solusi:** Tambahkan tabel `staff_permissions` untuk override role defaults:

```php
Schema::create('staff_permissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('staff_id')->constrained()->onDelete('cascade');
    $table->string('permission'); // 'clinical_notes.create', 'referrals.approve', dll
    $table->boolean('granted')->default(true);
    $table->timestamps();
});
```

**Pola:** Role-based default + per-staff override.

### 3.3 Phase 19C — Permission Middleware

**Prioritas: SEDANG**

Buat middleware `EnsurePermission` yang bisa digunakan di routes:

```php
Route::middleware('permission:clinical_notes.create')->group(function () {
    // Hanya staff yang punya permission ini bisa akses
});
```

### 3.4 Phase 19D — Permission Management UI

**Prioritas: RENDAH**

- Tenant Admin bisa mengelola permission staff melalui panel
- Owner bisa mengatur permission level anggota keluarga
- Super Admin bisa melihat audit log permission changes

---

## 4. Rekomendasi Prioritas

| No | Item | Prioritas | Estimasi | Phase |
|----|------|-----------|----------|-------|
| 1 | Family Member permission level (view/edit/admin) | TINGGI | 2-3 hari | 19A |
| 2 | Family Member permission middleware | TINGGI | 1 hari | 19A |
| 3 | Family Member permission UI | TINGGI | 1 hari | 19A |
| 4 | B2B staff_permissions table | SEDANG | 1 hari | 19B |
| 5 | B2B staff permission override | SEDANG | 2 hari | 19B |
| 6 | EnsurePermission middleware | SEDANG | 1 hari | 19C |
| 7 | Permission management UI | RENDAH | 3 hari | 19D |
| 8 | Permission audit log | RENDAH | 1 hari | 19D |

---

## 5. Catatan Keamanan

### 5.1 Current Security Model

- ✅ Child ownership enforced via `EnsureChildOwnership` middleware
- ✅ Role-based access via `EnsureRole` middleware
- ✅ Subscription check via `EnsureActiveSubscription` middleware
- ✅ Feature limits via `EnsureFeatureLimit` middleware
- ✅ Tenant isolation via column-based tenancy (tenant_id)
- ✅ API rate limiting (60/min general, 5/min auth)
- ✅ Sanctum token-based API auth

### 5.2 Security Gaps

- ⚠️ Family members have same access level as owner (no granular permissions)
- ⚠️ No permission audit trail for family member actions
- ⚠️ B2B staff roles are rigid (no per-staff overrides)
- ⚠️ No IP-based access restrictions for sensitive operations
- ⚠️ No 2FA/MFA support (recommended for Super Admin)

### 5.3 Recommended Security Enhancements

| No | Enhancement | Prioritas | Phase |
|----|------------|-----------|-------|
| 1 | Family member permission levels | TINGGI | 19A |
| 2 | Permission audit trail | SEDANG | 19B |
| 3 | 2FA for Super Admin | SEDANG | 19C |
| 4 | IP whitelist for admin panels | RENDAH | 19D |
| 5 | Session timeout configuration | RENDAH | 19D |

---

## 6. Rekomendasi untuk Regulasi

### 6.1 UU PDP (Pelindungan Data Pribadi)

- Data anak termasuk data pribadi yang dilindungi
- Diperlukan consent dari orang tua/wali sebelum pengumpulan data
- Data kesehatan dan dokumen memerlukan proteksi ekstra
- Right to erasure (hapus data) harus tersedia

### 6.2 HIPAA (untuk B2B Healthcare)

- Clinical notes dan health records memerlukan akses terbatas
- Audit trail wajib untuk semua akses data pasien
- Encryption untuk data in-transit dan at-rest
- Business Associate Agreement (BAA) untuk setiap fasilitas

### 6.3 Rekomendasi Compliance

| No | Item | Regulasi | Prioritas |
|----|------|----------|-----------|
| 1 | Consent management untuk data anak | UU PDP | TINGGI |
| 2 | Data retention policy | UU PDP | TINGGI |
| 3 | Right to erasure | UU PDP | TINGGI |
| 4 | Audit trail untuk data kesehatan | HIPAA | SEDANG |
| 5 | Encryption at-rest untuk dokumen | HIPAA | SEDANG |
| 6 | BAA template untuk B2B | HIPAA | RENDAH |

---

## 7. Migration Plan

### Phase 19A (Quick Win — 4-5 hari)
1. Tambah field `permission_level` ke `family_members` table
2. Update `FamilyMemberController` untuk handle permission level
3. Buat middleware `EnsureFamilyPermission`
4. Update family sharing views untuk tampilkan permission level
5. Tests baru untuk permission checking

### Phase 19B (Medium — 4-5 hari)
1. Buat tabel `staff_permissions`
2. Update `StaffRole` enum dengan permission matrix
3. Buat `StaffPermissionService`
4. Update facility admin views
5. Tests baru

### Phase 19C (Foundation — 2-3 hari)
1. Buat `EnsurePermission` middleware
2. Update routes untuk gunakan permission middleware
3. Audit semua routes yang perlu permission check

### Phase 19D (UI & Polish — 4-5 hari)
1. Permission management UI untuk Tenant Admin
2. Permission audit log
3. 2FA untuk Super Admin
4. IP whitelist (opsional)
