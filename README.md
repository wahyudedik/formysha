<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# ForMysha

## Digital Life Book & Family Life Platform SaaS

**Versi:** 1.1

---

## Tentang ForMysha

ForMysha adalah platform **Digital Life Book & Family Life Platform** berbasis web yang membantu orang tua menyimpan dan mengelola perjalanan hidup anak sejak lahir hingga dewasa dalam satu tempat yang aman, terstruktur, dan mudah diakses.

Platform ini menggabungkan dokumentasi kenangan, kesehatan, pendidikan, dokumen penting, serta aktivitas keluarga dalam satu aplikasi — dengan sistem **Connection** yang menghubungkan keluarga dengan fasilitas kesehatan, sekolah, dan organisasi lainnya.

ForMysha dibangun sebagai **Software as a Service (SaaS)** yang melayani dua segmen utama:

* **B2C (Family)** — Keluarga yang ingin mendokumentasikan perjalanan hidup anak.
* **B2B (Organization)** — Fasilitas kesehatan, sekolah, dan organisasi yang mengelola data pasien/siswa.

---

## Visi

Menjadi platform digital terpercaya untuk mendokumentasikan perjalanan hidup anak dari lahir hingga dewasa, sekaligus menjadi **jembatan digital** antara keluarga dan organisasi yang merawat mereka.

---

## Misi

* Menyediakan aplikasi yang sederhana dan mudah digunakan.
* Menjaga keamanan dan privasi data keluarga.
* Mengembangkan platform SaaS yang stabil dan berkelanjutan.
* Memberikan fleksibilitas melalui integrasi API dengan layanan pihak ketiga.
* Fokus pada kualitas aplikasi, maintenance, dan pembaruan fitur.

---

## Target Pengguna

### Individu

* Orang tua.
* Wali.
* Keluarga.

### Organisasi

* Klinik.
* Rumah sakit.
* Bidan.
* Posyandu.
* Daycare.
* PAUD.
* TK.
* Sekolah.
* Perusahaan.

---

## Value Proposition

ForMysha bukan sekadar album foto.

ForMysha adalah **Digital Life Book & Family Life Platform** yang menyimpan seluruh perjalanan hidup anak dalam satu aplikasi, mulai dari kenangan, pertumbuhan, kesehatan, pendidikan, hingga dokumen penting — dengan **sistem Connection** yang menghubungkan keluarga dengan organisasi secara aman dan terkontrol.

---

## Core Architecture

Arsitektur inti ForMysha dibangun dengan fondasi berikut:

```
Identity → Family → Person → Relationship → Organization → Connection → Permission → Collaboration → Audit Trail → Family Tree
```

### Konsep Dasar

* **Identity (User Account)** — Satu akun pengguna (orang tua/wali) bisa terhubung ke banyak keluarga dan organisasi.
* **Family** — Unit keluarga yang memiliki satu atau lebih anak.
* **Person / Child Profile** — Anak adalah **profil/entitas**, bukan akun terpisah.
* **Relationship** — Hubungan keluarga (ayah, ibu, kakek, nenek, wali).
* **Organization** — Fasilitas kesehatan, sekolah, atau organisasi lainnya (B2B).
* **Connection** — Hubungan antara keluarga dan organisasi dengan permission-based access.
* **Permission** — Level akses yang dikontrol oleh pemilik data (keluarga).
* **Audit Trail** — Catatan lengkap WHO, WHAT, WHEN, WHERE, WHY, dan PERMISSION.
* **Family Tree** — Visualisasi hubungan keluarga dan organisasi yang terhubung.

### Prinsip: 1 Anak ≠ 1 Akun

Anak bukan pengguna aplikasi. Anak adalah **profil/entitas** yang dimiliki oleh akun orang tua/wali.

```
User Account (Orang Tua) → Family → Child Profile → Connection → Organization
```

Satu orang tua bisa memiliki banyak anak. Satu anak bisa terhubung ke banyak organisasi. Tetapi satu akun = satu identitas digital.

---

## 10 Prinsip Inti ForMysha

### Prinsip 1 — Satu Akun, Banyak Keluarga & Organisasi

Satu akun bisa terhubung ke banyak keluarga dan organisasi. Login sekali, akses semua.

### Prinsip 2 — Anak Bukan Akun

Anak adalah profil/entitas, bukan akun login terpisah. Orang tua mengelola semua data anak dari akun mereka.

### Prinsip 3 — Keluarga Adalah Pemilik Data

Keluarga memiliki dan mengontrol semua data anak. Organisasi hanya memiliki akses berdasarkan permission yang diberikan.

### Prinsip 4 — Organisasi adalah Partner, Bukan Pemilik

Organisasi (B2B) terhubung melalui Connection, bukan memiliki data. Akses diatur oleh permission level.

### Prinsip 5 — Connection ≠ Ownership

Terhubung (connected) tidak berarti memiliki. Organisasi bisa melihat data yang diizinkan, tetapi tidak bisa mengubah atau menghapus data milik keluarga.

### Prinsip 6 — Permission-Based Access

Setiap Connection memiliki permission level: View, Comment, Edit, atau Manage. Keluarga mengontrol siapa bisa melakukan apa.

### Prinsip 7 — Audit Trail Wajib

Setiap aksi tercatat: WHO (siapa), WHAT (apa yang dilakukan), WHEN (kapan), WHERE (dari mana), WHY (alasan), dan PERMISSION (berdasarkan hak akses apa).

### Prinsip 8 — B2B adalah Gateway ke B2C

Organisasi (rumah sakit, sekolah) bisa membantu registrasi keluarga. Ini menjadi jalur akuisisi B2C yang powerful.

### Prinsip 9 — Subscription Tied to Family/Organization

Subscription terikat pada Family atau Organization, bukan pada user individual. Satu user bisa memiliki akses ke multiple subscriptions melalui connection.

### Prinsip 10 — Family Tree sebagai Core Feature

Family Tree bukan fitur tambahan, tetapi fondasi yang menghubungkan semua data: siapa, hubungan dengan siapa, dan terhubung ke organisasi mana.

---

## Mengapa Bernama ForMysha?

**ForMysha** bukan berarti aplikasi yang hanya dibuat untuk seseorang bernama **Mysha**.

Nama ini memiliki filosofi yang sederhana namun bermakna.

* **For** berarti **"untuk"**.
* **My** berarti **"milikku"** atau **"kesayanganku"**.
* **Sha** melambangkan **buah hati**, sebagai nama simbolis yang mewakili setiap anak.
* **.my.id** adalah domain Indonesia yang sekaligus memperkuat makna "My Identity" dan "My Indonesia", sehingga terasa personal namun tetap relevan untuk semua keluarga.

Secara sederhana, **ForMysha** dapat dimaknai sebagai:

> **"Untuk buah hati tercinta."**

Bukan hanya untuk satu anak, tetapi untuk **setiap anak yang menjadi kebanggaan keluarganya**.

---

## Filosofi Brand

Setiap anak memiliki cerita.

Setiap momen layak dikenang.

Setiap keluarga berhak memiliki tempat yang aman untuk menyimpan kenangan tersebut.

ForMysha hadir sebagai rumah digital untuk menyimpan perjalanan hidup anak, mulai dari hari pertama lahir hingga mereka tumbuh dewasa.

---

## Makna Domain

Domain:

**formysha.my.id**

memiliki tiga lapisan makna.

### For

Untuk.

Platform ini dibuat **untuk keluarga**.

### My

Milikku.

Melambangkan hubungan emosional antara orang tua dan anak.

Bukan sekadar kepemilikan, tetapi rasa sayang, perhatian, dan kenangan yang ingin dijaga.

### Sha

Simbol anak.

"Sha" dipilih sebagai representasi universal, sehingga setiap pengguna dapat membayangkan nama anak mereka sendiri.

Misalnya:

* For My Aisyah
* For My Adit
* For My Raka
* For My Nara
* For My Bintang

Intinya adalah **"For My Child"**, bukan terbatas pada nama tertentu.

### .my.id

Domain **.my.id** dipilih karena:

* Identitas Indonesia.
* Mudah diingat.
* Memberikan kesan personal.
* Mendukung konsep bahwa setiap anak memiliki identitas digitalnya sendiri.

---

## Brand Story

ForMysha berawal dari sebuah ide sederhana.

Bagaimana jika setiap anak memiliki tempat yang aman untuk menyimpan seluruh perjalanan hidupnya?

Bukan hanya foto.

Bukan hanya video.

Tetapi juga cerita, pertumbuhan, kesehatan, pendidikan, prestasi, dan kenangan yang dapat dikenang sepanjang hidup.

Dari pertanyaan itulah lahir ForMysha.

Sebuah Digital Life Book yang dapat digunakan oleh siapa pun, untuk anak siapa pun.

---

## Pesan Brand

> **ForMysha bukan tentang satu nama. Ini tentang setiap anak yang menjadi dunia bagi orang tuanya.**

Atau versi yang lebih singkat:

> **ForMysha berarti "Untuk Buah Hatiku". Sebuah ruang digital untuk menyimpan setiap momen, kenangan, dan perjalanan hidup anak sejak lahir hingga dewasa.**

---

## Personal Digital Identity

Setiap anak yang didaftarkan akan memiliki **slug unik** yang menjadi identitas digitalnya.

Contoh:

```
formysha.my.id/mysha
```

atau

```
formysha.my.id/qaireen
```

atau

```
formysha.my.id/alexa
```

Slug tersebut menjadi halaman utama perjalanan hidup anak dan hanya dapat diakses sesuai pengaturan privasi yang dipilih keluarga.

---

## Custom Public URL (Opsional)

Pengguna dapat mengaktifkan halaman publik dengan URL seperti:

```
formysha.my.id/mysha
```

Halaman ini dapat menampilkan informasi yang dipilih pengguna, misalnya:

* Foto profil.
* Nama panggilan.
* Timeline pilihan.
* Ucapan ulang tahun.
* Prestasi.
* Galeri pilihan.

Data sensitif seperti dokumen, kesehatan, atau informasi pribadi tetap bersifat privat.

---

## Konsep "My"

Kata **My** menjadi inti identitas platform.

Contohnya:

* My Story
* My Timeline
* My Memories
* My Growth
* My Health
* My Documents
* My School
* My Awards
* My Family
* My Gallery
* My Diary

Semua modul menggunakan awalan **My** agar konsisten dengan nama **ForMysha**.

---

## URL Structure

```
formysha.my.id/
```

Landing Page

```
formysha.my.id/login
```

Login

```
formysha.my.id/register
```

Register

```
formysha.my.id/dashboard
```

Dashboard

```
formysha.my.id/mysha
```

Profil anak

```
formysha.my.id/mysha/timeline
```

Timeline

```
formysha.my.id/mysha/gallery
```

Album

```
formysha.my.id/mysha/health
```

Kesehatan

```
formysha.my.id/mysha/documents
```

Dokumen

```
formysha.my.id/mysha/family
```

Family Sharing

### Future Roadmap

Apabila nanti memiliki domain internasional (misalnya `formysha.com`), struktur URL dan slug tetap sama sehingga migrasi tidak mengubah pengalaman pengguna.

Contoh:

```
formysha.com/mysha
```

atau

```
formysha.my.id/mysha
```

Keduanya dapat menggunakan sistem yang sama.

---

## Business Model

### SaaS Subscription

Pelanggan menyewa aplikasi melalui sistem langganan. Subscription terikat pada **Family** atau **Organization**, bukan pada user individual. Satu user bisa memiliki akses ke multiple subscriptions melalui Connection.

ForMysha menyediakan:

* Hosting.
* Maintenance.
* Update fitur.
* Backup.
* Monitoring.
* Dukungan teknis.

### Pricing B2C (Family)

| Paket | Harga | Fitur Utama |
|-------|-------|-------------|
| 🟢 **Free** | Rp0 | 1 anak, 10 foto, 500MB storage, timeline, gallery, dokumen |
| 💗 **Family** | Rp19.000/bulan | 3 anak, 200 foto, 5GB storage, + growth tracking, health records |
| 💙 **Family Plus** | Rp39.000/bulan | 5 anak, 500 foto, 15GB storage, + calendar, export PDF/ZIP, priority support |
| ⭐ **Family Pro** | Rp79.000/bulan | 10 anak, unlimited foto, 50GB storage, + custom API, white label, family tree |

### Pricing B2B (Organization)

| Paket | Harga | Fitur Utama |
|-------|-------|-------------|
| 🏥 **B2B Basic** | Rp299.000/bulan | 50 profil anak, 5 staf, clinical notes, basic reporting |
| 🏥 **B2B Growth** | Rp799.000/bulan | 200 profil anak, 20 staf, referrals, advanced analytics, custom branding |
| 🏥 **B2B Pro** | Rp1.999.000/bulan | 1000 profil anak, unlimited staf, API integration, priority support, white label |
| 🏢 **Enterprise** | Custom | Custom fitur, dedicated support, SLA, custom deployment |

### B2B sebagai Gateway B2C

Organisasi B2B (rumah sakit, sekolah) bisa membantu registrasi keluarga baru. Ketika rumah sakit mendaftarkan profil pasien, keluarga diundang untuk membuat akun B2C. Ini menjadi jalur akuisisi yang powerful — B2B membawa pengguna baru ke B2C.

---

## Integrasi

ForMysha **tidak menyediakan layanan AI maupun layanan pihak ketiga sebagai fitur bawaan.**

Sebagai gantinya, platform menyediakan **Custom API Integration** sehingga pelanggan dapat menghubungkan:

* AI pilihan mereka.
* WhatsApp API.
* Email SMTP.
* Cloud Storage.
* ERP.
* CRM.
* Sistem internal.
* REST API pihak ketiga.

Pendekatan ini menjaga biaya operasional tetap rendah dan memberi kebebasan kepada pelanggan memilih vendor sesuai kebutuhan.

---

## Modul Utama

### Authentication

* Login
* Register
* Reset Password
* Email Verification

### Dashboard

* Ringkasan aktivitas
* Timeline terbaru
* Reminder
* Statistik sederhana

### Profil Anak

* Biodata
* Orang tua
* Wali
* Foto

### Timeline Kehidupan

* Cerita
* Foto
* Video
* Audio
* Lokasi
* Tag

### Album

* Foto
* Video
* Folder
* Download
* Share privat

### Diary

* Catatan harian
* Cerita perkembangan
* Lampiran media

### Pertumbuhan

* Tinggi badan
* Berat badan
* Grafik
* Riwayat

### Kesehatan

* Imunisasi
* Riwayat penyakit
* Obat
* Dokter
* Alergi

### Dokumen

* Akta lahir
* KK
* KIA
* BPJS
* Paspor
* Sertifikat
* Rapor

### Kalender

* Jadwal imunisasi
* Ulang tahun
* Agenda
* Reminder

### Family Sharing

* Ayah
* Ibu
* Wali
* Kakek
* Nenek

### Backup

* Backup otomatis
* Restore
* Export PDF
* Export ZIP

### Notification

* Email
* Reminder sistem

### Search

* Timeline
* Foto
* Video
* Dokumen

### Custom API Integration

* REST API
* API Key
* OAuth
* Webhook
* Request Log
* Response Log

### Family Tree

* Visualisasi hubungan keluarga
* Relationship mapping (ayah, ibu, kakek, nenek, wali)
* Connection ke organisasi (rumah sakit, sekolah)
* Permission-based access control
* Timeline gabungan dari semua hubungan

### Connection System

* Hubungan antara keluarga dan organisasi
* Permission levels: View, Comment, Edit, Manage
* Connection status: Active, Pending, Referred
* Activity & Access History di kedua sisi
* Family bisa mengontrol akses organisasi

### Referral System

* B2B mereferensikan keluarga ke B2C
* Tracking referral antar organisasi
* Reward/milestone untuk referral aktif
* Status: Active, Pending, Referred

### Audit Trail

* WHO — Siapa yang melakukan aksi
* WHAT — Apa yang dilakukan
* WHEN — Kapan terjadi
* WHERE — Dari mana (device, IP)
* WHY — Alasan (jika ada)
* PERMISSION — Berdasarkan hak akses apa

### Activity & Access History

* Di sisi B2C: log semua akses ke profil anak
* Di sisi B2B: log semua aktivitas staf pada pasien
* Timeline kronologis semua interaksi
* Filter berdasarkan user, jenis aksi, tanggal

---

## Modul Lanjutan

### B2B Healthcare

* Patient management
* Clinical notes
* Referrals
* Staff management
* Facility settings
* Patient links (keluarga ↔ organisasi)
* Reporting & analytics

### B2B Assisted Registration

* Organisasi membuat profil pasien
* Keluarga diundang via email/WhatsApp
* Keluarga membuat akun dan mengklaim profil
* Data dari organisasi otomatis terhubung
* Permission diatur oleh keluarga

---

## SaaS Management

### Super Admin

* Tenant Management
* Subscription
* Billing
* Monitoring
* Backup
* Broadcast
* Audit Log

### Tenant Admin

* Pengguna
* Branding
* Storage
* Domain
* API Integration
* Activity Log

---

## Design Brief

### Design Philosophy

ForMysha dirancang sebagai aplikasi yang memberikan rasa hangat, aman, dan menyenangkan bagi orang tua dalam mendokumentasikan perjalanan hidup anak.

Desain harus mengutamakan **kesederhanaan**, **kemudahan penggunaan**, dan **emosi positif**, sehingga setiap interaksi terasa ringan dan menyenangkan.

Inspirasi desain berasal dari dunia bayi dan keluarga modern, dengan pendekatan minimalis yang tetap profesional.

### Design Style

Konsep desain:

* Modern
* Cute
* Clean
* Friendly
* Soft
* Minimalist
* Family Oriented
* Emotional
* Premium
* Playful (secukupnya)

Targetnya adalah tampilan yang tetap elegan untuk orang tua, tetapi memiliki sentuhan visual yang ramah untuk dunia anak.

### Design Keywords

* Baby
* Family
* Memory
* Growth
* Love
* Happiness
* Smile
* Story
* Journey
* Safe

### UI Character

Antarmuka harus memberikan kesan:

* Ramah.
* Menenangkan.
* Ceria.
* Tidak membingungkan.
* Mudah dipahami oleh semua usia.

Pengguna harus merasa nyaman menggunakan aplikasi setiap hari.

### Visual Direction

Gunakan elemen visual yang lembut seperti:

* Sudut membulat (rounded corners).
* Ikon bergaya outline atau rounded.
* Ilustrasi sederhana.
* Empty state yang lucu.
* Maskot atau karakter kecil sebagai pendamping (opsional).
* Animasi halus untuk transisi dan interaksi.

Hindari tampilan yang kaku, penuh garis tajam, atau terlalu teknis.

### Color Palette

Gunakan warna pastel modern dengan kontras yang tetap baik agar mudah dibaca.

Contoh palet:

* Sky Blue
* Mint Green
* Soft Pink
* Lavender
* Warm Yellow
* Peach
* Soft Orange
* White
* Light Gray

Warna utama sebaiknya menciptakan rasa tenang dan aman, bukan terlalu mencolok.

### Typography

Gunakan font modern dengan bentuk huruf yang membulat dan mudah dibaca.

Karakter font:

* Friendly
* Soft
* Clean
* Modern
* Tidak terlalu formal

Ukuran teks harus nyaman untuk dibaca di berbagai perangkat.

### Icon Style

Ikon harus:

* Rounded.
* Minimalis.
* Konsisten.
* Mudah dikenali.

Hindari ikon yang terlalu rumit atau bergaya korporat.

### Illustration Style

Gunakan ilustrasi bergaya:

* Flat Illustration.
* Soft Gradient (opsional).
* Cute Character.
* Family Friendly.
* Gender Neutral.
* Modern.

Ilustrasi berfungsi sebagai pendukung, bukan elemen utama yang mengganggu fokus.

### Components

Semua komponen UI harus memiliki bentuk yang konsisten.

* Rounded Card.
* Rounded Button.
* Rounded Input.
* Rounded Avatar.
* Soft Shadow.
* Clean Layout.
* White Space yang cukup.

### Dashboard Style

Dashboard harus terasa seperti melihat album kenangan, bukan laporan bisnis.

Elemen utama:

* Foto terbaru anak.
* Timeline singkat.
* Pengingat penting.
* Ringkasan pertumbuhan.
* Akses cepat ke fitur utama.

### User Experience

Prinsip UX:

* Maksimal tiga klik untuk mencapai fitur penting.
* Navigasi sederhana dan konsisten.
* Bahasa yang mudah dipahami.
* Proses tambah foto, video, atau catatan dibuat secepat mungkin.
* Fokus pada kemudahan penggunaan bagi orang tua yang sibuk.

### Responsive Design

Website harus responsif dan nyaman digunakan pada:

* Desktop.
* Laptop.
* Tablet.
* Smartphone.

Prioritaskan desain **mobile-friendly**, meskipun produk pertama adalah aplikasi web.

### Accessibility

Perhatikan:

* Kontras warna yang baik.
* Ukuran tombol yang mudah disentuh.
* Ukuran teks yang nyaman dibaca.
* Fokus keyboard yang jelas.
* Alt text untuk gambar penting.

### Brand Personality

ForMysha harus dikenal sebagai:

* Hangat.
* Ceria.
* Aman.
* Ramah.
* Modern.
* Terpercaya.
* Profesional.
* Penuh kasih sayang.

### Emotional Experience

Setiap kali orang tua membuka ForMysha, mereka harus merasakan:

> "Ini adalah tempat terbaik untuk menyimpan setiap kenangan berharga anakku."

Aplikasi bukan sekadar alat penyimpanan data, tetapi sebuah **ruang digital penuh cerita dan kasih sayang**, yang membuat orang tua ingin kembali menggunakannya setiap kali ada momen baru.

### Referensi Nuansa Desain

Sebagai acuan visual (bukan untuk ditiru), nuansa yang ingin dicapai adalah kombinasi:

* **Apple** → bersih, sederhana, premium.
* **Airbnb** → hangat dan ramah.
* **Duolingo** → karakter yang menyenangkan tanpa berlebihan.
* **Headspace** → ilustrasi lembut dan menenangkan.

Dengan kombinasi tersebut, ForMysha akan terasa seperti aplikasi keluarga modern: lucu, ringan, profesional, dan tetap dipercaya untuk menyimpan kenangan yang paling berharga.

### Brand Assets

#### Logo

![ForMysha Logo](public/logo.png)

Logo ForMysha menampilkan dua figur (orang tua dan anak) dalam bentuk hati dengan warna pastel — pink untuk orang tua dan biru untuk anak — melambangkan kasih sayang dan kebersamaan keluarga. Hati kuning kecil menambahkan sentuhan keceriaan.

#### Favicon

![ForMysha Favicon](public/favicon.png)

Favicon menggunakan desain yang sama dengan logo dalam ukuran yang lebih kecil, memastikan konsistensi visual di semua perangkat dan browser.

#### Lokasi Aset

```
public/logo.png      → Logo utama
public/favicon.png   → Favicon browser
public/favicon.ico   → Favicon legacy
```

---

## Teknologi

### Backend

* Laravel 12

### Frontend

* Blade
* Livewire
* Alpine.js
* Tailwind CSS

### Database

* MySQL

### Queue & Cache

* Redis
* Laravel Horizon

### Storage

* MinIO (utama)
* Kompatibel Amazon S3 dan Cloudflare R2

### Server

* Ubuntu Server
* Nginx
* PHP-FPM
* Redis
* Supervisor

---

## Keamanan

* HTTPS
* Role & Permission
* Audit Trail
* Session Management
* Backup Harian
* Restore
* Rate Limiting
* Enkripsi data sensitif

---

## Roadmap

### Phase 1 — MVP

* Authentication
* Dashboard
* Profil Anak
* Timeline
* Album
* Diary
* Dokumen
* Kalender
* Family Sharing

### Phase 2 — Parenting

* Pertumbuhan
* Grafik
* Kesehatan
* Reminder
* Notifikasi
* Pencarian

### Phase 3 — SaaS

* Multi Tenant
* Subscription
* Billing
* Tenant Management
* Branding
* Monitoring
* Analytics

### Phase 4 — Integration

* REST API
* OAuth
* Webhook
* API Documentation
* SDK

### Phase 5 — Enterprise

* White Label
* Custom Domain
* Multi Bahasa
* Marketplace Plugin
* Enterprise Dashboard

### Phase 6 — Integration

* REST API (Sanctum token-based)
* Webhook System
* Super Admin API
* API Resources & Rate Limiting

### Phase 7 — B2B Healthcare

* Tenant Type System (Family, Hospital, Clinic, etc.)
* Staff Model & Role Management
* Clinical Notes & Referrals
* Patient Links (keluarga ↔ organisasi)
* Facility Admin Panel

### Phase 8 — Responsive Design & UX

* Responsive design 88+ file view Blade
* Landing page, auth views, module views
* SaaS views, support views, Blade components
* Mobile-first design patterns

### Phase 9 — Quality & Improvement

* Bug fixes, documentation sync
* Advanced filtering, sort options
* UX improvements (empty state, validation, toast)
* Architecture improvements

### Phase 10 — PWA & Enhancement

* PWA Support (manifest, service worker, offline)
* Social sharing (Facebook, Twitter, WhatsApp, Telegram)
* Data import (CSV/JSON)
* Caching strategy & subscription lifecycle

### Phase 11 — Security Hardening

* Security fixes & environment sync
* PHP version update
* Route & link audit (284 routes)
* Responsive tables, loading states

### Phase 12 — Achievement & Milestones

* Achievement system (11 tipe pencapaian)
* Milestone alerts (5 tipe milestone)
* Keyboard shortcuts, print-friendly CSS
* Toast mobile positioning

### Phase 13 — B2B Healthcare (Full)

* Facility admin panel (19 views)
* B2B dashboard & analytics
* B2B monitoring & reporting
* Staff, patients, clinical notes, referrals

### Phase 14-17 — Audit & Code Quality

* Bug fixes, responsive fixes, UI/UX improvements
* DocumentType enum, i18n translation keys
* MediaService constructor injection
* Carbon import fix

### Phase 18-19 — i18n & UU PDP Compliance

* 50+ translation keys (ID & EN)
* Family permission levels
* Consent management
* Right to erasure (account deletion)

### Phase 20 — Comprehensive Audit

* 42 hardcoded strings → `__()` helper
* Return type hints (15 methods)
* DocumentType enum with options()
* 693 tests, 1668 assertions — all passing

### Phase 21 — Architecture Evolution ✅

* **Core Architecture**: Identity → Family → Person → Relationship → Organization → Connection → Permission
* **10 Prinsip Inti** — Fondasi desain platform
* **Connection System** — Hubungan keluarga ↔ organisasi dengan permission-based access
* **Family Tree** — Visualisasi hubungan keluarga dan organisasi
* **B2B Assisted Registration** — Organisasi membantu registrasi keluarga
* **Referral System** — Tracking referral B2B → B2C
* **Audit Trail** — WHO, WHAT, WHEN, WHERE, WHY, PERMISSION
* **Updated Pricing** — B2C (Free, Family, Family Plus, Family Pro) + B2B (Basic, Growth, Pro, Enterprise)
* **Subscription tied to Family/Organization**, bukan individual user

---

## Keunggulan Kompetitif

* Fokus pada **Digital Life Book & Family Life Platform**, bukan sekadar album foto.
* Menyatukan kenangan, kesehatan, pendidikan, dan dokumen dalam satu platform.
* **Sistem Connection** yang menghubungkan keluarga dengan organisasi secara aman.
* **Family Tree** sebagai fondasi data, bukan sekadar fitur visual.
* **B2B Assisted Registration** — organisasi membantu keluarga baru masuk platform.
* **Audit Trail** komprehensif untuk transparansi dan keamanan.
* Arsitektur SaaS yang melayani B2C (Family) dan B2B (Organization) dalam satu platform.
* Tidak bergantung pada vendor AI atau layanan tertentu.
* Integrasi fleksibel melalui Custom API.
* Teknologi modern dengan Laravel dan MySQL yang mudah dikembangkan.

---

## Tujuan Jangka Panjang

ForMysha bertujuan menjadi platform dokumentasi digital keluarga yang dipercaya oleh jutaan orang tua. Produk dikembangkan secara bertahap, dimulai dari **web SaaS** sebagai fondasi utama, kemudian dapat diperluas ke aplikasi mobile, desktop, dan solusi enterprise sesuai kebutuhan pasar.

### Motto

> **Every Moment, Every Memory, One Lifetime.**

---

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
