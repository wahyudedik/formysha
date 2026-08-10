<?php

use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;

describe('Facility Registration (B2B)', function () {
    it('displays the facility registration form', function () {
        $response = $this->get(route('register.facility'));

        $response->assertOk();
        $response->assertSee('Daftar Fasilitas');
        $response->assertSee('facility_name');
        $response->assertSee('facility_type');
    });

    it('can register a new clinic facility', function () {
        $email = fake()->unique()->safeEmail();

        $response = $this->post(route('register.facility'), [
            'name' => 'Dr. Budi',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Klinik Sehat',
            'facility_type' => 'clinic',
            'address' => 'Jl. Kesehatan No. 10',
            'phone' => '081234567890',
            'license_number' => 'KLN-001',
            'description' => 'Klinik umum',
        ]);

        $response->assertRedirect(route('facility.dashboard'));

        $user = User::where('email', $email)->first();
        expect($user)->not->toBeNull();
        expect($user->role)->toBe('tenant_admin');

        $tenant = Tenant::where('name', 'Klinik Sehat')->first();
        expect($tenant)->not->toBeNull();
        expect($tenant->type->value)->toBe('clinic');
    });

    it('can register a hospital facility', function () {
        $email = fake()->unique()->safeEmail();

        $response = $this->post(route('register.facility'), [
            'name' => 'Dr. Sari',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'RS Anak Sehat',
            'facility_type' => 'hospital',
        ]);

        $response->assertRedirect(route('facility.dashboard'));

        $user = User::where('email', $email)->first();
        expect($user)->not->toBeNull();

        $tenant = Tenant::where('name', 'RS Anak Sehat')->first();
        expect($tenant)->not->toBeNull();
        expect($tenant->type->value)->toBe('hospital');
    });

    it('can register a midwifery facility', function () {
        $email = fake()->unique()->safeEmail();

        $response = $this->post(route('register.facility'), [
            'name' => 'Bidan Sari',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Praktik Bidan Sari',
            'facility_type' => 'midwifery',
        ]);

        $response->assertRedirect(route('facility.dashboard'));

        $tenant = Tenant::where('name', 'Praktik Bidan Sari')->first();
        expect($tenant)->not->toBeNull();
        expect($tenant->type->value)->toBe('midwifery');
    });

    it('can register a daycare facility', function () {
        $email = fake()->unique()->safeEmail();

        $response = $this->post(route('register.facility'), [
            'name' => 'Ibu Dewi',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Daycare Ceria',
            'facility_type' => 'daycare',
        ]);

        $response->assertRedirect(route('facility.dashboard'));

        $tenant = Tenant::where('name', 'Daycare Ceria')->first();
        expect($tenant)->not->toBeNull();
        expect($tenant->type->value)->toBe('daycare');
    });

    it('can register a school facility', function () {
        $email = fake()->unique()->safeEmail();

        $response = $this->post(route('register.facility'), [
            'name' => 'Pak Guru',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'TK Ceria',
            'facility_type' => 'school',
        ]);

        $response->assertRedirect(route('facility.dashboard'));

        $tenant = Tenant::where('name', 'TK Ceria')->first();
        expect($tenant)->not->toBeNull();
        expect($tenant->type->value)->toBe('school');
    });

    it('validates required fields', function () {
        $response = $this->post(route('register.facility'), []);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
            'facility_name',
            'facility_type',
        ]);
    });

    it('validates email uniqueness', function () {
        User::factory()->create(['email' => 'existing@email.com']);

        $response = $this->post(route('register.facility'), [
            'name' => 'Admin',
            'email' => 'existing@email.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Fasilitas',
            'facility_type' => 'clinic',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('validates facility type must be B2B', function () {
        $response = $this->post(route('register.facility'), [
            'name' => 'Admin',
            'email' => fake()->safeEmail(),
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Fasilitas',
            'facility_type' => 'family',
        ]);

        $response->assertSessionHasErrors('facility_type');
    });

    it('validates password confirmation', function () {
        $response = $this->post(route('register.facility'), [
            'name' => 'Admin',
            'email' => fake()->safeEmail(),
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword!',
            'facility_name' => 'Fasilitas',
            'facility_type' => 'clinic',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('creates staff record for facility owner', function () {
        $email = fake()->unique()->safeEmail();

        $response = $this->post(route('register.facility'), [
            'name' => 'Dr. Sari',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Klinik Sari',
            'facility_type' => 'clinic',
        ]);

        $response->assertRedirect(route('facility.dashboard'));

        $user = User::where('email', $email)->first();
        $staff = Staff::where('user_id', $user->id)->first();

        expect($staff)->not->toBeNull();
        expect($staff->staff_role->value)->toBe('staff_admin');
        expect($staff->is_active)->toBeTrue();
    });

    it('sets user role to tenant_admin', function () {
        $email = fake()->unique()->safeEmail();

        $this->post(route('register.facility'), [
            'name' => 'Admin Fasilitas',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Klinik Admin',
            'facility_type' => 'clinic',
        ]);

        $user = User::where('email', $email)->first();
        expect($user->role)->toBe('tenant_admin');
    });

    it('sets tenant_id on user after registration', function () {
        $email = fake()->unique()->safeEmail();

        $this->post(route('register.facility'), [
            'name' => 'Admin',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Klinik Tenant',
            'facility_type' => 'clinic',
        ]);

        $user = User::where('email', $email)->first();
        $tenant = Tenant::where('name', 'Klinik Tenant')->first();

        expect($user->tenant_id)->toBe($tenant->id);
    });

    it('authenticates user after registration', function () {
        $email = fake()->unique()->safeEmail();

        $this->post(route('register.facility'), [
            'name' => 'Admin',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'facility_name' => 'Klinik Auto',
            'facility_type' => 'clinic',
        ]);

        $user = User::where('email', $email)->first();
        $this->assertAuthenticatedAs($user);
    });

    it('landing page has B2B registration link for facilities', function () {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('register.facility'));
        $response->assertSee('Fasilitas');
    });

    it('login page has B2B registration link for facilities', function () {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(route('register.facility'));
        $response->assertSee('Daftar Fasilitas');
    });

    it('register page has B2B registration link for facilities', function () {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee(route('register.facility'));
        $response->assertSee('Daftar Fasilitas');
    });
});
