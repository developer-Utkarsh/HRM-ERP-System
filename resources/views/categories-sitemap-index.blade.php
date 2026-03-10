<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	@foreach ($categories as $category)
	<url>
		<loc><?php echo url($category->slug) ?></loc>

		<lastmod>{{ $category->created_at->tz('UTC')->toAtomString() }}</lastmod>

		<priority>0.5</priority>
	</url>
	@endforeach
</urlset> 


