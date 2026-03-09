<!-- ==================== Header Start Here ==================== -->
<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light"> 
            <a class="navbar-brand logo" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="@lang('Loog')" style="height:38px;width:auto;"></a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
  
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('home') }}" aria-current="page" href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    @php
                        $pages = App\Models\Page::where('tempname',$activeTemplate)->where('is_default',Status::NO)->get();
                    @endphp
                    @foreach($pages as $k => $data)
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="{{ route('pages',[$data->slug]) }}">{{ __($data->name) }}</a>
                        </li>
                    @endforeach
                    <li class="nav-item"> 
                        <a class="nav-link {{ menuActive(['blogs', 'blog.details']) }}" aria-current="page" href="{{ route('blogs') }}">@lang('Blogs')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('api.documentation') }}" aria-current="page" href="{{ route('api.documentation') }}">@lang('Developer Guide')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive(['developer.documentation','api.reference']) }}" aria-current="page" href="{{ route('developer.documentation') }}">@lang('New Docs')</a>
                    </li>
                    @auth
                        <li class="nav-item"> 
                            <a href="{{ route('user.home') }}" class="header-buttons__link nav-link mx-0">@lang('Dashboard')</a>
                        </li>
                    @else 
                        <li class="nav-item"> 
                            <a href="{{ route('user.login') }}" class="header-buttons__link nav-link mx-0">@lang('Login')</a>
                        </li>
                    @endauth
                </ul> 
                  <div class="header-buttons">
                    <a href="{{ route('contact') }}" class="btn btn--white btn--sm outline">@lang('Get In Touch')</a>
                    @include($activeTemplate . 'partials.language') 
                  </div>
              </div> 
          </nav>
      </div>
</header>
<!-- ==================== Header End Here ==================== -->

@push('style')
    <style>
       .navbar-light .navbar-nav .nav-link:focus, .navbar-light .navbar-nav .nav-link:hover{
            color: hsl(var(--white));
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.navbar-nav .nav-item a[href="'+@json(url()->current())+'"]').addClass('active');

            const $navCollapse = $('#navbarSupportedContent');
            const $toggler = $('.navbar-toggler.header-button');

            function hideMobileMenu() {
                if (window.innerWidth >= 992) return;
                if ($navCollapse.hasClass('show')) {
                    if (window.bootstrap && bootstrap.Collapse) {
                        bootstrap.Collapse.getOrCreateInstance($navCollapse[0]).hide();
                    } else {
                        $navCollapse.removeClass('show');
                    }
                }
                $toggler.attr('aria-expanded', 'false');
            }

            // Close menu after selecting a nav item (especially on hash links/pages).
            $navCollapse.on('click', '.nav-link, .header-buttons a', function () {
                hideMobileMenu();
            });

            // Close if user taps outside the opened menu.
            $(document).on('click', function (e) {
                if (window.innerWidth >= 992 || !$navCollapse.hasClass('show')) return;
                const clickedInsideMenu = $(e.target).closest('#navbarSupportedContent, .navbar-toggler.header-button').length > 0;
                if (!clickedInsideMenu) hideMobileMenu();
            });

            // Close on hash navigation for docs anchors.
            $(window).on('hashchange', hideMobileMenu);
        })(jQuery);
    </script>
@endpush