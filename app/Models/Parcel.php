<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcel extends Model
{
    public const STATUSES = ['available', 'reserved', 'temp_reserved', 'leased', 'visit'];

    protected $fillable = [
        'parcel_no', 'block_no', 'property_id', 'status',
        'customer_name', 'customer_phone', 'price', 'notes',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
