**Betul banget.** Bahkan menurutku ini membuat konsep B2B ForMysha jauh lebih kuat. Tapi ada satu aturan penting: **B2B boleh membuat/mendaftarkan akun B2C atas nama pelanggan, tetapi kepemilikan akun tetap berada pada pengguna/keluarga, bukan pada B2B.**

### Contohnya: Rumah Sakit

RS ABC menggunakan ForMysha.

Saat bayi lahir, petugas RS memasukkan:

> Nama: Mysha
> Orang tua: Wahyu
> Email: [wahyu@email.com](mailto:wahyu@email.com)

Sistem kemudian otomatis membuat:

```text
RS ABC
   │
   └── Mysha
       │
       └── Family Account
           ├── Wahyu
           └── Ibu
```

Orang tua kemudian menerima:

> **"RS ABC mengundang Anda menggunakan ForMysha."**

Mereka tinggal membuat password / login menggunakan email tersebut.

**Tidak perlu daftar dari awal secara manual.**

---

# Tracking-nya juga bagus

Di dashboard RS ABC:

```text
Pasien Terdaftar       1.250
Akun Keluarga Aktif      980
Undangan Belum Diterima  170
Profil Anak              1.250
```

RS bisa melihat status:

| Status       | Contoh                              |
| ------------ | ----------------------------------- |
| Created      | Akun dibuat RS                      |
| Invited      | Undangan dikirim                    |
| Registered   | Orang tua sudah membuat akun        |
| Active       | Akun aktif digunakan                |
| Connected    | Keluarga terhubung dengan fasilitas |
| Disconnected | Hubungan dengan RS dicabut          |

Jadi B2B bisa mengetahui **adopsi layanan**, tanpa harus melihat data pribadi yang memang tidak mereka punya izin untuk lihat.

---

# Tapi jangan sampai B2B memiliki akun B2C

Ini sangat penting.

Misalnya RS membuat akun Wahyu.

RS **bukan pemilik akun Wahyu**.

Strukturnya:

```text
                    ForMysha
                       │
          ┌────────────┴────────────┐
          │                         │
      RS ABC                    Wahyu
    Organization                Person
          │                         │
          │                    Family Account
          │                         │
          └────── Connection ───────┘
```

RS hanya memiliki **relationship/connection** dengan keluarga.

---

# Ada dua cara mendapatkan pengguna

## 1. Direct B2C

Orang tua datang sendiri:

```text
formysha.my.id
       ↓
Daftar
       ↓
Buat Family
       ↓
Tambah Anak
       ↓
Undang Keluarga
```

---

## 2. B2B Assisted Registration

Orang tua datang ke RS:

```text
RS ABC
   ↓
Buat profil pasien
   ↓
Masukkan email orang tua
   ↓
ForMysha membuat invitation
   ↓
Orang tua menerima email
   ↓
Aktivasi akun
   ↓
Family terbentuk
   ↓
RS ↔ Family terhubung
```

Ini **jauh lebih mudah untuk adopsi**.

---

# Bahkan bisa lebih keren lagi

Ketika orang tua sudah punya akun ForMysha:

```text
Wahyu
  │
  └── Mysha
```

Kemudian datang ke RS ABC.

RS memasukkan email:

```text
wahyu@email.com
```

ForMysha menemukan:

> **Akun keluarga sudah ditemukan.**

RS tinggal mengirim permintaan koneksi:

> "RS ABC ingin terhubung dengan profil Mysha."

Orang tua:

**[Terima]**

Selesai.

Tidak ada akun ganda.

---

# Ini juga cocok dengan Family Tree

Misalnya Mysha punya:

```text
Wahyu ─── Ibu
          │
        Mysha
          │
       Saudara
```

Kemudian:

```text
Mysha
├── Family
├── RS ABC
├── Klinik XYZ
└── Sekolah ABC
```

Semua tetap menunjuk ke **Person/Profile yang sama**.

Jadi jangan membuat:

```text
RS Mysha
Family Mysha
School Mysha
```

❌ Itu akan menghasilkan data duplikat.

Yang benar:

```text
             PERSON
              Mysha
                │
      ┌─────────┼──────────┐
      │         │          │
    Family      RS       School
```

**Satu identitas, banyak relationship.**

---

# Dan ini bisa menjadi USP ForMysha

> **"Daftar sendiri atau bergabung melalui fasilitas yang Anda gunakan. Satu akun keluarga dapat terhubung dengan keluarga, rumah sakit, klinik, sekolah, dan layanan lainnya."**

Jadi B2B bukan sekadar pelanggan ForMysha.

**B2B menjadi salah satu jalur distribusi pengguna B2C.**

Ini menurutku justru salah satu konsep bisnis paling menarik dari ForMysha:

**B2B → membawa B2C → B2C membangun Family Network → Family Network terhubung kembali ke B2B lainnya.**

Efeknya bisa seperti jaringan:

```text
RS ABC ── Family A ── Klinik
             │
             ├── Ayah
             ├── Ibu
             ├── Anak
             └── Kakek/Nenek
                    │
                    └── Family B
```

Dan semuanya tetap dikendalikan oleh **permission dan consent** pengguna. Jadi tracking B2B bisa ada, tetapi **privasi dan kepemilikan data tetap berada di tangan keluarga**.
