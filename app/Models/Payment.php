<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $subscription_id
 * @property string $tenant_id
 * @property int $amount
 * @property string $currency
 * @property string $payment_method
 * @property string|null $bank_name
 * @property string|null $bank_account
 * @property string|null $account_holder
 * @property string|null $proof_path
 * @property string $status
 * @property string|null $notes
 * @property int|null $verified_by
 * @property Carbon|null $verified_at
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Payment extends Model
{
    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Available bank accounts for manual transfer.
     *
     * @var array<string, array{account: string, holder: string}>
     */
    public const BANKS = [
        'BRI' => ['account' => '2118 0100 8728 508', 'holder' => 'WAHYU DEDIK DWI ASTONO'],
        'JAGO' => ['account' => '106818913479', 'holder' => 'WAHYU DEDIK DWI ASTONO'],
        'BTN' => ['account' => '5901500292405', 'holder' => 'WAHYU DEDIK DWI ASTONO'],
        'BSI' => ['account' => '7243220925', 'holder' => 'WAHYU DEDIK DWI ASTONO'],
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Payment $payment): void {
            if (empty($payment->id)) {
                $payment->id = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subscription_id',
        'tenant_id',
        'amount',
        'currency',
        'payment_method',
        'bank_name',
        'bank_account',
        'account_holder',
        'proof_path',
        'status',
        'notes',
        'verified_by',
        'verified_at',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'verified_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the subscription for this payment.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the tenant that owns the payment.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who verified this payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the formatted amount.
     */
    public function getAmountFormatted(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }
}
