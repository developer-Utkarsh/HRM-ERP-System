@extends('layouts.front')
@section('content')
<!-- Breadcrumbs -->
@include('include/page-header')
<div class="main-container container" id="main-container">
	<!-- Content -->
	<div class="row">
		<!-- Posts -->
		<div class="col-lg-8 blog__content mb-72">
			<h1 class="page-title">{{ isset($get_all_news_category->name) ? $get_all_news_category->name : '' }}</h1>
			<div class="row card-row">
				@if(count($get_all_news_category->get_category_news) > 0)
				@foreach($get_all_news_category->get_category_news as $value)
				<div class="col-md-6">
					<article class="entry card">
						<div class="entry__img-holder card__img-holder">
							<a href="{{ route('blog.single', ['slug1' => $get_all_news_category->slug, 'slug2' => $value->slug]) }}">
								<div class="thumb-container thumb-70">
									@if(!empty($value->image))
									<img src="{{ asset('laravel/public/news/'.$value->image) }}" class="entry__img lazyload" alt="" />
									@else
									<img src="{{ asset('laravel/public/front/img/empty.png') }}" class="entry__img lazyload" alt="" />
									@endif
								</div>
							</a>							
						</div>
						<div class="entry__body card__body">
							<div class="entry__header">								
								<h2 class="entry__title">
									<a href="{{ route('blog.single', ['slug1' => $get_all_news_category->slug, 'slug2' => $value->slug]) }}">{{ $value->title }}</a>
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
				</div>
				@endforeach
				@endif
			</div>

			<!-- Pagination -->
			<?php /*
			<nav class="pagination">
				<span class="pagination__page pagination__page--current">1</span>
				<a href="#" class="pagination__page">2</a>
				<a href="#" class="pagination__page">3</a>
				<a href="#" class="pagination__page">4</a>
				<a href="#" class="pagination__page pagination__icon pagination__page--next"><i class="ui-arrow-right"></i></a>
			</nav>
			*/ ?>
		</div> <!-- end posts -->

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
			</aside> <!-- end widget popular posts -->

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
			</aside> <!-- end widget newsletter -->

			<!-- Widget Socials -->
			<aside class="widget widget-socials">
				<h4 class="widget-title">Let's hang out on social</h4>
				<div class="socials socials--wide socials--large">
					<div class="row row-16">
						<div class="col">
							<a class="social social-facebook" href="#" title="facebook" target="_blank" aria-label="facebook">
								<i class="ui-facebook"></i>
								<span class="social__text">Facebook</span>
							</a>
							<a class="social social-twitter" href="#" title="twitter" target="_blank" aria-label="twitter">
								<i class="ui-twitter"></i>
								<span class="social__text">Twitter</span>
							</a>
							<a class="social social-youtube" href="#" title="youtube" target="_blank" aria-label="youtube">
								<i class="ui-youtube"></i>
								<span class="social__text">Youtube</span>
							</a>
						</div>
						<div class="col">
							<a class="social social-google-plus" href="#" title="google" target="_blank" aria-label="google">
								<i class="ui-google"></i>
								<span class="social__text">Google+</span>
							</a>
							<a class="social social-instagram" href="#" title="instagram" target="_blank" aria-label="instagram">
								<i class="ui-instagram"></i>
								<span class="social__text">Instagram</span>
							</a>
							<a class="social social-rss" href="#" title="rss" target="_blank" aria-label="rss">
								<i class="ui-rss"></i>
								<span class="social__text">Rss</span>
							</a>
						</div>                
					</div>            
				</div>
			</aside> <!-- end widget socials -->

		</aside> <!-- end sidebar -->

	</div> <!-- end content -->
</div> <!-- end main container -->
@endsection