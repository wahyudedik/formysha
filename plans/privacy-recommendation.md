# Rekomendasi Privacy & Data Protection — ForMysha

**Tanggal:** 10 Agustus 2026
**Status:** Rekomendasi untuk Phase 19+

---

## 1. Klasifikasi Data

### 1.1 Data Pribadi (Personal Data)

| Kategori | Field | Sensitivity | Lokasi |
|----------|-------|-------------|--------|
| **Identitas Anak** | name, nickname, gender, date_of_birth, place_of_birth | TINGGI | children |
| **Foto Anak** | photo, cover_photo | TINGGI | children |
| **Profil Orang Tua** | name, email, phone, avatar, date_of_birth, address | TINGGI | users |
| **Kontak Darurat** | emergency_contact, emergency_phone | TINGGI | children |
| **Info Keluarga** | name, relationship, phone, email, photo | SEDANG | family_members |

### 1.2 Data Kesehatan (Health Data) — SENSITIF TINGGI

| Kategori | Field | Sensitivity | Lokasi |
|----------|-------|-------------|--------|
| **Riwayat Kesehatan** | record_type, title, description, doctor_name | SANGAT TINGGI | health_records |
| **Detail Medis** | medication, dosage, vital_signs, diagnosis | SANGAT TINGGI | health_records |
| **Alergi & Kondisi** | allergies, medical_conditions | SANGAT TINGGI | children |
| **Data Pertumbuhan** | height, weight, head_circumference | TINGGI | growths |
| **Imunisasi** | vaccine_name, date_given, batch_number | TINGGI | health_records |
| **Catatan Klinis (B2B)** | clinical_note_type, content, diagnosis, treatment | SANGAT TINGGI | clinical_notes |

### 1.3 Data Dokumen (Document Data) — SENSITIF TINGGI

| Kategori | Field | Sensitivity | Lokasi |
|----------|-------|-------------|--------|
| **Identitas Legal** | Akta lahir, KK, KIA, Paspor | SANGAT TINGGI | documents |
| **Asuransi** | BPJS, asuransi kesehatan | TINGGI | documents |
| **Pendidikan** | Rapor, sertifikat | SEDANG | documents |
| **Medis** | Surat keterangan dokter | SANGAT TINGGI | documents |

### 1.4 Data Aktivitas (Activity Data)

| Kategori | Field | Sensitivity | Lokasi |
|----------|-------|-------------|--------|
| **Timeline** | title, content, location, tags | SEDANG | timelines |
| **Diary** | title, content, mood | RENDAH | diaries |
| **Album** | name, description | RENDAH | albums |
| **Event** | title, description, location | RENDAH | events |

### 1.5 Data Sistem (System Data)

| Kategori | Field | Sensitivity | Lokasi |
|----------|-------|-------------|--------|
| **Auth** | email, password (hashed) | TINGGI | users |
| **API Keys** | key, secret | SANGAT TINGGI | api_keys |
| **Webhook** | url, secret | TINGGI | webhooks |
| **Audit Log** | event, description, properties | SEDANG | audit_logs |
| **Payment** | amount, bank_account, proof | TINGGI | payments |

---

## 2. Status Keamanan Saat Ini

### 2.1 Sudah Diimplementasi ✅

| No | Fitur | Status | Keterangan |
|----|-------|--------|------------|
| 1 | Password Hashing | ✅ | Laravel default (bcrypt/argon2) |
| 2 | HTTPS Enforcement | ✅ | Configured in .env |
| 3 | CSRF Protection | ✅ | Laravel default |
| 4 | XSS Protection | ✅ | Blade auto-escaping |
| 5 | SQL Injection Prevention | ✅ | Eloquent parameterized queries |
| 6 | Rate Limiting | ✅ | 60/min API, 5/min auth |
| 7 | Child Ownership Check | ✅ | EnsureChildOwnership middleware |
| 8 | Role-Based Access | ✅ | EnsureRole middleware |
| 9 | Subscription Check | ✅ | EnsureActiveSubscription middleware |
| 10 | Feature Limits | ✅ | EnsureFeatureLimit middleware |
| 11 | Tenant Isolation | ✅ | Column-based tenancy (tenant_id) |
| 12 | API Token Auth | ✅ | Laravel Sanctum |
| 13 | Audit Logging | ✅ | AuditService |
| 14 | Backup System | ✅ | Backup command |
| 15 | Session Management | ✅ | Laravel sessions |
| 16 | Input Validation | ✅ | Form Request classes |
| 17 | Soft Deletes | ✅ | For Tenant, Plan, Subscription, Payment |

### 2.2 Belum Diimplementasi ⚠️

| No | Fitur | Prioritas | Keterangan |
|----|-------|-----------|------------|
| 1 | Data Encryption at-Rest | TINGGI | File dokumen dan foto belum di-encrypt |
| 2 | Consent Management | TINGGI | Tidak ada mekanisme consent untuk data anak |
| 3 | Data Retention Policy | TINGGI | Tidak ada auto-deletion policy |
| 4 | Right to Erasure | TINGGI | Tidak ada fitur hapus semua data |
| 5 | Data Export (GDPR-style) | SEDANG | Export PDF/ZIP ada, tapi belum lengkap |
| 6 | 2FA/MFA | SEDANG | Belum ada untuk Super Admin |
| 7 | IP-based Restrictions | RENDAH | Tidak ada IP whitelist |
| 8 | Session Timeout | RENDAH | Menggunakan default Laravel |
| 9 | Data Anonymization | RENDAH | Tidak ada fitur anonymize |
| 10 | Privacy Policy Page | SEDANG | Sudah ada, tapi perlu review |

---

## 3. Rekomendasi Privacy

### 3.1 Phase 19A — Data Protection (Prioritas TINGGI)

#### 3.1.1 Encryption at-Rest

**Masalah:** File dokumen (akta lahir, KK, KIA, BPJS) dan foto anak disimpan tanpa enkripsi tambahan.

**Solusi:**
```php
// Gunakan Laravel's built-in encryption untuk file sensitif
// atau gunakan envelope encryption dengan KMS

// Option 1: Simple encryption via Storage
$ciphertext = Storage::disk('minio')->put(
    'documents/' . $filename,
    encrypt_file_get_contents($file)
);

// Option 2: Use Laravel Filesystem encryption driver
// config/filesystems.php
'disks' => [
    'minio-encrypted' => [
        'driver' => 's3',
        'encryption' => 'aws:kms',
        // ...
    ],
],
```

**Rekomendasi:** Gunakan **envelope encryption** dengan key rotation untuk file dokumen medis dan identitas.

#### 3.1.2 Consent Management

**Masalah:** Tidak ada mekanisme formal untuk mendapatkan consent orang tua sebelum pengumpulan data anak.

**Solusi:** Tambahkan tabel `consents`:

```php
Schema::create('consents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('child_id')->constrained();
    $table->string('consent_type'); // 'data_collection', 'photo_sharing', 'medical_records', 'public_profile'
    $table->boolean('granted');
    $table->text('notes')->nullable();
    $table->timestamp('granted_at')->nullable();
    $table->timestamp('revoked_at')->nullable();
    $table->timestamps();
});
```

**Consent Types:**
- `data_collection` — Persetujuan pengumpulan data umum
- `photo_sharing` — Persetujuan berbagi foto
- `medical_records` — Persetujuan akses catatan medis
- `public_profile` — Persetujuan publikasi profil
- `family_sharing` — Persetujuan berbagi dengan anggota keluarga
- `third_party` — Persetujuan integrasi pihak ketiga

#### 3.1.3 Data Retention Policy

**Masalah:** Tidak ada kebijakan retensi data otomatis.

**Solusi:** Tambahkan konfigurasi retention policy:

```php
// config/privacy.php
return [
    'retention' => [
        'timeline' => 365 * 10, // 10 tahun
        'diary' => 365 * 10,
        'health_records' => 365 * 25, // 25 tahun (sampai anak dewasa)
        'documents' => 365 * 25,
        'photos' => 365 * 20,
        'audit_logs' => 365 * 5, // 5 tahun
        'payments' => 365 * 10,
        'notifications' => 90, // 90 hari
    ],
    'auto_delete' => [
        'inactive_accounts' => 365 * 3, // 3 tahun tidak aktif
        'expired_subscriptions' => 365 * 1, // 1 tahun setelah expired
    ],
];
```

**Scheduled Command:**
```php
// app/Console/Commands/EnforceRetentionPolicy.php
// Run monthly via scheduler
```

#### 3.1.4 Right to Erasure (Right to be Forgotten)

**Masalah:** Tidak ada fitur untuk menghapus semua data pengguna.

**Solusi:** Buat `AccountDeletionService`:

```php
class AccountDeletionService
{
    public function deleteAccount(User $user): void
    {
        DB::transaction(function () use ($user) {
            // 1. Hapus semua media (foto, video, dokumen)
            $this->deleteAllMedia($user);
            
            // 2. Hapus semua data anak
            $this->deleteAllChildren($user);
            
            // 3. Hapus data tenant (soft delete)
            $this->deactivateTenant($user);
            
            // 4. Hapus semua audit logs
            $user->auditLogs()->delete();
            
            // 5. Hapus user (soft delete)
            $user->delete();
            
            // 6. Kirim email konfirmasi
            Mail::to($user->email)->send(new AccountDeletedMail());
        });
    }
}
```

### 3.2 Phase 19B — Security Hardening (Prioritas SEDANG)

#### 3.2.1 Two-Factor Authentication (2FA)

**Target:** Super Admin dan Tenant Admin.

**Solusi:** Gunakan Laravel Fortify atau custom implementation:

```php
// Tambah field ke users table
Schema::table('users', function (Blueprint $table) {
    $table->string('two_factor_secret')->nullable();
    $table->string('two_factor_recovery_codes')->nullable();
    $table->timestamp('two_factor_verified_at')->nullable();
});
```

#### 3.2.2 IP Whitelist untuk Admin

**Solusi:** Middleware `EnsureIpWhitelist` untuk route Super Admin dan Tenant Admin.

#### 3.2.3 Session Management

**Solusi:**
- Session timeout: 30 menit untuk admin, 24 jam untuk parent
- Concurrent session limit: 5 device per user
- Session tracking: log semua device yang login

### 3.3 Phase 19C — Compliance (Prioritas SEDANG)

#### 3.3.1 UU PDP Compliance

| No | Persyaratan | Implementasi | Status |
|----|------------|--------------|--------|
| 1 | Consent sebelum pengumpulan | Consent management | ⚠️ Belum |
| 2 | Tujuan pengumpulan jelas | Privacy policy | ✅ Sudah |
| 3 | Data minimal yang diperlukan | Form validation | ✅ Sudah |
| 4 | Akurasi data | Edit/delete data | ✅ Sudah |
| 5 | Batas penyimpanan | Retention policy | ⚠️ Belum |
| 6 | Hak akses data | Data export | ⚠️ Sebagian |
| 7 | Hak hapus data | Right to erasure | ⚠️ Belum |
| 8 | Keamanan data | Encryption | ⚠️ Sebagian |
| 9 | Pemberitahuan pelanggaran | Breach notification | ⚠️ Belum |

#### 3.3.2 HIPAA Compliance (B2B Healthcare)

| No | Persyaratan | Implementasi | Status |
|----|------------|--------------|--------|
| 1 | Access controls | Role-based + permissions | ⚠️ Sebagian |
| 2 | Audit controls | AuditService | ✅ Sudah |
| 3 | Integrity controls | Checksum validation | ⚠️ Belum |
| 4 | Transmission security | HTTPS | ✅ Sudah |
| 5 | Encryption at-rest | File encryption | ⚠️ Belum |
| 6 | Business Associate Agreement | BAA template | ⚠️ Belum |
| 7 | Minimum necessary access | Granular permissions | ⚠️ Belum |

---

## 4. Rekomendasi Privacy per Fitur

### 4.1 Public Profile

**Status:** Sudah ada fitur `is_public` dan `public_profile_data`.

**Rekomendasi:**
- Tambahkan consent check sebelum mengaktifkan public profile
- Log semua akses ke public profile (visitor tracking)
- Tambahkan opsi expiry untuk public profile
- Sembunyikan data sensitif (dokumen, kesehatan) dari public profile

### 4.2 Family Sharing

**Status:** Anggota keluarga bisa mengakses semua data anak.

**Rekomendasi:**
- Tambahkan permission level (view/edit/admin)
- Batasi akses data kesehatan dan dokumen untuk anggota keluarga
- Log semua akses anggota keluarga
- Tambahkan expiry untuk undangan keluarga

### 4.3 Export & Download

**Status:** Export PDF dan ZIP tersedia.

**Rekomendasi:**
- Tambahkan watermark pada export
- Log semua export activity
- Batasi jumlah export per hari
- Sembunyikan data sensitif dari export (opsional)

### 4.4 API Access

**Status:** REST API dengan Sanctum tokens.

**Rekomendasi:**
- Tambahkan scope-based permissions untuk API tokens
- Log semua API access
- Tambahkan IP whitelist untuk API keys
- Rotasi API keys secara berkala

---

## 5. Rekomendasi Prioritas

| No | Item | Prioritas | Estimasi | Phase |
|----|------|-----------|----------|-------|
| 1 | Encryption at-rest untuk dokumen | TINGGI | 3-4 hari | 19A |
| 2 | Consent management system | TINGGI | 4-5 hari | 19A |
| 3 | Right to erasure | TINGGI | 2-3 hari | 19A |
| 4 | Data retention policy | TINGGI | 2-3 hari | 19A |
| 5 | 2FA untuk admin | SEDANG | 2-3 hari | 19B |
| 6 | IP whitelist admin | SEDANG | 1-2 hari | 19B |
| 7 | Session management | SEDANG | 1-2 hari | 19B |
| 8 | BAA template untuk B2B | SEDANG | 1 hari | 19C |
| 9 | Breach notification system | SEDANG | 2-3 hari | 19C |
| 10 | Data anonymization | RENDAH | 2-3 hari | 19D |
| 11 | Privacy dashboard | RENDAH | 3-4 hari | 19D |

---

## 6. Monitoring & Audit

### 6.1 Privacy Metrics

| Metric | Target | Frequency |
|--------|--------|-----------|
| Consent coverage | 100% untuk data kesehatan | Monthly |
| Encryption coverage | 100% untuk dokumen sensitif | Monthly |
| Data retention compliance | 100% | Monthly |
| Export activity | Track all exports | Real-time |
| API access | Track all API calls | Real-time |
| Failed access attempts | Alert if > 10/hour | Real-time |

### 6.2 Audit Trail

| Event | Data Logged | Retention |
|-------|-------------|-----------|
| Login | user_id, ip, device, timestamp | 1 year |
| Data access | user_id, resource, action, timestamp | 6 months |
| Data export | user_id, format, timestamp | 1 year |
| Consent change | user_id, consent_type, granted, timestamp | 5 years |
| Permission change | user_id, target, old_value, new_value, timestamp | 5 years |
| Data deletion | user_id, resource_type, timestamp | 10 years |

---

## 7. Implementation Checklist

### Phase 19A — Data Protection (11-15 hari)
- [ ] Buat migration untuk `consents` table
- [ ] Buat `ConsentService` dan `ConsentController`
- [ ] Buat consent management UI
- [ ] Implementasi file encryption untuk dokumen
- [ ] Buat `AccountDeletionService`
- [ ] Buat account deletion UI
- [ ] Buat retention policy config
- [ ] Buat `EnforceRetentionPolicy` command
- [ ] Tests untuk semua fitur baru

### Phase 19B — Security Hardening (4-7 hari)
- [ ] Implementasi 2FA untuk admin
- [ ] Buat `EnsureIpWhitelist` middleware
- [ ] Update session management
- [ ] Tests untuk fitur keamanan baru

### Phase 19C — Compliance (3-5 hari)
- [ ] Buat BAA template
- [ ] Buat breach notification system
- [ ] Update privacy policy
- [ ] Review UU PDP compliance

### Phase 19D — Polish (5-7 hari)
- [ ] Data anonymization
- [ ] Privacy dashboard
- [ ] Audit log enhancement
- [ ] Final security audit
