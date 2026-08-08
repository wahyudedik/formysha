<?php

use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Support\Facades\Hash;

describe('SuperAdminSeeder', function () {
    it('creates super admin user', function () {
        (new SuperAdminSeeder)->run();

        $admin = User::where('email', 'info@formysha.my.id')->first();

        expect($admin)->not->toBeNull()
            ->and($admin->name)->toBe('Super Admin')
            ->and($admin->role)->toBe('super_admin')
            ->and($admin->email_verified_at)->not->toBeNull();

        expect(Hash::check('Wahyu123456789@', $admin->password))->toBeTrue();
    });

    it('is idempotent - does not create duplicate users', function () {
        (new SuperAdminSeeder)->run();
        (new SuperAdminSeeder)->run();

        $count = User::where('email', 'info@formysha.my.id')->count();

        expect($count)->toBe(1);
    });

    it('can login with seeded credentials', function () {
        (new SuperAdminSeeder)->run();

        $admin = User::where('email', 'info@formysha.my.id')->first();

        expect($admin)->not->toBeNull()
            ->and($admin->isSuperAdmin())->toBeTrue();
    });
});
