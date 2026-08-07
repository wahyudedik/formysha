<?php

use App\Http\Controllers\PublicProfileController;
use App\Models\Child;
use App\Models\User;

it('is an invokable controller', function () {
    $controller = new PublicProfileController;

    expect($controller)->toBeInstanceOf(PublicProfileController::class);
    expect(method_exists($controller, '__invoke'))->toBeTrue();
});

it('returns 404 for non-public child via controller', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => false,
    ]);

    $response = $this->get('/'.$child->slug);
    $response->assertNotFound();
});

it('returns 404 for non-existent slug via controller', function () {
    $response = $this->get('/non-existent-slug-xyz');
    $response->assertNotFound();
});

it('loads child with timelines relationship', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    $response = $this->get('/'.$child->slug);
    $response->assertOk();

    // Verify the view receives child data
    $viewData = $response->viewData('child');
    expect($viewData)->toBeInstanceOf(Child::class);
    expect($viewData->id)->toBe($child->id);
});

it('passes public_profile_data to view', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => ['timeline', 'gallery'],
    ]);

    $response = $this->get('/'.$child->slug);
    $response->assertOk();

    expect($response->viewData('showTimeline'))->toBeTrue();
    expect($response->viewData('showGallery'))->toBeTrue();
    expect($response->viewData('showAwards'))->toBeFalse();
});

it('defaults show flags to false when public_profile_data is empty', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => [],
    ]);

    $response = $this->get('/'.$child->slug);
    $response->assertOk();

    expect($response->viewData('showTimeline'))->toBeFalse();
    expect($response->viewData('showGallery'))->toBeFalse();
    expect($response->viewData('showAwards'))->toBeFalse();
});

it('uses public profile view', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    $response = $this->get('/'.$child->slug);
    $response->assertOk();
    $response->assertViewIs('public.profile');
});

it('handles null public_profile_data gracefully', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => null,
    ]);

    $response = $this->get('/'.$child->slug);
    $response->assertOk();

    expect($response->viewData('showTimeline'))->toBeFalse();
    expect($response->viewData('showGallery'))->toBeFalse();
    expect($response->viewData('showAwards'))->toBeFalse();
});

it('only shows public children', function () {
    $user = User::factory()->create();

    $publicChild = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'slug' => 'public-kid',
    ]);

    $privateChild = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => false,
        'slug' => 'private-kid',
    ]);

    $this->get('/public-kid')->assertOk();
    $this->get('/private-kid')->assertNotFound();
});
