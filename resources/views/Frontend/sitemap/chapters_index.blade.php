<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @for ($i = 1; $i <= $totalPages; $i++)
    <sitemap>
        <loc>{{ route('sitemap.chapters.alt', ['page' => $i]) }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </sitemap>
    @endfor
</sitemapindex> 