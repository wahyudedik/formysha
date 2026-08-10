<?php

namespace App\Models;

use App\Enums\ClinicalNoteType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $child_id
 * @property int $staff_user_id
 * @property string $tenant_id
 * @property string $type
 * @property string $title
 * @property string $content
 * @property array|null $vitals
 * @property string|null $diagnosis
 * @property array|null $medications
 * @property array|null $attachments
 */
class ClinicalNote extends Model
{
    /** @use HasFactory<Database\Factories\ClinicalNoteFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'staff_user_id',
        'tenant_id',
        'type',
        'title',
        'content',
        'vitals',
        'diagnosis',
        'medications',
        'attachments',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ClinicalNoteType::class,
            'vitals' => 'array',
            'medications' => 'array',
            'attachments' => 'array',
        ];
    }

    /**
     * Get the child (patient).
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the staff member who wrote this note.
     */
    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    /**
     * Get the tenant this note belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
