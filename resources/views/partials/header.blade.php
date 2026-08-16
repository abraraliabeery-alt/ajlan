<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home', app()->getLocale()) }}" class="brand" aria-label="{{ __('site.brand') }}">
            <img src="{{ asset(app()->getLocale() === 'ar' ? 'brand/logo-ar.png' : 'brand/logo-en.png') }}" alt="{{ __('site.brand') }}">
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav">☰</button>
        <nav id="main-nav" class="main-nav">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home', app()->getLocale()) }}">{{ __('site.home') }}</a>
            <a class="{{ request()->routeIs('properties.*') ? 'active' : '' }}" href="{{ route('properties.index', app()->getLocale()) }}">{{ __('site.warehouses') }}</a>
            <a class="{{ request()->routeIs('media') ? 'active' : '' }}" href="{{ route('media', app()->getLocale()) }}">{{ __('site.media') }}</a>
            <a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about', app()->getLocale()) }}">{{ __('site.about') }}</a>
            <a class="{{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact', app()->getLocale()) }}">{{ __('site.contact') }}</a>
        </nav>
        <div class="header-actions">
            <div class="language-switcher" aria-label="Language">
                @foreach(['ar' => 'AR', 'en' => 'EN', 'zh' => 'ZH'] as $target => $label)
                    <a class="{{ app()->getLocale() === $target ? 'active' : '' }}" href="{{ $switchUrl($target) }}" hreflang="{{ $target }}">{{ $label }}</a>
                @endforeach
            </div>
            <button class="theme-toggle" type="button" aria-label="Theme"><span class="theme-icon">◐</span></button>
            <a class="button button-small" href="{{ route('contact', app()->getLocale()) }}">{{ __('site.request_warehouse') }}</a>
        </div>
    </div>
</header>
