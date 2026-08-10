<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $tenant_id
 * @property string $facility_type
 * @property string|null $address
 * @property string|null $city
 * @property string|null $province
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $email_institution
 * @property string|null $website
 * @property string|null $license_number
 * @property array|null $operating_hours
 * @property string|null $description
 * @property array|null $facilities
 */
class Facility extends Model
{
    /** @use HasFactory<Database\Factories\FacilityFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'facility_type',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email_institution',
        'website',
        'license_number',
        'operating_hours',
        'description',
        'facilities',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'operating_hours' => 'array',
            'facilities' => 'array',
        ];
    }

    /**
     * Get the tenant that owns the facility.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all staff for this facility.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get all patients (children) registered at this facility.
     */
    public function patients()
    {
        return $this->hasMany(Child::class, 'tenant_id', 'tenant_id');
    }
}
