<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Robots" content="INDEX,FOLLOW">
    <meta name="Googlebot-News" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Favicon and touch icons -->
  <link rel="shortcut icon" href="{{ asset('front/images/sangritimesfavicon.png') }}" type="image/x-icon">
  <link rel="apple-touch-icon" type="image/x-icon" href="assets/images/ico/apple-touch-icon-57-precomposed.png') }}">
  <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="{{ asset('front/assets/images/ico/apple-touch-icon-72-precomposed.png') }}">
  <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="{{ asset('front/assets/images/ico/apple-touch-icon-114-precomposed.png') }}">
  <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="{{ asset('front/assets/images/ico/apple-touch-icon-144-precomposed.png') }}">
  <link href='https://www.sangritimes.com/' rel='openid.delegate'/>
  <meta name="description" content="Sangri Times is Hindi News Media website which covers the latest news in National, Politics, Rajasthan and many more categories.">
  <meta name="keywords" content="latest news, breaking news, India news, Sangri Times, sangritimesnews , News Analysis, sangri times hindi, Rajasthan News, current headlines, Sangri times india, news online, breaking news online, latest news headlines, live news online, hot topics, science, technology, lifestyle, world, business, photos, entertainment,local news, jaipur news" name="keywords">
  <meta name="news_keywords" content="Breaking News in Hindi,Hindi News, Latest News in Hindi,हिंदी में समाचार, हिन्दी समाचार, हिंदी ब्रेकिंग न्यूज, ताज़ा ख़बर, News, Hindi News, News in Hindi, Hindi Samachar " />
  @if(isset($article) && !empty($article))
  <link href='{{ route('blog.single', ['slug1' => $article->article_category[0]->alias, 'slug2' => $article->slug]) }}' rel='canonical'/>
  <link href='{{ asset('blog-images/'.$article->image) }}' rel='image_src'/>
  <meta content='{{ route('blog.single', ['slug1' => $article->article_category[0]->alias, 'slug2' => $article->slug]) }}' property='og:url'/>
  <meta content='{{ $article->heading }}' property='og:title'/>
  <meta content='{!! str_limit($article->content, $limit = 50, $end = '.') !!}' property='og:description'/>
  <meta content='{{ asset('blog-images/'.$article->image) }}' property='og:image'/>
  @endif
  <meta property="og:site_name" content="Sangri Times" />
  <meta property="og:description" content="Sangri Times is Hindi News Media website which covers the latest news in National, Politics, Rajasthan and many more categories." />
  <meta property="og:type" content="website" />
  <meta name="google-site-verification" content="agEnzsLrFUTeDmb_ryWptC5zbyMf6R6KMi6zJs2gkWo" />
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
        "url": "https://www.sangritimes.com/public/front/images/sangritimesfavicon.png"
    },
    "url": "https://www.sangritimes.com/",
    "name": "Sangri Times English",
    "description": "English News Portal"
}</script>
<script type="application/ld+json">{
    "@context": "http://schema.org/",
    "@type": "WebSite",
    "name": "Sangri Times",
    "alternateName": "Sangri Times News Portal",
    "url": "https://www.sangritimes.com/"
}</script>
<script type="application/ld+json">
  @if(isset($article) && !empty($article))
{
  "@context" : "http://schema.org",
  "@type" : "Article",
  "name" : "{{ $article->heading }}",
  "author" : {
    "@type" : "Person",
    "name" : "Sangri Internet"
  },
  "datePublished" : "{{ $article->created_at->format('F d, Y') }}",
  "image" : "{{ asset('blog-images/'.$article->image) }}",
@foreach($article->article_category as $value)
  "articleSection" : "{{ $value->name }}",
  @endforeach
  "articleBody" : "{!! $article->content !!}",
  "aggregateRating" : {
    "@type" : "AggregateRating",
    "ratingCount" : "{{ $article->hit_count }}"
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

    <!-- Scripts -->
    <script src="{{ asset('laravel/public/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('laravel/public/css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>
</html>
