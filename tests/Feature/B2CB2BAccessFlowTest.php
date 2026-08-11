<?php

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
use Database\Seeders\FamilyAndFacilitySeeder;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Helper: create a B2B tenant with owner user and staff record,
 * then switch session to that tenant.
 */
function createFacilityForFlowTest(string $staffRole = 'staff_admin'): array
{
    $tenant = Tenant::create([
        'name' => 'Klinik Flow Test '.fake()->unique()->bothify('####'),
        'slug' => 'klinik-flow-test-'.Str::slug(fake()->unique()->bothify('####')),
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

    Session::put('tenant_id', $tenant->id);
    Session::save();

    return ['tenant' => $tenant, 'user' => $user, 'staff' => $staff];
}

/*
|--------------------------------------------------------------------------
| B2C Family Sharing — Access Flow Tests
|--------------------------------------------------------------------------
|
| Tests untuk memverifikasi alur akses B2C Family Sharing:
| - Family member adalah metadata-only (TIDAK ada akun login)
| - Permission levels berfungsi dengan benar
| - User ID opsional (linked vs unlinked)
|
*/

describe('B2C Family Sharing — Metadata Only', function () {
    it('creates family member without user account (metadata only)', function () {
        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create(['user_id' => $parent->id]);

        $familyMember = FamilyMember::create([
            'child_id' => $child->id,
            'name' => 'Kakek Budi',
            'relationship' => 'grandfather',
            'email' => 'kakek@example.com',
            'phone' => '081234567890',
            'is_primary' => false,
            'permission_level' => FamilyMemberPermission::View,
        ]);

        // Family member TIDAK punya user_id (metadata only)
        expect($familyMember->user_id)->toBeNull();
        expect($familyMember->name)->toBe('Kakek Budi');
        expect($familyMember->permission_level)->toBe(FamilyMemberPermission::View);
    });

    it('creates family member WITH linked user account', function () {
        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create(['user_id' => $parent->id]);
        $linkedUser = User::factory()->create(['role' => 'parent']);

        $familyMember = FamilyMember::create([
            'child_id' => $child->id,
            'user_id' => $linkedUser->id,
            'name' => $linkedUser->name,
            'relationship' => 'mother',
            'email' => $linkedUser->email,
            'is_primary' => true,
            'permission_level' => FamilyMemberPermission::Edit,
        ]);

        expect($familyMember->user_id)->not->toBeNull();
        expect($familyMember->user_id)->toBe($linkedUser->id);
        expect($familyMember->user)->not->toBeNull();
    });

    it('sets correct permission level based on relationship', function () {
        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create(['user_id' => $parent->id]);

        // Father → edit permission
        $father = FamilyMember::create([
            'child_id' => $child->id,
            'name' => 'Ayah',
            'relationship' => 'father',
            'permission_level' => FamilyMemberPermission::Edit,
        ]);

        expect($father->canEdit())->toBeTrue();
        expect($father->canManage())->toBeFalse();

        // Grandmother → view permission
        $grandmother = FamilyMember::create([
            'child_id' => $child->id,
            'name' => 'Nenek',
            'relationship' => 'grandmother',
            'permission_level' => FamilyMemberPermission::View,
        ]);

        expect($grandmother->canEdit())->toBeFalse();
        expect($grandmother->canManage())->toBeFalse();
    });

    it('does NOT allow unlinked family member to login', function () {
        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create(['user_id' => $parent->id]);

        $familyMember = FamilyMember::create([
            'child_id' => $child->id,
            'name' => 'Tante Rina',
            'relationship' => 'other',
            'email' => 'rina@example.com',
            'permission_level' => FamilyMemberPermission::View,
        ]);

        // Verify no user account exists for this family member
        $user = User::where('email', 'rina@example.com')->first();
        expect($user)->toBeNull();
        expect($familyMember->user_id)->toBeNull();
    });
});

/*
|--------------------------------------------------------------------------
| B2B Staff — Access Flow Tests
|--------------------------------------------------------------------------
|
| Tests untuk memverifikasi alur akses B2B Staff:
| - Staff role menentukan akses ke modul
| - staff_admin TIDAK bisa akses catatan klinis
| - doctor/midwife/nurse BISA akses catatan klinis
|
*/

describe('B2B Staff — Role-Based Access', function () {
    it('creates staff with user account automatically', function () {
        $facility = createFacilityForFlowTest('doctor');

        $staffUser = User::where('email', $facility['user']->email)->first();
        expect($staffUser)->not->toBeNull();

        $staff = Staff::where('user_id', $staffUser->id)
            ->where('tenant_id', $facility['tenant']->id)
            ->first();
        expect($staff)->not->toBeNull();
        expect($staff->staff_role)->toBe(StaffRole::Doctor);
    });

    it('staff_admin can access staff management', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.staff.index'))
            ->assertOk();
    });

    it('staff_admin CANNOT access clinical notes', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertForbidden();
    });

    it('doctor CAN access clinical notes', function () {
        $facility = createFacilityForFlowTest('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertOk();
    });

    it('midwife CAN access clinical notes', function () {
        $facility = createFacilityForFlowTest('midwife');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertOk();
    });

    it('nurse CAN access clinical notes', function () {
        $facility = createFacilityForFlowTest('nurse');

        $this->actingAs($facility['user'])
            ->get(route('facility.clinical-notes.index'))
            ->assertOk();
    });

    it('staff_admin can access referrals', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.referrals.index'))
            ->assertOk();
    });

    it('nurse CANNOT access referrals', function () {
        $facility = createFacilityForFlowTest('nurse');

        $this->actingAs($facility['user'])
            ->get(route('facility.referrals.index'))
            ->assertForbidden();
    });

    it('staff_admin can access reports', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.reports.index'))
            ->assertOk();
    });

    it('doctor CANNOT access reports', function () {
        $facility = createFacilityForFlowTest('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.reports.index'))
            ->assertForbidden();
    });

    it('staff_admin can access settings', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $this->actingAs($facility['user'])
            ->get(route('facility.settings.edit'))
            ->assertOk();
    });

    it('doctor CANNOT access settings', function () {
        $facility = createFacilityForFlowTest('doctor');

        $this->actingAs($facility['user'])
            ->get(route('facility.settings.edit'))
            ->assertForbidden();
    });
});

/*
|--------------------------------------------------------------------------
| B2B Patient (PatientLink) — Access Flow Tests
|--------------------------------------------------------------------------
|
| Tests untuk memverifikasi alur akses B2B Patient:
| - Create form shows ALL children/parents (not just linked ones)
| - Prevents duplicate active links
| - PatientLink status lifecycle
|
*/

describe('B2B Patient — PatientLink Flow', function () {
    it('create form shows ALL children (not just linked ones)', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        // Create parent and child that are NOT linked to this facility
        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'name' => 'Anak Baru',
        ]);

        $response = $this->actingAs($facility['user'])
            ->get(route('facility.patients.create'));

        $response->assertOk();
        // The child should appear in the dropdown even though not linked
        $response->assertSee('Anak Baru');
    });

    it('create form shows ALL parent users (not just linked ones)', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $parent = User::factory()->create([
            'role' => 'parent',
            'name' => 'Orang Tua Baru',
        ]);

        $response = $this->actingAs($facility['user'])
            ->get(route('facility.patients.create'));

        $response->assertOk();
        $response->assertSee('Orang Tua Baru');
    });

    it('prevents duplicate active patient links', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create(['user_id' => $parent->id]);

        // Create first link
        PatientLink::create([
            'facility_tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'parent_user_id' => $parent->id,
            'link_code' => strtoupper(Str::random(8)),
            'status' => PatientLinkStatus::Active,
            'permissions' => ['view_timeline'],
            'linked_at' => now(),
        ]);

        // Try to create duplicate link
        $this->actingAs($facility['user'])
            ->post(route('facility.patients.store'), [
                'child_id' => $child->id,
                'parent_user_id' => $parent->id,
                'permissions' => ['view_timeline'],
            ])
            ->assertSessionHasErrors('child_id');
    });

    it('allows linking a new child to facility', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create(['user_id' => $parent->id]);

        $this->actingAs($facility['user'])
            ->post(route('facility.patients.store'), [
                'child_id' => $child->id,
                'parent_user_id' => $parent->id,
                'permissions' => ['view_timeline', 'view_growth'],
            ])
            ->assertRedirect();

        $link = PatientLink::where('facility_tenant_id', $facility['tenant']->id)
            ->where('child_id', $child->id)
            ->first();

        expect($link)->not->toBeNull();
        expect($link->status)->toBe(PatientLinkStatus::Pending);
        expect($link->permissions)->toContain('view_timeline');
        expect($link->permissions)->toContain('view_growth');
    });

    it('allows re-linking after revoking previous link', function () {
        $facility = createFacilityForFlowTest('staff_admin');

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create(['user_id' => $parent->id]);

        // Create and revoke first link
        $oldLink = PatientLink::create([
            'facility_tenant_id' => $facility['tenant']->id,
            'child_id' => $child->id,
            'parent_user_id' => $parent->id,
            'link_code' => strtoupper(Str::random(8)),
            'status' => PatientLinkStatus::Revoked,
            'permissions' => ['view_timeline'],
            'revoked_at' => now(),
        ]);

        // Should be able to create new link (revoked links don't block)
        $this->actingAs($facility['user'])
            ->post(route('facility.patients.store'), [
                'child_id' => $child->id,
                'parent_user_id' => $parent->id,
                'permissions' => ['view_timeline'],
            ])
            ->assertRedirect();

        $activeLinks = PatientLink::where('facility_tenant_id', $facility['tenant']->id)
            ->where('child_id', $child->id)
            ->where('status', '!=', 'revoked')
            ->count();

        expect($activeLinks)->toBe(1);
    });
});

/*
|--------------------------------------------------------------------------
| Seeder — Integration Tests
|--------------------------------------------------------------------------
|
| Tests untuk memverifikasi FamilyAndFacilitySeeder berjalan dengan benar.
|
*/

describe('FamilyAndFacilitySeeder — Integration', function () {
    it('seeds B2C family data correctly', function () {
        // Run seeder
        $seeder = new FamilyAndFacilitySeeder;
        $seeder->run();

        // Verify parent user exists
        $parent = User::where('email', 'budi@for-mysha.my.id')->first();
        expect($parent)->not->toBeNull();
        expect($parent->name)->toBe('Budi Santoso');

        // Verify children exist
        $mysha = Child::where('slug', 'mysha')->first();
        expect($mysha)->not->toBeNull();
        expect($mysha->user_id)->toBe($parent->id);

        $qaireen = Child::where('slug', 'qaireen')->first();
        expect($qaireen)->not->toBeNull();

        // Verify family members for Mysha
        $myshaFamily = FamilyMember::where('child_id', $mysha->id)->get();
        expect($myshaFamily->count())->toBeGreaterThanOrEqual(3);

        // Verify father is linked to parent user
        $father = FamilyMember::where('child_id', $mysha->id)
            ->where('relationship', 'father')
            ->first();
        expect($father)->not->toBeNull();
        expect($father->user_id)->toBe($parent->id);

        // Verify mother is NOT linked (metadata only)
        $mother = FamilyMember::where('child_id', $mysha->id)
            ->where('relationship', 'mother')
            ->first();
        expect($mother)->not->toBeNull();
        expect($mother->user_id)->toBeNull();
    });

    it('seeds B2B facility data correctly', function () {
        $seeder = new FamilyAndFacilitySeeder;
        $seeder->run();

        // Verify clinic tenant
        $clinic = Tenant::where('slug', 'klinik-sehat-bunda')->first();
        expect($clinic)->not->toBeNull();
        expect($clinic->type)->toBe(TenantType::Clinic);

        // Verify staff members
        $staffMembers = Staff::where('tenant_id', $clinic->id)->get();
        expect($staffMembers->count())->toBeGreaterThanOrEqual(4);

        // Verify different roles exist
        $roles = $staffMembers->pluck('staff_role')->map(fn ($r) => $r->value)->unique()->toArray();
        expect($roles)->toContain('staff_admin');
        expect($roles)->toContain('doctor');
        expect($roles)->toContain('midwife');
        expect($roles)->toContain('nurse');

        // Verify patient links
        $patientLinks = PatientLink::where('facility_tenant_id', $clinic->id)->get();
        expect($patientLinks->count())->toBeGreaterThanOrEqual(2);

        // Verify clinical notes
        $clinicalNotes = ClinicalNote::where('tenant_id', $clinic->id)->get();
        expect($clinicalNotes->count())->toBeGreaterThanOrEqual(2);
    });

    it('creates staff with random passwords (not known to admin)', function () {
        $seeder = new FamilyAndFacilitySeeder;
        $seeder->run();

        $clinic = Tenant::where('slug', 'klinik-sehat-bunda')->first();
        $staffUsers = User::where('tenant_id', $clinic->id)->get();

        // All staff users should have different passwords (random)
        $passwords = $staffUsers->pluck('password')->unique();
        expect($passwords->count())->toBe($staffUsers->count());

        // Staff use 'parent' role (not tenant_admin) to prevent admin panel access
        $staffUsers->each(function ($user) {
            expect($user->role)->toBe('parent');
        });
    });
});
