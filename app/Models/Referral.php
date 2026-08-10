<?php

namespace App\Models;

use App\Enums\ReferralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $child_id
 * @property string $from_tenant_id
 * @property string $to_tenant_id
 * @property int $referring_staff_id
 * @property string $reason
 * @property string|null $clinical_summary
 * @property string $status
 * @property string|null $notes
 */
class Referral extends Model
{
    /** @use HasFactory<Database\Factories\ReferralFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'from_tenant_id',
        'to_tenant_id',
        'referring_staff_id',
        'reason',
        'clinical_summary',
        'status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class,
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
     * Get the source tenant (facility that made the referral).
     */
    public function fromTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'from_tenant_id');
    }

    /**
     * Get the destination tenant (facility receiving the referral).
     */
    public function toTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'to_tenant_id');
    }

    /**
     * Get the staff member who made the referral.
     */
    public function referringStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referring_staff_id');
    }

    /**
     * Accept this referral.
     */
    public function accept(): void
    {
        $this->update(['status' => ReferralStatus::Accepted]);
    }

    /**
     * Complete this referral.
     */
    public function complete(): void
    {
        $this->update(['status' => ReferralStatus::Completed]);
    }

    /**
     * Cancel this referral.
     */
    public function cancel(): void
    {
        $this->update(['status' => ReferralStatus::Cancelled]);
    }
}
