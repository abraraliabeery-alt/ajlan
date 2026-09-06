<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyMedium;
use Illuminate\Database\Seeder;

class PlanImagesSeeder extends Seeder
{
    /** WhatsApp file name => property code. */
    private const MAP = [
        'WhatsApp Image 2026-09-06 at 5.05.25 PM.jpeg'      => 'T/18',
        'WhatsApp Image 2026-09-06 at 5.05.25 PM (1).jpeg'  => 'T/19',
        'WhatsApp Image 2026-09-06 at 5.05.26 PM.jpeg'      => 'T/20',
        'WhatsApp Image 2026-09-06 at 5.05.26 PM (1).jpeg'  => 'T/21',
        'WhatsApp Image 2026-09-06 at 5.05.26 PM (2).jpeg'  => 'T/23',
        'WhatsApp Image 2026-09-06 at 5.05.26 PM (3).jpeg'  => 'T/24',
        'WhatsApp Image 2026-09-06 at 5.05.26 PM (4).jpeg'  => 'T/25',
        'WhatsApp Image 2026-09-06 at 5.05.26 PM (5).jpeg'  => 'T/26',
        'WhatsApp Image 2026-09-06 at 5.05.27 PM.jpeg'      => 'T/27',
        'WhatsApp Image 2026-09-06 at 5.05.27 PM (1).jpeg'  => 'T/28',
        'WhatsApp Image 2026-09-06 at 5.05.27 PM (2).jpeg'  => 'T/29',
        'WhatsApp Image 2026-09-06 at 5.05.27 PM (3).jpeg'  => 'T/30',
        'WhatsApp Image 2026-09-06 at 5.05.27 PM (4).jpeg'  => 'T/44',
        'WhatsApp Image 2026-09-06 at 5.05.28 PM.jpeg'      => 'T/46',
        'WhatsApp Image 2026-09-06 at 5.05.28 PM (1).jpeg'  => 'T/47',
        'WhatsApp Image 2026-09-06 at 5.05.28 PM (2).jpeg'  => 'T/51',
        'WhatsApp Image 2026-09-06 at 5.05.28 PM (3).jpeg'  => 'T/52',
        'WhatsApp Image 2026-09-06 at 5.05.28 PM (4).jpeg'  => 'T/54',
        'WhatsApp Image 2026-09-06 at 5.05.28 PM (5).jpeg'  => 'T/76',
        'WhatsApp Image 2026-09-06 at 5.05.28 PM (6).jpeg'  => 'T/77',
        'WhatsApp Image 2026-09-06 at 5.05.29 PM.jpeg'      => 'T/80',
        'WhatsApp Image 2026-09-06 at 5.05.29 PM (1).jpeg'  => 'T/100',
        'WhatsApp Image 2026-09-06 at 5.05.29 PM (2).jpeg'  => 'T/102',
        'WhatsApp Image 2026-09-06 at 5.05.29 PM (3).jpeg'  => 'T/104',
        'WhatsApp Image 2026-09-06 at 5.05.29 PM (4).jpeg'  => 'T/108',
    ];

    public function run(): void
    {
        $sourceDir = public_path('WhatsApp Unknown 2026-09-06 at 6.07.23 PM');
        $targetDir = public_path('media/plots');

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        foreach (self::MAP as $file => $code) {
            $source = $sourceDir.DIRECTORY_SEPARATOR.$file;
            if (! is_file($source)) {
                $this->command?->warn("missing file: {$file}");
                continue;
            }

            $property = Property::where('code', $code)->first();
            if (! $property) {
                $this->command?->warn("no property for code: {$code}");
                continue;
            }

            $slug = str_replace('/', '-', strtolower($code));
            $target = "media/plots/plan-{$slug}.jpg";
            copy($source, public_path($target));

            PropertyMedium::updateOrCreate(
                ['file_path' => $target],
                [
                    'property_id' => $property->id,
                    'collection' => 'plan',
                    'type' => 'image',
                    'sort_order' => 0,
                ]
            );
        }

        $this->command?->info('Plan images assigned: '.count(self::MAP));
    }
}
