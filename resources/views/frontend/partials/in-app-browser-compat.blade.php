<script>
  (function () {
    var userAgent = navigator.userAgent || navigator.vendor || '';
    var isInAppBrowser = /FBAN|FBAV|FB_IAB|Instagram|MessengerForiOS/i.test(userAgent);

    window.isRestrictedInAppBrowser = isInAppBrowser;
    if (isInAppBrowser) {
      document.documentElement.classList.add('in-app-browser');
    }
  })();
</script>

<style>
  /* AOS can leave elements transparent when an embedded browser misses its
     observer callback. Content must remain readable without animation. */
  html.in-app-browser [data-aos] {
    opacity: 1 !important;
    transform: none !important;
    transition: none !important;
    animation: none !important;
  }

  /* Use a plain responsive grid for service sliders in constrained WebViews.
     Inline dimensions/transforms added by Swiper are intentionally overridden. */
  html.in-app-browser .service-area .swiper {
    overflow: visible !important;
  }

  html.in-app-browser .service-area .swiper-wrapper {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 24px;
    transform: none !important;
  }

  html.in-app-browser .service-area .swiper-slide {
    width: 100% !important;
    margin: 0 !important;
    transform: none !important;
  }

  html.in-app-browser .service-area .swiper-pagination,
  html.in-app-browser .service-area .slider-btn {
    display: none !important;
  }

  @media (max-width: 991.98px) {
    html.in-app-browser .service-area .swiper-wrapper {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 575.98px) {
    html.in-app-browser .service-area .swiper-wrapper {
      grid-template-columns: 1fr;
    }
  }
</style>

<script>
  (function () {
    if (!window.isRestrictedInAppBrowser) return;

    function enableSafeContent() {
      // Do not rely on AOS/IntersectionObserver to reveal important content.
      Array.prototype.forEach.call(document.querySelectorAll('[data-aos]'), function (element) {
        element.removeAttribute('data-aos');
        element.removeAttribute('data-aos-delay');
        element.removeAttribute('data-aos-duration');
      });

      // Facebook's WebView has had intermittent lazy-load observer failures.
      // Promote service images immediately and retain the local placeholder if
      // the real image cannot be loaded.
      Array.prototype.forEach.call(document.querySelectorAll('.service-area img[data-src]'), function (image) {
        var placeholder = image.getAttribute('src');
        var source = image.getAttribute('data-src');
        if (!source) return;

        image.addEventListener('error', function handleImageError() {
          image.removeEventListener('error', handleImageError);
          if (placeholder) image.setAttribute('src', placeholder);
          image.classList.add('lazyloaded');
        });

        image.setAttribute('loading', 'eager');
        image.setAttribute('decoding', 'async');
        image.setAttribute('src', source);
        image.classList.remove('lazyload');
        image.classList.add('lazyloaded');
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', enableSafeContent, { once: true });
    } else {
      enableSafeContent();
    }

    // A final safety path if DOMContentLoaded is delayed by a third-party SDK.
    window.setTimeout(enableSafeContent, 3000);
  })();
</script>
