<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ Meta::get('title').' - '.config('app.name') }}</title>
    <meta name="description" content="{!! Meta::get('description') !!}">
    <meta name="keywords" content="{!! Meta::get('keywords') !!}" name="keywords">
    <meta name="news_keywords" content="Breaking News in English,English News, Latest News in English,Sangri Times English, News, English News, News in English " />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    
    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="{{ asset('laravel/public/images/favicon.ico') }}" type="image/x-icon" />
    <link rel="apple-touch-icon" href="{{ asset('laravel/public/images/apple-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('laravel/public/images/apple-touch-icon-57x57.png') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('laravel/public/images/apple-touch-icon-72x72.png') }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('laravel/public/images/apple-touch-icon-76x76.png') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('laravel/public/images/apple-touch-icon-114x114.png') }}" />
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('laravel/public/images/apple-touch-icon-120x120.png') }}" />
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('laravel/public/images/apple-touch-icon-144x144.png') }}" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('laravel/public/images/apple-touch-icon-152x152.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('laravel/public/images/apple-touch-icon-180x180.png') }}" />
    <link href='https://www.sangritimes.com/' rel='openid.delegate'/>

    
    @if(isset($news) && !empty($news))
    <link href='{{ route('blog.single', ['slug1' => $news->news_category[0]->slug, 'slug2' => $news->slug]) }}' rel='canonical'/>
    <link href='{{ asset('laravel/public/news/' . $news->image) }}' rel='image_src'/>
    <meta content='{{ route('blog.single', ['slug1' => $news->news_category[0]->slug, 'slug2' => $news->slug]) }}' property='og:url'/>
    <meta content='{{ $news->title }}' property='og:title'/>
    <meta content='{!! substr(strip_tags($news->content), 0, 79) . '...' !!}' property='og:description'/>
    <meta content='{{ asset('laravel/public/news/' . $news->image) }}' property='og:image'/>
    @endif
    <!--<meta property="og:site_name" content="Sangri Times" />-->
    <!--<meta property="og:description" content="Sangri Times is News Media website which covers the latest news in National, Politics, Rajasthan and many more categories." />-->
    <meta property="og:type" content="website" />
    <meta name="google-site-verification" content="agEnzsLrFUTeDmb_ryWptC5zbyMf6R6KMi6zJs2gkWo" />
    <meta name="google-site-verification" content="bu4qMzNoFnJq2Bg4Tiw4UAAbADJXc0Zgvz8i4jKZk3w" />
    <meta name='dmca-site-verification' content='YWJnZ2lKUUtoZFpzUUlYcCt0OGRTM1JrTU9TYjNBRldaQUd5R1BlY2tpQT01' />
    <meta content='BING-WEBMASTER-CODE' name='msvalidate.01'/>
    <meta name="theme-color" content="#910404">
    <meta content='Jaipur, India' name='geo.placename'/>
    <meta content='Sangri Times' name='Author'/>
    <meta content='general' name='rating'/>
    <meta content='India' name='geo.country'/>
    <meta content='en_IN' property='og:locale'/>
    <meta content='en_GB' property='og:locale:alternate'/>
    <meta name="atdlayout" content="home">
    <meta content='hi' name='language'/>
    <meta name="Rating" content="General">
    <meta name="Distribution" content="Global">
    <meta name="Revisit-after" content="1 Day">
    <meta content='https://www.facebook.com/sangritimes' property='article:author'/>
    <meta content='https://www.facebook.com/sangritimes' property='article:publisher'/>
    <meta content='482525085745422' property='fb:app_id'/>
    <meta content='992882750732646' property='fb:admins'/>
    <link rel="alternate" media="only screen and (max-width: 640px)" href="https://www.sangritimes.com/" />
    <meta content='@sangritimes' name='twitter:site'/>
    <meta content='@sangritimes' name='twitter:creator'/>
    <link href="https://m.sangritimes.com/" media="only screen and (max-width: 480px)" rel="alternate">
    <link rel="dns-prefetch" href="https://securepubads.g.doubleclick.net">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">
    <link rel="dns-prefetch" href="https://www.googletagservices.com">
    <link rel="dns-prefetch" href="https://connect.facebook.net">
    <link rel="dns-prefetch" href="https://www.facebook.com">
    <link rel="dns-prefetch" href="https://pagead2.googlesyndication.com">
    <link rel="dns-prefetch" href="https://tpc.googlesyndication.com">
    <link rel="dns-prefetch" href="https://adservice.google.com">
    <link rel="dns-prefetch" href="https://www.google.com">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://adservice.google.co.in">
    <link rel="dns-prefetch" href="https://www.google.co.in">
    <link rel="dns-prefetch" href="https://stats.g.doubleclick.net">
    <link rel="dns-prefetch" href="https://s7.addthis.com">
    <link rel="dns-prefetch" href="https://res.cloudinary.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://z.moatads.com">
    <link rel="dns-prefetch" href="https://www.sangrifactcheck.in">
    <link rel="dns-prefetch" href="https://v1.addthisedge.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">

    <!-- JSON-LD markup generated by Google Structured Data Markup Helper. -->
    <script type="application/ld+json">{
        "@context": "http://schema.org/",
        "@type": "Organization",
        "@id": "#organization",
        "logo": {
        "@type": "ImageObject",
        "url": "https://en.sangritimes.com/laravel/public/front/img/logo_default.png"
    },
    "url": "https://en.sangritimes.com/",
    "name": "Sangri Times English",
    "description": "Sangri Times is a website of news and current affairs that publishes news reports from various places, from general reports to opinion, analysis and fact checks."
}</script>
<script type='application/ld+json'> 
{
  "@context": "http://www.schema.org",
  "@type": "Organization",
  "name": "Sangri Times",
  "url": "https://en.sangritimes.com",
  "sameAs": [
 "http://www.facebook.com/sangritimes",
 "http://instagram.com/sangritimes",
 "http://twitter.com/sangritimes"
 ]
  "logo": "https://en.sangritimes.com/laravel/public/front/img/logo_default.png",
  "description": "Sangri Times is a website of news and current affairs that publishes news reports from various places, from general reports to opinion, analysis and fact checks."
}
 </script>
 <script type='application/ld+json'> 
{
  "@context": "http://www.schema.org",
  "@type": "WebSite",
  "name": "Sangri Times",
  "alternateName": "Sangri Times News",
  "url": "https://en.sangritimes.com"
}
 </script>
<script type='application/ld+json'> 
{
  "@context": "http://www.schema.org",
  "@type": "person",
  "name": "Junjaram Thory",
  "jobTitle": "CEO, Sangri Internet",
  "url": "https://en.everybodywiki.com/Junjaram_Thory",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Jaipur",
    "addressRegion": "Rajasthan",
    "addressCountry": "India"
  },
  "birthDate": "1997-04-30"
}
 </script>
<script type="application/ld+json">
  @if(isset($news) && !empty($news))
  {
      "@context" : "http://schema.org",
      "@type" : "Article",
      "name" : "{{ $news->title }}",
      "author" : {
      "@type" : "Person",
      "name" : "{{ isset($value->user->name) ? $value->user->name : '' }}"
  },
  "datePublished" : "{{ $value->created_at->format('M d, Y') }}",
  "image" : "{{ asset('laravel/public/news/'.$value->image) }}",
  @foreach($news->news_category as $value)
  "articleSection" : "{{ $value->name }}",
  @endforeach
  "articleBody" : "{!! $news->content !!}",
  "aggregateRating" : {
  "@type" : "AggregateRating",
  "ratingCount" : "{{ $news->hit_count }}"
},
"publisher": {
"@type": "Organization",
"name": "Sangri Times",
"logo": {
"@type": "ImageObject",
"url": "https://www.sangritimes.com/public/front/images/sangritimesfavicon.png"
}
}
}
@endif
</script>


<link href='https://fonts.googleapis.com/css?family=Montserrat:400,600,700%7CSource+Sans+Pro:400,600,700' rel='stylesheet'>
<link rel="stylesheet" href="{{ asset('laravel/public/front/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('laravel/public/front/css/font-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('laravel/public/front/css/style.css?v=04-09-2020') }}" />
<script src="{{ asset('laravel/public/front/js/lazysizes.min.js') }}"></script>
</head>
<body class="bg-light style-default style-rounded">
    <!-- Preloader -->
    <div class="loader-mask">
        <div class="loader">
            <div></div>
        </div>
    </div>
    <!-- Bg Overlay -->
    <div class="content-overlay"></div>
    <!-- Sidenav -->
    @include('layouts.front.sidebar')    
    <!-- end sidenav -->
    <main class="main oh" id="main">
        <!-- Top Bar -->
        @include('layouts.front.header') 
        <!-- end navigation -->
        <!-- Trending Now -->
        @yield('content')
        <!-- end main container -->
        <!-- Footer -->
        @include('layouts.front.footer')
        <!-- end footer -->
        <div id="back-to-top">
            <a href="#top" aria-label="Go to top"><i class="ui-arrow-up"></i></a>
        </div>
    </main>
    <!-- end main-wrapper -->
    <!-- jQuery Scripts -->
    <script src="{{ asset('laravel/public/front/js/jquery.min.js') }}"></script>
    <script src="{{ asset('laravel/public/front/js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ asset('laravel/public/front/js/easing.min.js')}}"></script> --}}
    <script src="{{ asset('laravel/public/front/js/owl-carousel.min.js')}}"></script>
    <script src="{{ asset('laravel/public/front/js/flickity.pkgd.min.js') }}"></script>
    {{-- <script src="{{ asset('laravel/public/front/js/twitterFetcher_min.js') }}"></script> --}}
    <script src="{{ asset('laravel/public/front/js/jquery.newsTicker.min.js') }}"></script>  
    <script src="{{ asset('laravel/public/front/js/modernizr.min.js') }}"></script>
    <script src="{{ asset('laravel/public/front/js/scripts.js') }}"></script>
    @include('layouts.notification')
    @yield('scripts')
</body>
</html>