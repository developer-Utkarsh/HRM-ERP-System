<?php 
/*
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	@foreach ($posts as $post)
	<url>
		<loc><?php echo url($post->article_category[0]->alias. '/' . $post->slug) ?></loc>
		<lastmod>{{ $post->created_at->tz('UTC')->toAtomString() }}</lastmod>            
	</url>
	@endforeach
</urlset>
*/ ?>

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	<sitemap>

		<loc><?php echo url('googlenewssitemap.xml') ?></loc>

	</sitemap>

	<sitemap>

		<loc><?php echo url('news-sitemap-index.xml') ?></loc>		

	</sitemap>

	<sitemap>

		<loc><?php echo url('categories-sitemap-index.xml') ?></loc>		

	</sitemap>

</sitemapindex>
