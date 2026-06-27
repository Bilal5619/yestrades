<script>
  'use strict';
  const baseURL = "{{ url('/') }}";
  const all_model = "{{ __('All') }}";
  const read_more = "{{ __('Read More') }}";
  const read_less = "{{ __('Read Less') }}";
  const show_more = "{{ __('Show More') . '+' }}";
  const show_less = "{{ __('Show Less') . '-' }}";
  const langDir = "{{ $currentLanguageInfo->direction }}";
  var vapid_public_key = "{!! env('VAPID_PUBLIC_KEY') !!}";
  let bookingUnableText =
    "{{ __('We regret to inform you that the service you are trying to book is currently unavailable. Please contact our support team for further assistance') }}";
</script>
@php
  $frontendAsset = function ($path) {
      $publicPath = public_path($path);
      $version = is_file($publicPath) ? filemtime($publicPath) : time();

      return asset($path) . '?v=' . $version;
  };
@endphp
<!-- Jquery JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/jquery.min.js') }}"></script>
<!-- Bootstrap JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/bootstrap.min.js') }}"></script>
<!-- Data Tables JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/datatables.min.js') }}"></script>
<!-- Date-range Picker JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/moment.min.js') }}"></script>
<script src="{{ $frontendAsset('assets/frontend/js/vendors/daterangepicker.js') }}"></script>
<!-- Nice Select JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/jquery.nice-select.min.js') }}"></script>
<!-- Magnific Popup JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/jquery.magnific-popup.min.js') }}"></script>
<!-- Calendar js -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/pignose.calendar.full.min.js') }}"></script>
<!-- Swiper Slider JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/swiper-bundle.min.js') }}"></script>
<!-- Lazysizes -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/lazysizes.min.js') }}"></script>
<!-- Noui Range Slider JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/nouislider.min.js') }}"></script>
<!-- Twinmax JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/tweenMax.min.js') }}"></script>
<!-- Simple Parallax JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/parallax.min.js') }}"></script>
<!-- AOS JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/aos.min.js') }}"></script>
<!-- Mouse Hover JS -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/mouse-hover-move.js') }}"></script>
<!-- toastr js -->
<script src="{{ $frontendAsset('assets/frontend/js/toastr.min.js') }}"></script>
<script src="{{ $frontendAsset('assets/frontend/js/custom-notifications.js') }}"></script>
<!-- Stepper js -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/bs-stepper.min.js') }}"></script>
<!-- syotimer js -->
<script src="{{ $frontendAsset('assets/frontend/js/vendors/jquery.syotimer.min.js') }}"></script>
<!-- Main script JS -->
<script src="{{ $frontendAsset('assets/frontend/js/script.js') }}"></script>
<script src="{{ $frontendAsset('assets/frontend/js/svg-loader.min.js') }}"></script>
<!--Start of Tawk.to Script-->
@if ($basicInfo->tawkto_status == 1)
  <script src="{{ $frontendAsset('assets/frontend/js/tawk-to.js') }}"></script>
  <script type="text/javascript">
    var Tawk_API = Tawk_API || {},
      Tawk_LoadStart = new Date();

    (function() {
      var s1 = document.createElement("script"),
        s0 = document.getElementsByTagName("script")[0];
      s1.async = true;
      s1.src = 'https://embed.tawk.to/{{ $basicInfo->tawkto_direct_chat_link }}/default';
      s1.charset = 'UTF-8';
      s1.setAttribute('crossorigin', '*');
      s0.parentNode.insertBefore(s1, s0);
    })();
  </script>
@endif
<!--End of Tawk.to Script-->

@yield('script')
@if (session()->has('success'))
  <script>
    "use strict";
    toastr['success']("{{ __(session('success')) }}");
  </script>
@endif

@if (session()->has('error'))
  <script>
    "use strict";
    toastr['error']("{{ __(session('error')) }}");
  </script>
@endif
@if (session()->has('warning'))
  <script>
    "use strict";
    toastr['warning']("{{ __(session('warning')) }}");
  </script>
@endif
