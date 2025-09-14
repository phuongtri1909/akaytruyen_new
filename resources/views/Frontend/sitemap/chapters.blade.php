<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($chapters as $chapter)
        <url>
            <loc>{{ route('chapter', [$chapter->story->slug, $chapter->slug]) }}</loc>
            <lastmod>{{ $chapter->updated_at ? $chapter->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset> 