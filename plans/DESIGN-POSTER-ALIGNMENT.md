# Rencana: Penyelarasan Desain ForMysha dengan Poster

## Ringkasan Analisis

Berdasarkan analisis dua poster desain ForMysha, berikut adalah kesenjangan utama yang perlu ditangani:

---

## Kesenjangan yang Ditemukan

### 1. Dashboard (Poster 1)
**Poster menampilkan:**
- Profil anak dengan foto prominent di bagian atas
- Stats cards: Usia (2 Tahun 3 Bulan), Momen (1.248), Dokumen (24 File)
- "Momen Terbaru" dengan thumbnail foto aktual
- "Pengingat" section dengan agenda spesifik (Imunisasi DPT, Kontrol Dokter, Ulang Tahun Nenek)
- Sidebar navigation: Dashboard, Timeline, Album, Diary, Pertumbuhan, Kesehatan, Dokumen, Kalender, Keluarga, Pengaturan

**Kode saat ini:**
- Dashboard generik dengan child cards
- Statistik: Timeline, Diary, Dokumen counts
- Timeline Terbaru, Acara Mendatang, Diary Terbaru (text-based)
- Quick Access grid

### 2. Landing Page (Poster 1 & 2)
**Poster menampilkan:**
- Hero dengan elemen dekoratif (awan, bintang, mainan)
- Brand identity explanation: For, My, Sha, .my.id
- Feature icons row di bagian bawah
- "Simpan Hari ini, Kenang Selamanya" CTA

**Kode saat ini:**
- Hero sederhana dengan logo dan tagline
- Features section dengan 6 cards
- CTA section

### 3. Mobile Experience (Poster 2)
**Poster menampilkan:**
- Bottom navigation bar dengan ikon modul
- Grid layout untuk modul (Timeline, Album, Diary, Dokumen, dll)
- Dashboard mobile yang terstruktur

**Kode saat ini:**
- Mobile nav ada di child-nav.blade.php
- Layout sudah responsive

---

## Rencana Implementasi

### Tahap 1: Dashboard Redesign
1. **Perbarui DashboardController** untuk menyediakan data yang sesuai dengan poster
2. **Redesign dashboard.blade.php** dengan:
   - Profil anak prominent dengan foto
   - Stats cards (Usia, Total Momen, Total Dokumen)
   - "Momen Terbaru" dengan thumbnail foto
   - "Pengingat" section
   - Sidebar navigation yang sesuai poster

### Tahap 2: Landing Page Enhancement
1. **Perbarui welcome.blade.php** dengan:
   - Hero section yang lebih playful dengan elemen dekoratif
   - Brand identity explanation section
   - Feature icons row
   - CTA yang lebih menarik

### Tahap 3: Component Updates
1. **Perbarui child-nav.blade.php** untuk mencocokkan sidebar poster
2. **Pastikan mobile navigation** sesuai dengan mockup poster

### Tahap 4: Testing & Polish
1. Jalankan test suite untuk memastikan tidak ada regression
2. Pastikan semua halaman responsive
3. Periksa aksesibilitas

---

## File yang Perlu Dimodifikasi

### Dashboard
- `app/Http/Controllers/DashboardController.php`
- `app/Services/DashboardService.php`
- `resources/views/dashboard.blade.php`

### Landing Page
- `resources/views/welcome.blade.php`

### Navigation
- `resources/views/components/child-nav.blade.php`
- `resources/views/layouts/navigation.blade.php`

### CSS
- `resources/css/app.css` (jika perlu style baru)

---

## Prioritas

| # | Task | Prioritas | Status |
|---|------|-----------|--------|
| 1 | Dashboard redesign sesuai poster | Tinggi | Pending |
| 2 | Landing page enhancement | Tinggi | Pending |
| 3 | Child nav sidebar update | Sedang | Pending |
| 4 | Mobile navigation polish | Sedang | Pending |
| 5 | Testing & QA | Tinggi | Pending |

---

## Catatan Penting

1. Semua UI text harus dalam Bahasa Indonesia
2. Gunakan komponen Blade yang sudah ada sebelum membuat baru
3. Ikuti color palette pastel: skyBlue, mintGreen, softPink, lavender, warmYellow, peach, softOrange, cream
4. Font: Nunito (friendly & rounded)
5. Rounded corners, soft shadows, gradient backgrounds
6. Responsive design: mobile-first approach

---

## Contoh Dashboard sesuai Poster

```
┌─────────────────────────────────────────────────────┐
│ Selamat datang kembali, [Nama User] 💕              │
│ Bersama setiap langkahmu                            │
├─────────────────────────────────────────────────────┤
│ ┌──────────┐ ┌──────────┐ ┌──────────┐             │
│ │ 📷 Foto  │ │ Usia     │ │ Momen    │             │
│ │ Anak     │ │ 2 Tahun  │ │ 1.248    │             │
│ │          │ │ 3 Bulan  │ │          │             │
│ └──────────┘ └──────────┘ └──────────┘             │
├─────────────────────────────────────────────────────┤
│ Momen Terbaru                    │ Pengingat        │
│ ┌────┐ ┌────┐ ┌────┐ ┌────┐    │ ☐ Imunisasi DPT │
│ │ 📷 │ │ 📷 │ │ 📷 │ │ 📷 │    │ ☐ Kontrol Dokter│
│ │    │ │    │ │    │ │    │    │ ☐ Ulang Tahun   │
│ └────┘ └────┘ └────┘ └────┘    │    Nenek        │
│ Ulang  Vaksin  Main  Berenang   │                  │
│ Tahun   asasi  di    di         │                  │
│         Taman  Taman            │                  │
└─────────────────────────────────────────────────────┘
```

---

**Dokumen ini dibuat sebagai panduan implementasi untuk menyelaraskan desain ForMysha dengan poster yang diberikan.**
