<?php

use App\Models\Child;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('requires authentication to access document index', function () {
    $child = Child::factory()->create();

    $this->get(route('documents.index', $child))
        ->assertRedirect(route('login'));
});

it('shows empty state when no documents exist', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('documents.index', $child))
        ->assertOk()
        ->assertSee('Belum Ada Dokumen');
});

it('lists documents for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Document::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'Akta Lahir Mysha',
    ]);

    actingAs($user)
        ->get(route('documents.index', $child))
        ->assertOk()
        ->assertSee('Akta Lahir Mysha');
});

it('prevents other users from viewing documents', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('documents.index', $child))
        ->assertForbidden();
});

it('shows create document form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('documents.create', $child))
        ->assertOk()
        ->assertSee('Tambah Dokumen');
});

it('stores a new document with file upload', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $file = UploadedFile::fake()->create('akta_lahir.pdf', 512, 'application/pdf');

    actingAs($user)
        ->post(route('documents.store', $child), [
            'name' => 'Akta Lahir Mysha',
            'type' => 'birth_certificate',
            'description' => 'Akta lahir resmi',
            'file' => $file,
            'issued_date' => '2026-01-15',
            'is_private' => '1',
        ])
        ->assertRedirect(route('documents.index', $child));

    assertDatabaseHas('documents', [
        'child_id' => $child->id,
        'name' => 'Akta Lahir Mysha',
        'type' => 'birth_certificate',
        'is_private' => true,
    ]);
});

it('validates required fields when storing document', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('documents.store', $child), [
            'name' => '',
            'type' => '',
        ])
        ->assertSessionHasErrors(['name', 'type', 'file']);
});

it('validates document type enum when storing', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

    actingAs($user)
        ->post(route('documents.store', $child), [
            'name' => 'Test Document',
            'type' => 'invalid_type',
            'file' => $file,
        ])
        ->assertSessionHasErrors('type');
});

it('shows document detail page', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $document = Document::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'Detail Dokumen',
    ]);

    actingAs($user)
        ->get(route('documents.show', [$child, $document]))
        ->assertOk()
        ->assertSee('Detail Dokumen');
});

it('prevents other users from viewing document detail', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $document = Document::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('documents.show', [$child, $document]))
        ->assertForbidden();
});

it('shows edit document form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $document = Document::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'Edit Me',
    ]);

    actingAs($user)
        ->get(route('documents.edit', [$child, $document]))
        ->assertOk()
        ->assertSee('Edit Dokumen')
        ->assertSee('Edit Me');
});

it('updates a document without new file', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $document = Document::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'Old Name',
    ]);

    actingAs($user)
        ->put(route('documents.update', [$child, $document]), [
            'name' => 'New Name',
            'type' => 'kia',
        ])
        ->assertRedirect(route('documents.show', [$child, $document]));

    assertDatabaseHas('documents', [
        'id' => $document->id,
        'name' => 'New Name',
        'type' => 'kia',
    ]);
});

it('updates a document with new file', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $document = Document::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'Old Name',
    ]);

    $file = UploadedFile::fake()->create('new_file.pdf', 256, 'application/pdf');

    actingAs($user)
        ->put(route('documents.update', [$child, $document]), [
            'name' => 'Updated Name',
            'type' => 'passport',
            'file' => $file,
        ])
        ->assertRedirect(route('documents.show', [$child, $document]));

    assertDatabaseHas('documents', [
        'id' => $document->id,
        'name' => 'Updated Name',
        'type' => 'passport',
    ]);
});

it('deletes a document', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $document = Document::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'To Delete',
    ]);

    actingAs($user)
        ->delete(route('documents.destroy', [$child, $document]))
        ->assertRedirect(route('documents.index', $child));

    assertDatabaseMissing('documents', ['id' => $document->id]);
});
