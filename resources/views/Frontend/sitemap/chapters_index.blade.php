@php
echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @for ($i = 1; $i <= max(1, $totalPages); $i++)
    <sitemap>
        <loc>{{ url(route('sitemap.chapters.alt') . '?page=' . $i) }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </sitemap>
    @endfor
</sitemapindex> 