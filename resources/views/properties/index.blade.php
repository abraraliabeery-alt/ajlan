@extends('layouts.app')

@section('title', __('site.catalog_title').' | '.__('site.brand'))
@section('description', __('site.catalog_body'))

@section('content')
<section class="page-hero compact-hero">
    <div class="container"><span class="kicker">{{ __('site.warehouses') }}</span><h1>{{ __('site.catalog_title') }}</h1><p>{{ __('site.catalog_body') }}</p></div>
</section>
<section class="section catalog-section">
    <div class="container">
        <div class="catalog-tools">
            <label class="search-field"><span>⌕</span><input id="plot-search" type="search" placeholder="{{ __('site.search') }}" autocomplete="off"></label>
            <div class="result-count"><strong id="result-count">{{ $properties->count() }}</strong> {{ __('site.results') }}</div>
        </div>
        <div id="property-grid" class="property-grid catalog-grid">
            @foreach($properties as $property)
                @include('partials.property-card', ['property' => $property])
            @endforeach
        </div>
        <p id="no-results" class="no-results" hidden>{{ __('site.no_results') }}</p>
    </div>
</section>
@endsection
