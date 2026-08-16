<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $plots = [
            ['T/18', 10, 13212, null, 1311, 1344, 9, 11, false],
            ['T/19', 8, 9537.92, null, 1181.45, 1202.48, 9, 11, false],
            ['T/20', 8, 11252, null, 1400.7, 1412.3, 12, 14, false],
            ['T/21', 12, 15718, null, 1267.3, 1334, 12, 14, false],
            ['T/23', 4, 8024, 10388.75, 1999.2, 2012.8, 16, 18, true],
            ['T/24', 10, 11040, null, 1094.4, 1123.2, 12, 14, false],
            ['T/25', 6, 6996, null, 1166, 1166, 16, 18, false],
            ['T/26', 12, 14257, null, 1186.14, 1190, 16, 18, false],
            ['T/27', 6, 6996, null, 1163.35, 1168.65, 12, 14, false],
            ['T/28', 6, 7526, null, 1250.8, 1272, 12, 14, false],
            ['T/29', 17, 18350.32, null, 1060, 1390.32, 12, 14, false],
            ['T/30', 22, 25733.7, null, 1107.7, 1188, 9, 11, true],
            ['T/44', 10, 12268.72, null, 1145.56, 1399.37, 12, 14, false],
            ['T/46', 8, 9020, null, 1125, 1130, 16, 18, false],
            ['T/47', 16, 17807, null, 1110.28, 1115.61, 16, 18, false],
            ['T/51', 16, 20966.78, null, 1290.68, 1311.74, 9, 11, true],
            ['T/52', 16, 20988, null, 1311.75, 1311.75, 9, 11, false],
            ['T/54', 2, 3743.72, 5400, 1871.86, 1871.86, 12, 14, true],
            ['T/76', 10, 13112.77, null, 1298.73, 1409.7, 9, 11, false],
            ['T/77', 4, 6720.4, null, 1680.1, 1680.1, 12, 14, false],
            ['T/80', 16, 21436.8, null, 1339.8, 1339.8, 9, 11, false],
            ['T/100', 16, 22578.76, null, 1411, 1413.76, 9, 11, false],
            ['T/102', 10, 14422, null, 1440, 1462, 9, 11, false],
            ['T/104', 10, 13440, null, 1344, 1344, 9, 11, false],
            ['T/108', 14, 18874.22, null, 1320, 1500.71, 9, 11, false],
        ];

        foreach ($plots as $index => [$code, $units, $land, $built, $min, $max, $side, $middle, $featured]) {
            $property = Property::updateOrCreate(
                ['code' => $code],
                [
                    'type' => 'industrial_plot', 'status' => 'contact', 'land_area' => $land,
                    'built_area' => $built, 'min_unit_area' => $min, 'max_unit_area' => $max,
                    'side_height' => $side, 'middle_height' => $middle, 'units_count' => $units,
                    'is_featured' => $featured, 'is_published' => true, 'sort_order' => $index + 1,
                ]
            );

            $slug = Str::lower(str_replace('/', '-', $code));
            $number = fn ($value) => number_format((float) $value, fmod((float) $value, 1.0) === 0.0 ? 0 : 2, '.', ',');
            $translations = [
                'ar' => [
                    'name' => "مستودعات للإيجار في الرياض – القطعة {$code}",
                    'summary' => "قطعة صناعية تضم {$units} مستودعًا بمساحات من {$number($min)} إلى {$number($max)} م² في جنوب الرياض.",
                    'description' => "توفر القطعة {$code} ضمن مخطط عجلان وإخوانه في جنوب الرياض {$units} وحدة صناعية مناسبة للتخزين والخدمات اللوجستية، بارتفاعات تشغيلية تبدأ من {$number($side)} م وتصل إلى {$number($middle)} م.",
                    'seo_title' => "مستودعات للإيجار في الرياض – القطعة {$code} | عجلان وإخوانه",
                    'seo_description' => "استأجر مستودعًا في القطعة {$code} جنوب الرياض. {$units} وحدات بمساحات {$number($min)}–{$number($max)} م² ومواصفات صناعية متكاملة.",
                ],
                'en' => [
                    'name' => "Warehouses for rent in Riyadh – Plot {$code}",
                    'summary' => "An industrial plot with {$units} warehouses from {$number($min)} to {$number($max)} m² in south Riyadh.",
                    'description' => "Plot {$code} in the Ajlan & Bros industrial plan offers {$units} units for warehousing and logistics, with operating heights from {$number($side)} m to {$number($middle)} m.",
                    'seo_title' => "Warehouses for Rent in Riyadh – Plot {$code} | Ajlan & Bros",
                    'seo_description' => "Rent a warehouse in plot {$code}, south Riyadh. {$units} industrial units from {$number($min)} to {$number($max)} m².",
                ],
                'zh' => [
                    'name' => "利雅得仓库出租 – {$code}号地块",
                    'summary' => "利雅得南部工业地块，共有{$units}个仓库，面积从{$number($min)}至{$number($max)}平方米。",
                    'description' => "Ajlan & Bros工业园区{$code}号地块设有{$units}个仓储和物流单元，运营高度从{$number($side)}米至{$number($middle)}米。",
                    'seo_title' => "利雅得仓库出租 – {$code}号地块 | Ajlan & Bros",
                    'seo_description' => "利雅得南部{$code}号地块仓库出租，共{$units}个工业单元，面积{$number($min)}至{$number($max)}平方米。",
                ],
            ];

            foreach ($translations as $locale => $translation) {
                $property->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['slug' => $slug, ...$translation]
                );
            }
        }

        foreach ([13, 14, 15, 16] as $index => $number) {
            $showroom = Property::updateOrCreate(
                ['code' => 'S/'.$number],
                [
                    'type' => 'showroom', 'status' => 'contact', 'units_count' => 1,
                    'is_featured' => false, 'is_published' => false, 'sort_order' => 100 + $index,
                ]
            );

            foreach ([
                'ar' => ['name' => "معرض {$number}", 'summary' => 'معرض تجاري ضمن مجموعة معارض 13–16.', 'seo_title' => "معرض {$number} | عجلان وإخوانه العقارية", 'seo_description' => "معرض تجاري ضمن محفظة عجلان وإخوانه العقارية في الرياض."],
                'en' => ['name' => "Showroom {$number}", 'summary' => "A commercial unit within the 13–16 showroom collection.", 'seo_title' => "Showroom {$number} | Ajlan & Bros Real Estate", 'seo_description' => "A commercial showroom within the Ajlan & Bros real estate portfolio in Riyadh."],
                'zh' => ['name' => "{$number}号展厅", 'summary' => "13–16号商业展厅组中的一个单元。", 'seo_title' => "{$number}号展厅 | Ajlan & Bros房地产", 'seo_description' => "Ajlan & Bros利雅得房地产组合中的商业展厅。"],
            ] as $locale => $translation) {
                $showroom->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['slug' => 'showroom-'.$number, 'description' => $translation['summary'], ...$translation]
                );
            }
        }
    }
}
