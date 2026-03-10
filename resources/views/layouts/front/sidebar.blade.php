<header class="sidenav" id="sidenav">
    <!-- close -->
    <div class="sidenav__close">
        <button class="sidenav__close-button" id="sidenav__close-button" aria-label="close sidenav">
            <i class="ui-close sidenav__close-icon"></i>
        </button>
    </div>
    <!-- Nav -->
    <nav class="sidenav__menu-container">
        <?php $categories = \App\Category::where('status','1')->orderBy('priority', 'asc')->limit(8)->get(); ?>
        <ul class="sidenav__menu" role="menubar">
            <li><a href="{{ url('/') }}" class="sidenav__menu-url">Home</a></li>
            @if(count($categories) > 0)
            @foreach($categories as $value)
            <li>
                <a href="{{ route($value->slug) }}" class="sidenav__menu-url">{{ $value->name }}</a>
            </li>
            @endforeach
            @endif           
            <li>
                <a href="#" class="sidenav__menu-url">Epaper</a>
            </li>
        </ul>
    </nav>
    <div class="socials sidenav__socials"> 
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
</header>