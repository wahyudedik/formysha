# Dark Mode Implementation Plan — ForMysha

## Status: 🔴 Belum Dikerjakan

## Temuan Analisis

### Kondisi Saat Ini

1. **`tailwind.config.js`** — TIDAK ada konfigurasi `darkMode`. Semua class `dark:` di Breeze components **tidak aktif**.
2. **`resources/js/app.js`** — Tidak ada logika dark mode toggle.
3. **`resources/css/app.css`** — CSS variables di `:root` tapi tidak ada dark variants. Custom CSS components (`.card`, `.btn-primary`, `.btn-secondary`) menggunakan warna light hardcoded.
4. **`layouts/app.blade.php`** — Background hardcoded light (`bg-cream-50`, `bg-white`).
5. **`layouts/navigation.blade.php`** — Background hardcoded white.

### File Yang Sudah Punya Class `dark:` (Breeze-generated, SAAT INI TIDAK AKTIF)

- `components/dropdown.blade.php` — `dark:bg-gray-700`
- `components/modal.blade.php` — `dark:bg-gray-900`, `dark:bg-gray-800`
- `components/nav-link.blade.php`
- `components/responsive-nav-link.blade.php`
- `components/text-input.blade.php`
- `components/input-label.blade.php`
- `components/input-error.blade.php`
- `components/primary-button.blade.php`
- `components/secondary-button.blade.php`
- `components/danger-button.blade.php`
- `components/dropdown-link.blade.php`
- `components/auth-session-status.blade.php`
- `auth/*.blade.php` (login, register, forgot-password, confirm-password, verify-email)
- `profile/*.blade.php` (edit, partials/*)

### File Yang BELUM Punya Dark Mode (Custom ForMysha Views — ~70+ files)

**Layout & Navigasi:**
- `layouts/app.blade.php`
- `layouts/navigation.blade.php`

**Components Custom (16 files):**
- `components/child-nav.blade.php`
- `components/child-selector.blade.php`
- `components/empty-state.blade.php`
- `components/breadcrumb.blade.php`
- `components/page-header.blade.php`
- `components/confirm-delete.blade.php`
- `components/toast.blade.php`
- `components/notification-badge.blade.php`
- `components/calendar-grid.blade.php`
- `components/growth-chart.blade.php`
- `components/loading-skeleton.blade.php`
- `components/loading.blade.php`
- `components/media-upload.blade.php`
- `components/pages-layout.blade.php`
- `components/branding/footer.blade.php`
- `components/branding/favicon.blade.php`

**Dashboard:**
- `dashboard.blade.php`

**Module Views (~40 files):**
- `timeline/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`
- `growth/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`
- `health/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`
- `family/index.blade.php`, `create.blade.php`, `edit.blade.php`
- `notifications/index.blade.php`
- `search/index.blade.php`
- `subscription/plans.blade.php`, `current.blade.php`, `history.blade.php`, `payment-upload.blade.php`
- `super-admin/dashboard.blade.php`
- `super-admin/tenants/*.blade.php`
- `super-admin/payments/*.blade.php`
- `super-admin/plans/*.blade.php`
- `super-admin/analytics/index.blade.php`
- `super-admin/monitoring/index.blade.php`
- `super-admin/audit-logs/index.blade.php`
- `super-admin/plugins/*.blade.php`
- `super-admin/partials/sidebar.blade.php`
- `pages/about.blade.php`, `privacy.blade.php`, `terms.blade.php`
- `public/profile.blade.php`

### Pendekatan

Gunakan **`darkMode: 'media'`** (bukan `'class'`). Alasan:

1. User ingin dark mode aktif otomatis saat device/browsing pakai dark mode
2. Tidak perlu toggle button — sesuai permintaan user
3. Implementasi lebih simpel, tidak perlu Alpine.js localStorage logic
4. Berfungsi langsung di semua device yang support `prefers-color-scheme`

### Dark Mode Color Mapping

#### Background Mapping

| Light Class | Dark Class | Element |
|---|---|---|
| `bg-cream-50` | `dark:bg-gray-900` | Body, main container |
| `bg-white` | `dark:bg-gray-800` | Cards, panels, sidebars |
| `bg-white/80` | `dark:bg-gray-800/80` | Frosted glass elements |
| `bg-white/95` | `dark:bg-gray-800/95` | Mobile bottom nav |
| `bg-gray-50` | `dark:bg-gray-800` | Hover states, calendar |
| `bg-gray-100` | `dark:bg-gray-700` | Buttons, inputs |

#### Text Mapping

| Light Class | Dark Class | Usage |
|---|---|---|
| `text-gray-800` | `dark:text-gray-100` | Headings, primary text |
| `text-gray-700` | `dark:text-gray-200` | Secondary headings |
| `text-gray-600` | `dark:text-gray-300` | Body text, labels |
| `text-gray-500` | `dark:text-gray-400` | Descriptions, meta |
| `text-gray-400` | `dark:text-gray-500` | Captions, timestamps |

#### Border Mapping

| Light Class | Dark Class |
|---|---|
| `border-gray-100` | `dark:border-gray-700` |
| `border-gray-200` | `dark:border-gray-600` |

#### Brand Color Mapping (Pastel Backgrounds)

| Light Class | Dark Class |
|---|---|
| `bg-softPink-50` | `dark:bg-softPink-950/30` |
| `bg-softPink-100` | `dark:bg-softPink-900/30` |
| `bg-skyBlue-50` | `dark:bg-skyBlue-950/30` |
| `bg-skyBlue-100` | `dark:bg-skyBlue-900/30` |
| `bg-mintGreen-50` | `dark:bg-mintGreen-950/30` |
| `bg-mintGreen-100` | `dark:bg-mintGreen-900/30` |
| `bg-lavender-50` | `dark:bg-lavender-950/30` |
| `bg-lavender-100` | `dark:bg-lavender-900/30` |
| `bg-warmYellow-50` | `dark:bg-warmYellow-950/30` |
| `bg-warmYellow-100` | `dark:bg-warmYellow-900/30` |
| `bg-peach-50` | `dark:bg-peach-950/30` |
| `bg-softOrange-50` | `dark:bg-softOrange-950/30` |

#### Brand Color Text Mapping

| Light Class | Dark Class |
|---|---|
| `text-softPink-500` | `dark:text-softPink-400` |
| `text-softPink-600` | `dark:text-softPink-300` |
| `text-skyBlue-500` | `dark:text-skyBlue-400` |
| `text-skyBlue-600` | `dark:text-skyBlue-300` |
| `text-mintGreen-500` | `dark:text-mintGreen-400` |
| `text-mintGreen-600` | `dark:text-mintGreen-300` |
| `text-mintGreen-700` | `dark:text-mintGreen-300` |
| `text-mintGreen-800` | `dark:text-mintGreen-200` |
| `text-lavender-500` | `dark:text-lavender-400` |
| `text-warmYellow-600` | `dark:text-warmYellow-300` |
| `text-warmYellow-700` | `dark:text-warmYellow-300` |
| `text-warmYellow-800` | `dark:text-warmYellow-200` |

#### Gradient Mapping

| Light Class | Dark Class |
|---|---|
| `from-softPink-50` | `dark:from-softPink-950/30` |
| `from-cream-50` | `dark:from-gray-800` |
| `to-lavender-50` | `dark:to-lavender-950/30` |
| `via-cream-50` | `dark:via-gray-800` |
| `from-skyBlue-50` | `dark:from-skyBlue-950/30` |
| `to-mintGreen-50` | `dark:to-mintGreen-950/30` |

#### Shadow Mapping

| Light Class | Dark Class |
|---|---|
| `shadow-soft` | `dark:shadow-gray-900/20` |
| `shadow-soft-md` | `dark:shadow-gray-900/30` |
| `ring-white` | `dark:ring-gray-800` |
| `ring-2 ring-softPink-300` | `dark:ring-softPink-700` |

---

## Rencana Implementasi

### Step 1: Konfigurasi Tailwind Dark Mode

**File:** `tailwind.config.js`

Tambahkan `darkMode: 'media'` di level atas config.

### Step 2: Update CSS Custom Properties

**File:** `resources/css/app.css`

Tambahkan dark variants untuk CSS variables dan custom CSS components:
- `.card` → `dark:bg-gray-800 dark:border-gray-700`
- `.btn-secondary` → `dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600`
- `.section-title` → `dark:text-gray-100`
- `.bg-gradient-warm` → dark gradient variants
- `.bg-gradient-sky` → dark gradient variants

### Step 3: Update Main Layout

**File:** `layouts/app.blade.php`

- Body: `dark:bg-gray-900`
- Header: `dark:bg-gray-800 dark:border-gray-700`
- Footer: `dark:bg-gray-800/80 dark:border-gray-700`

### Step 4: Update Navigation

**File:** `layouts/navigation.blade.php`

- Nav bar: `dark:bg-gray-800/80 dark:border-gray-700`
- Text colors: dark variants
- Dropdown menus: dark backgrounds
- Mobile menu: dark backgrounds

### Step 5: Update Custom Components (16 files)

Urutan berdasarkan prioritas (paling sering digunakan):

1. `components/child-nav.blade.php` — Sidebar + mobile bottom nav
2. `components/empty-state.blade.php` — Empty states
3. `components/breadcrumb.blade.php` — Breadcrumbs
4. `components/page-header.blade.php` — Page headers
5. `components/confirm-delete.blade.php` — Delete modal
6. `components/toast.blade.php` — Toast notifications
7. `components/notification-badge.blade.php` — Notification badge
8. `components/calendar-grid.blade.php` — Calendar grid
9. `components/growth-chart.blade.php` — Growth chart (SVG colors)
10. `components/loading-skeleton.blade.php` — Loading skeleton
11. `components/loading.blade.php` — Loading spinner
12. `components/media-upload.blade.php` — Media upload
13. `components/pages-layout.blade.php` — Static pages layout
14. `components/child-selector.blade.php` — Child selector
15. `components/branding/footer.blade.php` — Branding footer
16. `components/branding/favicon.blade.php` — Favicon (no changes needed)

### Step 6: Update Dashboard

**File:** `dashboard.blade.php`

- Welcome section gradient
- Child profile cards
- Stats cards
- Content sections (Momen, Pengingat, Pertumbuhan, Kesehatan)

### Step 7: Update Module Views (~40 files)

Group berdasarkan kemiripan struktur:

**Group A — CRUD Index Views** (polosan, ada table/list):
- `timeline/index.blade.php`
- `growth/index.blade.php`
- `health/index.blade.php`
- `family/index.blade.php`
- `notifications/index.blade.php`
- `search/index.blade.php`
- `subscription/plans.blade.php`
- `subscription/current.blade.php`
- `subscription/history.blade.php`
- `subscription/payment-upload.blade.php`

**Group B — CRUD Form Views** (create/edit):
- `timeline/create.blade.php`, `edit.blade.php`
- `growth/create.blade.php`, `edit.blade.php`
- `health/create.blade.php`, `edit.blade.php`
- `health/show.blade.php`
- `family/create.blade.php`, `edit.blade.php`
- `growth/show.blade.php`

**Group C — Show/Detail Views**:
- `timeline/show.blade.php`

**Group D — Super Admin Views**:
- `super-admin/dashboard.blade.php`
- `super-admin/partials/sidebar.blade.php`
- `super-admin/tenants/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`
- `super-admin/payments/index.blade.php`, `show.blade.php`
- `super-admin/plans/index.blade.php`, `create.blade.php`, `edit.blade.php`
- `super-admin/analytics/index.blade.php`
- `super-admin/monitoring/index.blade.php`
- `super-admin/audit-logs/index.blade.php`
- `super-admin/plugins/index.blade.php`, `show.blade.php`

**Group E — Static/Public Pages**:
- `pages/about.blade.php`
- `pages/privacy.blade.php`
- `pages/terms.blade.php`
- `public/profile.blade.php`

### Step 8: Handle SVG Chart Colors

**File:** `components/growth-chart.blade.php`

SVG charts menggunakan warna hardcoded di attribute. Perlu tambahkan logic untuk:
- Background chart: dark background
- Reference lines: adjust opacity/warna
- Axis labels: dark text
- Data point colors: tetap vivid di dark mode

### Step 9: Rebuild Frontend & Tests

- Jalankan `npm run build` untuk rebuild Tailwind CSS
- Jalankan `php artisan test --compact` untuk memastikan tidak ada regression
- Jalankan `vendor/bin/pint --dirty --format agent`

---

## Pola Implementasi

### Pattern 1: Card/Panel

```blade
{{-- Light --}}
<div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">

{{-- Dark --}}
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft dark:shadow-gray-900/20 sm:rounded-3xl">
```

### Pattern 2: Text Colors

```blade
{{-- Light --}}
<h3 class="font-semibold text-gray-800">Judul</h3>
<p class="text-gray-500">Deskripsi</p>

{{-- Dark --}}
<h3 class="font-semibold text-gray-800 dark:text-gray-100">Judul</h3>
<p class="text-gray-500 dark:text-gray-400">Deskripsi</p>
```

### Pattern 3: Borders

```blade
{{-- Light --}}
<div class="border-b border-gray-100">

{{-- Dark --}}
<div class="border-b border-gray-100 dark:border-gray-700">
```

### Pattern 4: Brand Colored Backgrounds

```blade
{{-- Light --}}
<div class="bg-softPink-50 border border-softPink-100">

{{-- Dark --}}
<div class="bg-softPink-50 dark:bg-softPink-950/30 border border-softPink-100 dark:border-softPink-900/30">
```

### Pattern 5: Gradient Backgrounds

```blade
{{-- Light --}}
<div class="bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50">

{{-- Dark --}}
<div class="bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50 dark:from-softPink-950/30 dark:via-gray-800 dark:to-lavender-950/30">
```

### Pattern 6: Buttons

```blade
{{-- btn-primary --}}
<div class="btn-primary"> {{-- Sudah cukup baik, warna solid --}}

{{-- btn-secondary --}}
<div class="btn-secondary"> {{-- Perlu dark variants --}}
<div class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl border border-gray-200 dark:border-gray-600 shadow-soft transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-skyBlue-300 focus:ring-offset-2">
```

### Pattern 7: Hover States

```blade
{{-- Light --}}
<a class="hover:bg-mintGreen-50 transition">

{{-- Dark --}}
<a class="hover:bg-mintGreen-50 dark:hover:bg-mintGreen-950/30 transition">
```

### Pattern 8: SVG in Dark Mode

```blade
{{-- Light --}}
<svg class="w-5 h-5 text-gray-400">

{{-- Dark --}}
<svg class="w-5 h-5 text-gray-400 dark:text-gray-500">
```

---

## Total File Yang Perlu Diupdate

| Category | Files | Priority |
|---|---|---|
| Config (tailwind.config.js) | 1 | 🔴 Critical |
| CSS (app.css) | 1 | 🔴 Critical |
| Layout (app.blade.php) | 1 | 🔴 Critical |
| Navigation (navigation.blade.php) | 1 | 🔴 Critical |
| Custom Components | 15 | 🟠 High |
| Dashboard | 1 | 🟠 High |
| Module Views (CRUD) | ~20 | 🟡 Medium |
| Super Admin Views | ~12 | 🟡 Medium |
| Static/Public Pages | 4 | 🟢 Low |
| **Total** | **~56 files** | |

## Estimasi Kompleksitas

- **Step 1-2** (Config + CSS): Simpel — 2 file
- **Step 3-4** (Layout + Nav): Medium — 2 file, banyak classes
- **Step 5** (Components): Medium — 15 file, pola repetitif
- **Step 6** (Dashboard): Medium — 1 file, 272 baris
- **Step 7** (Module Views): Tinggi — ~40 file, pola repetitif tapi banyak
- **Step 8** (SVG Charts): Complex — perlu careful color mapping
- **Step 9** (Build + Test): Standard

## Catatan Penting

1. **Shadows di dark mode** — Shadow kurang terlihat di dark background. Gunakan kombinasi `ring` atau border untuk definition.
2. **Gradients** — Gradient pastel di dark mode harus di-adjust agar tidak terlalu terang. Gunakan opacity rendah (10-30%).
3. **SVG Charts** — Warna SVG hardcoded, perlu careful mapping.
4. **Emoji icons** — Emoji tetap tampil baik di dark mode, tidak perlu perubahan.
5. **Brand colors (solid)** — Button dengan warna solid (bg-softPink-300, bg-skyBlue-300) tetap OK di dark mode.
6. **Backdrop blur** — `backdrop-blur-md` bekerja baik di dark mode.
