@extends('layouts.app')
@section('title', __('site.contact_title').' | '.__('site.brand'))
@section('description', __('site.contact_body'))
@section('content')
<section class="page-hero"><div class="container"><span class="kicker">{{ __('site.contact') }}</span><h1>{{ __('site.contact_title') }}</h1><p>{{ __('site.contact_body') }}</p></div></section>
<section class="section"><div class="container contact-grid"><div class="contact-direct"><h2>{{ __('site.brand') }}</h2><a dir="ltr" href="tel:{{ config('app.contact_phone') }}">{{ config('app.contact_phone') }}</a><a dir="ltr" href="mailto:{{ config('app.contact_email') }}">{{ config('app.contact_email') }}</a><img src="{{ asset('media/warehouses/warehouses-poster.jpg') }}" alt="{{ __('site.warehouse_collection') }}"></div>@include('partials.inquiry-form', ['properties' => $properties])</div></section>
@endsection
