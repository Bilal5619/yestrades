<!DOCTYPE html>
<html>

<head>
  {{-- required meta tags --}}
  <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

  {{-- csrf-token for ajax request --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- title --}}
  <title>{{ __('Admin') . ' | ' . $websiteInfo->website_title }}</title>

  {{-- fav icon --}}
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/' . $websiteInfo->favicon) }}">
  <!-- Latest Font Awesome -->


  {{-- include styles --}}
  @includeIf('admin.partials.styles')

  {{-- additional style --}}
  @yield('style')
</head>

@php
  $adminAutoRefreshRoutes = [
    'admin.dashboard',
    'admin.all_appointment',
    'admin.pending_appointment',
    'admin.accepted_appointment',
    'admin.completed_appointment',
    'admin.rejected_appointment',
    'admin.availability_compliance',
    'admin.invoices.index',
    'admin.booking.inquiry',
    'admin.support_tickets',
    'admin.service_managment',
    'admin.shop_management.orders',
    'admin.transaction',
    'admin.vendor_management.registered_vendor',
    'admin.vendor_management.bank_accounts',
    'admin.service_reviews.index',
    'admin.withdraw.withdraw_request',
    'admin.all-featured.service',
    'admin.pending-featured.service',
    'admin.approved-featured.service',
    'admin.rejected-featured.service',
  ];
  $adminAutoRefreshEnabled = false;
  foreach ($adminAutoRefreshRoutes as $adminAutoRefreshRoute) {
    if (request()->routeIs($adminAutoRefreshRoute)) {
      $adminAutoRefreshEnabled = true;
      break;
    }
  }
@endphp

<body data-background-color="{{ $settings->admin_theme_version == 'light' ? 'white' : 'dark' }}">
  {{-- loader start --}}
  <div class="request-loader">
    <img src="{{ asset('assets/img/loader.gif') }}" alt="loader">
  </div>
  {{-- loader end --}}

  <div class="wrapper">
    {{-- top navbar area start --}}
    @includeIf('admin.partials.top-navbar')
    {{-- top navbar area end --}}

    {{-- side navbar area start --}}
    @includeIf('admin.partials.side-navbar')
    {{-- side navbar area end --}}

    <div class="main-panel">
      <div class="content">
        @if ($adminAutoRefreshEnabled)
          <div class="admin-auto-refresh-indicator" data-admin-auto-refresh-indicator>
            <span class="admin-auto-refresh-dot" data-admin-auto-refresh-dot></span>
            <span data-admin-auto-refresh-text>Auto-refresh on</span>
            <span class="admin-auto-refresh-time" data-admin-auto-refresh-time></span>
          </div>
        @endif
        <div class="page-inner" data-admin-refresh-target>
          @yield('content')
        </div>
      </div>

      {{-- footer area start --}}
      @includeIf('admin.partials.footer')
      {{-- footer area end --}}
    </div>
  </div>

  @if ($adminAutoRefreshEnabled)
    <script>
      window.adminAutoRefreshConfig = {
        enabled: true,
        interval: 30000,
        targetSelector: '[data-admin-refresh-target]',
        indicatorSelector: '[data-admin-auto-refresh-indicator]'
      };
    </script>
  @endif

  {{-- include scripts --}}
  @includeIf('admin.partials.scripts')

  {{-- additional script --}}
  @yield('variables')
  @yield('script')
  @stack('scripts')
</body>

</html>
