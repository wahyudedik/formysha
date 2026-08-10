<?php

namespace App\Models;

use App\Enums\StaffRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $tenant_id
 * @property StaffRole $staff_role
 * @property string|null $specialization
 * @property string|null $license_number
 * @property string|null $phone
 * @property bool $is_active
 * @property array|null $settings
 */
class Staff extends Model
{
    /** @use HasFactory<Database\Factories\StaffFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'tenant_id',
        'staff_role',
        'specialization',
        'license_number',
        'phone',
        'is_active',
        'settings',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'staff_role' => StaffRole::class,
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    /**
     * Get the user that owns this staff profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tenant this staff belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if this staff can write clinical notes.
     */
    public function canWriteClinicalNotes(): bool
    {
        /** @var StaffRole $role */
        $role = $this->staff_role;

        return $role->canWriteClinicalNotes();
    }

    /**
     * Check if this staff can create referrals.
     */
    public function canCreateReferrals(): bool
    {
        /** @var StaffRole $role */
        $role = $this->staff_role;

        return $role->canCreateReferrals();
    }

    /**
     * Check if this staff has admin access.
     */
    public function isAdmin(): bool
    {
        /** @var StaffRole $role */
        $role = $this->staff_role;

        return $role->isAdmin();
    }

    /**
     * Scope: only active staff.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
