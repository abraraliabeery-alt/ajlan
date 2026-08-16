@php($translation = $property->translations->first())
<article class="property-card" data-code="{{ strtolower($property->code) }}" data-area="{{ $property->min_unit_area }}">
    <div class="card-top">
        <span class="plot-code" dir="ltr">{{ $property->code }}</span>
        <span class="unit-badge">{{ $property->units_count }} {{ __('site.units') }}</span>
    </div>
    <h3>{{ $translation->name }}</h3>
    <p>{{ $translation->summary }}</p>
    <dl class="card-specs">
        <div><dt>{{ __('site.land_area') }}</dt><dd>{{ number_format((float) $property->land_area, 2, '.', ',') }} {{ __('site.sqm') }}</dd></div>
        <div><dt>{{ __('site.unit_area') }}</dt><dd>{{ number_format((float) $property->min_unit_area, 2, '.', ',') }}–{{ number_format((float) $property->max_unit_area, 2, '.', ',') }} {{ __('site.sqm') }}</dd></div>
        <div><dt>{{ __('site.height') }}</dt><dd>{{ number_format((float) $property->side_height, 0, '.', ',') }}–{{ number_format((float) $property->middle_height, 0, '.', ',') }} {{ __('site.meter') }}</dd></div>
    </dl>
    <a class="text-link" href="{{ route('properties.show', [app()->getLocale(), $translation->slug]) }}">{{ __('site.details') }} <span>↗</span></a>
</article>
