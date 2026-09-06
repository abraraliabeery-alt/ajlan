@extends('layouts.app')

@section('title', $translation->seo_title)
@section('description', $translation->seo_description)
@section('canonical', route('properties.show', [$locale, $translation->slug]))

@push('head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Place',
    'name' => $translation->name,
    'description' => $translation->seo_description,
    'url' => route('properties.show', [$locale, $translation->slug]),
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Riyadh', 'addressCountry' => 'SA'],
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faq)->map(fn ($item) => ['@type' => 'Question', 'name' => $item[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item[1]]]),
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<section class="plot-hero">
    <div class="plot-lines"></div>
    <div class="container plot-hero-grid">
        <div class="plot-copy"><a class="back-link" href="{{ route('properties.index', $locale) }}">← {{ __('site.warehouses') }}</a><span class="kicker">Industrial plot</span><h1>{{ $translation->name }}</h1><div class="giant-code" dir="ltr">{{ $property->code }}</div><p>{{ $translation->description }}</p></div>
        <div class="plot-number"><strong>{{ preg_replace('/\D/', '', $property->code) }}</strong><span>{{ $property->units_count }} {{ __('site.units') }}</span></div>
    </div>
    <div class="container plot-facts">
        <div><span>{{ __('site.land_area') }}</span><strong>{{ number_format((float) $property->land_area, 2, '.', ',') }} {{ __('site.sqm') }}</strong></div>
        <div><span>{{ __('site.unit_area') }}</span><strong>{{ number_format((float) $property->min_unit_area, 2, '.', ',') }}–{{ number_format((float) $property->max_unit_area, 2, '.', ',') }} {{ __('site.sqm') }}</strong></div>
        <div><span>{{ __('site.height') }}</span><strong>{{ number_format((float) $property->side_height, 0, '.', ',') }}–{{ number_format((float) $property->middle_height, 0, '.', ',') }} {{ __('site.meter') }}</strong></div>
        <div><span>{{ __('site.units') }}</span><strong>{{ $property->units_count }}</strong></div>
    </div>
</section>

@if($gallery->isNotEmpty())
<section class="album-strip">
    <div class="container">
        <div class="album-grid">
            @foreach($gallery as $item)
                @if($item->type === 'video')
                <button type="button" class="album-card album-video" data-lightbox="{{ asset($item->file_path) }}" data-lb-group="album" data-lb-type="video">
                    <img src="{{ asset($item->thumbnail_path) }}" alt="" loading="lazy"><span class="album-play">▶</span>
                </button>
                @else
                <button type="button" class="album-card" data-lightbox="{{ asset($item->file_path) }}" data-lb-group="album">
                    <img src="{{ asset($item->file_path) }}" alt="" loading="lazy">
                </button>
                @endif
            @endforeach
        </div>
    </div>
</section>
<dialog id="album-lightbox" class="lightbox" data-lb-dialog="album">
    <button type="button" class="lightbox-close" aria-label="Close">×</button>
    <button type="button" class="lightbox-nav lightbox-prev" aria-label="Prev">‹</button>
    <img src="" alt="">
    <video controls playsinline preload="metadata" style="display:none"></video>
    <button type="button" class="lightbox-nav lightbox-next" aria-label="Next">›</button>
    <div class="lightbox-counter"></div>
</dialog>
@endif

<section class="section property-overview"><div class="container two-column"><div><span class="kicker">{{ __('site.overview') }}</span><h2>{{ $translation->name }}</h2><p>{{ $translation->description }}</p>@if(in_array($property->code, ['T/51','T/54']))<div class="media-note">◉ {{ __('site.media_available') }}</div>@endif</div><div class="industrial-shape"><span dir="ltr">{{ $property->code }}</span></div></div></section>

<section class="section specs-section"><div class="container"><div class="section-heading light-heading"><div><span class="kicker">{{ __('site.specifications') }}</span><h2>{{ __('site.specs_title') }}</h2></div><p>{{ __('site.specs_body') }}</p></div><div class="feature-grid">@foreach(__('site.features') as $index => $feature)<article><span class="feature-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $feature[0] }}</h3><p>{{ $feature[1] }}</p></article>@endforeach</div></div></section>

<section class="section faq-section"><div class="container"><span class="kicker">FAQ</span><h2>{{ __('site.faq') }}</h2><div class="faq-list">@foreach($faq as $index => $item)<details @if($index === 0) open @endif><summary><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>{{ $item[0] }}</summary><p>{{ $item[1] }}</p></details>@endforeach</div></div></section>

<section class="section property-contact"><div class="container contact-grid"><div><span class="kicker">{{ __('site.availability') }}</span><h2>{{ __('site.contact_title') }}</h2><p>{{ __('site.contact_body') }}</p></div>@include('partials.inquiry-form', ['property' => $property])</div></section>
@endsection
