<?php

use App\Enums\TenantType;
use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\Media;
use App\Models\PatientLink;
use App\Models\Plan;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

/**
 * Helper: create a tenant with an active subscription on a plan whose
 * B2C limits can be overridden per test.
 *
 * @return array{tenant: Tenant, plan: Plan}
 */
function createEnforcementTenant(array $planOverrides = []): array
{
    $tenant = Tenant::create([
        'name' => 'Keluarga Limit '.fake()->unique()->bothify('####'),
        'slug' => 'keluarga-limit-'.fake()->unique()->bothify('####'),
        'is_active' => true,
    ]);

    $plan = Plan::create(array_merge([
        'name' => 'Paket Limit '.fake()->unique()->bothify('####'),
        'slug' => 'paket-limit-'.fake()->unique()->bothify('####'),
        'price_monthly' => 0,
        'max_children' => -1,
        'max_photos' => -1,
        'max_videos' => -1,
        'max_storage_mb' => -1,
        'max_family_members' => -1,
        'is_active' => true,
        'sort_order' => 99,
    ], $planOverrides));

    Subscription::create([
        'tenant_id' => $tenant->id,
        'plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now(),
        'ends_at' => null,
    ]);

    return ['tenant' => $tenant, 'plan' => $plan];
}

/**
 * Helper: create a B2B facility tenant with an unlimited-by-default plan
 * whose feature limits can be overridden per test.
 *
 * @return array{tenant: Tenant, plan: Plan, user: User, staff: Staff}
 */
function createEnforcementFacility(array $featureOverrides = []): array
{
    $tenant = Tenant::create([
        'name' => 'Klinik Limit '.fake()->unique()->bothify('####'),
        'slug' => 'klinik-limit-'.fake()->unique()->bothify('####'),
        'type' => TenantType::Clinic,
        'is_active' => true,
    ]);

    $plan = Plan::create([
        'name' => 'B2B Limit '.fake()->unique()->bothify('####'),
        'slug' => 'b2b-limit-'.fake()->unique()->bothify('####'),
        'price_monthly' => 0,
        'features' => array_merge([
            'max_staff' => -1,
            'max_patients' => -1,
            'max_clinical_notes' => -1,
            'referrals_enabled' => true,
        ], $featureOverrides),
        'is_active' => true,
        'sort_order' => 99,
    ]);

    Subscription::create([
        'tenant_id' => $tenant->id,
        'plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now(),
        'ends_at' => null,
    ]);

    $user = User::factory()->tenantAdmin()->create([
        'tenant_id' => $tenant->id,
    ]);

    $staff = Staff::create([
        'user_id' => $user->id,
        'tenant_id' => $tenant->id,
        'staff_role' => 'staff_admin',
        'is_active' => true,
    ]);

    Session::put('tenant_id', $tenant->id);
    Session::save();

    return ['tenant' => $tenant, 'plan' => $plan, 'user' => $user, 'staff' => $staff];
}

describe('Feature Limit Enforcement — Media Service (Web)', function () {
    it('rejects photo upload beyond max_photos and stores nothing', function () {
        Storage::fake('private');

        ['tenant' => $tenant] = createEnforcementTenant(['max_photos' => 1]);

        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        // Fill the single photo slot.
        Media::create([
            'mediable_type' => Timeline::class,
            'mediable_id' => $timeline->id,
            'file_path' => 'media/existing.jpg',
            'file_name' => 'existing.jpg',
            'file_type' => 'photo',
            'file_size' => 1024,
        ]);

        $file = UploadedFile::fake()->image('over-limit.jpg', 800, 600)->size(512);

        actingAs($user)
            ->post(route('media.store.timeline', [$child, $timeline]), [
                'media' => [$file],
            ])
            ->assertSessionHasErrors('media');

        // The rejected file must not be persisted anywhere.
        expect(Media::count())->toBe(1);
        expect(Storage::disk('private')->allFiles('media'))->toBe([]);
    });

    it('rejects video upload beyond max_videos and stores nothing', function () {
        Storage::fake('private');

        ['tenant' => $tenant] = createEnforcementTenant(['max_videos' => 1]);

        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        // Fill the single video slot.
        Media::create([
            'mediable_type' => Timeline::class,
            'mediable_id' => $timeline->id,
            'file_path' => 'media/existing.mp4',
            'file_name' => 'existing.mp4',
            'file_type' => 'video',
            'file_size' => 1024,
        ]);

        $file = UploadedFile::fake()->create('over-limit.mp4', 5120, 'video/mp4');

        actingAs($user)
            ->post(route('media.store.timeline', [$child, $timeline]), [
                'media' => [$file],
            ])
            ->assertSessionHasErrors('media');

        expect(Media::where('file_type', 'video')->count())->toBe(1);
        expect(Storage::disk('private')->allFiles('media'))->toBe([]);
    });

    it('rejects upload beyond max_storage_mb and stores nothing', function () {
        Storage::fake('private');

        // 1 MB storage quota, no photo/video count limits.
        ['tenant' => $tenant] = createEnforcementTenant(['max_storage_mb' => 1]);

        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        // A 2 MB photo exceeds the 1 MB quota.
        $file = UploadedFile::fake()->image('too-big.jpg', 800, 600)->size(2048);

        actingAs($user)
            ->post(route('media.store.timeline', [$child, $timeline]), [
                'media' => [$file],
            ])
            ->assertSessionHasErrors('media');

        expect(Media::count())->toBe(0);
        expect(Storage::disk('private')->allFiles('media'))->toBe([]);
    });

    it('allows upload when plan limits are unlimited (-1)', function () {
        Storage::fake('private');

        ['tenant' => $tenant] = createEnforcementTenant();

        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        $file = UploadedFile::fake()->image('unlimited.jpg', 800, 600)->size(512);

        actingAs($user)
            ->post(route('media.store.timeline', [$child, $timeline]), [
                'media' => [$file],
            ])
            ->assertRedirect(route('timeline.show', [$child, $timeline]));

        assertDatabaseHas('media', [
            'mediable_type' => Timeline::class,
            'mediable_id' => $timeline->id,
            'file_name' => 'unlimited.jpg',
            'file_type' => 'photo',
        ]);
    });

    it('skips enforcement for children without a tenant (backward compatibility)', function () {
        Storage::fake('private');

        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => null]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        $file = UploadedFile::fake()->image('no-tenant.jpg', 800, 600)->size(512);

        actingAs($user)
            ->post(route('media.store.timeline', [$child, $timeline]), [
                'media' => [$file],
            ])
            ->assertRedirect(route('timeline.show', [$child, $timeline]));

        assertDatabaseHas('media', [
            'file_name' => 'no-tenant.jpg',
            'file_type' => 'photo',
        ]);
    });
});

describe('Feature Limit Enforcement — API Children', function () {
    it('returns JSON 422 when creating a child beyond max_children', function () {
        ['tenant' => $tenant] = createEnforcementTenant(['max_children' => 1]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        // Fill the single child slot.
        Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children', [
                'name' => 'Anak Kedua',
                'gender' => 'female',
                'date_of_birth' => '2025-06-15',
            ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false);

        expect(Child::where('name', 'Anak Kedua')->exists())->toBeFalse();
    });

    it('allows creating a child when max_children is unlimited (-1)', function () {
        ['tenant' => $tenant] = createEnforcementTenant(['max_children' => -1]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children', [
                'name' => 'Anak Unlimited',
                'gender' => 'male',
                'date_of_birth' => '2025-06-15',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Anak Unlimited');
    });
});

describe('Feature Limit Enforcement — API Family Members', function () {
    it('returns JSON 422 when creating a family member beyond max_family_members', function () {
        ['tenant' => $tenant] = createEnforcementTenant(['max_family_members' => 1]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);

        // Fill the single family member slot.
        FamilyMember::create([
            'child_id' => $child->id,
            'name' => 'Ayah Satu',
            'relationship' => 'father',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/family-members', [
                'name' => 'Ibu Dua',
                'relationship' => 'mother',
            ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false);

        expect(FamilyMember::where('name', 'Ibu Dua')->exists())->toBeFalse();
    });

    it('allows creating a family member when max_family_members is unlimited (-1)', function () {
        ['tenant' => $tenant] = createEnforcementTenant(['max_family_members' => -1]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/family-members', [
                'name' => 'Kakek Unlimited',
                'relationship' => 'grandfather',
            ]);

        $response->assertCreated();
    });
});

describe('Feature Limit Enforcement — API Media', function () {
    it('returns JSON 422 when uploading a photo beyond max_photos via API', function () {
        Storage::fake('private');

        ['tenant' => $tenant] = createEnforcementTenant(['max_photos' => 1]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;
        $child = Child::factory()->create(['user_id' => $user->id, 'tenant_id' => $tenant->id]);

        // Fill the single photo slot.
        Media::create([
            'mediable_type' => Child::class,
            'mediable_id' => $child->id,
            'file_path' => 'media/existing.jpg',
            'file_name' => 'existing.jpg',
            'file_type' => 'photo',
            'file_size' => 1024,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/media', [
                'file' => UploadedFile::fake()->image('over-limit.jpg', 800, 600)->size(512),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['media']);

        expect(Media::where('file_name', 'over-limit.jpg')->exists())->toBeFalse();
        expect(Storage::disk('private')->allFiles('media'))->toBe([]);
    });
});

describe('Feature Limit Enforcement — B2B Facility Routes', function () {
    it('rejects staff creation beyond max_staff', function () {
        $facility = createEnforcementFacility(['max_staff' => 1]);

        // The helper already created one staff member, filling the slot.
        $this->actingAs($facility['user'])
            ->post(route('facility.staff.store'), [
                'name' => 'Dr. Lebih',
                'email' => fake()->unique()->safeEmail(),
                'staff_role' => 'doctor',
            ])
            ->assertSessionHas('error');

        expect(User::where('name', 'Dr. Lebih')->exists())->toBeFalse();
        expect(Staff::where('tenant_id', $facility['tenant']->id)->count())->toBe(1);
    });

    it('allows staff creation when max_staff is unlimited (-1)', function () {
        $facility = createEnforcementFacility(['max_staff' => -1]);

        $this->actingAs($facility['user'])
            ->post(route('facility.staff.store'), [
                'name' => 'Dr. Unlimited',
                'email' => fake()->unique()->safeEmail(),
                'staff_role' => 'doctor',
            ])
            ->assertRedirect(route('facility.staff.index'));

        expect(User::where('name', 'Dr. Unlimited')->exists())->toBeTrue();
    });

    it('rejects patient link creation beyond max_patients', function () {
        $facility = createEnforcementFacility(['max_patients' => 1]);

        $parent = User::factory()->create(['role' => 'parent']);
        $childOne = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);
        $childTwo = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);

        // Fill the single patient slot.
        PatientLink::create([
            'facility_tenant_id' => $facility['tenant']->id,
            'child_id' => $childOne->id,
            'parent_user_id' => $parent->id,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.patients.store'), [
                'child_id' => $childTwo->id,
                'parent_user_id' => $parent->id,
            ])
            ->assertSessionHas('error');

        expect(PatientLink::where('facility_tenant_id', $facility['tenant']->id)->count())->toBe(1);
    });

    it('allows patient link creation when max_patients is unlimited (-1)', function () {
        $facility = createEnforcementFacility(['max_patients' => -1]);

        $parent = User::factory()->create(['role' => 'parent']);
        $child = Child::factory()->create([
            'user_id' => $parent->id,
            'tenant_id' => $facility['tenant']->id,
        ]);

        $this->actingAs($facility['user'])
            ->post(route('facility.patients.store'), [
                'child_id' => $child->id,
                'parent_user_id' => $parent->id,
            ])
            ->assertRedirect();

        expect(PatientLink::where('facility_tenant_id', $facility['tenant']->id)->count())->toBe(1);
    });
});
