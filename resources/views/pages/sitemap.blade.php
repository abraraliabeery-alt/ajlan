{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach(config('app.supported_locales') as $locale)
@foreach(['home', 'properties.index', 'media', 'contact'] as $routeName)
<url><loc>{{ route($routeName, $locale) }}</loc><changefreq>{{ $routeName === 'home' ? 'weekly' : 'monthly' }}</changefreq><priority>{{ $routeName === 'home' ? '1.0' : '0.8' }}</priority></url>
@endforeach
@endforeach
@foreach($properties as $translation)
<url><loc>{{ route('properties.show', [$translation->locale, $translation->slug]) }}</loc><changefreq>weekly</changefreq><priority>0.9</priority></url>
@endforeach
</urlset>
