<!-- Header-area start -->
<header class="header-area header-2 @if(!request()->routeIs('index')) header-static @endif" data-aos="fade-down">
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
        <a href="{{ route('index') }}" target="_self" title="{{ $websiteInfo->website_title ?? 'yestrades' }}" class="header-wordmark">
          <span class="header-wordmark-text">yestrades</span>
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
        <a class="navbar-brand header-wordmark" href="{{ route('index') }}" target="_self" title="{{ $websiteInfo->website_title ?? 'yestrades' }}">
          <span class="header-wordmark-text">yestrades</span>
        </a>
        <!-- Navigation items -->
        <div class="collapse navbar-collapse">
          <ul id="mainMenu" class="navbar-nav mobile-item mx-auto">
            <li class="nav-item">
              <a href="{{ route('frontend.services') }}" class="nav-link">{{ __('Services') }}</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('frontend.landlords') }}" class="nav-link">{{ __('Landlords') }}</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('index') }}#reviews" class="nav-link">{{ __('Reviews') }}</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('contact') }}" class="nav-link">{{ __('Contact') }}</a>
            </li>
          </ul>
        </div>

        <div class="more-option mobile-item">
          <!-- div class="item">
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
          </div -->
          <!-- @php
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
          @php
            $whatsappNumber = !empty($basicInfo->whatsapp_number) ? preg_replace('/\D/', '', $basicInfo->whatsapp_number) : null;
          @endphp
          @if ($whatsappNumber)
            <div class="item">
              <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="nav-whatsapp-link">
                <i class="fab fa-whatsapp"></i> {{ __('WhatsApp') }}
              </a>
            </div>
          @endif
          <div class="item">
            @if (Auth::guard('web')->check() || Auth::guard('vendor')->check())
              <div class="dropdown nav-account-menu">
                <button class="nav-sign-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  @if (Auth::guard('web')->check())
                    {{ Auth::guard('web')->user()->username }}
                  @else
                    {{ Auth::guard('vendor')->user()->username }}
                  @endif
                  <i class="fal fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  @if (Auth::guard('web')->check())
                    <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">{{ __('Customer dashboard') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('user.logout') }}">{{ __('Customer logout') }}</a></li>
                  @endif

                  @if (Auth::guard('vendor')->check())
                    <li><a class="dropdown-item" href="{{ route('vendor.dashboard') }}">{{ __('Business dashboard') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('vendor.logout') }}">{{ __('Business logout') }}</a></li>
                  @endif
                </ul>
              </div>
            @else
              <div class="dropdown nav-account-menu">
                <button class="nav-sign-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  {{ __('Sign in') }}
                  <i class="fal fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="{{ route('user.login') }}">{{ __('Customer sign in') }}</a></li>
                  <li><a class="dropdown-item" href="{{ route('vendor.login') }}">{{ __('Business sign in') }}</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="{{ route('user.signup') }}">{{ __('Create account') }}</a></li>
                </ul>
              </div>
            @endif
          </div>
          <div class="item">
            @php
              $serviceCart = collect(session()->get('serviceCart', []));
              $serviceCartCount = $serviceCart->count();
            @endphp
            <a href="{{ route('frontend.services.cart') }}" class="nav-book-btn">
              <i class="fal fa-shopping-cart"></i>
              {{ __('Service cart') }}
              @if ($serviceCartCount > 0)
                <span class="nav-cart-count">{{ $serviceCartCount }}</span>
              @endif
            </a>
          </div>
        </div>
      </nav>
    </div>
  </div>
</header>
<!-- Header-area end -->
