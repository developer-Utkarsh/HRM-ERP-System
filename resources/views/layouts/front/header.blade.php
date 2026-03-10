<div class="top-bar d-none d-lg-block">
    <div class="container">
        <div class="row">
            <!-- Top menu -->
            <div class="col-lg-6">
                <ul class="top-menu">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ route('about-us') }}">About Us</a></li>
                    <li><a href="{{ route('contact-us') }}">Advertise</a></li>
                    <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
                </ul>
            </div>
            <!-- Socials -->
            <div class="col-lg-6">
                <div class="socials nav__socials socials--nobase socials--white justify-content-end"> 
                    <a class="social social-facebook" href="https://www.facebook.com/sangritimes/" target="_blank" aria-label="facebook">
                        <i class="ui-facebook"></i>
                    </a>
                    <a class="social social-twitter" href="https://twitter.com/sangritimes" target="_blank" aria-label="twitter">
                        <i class="ui-twitter"></i>
                    </a>
                    <a class="social social-youtube" href="https://www.youtube.com/channel/UCRpcWQ4UtHx4_Hy-5HLCCMw" target="_blank" aria-label="youtube">
                        <i class="ui-youtube"></i>
                    </a>
                    <a class="social social-instagram" href="https://www.instagram.com/sangritimes/" target="_blank" aria-label="instagram">
                        <i class="ui-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end top bar -->        
<!-- Navigation -->
<header class="nav">
    <div class="nav__holder nav--sticky">
        <div class="container relative">
            <div class="flex-parent">
                <!-- Side Menu Button -->
                <button class="nav-icon-toggle" id="nav-icon-toggle" aria-label="Open side menu">
                    <span class="nav-icon-toggle__box">
                        <span class="nav-icon-toggle__inner"></span>
                    </span>
                </button> 
                <!-- Logo -->
                <a href="{{ url('/') }}" class="logo">
                    <img class="logo__img" src="{{ asset('laravel/public/front/img/logo_default.png') }}" alt="logo" width="149">
                </a>
                <!-- Nav-wrap -->
                <nav class="flex-child nav__wrap d-none d-lg-block">
                    <?php $categories = \App\Category::where('status','1')->orderBy('priority', 'asc')->limit(8)->get(); ?>
                    <ul class="nav__menu">
                        <li class="active">
                            <a href="{{ url('/') }}">Home</a>
                        </li>
                        @if(count($categories) > 0)
                        @foreach($categories as $value)
                        <li class="">
                            <a href="{{ route($value->slug) }}">{{ $value->name }}</a>
                        </li>
                        @endforeach
                        @endif
                        <li class="">
                            <a href="https://epaper.sangritimes.com/">Epaper</a>
                        </li>
                    </ul>
                    <!-- end menu -->
                </nav>
                <!-- end nav-wrap -->
                <!-- Nav Right -->
                <div class="nav__right">
                    <!-- Search -->
                    <div class="nav__right-item nav__search">
                        <a href="#" class="nav__search-trigger" id="nav__search-trigger">
                            <i class="ui-search nav__search-trigger-icon"></i>
                        </a>
                        <div class="nav__search-box" id="nav__search-box">
                            <form class="nav__search-form">
                                <input type="text" placeholder="Search an article" class="nav__search-input">
                                <button type="submit" class="search-button btn btn-lg btn-color btn-button">
                                    <i class="ui-search nav__search-icon"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- end nav right -->            
            </div>
            <!-- end flex-parent -->
        </div>
        <!-- end container -->
    </div>
</header>