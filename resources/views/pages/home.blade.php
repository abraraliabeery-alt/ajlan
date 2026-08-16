@extends('layouts.app')

@section('title', __('site.seo_default_title'))
@section('description', __('site.seo_default_description'))

@section('content')
<section class="hero">
    <video class="hero-video" autoplay muted loop playsinline poster="{{ asset('media/warehouses/warehouses-poster.jpg') }}">
        <source src="{{ asset('media/warehouses/warehouses-51-54.mp4') }}" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <span class="kicker">{{ __('site.hero_kicker') }}</span>
        <h1>{{ __('site.hero_title') }}</h1>
        <p>{{ __('site.hero_body') }}</p>
        <div class="button-row">
            <a class="button" href="{{ route('properties.index', $locale) }}">{{ __('site.browse') }}</a>
            <a class="button button-ghost" href="{{ route('contact', $locale) }}">{{ __('site.request_visit') }}</a>
        </div>
    </div>
    <div class="container stats-strip">
        <div><strong>269</strong><span>{{ __('site.warehouses_count') }}</span></div>
        <div><strong>25</strong><span>{{ __('site.plots_count') }}</span></div>
        <div><strong>344,022.11</strong><span>{{ __('site.land_area_total') }}</span></div>
        <div><strong>450,186.22</strong><span>{{ __('site.built_area_total') }}</span></div>
    </div>
</section>

<section class="section featured-section">
    <div class="container">
        <div class="section-heading">
            <div><span class="kicker">{{ __('site.featured_kicker') }}</span><h2>{{ __('site.featured_title') }}</h2></div>
            <p>{{ __('site.featured_body') }}</p>
        </div>
        <div class="property-grid">
            @foreach($featured as $property)
                @include('partials.property-card', ['property' => $property])
            @endforeach
        </div>
        <div class="section-action"><a class="button button-outline" href="{{ route('properties.index', $locale) }}">{{ __('site.all_plots') }}</a></div>
    </div>
</section>

<section class="section specs-section">
    <div class="container">
        <div class="section-heading light-heading">
            <div><span class="kicker">{{ __('site.specs_kicker') }}</span><h2>{{ __('site.specs_title') }}</h2></div>
            <p>{{ __('site.specs_body') }}</p>
        </div>
        <div class="feature-grid">
            @foreach(__('site.features') as $index => $feature)
                <article><span class="feature-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $feature[0] }}</h3><p>{{ $feature[1] }}</p></article>
            @endforeach
        </div>
    </div>
</section>

<section class="section media-promo">
    <div class="container media-promo-grid">
        <div class="media-stack">
            <img src="{{ asset('media/warehouses/warehouse-039.webp') }}" alt="{{ __('site.warehouse_collection') }}" loading="lazy">
            <img src="{{ asset('media/showrooms/showroom-057.webp') }}" alt="{{ __('site.showroom_collection') }}" loading="lazy">
        </div>
        <div class="media-copy"><span class="kicker">{{ __('site.media_kicker') }}</span><h2>{{ __('site.media_title') }}</h2><p>{{ __('site.media_body') }}</p><a class="button" href="{{ route('media', $locale) }}">{{ __('site.view_media') }}</a></div>
    </div>
</section>

<section class="section company-band">
    <div class="container company-grid">
        <div><span class="kicker">{{ __('site.company_kicker') }}</span><h2>{{ __('site.company_title') }}</h2></div>
        <p>{{ __('site.company_body') }}</p>
    </div>
</section>

<section class="section contact-cta">
    <div class="container cta-panel">
        <div><span class="kicker">{{ __('site.contact') }}</span><h2>{{ __('site.contact_title') }}</h2><p>{{ __('site.contact_body') }}</p></div>
        <a class="button button-light" href="{{ route('contact', $locale) }}">{{ __('site.request_warehouse') }}</a>
    </div>
</section>
@endsection
