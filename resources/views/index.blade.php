@extends('layouts.front')
@section('content')
<div class="container">
    <div class="trending-now">
        <span class="trending-now__label">
            <i class="ui-flash"></i>
        Newsflash</span>
        <div class="newsticker">
            <ul class="newsticker__list">
                @if(count($latest_news) > 0)
                @foreach($latest_news as $value)
                <li class="newsticker__item">
                    <a href="{{ route('blog.single', ['slug1' => $value->news_category[0]->slug, 'slug2' => $value->slug]) }}" class="newsticker__item-url">{{ $value->title }}</a>
                </li>
                @endforeach
                @endif
            </ul>
        </div>
        <div class="newsticker-buttons">
            <button class="newsticker-button newsticker-button--prev" id="newsticker-button--prev" aria-label="next article"><i class="ui-arrow-left"></i></button>
            <button class="newsticker-button newsticker-button--next" id="newsticker-button--next" aria-label="previous article"><i class="ui-arrow-right"></i></button>
        </div>
    </div>
</div>
<div class="main-container container pt-24" id="main-container">
    <!-- Content -->
    <div class="row">
        <!-- Posts -->
        <div class="col-lg-8 blog__content">
            <!-- Latest News -->
            <section class="section tab-post mb-16">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">{{ isset($first_cat_news->name) ? $first_cat_news->name : '' }}</h3>
                </div>
                <!-- tab content -->
                <div class="tabs__content tabs__content-trigger tab-post__tabs-content">
                    <div class="tabs__content-pane tabs__content-pane--active" id="tab-all">
                        <div class="row card-row">
                            @if(count($first_cat_news->get_category_news) > 0)
                            @foreach($first_cat_news->get_category_news as $national)
                            <div class="col-md-6">
                                <article class="entry card">
                                    <div class="entry__img-holder card__img-holder">
                                        <a href="{{ route('blog.single', ['slug1' => $first_cat_news->slug, 'slug2' => $national->slug]) }}">
                                            <div class="thumb-container thumb-70">
                                                @if(!empty($national->image))
                                                <img src="{{ asset('laravel/public/news/' . $national->image) }}" class="entry__img lazyload" alt="" />
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                    <div class="entry__body card__body">
                                        <div class="entry__header">
                                            <h2 class="entry__title">
                                                <a href="{{ route('blog.single', ['slug1' => $first_cat_news->slug, 'slug2' => $national->slug]) }}">{{ $national->title }}</a>
                                            </h2>
                                            <ul class="entry__meta">
                                                <?php $user = \App\User::where('id', $national->user_id)->first(); ?>
                                                <li class="entry__meta-author">
                                                    <span>by</span>
                                                    <a href="{{ route('user', $user->username) }}">{{ isset($user->name) ? $user->name : '' }}</a>
                                                </li>
                                                <li class="entry__meta-date">
                                                    {{ $national->created_at->format('M d, Y') }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="entry__excerpt">
                                            {!! substr(strip_tags($national->content), 0, 79) . '...' !!}
                                        </div>
                                    </div>
                                </article>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <!-- end tab content -->            
            </section>
            <!-- end latest news -->
        </div>
        <!-- end posts -->
        <!-- Sidebar -->
        <aside class="col-lg-4 sidebar sidebar--right">
            <!-- Widget Popular Posts -->
            <aside class="widget widget-popular-posts">
                <h4 class="widget-title">Popular Posts</h4>
                @if(count($popular_news) > 0)
                <ul class="post-list-small">
                    @foreach($popular_news as $value)
                    <li class="post-list-small__item">
                        <article class="post-list-small__entry clearfix">
                            <div class="post-list-small__img-holder">
                                <div class="thumb-container thumb-100">
                                    @if(!empty($value->image))
                                    <a href="{{ route('blog.single', ['slug1' => $value->news_category[0]->slug, 'slug2' => $value->slug]) }}">
                                        <img src="{{ asset('laravel/public/news/'.$value->image) }}" alt="" class="post-list-small__img--rounded lazyload">
                                    </a>
                                    @else
                                    <a href="{{ route('blog.single', ['slug1' => $value->news_category[0]->slug, 'slug2' => $value->slug]) }}">
                                        <img src="{{ asset('laravel/public/front/img/empty.png') }}" alt="" class="post-list-small__img--rounded lazyload">
                                    </a>
                                    @endif
                                </div>
                            </div>
                            <div class="post-list-small__body">
                                <h3 class="post-list-small__entry-title">
                                    <a href="{{ route('blog.single', ['slug1' => $value->news_category[0]->slug, 'slug2' => $value->slug]) }}">{{ $value->title }}</a>
                                </h3>
                                <ul class="entry__meta">
                                    <li class="entry__meta-author">
                                        <span>by</span>
                                        <a href="{{ route('user', $value->user->username) }}">{{ isset($value->user->name) ? $value->user->name : '' }}</a>
                                    </li>
                                    <li class="entry__meta-date">
                                        {{ $value->created_at->format('M d, Y') }}
                                    </li>
                                </ul>
                            </div>
                        </article>
                    </li>
                    @endforeach
                </ul>
                @endif
            </aside>
            <!-- end widget popular posts -->
            <!-- Widget Newsletter -->
            <aside class="widget widget_mc4wp_form_widget">
                <h4 class="widget-title">Newsletter</h4>
                <p class="newsletter__text">
                    <i class="ui-email newsletter__icon"></i>
                    Subscribe for our daily news
                </p>
                <form class="mc4wp-form" method="post">
                    <div class="mc4wp-form-fields">
                        <div class="form-group">
                            <input type="email" name="EMAIL" placeholder="Your email" required="">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-lg btn-color" value="Sign Up">
                        </div>
                    </div>
                </form>
            </aside>
            <!-- end widget newsletter -->
            <!-- Widget Socials -->
            <aside class="widget widget-socials">
                <h4 class="widget-title">Let's hang out on social</h4>
                <div class="socials socials--wide socials--large">
                    <div class="row row-16">
                        <div class="col">
                            <a class="social social-facebook" href="#" title="facebook" target="_blank" aria-label="facebook">
                                <i class="ui-facebook"></i>
                                <span class="social__text">Facebook</span>
                            </a><!--
                        --><a class="social social-twitter" href="#" title="twitter" target="_blank" aria-label="twitter">
                            <i class="ui-twitter"></i>
                            <span class="social__text">Twitter</span>
                            </a><!--
                        --><a class="social social-youtube" href="#" title="youtube" target="_blank" aria-label="youtube">
                            <i class="ui-youtube"></i>
                            <span class="social__text">Youtube</span>
                        </a>
                    </div>
                    <div class="col">
                        <a class="social social-google-plus" href="#" title="google" target="_blank" aria-label="google">
                            <i class="ui-google"></i>
                            <span class="social__text">Google+</span>
                            </a><!--
                        --><a class="social social-instagram" href="#" title="instagram" target="_blank" aria-label="instagram">
                            <i class="ui-instagram"></i>
                            <span class="social__text">Instagram</span>
                            </a><!--
                        --><a class="social social-rss" href="#" title="rss" target="_blank" aria-label="rss">
                            <i class="ui-rss"></i>
                            <span class="social__text">Rss</span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>
        <!-- end widget socials -->
    </aside>
    <!-- end sidebar -->
</div>
<!-- end content -->
<!-- Ad Banner 728 -->
<div class="text-center pb-48">
    <a href="#">
        <img src="{{ asset('laravel/public/front/img/content/placeholder_728.jpg') }}" alt="">
    </a>
</div>
<!-- Carousel posts -->
<section class="section mb-0">
    <div class="title-wrap title-wrap--line title-wrap--pr">
        <h3 class="section-title">{{ isset($second_cat_news->name) ? $second_cat_news->name : ''}}</h3>
    </div>
    <!-- Slider -->
    <div id="owl-posts" class="owl-carousel owl-theme owl-carousel--arrows-outside">
        @if(count($second_cat_news->get_category_news) > 0)
        @foreach($second_cat_news->get_category_news as $sport)
        <article class="entry thumb thumb--size-1">
            @if(!empty($sport->image))
            <div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/'. $sport->image) }});">
                @else
                <div class="entry__img-holder thumb__img-holder" style="background-image: url('img/content/carousel/carousel_post_1.jpg');">
                    @endif
                    <div class="bottom-gradient"></div>
                    <div class="thumb-text-holder">
                        <h2 class="thumb-entry-title">
                            <a href="{{ route('blog.single', ['slug1' => $second_cat_news->slug, 'slug2' => $sport->slug]) }}">{{ $sport->title }}</a>
                        </h2>
                    </div>
                    <a href="{{ route('blog.single', ['slug1' => $second_cat_news->slug, 'slug2' => $sport->slug]) }}" class="thumb-url"></a>
                </div>
            </article>
            @endforeach
            @endif
        </div>
        <!-- end slider -->
    </section>
    <!-- end carousel posts -->
    <!-- Posts from categories -->
    <section class="section mb-0">
        <div class="row">
            <!-- Technology -->
            <div class="col-md-6">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">{{ isset($third_cat_news->name) ? $third_cat_news->name : '' }}</h3>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?php $i = 1; ?>
                        @if(count($third_cat_news->get_category_news) > 0)
                        @foreach($third_cat_news->get_category_news as $third)
                        @if($i <= 1)
                        <article class="entry thumb thumb--size-2">
                         @if(!empty($third->image))
                         <div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/'. $third->image) }});">
                            @else
                            <div class="entry__img-holder thumb__img-holder" style="background-image: url('img/content/carousel/carousel_post_1.jpg');">
                                @endif
                                <div class="bottom-gradient"></div>
                                <div class="thumb-text-holder thumb-text-holder--1">
                                    <h2 class="thumb-entry-title">
                                        <a href="{{ route('blog.single', ['slug1' => $third_cat_news->slug, 'slug2' => $third->slug]) }}">{{ $third->title }}</a>
                                    </h2>
                                    <ul class="entry__meta">
                                        <?php $user = \App\User::where('id', $third->user_id)->first(); ?>
                                        <li class="entry__meta-author">
                                            <span>by</span>
                                            <a href="{{ route('user', $user->username) }}">{{ $user->name }}</a>
                                        </li>
                                        <li class="entry__meta-date">
                                            {{ $third->created_at->format('M d, Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('blog.single', ['slug1' => $third_cat_news->slug, 'slug2' => $third->slug]) }}" class="thumb-url"></a>
                            </div>
                        </article>
                        @endif
                        <?php $i++; ?>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <ul class="post-list-small post-list-small--dividers post-list-small--arrows mb-24">
                            <?php $i = 1; ?>
                            @if(count($third_cat_news->get_category_news) > 0)
                            @foreach($third_cat_news->get_category_news as $rajasthan)
                            @if($i > 1)
                            <li class="post-list-small__item">
                                <article class="post-list-small__entry">
                                    <div class="post-list-small__body">
                                        <h3 class="post-list-small__entry-title">
                                            <a href="{{ route('blog.single', ['slug1' => $third_cat_news->slug, 'slug2' => $rajasthan->slug]) }}">{{ $rajasthan->title }}</a>
                                        </h3>
                                    </div>
                                </article>
                            </li>
                            @endif
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end technology -->
            <!-- Travel -->
            <div class="col-md-6">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">{{ isset($fourth_cat_news->name) ? $fourth_cat_news->name : '' }}</h3>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?php $i = 1; ?>
                        @if(count($fourth_cat_news->get_category_news) > 0)
                        @foreach($fourth_cat_news->get_category_news as $fourth)
                        @if($i <= 1)
                        <article class="entry thumb thumb--size-2">
                         @if(!empty($fourth->image))
                         <div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/'. $fourth->image) }});">
                            @else
                            <div class="entry__img-holder thumb__img-holder" style="background-image: url('img/content/carousel/carousel_post_1.jpg');">
                                @endif
                                <div class="bottom-gradient"></div>
                                <div class="thumb-text-holder thumb-text-holder--1">
                                    <h2 class="thumb-entry-title">
                                        <a href="{{ route('blog.single', ['slug1' => $fourth_cat_news->slug, 'slug2' => $fourth->slug]) }}">{{ $fourth->title }}</a>
                                    </h2>
                                    <ul class="entry__meta">
                                        <?php $user = \App\User::where('id', $fourth->user_id)->first(); ?>
                                        <li class="entry__meta-author">
                                            <span>by</span>
                                            <a href="{{ route('user', $user->username) }}">{{ $user->name }}</a>
                                        </li>
                                        <li class="entry__meta-date">
                                            {{ $fourth->created_at->format('M d, Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('blog.single', ['slug1' => $fourth_cat_news->slug, 'slug2' => $fourth->slug]) }}" class="thumb-url"></a>
                            </div>
                        </article>
                        @endif
                        <?php $i++; ?>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <ul class="post-list-small post-list-small--dividers post-list-small--arrows mb-24">
                            <?php $i = 1; ?>
                            @if(count($fourth_cat_news->get_category_news) > 0)
                            @foreach($fourth_cat_news->get_category_news as $business)
                            @if($i > 1)
                            <li class="post-list-small__item">
                                <article class="post-list-small__entry">
                                    <div class="post-list-small__body">
                                        <h3 class="post-list-small__entry-title">
                                            <a href="{{ route('blog.single', ['slug1' => $fourth_cat_news->slug, 'slug2' => $business->slug]) }}">{{ $business->title }}</a>
                                        </h3>
                                    </div>
                                </article>
                            </li>
                            @endif
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end travel -->
            <!-- Technology -->
            <div class="col-md-6">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">{{ isset($five_cat_news->name) ? $five_cat_news->name : '' }}</h3>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?php $i = 1; ?>
                        @if(count($five_cat_news->get_category_news) > 0)
                        @foreach($five_cat_news->get_category_news as $five)
                        @if($i <= 1)
                        <article class="entry thumb thumb--size-2">
                         @if(!empty($five->image))
                         <div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/'. $five->image) }});">
                            @else
                            <div class="entry__img-holder thumb__img-holder" style="background-image: url('img/content/carousel/carousel_post_1.jpg');">
                                @endif
                                <div class="bottom-gradient"></div>
                                <div class="thumb-text-holder thumb-text-holder--1">
                                    <h2 class="thumb-entry-title">
                                        <a href="{{ route('blog.single', ['slug1' => $five_cat_news->slug, 'slug2' => $five->slug]) }}">{{ $five->title }}</a>
                                    </h2>
                                    <ul class="entry__meta">
                                        <?php $user = \App\User::where('id', $five->user_id)->first(); ?>
                                        <li class="entry__meta-author">
                                            <span>by</span>
                                            <a href="{{ route('user', $user->username) }}">{{ $user->name }}</a>
                                        </li>
                                        <li class="entry__meta-date">
                                            {{ $five->created_at->format('M d, Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('blog.single', ['slug1' => $five_cat_news->slug, 'slug2' => $five->slug]) }}" class="thumb-url"></a>
                            </div>
                        </article>
                        @endif
                        <?php $i++; ?>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <ul class="post-list-small post-list-small--dividers post-list-small--arrows mb-24">
                            <?php $i = 1; ?>
                            @if(count($five_cat_news->get_category_news) > 0)
                            @foreach($five_cat_news->get_category_news as $technology)
                            @if($i > 1)
                            <li class="post-list-small__item">
                                <article class="post-list-small__entry">
                                    <div class="post-list-small__body">
                                        <h3 class="post-list-small__entry-title">
                                            <a href="{{ route('blog.single', ['slug1' => $five_cat_news->slug, 'slug2' => $technology->slug]) }}">{{ $technology->title }}</a>
                                        </h3>
                                    </div>
                                </article>
                            </li>
                            @endif
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end technology -->
            <!-- Travel -->
            <div class="col-md-6">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">{{ isset($six_cat_news->name) ? $six_cat_news->name : '' }}</h3>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?php $i = 1; ?>
                        @if(count($six_cat_news->get_category_news) > 0)
                        @foreach($six_cat_news->get_category_news as $six)
                        @if($i <= 1)
                        <article class="entry thumb thumb--size-2">
                         @if(!empty($six->image))
                         <div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/'. $six->image) }});">
                            @else
                            <div class="entry__img-holder thumb__img-holder" style="background-image: url('img/content/carousel/carousel_post_1.jpg');">
                                @endif
                                <div class="bottom-gradient"></div>
                                <div class="thumb-text-holder thumb-text-holder--1">
                                    <h2 class="thumb-entry-title">
                                        <a href="{{ route('blog.single', ['slug1' => $six_cat_news->slug, 'slug2' => $six->slug]) }}">{{ $six->title }}</a>
                                    </h2>
                                    <ul class="entry__meta">
                                        <?php $user = \App\User::where('id', $six->user_id)->first(); ?>
                                        <li class="entry__meta-author">
                                            <span>by</span>
                                            <a href="{{ route('user', $user->username) }}">{{ $user->name }}</a>
                                        </li>
                                        <li class="entry__meta-date">
                                            {{ $six->created_at->format('M d, Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('blog.single', ['slug1' => $six_cat_news->slug, 'slug2' => $six->slug]) }}" class="thumb-url"></a>
                            </div>
                        </article>
                        @endif
                        <?php $i++; ?>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <ul class="post-list-small post-list-small--dividers post-list-small--arrows mb-24">
                            <?php $i = 1; ?>
                            @if(count($six_cat_news->get_category_news) > 0)
                            @foreach($six_cat_news->get_category_news as $fashion)
                            @if($i > 1)
                            <li class="post-list-small__item">
                                <article class="post-list-small__entry">
                                    <div class="post-list-small__body">
                                        <h3 class="post-list-small__entry-title">
                                            <a href="{{ route('blog.single', ['slug1' => $six_cat_news->slug, 'slug2' => $fashion->slug]) }}">{{ $fashion->title }}</a>
                                        </h3>
                                    </div>
                                </article>
                            </li>
                            @endif
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end travel -->
            <!-- Cryptocurrency -->
            <div class="col-md-6">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">{{ isset($seven_cat_news->name) ? $seven_cat_news->name : '' }}</h3>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?php $i = 1; ?>
                        @if(count($seven_cat_news->get_category_news) > 0)
                        @foreach($seven_cat_news->get_category_news as $seven)
                        @if($i <= 1)
                        <article class="entry thumb thumb--size-2">
                         @if(!empty($seven->image))
                         <div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/'.$seven->image) }});">
                            @else
                            <div class="entry__img-holder thumb__img-holder" style="background-image: url('img/content/carousel/carousel_post_1.jpg');">
                                @endif
                                <div class="bottom-gradient"></div>
                                <div class="thumb-text-holder thumb-text-holder--1">
                                    <h2 class="thumb-entry-title">
                                        <a href="{{ route('blog.single', ['slug1' => $seven_cat_news->slug, 'slug2' => $seven->slug]) }}">{{ $seven->title }}</a>
                                    </h2>
                                    <ul class="entry__meta">
                                        <?php $user = \App\User::where('id', $seven->user_id)->first(); ?>
                                        <li class="entry__meta-author">
                                            <span>by</span>
                                            <a href="{{ route('user', $user->username) }}">{{ $user->name }}</a>
                                        </li>
                                        <li class="entry__meta-date">
                                            {{ $seven->created_at->format('M d, Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('blog.single', ['slug1' => $seven_cat_news->slug, 'slug2' => $seven->slug]) }}" class="thumb-url"></a>
                            </div>
                        </article>
                        @endif
                        <?php $i++; ?>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <ul class="post-list-small post-list-small--dividers post-list-small--arrows mb-24">
                            <?php $i = 1; ?>
                            @if(count($seven_cat_news->get_category_news) > 0)
                            @foreach($seven_cat_news->get_category_news as $entertainment)
                            @if($i > 1)
                            <li class="post-list-small__item">
                                <article class="post-list-small__entry">
                                    <div class="post-list-small__body">
                                        <h3 class="post-list-small__entry-title">
                                            <a href="{{ route('blog.single', ['slug1' => $seven_cat_news->slug, 'slug2' => $entertainment->slug]) }}">{{ $entertainment->title }}</a>
                                        </h3>
                                    </div>
                                </article>
                            </li>
                            @endif
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end cryptocurrency -->
            <!-- Investment -->
            <div class="col-md-6">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">{{ isset($eight_cat_news->name) ? $eight_cat_news->name : '' }}</h3>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?php $i = 1; ?>
                        @if(count($eight_cat_news->get_category_news) > 0)
                        @foreach($eight_cat_news->get_category_news as $eight)
                        @if($i <= 1)
                        <article class="entry thumb thumb--size-2">
                         @if(!empty($eight->image))
                         <div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/'. $eight->image) }});">
                            @else
                            <div class="entry__img-holder thumb__img-holder" style="background-image: url('img/content/carousel/carousel_post_1.jpg');">
                                @endif
                                <div class="bottom-gradient"></div>
                                <div class="thumb-text-holder thumb-text-holder--1">
                                    <h2 class="thumb-entry-title">
                                        <a href="{{ route('blog.single', ['slug1' => $eight_cat_news->slug, 'slug2' => $eight->slug]) }}">{{ $eight->title }}</a>
                                    </h2>
                                    <ul class="entry__meta">
                                        <?php $user = \App\User::where('id', $eight->user_id)->first(); ?>
                                        <li class="entry__meta-author">
                                            <span>by</span>
                                            <a href="{{ route('user', $user->username) }}">{{ $user->name }}</a>
                                        </li>
                                        <li class="entry__meta-date">
                                            {{ $eight->created_at->format('M d, Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <a href="{{ route('blog.single', ['slug1' => $eight_cat_news->slug, 'slug2' => $eight->slug]) }}" class="thumb-url"></a>
                            </div>
                        </article>
                        @endif
                        <?php $i++; ?>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <ul class="post-list-small post-list-small--dividers post-list-small--arrows mb-24">
                            <?php $i = 1; ?>
                            @if(count($eight_cat_news->get_category_news) > 0)
                            @foreach($eight_cat_news->get_category_news as $politics)
                            @if($i > 1)
                            <li class="post-list-small__item">
                                <article class="post-list-small__entry">
                                    <div class="post-list-small__body">
                                        <h3 class="post-list-small__entry-title">
                                            <a href="{{ route('blog.single', ['slug1' => $eight_cat_news->slug, 'slug2' => $politics->slug]) }}">{{ $politics->title }}</a>
                                        </h3>
                                    </div>
                                </article>
                            </li>
                            @endif
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end investment -->
        </div>
    </section>
    <!-- end posts from categories -->
    <!-- Content Secondary -->
    <div class="row">
        <!-- Posts -->
        <div class="col-lg-8 blog__content mb-72">
            <!-- Worldwide News -->
            <section class="section">
                <div class="title-wrap title-wrap--line">
                    <h3 class="section-title">Latest</h3>
                    <a href="#" class="all-posts-url">View All</a>
                </div>
                @if(count($latest_news_bottom) > 0)
                @foreach($latest_news_bottom as $value)
                <article class="entry card post-list">
                    @if(!empty($value->image))
                    <div class="entry__img-holder post-list__img-holder card__img-holder" style="background-image: url({{ asset('laravel/public/news/'. $value->image) }})"> 
                        @else
                        <div class="entry__img-holder post-list__img-holder card__img-holder" style="background-image: url(img/content/list/list_post_1.jpg)"> 
                            @endif                
                            <a href="#" class="entry__meta-category entry__meta-category--label entry__meta-category--align-in-corner entry__meta-category--blue">{{ isset($value->news_category[0]->name) ? $value->news_category[0]->name : '' }}</a>
                        </div>
                        <div class="entry__body post-list__body card__body">
                            <div class="entry__header">
                                <h2 class="entry__title">
                                    <a href="{{ route('blog.single', ['slug1' => $value->news_category[0]->slug, 'slug2' => $value->slug]) }}">{{ $value->title }}</a>
                                </h2>
                                <ul class="entry__meta">
                                    <li class="entry__meta-author">
                                        <span>by</span>
                                        <a href="{{ route('user', $value->user->username) }}">{{ isset($value->user->name) ? $value->user->name : '' }}</a>
                                    </li>
                                    <li class="entry__meta-date">
                                        {{ $value->created_at->format('M d, Y') }}
                                    </li>
                                </ul>
                            </div>
                            <div class="entry__excerpt">
                                {!! substr(strip_tags($value->content), 0, 79) . '...' !!}
                            </div>
                        </div>
                    </article>
                    @endforeach
                    @endif
                </section>
                <!-- end worldwide news -->
                <!-- Pagination -->
                <nav class="pagination">
                    <span class="pagination__page pagination__page--current">1</span>
                    <a href="#" class="pagination__page">2</a>
                    <a href="#" class="pagination__page">3</a>
                    <a href="#" class="pagination__page">4</a>
                    <a href="#" class="pagination__page pagination__icon pagination__page--next"><i class="ui-arrow-right"></i></a>
                </nav>
            </div>
            <!-- end posts -->
            <!-- Sidebar 1 -->
            <aside class="col-lg-4 sidebar sidebar--1 sidebar--right">
                <!-- Widget Ad 300 -->
                <aside class="widget widget_media_image">
                    <a href="#">
                        <img src="{{ asset('laravel/public/front/img/content/placeholder_336.jpg') }}" alt="">
                    </a>
                </aside>
                <!-- end widget ad 300 -->
                <!-- Widget Recommended (Rating) -->
                <aside class="widget widget-rating-posts">
                    <h4 class="widget-title">Recommended</h4>
                    @if(count($random_news) > 0)
                    @foreach($random_news as $value)
                    <article class="entry">
                        <div class="entry__img-holder">
                            <a href="{{ route('blog.single', ['slug1' => $value->news_category[0]->slug, 'slug2' => $value->slug]) }}">
                                <div class="thumb-container thumb-60">
                                   @if(!empty($value->image))
                                   <img src="{{ asset('laravel/public/news/'.$value->image) }}" class="entry__img lazyload" alt="">
                                   @else
                                   <img src="{{ asset('laravel/public/front/img/empty.png') }}" class="entry__img lazyload" alt="">
                                   @endif
                               </div>
                           </a>
                       </div>
                       <div class="entry__body">
                        <div class="entry__header">
                            <h2 class="entry__title">
                                <a href="{{ route('blog.single', ['slug1' => $value->news_category[0]->slug, 'slug2' => $value->slug]) }}">{{ $value->title }}</a>
                            </h2>
                            <ul class="entry__meta">
                                <li class="entry__meta-author">
                                    <span>by</span>
                                    <a href="{{ route('user', $value->user->username) }}">{{ isset($value->user->name) ? $value->user->name : '' }}</a>
                                </li>
                                <li class="entry__meta-date">
                                    {{ $value->created_at->format('M d, Y') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </article>
                @endforeach
                @endif
            </aside>
            <!-- end widget recommended (rating) -->
        </aside>
        <!-- end sidebar 1 -->
    </div>
    <!-- content secondary -->      
</div>
@endsection