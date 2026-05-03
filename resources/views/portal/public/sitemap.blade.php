<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    @foreach ($menus as $menu)
    <url>
        <loc>{{ route('katalog.show', $menu->slug) }}</loc>
        <lastmod>{{ \Carbon\Carbon::parse($menu->updated_at)->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    @foreach ($events as $event)
    <url>
        <loc>{{ route('event.show', $event->slug) }}</loc>
        <lastmod>{{ \Carbon\Carbon::parse($event->updated_at)->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
