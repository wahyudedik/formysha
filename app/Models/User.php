<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property string|null $phone
 * @property string|null $date_of_birth
 * @property string|null $address
 * @property string $role
 * @property string $language
 * @property string|null $timezone
 */
#[Fillable(['name', 'email', 'password', 'avatar', 'phone', 'date_of_birth', 'address', 'role', 'language', 'timezone', 'tenant_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the children belonging to this user.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Child::class);
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the count of unread notifications.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a guardian.
     */
    public function isGuardian(): bool
    {
        return $this->role === 'guardian';
    }

    /**
     * Get the user's preferred locale.
     */
    public function getPreferredLocale(): string
    {
        return $this->language ?? config('app.locale', 'id');
    }

    /**
     * Get the user's preferred timezone.
     */
    public function getPreferredTimezone(): string
    {
        return $this->timezone ?? config('app.timezone', 'Asia/Jakarta');
    }

    /**
     * Get the user's role label in Indonesian.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'parent' => 'Orang Tua',
            'guardian' => 'Wali',
            'admin' => 'Admin',
            'super_admin' => 'Super Admin',
            'tenant_admin' => 'Tenant Admin',
            default => $this->role,
        };
    }

    /**
     * Get the tenant that owns this user.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the current subscription for this user's tenant.
     */
    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the audit logs for this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if the user is a parent.
     */
    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if the user is a tenant admin.
     */
    public function isTenantAdmin(): bool
    {
        return $this->role === 'tenant_admin';
    }
}
