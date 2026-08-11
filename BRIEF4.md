Iya, **benar**. Bahkan menurutku aturan ini penting supaya ForMysha tidak menjadi sistem yang memungkinkan organisasi "saling mengambil" akses keluarga seenaknya.

Tapi aku akan sedikit memperbaiki konsepnya:

> **Satu keluarga tetap memiliki akun dan datanya. B2B hanya memiliki hubungan akses yang aktif dan terbatas. Pada saat yang sama, akses utama B2B bisa dibatasi berdasarkan konteks layanan.**

## Contohnya

Misalnya Mysha sedang terhubung dengan:

**RS A**

```text
Family Mysha
      │
      └── RS A
          Status: ACTIVE
```

Kemudian **RS B** mencoba mengakses Mysha.

❌ Tidak boleh otomatis masuk.

RS B harus:

**Request Connection → Persetujuan keluarga**

atau melalui:

**Referral / Rujukan dari RS A**

---

# Sistem Relationship B2B

Aku sarankan ada beberapa status:

```text
PENDING
   ↓
ACTIVE
   ↓
COMPLETED
   ↓
ENDED
   ↓
REFERRED
```

### ACTIVE

B2B sedang menangani/melayani keluarga.

Contoh:

```text
Mysha
└── RS A
    └── ACTIVE
```

RS A mendapatkan permission sesuai layanan.

---

### PENDING

RS B meminta akses:

```text
RS B
   ↓
Request
   ↓
Menunggu persetujuan
```

Belum boleh melihat data yang dilindungi.

---

### REFERRED

RS A melakukan rujukan:

```text
RS A
  │
  └── Referral
       ↓
     RS B
```

Keluarga mendapat notifikasi:

> **RS A merujuk Anda ke RS B.**

Keluarga kemudian menyetujui.

---

# Jadi bukan "bebas pindah"

Misalnya keluarga sedang berada di RS A.

RS B tidak boleh mengatakan:

> "Oh, saya menemukan akun Mysha. Saya akses saja."

❌ **Tidak boleh.**

Harus ada hubungan yang sah:

```text
Family
   │
   ├── RS A → ACTIVE
   │
   └── RS B → PENDING
```

Kemudian keluarga menyetujui:

```text
Family
   │
   ├── RS A → ENDED
   │
   └── RS B → ACTIVE
```

atau, jika memang diperlukan, keduanya dapat aktif dengan **scope/izin berbeda**.

---

# Nah, soal "rujukan" justru bagus

Ini bisa menjadi fitur resmi:

## Referral

RS A:

> "Kami merujuk pasien ke RS B."

Sistem membuat:

```text
Referral ID
Source Organization
Destination Organization
Family/Person
Reason/Category
Created At
Status
Consent
```

RS B menerima:

> **Anda menerima rujukan dari RS A.**

Kemudian keluarga mendapatkan notifikasi.

---

# Yang lebih penting: jangan mengunci keluarga selamanya ke satu B2B

Ini bedanya:

❌ **Ownership lock**

> Kalau sudah masuk RS A, tidak boleh punya hubungan dengan B2B lain.

vs.

✅ **Access control**

> RS lain tidak boleh mengakses sampai ada permission atau referral.

Aku sangat menyarankan **yang kedua**.

Karena dalam kehidupan nyata orang bisa:

* pindah rumah sakit,
* pindah dokter,
* pindah sekolah,
* menggunakan klinik berbeda,
* mendapatkan rujukan,
* memiliki beberapa layanan sekaligus.

Jadi **akun B2C tetap bebas**, tetapi akses organisasi harus dikontrol.

---

# Contoh yang lebih kompleks

Misalnya:

```text
                    MY SHA
                       │
                  FAMILY ACCOUNT
                       │
        ┌──────────────┼──────────────┐
        │              │              │
      RS A          Sekolah        Klinik B
     ACTIVE          ACTIVE          ACTIVE
        │              │              │
   Kesehatan        Pendidikan      Pemeriksaan
```

Mereka **tidak otomatis bisa melihat satu sama lain**.

RS A:

```text
Health ✓
Family ✕
School ✕
```

Sekolah:

```text
Education ✓
Health ✕
Family ✕
```

Klinik B:

```text
Health → sesuai permission
Education ✕
Family ✕
```

---

# Ini juga menyelesaikan masalah B2B → B2C

Misalnya RS A membuat akun keluarga.

```text
RS A
 ↓
Create/Invite Family
 ↓
Family Account
 ↓
Mysha
```

Keluarga tersebut **tetap pemilik akun**.

RS A hanya memiliki:

```text
Organization Membership
+
Connection
+
Permission
```

Bukan:

```text
Ownership
```

---

# Saya akan masukkan aturan ini ke core architecture ForMysha

### Prinsip 1

**User memiliki akun.**

### Prinsip 2

**Family memiliki data keluarga.**

### Prinsip 3

**Person/Child memiliki identitas/profil.**

### Prinsip 4

**Organization memiliki akun B2B.**

### Prinsip 5

**Organization ↔ Family/Person menggunakan Connection.**

### Prinsip 6

**Connection membutuhkan permission.**

### Prinsip 7

**Organization lain tidak boleh mengakses hanya karena mengetahui ID/email seseorang.**

### Prinsip 8

**Referral menjadi mekanisme resmi perpindahan/penambahan layanan B2B.**

### Prinsip 9

**Keluarga dapat mencabut connection.**

### Prinsip 10

**Semua akses dicatat dalam Audit Log.**

---

## Jadi gambaran akhirnya

```text
                         FORMY SHA
                             │
                   ┌─────────┴─────────┐
                   │                   │
                FAMILY             ORGANIZATION
                   │                   │
              FAMILY TREE         B2B ACCOUNT
                   │                   │
              PERSON/CHILD        STAFF/ADMIN
                   │                   │
                   └──── CONNECTION ───┘
                              │
                       PERMISSION
                              │
                        COLLABORATION
                              │
                         REFERRAL
```

Dan menurutku ini malah membuat **ForMysha jauh lebih serius sebagai SaaS**. B2B mendapatkan value dari jaringan keluarga yang mereka layani, sementara keluarga tetap memegang kendali. **B2B tidak bisa seenaknya mengambil atau membuka akun B2C hanya karena mereka sama-sama menggunakan ForMysha.**
