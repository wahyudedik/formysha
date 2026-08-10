<?php

use App\Enums\ReferralStatus;
use App\Enums\TenantType;
use App\Models\Child;
use App\Models\ClinicalNote;
use App\Models\PatientLink;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Helper: create a B2B tenant with owner user and staff record,
 * then switch session to that tenant.
 *
 * @return array{tenant: Tenant, user: User, staff: Staff}
 */
function createFacilityWithStaff(string $staffRole = 'staff_admin'): array
{
    $tenant = Tenant::create([
        'name' => 'Klinik Test '.fake()->unique()->bothify('####'),
        'slug' => 'klinik-test-'.Str::slug(fake()->unique()->bothify('####')),
        'type' => TenantType::Clinic,
        'is_active' => true,
    ]);

    $user = User::factory()->tenantAdmin()->create([
        'tenant_id' => $tenant->id,
    ]);

    $staff = Staff::create([
        'user_id' => $user->id,
        'tenant_id' => $tenant->id,
        'staff_role' => $staffRole,
        'is_active' => true,
    ]);

    // Set tenant in session for TenantService
    Session::put('tenant_id', $tenant->id);
    Session::save();

    return ['tenant' => $tenant, 'user' => $user, 'staff' => $staff];
}

describe('Facility Admin — Dashboard', function () {
    it('allows staff admin to view dashboard', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.dashboard'))
            ->assertOk();
    });

    it('allows doctor to view dashboard', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.dashboard'))
            ->assertOk();
    });

    it('redirects unauthenticated user to login', function () {
        $this->get(route('facility.dashboard'))
            ->assertRedirect('/login');
    });
});

describe('Facility Admin — Staff Management', function () {
    it('allows staff admin to view staff list', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.staff.index'))
            ->assertOk();
    });

    it('allows doctor to view staff list', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.staff.index'))
            ->assertOk();
    });

    it('allows staff admin to view create staff form', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.staff.create'))
            ->assertOk();
    });

    it('allows staff admin to create a new staff member', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->post(route('facility.staff.store'), [
                'name' => 'Dr. Baru',
                'email' => fake()->unique()->safeEmail(),
                'staff_role' => 'doctor',
                'specialization' => 'Anak',
                'license_number' => 'STR-12345',
            ])
            ->assertRedirect(route('facility.staff.index'));

        $staffUser = User::where('name', 'Dr. Baru')->first();
        expect($staffUser)->not->toBeNull();
        expect($staffUser->role)->toBe('parent');
        expect($staffUser->tenant_id)->toBe($facility['tenant']->id);

        $staffRecord = Staff::where('user_id', $staffUser->id)->first();
        expect($staffRecord)->not->toBeNull();
        expect($staffRecord->staff_role->value)->toBe('doctor');
    });

    it('validates required fields when creating staff', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->post(route('facility.staff.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'staff_role']);
    });

    it('validates unique email when creating staff', function () {
        $facility = createFacilityWithStaff('staff_admin');
        $existingEmail = fake()->unique()->safeEmail();
        User::factory()->create(['email' => $existingEmail]);

        $this->actingAs($facility['user'])
            ->post(route('facility.staff.store'), [
                'name' => 'Dr. Duplikat',
                'email' => $existingEmail,
                'staff_role' => 'doctor',
            ])
            ->assertSessionHasErrors('email');
    });

    it('allows staff admin to view staff detail', function () {
        $facility = createFacilityWithStaff('staff_admin');
        $staff = Staff::where('tenant_id', $facility['tenant']->id)->first();

        $this->actingAs($facility['user'])
            ->get(route('facility.staff.show', $staff))
            ->assertOk();
    });

    it('allows staff admin to update staff', function () {
        $facility = createFacilityWithStaff('staff_admin');
        $staff = Staff::where('tenant_id', $facility['tenant']->id)
            ->where('staff_role', 'staff_admin')
            ->first();

        $this->actingAs($facility['user'])
            ->put(route('facility.staff.update', $staff), [
                'staff_role' => 'doctor',
                'specialization' => 'Umum',
                'is_active' => true,
            ])
            ->assertRedirect(route('facility.staff.show', $staff));

        $staff->refresh();
        expect($staff->staff_role->value)->toBe('doctor');
        expect($staff->specialization)->toBe('Umum');
    });

    it('allows staff admin to deactivate staff', function () {
        $facility = createFacilityWithStaff('staff_admin');

        // Create another staff member to deactivate
        $otherUser = User::factory()->create([
            'tenant_id' => $facility['tenant']->id,
            'role' => 'tenant_admin',
        ]);
        $otherStaff = Staff::create([
            'user_id' => $otherUser->id,
            'tenant_id' => $facility['tenant']->id,
            'staff_role' => 'nurse',
            'is_active' => true,
        ]);

        $this->actingAs($facility['user'])
            ->delete(route('facility.staff.destroy', $otherStaff))
            ->assertRedirect(route('facility.staff.index'));

        $otherStaff->refresh();
        expect($otherStaff->is_active)->toBeFalse();
    });

    it('prevents nurse from accessing staff management', function () {
        $facility = createFacilityWithStaff('nurse');

        $this->actingAs($facility['user'])
            ->get(route('facility.staff.index'))
            ->assertForbidden();
    });
});

describe('Facility Admin — Patient Links', function () {
    it('allows staff admin to view patient list', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.patients.index'))
            ->assertOk();
    });

    it('allows doctor to view patient list', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.patients.index'))
            ->assertOk();
    });

    it('allows midwife to view patient list', function () {
        $facility = createFacilityWithStaff('midwife');

        $this->actingAs($facility['user'])
            ->get(route('facility.patients.index'))
            ->assertOk();
    });

    it('allows nurse to view patient list', function () {
        $facility = createFacilityWithStaff('nurse');

        $this->actingAs($facility['user'])
            ->get(route('facility.patients.index'))
            ->assertOk();
    });

    it('allows staff admin to view create patient form', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.patients.create'))
            ->assertOk();
    });

    it('allows staff admin to create patient link', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.patients.store'), [
                'child_id' => $child->id,
                'parent_user_id' => $parent->id,
                'permissions' => ['view_timeline', 'view_growth'],
            ])
            ->assertRedirect();

        $link = PatientLink::where('facility_tenant_id', $facility['tenant']->id)->first();
        expect($link)->not->toBeNull();
        expect($link->child_id)->toBe($child->id);
        expect($link->parent_user_id)->toBe($parent->id);
        expect($link->link_code)->not->toBeEmpty();
    });

    it('validates required fields when creating patient link', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->post(route('facility.patients.store'), [])
            ->assertSessionHasErrors(['child_id', 'parent_user_id']);
    });

    it('allows staff admin to view patient detail', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);
        $link = PatientLink::create([
            'facility_tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'parent_user_id' => $parent->id,
        ]);

        $this->actingAs($facility['user'])
            ->get(route('facility.patients.show', $link))
            ->assertOk();
    });

    it('allows staff admin to revoke patient link', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);
        $link = PatientLink::create([
            'facility_tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'parent_user_id' => $parent->id,
        ]);

        $this->actingAs($facility['user'])
            ->delete(route('facility.patients.destroy', $link))
            ->assertRedirect(route('facility.patients.index'));

        $link->refresh();
        expect($link->status->value)->toBe('revoked');
        expect($link->revoked_at)->not->toBeNull();
    });

    it('prevents staff from accessing other facility patients', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $otherTenant = Tenant::create([
            'name' => 'Other Facility',
            'slug' => 'other-facility-'.fake()->unique()->bothify('####'),
            'type' => TenantType::Clinic,
            'is_active' => true,
        ]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $otherTenant->id,
        ]);
        $link = PatientLink::create([
            'facility_tenant_id' => $otherTenant->id,
            'child_id' => $child->id,
            'parent_user_id' => $parent->id,
        ]);

        $this->actingAs($facility['user'])
            ->get(route('facility.patients.show', $link))
            ->assertForbidden();
    });
});

describe('Facility Admin — Clinical Notes', function () {
    it('allows doctor to view clinical notes list', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertOk();
    });

    it('allows midwife to view clinical notes list', function () {
        $facility = createFacilityWithStaff('midwife');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertOk();
    });

    it('allows nurse to view clinical notes list', function () {
        $facility = createFacilityWithStaff('nurse');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertOk();
    });

    it('prevents staff_admin from accessing clinical notes', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertForbidden();
    });

    it('allows doctor to view create clinical note form', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.create'))
            ->assertOk();
    });

    it('allows doctor to create clinical note', function () {
        $facility = createFacilityWithStaff('doctor');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.clinical-notes.store'), [
                'child_id' => $child->id,
                'staff_user_id' => $facility['user']->id,
                'type' => 'consultation',
                'title' => 'Konsultasi Pertama',
                'content' => 'Pasien datang dengan keluhan demam.',
                'diagnosis' => 'Demam berdarah ringan',
            ])
            ->assertRedirect();

        $note = ClinicalNote::where('tenant_id', $facility['tenant']->id)->first();
        expect($note)->not->toBeNull();
        expect($note->title)->toBe('Konsultasi Pertama');
        expect($note->type->value)->toBe('consultation');
    });

    it('validates required fields when creating clinical note', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->post(route('facility.clinical-notes.store'), [])
            ->assertSessionHasErrors(['child_id', 'staff_user_id', 'type', 'title', 'content']);
    });

    it('allows doctor to view clinical note detail', function () {
        $facility = createFacilityWithStaff('doctor');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);
        $note = ClinicalNote::create([
            'tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'staff_user_id' => $facility['user']->id,
            'type' => 'examination',
            'title' => 'Pemeriksaan Rutin',
            'content' => 'Hasil pemeriksaan normal.',
        ]);

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.show', $note))
            ->assertOk();
    });

    it('allows doctor to update clinical note', function () {
        $facility = createFacilityWithStaff('doctor');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);
        $note = ClinicalNote::create([
            'tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'staff_user_id' => $facility['user']->id,
            'type' => 'consultation',
            'title' => 'Judul Lama',
            'content' => 'Isi lama.',
        ]);

        $this->actingAs($facility['user'])
            ->put(route('facility.clinical-notes.update', $note), [
                'type' => 'treatment',
                'title' => 'Judul Baru',
                'content' => 'Isi baru.',
            ])
            ->assertRedirect(route('facility.clinical-notes.show', $note));

        $note->refresh();
        expect($note->title)->toBe('Judul Baru');
        expect($note->type->value)->toBe('treatment');
    });

    it('allows doctor to delete clinical note', function () {
        $facility = createFacilityWithStaff('doctor');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);
        $note = ClinicalNote::create([
            'tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'staff_user_id' => $facility['user']->id,
            'type' => 'consultation',
            'title' => 'Catatan Dihapus',
            'content' => 'Isi.',
        ]);

        $this->actingAs($facility['user'])
            ->delete(route('facility.clinical-notes.destroy', $note))
            ->assertRedirect(route('facility.clinical-notes.index'));

        $this->assertDatabaseMissing('clinical_notes', ['id' => $note->id]);
    });

    it('prevents staff from accessing other facility clinical notes', function () {
        $facility = createFacilityWithStaff('doctor');

        $otherTenant = Tenant::create([
            'name' => 'Other Facility',
            'slug' => 'other-facility-'.fake()->unique()->bothify('####'),
            'type' => TenantType::Clinic,
            'is_active' => true,
        ]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $otherTenant->id,
        ]);
        $note = ClinicalNote::create([
            'tenant_id' => $otherTenant->id,
            'child_id' => $child->id,
            'staff_user_id' => $facility['user']->id,
            'type' => 'consultation',
            'title' => 'Catatan Lain',
            'content' => 'Isi.',
        ]);

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.show', $note))
            ->assertForbidden();
    });
});

describe('Facility Admin — Referrals', function () {
    it('allows doctor to view referrals list', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.referrals.index'))
            ->assertOk();
    });

    it('allows midwife to view referrals list', function () {
        $facility = createFacilityWithStaff('midwife');

        $this->actingAs($facility['user'])
            ->get(route('facility.referrals.index'))
            ->assertOk();
    });

    it('allows staff admin to view referrals list', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.referrals.index'))
            ->assertOk();
    });

    it('prevents nurse from accessing referrals', function () {
        $facility = createFacilityWithStaff('nurse');

        $this->actingAs($facility['user'])
            ->get(route('facility.referrals.index'))
            ->assertForbidden();
    });

    it('allows doctor to create referral', function () {
        $facility = createFacilityWithStaff('doctor');

        $otherTenant = Tenant::create([
            'name' => 'RS Tujuan',
            'slug' => 'rs-tujuan-'.fake()->unique()->bothify('####'),
            'type' => TenantType::Hospital,
            'is_active' => true,
        ]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.referrals.store'), [
                'child_id' => $child->id,
                'to_tenant_id' => $otherTenant->id,
                'reason' => 'Perlu penanganan lebih lanjut',
                'clinical_summary' => 'Pasien dengan gejala berat',
                'notes' => 'Mohon segera ditangani',
            ])
            ->assertRedirect();

        $referral = Referral::where('from_tenant_id', $facility['tenant']->id)->first();
        expect($referral)->not->toBeNull();
        expect($referral->to_tenant_id)->toBe($otherTenant->id);
        expect($referral->reason)->toBe('Perlu penanganan lebih lanjut');
        expect($referral->status->value)->toBe('pending');
    });

    it('validates required fields when creating referral', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->post(route('facility.referrals.store'), [])
            ->assertSessionHasErrors(['child_id', 'to_tenant_id', 'reason']);
    });

    it('allows staff admin to accept incoming referral', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $otherTenant = Tenant::create([
            'name' => 'RS Pengirim',
            'slug' => 'rs-pengirim-'.fake()->unique()->bothify('####'),
            'type' => TenantType::Hospital,
            'is_active' => true,
        ]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $otherTenant->id,
        ]);

        $referral = Referral::create([
            'from_tenant_id' => $otherTenant->id,
            'to_tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'referring_staff_id' => $facility['user']->id,
            'reason' => 'Rujukan dari RS lain',
            'status' => ReferralStatus::Pending,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.referrals.accept', $referral))
            ->assertRedirect(route('facility.referrals.show', $referral));

        $referral->refresh();
        expect($referral->status->value)->toBe('accepted');
    });

    it('allows staff admin to complete referral', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $otherTenant = Tenant::create([
            'name' => 'RS Lain',
            'slug' => 'rs-lain-'.fake()->unique()->bothify('####'),
            'type' => TenantType::Hospital,
            'is_active' => true,
        ]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);

        $referral = Referral::create([
            'from_tenant_id' => $facility['tenant']->id,
            'to_tenant_id' => $otherTenant->id,
            'child_id' => $child->id,
            'referring_staff_id' => $facility['user']->id,
            'reason' => 'Rujukan keluar',
            'status' => ReferralStatus::Pending,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.referrals.complete', $referral))
            ->assertRedirect(route('facility.referrals.show', $referral));

        $referral->refresh();
        expect($referral->status->value)->toBe('completed');
    });

    it('allows staff admin to cancel referral', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $otherTenant = Tenant::create([
            'name' => 'RS Batal',
            'slug' => 'rs-batal-'.fake()->unique()->bothify('####'),
            'type' => TenantType::Hospital,
            'is_active' => true,
        ]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);

        $referral = Referral::create([
            'from_tenant_id' => $facility['tenant']->id,
            'to_tenant_id' => $otherTenant->id,
            'child_id' => $child->id,
            'referring_staff_id' => $facility['user']->id,
            'reason' => 'Rujukan batal',
            'status' => ReferralStatus::Pending,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.referrals.cancel', $referral))
            ->assertRedirect(route('facility.referrals.show', $referral));

        $referral->refresh();
        expect($referral->status->value)->toBe('cancelled');
    });

    it('prevents staff from accessing other facility referrals', function () {
        $facility = createFacilityWithStaff('doctor');

        $otherTenant = Tenant::create([
            'name' => 'RS Asing',
            'slug' => 'rs-asing-'.fake()->unique()->bothify('####'),
            'type' => TenantType::Hospital,
            'is_active' => true,
        ]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $otherTenant->id,
        ]);

        $referral = Referral::create([
            'from_tenant_id' => $otherTenant->id,
            'to_tenant_id' => $otherTenant->id,
            'child_id' => $child->id,
            'referring_staff_id' => $facility['user']->id,
            'reason' => 'Rujukan asing',
            'status' => ReferralStatus::Pending,
        ]);

        $this->actingAs($facility['user'])
            ->get(route('facility.referrals.show', $referral))
            ->assertForbidden();
    });
});

describe('Facility Admin — Settings', function () {
    it('allows staff admin to view settings', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.settings.edit'))
            ->assertOk();
    });

    it('prevents doctor from accessing settings', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.settings.edit'))
            ->assertForbidden();
    });

    it('allows staff admin to update facility settings', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->put(route('facility.settings.update'), [
                'name' => 'Klinik Updated',
                'address' => 'Jl. Baru No. 99',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12345',
                'phone' => '021-1234567',
                'email_institution' => 'info@klinik.com',
                'website' => 'https://klinik.com',
                'license_number' => 'LIC-999',
                'description' => 'Deskripsi updated',
            ])
            ->assertRedirect(route('facility.settings.edit'));

        $facility['tenant']->refresh();
        expect($facility['tenant']->name)->toBe('Klinik Updated');
        expect($facility['tenant']->address)->toBe('Jl. Baru No. 99');
    });

    it('validates required fields when updating settings', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->put(route('facility.settings.update'), [])
            ->assertSessionHasErrors('name');
    });
});

describe('Facility Admin — Reports', function () {
    it('allows staff admin to view reports overview', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.reports.index'))
            ->assertOk();
    });

    it('prevents doctor from accessing reports', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.reports.index'))
            ->assertForbidden();
    });

    it('allows staff admin to view clinical notes report', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.reports.clinical-notes'))
            ->assertOk();
    });

    it('allows staff admin to view patients report', function () {
        $facility = createFacilityWithStaff('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.reports.patients'))
            ->assertOk();
    });

    it('prevents doctor from accessing clinical notes report', function () {
        $facility = createFacilityWithStaff('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.reports.clinical-notes'))
            ->assertForbidden();
    });
});
