<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyUnit;
use Illuminate\Database\Seeder;

class PropertyUnitsSeeder extends Seeder
{
    public function run(): void
    {
        Property::query()->where('units_count', '>', 0)->each(function (Property $property): void {
            for ($i = 1; $i <= $property->units_count; $i++) {
                PropertyUnit::firstOrCreate(
                    ['property_id' => $property->id, 'unit_number' => $i],
                    ['status' => 'available']
                );
            }
        });
    }
}
