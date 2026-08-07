<?php

use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\User;
use Carbon\Carbon;

describe('Child Model', function () {
    it('creates a child with valid data', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        expect($child)->toBeInstanceOf(Child::class);
        expect($child->name)->not->toBeEmpty();
        expect($child->slug)->not->toBeEmpty();
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        expect($child->user)->toBeInstanceOf(User::class);
        expect($child->user->id)->toBe($user->id);
    });

    it('has many family members', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        FamilyMember::factory()->count(3)->create(['child_id' => $child->id]);

        expect($child->familyMembers)->toHaveCount(3);
        expect($child->familyMembers->first())->toBeInstanceOf(FamilyMember::class);
    });

    it('generates slug from name on creation', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mysha Aisyah',
            'slug' => 'mysha-aisyah',
        ]);

        expect($child->slug)->toBe('mysha-aisyah');
    });

    it('calculates age correctly', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'date_of_birth' => now()->subYears(2)->subMonths(3),
        ]);

        $age = $child->age;
        expect($age)->toContain('2 tahun');
    });

    it('returns correct public url', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'slug' => 'mysha',
        ]);

        expect($child->public_url)->toBe('/mysha');
    });

    it('casts boolean is_public correctly', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'is_public' => true,
        ]);

        expect($child->is_public)->toBeTrue();
    });

    it('casts date_of_birth as carbon instance', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'date_of_birth' => '2023-06-15',
        ]);

        expect($child->date_of_birth)->toBeInstanceOf(Carbon::class);
    });
});
