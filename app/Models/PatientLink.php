<?php

namespace App\Models;

use App\Enums\PatientLinkStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $child_id
 * @property int $parent_user_id
 * @property string $facility_tenant_id
 * @property string $link_code
 * @property string $status
 * @property array|null $permissions
 * @property Carbon|null $linked_at
 * @property Carbon|null $revoked_at
 */
class PatientLink extends Model
{
    /** @use HasFactory<Database\Factories\PatientLinkFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'parent_user_id',
        'facility_tenant_id',
        'link_code',
        'status',
        'permissions',
        'linked_at',
        'revoked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PatientLinkStatus::class,
            'permissions' => 'array',
            'linked_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * Auto-generate link code on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (PatientLink $link): void {
            if (empty($link->link_code)) {
                $link->link_code = strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the child (patient).
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the parent user.
     */
    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    /**
     * Get the facility tenant.
     */
    public function facilityTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'facility_tenant_id');
    }

    /**
     * Activate this link.
     */
    public function activate(): void
    {
        $this->update([
            'status' => PatientLinkStatus::Active,
            'linked_at' => now(),
        ]);
    }

    /**
     * Revoke this link.
     */
    public function revoke(): void
    {
        $this->update([
            'status' => PatientLinkStatus::Revoked,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Check if a specific permission is granted.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions[$permission] ?? false;
    }
}
