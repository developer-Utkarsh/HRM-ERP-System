<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto">
        <a class="navbar-brand" href="#">
                    {{-- <div class="brand-logo">
                        <img src="{{ asset('laravel/public/profile/' . Auth::user()->image)}}"/ height="65" width="65" style="margin-top: -22px;">
                      </div> --}}
                      <h2 class="brand-text mb-0" style="padding-left: 1rem;">Studio Assistant</h2>
                    </a>
                  </li>
                </ul>
              </div>
              <div class="shadow-bottom"></div>
              <div class="main-menu-content">
                <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                  <li class="nav-item {{ Request::is('studioassistant') || Request::is('studioassistant/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('studioassistant.dashboard') }}"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span>
                    </a>
                  </li>
                  <li class="nav-item"><a href="#"><i class="feather icon-file"></i><span class="menu-title" data-i18n="User">Time Table Management</span></a>
                    <ul class="menu-content">
                     <li class="{{ Request::is('studioassistant/timetable') ? 'active' : '' }}">
                      <a href="{{ route('studioassistant.timetable.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">View</span></a>
                    </li>                       
                  </ul>
                </li>
              </ul>
            </div>
          </div>