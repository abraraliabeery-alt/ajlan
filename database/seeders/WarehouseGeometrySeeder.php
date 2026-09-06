<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class WarehouseGeometrySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/warehouses_269.geojson');
        if (! file_exists($path)) {
            $this->command?->warn('warehouses_269.geojson not found — skipped.');

            return;
        }

        $features = json_decode(file_get_contents($path), true)['features'] ?? [];
        $properties = Property::with('units')->get()->keyBy('code');
        $matched = 0;
        $missing = [];

        foreach ($features as $feature) {
            $props = $feature['properties'];
            $property = $properties->get($props['ajlan_block_code']);
            if (! $property) {
                $missing[] = $props['warehouse_code'];

                continue;
            }

            $unit = $property->units->firstWhere('code', $props['warehouse_code'])
                ?? $property->units->sortBy('unit_number')->values()->get($props['sequence'] - 1);

            if (! $unit) {
                $missing[] = $props['warehouse_code'];

                continue;
            }

            $unit->update([
                'code' => $props['warehouse_code'],
                'parcel_nos' => $props['parcel_numbers'] ?? null,
                'land_area' => $props['land_area_m2'] ?? null,
                'area' => $props['built_area_report_m2'] ?? null,
                'geometry' => $feature['geometry'],
            ]);
            $matched++;
        }

        $this->command?->info("warehouse geometries imported: {$matched}");
        if ($missing) {
            $this->command?->warn('unmatched: '.implode(', ', $missing));
        }
    }
}
