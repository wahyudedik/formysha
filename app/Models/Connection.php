<?php

namespace App\Models;

use App\Enums\ConnectionPermission;
use App\Enums\ConnectionStatus;
use Carbon\Carbon;
use Database\Factories\ConnectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $child_id
 * @property string $tenant_id
 * @property ConnectionStatus $status
 * @property ConnectionPermission $permission
 * @property int|null $invited_by
 * @property Carbon|null $invited_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $expires_at
 * @property string|null $notes
 * @property array|null $metadata
 */
class Connection extends Model
{
    /** @use HasFactory<ConnectionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'tenant_id',
        'status',
        'permission',
        'invited_by',
        'invited_at',
        'accepted_at',
        'expires_at',
        'notes',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ConnectionStatus::class,
            'permission' => ConnectionPermission::class,
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    /**
     * Get the child that this connection belongs to.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the tenant (organization) that this connection belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who invited this connection.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the activity history for this connection.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ActivityHistory::class);
    }

    /**
     * Get the audit logs related to this connection.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Status Checks ──────────────────────────────────────────

    /**
     * Check if the connection is active.
     */
    public function isActive(): bool
    {
        return $this->status === ConnectionStatus::Active;
    }

    /**
     * Check if the connection is pending.
     */
    public function isPending(): bool
    {
        return $this->status === ConnectionStatus::Pending;
    }

    /**
     * Check if the connection is referred.
     */
    public function isReferred(): bool
    {
        return $this->status === ConnectionStatus::Referred;
    }

    // ─── Permission Checks ──────────────────────────────────────

    /**
     * Check if this connection has at least the given permission level.
     */
    public function hasPermission(ConnectionPermission $perm): bool
    {
        return $this->permission->level() >= $perm->level();
    }

    /**
     * Check if this connection can view data.
     */
    public function canView(): bool
    {
        return true;
    }

    /**
     * Check if this connection can comment on data.
     */
    public function canComment(): bool
    {
        return $this->permission->canComment();
    }

    /**
     * Check if this connection can edit data.
     */
    public function canEdit(): bool
    {
        return $this->permission->canEdit();
    }

    /**
     * Check if this connection can manage data.
     */
    public function canManage(): bool
    {
        return $this->permission->canManage();
    }

    // ─── Actions ────────────────────────────────────────────────

    /**
     * Approve the connection — set status to Active and record accepted_at.
     */
    public function approve(): void
    {
        $this->update([
            'status' => ConnectionStatus::Active,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Reject the connection — delete it.
     */
    public function reject(): void
    {
        $this->delete();
    }

    /**
     * Revoke the connection — delete it (connection terminated by owner).
     */
    public function revoke(): void
    {
        $this->delete();
    }

    /**
     * Update the permission level for this connection.
     */
    public function updatePermission(ConnectionPermission $perm): void
    {
        $this->update(['permission' => $perm]);
    }
}
