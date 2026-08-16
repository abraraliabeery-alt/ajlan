@extends('layouts.app')
@section('title', __('site.about').' | '.__('site.brand'))
@section('description', __('site.company_body'))
@section('content')
<section class="page-hero about-hero"><div class="container"><span class="kicker">{{ __('site.company_kicker') }}</span><h1>{{ __('site.company_title') }}</h1><p>{{ __('site.company_body') }}</p></div></section>
<section class="section"><div class="container company-story"><div class="story-number">25<span>+</span></div><div><h2>{{ __('site.brand') }}</h2><p>{{ __('site.company_body') }}</p><div class="mini-stats"><div><strong>25</strong><span>{{ __('site.plots_count') }}</span></div><div><strong>269</strong><span>{{ __('site.warehouses_count') }}</span></div><div><strong>25+</strong><span>{{ app()->getLocale() === 'ar' ? 'دولة' : (app()->getLocale() === 'zh' ? '个国家' : 'countries') }}</span></div></div></div></div></section>
@endsection
