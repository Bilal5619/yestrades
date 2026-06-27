<!DOCTYPE html>
<html lang="xxx" dir="{{ $currentLanguageInfo->direction == 1 ? 'rtl' : '' }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="KreativDev">

  <meta name="keywords" content="@yield('metaKeywords')">
  <meta name="description" content="@yield('metaDescription')">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Title -->
  <title>@yield('pageHeading') {{ '| ' . $websiteInfo->website_title }}</title>
  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/img/11done-icon.png') }}" type="image/x-icon">
  <link rel="apple-touch-icon" href="{{ asset('assets/img/11done-icon.png') }}">
  <!-- Latest Font Awesome -->
  <!-- Font Awesome is loaded in partials/styles.blade.php -->

  <!-- Google font -->
  <link rel="stylesheet" href="{{ asset('assets/frontend/css/font.css') }}">
  @php
    $primaryColor = $basicInfo->primary_color;
    $secondaryColor = $basicInfo->secondary_color;

    // check, whether color has '#' or not, will return 0 or 1
    if (!function_exists('checkColorCode')) {
        function checkColorCode($color)
        {
            return preg_match('/^#[a-f0-9]{6}/i', $color);
        }
    }

    // if, primary color value does not contain '#', then add '#' before color value
    if (isset($primaryColor) && checkColorCode($primaryColor) == 0) {
        $primaryColor = '#' . $primaryColor;
    }
    // if, secondary color value does not contain '#', then add '#' before color value
    if (isset($secondaryColor) && checkColorCode($secondaryColor) == 0) {
        $secondaryColor = '#' . $secondaryColor;
    }

    // change decimal point into hex value for opacity
    if (!function_exists('rgb')) {
        function rgb($color = null)
        {
            if (!$color) {
                echo '';
            }
            $hex = htmlspecialchars($color);
            [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
            echo "$r, $g, $b";
        }
    }
  @endphp

  @includeIf('frontend.partials.styles')
  @includeIf('frontend.partials.in-app-browser-compat')
  <style>
    :root {
      --color-primary: {{ $primaryColor }};
      --color-secondary: {{ $secondaryColor }};
      --color-primary-rgb: {{ rgb(htmlspecialchars($primaryColor)) }};
    }
  </style>
</head>


<body dir="{{ $currentLanguageInfo->direction == 1 ? 'rtl' : '' }}">
  {{-- booking loader start --}}
  <div class="request-loader-time" role="status" aria-live="polite" aria-label="{{ __('Loading') }}">
    <div class="request-loader-card">
      <div class="request-loader-mark" aria-hidden="true">
        <span></span>
      </div>
    </div>
  </div>
  @if ($basicInfo->preloader_status == 1)
    <!-- Preloader start -->
    <div id="preLoader" data-preloader-status="{{ $basicInfo->preloader_status }}">
      <div class="eleven-preloader-card" role="status" aria-live="polite" aria-label="{{ __('Loading') }}">
        <div class="eleven-preloader-wordmark" aria-hidden="true">
          <strong>yestrades</strong>
        </div>
        <div class="eleven-preloader-bar" aria-hidden="true">
          <span></span>
        </div>
        <p>{{ __('Getting things ready') }}</p>
      </div>
    </div>
  @endif
  <script>
    // Never let a slow image or third-party SDK leave the site behind the
    // preloader. Facebook's WebView does not always dispatch window.load.
    (function () {
      var timeout = window.isRestrictedInAppBrowser ? 2500 : 7000;

      function dismissPreloader() {
        var preloader = document.getElementById('preLoader');
        if (!preloader) return;

        preloader.style.opacity = '0';
        preloader.style.pointerEvents = 'none';
        preloader.setAttribute('aria-hidden', 'true');
        window.setTimeout(function () {
          if (preloader.parentNode) preloader.parentNode.removeChild(preloader);
        }, 250);
      }

      window.dismissSitePreloader = dismissPreloader;
      window.setTimeout(dismissPreloader, timeout);
      window.addEventListener('pageshow', dismissPreloader, { once: true });
    })();
  </script>
  {{-- booking loader end --}}

  @php
    $theme_version = $basicInfo->theme_version;
  @endphp

  @if ($theme_version == 1)
    @includeIf('frontend.partials.header.header-v1')
  @elseif($theme_version == 2)
    @includeIf('frontend.partials.header.header-v2')
  @elseif($theme_version == 3)
    @includeIf('frontend.partials.header.header-v3')
  @endif



  @if (request()->routeIs('index'))
  @endif

  @yield('breadcrumb')

  @yield('content')
  <div id="razorPayForm"></div>
  @includeIf('frontend.partials.popups')

  @includeIf('frontend.partials.footer')

  {{-- cookie alert --}}
  @if (!is_null($cookieAlertInfo) && $cookieAlertInfo->cookie_alert_status == 1)
    @include('cookie-consent::index')
  @endif

  @php
    $serviceCartItems = collect(session()->get('serviceCart', []));
    $serviceCartTotal = $serviceCartItems->sum('price');
  @endphp
  @if ($serviceCartItems->count() > 0)
    <div id="cartIconWrapper" class="cartIconWrapper">
      <a href="{{ route('frontend.services.cart') }}" class="d-block" id="cartIcon">
        <div class="cart-length">
          <i class="fal fa-shopping-bag"></i>
          <span class="length totalItems">
            {{ $serviceCartItems->count() }} {{ __('Services') }}
          </span>
        </div>
        <div class="cart-total">
          {{ $basicInfo->base_currency_symbol_position == 'left' ? $basicInfo->base_currency_symbol : '' }}<span
            class="totalPrice">{{ number_format($serviceCartTotal, 2) }}</span>{{ $basicInfo->base_currency_symbol_position == 'right' ? $basicInfo->base_currency_symbol : '' }}
        </div>
      </a>
    </div>
  @endif
  @includeIf('frontend.partials.scripts')
  @includeIf('frontend.partials.toastr')
  <script>
    (function () {
      var storageKey = 'visitor_uuid';
      var cookieName = 'visitor_uuid';
      var visitorUuid = null;

      function getCookie(name) {
        var parts = document.cookie ? document.cookie.split('; ') : [];
        for (var i = 0; i < parts.length; i++) {
          var pair = parts[i].split('=');
          if (pair[0] === name) {
            return decodeURIComponent(pair.slice(1).join('='));
          }
        }
        return null;
      }

      function setCookie(name, value) {
        var expires = new Date();
        expires.setFullYear(expires.getFullYear() + 1);
        document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
      }

      function createUuid() {
        if (window.crypto && typeof window.crypto.randomUUID === 'function') {
          return window.crypto.randomUUID();
        }

        return 'visitor-' + Date.now() + '-' + Math.random().toString(16).slice(2);
      }

      try {
        visitorUuid = localStorage.getItem(storageKey) || getCookie(cookieName);
        if (!visitorUuid) {
          visitorUuid = createUuid();
        }
        localStorage.setItem(storageKey, visitorUuid);
        setCookie(cookieName, visitorUuid);
      } catch (error) {
        visitorUuid = getCookie(cookieName) || createUuid();
        setCookie(cookieName, visitorUuid);
      }

      var params = new URLSearchParams(window.location.search || '');
      var payload = {
        visitor_uuid: visitorUuid,
        page_url: window.location.href,
        page_title: document.title || '',
        referrer: document.referrer || '',
        utm_source: params.get('utm_source') || '',
        utm_medium: params.get('utm_medium') || '',
        utm_campaign: params.get('utm_campaign') || '',
        screen_width: window.screen ? window.screen.width : null,
        screen_height: window.screen ? window.screen.height : null
      };

      fetch(@json(route('track.visitor')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
        },
        body: JSON.stringify(payload),
        credentials: 'same-origin',
        keepalive: true
      }).catch(function () {});
    })();
  </script>
  @stack('scripts')
</body>

</html>
