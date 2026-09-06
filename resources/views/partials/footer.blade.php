<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <img class="footer-logo" src="{{ asset(app()->getLocale() === 'ar' ? 'brand/logo-ar.png' : 'brand/logo-en.png') }}" alt="{{ __('site.brand') }}">
            <p>{{ __('site.footer_text') }}</p>
        </div>
        <div class="footer-links">
            <a href="{{ route('properties.index', app()->getLocale()) }}">{{ __('site.warehouses') }}</a>
            <a href="{{ route('media', app()->getLocale()) }}">{{ __('site.media') }}</a>
            <a href="{{ route('contact', app()->getLocale()) }}">{{ __('site.contact') }}</a>
        </div>
        <div class="footer-contact">
            <a dir="ltr" href="tel:{{ config('app.contact_phone') }}">{{ config('app.contact_phone') }}</a>
            <a dir="ltr" href="mailto:{{ config('app.contact_email') }}">{{ config('app.contact_email') }}</a>
        </div>
    </div>
    <div class="container copyright">© {{ date('Y') }} {{ __('site.brand') }} — {{ __('site.rights') }}</div>
</footer>
