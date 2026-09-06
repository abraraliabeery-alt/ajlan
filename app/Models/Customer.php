<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'company', 'cr_number',
        'national_id', 'city', 'address', 'notes',
    ];

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function payments(): HasMany
    {
        return $this->hasManyThrough(LeasePayment::class, Lease::class);
    }

    public function getOutstandingAttribute(): float
    {
        return (float) $this->leases->sum(fn (Lease $lease) => $lease->outstanding);
    }
}
