<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	@foreach ($posts as $post)
	<url>
		<loc><?php echo url($post->news_category[0]->slug. '/' . $post->slug) ?></loc>

		<lastmod>{{ $post->created_at->tz('UTC')->toAtomString() }}</lastmod>

		<changefreq>Daily</changefreq>

		<priority>1</priority>
	</url>
	@endforeach
</urlset> 





