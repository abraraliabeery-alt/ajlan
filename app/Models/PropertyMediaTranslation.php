<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyMediaTranslation extends Model
{
    protected $fillable = ['property_media_id', 'locale', 'alt_text'];

    public function medium(): BelongsTo
    {
        return $this->belongsTo(PropertyMedium::class, 'property_media_id');
    }
}
