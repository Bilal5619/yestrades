<!-- Header-area start -->
<header class="header-area header-1 bg-white @if(!request()->routeIs('index')) header-static @endif" data-aos="fade-down">
  <!-- Start mobile menu -->
  <div class="mobile-menu">
    <div class="container">
      <div class="mobile-menu-wrapper"></div>
    </div>
  </div>
  <!-- End mobile menu -->

  <div class="main-responsive-nav">
    <div class="container">
      <!-- Mobile Logo -->
      <div class="logo">
        <a href="{{ route('index') }}" target="_self" title="{{ $websiteInfo->website_title ?? '11Done' }}" class="header-wordmark">
          <img src="{{ asset('assets/img/11done-icon.png') }}" alt="{{ $websiteInfo->website_title ?? '11Done' }}" class="header-logo-img">
          <span class="header-wordmark-text"><span class="header-wordmark-number">11</span><span class="header-wordmark-done">DONE</span></span>
        </a>
      </div>
      <!-- Menu toggle button -->
      <button class="menu-toggler" type="button">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>

  <div class="main-navbar">
    <div class="container">
      <nav class="navbar navbar-expand-lg">
        <!-- Logo -->
        <a class="navbar-brand header-wordmark" href="{{ route('index') }}" target="_self" title="{{ $websiteInfo->website_title ?? '11Done' }}">
          <img src="{{ asset('assets/img/11done-icon.png') }}" alt="{{ $websiteInfo->website_title ?? '11Done' }}" class="header-logo-img">
          <span class="header-wordmark-text"><span class="header-wordmark-number">11</span><span class="header-wordmark-done">DONE</span></span>
        </a>
        <!-- Navigation items -->
        <div class="collapse navbar-collapse">
          @php
            $menuDatas = json_decode($menuInfos);
          @endphp
          <ul id="mainMenu" class="navbar-nav mobile-item mx-auto">
            @foreach ($menuDatas as $menuData)
              @php $href = get_href($menuData) @endphp
              @if (!property_exists($menuData, 'children'))
                <li class="nav-item">
                  <a href="{{ $href }}" class="nav-link toggle">{{ $menuData->text }}</a>
                </li>
              @else
                <li class="nav-item">
                  <a href="{{ $href }}" class="nav-link toggle">{{ $menuData->text }}<i
                      class="fal fa-plus"></i></a>
                  <ul class="menu-dropdown">
                    @php $childMenusDatas = $menuData->children @endphp
                    @foreach ($childMenusDatas as $childMenusData)
                      @php $href = get_href($childMenusData) @endphp
                      <li class="nav-item">
                        <a class="nav-link" href="{{ $href }}">{{ $childMenusData->text }}</a>
                      </li>
                    @endforeach
                  </ul>
                </li>
              @endif
            @endforeach
          </ul>
        </div>
       <div class="more-option mobile-item">
        <!--    @php
            $serviceCart = collect(session()->get('serviceCart', []));
            $serviceCartCount = $serviceCart->count();
            $serviceCartTotal = $serviceCart->sum('price');
            $currencySymbol = $basicInfo->base_currency_symbol ?? '£';
            $currencyPosition = $basicInfo->base_currency_symbol_position ?? 'left';
          @endphp
          <div class="item">
            <a href="{{ route('frontend.services.cart') }}" class="btn btn-outline btn-sm position-relative" title="{{ __('Service Cart') }}">
              <i class="fal fa-shopping-cart"></i>
              @if($serviceCartCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.65rem;">
                  {{ $serviceCartCount }}
                </span>
              @endif
            </a>
          </div> -->
          <div class="item">
            <div class="language">
              <form action="{{ route('change_language') }}" method="GET">
                <select class="niceselect" name="lang_code" onchange="this.form.submit()">
                  @foreach ($allLanguageInfos as $languageInfo)
                    <option value="{{ $languageInfo->code }}"
                      {{ $languageInfo->code == $currentLanguageInfo->code ? 'selected' : '' }}>
                      {{ $languageInfo->name }}
                    </option>
                  @endforeach
                </select>
              </form>
            </div>
          </div>
          <div class="item">
            <div class="dropdown">
              <button class="btn btn-outline btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                @if (!Auth::guard('web')->check())
                  {{ __('Customer') }}
                @else
                  {{ Auth::guard('web')->user()->username }}
                @endif
              </button>
              <ul class="dropdown-menu radius-0">
                @if (!Auth::guard('web')->check())
                  <li><a class="dropdown-item" href="{{ route('user.login') }}">{{ __('Login') }}</a></li>
                  <li><a class="dropdown-item" href="{{ route('user.signup') }}">{{ __('Signup') }}</a></li>
                @else
                  <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a></li>
                  <li><a class="dropdown-item" href="{{ route('user.logout') }}">{{ __('Logout') }}</a></li>
                @endif
              </ul>
            </div>
          </div>
          <div class="item">
            <div class="dropdown">
              <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                @if (!Auth::guard('vendor')->check())
                  {{ __('Vendor') }}
                @else
                  {{ Auth::guard('vendor')->user()->username }}
                @endif
              </button>
              <ul class="dropdown-menu radius-0">
                @if (!Auth::guard('vendor')->check())
                  <li><a class="dropdown-item" href="{{ route('vendor.login') }}">{{ __('Login') }}</a></li>
                  <li><a class="dropdown-item" href="{{ route('vendor.signup') }}">{{ __('Signup') }}</a></li>
                @else
                  <li><a class="dropdown-item" href="{{ route('vendor.dashboard') }}">{{ __('Dashboard') }}</a></li>

                  <li><a class="dropdown-item" href="{{ route('vendor.logout') }}">{{ __('Logout') }}</a></li>
                @endif
              </ul>
            </div>
          </div>
        </div>
      </nav>
    </div>
  </div>
</header>
<!-- Header-area end -->
