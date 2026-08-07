# ForMysha — Rencana Implementasi MVP

## Analisis Kondisi Saat Ini

### Status Proyek

**Phase 1 — MVP: ✅ SELESAI (12 Batch)**

| Batch | Status | Tests |
|-------|--------|-------|
| Batch 0: Foundation & Configuration | ✅ Selesai | - |
| Batch 1: User Profile & Child Model | ✅ Selesai | - |
| Batch 2: Timeline & Memories | ✅ Selesai | - |
| Batch 3: Album & Gallery | ✅ Selesai | - |
| Batch 4: Diary | ✅ Selesai | - |
| Batch 5: Documents | ✅ Selesai | 28 Unit |
| Batch 6: Calendar & Reminders | ✅ Selesai | 30 Unit + Feature |
| Batch 7: Dashboard Real | ✅ Selesai | - |
| Batch 8: Public Profile & Slug | ✅ Selesai | 25 Feature + Unit |
| Batch 9: Navigation & UX Polish | ✅ Selesai | - |
| Batch 10: Testing & QA | ✅ Selesai | **238 tests (495 assertions)** |
| Batch 11: Documentation Sync | ✅ Selesai | - |

**Total: 238 tests, 495 assertions, 73 routes**

### Ringkasan Implementasi

Proyek ini telah diimplementasi dari **scaffold Laravel Breeze** menjadi **Digital Life Book** yang fungsional.

### Tech Stack Aktual (dari composer.json & package.json)

| Komponen | Aktual | Dokumentasi |
|---|---|---|
| Laravel | 13.8 | 12 |
| PHP | 8.3+ | 8.4 |
| Frontend | Blade + Alpine.js + Tailwind CSS 3.x + Vite 8.x | Blade + Livewire + Alpine.js + Tailwind CSS |
| Auth | Laravel Breeze 2.4 | - |
| Testing | Pest 5.0 | - |
| Database | SQLite (default) | PostgreSQL |
| Queue/Cache | Database (default) | Redis + Horizon |
| Storage | Local (default) | MinIO |

### Gap Kritis

1. **Livewire belum terinstall** — Dokumentasi menyebutkan Livewire tapi tidak ada di composer.json
2. **APP_NAME masih "Laravel"** — Belum diubah ke "ForMysha"
3. **Database config belum PostgreSQL** — Masih SQLite default
4. **Redis/Horizon belum dikonfigurasi** — Tidak ada di dependencies
5. **MinIO/S3 belum dikonfigurasi** — Storage masih local
6. **Tidak ada model/migration/controller ForMysha** — Semua masih default Breeze
7. **Welcome page masih default Laravel** — Belum ada landing page ForMysha
8. **Favicon/Logo belum diintegrasikan** ke layout blade

### Yang Sudah Ada

- ✅ Authentication (login, register, forgot password, email verification)
- ✅ Basic Dashboard (hanya "You're logged in!")
- ✅ Profile management (edit, update, delete)
- ✅ Logo & Favicon di `public/`
- ✅ Tailwind CSS + Alpine.js
- ✅ Pest testing setup
- ✅ Laravel Boost + Pint

---

## Rencana Implementasi Phase 1 — MVP

### Batch 0: Foundation & Configuration

**Tujuan:** Setup fondasi yang benar sebelum membangun fitur.

#### 0.1 Update Environment & Config
- Ubah `APP_NAME` di `.env.example` dan `config/app.php` menjadi `ForMysha`
- Tambahkan `APP_URL` yang sesuai
- Update `APP_LOCALE` ke `id`
- Pastikan logo dan favicon diintegrasikan ke layout

#### 0.2 Update Branding Layout
- Update `resources/views/layouts/app.blade.php` — ganti font, favicon, title
- Update `resources/views/layouts/guest.blade.php` — favicon, title
- Update `resources/views/components/application-logo.blade.php` — gunakan logo ForMysha
- Update `resources/views/welcome.blade.php` — buat landing page sederhana ForMysha
- Update `resources/views/layouts/navigation.blade.php` — tambah menu sesuai modul

#### 0.3 Tailwind CSS Customization
- Update `tailwind.config.js` — tambah warna pastel, font custom
- Buat CSS custom variables untuk brand colors
- Tambahkan custom components (rounded cards, soft shadows, dll)

---

### Batch 1: User Profile & Child Model

**Tujuan:** Setup data model inti — User yang memiliki Child.

#### 1.1 Migration — User Profile Extension
```
Schema: users
- Add: avatar (string, nullable)
- Add: phone (string, nullable)
- Add: date_of_birth (date, nullable)
- Add: address (text, nullable)
- Add: role (enum: parent, guardian, admin)
```

#### 1.2 Migration — Children Table
```
Schema: children
- id
- user_id (foreign key)
- name (string)
- slug (string, unique) — untuk URL formysha.my.id/{slug}
- nickname (string, nullable)
- gender (enum: male, female)
- date_of_birth (date)
- place_of_birth (string, nullable)
- blood_type (string, nullable)
- photo (string, nullable)
- bio (text, nullable)
- is_public (boolean, default: false)
- public_profile_data (json, nullable)
- timestamps
```

#### 1.3 Migration — Family Members Table
```
Schema: family_members
- id
- child_id (foreign key)
- user_id (foreign key, nullable)
- name (string)
- relationship (enum: father, mother, guardian, grandfather, grandmother, sibling, other)
- phone (string, nullable)
- email (string, nullable)
- photo (string, nullable)
- is_primary (boolean, default: false)
- timestamps
```

#### 1.4 Models
- `App\Models\User` — tambah relationships (children, profile)
- `App\Models\Child` — model baru
- `App\Models\FamilyMember` — model baru

#### 1.5 Factories & Seeders
- `ChildFactory`
- `FamilyMemberFactory`
- Update `UserFactory`
- `ChildSeeder` (untuk testing)

#### 1.6 Controllers
- `App\Http\Controllers\ChildController` — CRUD anak
- `App\Http\Controllers\FamilyMemberController` — CRUD anggota keluarga

#### 1.7 Views
- `resources/views/children/index.blade.php` — daftar anak
- `resources/views/children/create.blade.php` — tambah anak
- `resources/views/children/show.blade.php` — profil anak
- `resources/views/children/edit.blade.php` — edit profil anak
- `resources/views/family/index.blade.php` — daftar keluarga
- `resources/views/family/create.blade.php` — tambah anggota
- `resources/views/family/edit.blade.php` — edit anggota

#### 1.8 Routes
- `GET /children` — index
- `GET /children/create` — create
- `POST /children` — store
- `GET /children/{child}` — show
- `GET /children/{child}/edit` — edit
- `PUT /children/{child}` — update
- `DELETE /children/{child}` — destroy
- `GET /children/{child}/family` — family index
- `POST /children/{child}/family` — family store
- `PUT /children/{child}/family/{member}` — family update
- `DELETE /children/{child}/family/{member}` — family destroy

#### 1.9 Tests
- Feature test untuk Child CRUD
- Feature test untuk FamilyMember CRUD
- Unit test untuk Child model (slug generation, relationships)

---

### Batch 2: Timeline & Memories

**Tujuan:** Fitur inti — mencatat kenangan perjalanan hidup anak.

#### 2.1 Migration — Timeline Table
```
Schema: timelines
- id
- child_id (foreign key)
- user_id (foreign key)
- title (string)
- description (text, nullable)
- event_date (date)
- event_time (time, nullable)
- location (string, nullable)
- latitude (decimal, nullable)
- longitude (decimal, nullable)
- mood (enum: happy, excited, calm, sad, surprised, loved, nullable)
- tags (json, nullable)
- is_featured (boolean, default: false)
- timestamps
```

#### 2.2 Migration — Media Table
```
Schema: media
- id
- mediable_type (string) — polymorphic
- mediable_id (unsignedBigInteger) — polymorphic
- file_path (string)
- file_name (string)
- file_type (enum: photo, video, audio, document)
- file_size (unsignedBigInteger)
- alt_text (string, nullable)
- sort_order (integer, default: 0)
- timestamps
```

#### 2.3 Models
- `App\Models\Timeline`
- `App\Models\Media` (polymorphic)

#### 2.4 Controllers
- `App\Http\Controllers\TimelineController` — CRUD timeline
- `App\Http\Controllers\MediaController` — upload/hapus media

#### 2.5 Views
- `resources/views/timeline/index.blade.php` — daftar timeline
- `resources/views/timeline/create.blade.php` — tambah kenangan
- `resources/views/timeline/show.blade.php` — detail kenangan
- `resources/views/timeline/edit.blade.php` — edit kenangan

#### 2.6 Routes
- `GET /children/{child}/timeline` — index
- `GET /children/{child}/timeline/create` — create
- `POST /children/{child}/timeline` — store
- `GET /children/{child}/timeline/{timeline}` — show
- `GET /children/{child}/timeline/{timeline}/edit` — edit
- `PUT /children/{child}/timeline/{timeline}` — update
- `DELETE /children/{child}/timeline/{timeline}` — destroy

---

### Batch 3: Album & Gallery

**Tujuan:** Galeri foto dan video terorganisir.

#### 3.1 Migration — Albums Table
```
Schema: albums
- id
- child_id (foreign key)
- name (string)
- description (text, nullable)
- cover_photo (string, nullable)
- is_private (boolean, default: true)
- sort_order (integer, default: 0)
- timestamps
```

#### 3.2 Model & Controller
- `App\Models\Album`
- `App\Http\Controllers\AlbumController`

#### 3.3 Views
- `resources/views/albums/index.blade.php`
- `resources/views/albums/create.blade.php`
- `resources/views/albums/show.blade.php`
- `resources/views/albums/edit.blade.php`

---

### Batch 4: Diary

**Tujuan:** Catatan harian dan cerita perkembangan.

#### 4.1 Migration — Diaries Table
```
Schema: diaries
- id
- child_id (foreign key)
- user_id (foreign key)
- title (string)
- content (text)
- mood (enum, nullable)
- diary_date (date)
- weather (string, nullable)
- is_private (boolean, default: true)
- timestamps
```

#### 4.2 Model & Controller
- `App\Models\Diary`
- `App\Http\Controllers\DiaryController`

#### 4.3 Views
- `resources/views/diaries/index.blade.php`
- `resources/views/diaries/create.blade.php`
- `resources/views/diaries/show.blade.php`
- `resources/views/diaries/edit.blade.php`

---

### Batch 5: Documents

**Tujuan:** Penyimpanan dokumen penting anak.

#### 5.1 Migration — Documents Table
```
Schema: documents
- id
- child_id (foreign key)
- user_id (foreign key)
- name (string)
- type (enum: birth_certificate, family_card, kia, bpjs, passport, certificate, report_card, other)
- description (text, nullable)
- file_path (string)
- file_name (string)
- file_size (unsignedBigInteger)
- issued_date (date, nullable)
- expiry_date (date, nullable)
- is_private (boolean, default: true)
- timestamps
```

#### 5.2 Model & Controller
- `App\Models\Document`
- `App\Http\Controllers\DocumentController`

#### 5.3 Views
- `resources/views/documents/index.blade.php`
- `resources/views/documents/create.blade.php`
- `resources/views/documents/show.blade.php`
- `resources/views/documents/edit.blade.php`

---

### Batch 6: Calendar & Reminders

**Tujuan:** Kalender untuk jadwal imunisasi, ulang tahun, dan reminder.

#### 6.1 Migration — Events Table
```
Schema: events
- id
- child_id (foreign key)
- user_id (foreign key)
- title (string)
- description (text, nullable)
- event_date (date)
- event_time (time, nullable)
- event_type (enum: birthday, immunization, appointment, school, other)
- is_recurring (boolean, default: false)
- recurrence_pattern (string, nullable)
- reminder_at (timestamp, nullable)
- timestamps
```

#### 6.2 Model & Controller
- `App\Models\Event`
- `App\Http\Controllers\CalendarController`

#### 6.3 Views
- `resources/views/calendar/index.blade.php` — tampilan kalender
- `resources/views/calendar/create.blade.php` — tambah event
- `resources/views/calendar/show.blade.php` — detail event

---

### Batch 7: Dashboard Real

**Tujuan:** Dashboard yang menampilkan data nyata.

#### 7.1 Update Dashboard View
- Tampilkan foto terbaru anak
- Timeline singkat
- Pengingat penting
- Ringkasan pertumbuhan
- Akses cepat ke fitur utama

#### 7.2 Dashboard Controller
- Update route handler untuk mengambil data

---

### Batch 8: Public Profile & Slug

**Tujuan:** Halaman publik untuk setiap anak.

#### 8.1 Routes
- `GET /{slug}` — halaman publik anak

#### 8.2 Controller
- `App\Http\Controllers\PublicProfileController`

#### 8.3 Views
- `resources/views/public/profile.blade.php`

---

### Batch 9: Navigation & UX Polish

**Tujuan:** Navigasi yang lengkap dan UX yang seamless.

#### 9.1 Update Navigation
- Sidebar navigation untuk semua modul
- Breadcrumb navigation
- Mobile bottom navigation

#### 9.2 Components
- Reusable card components
- Empty state components
- Loading states
- Toast notifications

#### 9.3 Responsive Design
- Pastikan semua halaman mobile-friendly
- Test di berbagai ukuran layar

---

### Batch 10: Testing & Quality Assurance

**Tujuan:** Memastikan semua fitur berfungsi dan tested.

#### 10.1 Feature Tests
- Child CRUD tests
- Timeline CRUD tests
- Album CRUD tests
- Diary CRUD tests
- Document CRUD tests
- Calendar CRUD tests
- Public profile tests

#### 10.2 Unit Tests
- Model relationship tests
- Slug generation tests
- Validation tests

#### 10.3 Code Quality
- Jalankan Pint formatter
- Pastikan tidak ada console errors
- Pastikan tidak ada broken links/routes

---

### Batch 11: Documentation Sync

**Tujuan:** Update dokumentasi sesuai implementasi.

#### 11.1 Update FILES
- Update `FEATURES.md` — tandai fitur yang sudah diimplementasi
- Update `ROADMAP.md` — update status progress
- Update `AGENTS.md` — tambah rules baru jika ada

---

## Urutan Eksekusi

```
Batch 0: Foundation & Configuration
    ↓
Batch 1: User Profile & Child Model
    ↓
Batch 2: Timeline & Memories
    ↓
Batch 3: Album & Gallery
    ↓
Batch 4: Diary
    ↓
Batch 5: Documents
    ↓
Batch 6: Calendar & Reminders
    ↓
Batch 7: Dashboard Real
    ↓
Batch 8: Public Profile & Slug
    ↓
Batch 9: Navigation & UX Polish
    ↓
Batch 10: Testing & QA
    ↓
Batch 11: Documentation Sync
```

---

## Saran Perbaikan Arsitektur & Improvisasi

### 1. Livewire Integration
Pertimbangkan untuk menambahkan Livewire untuk:
- Real-time dashboard updates
- Inline editing di timeline
- Drag & drop photo upload
- Live search & filter

### 2. Media Processing Pipeline
- Gunakan queue untuk compress/resize gambar
- Buat multiple sizes (thumbnail, medium, large)
- Support WebP conversion

### 3. Tagging System
- Buat table `tags` dan `taggables` (polymorphic)
- Memungkinkan tagging di timeline, album, dokumen

### 4. Growth Tracking (Phase 2)
- Chart.js atau Chartisan untuk grafik pertumbuhan
- Export growth report sebagai PDF

### 5. Notification System
- Email notifications untuk reminder imunisasi
- Push notifications (future)
- In-app notifications

### 6. Export Features
- Export timeline sebagai PDF
- Export album sebagai ZIP
- Export semua data sebagai backup

### 7. Role-Based Access Control
- Gunakan Spatie Permission untuk RBAC
- Roles: parent, guardian, viewer
- Permissions per child

### 8. Audit Trail
- Log semua perubahan data
- Siapa yang mengubah apa dan kapan

### 9. Search & Filter
- Full-text search di timeline, dokumen, foto
- Filter berdasarkan tanggal, tag, tipe

### 10. Mobile-First Design
- Prioritaskan mobile experience
- Bottom navigation untuk mobile
- Swipe gestures untuk gallery

---

## Catatan Penting

1. **TIDAK BOLEH** mengubah migration yang sudah dieksekusi di production
2. Semua migration baru harus bersifat **non-destructive**
3. Seeder harus menggunakan `updateOrCreate` / `firstOrCreate`
4. Semua route harus valid (tidak ada 404)
5. Semua tombol harus memiliki handler yang berfungsi
6. Semua halaman harus responsive
7. Run `vendor/bin/pint` setelah perubahan PHP
8. Run tests setelah setiap batch selesai
