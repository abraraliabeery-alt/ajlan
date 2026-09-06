<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyUnit extends Model
{
    public const STATUSES = ['available', 'reserved', 'temp_reserved', 'leased', 'visit'];

    protected $fillable = ['property_id', 'unit_number', 'status', 'area', 'code', 'parcel_nos', 'land_area', 'geometry'];

    protected function casts(): array
    {
        return ['area' => 'decimal:2', 'land_area' => 'decimal:2', 'parcel_nos' => 'array', 'geometry' => 'array'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease(): ?Lease
    {
        return $this->leases()->active()->latest('start_date')->first();
    }
}
