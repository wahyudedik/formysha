<?php

namespace Database\Seeders;

use App\Enums\FamilyMemberPermission;
use App\Enums\PatientLinkStatus;
use App\Enums\StaffRole;
use App\Enums\TenantType;
use App\Models\Child;
use App\Models\ClinicalNote;
use App\Models\FamilyMember;
use App\Models\PatientLink;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder lengkap untuk B2C Family Sharing dan B2B Facility scenarios.
 *
 * ====================================================================
 * CATATAN PENTING — ALUR AKSES FORMYSHA
 * ====================================================================
 *
 * ### B2C (Keluarga) — Family Sharing
 *
 * ForMysha B2C dirancang sebagai **Digital Life Book** pribadi.
 * Fitur Family Sharing bersifat **metadata-only** — artinya anggota keluarga
 * yang ditambahkan HANYA berupa catatan (nama, email, hubungan, permission).
 *
 * **PENTING: Anggota keluarga TIDAK otomatis mendapat akun login.**
 *
 * Alur saat ini:
 *   1. Orang tua menambah anggota keluarga (misal: Kakek, Nenek, Tante).
 *   2. Data tersimpan: nama, email, telepon, hubungan, level permission.
 *   3. Anggota keluarga TIDAK bisa login ke aplikasi.
 *   4. Data hanya bisa dilihat oleh orang tua yang memiliki akses.
 *
 * **Mengapa demikian?**
 *   - ForMysha mengutamakan PRIVASI data anak.
 *   - Tidak semua anggota keluarga perlu akses aplikasi.
 *   - Fitur ini cocok untuk dokumentasi internal keluarga.
 *
 * **Jika ingin anggota keluarga bisa login:**
 *   - Mereka harus mendaftar akun sendiri di form registrasi.
 *   - Setelah itu, orang tua bisa mengisi `user_id` di family member
 *     untuk menghubungkan dengan akun yang sudah ada.
 *
 * **Permission Levels:**
 *   - `view`  → Hanya bisa melihat data (Kakek, Nenek, Teman)
 *   - `edit`  → Bisa menambah & mengedit data (Ayah, Ibu, Wali)
 *   - `admin` → Bisa mengelola semua data termasuk hapus (Co-parent)
 *
 * ### B2B (Fasilitas Kesehatan) — Staff & Patient
 *
 * ForMysha B2B melayani klinik, rumah sakit, bidan, posyandu, dll.
 *
 * #### Staff (Tenaga Kesehatan)
 *
 * Saat admin fasilitas menambah staf, sistem OTOMATIS membuat akun User
 * dengan随机 password. Namun, **email dengan kredensial TIDAK dikirim**.
 *
 * **Alur Staff:**
 *   1. Admin fasilitas mengisi form: nama, email, role, spesialisasi.
 *   2. Sistem membuat User account (role: `parent`, password: random).
 *   3. Sistem membuat Staff record (linked ke User).
 *   4. Staff harus menggunakan "Lupa Password" untuk mendapat akses.
 *
 * **Staff Roles:**
 *   - `doctor`     → Dokter (bisa catatan klinis + rujukan)
 *   - `midwife`    → Bidan (bisa catatan klinis + rujukan)
 *   - `nurse`      → Perawat (bisa catatan klinis)
 *   - `staff_admin`→ Admin Fasilitas (full access kelola fasilitas)
 *   - `staff`      → Staf Umum (akses terbatas)
 *
 * **Akses per Role:**
 *   - Dashboard          → Semua staf
 *   - Staf Management    → staff_admin, doctor
 *   - Pasien             → staff_admin, doctor, midwife, nurse
 *   - Catatan Klinis     → doctor, midwife, nurse (BUKAN staff_admin)
 *   - Rujukan            → doctor, midwife, staff_admin
 *   - Laporan            → staff_admin saja
 *   - Pengaturan         → staff_admin saja
 *
 * **⚠️ Penting: staff_admin TIDAK bisa akses Catatan Klinis!**
 *   Role admin bersifat "manajerial", bukan klinis.
 *   Jika admin juga perlu menulis catatan klinis, buat akun terpisah
 *   dengan role `doctor` atau `midwife`.
 *
 * #### Patient (Pasien / PatientLink)
 *
 * PatientLink menghubungkan Child (yang sudah terdaftar di ForMysha)
 * dengan Fasilitas Kesehatan.
 *
 * **Alur Patient:**
 *   1. Orang tua mendaftarkan anak di ForMysha (akun B2C).
 *   2. Admin fasilitas membuat PatientLink: pilih Child + Parent.
 *   3. PatientLink dibuat dengan kode unik (link_code).
 *   4. Parent bisa melihat data kesehatan anak di fasilitas.
 *
 * **⚠️ Catatan:**
 *   - Child HARUS sudah terdaftar di ForMysha (punya akun orang tua).
 *   - Fasilitas HANYA bisa melihat data yang diizinkan (permissions).
 *   - Data sensitif (dokumen, foto pribadi) tetap privat.
 *
 * ### Perbedaan B2C vs B2B
 *
 * | Aspek           | B2C (Keluarga)          | B2B (Fasilitas)           |
 * |-----------------|------------------------|--------------------------|
 * | User Account    | Tidak otomatis          | Ya (otomatis dibuat)      |
 * | Email Invitation| Tidak ada               | Tidak ada (password random)|
 * | Login Access    | Tidak bisa              | Bisa (lupa password)      |
 * | Data Scope      | Data pribadi anak       | Data kesehatan terbatas   |
 * | Permission      | view/edit/admin         | per-modul (timeline, dll) |
 *
 * ====================================================================
 */
class FamilyAndFacilitySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedB2CFamilySharing();
        $this->seedB2BFacility();
    }

    /**
     * Seed B2C Family Sharing scenarios.
     *
     * Membuat:
     * - 1 parent user dengan 2 anak
     * - Family members dengan berbagai permission levels
     * - Contoh: father (linked to user), mother (no account), grandmother (no account)
     */
    private function seedB2CFamilySharing(): void
    {
        // ── Parent User ──────────────────────────────────────────────
        $parent = User::firstOrCreate(
            ['email' => 'budi@for-mysha.my.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'parent',
            ]
        );

        // ── Anak Pertama: Mysha ──────────────────────────────────────
        $mysha = Child::firstOrCreate(
            ['slug' => 'mysha'],
            [
                'user_id' => $parent->id,
                'name' => 'Mysha Aisyah',
                'nickname' => 'Mysha',
                'gender' => 'female',
                'date_of_birth' => '2023-06-15',
                'place_of_birth' => 'Jakarta',
                'blood_type' => 'A',
                'is_public' => true,
            ]
        );

        // ── Anak Kedua: Qaireen ──────────────────────────────────────
        $qaireen = Child::firstOrCreate(
            ['slug' => 'qaireen'],
            [
                'user_id' => $parent->id,
                'name' => 'Qaireen Ahmad',
                'nickname' => 'Qai',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
                'place_of_birth' => 'Bandung',
                'blood_type' => 'O',
                'is_public' => false,
            ]
        );

        // ── Family Members untuk Mysha ───────────────────────────────

        // Ayah — linked ke akun parent, permission: edit
        FamilyMember::firstOrCreate(
            ['child_id' => $mysha->id, 'name' => 'Budi Santoso'],
            [
                'user_id' => $parent->id,
                'relationship' => 'father',
                'email' => $parent->email,
                'is_primary' => true,
                'permission_level' => FamilyMemberPermission::Edit,
            ]
        );

        // Ibu — TIDAK linked ke akun (hanya metadata), permission: edit
        FamilyMember::firstOrCreate(
            ['child_id' => $mysha->id, 'name' => 'Rina Sari'],
            [
                'user_id' => null,
                'relationship' => 'mother',
                'email' => 'rina@example.com',
                'phone' => '081234567890',
                'is_primary' => true,
                'permission_level' => FamilyMemberPermission::Edit,
            ]
        );

        // Kakek — TIDAK linked, permission: view only
        FamilyMember::firstOrCreate(
            ['child_id' => $mysha->id, 'name' => 'Hendra Wijaya'],
            [
                'user_id' => null,
                'relationship' => 'grandfather',
                'email' => 'hendra@example.com',
                'phone' => '081234567891',
                'is_primary' => false,
                'permission_level' => FamilyMemberPermission::View,
            ]
        );

        // Nenek — TIDAK linked, permission: view only
        FamilyMember::firstOrCreate(
            ['child_id' => $mysha->id, 'name' => 'Siti Aminah'],
            [
                'user_id' => null,
                'relationship' => 'grandmother',
                'email' => null,
                'phone' => '081234567892',
                'is_primary' => false,
                'permission_level' => FamilyMemberPermission::View,
            ]
        );

        // Saudara — permission: view
        FamilyMember::firstOrCreate(
            ['child_id' => $mysha->id, 'name' => 'Rizki Santoso'],
            [
                'user_id' => null,
                'relationship' => 'sibling',
                'email' => null,
                'phone' => null,
                'is_primary' => false,
                'permission_level' => FamilyMemberPermission::View,
            ]
        );

        // ── Family Members untuk Qaireen ─────────────────────────────
        FamilyMember::firstOrCreate(
            ['child_id' => $qaireen->id, 'name' => 'Budi Santoso'],
            [
                'user_id' => $parent->id,
                'relationship' => 'father',
                'email' => $parent->email,
                'is_primary' => true,
                'permission_level' => FamilyMemberPermission::Edit,
            ]
        );

        FamilyMember::firstOrCreate(
            ['child_id' => $qaireen->id, 'name' => 'Rina Sari'],
            [
                'user_id' => null,
                'relationship' => 'mother',
                'email' => 'rina@example.com',
                'phone' => '081234567890',
                'is_primary' => true,
                'permission_level' => FamilyMemberPermission::Edit,
            ]
        );
    }

    /**
     * Seed B2B Facility scenarios.
     *
     * Membuat:
     * - 1 klinik dengan staff (doctor, midwife, nurse, staff_admin)
     * - 2 parent users dengan anak
     * - PatientLink antara anak dan klinik
     * - Contoh clinical notes
     */
    private function seedB2BFacility(): void
    {
        // ── Facility Tenant (Klinik) ─────────────────────────────────
        $clinic = Tenant::firstOrCreate(
            ['slug' => 'klinik-sehat-bunda'],
            [
                'name' => 'Klinik Sehat Bunda',
                'type' => TenantType::Clinic,
                'is_active' => true,
                'address' => 'Jl. Kesehatan No. 10, Jakarta Selatan',
                'phone' => '021-12345678',
                'email_institution' => 'info@kliniksehatbunda.co.id',
            ]
        );

        // ── Staff: Admin Fasilitas ───────────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@kliniksehatbunda.co.id'],
            [
                'name' => 'Dr. Ratna Dewi',
                'password' => Hash::make(Str::random(12)),
                'email_verified_at' => now(),
                'role' => 'parent', // Staff uses 'parent' role (not tenant_admin)
                'tenant_id' => $clinic->id,
            ]
        );

        Staff::firstOrCreate(
            ['user_id' => $adminUser->id, 'tenant_id' => $clinic->id],
            [
                'staff_role' => StaffRole::StaffAdmin,
                'specialization' => null,
                'license_number' => null,
                'is_active' => true,
            ]
        );

        // ── Staff: Dokter ────────────────────────────────────────────
        $doctorUser = User::firstOrCreate(
            ['email' => 'dokter.budi@kliniksehatbunda.co.id'],
            [
                'name' => 'Dr. Budi Prasetyo',
                'password' => Hash::make(Str::random(12)),
                'email_verified_at' => now(),
                'role' => 'parent',
                'tenant_id' => $clinic->id,
            ]
        );

        Staff::firstOrCreate(
            ['user_id' => $doctorUser->id, 'tenant_id' => $clinic->id],
            [
                'staff_role' => StaffRole::Doctor,
                'specialization' => 'Anak',
                'license_number' => 'STR-1234567890',
                'is_active' => true,
            ]
        );

        // ── Staff: Bidan ─────────────────────────────────────────────
        $midwifeUser = User::firstOrCreate(
            ['email' => 'bidan.sari@kliniksehatbunda.co.id'],
            [
                'name' => 'Bidan Sari Wulandari',
                'password' => Hash::make(Str::random(12)),
                'email_verified_at' => now(),
                'role' => 'parent',
                'tenant_id' => $clinic->id,
            ]
        );

        Staff::firstOrCreate(
            ['user_id' => $midwifeUser->id, 'tenant_id' => $clinic->id],
            [
                'staff_role' => StaffRole::Midwife,
                'specialization' => 'Kebidanan',
                'license_number' => 'STR-0987654321',
                'is_active' => true,
            ]
        );

        // ── Staff: Perawat ───────────────────────────────────────────
        $nurseUser = User::firstOrCreate(
            ['email' => 'perawat.maya@kliniksehatbunda.co.id'],
            [
                'name' => 'Maya Putri',
                'password' => Hash::make(Str::random(12)),
                'email_verified_at' => now(),
                'role' => 'parent',
                'tenant_id' => $clinic->id,
            ]
        );

        Staff::firstOrCreate(
            ['user_id' => $nurseUser->id, 'tenant_id' => $clinic->id],
            [
                'staff_role' => StaffRole::Nurse,
                'specialization' => 'Umum',
                'license_number' => 'STR-1122334455',
                'is_active' => true,
            ]
        );

        // ── Parent Users dengan Anak ─────────────────────────────────
        $parent1 = User::firstOrCreate(
            ['email' => 'andi@example.com'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'parent',
            ]
        );

        $child1 = Child::firstOrCreate(
            ['slug' => 'rafa-wijaya'],
            [
                'user_id' => $parent1->id,
                'name' => 'Rafa Wijaya',
                'nickname' => 'Rafa',
                'gender' => 'male',
                'date_of_birth' => '2024-03-10',
                'place_of_birth' => 'Jakarta',
                'blood_type' => 'B',
                'is_public' => false,
            ]
        );

        $parent2 = User::firstOrCreate(
            ['email' => 'sinta@example.com'],
            [
                'name' => 'Sinta Maharani',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'parent',
            ]
        );

        $child2 = Child::firstOrCreate(
            ['slug' => 'luna-maharani'],
            [
                'user_id' => $parent2->id,
                'name' => 'Luna Maharani',
                'nickname' => 'Luna',
                'gender' => 'female',
                'date_of_birth' => '2024-08-22',
                'place_of_birth' => 'Bandung',
                'blood_type' => 'AB',
                'is_public' => false,
            ]
        );

        // ── Patient Links ────────────────────────────────────────────
        PatientLink::firstOrCreate(
            [
                'facility_tenant_id' => $clinic->id,
                'child_id' => $child1->id,
            ],
            [
                'parent_user_id' => $parent1->id,
                'link_code' => strtoupper(Str::random(8)),
                'status' => PatientLinkStatus::Active,
                'permissions' => ['view_timeline', 'view_growth', 'view_health'],
                'linked_at' => now(),
            ]
        );

        PatientLink::firstOrCreate(
            [
                'facility_tenant_id' => $clinic->id,
                'child_id' => $child2->id,
            ],
            [
                'parent_user_id' => $parent2->id,
                'link_code' => strtoupper(Str::random(8)),
                'status' => PatientLinkStatus::Active,
                'permissions' => ['view_timeline', 'view_growth', 'view_health', 'view_documents'],
                'linked_at' => now(),
            ]
        );

        // ── Clinical Notes (contoh) ─────────────────────────────────
        ClinicalNote::firstOrCreate(
            [
                'tenant_id' => $clinic->id,
                'child_id' => $child1->id,
                'staff_user_id' => $doctorUser->id,
            ],
            [
                'type' => 'consultation',
                'title' => 'Konsultasi Pertumbuhan',
                'content' => 'Rafa dalam kondisi sehat. Pertumbuhan sesuai grafik. Imunisasi lengkap.',
                'vitals' => [
                    'weight' => 8.5,
                    'height' => 72.0,
                    'temperature' => 36.5,
                ],
                'diagnosis' => 'Pertumbuhan normal',
                'medications' => [],
            ]
        );

        ClinicalNote::firstOrCreate(
            [
                'tenant_id' => $clinic->id,
                'child_id' => $child2->id,
                'staff_user_id' => $midwifeUser->id,
            ],
            [
                'type' => 'examination',
                'title' => 'Pemeriksaan Rutin',
                'content' => 'Luna sehat. Berat badan naik sesuai ekspektasi. Tidak ada keluhan.',
                'vitals' => [
                    'weight' => 7.2,
                    'height' => 68.0,
                    'temperature' => 36.4,
                ],
                'diagnosis' => 'Sehat',
                'medications' => [],
            ]
        );

        if ($this->command) {
            $this->command->info('✅ FamilyAndFacilitySeeder: B2C Family & B2B Facility data seeded.');
        }
    }
}
