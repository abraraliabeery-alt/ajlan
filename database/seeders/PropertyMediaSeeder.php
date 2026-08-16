<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyMedium;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertyMediaSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Property::where('code', 'T/51')->first();

        $this->seedCollection(
            collection: 'warehouses',
            directory: 'media/warehouses',
            propertyId: $warehouse?->id,
            labels: ['ar' => 'مستودعات عجلان وإخوانه في جنوب الرياض', 'en' => 'Ajlan & Bros warehouses in south Riyadh', 'zh' => 'Ajlan & Bros利雅得南部仓库']
        );

        $this->seedCollection(
            collection: 'showrooms',
            directory: 'media/showrooms',
            propertyId: null,
            labels: ['ar' => 'معارض عجلان وإخوانه 13–16', 'en' => 'Ajlan & Bros showrooms 13–16', 'zh' => 'Ajlan & Bros 13–16号展厅']
        );
    }

    private function seedCollection(string $collection, string $directory, ?int $propertyId, array $labels): void
    {
        $files = glob(public_path($directory.'/*')) ?: [];
        sort($files, SORT_NATURAL);

        foreach ($files as $index => $file) {
            $relativePath = $directory.'/'.basename($file);
            $extension = Str::lower(pathinfo($file, PATHINFO_EXTENSION));
            $type = $extension === 'mp4' ? 'video' : 'image';
            $medium = PropertyMedium::updateOrCreate(
                ['file_path' => $relativePath],
                [
                    'property_id' => $propertyId,
                    'collection' => $collection,
                    'type' => $type,
                    'thumbnail_path' => $type === 'video' ? $directory.'/'.($collection === 'warehouses' ? 'warehouses-poster.jpg' : 'showrooms-poster.jpg') : null,
                    'is_cover' => str_contains(basename($file), 'poster'),
                    'sort_order' => $index + 1,
                ]
            );

            foreach ($labels as $locale => $label) {
                $medium->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['alt_text' => $label.' '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)]
                );
            }
        }
    }
}
