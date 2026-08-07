<?php

use App\Models\Child;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;

it('can create a document', function () {
    $document = Document::factory()->create();

    expect($document)->toBeInstanceOf(Document::class);
    expect($document->name)->toBeString();
    expect($document->type)->toBeString();
    expect($document->child_id)->toBeInt();
    expect($document->user_id)->toBeInt();
});

it('belongs to a child', function () {
    $document = Document::factory()->create();

    expect($document->child)->not->toBeNull();
    expect($document->child)->toBeInstanceOf(Child::class);
});

it('belongs to a user', function () {
    $document = Document::factory()->create();

    expect($document->user)->not->toBeNull();
    expect($document->user)->toBeInstanceOf(User::class);
});

it('has correct fillable attributes', function () {
    $document = new Document;

    expect($document->getFillable())->toBe([
        'child_id',
        'user_id',
        'name',
        'type',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'issued_date',
        'expiry_date',
        'is_private',
    ]);
});

it('casts is_private as boolean', function () {
    $document = Document::factory()->create(['is_private' => true]);

    expect($document->is_private)->toBeTrue();
});

it('casts file_size as integer', function () {
    $document = Document::factory()->create(['file_size' => 5242880]);

    expect($document->file_size)->toBeInt();
    expect($document->file_size)->toBe(5242880);
});

it('casts issued_date as date', function () {
    $document = Document::factory()->create(['issued_date' => '2026-08-07']);

    expect($document->issued_date)->toBeInstanceOf(Carbon::class);
});

it('casts expiry_date as date', function () {
    $document = Document::factory()->create(['expiry_date' => '2028-12-31']);

    expect($document->expiry_date)->toBeInstanceOf(Carbon::class);
});

it('returns correct type labels', function () {
    $types = [
        'birth_certificate' => '📜 Akta Lahir',
        'family_card' => '🏠 Kartu Keluarga',
        'kia' => '🪪 KIA',
        'bpjs' => '🏥 BPJS',
        'passport' => '✈️ Paspor',
        'certificate' => '🎓 Sertifikat',
        'report_card' => '📋 Rapor',
        'other' => '📄 Lainnya',
    ];

    foreach ($types as $type => $label) {
        $document = Document::factory()->create(['type' => $type]);
        expect($document->type_label)->toBe($label);
    }
});

it('returns formatted size in bytes', function () {
    $document = Document::factory()->create(['file_size' => 500]);
    expect($document->formatted_size)->toBe('500 B');
});

it('returns formatted size in kilobytes', function () {
    $document = Document::factory()->create(['file_size' => 2048]);
    expect($document->formatted_size)->toBe('2.00 KB');
});

it('returns formatted size in megabytes', function () {
    $document = Document::factory()->create(['file_size' => 5242880]);
    expect($document->formatted_size)->toBe('5.00 MB');
});

it('returns formatted size in gigabytes', function () {
    $document = Document::factory()->create(['file_size' => 2147483648]);
    expect($document->formatted_size)->toBe('2.00 GB');
});

it('returns formatted issued date', function () {
    $document = Document::factory()->create(['issued_date' => '2026-08-07']);

    expect($document->formatted_issued_date)->toBeString();
    expect($document->formatted_issued_date)->toContain('2026');
});

it('returns null formatted issued date when not set', function () {
    $document = Document::factory()->create(['issued_date' => null]);

    expect($document->formatted_issued_date)->toBeNull();
});

it('returns formatted expiry date', function () {
    $document = Document::factory()->create(['expiry_date' => '2028-12-31']);

    expect($document->formatted_expiry_date)->toBeString();
    expect($document->formatted_expiry_date)->toContain('2028');
});

it('returns null formatted expiry date when not set', function () {
    $document = Document::factory()->create(['expiry_date' => null]);

    expect($document->formatted_expiry_date)->toBeNull();
});

it('detects expired document', function () {
    $document = Document::factory()->create(['expiry_date' => '2020-01-01']);

    expect($document->is_expired)->toBeTrue();
});

it('detects non-expired document', function () {
    $document = Document::factory()->create(['expiry_date' => '2099-12-31']);

    expect($document->is_expired)->toBeFalse();
});

it('returns false for is_expired when no expiry date', function () {
    $document = Document::factory()->create(['expiry_date' => null]);

    expect($document->is_expired)->toBeFalse();
});

it('can be created with public state', function () {
    $document = Document::factory()->public()->create();

    expect($document->is_private)->toBeFalse();
});

it('can be created with private state', function () {
    $document = Document::factory()->private()->create();

    expect($document->is_private)->toBeTrue();
});

it('can be created with specific type state', function () {
    $document = Document::factory()->ofType('passport')->create();

    expect($document->type)->toBe('passport');
});

it('child has many documents', function () {
    $child = Child::factory()->create();
    $user = User::factory()->create();
    Document::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);
    Document::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    expect($child->documents)->toHaveCount(2);
});
