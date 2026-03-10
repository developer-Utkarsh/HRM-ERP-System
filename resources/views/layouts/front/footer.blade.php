<footer class="footer footer--grey">
  <div class="container">
    <div class="footer__widgets">
      <div class="row">

        <div class="col-lg-4 col-md-6">
          <aside class="widget widget-logo">
              <a href="{{ url('/') }}">
                <img src="{{ asset('laravel/public/front/img/logo_default.png') }}" class="logo__img" alt="">
            </a>
            <p class="mt-24">Sangri Times is Hindi News Media website which covers the latest news in National, Politics, Rajasthan, Crime, Sports, Entertainment, Lifestyle, Business, Technology and many more categories.</p>
            <p class="mb-32">
              Contact us: info@sangritimes.com
          </p>
          <div class="socials socials--large socials--white-base socials--rounded mb-24">
              <a href="https://www.facebook.com/sangritimes/" target="blank" class="social social-facebook" aria-label="facebook"><i class="ui-facebook"></i></a>
              <a href="https://twitter.com/sangritimes" class="social social-twitter" target="blank" aria-label="twitter"><i class="ui-twitter"></i></a>
              <a href="https://www.youtube.com/channel/UCRpcWQ4UtHx4_Hy-5HLCCMw" class="social social-youtube" target="blank" aria-label="youtube"><i class="ui-youtube"></i></a>
              <a href="https://www.instagram.com/sangritimes/" class="social social-instagram" target="blank" aria-label="instagram"><i class="ui-instagram"></i></a>
          </div>
      </aside>
  </div>

  <div class="col-lg-4 col-md-6">
      <aside class="widget widget-twitter">
        <h4 class="widget-title">Latest Tweets</h4>
        <div class="tweets-container">
          <div id="tweets"></div>                  
      </div>
  </aside>
</div>

<div class="col-lg-4 col-md-6">
  <aside class="widget widget_categories">
    <h4 class="widget-title">Categories</h4>
    <?php $categories = \App\Category::with('get_category_news')->where('status','1')->orderBy('priority', 'asc')->limit(6)->get(); ?>
    @if(count($categories) > 0)
    <ul>
        @foreach($categories as $value)
        <li><a href="{{ route($value->slug) }}">{{ $value->name }} <span class="categories-count">{{ $value->get_category_news->count() }}</span></a></li>
        @endforeach
    </ul>
    @endif
</aside>
</div>

</div>
</div>    
</div> <!-- end container -->

<div class="footer__bottom footer__bottom--white">
    <div class="container text-center">
      <ul class="footer__nav-menu footer__nav-menu--1">
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><a href="{{ route('about-us') }}">About Us</a></li>        
        <li><a href="{{ route('contact-us') }}">Advertise With Us</a></li>
        <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
        <li><a href="{{ route('team') }}">Editorial Team</a></li>
        <li><a href="{{ route('join-us') }}">Join Us</a></li>        
        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
        <li><a href="{{ route('terms') }}">Terms & Condition</a></li>
        <li><a href="{{ route('legal-info') }}">Legal Info</a></li>
    </ul>
    <p class="copyright">
        © {{ date('Y') }} Sangri Times
    </p>   
</div>            
</div> <!-- end footer bottom -->
</footer>