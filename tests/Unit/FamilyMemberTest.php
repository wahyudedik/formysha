<?php

use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\User;

describe('FamilyMember Model', function () {
    it('creates a family member with valid data', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create(['child_id' => $child->id]);

        expect($member)->toBeInstanceOf(FamilyMember::class);
        expect($member->name)->not->toBeEmpty();
        expect($member->relationship)->not->toBeEmpty();
    });

    it('belongs to a child', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create(['child_id' => $child->id]);

        expect($member->child)->toBeInstanceOf(Child::class);
        expect($member->child->id)->toBe($child->id);
    });

    it('optionally belongs to a user', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->linked()->create([
            'child_id' => $child->id,
        ]);

        expect($member->user)->toBeInstanceOf(User::class);
    });

    it('returns correct relationship label in Indonesian', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $father = FamilyMember::factory()->father()->create(['child_id' => $child->id]);
        $mother = FamilyMember::factory()->mother()->create(['child_id' => $child->id]);

        expect($father->relationship_label)->toBe('Ayah');
        expect($mother->relationship_label)->toBe('Ibu');
    });

    it('casts is_primary as boolean', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->primary()->create([
            'child_id' => $child->id,
        ]);

        expect($member->is_primary)->toBeTrue();
    });
});
