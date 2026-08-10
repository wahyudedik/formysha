<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the super admin user.
     */
    public function run(): void
    {
        $password = env('SUPER_ADMIN_PASSWORD', Str::random(20));

        $user = User::firstOrCreate(
            ['email' => 'info@formysha.my.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'role' => 'super_admin', 
            ]
        );

        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }
    }
}
