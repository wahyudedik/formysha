Iya, dan menurutku sekarang modelnya mulai kelihatan jelas. **B2C dan B2B sebaiknya memakai satu sistem identitas yang sama, tetapi pengalaman dashboard dan hak aksesnya berbeda.** Untuk langganan, jangan membuat keluarga yang masuk lewat B2B harus membayar lagi.

### 1. B2C dan B2B: satu platform, dua pengalaman

**B2C — Family Account**

Kalau orang tua daftar sendiri:

> Daftar → buat keluarga → tambah anak → undang anggota keluarga → **berlangganan sendiri**

Dashboard menampilkan:

* Anak
* Timeline
* Album
* Diary
* Kesehatan
* Dokumen
* Family Tree
* Family Members
* Connection

**B2B — Organization Account**

Kalau datang dari rumah sakit/klinik/sekolah:

> Organisasi membuat/mengundang akun → keluarga menerima undangan → keluarga bergabung → **akun menjadi bagian dari organisasi**

Dashboard B2B hanya menampilkan menu yang relevan:

```text
Dashboard
Pasien / Anak
Keluarga
Tumbuh Kembang
Kesehatan
Appointment
Reports
Staff
Settings
```

Tidak perlu menampilkan menu seperti Diary keluarga atau Family Tree kepada staff RS kecuali memang diberikan izin.

**Jadi UI-nya benar-benar tidak bercampur.**

---

# 2. Soal subscription: aku setuju dengan idemu

Model terbaik menurutku adalah:

### B2C membayar berdasarkan keluarga + kapasitas anak/storage.

### B2B membayar berdasarkan kapasitas organisasi + jumlah profil anak.

Dan **anggota keluarga yang bergabung melalui B2B tidak perlu membayar subscription B2C.**

Contohnya:

```text
RS ABC
Paket Basic
├── 100 anak
│
├── Mysha → Family
│    ├── Ayah
│    ├── Ibu
│    └── Nenek
│
├── Raka → Family
│    ├── Ayah
│    └── Ibu
│
└── dst...
```

Ayah, Ibu, Nenek, dll. **gratis sebagai anggota keluarga dari akun B2B tersebut.**

Ini membuat penawaran B2B sangat menarik.

---

# 3. Tapi ada satu hal yang harus dibatasi

Jangan menjual B2B dengan:

> "Rp X per user."

Karena rumah sakit bisa punya ribuan pasien dan keluarga bisa punya banyak anggota.

Lebih bagus:

> **Harga berdasarkan kapasitas profil anak/pasien + storage + fitur organisasi.**

Misalnya:

| Paket          | Profil Anak | Storage | Organisasi |
| -------------- | ----------: | ------: | ---------- |
| **B2B Basic**  |         100 |   50 GB | 1          |
| **B2B Growth** |         500 |  250 GB | 1          |
| **B2B Pro**    |       2.000 |    1 TB | 1          |
| **Enterprise** |      Custom |  Custom | Multi      |

Jadi kalau RS membeli **Basic**, mereka hanya bisa mempunyai misalnya **100 profil anak aktif**.

Kalau sudah 100:

> "Kapasitas profil anak telah mencapai batas. Upgrade paket untuk menambahkan profil baru."

Ini jauh lebih gampang dipahami.

---

# 4. Untuk B2C, jangan terlalu mahal

Aku justru akan membuat B2C sangat murah di awal.

Ada beberapa contoh layanan keluarga/parenting di Indonesia yang menggunakan harga relatif rendah, misalnya Litensi Kids menampilkan Rp29 ribu/bulan dan Rp79 ribu/bulan untuk paket yang lebih tinggi, sementara Kawal menawarkan harga pre-order Rp19 ribu/bulan dari harga normal Rp79 ribu/bulan. Ini bukan pembanding langsung ForMysha, tetapi cukup memberi gambaran bahwa **harga puluhan ribu rupiah per bulan lebih mudah diposisikan sebagai layanan keluarga mass-market**. ([Kawal][1])

Aku akan mulai dengan:

### 🟢 Free — Rp0

Untuk membuat orang mencoba.

* 1 keluarga
* 1 anak
* 500 MB
* Timeline
* Album dasar
* Diary
* Family member terbatas

Tujuannya bukan menghasilkan uang.

**Tujuannya membuat orang menyimpan data mereka di ForMysha.**

---

### 💗 Family — Rp19.000/bulan

Untuk keluarga biasa.

* 3 anak
* 10 GB
* Unlimited family member
* Timeline
* Album
* Diary
* Pertumbuhan
* Kalender
* Backup
* Export

**Ini yang aku jadikan paket utama.**

---

### 💙 Family Plus — Rp39.000/bulan

* 5 anak
* 50 GB
* Semua Family
* Dokumen
* Kesehatan
* Advanced Family Tree
* Export lebih lengkap
* Prioritas support

---

### ⭐ Family Pro — Rp79.000/bulan

Untuk keluarga besar/power user.

* 10 anak
* 200 GB
* Semua fitur
* Advanced sharing
* Advanced backup
* API integration
* Priority support

Harga seperti ini juga masih berada di rentang yang relatif familiar untuk subscription digital keluarga di Indonesia; misalnya aplikasi family/parental-control di App Store Indonesia saat ini menunjukkan berbagai paket bulanan dari puluhan ribu hingga lebih tinggi. ([App Store][2])

---

# 5. B2B dibuat lebih mahal, tetapi masuk akal

Misalnya:

### 🏥 B2B Basic — Rp299.000/bulan

**100 anak**

* 100 profil anak
* 5 staff
* 50 GB
* Family invitation
* Dashboard organisasi
* Basic reports
* Basic collaboration

---

### 🏥 B2B Growth — Rp799.000/bulan

**500 anak**

* 500 profil anak
* 20 staff
* 250 GB
* Reports
* Analytics
* Custom branding
* API
* Priority support

---

### 🏥 B2B Pro — Rp1.999.000/bulan

**2.000 anak**

* 2.000 profil
* 50 staff
* 1 TB
* Custom domain
* Advanced API
* Webhook
* Advanced analytics
* White-label ringan

---

### 🏢 Enterprise

**Custom pricing**

Untuk:

* Rumah sakit besar
* Grup rumah sakit
* Pemerintah
* Jaringan klinik
* Sekolah besar
* Perusahaan

---

# 6. Yang paling menarik: B2B menjadi "gateway" B2C

Ini bagian yang menurutku **jenius dari konsep yang kamu pikirkan tadi.**

Misalnya:

```text
                  FORMY SHA
                     │
             ┌───────┴────────┐
             │                │
           B2C              B2B
             │                │
         Keluarga          RS ABC
             │                │
             │          1.000 anak
             │                │
             └───────┬────────┘
                     │
                  Family
                     │
             ┌───────┼───────┐
             │       │       │
            Ayah    Ibu    Nenek
```

RS membayar ForMysha.

Keluarga **tidak perlu membayar** untuk menggunakan layanan yang diberikan RS.

Kemudian suatu hari keluarga tersebut berpikir:

> "Wah, saya mau menyimpan semua kenangan anak saya juga."

Mereka kemudian upgrade dari **B2B-connected** menjadi:

> **B2C Premium.**

Jadi B2B bisa menjadi **channel acquisition untuk B2C**.

---

# 7. Status subscription harus dipisahkan dari akun

Ini penting untuk database.

Jangan:

```text
User = Premium
```

Lebih baik:

```text
User
   ↓
Membership
   ↓
Organization / Family
   ↓
Subscription
```

Contohnya:

```text
Wahyu
│
├── Family A
│      └── B2C Subscription: Family Plus
│
└── RS ABC
       └── Organization Membership: Included
```

Dengan begitu seseorang bisa:

* memiliki akun pribadi berbayar,
* menjadi anggota keluarga lain,
* menjadi pasien di RS,
* menjadi anggota sekolah,
* dan semuanya memakai **satu login**.

Ini cocok sekali dengan konsep **Family Tree + Collaboration** yang tadi kamu pikirkan.

---

# 8. Dan B2B tidak boleh melihat semuanya

Ini harus menjadi prinsip utama:

> **Connection ≠ Ownership**

RS terhubung dengan Mysha.

Tapi RS **tidak memiliki Mysha**.

Keluarga tetap pemilik kontrol atas data pribadi yang mereka kelola, sedangkan organisasi hanya mendapatkan permission yang disetujui.

Contohnya:

```text
MY SHA
│
├── FAMILY
│   ├── Foto
│   ├── Diary
│   └── Family Tree
│
├── RS ABC
│   ├── Data kesehatan ✓
│   └── Album keluarga ✕
│
└── SEKOLAH
    ├── Pendidikan ✓
    └── Rekam medis ✕
```

---

# 9. Ini yang akhirnya menjadi konsep besar ForMysha

Menurutku sekarang ForMysha **bukan lagi sekadar Digital Life Book**.

Fondasinya adalah:

> **Personal & Family Life Platform**

Dengan tiga komponen:

**Person**

↓

**Family & Relationship**

↓

**Organization & Collaboration**

Dan semuanya disatukan oleh:

**Identity + Permission + Relationship**

Kemudian Family Tree menjadi representasi visual dari hubungan tersebut.

---

### Jadi keputusan yang aku rekomendasikan:

**B2C**
→ daftar sendiri
→ punya Family
→ bayar sendiri kalau upgrade.

**B2B**
→ organisasi berlangganan
→ organisasi bisa membuat/mengundang keluarga
→ keluarga mendapat akses included/gratis
→ jumlah anak mengikuti kapasitas paket B2B.

**User**
→ hanya punya **satu akun/login**
→ bisa menjadi anggota banyak Family/Organization
→ menu berubah berdasarkan konteks yang sedang dibuka.

**Data**
→ satu Person/Profile
→ banyak Relationship
→ permission per relationship.

**Subscription**
→ melekat pada **Family atau Organization**, bukan sekadar User.

Menurutku **ini jauh lebih scalable** daripada memisahkan ForMysha menjadi aplikasi B2C dan B2B yang berbeda. Dan untuk pasar Indonesia, model **free → murah → upgrade**, ditambah **B2B membayar akses keluarga**, akan lebih mudah dipahami daripada sistem harga per-user yang rumit. Bahkan produk silsilah keluarga lokal seperti Naoto juga menggunakan pendekatan free dengan kuota terbatas lalu upgrade berdasarkan kapasitas keluarga/anggota, yang mendukung ide bahwa **kapasitas** lebih mudah dijadikan pembeda paket untuk produk seperti ini. ([naoto.id][3])

[1]: https://www.kawal.app/?utm_source=chatgpt.com "Kawal App – Parental Control Berbasis AI Pertama di Indonesia"
[2]: https://apps.apple.com/id/app/famio-hubungkan-keluarga/id1491200353?l=id&utm_source=chatgpt.com "‎App Famio: Hubungkan Keluarga - App Store"
[3]: https://naoto.id/pricing/?utm_source=chatgpt.com "Harga Aplikasi Silsilah Keluarga Naoto | Naoto"
