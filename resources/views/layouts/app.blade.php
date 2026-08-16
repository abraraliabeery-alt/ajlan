@php
    $currentLocale = app()->getLocale();
    $direction = $currentLocale === 'ar' ? 'rtl' : 'ltr';
    $pageTitle = Illuminate\Support\Facades\View::hasSection('title') ? trim(Illuminate\Support\Facades\View::yieldContent('title')) : __('site.seo_default_title');
    $pageDescription = Illuminate\Support\Facades\View::hasSection('description') ? trim(Illuminate\Support\Facades\View::yieldContent('description')) : __('site.seo_default_description');
    $canonical = Illuminate\Support\Facades\View::hasSection('canonical') ? trim(Illuminate\Support\Facades\View::yieldContent('canonical')) : url()->current();
    $property = isset($property) ? $property : null;
    $switchUrl = function (string $target) use ($currentLocale, $property) {
        if (isset($property)) {
            $translated = $property->translations->firstWhere('locale', $target);
            return $translated ? route('properties.show', [$target, $translated->slug]) : route('home', $target);
        }
        $segments = request()->segments();
        if (isset($segments[0]) && in_array($segments[0], config('app.supported_locales'), true)) {
            $segments[0] = $target;
            return url(implode('/', $segments));
        }
        return route('home', $target);
    };
@endphp
<!doctype html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $canonical }}">
    @foreach(config('app.supported_locales') as $targetLocale)
        <link rel="alternate" hreflang="{{ $targetLocale === 'zh' ? 'zh-CN' : $targetLocale }}" href="{{ $switchUrl($targetLocale) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $switchUrl('ar') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $currentLocale === 'ar' ? 'ar_SA' : ($currentLocale === 'zh' ? 'zh_CN' : 'en_US') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ asset('media/warehouses/warehouses-poster.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ asset('media/warehouses/warehouses-poster.jpg') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
    <script>document.documentElement.dataset.theme=localStorage.getItem('ajlan-theme')||'dark';</script>
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'RealEstateAgent',
        'name' => __('site.brand'),
        'url' => route('home', $currentLocale),
        'logo' => asset('brand/logo-ar.png'),
        'telephone' => config('app.contact_phone'),
        'email' => config('app.contact_email'),
        'areaServed' => ['@type' => 'City', 'name' => 'Riyadh'],
    ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    @stack('head')
</head>
<body>
    @include('partials.header')
    <main>@yield('content')</main>
    @include('partials.footer')

    <nav class="quick-contact" aria-label="{{ __('site.contact') }}">
        <a href="tel:{{ config('app.contact_phone') }}" aria-label="{{ __('site.call_now') }}">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
            </svg>
        </a>
        <a href="mailto:{{ config('app.contact_email') }}" aria-label="{{ __('site.email_us') }}">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
        </a>
        <a href="https://wa.me/{{ config('app.contact_phone') }}" aria-label="واتساب">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.358-.214-3.721.976.994-3.626-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.134 1.588 5.94L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </a>
    </nav>
    <script src="{{ asset('js/site.js') }}" defer></script>
</body>
</html>
