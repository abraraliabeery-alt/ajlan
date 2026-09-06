<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class WarehouseUnitsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/warehouse_units.json');
        if (! file_exists($path)) {
            return;
        }

        $data = json_decode(file_get_contents($path), true);
        $counters = [];

        foreach ($data as $code => $row) {
            $propertyCode = implode('/', array_slice(explode('/', $code), 0, 2));
            $index = $counters[$propertyCode] ?? 0;

            $unit = Property::where('code', $propertyCode)->first()
                ?->units()->orderBy('unit_number')->skip($index)->first();

            if ($unit) {
                $unit->update([
                    'code' => $code,
                    'parcel_nos' => $row['parcels'],
                    'land_area' => $row['land_area'] ?? null,
                    'area' => $row['built_area'] ?? null,
                ]);
            }

            $counters[$propertyCode] = $index + 1;
        }
    }
}
