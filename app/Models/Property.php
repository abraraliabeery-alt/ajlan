<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    protected $fillable = [
        'parent_id', 'code', 'type', 'status', 'land_area', 'built_area',
        'min_unit_area', 'max_unit_area', 'side_height', 'middle_height',
        'units_count', 'is_featured', 'is_published', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'land_area' => 'decimal:2',
            'built_area' => 'decimal:2',
            'min_unit_area' => 'decimal:2',
            'max_unit_area' => 'decimal:2',
            'side_height' => 'decimal:2',
            'middle_height' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PropertyTranslation::class);
    }

    public function translation(?string $locale = null): HasOne
    {
        $locale ??= app()->getLocale();

        return $this->hasOne(PropertyTranslation::class)->where('locale', $locale);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PropertyMedium::class)->orderBy('sort_order');
    }
}
