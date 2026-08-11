<?php

namespace Database\Seeders;

use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            SuperAdminSeeder::class,
            FamilyAndFacilitySeeder::class,
        ]);

        // Hanya buat test data di environment local atau testing
        if (! app()->environment('local', 'testing')) {
            return;
        }

        // Create a test user with children (skip if already exists)
        $existingUser = User::where('email', 'budi@for-mysha.my.id')->first();

        if (! $existingUser) {
            $user = User::factory()->create([
                'name' => 'Budi Santoso',
                'email' => 'budi@for-mysha.my.id',
            ]);

            // Create children for the test user
            $mysha = Child::factory()->create([
                'user_id' => $user->id,
                'name' => 'Mysha Aisyah',
                'slug' => 'mysha',
                'nickname' => 'Mysha',
                'gender' => 'female',
                'date_of_birth' => '2023-06-15',
                'place_of_birth' => 'Jakarta',
                'blood_type' => 'A',
                'is_public' => true,
            ]);

            $qaireen = Child::factory()->create([
                'user_id' => $user->id,
                'name' => 'Qaireen Ahmad',
                'slug' => 'qaireen',
                'nickname' => 'Qai',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
                'place_of_birth' => 'Bandung',
                'blood_type' => 'O',
                'is_public' => false,
            ]);

            // Create family members for Mysha
            FamilyMember::factory()->father()->primary()->create([
                'child_id' => $mysha->id,
                'user_id' => $user->id,
                'name' => 'Budi Santoso',
            ]);

            FamilyMember::factory()->mother()->primary()->create([
                'child_id' => $mysha->id,
                'name' => 'Rina Sari',
                'email' => 'rina@for-mysha.my.id',
            ]);

            FamilyMember::factory()->state([
                'relationship' => 'grandmother',
            ])->create([
                'child_id' => $mysha->id,
                'name' => 'Siti Aminah',
            ]);

            // Create family members for Qaireen
            FamilyMember::factory()->father()->primary()->create([
                'child_id' => $qaireen->id,
                'user_id' => $user->id,
                'name' => 'Budi Santoso',
            ]);

            FamilyMember::factory()->mother()->primary()->create([
                'child_id' => $qaireen->id,
                'name' => 'Rina Sari',
                'email' => 'rina@for-mysha.my.id',
            ]);
        }
    }
}
