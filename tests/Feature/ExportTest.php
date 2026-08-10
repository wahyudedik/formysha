<?php

use App\Models\Child;
use App\Models\User;

describe('Export Authorization', function () {
    it('redirects unauthenticated users to login', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->get(route('export.profile', $child))
            ->assertRedirect(route('login'));
    });

    it('allows owner to export child profile', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('export.profile', $child))
            ->assertOk();
    });

    it('prevents other users from exporting child profile', function () {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('export.profile', $child))
            ->assertForbidden();
    });

    it('allows owner to export health records', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('export.health', $child))
            ->assertOk();
    });

    it('prevents other users from exporting health records', function () {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('export.health', $child))
            ->assertForbidden();
    });

    it('allows owner to export growth records', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('export.growth', $child))
            ->assertOk();
    });

    it('prevents other users from exporting growth records', function () {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('export.growth', $child))
            ->assertForbidden();
    });

    it('allows owner to export child zip', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('export.zip', $child))
            ->assertOk();
    });

    it('prevents other users from exporting child zip', function () {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('export.zip', $child))
            ->assertForbidden();
    });
});
