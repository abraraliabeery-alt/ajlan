<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyMedium extends Model
{
    protected $table = 'property_media';

    protected $fillable = ['property_id', 'collection', 'type', 'file_path', 'thumbnail_path', 'is_cover', 'sort_order'];

    protected function casts(): array
    {
        return ['is_cover' => 'boolean'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PropertyMediaTranslation::class, 'property_media_id');
    }
}
