<?php

use App\Enums\TenantType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('facility admin is redirected to facility dashboard after login', function () {
    // Create a B2B tenant (clinic) manually since Tenant doesn't have a factory
    $tenantId = Str::uuid();
    DB::table('tenants')->insert([
        'id' => $tenantId,
        'name' => 'Klinik Test',
        'slug' => 'klinik-test-'.Str::random(5),
        'type' => TenantType::Clinic->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create a tenant_admin user for this tenant
    $user = User::factory()->create([
        'role' => 'tenant_admin',
        'tenant_id' => $tenantId,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('facility.dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
