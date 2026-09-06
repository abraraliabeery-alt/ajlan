<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Property;
use App\Models\PropertyMedium;
use App\Models\PropertyTranslation;
use Illuminate\Http\Response;

class SiteController extends Controller
{
    public function home(string $locale)
    {
        $properties = $this->propertyQuery($locale)->get();
        $featured = $properties->where('is_featured', true);

        return view('pages.home', compact('locale', 'properties', 'featured'));
    }

    public function properties(string $locale)
    {
        $properties = $this->propertyQuery($locale)->get();

        return view('properties.index', compact('locale', 'properties'));
    }

    public function property(string $locale, string $slug)
    {
        $translation = PropertyTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->with(['property.translations', 'property.media.translations'])
            ->firstOrFail();

        $property = $translation->property;
        $faq = $this->propertyFaq($property, $locale);

        $collection = $property->type === 'showroom' ? 'showrooms' : 'warehouses';
        $gallery = PropertyMedium::query()->with('translations')
            ->where('collection', $collection)
            ->orderBy('sort_order')->get()
            ->reject(fn ($m) => str_contains($m->file_path, 'poster'));

        $planImage = $property->media->firstWhere('collection', 'plan');

        $unitRows = $property->units
            ->sortBy('code', SORT_NATURAL)
            ->groupBy(fn ($u) => explode('/', (string) $u->code)[2] ?? '');

        return view('properties.show', compact('locale', 'property', 'translation', 'faq', 'gallery', 'planImage', 'unitRows'));
    }

    public function media(string $locale)
    {
        $media = PropertyMedium::query()->with('translations')->orderBy('collection')->orderBy('sort_order')->get();

        return view('pages.media', compact('locale', 'media'));
    }

    public function map(string $locale)
    {
        $properties = Property::query()
            ->where('is_published', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale), 'units'])
            ->withCount([
                'units as available_count' => fn ($query) => $query->where('status', 'available'),
                'units as reserved_count' => fn ($query) => $query->where('status', 'reserved'),
                'units as leased_count' => fn ($query) => $query->where('status', 'leased'),
            ])
            ->get();

        $blockProperties = $properties->mapWithKeys(function ($property) use ($locale) {
                if (! preg_match('/(\d+)/', (string) $property->code, $matches)) {
                    return [];
                }
                $slug = $property->translations->first()->slug ?? null;

                return [(int) $matches[1] => [
                    'code' => $property->code,
                    'units' => $property->units_count,
                    'available' => $property->available_count,
                    'reserved' => $property->reserved_count,
                    'leased' => $property->leased_count,
                    'unitList' => $property->units->map(fn ($u) => [
                        'n' => $u->unit_number, 's' => $u->status, 'c' => $u->code,
                        'p' => $u->parcel_nos, 'g' => $u->geometry,
                        'a' => $u->area ? (float) $u->area : null,
                        'la' => $u->land_area ? (float) $u->land_area : null,
                    ])->values(),
                    'url' => $slug ? route('properties.show', [$locale, $slug]) : null,
                ]];
            });

        $parcelUnits = [];
        foreach ($properties as $property) {
            $slug = $property->translations->first()->slug ?? null;
            $url = $slug ? route('properties.show', [$locale, $slug]) : null;
            foreach ($property->units as $unit) {
                foreach ((array) $unit->parcel_nos as $pn) {
                    $parcelUnits[$pn] = ['code' => $unit->code, 's' => $unit->status, 'url' => $url];
                }
            }
        }

        $parcelStatuses = Parcel::query()->pluck('status', 'parcel_no');

        $statusNames = [];
        foreach (Parcel::STATUSES as $status) {
            $statusNames[$status] = __('site.st_'.$status);
        }

        return view('pages.map', compact('locale', 'blockProperties', 'parcelStatuses', 'parcelUnits', 'statusNames'));
    }

    public function about(string $locale)
    {
        return view('pages.about', compact('locale'));
    }

    public function contact(string $locale)
    {
        $properties = $this->propertyQuery($locale)->get();

        return view('pages.contact', compact('locale', 'properties'));
    }

    public function sitemap(): Response
    {
        $properties = PropertyTranslation::query()->whereHas('property', fn ($query) => $query->where('is_published', true))->get();

        return response()
            ->view('pages.sitemap', compact('properties'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        return response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200)
            ->header('Content-Type', 'text/plain');
    }

    private function propertyQuery(string $locale)
    {
        return Property::query()
            ->where('is_published', true)
            ->where('type', 'industrial_plot')
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('sort_order');
    }

    private function propertyFaq(Property $property, string $locale): array
    {
        $number = fn ($value) => number_format((float) $value, fmod((float) $value, 1.0) === 0.0 ? 0 : 2, '.', ',');

        return match ($locale) {
            'en' => [
                ["How many warehouses are in plot {$property->code}?", "Plot {$property->code} contains {$property->units_count} industrial warehouse units."],
                ["What is the warehouse area in plot {$property->code}?", 'Unit areas range from '.$number($property->min_unit_area).' to '.$number($property->max_unit_area).' m².'],
                ["What is the warehouse height?", 'Side height is '.$number($property->side_height).' m and middle height reaches '.$number($property->middle_height).' m.'],
            ],
            'zh' => [
                ["{$property->code}号地块有多少个仓库？", "该地块共有{$property->units_count}个工业仓库单元。"],
                ["{$property->code}号地块的仓库面积是多少？", '单元面积从'.$number($property->min_unit_area).'至'.$number($property->max_unit_area).'平方米。'],
                ['仓库高度是多少？', '边高为'.$number($property->side_height).'米，中间高度可达'.$number($property->middle_height).'米。'],
            ],
            default => [
                ["كم عدد المستودعات في القطعة {$property->code}؟", "تضم القطعة {$property->code} عدد {$property->units_count} وحدة صناعية ومستودعًا."],
                ["ما مساحة المستودع في القطعة {$property->code}؟", 'تتراوح مساحة الوحدة من '.$number($property->min_unit_area).' إلى '.$number($property->max_unit_area).' م².'],
                ['ما ارتفاع المستودعات؟', 'يبلغ الارتفاع الجانبي '.$number($property->side_height).' م ويصل الارتفاع الأوسط إلى '.$number($property->middle_height).' م.'],
            ],
        };
    }
}
