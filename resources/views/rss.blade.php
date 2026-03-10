<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>{{ config('app.name') }}</title>
    <link>{{ url('/') }}</link>
    <description><![CDATA[RSS Feed]]></description>
    <atom:link href="{{ url('/') }}" rel="self" type="application/rss+xml" />
    <language>en</language>
    <lastBuildDate></lastBuildDate>

    @foreach($posts as $post)
    <?php if ($post->image !='') {
        $img = "<img src='".asset('laravel/public/news/' . $post->image)."' alt='".$post->heading."' width='100%'>";
    } else {
        $img = null;
    }
    ?>
    <item>
      <title><![CDATA[{!! $post->title !!}]]></title>
      <link>{{ url($post->news_category[0]->slug. '/' . $post->slug) }}</link>
      <guid isPermaLink="true">{{ url($post->news_category[0]->slug. '/' . $post->slug) }}</guid>
      <description><![CDATA[{!! $img !!} {!! $post->content !!}]]></description>
      {{-- <content:encoded><![CDATA[{!! $post->content !!}]]></content:encoded> --}}
      <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">Sangri Times</dc:creator>
      <pubDate>{{ $post->created_at->format(DateTime::RSS) }}</pubDate>
  </item>
  @endforeach
</channel>
</rss>