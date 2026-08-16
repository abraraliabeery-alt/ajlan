@extends('layouts.app')
@section('title', __('site.media_title').' | '.__('site.brand'))
@section('description', __('site.media_body'))
@section('content')
<section class="page-hero media-hero"><div class="container"><span class="kicker">{{ __('site.media_kicker') }}</span><h1>{{ __('site.media_title') }}</h1><p>{{ __('site.media_body') }}</p></div></section>
@foreach(['warehouses' => 'site.warehouse_collection', 'showrooms' => 'site.showroom_collection'] as $collection => $label)
@php($collectionMedia = $media->where('collection', $collection))
<section class="section media-section"><div class="container"><div class="section-heading"><div><span class="kicker">{{ __('site.photos') }} & {{ __('site.video') }}</span><h2>{{ __($label) }}</h2></div><p>{{ $collectionMedia->where('type', 'image')->count() }} {{ __('site.photos') }}</p></div>
@foreach($collectionMedia->where('type', 'video') as $video)<video class="collection-video" controls preload="metadata" poster="{{ asset($video->thumbnail_path) }}"><source src="{{ asset($video->file_path) }}" type="video/mp4"></video>@endforeach
<div class="media-grid">@foreach($collectionMedia->where('type', 'image')->reject(fn($item) => str_contains($item->file_path, 'poster')) as $item)@php($alt = optional($item->translations->firstWhere('locale', $locale))->alt_text ?? __('site.brand'))<button type="button" class="media-card" data-lightbox="{{ asset($item->file_path) }}" data-alt="{{ $alt }}"><img src="{{ asset($item->file_path) }}" alt="{{ $alt }}" loading="lazy"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></button>@endforeach</div></div></section>
@endforeach
<dialog id="lightbox"><button type="button" class="lightbox-close" aria-label="Close">×</button><img src="" alt=""></dialog>
@endsection
