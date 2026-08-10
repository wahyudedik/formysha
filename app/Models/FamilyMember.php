<?php

namespace App\Models;

use App\Enums\FamilyMemberPermission;
use Database\Factories\FamilyMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $child_id
 * @property string|null $tenant_id
 * @property int|null $user_id
 * @property string $name
 * @property string $relationship
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $photo
 * @property bool $is_primary
 * @property FamilyMemberPermission $permission_level
 */
class FamilyMember extends Model
{
    /** @use HasFactory<FamilyMemberFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'tenant_id',
        'user_id',
        'name',
        'relationship',
        'phone',
        'email',
        'photo',
        'is_primary',
        'permission_level',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'permission_level' => FamilyMemberPermission::class,
        ];
    }

    /**
     * Get the child that this family member belongs to.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user account linked to this family member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the relationship label in Indonesian.
     */
    public function getRelationshipLabelAttribute(): string
    {
        return match ($this->relationship) {
            'father' => 'Ayah',
            'mother' => 'Ibu',
            'guardian' => 'Wali',
            'grandfather' => 'Kakek',
            'grandmother' => 'Nenek',
            'sibling' => 'Saudara/i',
            'other' => 'Lainnya',
            default => $this->relationship,
        };
    }

    /**
     * Check if this family member can edit data.
     */
    public function canEdit(): bool
    {
        return $this->permission_level->canEdit();
    }

    /**
     * Check if this family member can manage (admin) data.
     */
    public function canManage(): bool
    {
        return $this->permission_level->canManage();
    }

    /**
     * Check if this family member has at least the given permission level.
     */
    public function hasPermission(FamilyMemberPermission $required): bool
    {
        return $this->permission_level->level() >= $required->level();
    }
}
