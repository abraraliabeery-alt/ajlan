<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTranslation extends Model
{
    protected $fillable = [
        'property_id', 'locale', 'name', 'slug', 'summary', 'description',
        'seo_title', 'seo_description',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
