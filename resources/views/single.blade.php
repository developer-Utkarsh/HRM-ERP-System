@extends('layouts.front')
@section('content')
@include('include/page-header')
<div class="main-container container" id="main-container">
	<div class="row">
		<div class="col-lg-8 blog__content mb-72">
			<div class="content-box">
				<article class="entry mb-0">
					<div class="single-post__entry-header entry__header">
						@foreach($news->news_category as $value)
						<a href="{{ route($value->slug) }}" class="entry__meta-category entry__meta-category--label entry__meta-category--green">{{ $value->name }}</a>
						@endforeach
						<h1 class="single-post__entry-title">
							{{ $news->title }}
						</h1>
						<div class="entry__meta-holder">
							<ul class="entry__meta">
								<li class="entry__meta-author">
									<span>by</span>
									<a href="{{ route('user', $news->user->username) }}">{{ isset($news->user->name) ? $news->user->name : '' }}</a>
								</li>
								<li class="entry__meta-date">
									{{ $news->created_at->format('M d, Y') }}
								</li>
							</ul>
							<ul class="entry__meta">
								<li class="entry__meta-views">
									<i class="ui-eye"></i>
									<span>{{ $news->hit_count }}</span>
								</li>
							</ul>
						</div>
					</div>
					@if(!empty($news->image))
					<div class="entry__img-holder">
						<img src="{{ asset('laravel/public/news/' . $news->image) }}" alt="" class="entry__img">
					</div>
					@else
					<div class="entry__img-holder">
						<img src="{{ asset('laravel/public/front/img/content/single/single_post_featured_img.jpg') }}" alt="" class="entry__img">
					</div>
					@endif
					<div class="entry__article-wrap">
						<div class="entry__article" style="padding-left: 0px; ">
							{!! $news->content !!}
							<div class="sharethis-inline-share-buttons"></div>
							@if(count($news->has_tags) > 0)							
							<div class="entry__tags">
								<i class="ui-tags"></i>
								<span class="entry__tags-label">Topics:</span>
								@foreach($news->has_tags as $value)
								<a href="{{ route('topic', strtolower($value->tags)) }}" rel="tag">{{ str_replace("-", " ", $value->tags) }}</a>
								@endforeach
							</div>
							@endif
							<br>
							<p>For Latest Update from <strong>Sangri Times News</strong> Like us on <a title="Facebook" href="https://www.facebook.com/sangritimes/" target="_blank" rel="noopener">Facebook</a> and Follow on <a title="Twitter" href="https://www.twitter.com/sangritimes/" target="_blank" rel="noopener">Twitter</a>. <br />Fore Latest Video News Subscribe our channel on <a title="sangritv" href="https://www.youtube.com/SangriTV" target="_blank" rel="noopener">YOUTUBE</a><p></div>
					</div> 
					<div class="newsletter-wide">
						<div class="widget widget_mc4wp_form_widget">
							<div class="newsletter-wide__container">
								<div class="newsletter-wide__text-holder">
									<p class="newsletter-wide__text">
										<i class="ui-email newsletter__icon"></i>
										Subscribe for our daily news
									</p>
								</div>
								<div class="newsletter-wide__form">
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
								</div> 
							</div>         
						</div>
					</div> <!-- end newsletter wide -->

					<!-- Related Posts -->
					<section class="section related-posts mt-40 mb-0">
						<div class="title-wrap title-wrap--line title-wrap--pr">
							<h3 class="section-title">Related Articles</h3>
						</div>

						<!-- Slider -->
						<div id="owl-posts-3-items" class="owl-carousel owl-theme owl-carousel--arrows-outside">
							@if(count($related_news->get_category_news) > 0)
							@foreach($related_news->get_category_news as $value)
							<article class="entry thumb thumb--size-1">
								@if(!empty($value->image))
								<div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/news/' . $value->image) }});">
									@else
									<div class="entry__img-holder thumb__img-holder" style="background-image: url({{ asset('laravel/public/front/img/content/carousel/carousel_post_1.jpg')  }});">
										@endif
										<div class="bottom-gradient"></div>
										<div class="thumb-text-holder">   
											<h2 class="thumb-entry-title">
												<a href="{{ route('blog.single', ['slug1' => $related_news->slug, 'slug2' => $value->slug]) }}">{{ $value->title }}</a>
											</h2>
										</div>
										<a href="{{ route('blog.single', ['slug1' => $related_news->slug, 'slug2' => $value->slug]) }}" class="thumb-url"></a>
									</div>
								</article>
								@endforeach
								@endif
							</div>
						</section>
					</article> 
				</div> 
			</div>
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

	@section('scripts')
	<script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=5e1ed2d1f9d66200125f89bc&product=inline-share-buttons' async='async'></script>
	@endsection