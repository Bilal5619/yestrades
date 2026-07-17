@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ __('Home') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_home }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_home }}
  @endif
@endsection

@section('content')
  {{-- ── Advertisement Slides (above hero) ─────────────────── --}}
  <!-- Home-area start-->
  <section class="home-service-hero">
    <div class="container">
      <div class="home-service-hero-grid">
        <div class="home-service-hero-copy" data-aos="fade-up" data-aos-delay="100">
          <div class="hero-rating-pill">
            <i class="fal fa-check-circle"></i>
            {{ __('Simple online booking for UK services') }}
          </div>

          <h1>
            {{ __('Services done right.') }}
            <span>{{ __('Book online with ease.') }}</span>
          </h1>

          <div class="home-service-hero-media-inline" data-aos="fade-up" data-aos-delay="120">
            <div class="hero-slide-card">
              @if (!empty($heroSliderImages) && $heroSliderImages->count() > 0)
                <div class="swiper heroVisualSliderInline">
                  <div class="swiper-wrapper">
                    @foreach ($heroSliderImages as $slide)
                      <div class="swiper-slide">
                        <img src="{{ asset('assets/img/hero/' . $slide->image) }}" alt="{{ __('yestrades service') }}">
                      </div>
                    @endforeach
                  </div>
                  <div class="swiper-pagination hero-visual-pagination-inline"></div>
                </div>
              @else
                <img src="{{ asset('assets/img/hero/' . @$sectionContent->hero_section_background_img) }}"
                  alt="{{ __('yestrades service') }}">
              @endif

              <div class="hero-arrival-chip">{{ __('On the way - 14 min') }}</div>
            </div>
          </div>

          <p>
            {{ __('Find plumbing, gas, electrical and landlord certificate services with clear prices, simple booking steps and helpful updates from one place.') }}
          </p>

          <div class="hero-search-panel">
            <form id="homepage_search" action="{{ route('frontend.services') }}" method="get">
              <div class="hero-search-row">
                <div class="hero-search-field">
                  <label for="service_name"><i class="fal fa-search"></i></label>
                  <input type="text" id="service_name" name="service_title" placeholder="{{ __('What do you need?') }}">
                </div>

                <button type="submit" class="hero-find-btn">
                  {{ __('Find Now') }} <i class="fal fa-long-arrow-right"></i>
                </button>
              </div>

              <div class="hero-popular-row">
                <span>{{ __('Popular:') }}</span>
                <a href="{{ route('frontend.services', ['service_title' => 'Boiler service']) }}">{{ __('Boiler service') }}</a>
                <a href="{{ route('frontend.services', ['service_title' => 'EICR']) }}">{{ __('EICR') }}</a>
                <a href="{{ route('frontend.services', ['service_title' => 'Blocked drain']) }}">{{ __('Blocked drain') }}</a>
                <a href="{{ route('frontend.services', ['service_title' => 'Oven clean']) }}">{{ __('Oven clean') }}</a>
              </div>
            </form>
          </div>

          <div class="hero-trust-row">
            <span><i class="fal fa-shield-check"></i> {{ __('Gas Safe & NICEIC engineers') }}</span>
            <span><i class="fal fa-clock"></i> {{ __('Same-day available') }}</span>
            <span><i class="fal fa-pound-sign"></i> {{ __('Fixed price, no hidden fees') }}</span>
          </div>
        </div>

        <div class="home-service-hero-media" data-aos="fade-up" data-aos-delay="150">
          <div class="hero-slide-card">
            @if (!empty($heroSliderImages) && $heroSliderImages->count() > 0)
              <div class="swiper heroVisualSlider">
                <div class="swiper-wrapper">
                  @foreach ($heroSliderImages as $slide)
                    <div class="swiper-slide">
                      <img src="{{ asset('assets/img/hero/' . $slide->image) }}" alt="{{ __('yestrades service') }}">
                    </div>
                  @endforeach
                </div>
                <div class="swiper-pagination hero-visual-pagination"></div>
              </div>
            @else
              <img src="{{ asset('assets/img/hero/' . @$sectionContent->hero_section_background_img) }}"
                alt="{{ __('yestrades service') }}">
            @endif

            <div class="hero-arrival-chip">{{ __('Booking in progress') }}</div>
          </div>

          <div class="hero-review-card">
            <div class="hero-review-stars">
              <i class="fal fa-calendar-check"></i>
            </div>
            <p>{{ __('Choose a service, confirm the details and complete your booking online.') }}</p>
            <span>{{ __('Simple booking flow') }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="hero-stat-band">
      <div class="container">
        <div class="hero-stat-grid">
          <div>
            <strong>{{ __('Clear') }}</strong>
            <span>{{ __('Service pricing') }}</span>
          </div>
          <div>
            <strong>{{ __('Secure') }}</strong>
            <span>{{ __('Online checkout') }}</span>
          </div>
          <div>
            <strong>{{ __('Flexible') }}</strong>
            <span>{{ __('Booking details') }}</span>
          </div>
          <div>
            <strong>{{ __('Simple') }}</strong>
            <span>{{ __('Service requests') }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Home-area end -->

  @push('scripts')
    <style>
      .home-service-hero {
        position: relative;
        overflow: hidden;
        padding: 210px 0 0;
        background:
          radial-gradient(circle at 94% 8%, rgba(var(--color-primary-rgb), 0.5), transparent 24%),
          radial-gradient(circle at 8% 92%, rgba(255, 177, 66, 0.18), transparent 28%),
          #071122;
        color: #ffffff;
      }

      .home-service-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
          linear-gradient(135deg, rgba(255, 255, 255, 0.03), transparent 40%),
          linear-gradient(90deg, rgba(7, 17, 34, 0.82), rgba(7, 17, 34, 0.35));
        pointer-events: none;
      }

      .home-service-hero .container {
        position: relative;
        z-index: 1;
      }

      .home-service-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 400px;
        align-items: center;
        gap: 74px;
        min-height: 650px;
        padding-bottom: 84px;
      }

      .home-service-hero-copy {
        max-width: 600px;
      }

      .hero-rating-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        padding: 7px 12px;
        color: #dbe4f5;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
        text-transform: uppercase;
      }

      .hero-rating-pill i,
      .hero-review-stars i,
      .hero-stat-grid i {
        color: #ffc233;
      }

      .home-service-hero h1 {
        margin: 0 0 26px;
        color: #ffffff;
        font-size: 58px;
        line-height: 1.08;
        font-weight: 900;
        letter-spacing: 0;
      }

      .home-service-hero h1 span {
        display: block;
        color: var(--color-primary);
      }

      .home-service-hero-copy > p {
        max-width: 570px;
        margin: 0;
        color: #c8d0df;
        font-size: 16px;
        line-height: 1.75;
      }

      .hero-search-panel {
        max-width: 590px;
        margin-top: 28px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        box-shadow: 0 24px 50px rgba(0, 0, 0, 0.25);
      }

      .hero-search-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr) auto;
        gap: 8px;
      }

      .hero-search-field {
        display: flex;
        align-items: center;
        gap: 9px;
        min-height: 52px;
        padding: 0 14px;
        background: #ffffff;
        border-radius: 8px;
      }

      .hero-search-field label {
        margin: 0;
        color: var(--color-primary);
        font-size: 16px;
        line-height: 1;
      }

      .hero-search-field input {
        width: 100%;
        min-width: 0;
        height: 100%;
        color: #101521;
        background: transparent;
        border: 0;
        outline: 0;
        font-size: 14px;
      }

      .hero-find-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 52px;
        padding: 0 24px;
        color: #ffffff;
        background: var(--color-primary);
        border: 0;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
      }

      .hero-popular-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        color: #9ca8bb;
        font-size: 12px;
      }

      .hero-popular-row a {
        padding: 5px 10px;
        color: #cdd6e7;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        text-decoration: none;
      }

      .hero-trust-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 26px;
        margin-top: 28px;
        color: #b8c2d3;
        font-size: 13px;
      }

      .hero-trust-row i {
        margin-right: 6px;
        color: #18c98b;
      }

      .hero-trust-row span:nth-child(2) i {
        color: #ffc233;
      }

      .home-service-hero-media {
        position: relative;
        max-width: 400px;
      }

      .home-service-hero-media-inline {
        display: none;
      }

      .hero-slide-card {
        position: relative;
        overflow: hidden;
        height: 500px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        box-shadow: 0 32px 70px rgba(0, 0, 0, 0.32);
      }

      .hero-slide-card img,
      .heroVisualSlider,
      .heroVisualSliderInline,
      .heroVisualSlider .swiper-wrapper,
      .heroVisualSliderInline .swiper-wrapper,
      .heroVisualSlider .swiper-slide,
      .heroVisualSliderInline .swiper-slide {
        width: 100%;
        height: 100%;
      }

      .hero-slide-card img {
        display: block;
        object-fit: cover;
      }

      .heroVisualSliderInline:not(.swiper-initialized) .swiper-wrapper {
        display: block;
      }

      .heroVisualSliderInline:not(.swiper-initialized) .swiper-slide {
        display: none;
      }

      .heroVisualSliderInline:not(.swiper-initialized) .swiper-slide:first-child {
        display: block;
      }

      .home-service-hero-media-inline .heroVisualSliderInline {
        position: relative;
        overflow: hidden;
      }

      .home-service-hero-media-inline .heroVisualSliderInline .swiper-wrapper {
        position: relative;
        transform: none !important;
      }

      .home-service-hero-media-inline .heroVisualSliderInline .swiper-slide {
        position: absolute !important;
        inset: 0;
        opacity: 0 !important;
        visibility: hidden !important;
        background: #121d2e;
        transition: opacity 0.6s ease;
      }

      .home-service-hero-media-inline .heroVisualSliderInline .swiper-slide.is-active,
      .home-service-hero-media-inline .heroVisualSliderInline .swiper-slide:first-child {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
      }

      .home-service-hero-media-inline .heroVisualSliderInline img {
        width: 100%;
        height: 100%;
        opacity: 1 !important;
        visibility: visible !important;
      }

      .hero-arrival-chip {
        position: absolute;
        right: 16px;
        bottom: 18px;
        padding: 7px 11px;
        color: #b9ffe4;
        background: rgba(8, 136, 91, 0.72);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        z-index: 2;
      }

      .hero-review-card {
        position: absolute;
        left: -18px;
        bottom: -26px;
        width: 218px;
        padding: 14px 16px;
        color: #111827;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        z-index: 2;
      }

      .hero-review-card p {
        margin: 5px 0 8px;
        color: #374151;
        font-size: 12px;
        line-height: 1.45;
      }

      .hero-review-card span {
        display: block;
        color: #6b7280;
        font-size: 11px;
      }

      .hero-visual-pagination {
        bottom: 14px !important;
      }

      .hero-visual-pagination .swiper-pagination-bullet,
      .hero-visual-pagination-inline .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.8);
      }

      .hero-stat-band {
        position: relative;
        z-index: 1;
        padding: 28px 0;
        background: rgba(4, 10, 20, 0.78);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
      }

      .hero-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 30px;
      }

      .hero-stat-grid strong {
        display: block;
        color: #ffffff;
        font-size: 30px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: 0;
      }

      .hero-stat-grid span {
        display: block;
        margin-top: 7px;
        color: #8390a3;
        font-size: 11px;
        font-weight: 800;
        line-height: 1.2;
        text-transform: uppercase;
      }

      .home-reviews-section {
        padding: 86px 0;
        background: #f8f8f7;
      }

      .home-reviews-head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        gap: 28px;
        margin-bottom: 34px;
      }

      .home-reviews-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding: 7px 12px;
        color: #079767;
        background: #ffffff;
        border: 1px solid #ecebea;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        line-height: 1;
        text-transform: uppercase;
      }

      .home-reviews-head h2 {
        max-width: 620px;
        margin: 0;
        color: #10131a;
        font-size: 40px;
        line-height: 1.12;
        font-weight: 900;
        letter-spacing: 0;
      }

      .home-reviews-summary {
        min-width: 210px;
        padding: 18px 20px;
        text-align: right;
        background: #ffffff;
        border: 1px solid #ecebea;
        border-radius: 8px;
        box-shadow: 0 16px 38px rgba(17, 19, 26, 0.04);
      }

      .home-reviews-summary strong {
        display: block;
        color: #10131a;
        font-size: 30px;
        line-height: 1;
        font-weight: 900;
      }

      .home-reviews-summary strong i {
        margin-left: 4px;
        color: #ffc233;
        font-size: 20px;
      }

      .home-reviews-summary span {
        display: block;
        margin-top: 7px;
        color: #6c7280;
        font-size: 13px;
        font-weight: 700;
      }

      .home-reviews-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
      }

      .home-review-card {
        min-height: 100%;
        padding: 24px;
        background: #ffffff;
        border: 1px solid #ecebea;
        border-radius: 8px;
        box-shadow: 0 16px 38px rgba(17, 19, 26, 0.04);
      }

      .home-review-stars {
        display: flex;
        gap: 3px;
        margin-bottom: 16px;
        color: #ffc233;
        font-size: 14px;
      }

      .home-review-card blockquote {
        margin: 0 0 18px;
        color: #30333b;
        font-size: 15px;
        line-height: 1.7;
      }

      .home-review-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #747b88;
        font-size: 13px;
      }

      .home-review-meta strong {
        display: block;
        color: #10131a;
        font-size: 14px;
        font-weight: 900;
      }

      .home-review-tag {
        padding: 6px 10px;
        color: var(--color-primary);
        background: rgba(var(--color-primary-rgb), 0.08);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
      }

      .home-reviews-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 34px;
      }

      .home-reviews-cta a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 44px;
        padding: 0 18px;
        color: #ffffff;
        background: var(--color-primary);
        border-radius: 8px;
        font-size: 14px;
        font-weight: 900;
        text-decoration: none;
      }

      @media (max-width: 1199.98px) {
        .home-service-hero {
          padding-top: 150px;
        }

        .home-service-hero-grid {
          grid-template-columns: 1fr;
          gap: 44px;
          min-height: 0;
        }

        .home-service-hero-copy,
        .home-service-hero-media {
          max-width: 100%;
        }

        .home-service-hero-media {
          width: min(520px, 100%);
          margin: 0 auto;
        }
      }

      @media (min-width: 768px) and (max-width: 1199.98px) {
        .home-service-hero-grid {
          gap: 28px;
          padding-bottom: 54px;
        }

        .home-service-hero-copy {
          max-width: 100%;
        }

        .home-service-hero h1 {
          font-size: 48px;
          line-height: 1.1;
        }

        .home-service-hero-media-inline {
          display: block;
          width: 100%;
          margin: 0 0 24px;
        }

        .home-service-hero-copy > p {
          max-width: 780px;
        }

        .home-service-hero-media {
          display: none;
        }

        .hero-slide-card {
          height: 360px;
        }

        .hero-search-panel {
          max-width: 100%;
        }
      }

      @media (max-width: 767.98px) {
        .home-service-hero {
          padding-top: 86px;
          overflow: hidden;
        }

        .home-service-hero .container,
        .home-service-hero-copy,
        .hero-search-panel,
        .hero-search-row,
        .hero-search-field {
          max-width: 100%;
          min-width: 0;
        }

        .home-service-hero-grid {
          grid-template-columns: 1fr;
          gap: 24px;
          min-height: 0;
          padding-bottom: 32px;
        }

        .hero-rating-pill {
          margin-bottom: 16px;
          font-size: 10px;
          line-height: 1.25;
          white-space: normal;
        }

        .home-service-hero h1 {
          margin-bottom: 16px;
          font-size: 32px;
          line-height: 1.12;
          overflow-wrap: anywhere;
        }

        .home-service-hero-media-inline {
          display: block;
          width: 100%;
          margin: 0 0 18px;
        }

        .home-service-hero-copy > p {
          max-width: 100%;
          font-size: 14px;
          line-height: 1.65;
          overflow-wrap: anywhere;
        }

        .hero-search-panel {
          max-width: 100%;
          margin-top: 22px;
          padding: 8px;
          overflow: hidden;
        }

        .hero-search-row,
        .hero-stat-grid {
          grid-template-columns: 1fr;
        }

        .hero-search-field,
        .hero-find-btn {
          min-height: 48px;
        }

        .hero-search-field {
          padding: 0 12px;
        }

        .hero-find-btn {
          width: 100%;
        }

        .hero-popular-row {
          flex-wrap: nowrap;
          overflow-x: auto;
          padding-bottom: 2px;
          scrollbar-width: none;
        }

        .hero-popular-row::-webkit-scrollbar {
          display: none;
        }

        .hero-popular-row span,
        .hero-popular-row a {
          flex: 0 0 auto;
        }

        .hero-trust-row {
          display: grid;
          grid-template-columns: 1fr;
          gap: 11px;
          margin-top: 20px;
        }

        .home-service-hero-media {
          display: none;
        }

        .hero-slide-card {
          height: 285px;
        }

        .hero-review-card {
          position: relative;
          left: auto;
          bottom: auto;
          width: 100%;
          margin-top: 12px;
        }

        .hero-stat-band {
          padding: 18px 0;
        }

        .hero-stat-grid {
          grid-template-columns: repeat(2, minmax(0, 1fr));
          gap: 1px;
          overflow: hidden;
          border: 1px solid rgba(255, 255, 255, 0.08);
          border-radius: 8px;
          background: rgba(255, 255, 255, 0.06);
        }

        .hero-stat-grid > div {
          padding: 16px 12px;
          background: rgba(4, 10, 20, 0.38);
        }

        .hero-stat-grid strong {
          font-size: 24px;
        }

        .hero-stat-grid span {
          font-size: 10px;
        }

        .home-reviews-head,
        .home-reviews-grid {
          grid-template-columns: 1fr;
        }

        .home-reviews-summary {
          text-align: left;
        }

        .home-reviews-head h2 {
          font-size: 30px;
        }
      }

      @media (max-width: 575.98px) {
        .home-service-hero {
          padding-top: 82px;
        }

        .home-service-hero h1 {
          font-size: 29px;
        }

        .hero-slide-card {
          height: 225px;
        }

        .hero-review-card {
          display: none;
        }
      }
    </style>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var heroSwipers = [];

        document.querySelectorAll('.heroVisualSlider').forEach(function (slider) {
          var pagination = slider.querySelector('.swiper-pagination');

          var instance = new Swiper(slider, {
            loop: true,
            speed: 800,
            autoplay: { delay: 4200, disableOnInteraction: false },
            effect: 'fade',
            fadeEffect: { crossFade: true },
            observer: true,
            observeParents: true,
            pagination: {
              el: pagination,
              clickable: true,
            },
          });

          heroSwipers.push(instance);
        });

        function refreshHeroSwipers() {
          heroSwipers.forEach(function (swiper) {
            if (swiper && !swiper.destroyed) {
              swiper.update();
            }
          });
        }

        window.addEventListener('load', refreshHeroSwipers);
        window.addEventListener('resize', refreshHeroSwipers);
        setTimeout(refreshHeroSwipers, 250);

        document.querySelectorAll('.heroVisualSliderInline').forEach(function (slider) {
          var slides = Array.prototype.slice.call(slider.querySelectorAll('.swiper-slide'));
          var activeIndex = 0;

          if (!slides.length) {
            return;
          }

          slides.forEach(function (slide, index) {
            slide.classList.toggle('is-active', index === 0);
          });

          if (slides.length > 1) {
            setInterval(function () {
              slides[activeIndex].classList.remove('is-active');
              activeIndex = (activeIndex + 1) % slides.length;
              slides[activeIndex].classList.add('is-active');
            }, 4200);
          }
        });
      });
    </script>
  @endpush
  @if (count($after_hero) > 0)
    @foreach ($after_hero as $cusHero)
      @if (isset($homecusSec[$cusHero->id]))
        @if ($homecusSec[$cusHero->id] == 1)
          @php
            $cusHeroContent = App\Models\CustomSectionContent::where('custom_section_id', $cusHero->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusHeroContent])
        @endif
      @endif
    @endforeach
  @endif

  <!-- Category-area start >
      @if ($secInfo->category_section_status == 1)
        <section class="category-area category-1 ptb-100">
          <div class="container">
            <div class="row">
              <div class="col-12">
                <div class="section-title title-center mb-50" data-aos="fade-up">
                  <h2 class="title mb-0">
                    {{ !empty($sectionContent->category_section_title) ? $sectionContent->category_section_title : 'Most Popular Categories' }}
                  </h2>
                </div>
              </div>
              <div class="col-12" data-aos="fade-up">
                @if (count($categories) == 0)
                  <h4 class="text-center">{{ __('NO CATEGORIES FOUND') }}!</h4>
                @else
                  <div class="swiper category-slider" id="category-slider-1" data-slides-per-view="5"
                    data-swiper-loop="false">
                    <div class="swiper-wrapper">
                      @foreach ($categories as $category)
                        <style>
                          .category-area.category-1 .swiper-slide .card-icon.icon-bg{{ $loop->iteration }} {
                            background-color: #{{ $category->background_color }};
                            box-shadow: 0 15px 30px -12px rgba({{ hexdec(substr($category->background_color, 0, 2)) }}, {{ hexdec(substr($category->background_color, 2, 2)) }}, {{ hexdec(substr($category->background_color, 4, 2)) }}, 0.7);
                          }

                          .category-area.category-1 .swiper-slide .card:hover .card-title.title-hover{{ $loop->iteration }} {
                            color: #{{ $category->background_color }};
                          }
                        </style>
                        <a href="{{ route('frontend.services', ['category' => $category->slug]) }}" target="_self">
                          <div class="swiper-slide">
                            <div class="card p-25 border radius-md">
                              <div class="card-icon icon-bg{{ $loop->iteration }} rounded-pill mb-20">
                                <i class="{{ $category->icon }}"></i>
                              </div>
                              <h4 class="card-title title-hover{{ $loop->iteration }} lc-1 mb-1">
                                <a href="{{ route('frontend.services', ['category' => $category->slug]) }}" target="_self">
                                  {{ $category->name }}
                                </a>
                              </h4>
                              <span class="font-sm">
                                @if ($category->service_count > 1)
                                  {{ $category->service_count }}
                                  {{ __('Services Available') }}
                                @elseif($category->service_count == 1)
                                  {{ $category->service_count }}
                                  {{ __('Service Available') }}
                                @endif
                              </span>
                            </div>
                          </div>
                        </a>
                      @endforeach
                    </div>
                    <div class="swiper-pagination position-static mt-40" id="category-slider-1-pagination"></div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </section>
      @endif -->
  <!-- Category-area end -->

  @if (count($after_category) > 0)
    @foreach ($after_category as $cusCategory)
      @if (isset($homecusSec[$cusCategory->id]))
        @if ($homecusSec[$cusCategory->id] == 1)
          @php
            $cusCategoryContent = App\Models\CustomSectionContent::where('custom_section_id', $cusCategory->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusCategoryContent])
        @endif
      @endif
    @endforeach
  @endif




  {{-- Hidden for now (client request) — uncomment to bring the packages section back --}}
  {{-- @include('frontend.home.renewal-section') --}}
  <!-- Service-area start -->
  @if ($secInfo->latest_service_section_status == 1)
    <section class="service-area service-1 ptb-100 home-services">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section-title title-center mb-50" data-aos="fade-up">
              <h2 class="title mb-20">
                {{ !empty($sectionContent->featured_service_section_title) ? $sectionContent->featured_service_section_title : 'Services We Offer' }}
              </h2>
              @if (count($categories) > 0)
                <div class="tabs-navigation">
                  <ul class="nav nav-tabs" data-hover="fancyHover">
                    <li class="nav-item active">
                      <button class="nav-link hover-effect active btn-md rounded-pill" data-bs-toggle="tab"
                        data-bs-target="#defaultTab" type="button">{{ __('All Services') }}</button>
                    </li>
                    @foreach ($categories as $category)
                      <li class="nav-item">
                        <button class="nav-link hover-effect btn-md rounded-pill" data-bs-toggle="tab"
                          data-bs-target="#tab{{ $category->id }}" type="button">{{ $category->name }}</button>
                      </li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </div>
          </div>
          <div class="col-12 home-grid-services">
            @if (count($services) == 0)
              <h4 class="text-center">{{ __('NO SERVICE FOUND') }}!</h4>
            @else
              <div class="tab-content" data-aos="fade-up">
                <div class="tab-pane fade show active" id="defaultTab">
                  <div class="row">
                    @foreach ($services as $service)
                      <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
                        <div class="product-default border radius-md p-15 mb-25">
                          <figure class="product-img mb-15">
                            <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                              title="Image" target="_self" class="lazy-container radius-sm ratio ratio-2-3">
                              <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="Service">
                            </a>
                          </figure>
                          <div class="product-details">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                              <a href="{{ route('frontend.services', ['category_id' => $service->categoryId]) }}">
                                <span class="tag font-sm">{{ $service->categoryName }}</span>
                              </a>
                              @if (Auth::guard('web')->check())
                                @php
                                  $user_id = Auth::guard('web')->user()->id;
                                  $checkWishList = checkWishList($service->id, $user_id);
                                @endphp
                              @else
                                @php
                                  $checkWishList = false;
                                @endphp
                              @endif
                              <a href="{{ $checkWishList == false ? route('addto.wishlist', $service->id) : route('remove.wishlist', $service->id) }}"
                                class="btn btn-icon border rounded-circle {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                                title="Save to Wishlist">
                                <i class="fal fa-heart"></i>
                              </a>
                            </div>
                            <h6 class="product-title mb-0">
                              <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                                target="_self" title="{{ $service->name }}">
                                {{ truncateString($service->name, 60) }}</a>
                            </h6>
                            <input type="hidden" value="{{ $service->language_id }}">
                            <!--div class="author mb-10 mt-10">
                                              @if ($service->vendor_id != 0)
                                                @if ($service->vendor->photo != null)
                                                  <a href="{{ route('frontend.vendor.details', ['username' => $service->vendor->username]) }}"
                                                    target="_self" title="{{ $service->vendor->username }}">
                                                    <img class="lazyload blur-up"
                                                      src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                      data-src="{{ asset('assets/admin/img/vendor-photo/' . $service->vendor->photo) }}"
                                                      alt="Image">
                                                  </a>
                                                @else
                                                  <a href="{{ route('frontend.vendor.details', ['username' => $service->vendor->username]) }}"
                                                    target="_self" title="{{ $service->vendor->username }}">
                                                    <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                      data-src="{{ asset('assets/img/user.png') }}" alt="Vendor">
                                                  </a>
                                                @endif
                                                <span class="font-sm">
                                                  {{ __('By') }} <a
                                                    href="{{ route('frontend.vendor.details', ['username' => $service->vendor->username]) }}"
                                                    target="_self"
                                                    title="{{ $service->vendor->username }}">{{ $service->vendor->username }}</a>
                                                </span>
                                              @else
                                                <a href="{{ route('frontend.vendor.details', ['username' => $admin->username]) }}"
                                                  target="_self" title="{{ $admin->username }}">
                                                  <img class="lazyload blur-up"
                                                    src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                    data-src="{{ asset('assets/img/admins/' . $admin->image) }}" alt="Image">
                                                </a>
                                                <span class="font-sm">
                                                  {{ __('By') }} <a
                                                    href="{{ route('frontend.vendor.details', ['username' => $admin->username]) }}"
                                                    target="_self" title="{{ $admin->username }}">{{ $admin->username }}</a>
                                                </span>
                                              @endif
                                            </div>
                                            @if (!empty($service->address))
                                              <span class="font-sm icon-start"><i
                                                  class="fal fa-map-marker-alt"></i>{{ truncateString($service->address, 30) }}</span>
                                            @endif

                                            -->





                            @if ($service->zoom_meeting == 1)
                              <span class="font-sm icon-start"><i class="fal fa-video"></i>{{ __('Online') }}</span>
                            @endif
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-10">
                              <div class="product-price">
                                @if ($service->hasPricePlaceholder())
                                  <span class="h6 new-price">{{ $service->price_placeholder }}</span>
                                @else
                                  <span class="h6 new-price">{{ symbolPrice($service->price) }}</span>
                                <span
                                  class="prev-price font-sm">{{ $service->prev_price ? symbolPrice($service->prev_price) : '' }}</span>
                                @endif
                              </div>
                              <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <button type="submit" class="btn btn-sm btn-outline-2 rounded-pill" title="{{ __('Add to Cart') }}">
                                  <i class="fal fa-shopping-cart me-1"></i>{{ __('Add to Cart') }}
                                </button>
                              </form>
                            </div>
                          </div>
                        </div><!-- product-default -->
                      </div>
                    @endforeach
                    <div class="cta-btn text-center mt-15">
                      <a href="{{ route('frontend.services') }}"
                        class="btn btn-lg btn-primary btn-gradient icon-start rounded-pill" title="View More"
                        target="_self"><i class="fal fa-arrow-right"></i>{{ __('View More') }}</a>
                    </div>
                  </div>
                </div>
                @foreach ($categories as $category)
                  @php
                    $vendorStatus = App\Models\Vendor::where('status', 1)->select('id')->get()->toArray();
                    $services = App\Models\Services\Services::join(
                      'service_contents',
                      'service_contents.service_id',
                      '=',
                      'services.id',
                    )
                      ->join('service_categories', 'service_categories.id', '=', 'service_contents.category_id')
                      ->where('service_contents.language_id', $language->id)
                      ->where('service_categories.language_id', $language->id)
                      ->where('service_contents.category_id', $category->id)
                      ->where(function ($query) use ($vendorStatus) {
                        $query->whereIn('services.vendor_id', $vendorStatus)->orWhere('services.vendor_id', 0);
                      })
                      ->when('services.vendor_id' != '0', function ($query) {
                        return $query
                          ->leftJoin('memberships', 'services.vendor_id', '=', 'memberships.vendor_id')
                          ->where(function ($query) {
                            $query
                              ->where([
                                ['memberships.status', '=', 1],
                                ['memberships.start_date', '<=', now()->format('Y-m-d')],
                                ['memberships.expire_date', '>=', now()->format('Y-m-d')],
                              ])
                              ->orWhere('services.vendor_id', '=', 0);
                          });
                      })
                      ->select(
                        'services.*',
                        'service_contents.name',
                        'service_contents.address',
                        'service_contents.slug',
                      )
                      ->paginate(8);
                  @endphp
                  @if (count($services) > 0)
                    <div class="tab-pane fade" id="tab{{ $category->id }}">
                      <div class="row">
                        @foreach ($services as $service)
                          <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
                            <div class="product-default border radius-md p-15 mb-25">
                              <figure class="product-img mb-15">
                                <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                                  title="Image" target="_self" class="lazy-container radius-sm ratio ratio-2-3">
                                  <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                    data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="Service">
                                </a>
                              </figure>
                              <div class="product-details">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                  <a href="{{ route('frontend.services', ['category_id' => $category->id]) }}">
                                    <span class="tag font-sm">{{ $category->name }}</span>
                                  </a>
                                  @if (Auth::guard('web')->check())
                                    @php
                                      $user_id = Auth::guard('web')->user()->id;
                                      $checkWishList = checkWishList($service->id, $user_id);
                                    @endphp
                                  @else
                                    @php
                                      $checkWishList = false;
                                    @endphp
                                  @endif
                                  <a href="{{ $checkWishList == false ? route('addto.wishlist', $service->id) : route('remove.wishlist', $service->id) }}"
                                    class="btn btn-icon border rounded-circle {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                                    title="Save to Wishlist">
                                    <i class="fal fa-heart"></i>
                                  </a>
                                </div>
                                <h6 class="product-title mb-0">
                                  <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                                    target="_self" title="{{ $service->name }}">
                                    {{ truncateString($service->name, 60) }}</a>
                                </h6>
                                <input type="hidden" value="{{ $service->language_id }}">




                                <!--div class="author mb-10 mt-10">
                                                          @if ($service->vendor_id != 0)
                                                            @if ($service->vendor->photo)
                                                              <img class="lazyload blur-up"
                                                                src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                                data-src="{{ asset('assets/admin/img/vendor-photo/' . $service->vendor->photo) }}"
                                                                alt="Image">
                                                            @else
                                                              <img class="lazyload"
                                                                src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                                data-src="{{ asset('assets/img/user.png') }}" alt="Vendor">
                                                            @endif
                                                            <span class="font-sm">
                                                              {{ __('By') }} <a
                                                                href="{{ route('frontend.vendor.details', ['username' => $service->vendor->username]) }}"
                                                                target="_self"
                                                                title="{{ $service->vendor->username }}">{{ $service->vendor->username }}</a>
                                                            </span>
                                                          @else
                                                            <img class="lazyload blur-up"
                                                              src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                              data-src="{{ asset('assets/img/admins/' . $admin->image) }}" alt="Image">
                                                            <span class="font-sm">
                                                              {{ __('By') }} <a href="" target="_self"
                                                                title="{{ $admin->username }}">{{ $admin->username }}</a>
                                                            </span>
                                                          @endif
                                                        </div>
                                                        @if (!empty($service->address))
                                                          <span class="font-sm icon-start"><i
                                                              class="fal fa-map-marker-alt"></i>{{ truncateString($service->address, 30) }}</span>
                                                        @endif
                                                        -->


                                @if ($service->zoom_meeting == 1)
                                  <span class="font-sm icon-start"><i class="fal fa-video"></i>{{ __('Online') }}</span>
                                @endif
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-10">
                                  <div class="product-price">
                                    @if ($service->hasPricePlaceholder())
                                      <span class="h6 new-price">{{ $service->price_placeholder }}</span>
                                    @else
                                    <span class="h6 new-price">{{ symbolPrice($service->price) }}</span>
                                    <span
                                      class="prev-price font-sm">{{ $service->prev_price ? symbolPrice($service->prev_price) : '' }}</span>
                                    @endif
                                  </div>
                                  <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-2 rounded-pill" title="{{ __('Add to Cart') }}">
                                      <i class="fal fa-shopping-cart me-1"></i>{{ __('Add to Cart') }}
                                    </button>
                                  </form>
                                </div>
                              </div>
                            </div><!-- product-default -->
                          </div>
                        @endforeach
                      </div>
                      <div class="cta-btn text-center mt-15">
                        <a href="{{ route('frontend.services', ['category_id' => $category->id]) }}"
                          class="btn btn-lg btn-primary btn-gradient icon-start rounded-pill" target="_self"><i
                            class="fal fa-arrow-right"></i>{{ __('View More') }}</a>
                      </div>
                    </div>
                  @endif
                @endforeach

              </div>
            @endif
          </div>
        </div>
    </section>
  @endif
  <!-- Service-area end -->

  <!-- Why Choose Us section start -->
  <section class="why-choose-us-area ptb-100 bg-primary-light">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="section-title title-center mb-50" data-aos="fade-up">
            <h2 class="title mb-20">{{ __('Why Choose Us') }}</h2>
            <p class="text">{{ __('We provide the best service with experienced professionals') }}</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4 col-md-6" data-aos="fade-up">
          <div class="card text-center p-30 radius-md mb-30 shadow-md bg-white">
            <div
              class="card-icon mx-auto mb-20 rounded-circle bg-primary d-flex align-items-center justify-content-center"
              style="width: 80px; height: 80px;">
              <i class="fal fa-certificate fa-2x text-white"></i>
            </div>
            <h4 class="card-title mb-15">{{ __('Certified & Experienced Professionals') }}</h4>
            <p class="card-text">
              {{ __('Our team consists of certified and highly experienced professionals dedicated to providing top-quality service') }}
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="card text-center p-30 radius-md mb-30 shadow-md bg-white">
            <div
              class="card-icon mx-auto mb-20 rounded-circle bg-success d-flex align-items-center justify-content-center"
              style="width: 80px; height: 80px;">
              <i class="fal fa-tags fa-2x text-white"></i>
            </div>
            <h4 class="card-title mb-15">{{ __('Affordable & Transparent Pricing') }}</h4>
            <p class="card-text">
              {{ __('We offer competitive and transparent pricing with no hidden fees, ensuring you get the best value for your money') }}
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="card text-center p-30 radius-md mb-30 shadow-md bg-white">
            <div class="card-icon mx-auto mb-20 rounded-circle bg-info d-flex align-items-center justify-content-center"
              style="width: 80px; height: 80px;">
              <i class="fal fa-headset fa-2x text-white"></i>
            </div>
            <h4 class="card-title mb-15">{{ __('24/7 Customer Support') }}</h4>
            <p class="card-text">
              {{ __('Our dedicated support team is available round the clock to assist you with any questions or concerns') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Why Choose Us section end -->

  <!-- How It Works section start -->
  <section class="how-it-works-area ptb-100">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="section-title title-center mb-50" data-aos="fade-up">
            <h2 class="title mb-20">{{ __('How It Works') }}</h2>
            <p class="text">{{ __('Book your service in 4 simple steps') }}</p>
          </div>
        </div>
      </div>
      <div class="row align-items-center">
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="card text-center p-30 radius-lg mb-30 position-relative overflow-hidden"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
            <div class="position-absolute"
              style="top: -20px; right: -20px; font-size: 100px; opacity: 0.1; color: white; font-weight: bold;">01</div>
            <div class="card-icon mx-auto mb-20 rounded-circle bg-white d-flex align-items-center justify-content-center"
              style="width: 70px; height: 70px;">
              <i class="fal fa-search fa-2x" style="color: #667eea;"></i>
            </div>
            <h4 class="card-title mb-15 text-white">{{ __('Search Service') }}</h4>
            <p class="card-text text-white opacity-75">
              {{ __('Browse and find the perfect service that meets your needs') }}
            </p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="card text-center p-30 radius-lg mb-30 position-relative overflow-hidden"
            style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">
            <div class="position-absolute"
              style="top: -20px; right: -20px; font-size: 100px; opacity: 0.1; color: white; font-weight: bold;">02</div>
            <div class="card-icon mx-auto mb-20 rounded-circle bg-white d-flex align-items-center justify-content-center"
              style="width: 70px; height: 70px;">
              <i class="fal fa-calendar-alt fa-2x" style="color: #f5576c;"></i>
            </div>
            <h4 class="card-title mb-15 text-white">{{ __('Select Date & Time') }}</h4>
            <p class="card-text text-white opacity-75">
              {{ __('Choose your preferred date and time slot from available options') }}
            </p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
          <div class="card text-center p-30 radius-lg mb-30 position-relative overflow-hidden"
            style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none;">
            <div class="position-absolute"
              style="top: -20px; right: -20px; font-size: 100px; opacity: 0.1; color: white; font-weight: bold;">03</div>
            <div class="card-icon mx-auto mb-20 rounded-circle bg-white d-flex align-items-center justify-content-center"
              style="width: 70px; height: 70px;">
              <i class="fal fa-credit-card fa-2x" style="color: #00f2fe;"></i>
            </div>
            <h4 class="card-title mb-15 text-white">{{ __('Make Payment') }}</h4>
            <p class="card-text text-white opacity-75">
              {{ __('Complete your booking with secure online payment options') }}
            </p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
          <div class="card text-center p-30 radius-lg mb-30 position-relative overflow-hidden"
            style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none;">
            <div class="position-absolute"
              style="top: -20px; right: -20px; font-size: 100px; opacity: 0.1; color: white; font-weight: bold;">04</div>
            <div class="card-icon mx-auto mb-20 rounded-circle bg-white d-flex align-items-center justify-content-center"
              style="width: 70px; height: 70px;">
              <i class="fal fa-check-circle fa-2x" style="color: #43e97b;"></i>
            </div>
            <h4 class="card-title mb-15 text-white">{{ __('Get Confirmation') }}</h4>
            <p class="card-text text-white opacity-75">
              {{ __('Receive instant confirmation and enjoy your booked service') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- How It Works section end -->

  <section class="home-reviews-section" id="reviews">
    <div class="container">
      <div class="home-reviews-head">
        <div>
          <span class="home-reviews-eyebrow">
            <i class="fal fa-star"></i> {{ __('Customer reviews') }}
          </span>
          <h2>{{ __('Trusted by UK customers for urgent jobs, certificates and home services.') }}</h2>
        </div>
      </div>

      <div class="home-reviews-grid">
        <article class="home-review-card">
          <div class="home-review-stars" aria-label="{{ __('5 star review') }}">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <blockquote>{{ __('Booked Friday night and the boiler was fixed Saturday morning. Clear price, quick arrival, no drama.') }}</blockquote>
          <div class="home-review-meta">
            <span><strong>{{ __('Sarah M') }}</strong>{{ __('Croydon') }}</span>
            <span class="home-review-tag">{{ __('Boiler repair') }}</span>
          </div>
        </article>

        <article class="home-review-card">
          <div class="home-review-stars" aria-label="{{ __('5 star review') }}">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <blockquote>{{ __('Needed an EICR for a rental property. The engineer arrived on time and the report was easy to share with my agent.') }}</blockquote>
          <div class="home-review-meta">
            <span><strong>{{ __('Imran K') }}</strong>{{ __('Ilford') }}</span>
            <span class="home-review-tag">{{ __('EICR') }}</span>
          </div>
        </article>

        <article class="home-review-card">
          <div class="home-review-stars" aria-label="{{ __('5 star review') }}">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <blockquote>{{ __('End-of-tenancy cleaning was spotless. Booking took less than a minute and the team kept me updated.') }}</blockquote>
          <div class="home-review-meta">
            <span><strong>{{ __('Amelia R') }}</strong>{{ __('Wembley') }}</span>
            <span class="home-review-tag">{{ __('Cleaning') }}</span>
          </div>
        </article>
      </div>

      <div class="home-reviews-cta">
        <a href="{{ route('frontend.services') }}">
          {{ __('Book a service now') }} <i class="fal fa-long-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>

  <!-- Works-area start -->
  @if ($secInfo->work_process_section_status == 1)
    <section class="works-area works-2 pt-100 pb-70 bg-primary-light">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section-title title-center mb-50" data-aos="fade-up">
              <h2 class="title">
                {{ !empty($sectionContent->workprocess_section_title) ? $sectionContent->workprocess_section_title : 'How the Appointment Booking System Works ' }}
              </h2>
            </div>
          </div>
          <div class="col-12">
            <div class="row">
              @foreach ($processes as $processe)
                <div class="col-xl-4 col-lg-4 col-sm-6" data-aos="fade-up">
                  <div class="card p-30 radius-lg text-center mb-30 shadow-md">
                    <div class="card-img mb-20">
                      <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                        data-src="{{ asset('assets/img/workprocess/' . $processe->image) }}" alt="Image">
                    </div>
                    <h4 class="card-title lc-1 mb-0">
                      {{ $processe->title }}
                    </h4>
                    <span class="h1 color-primary stroke-gradient">0{{ $loop->iteration }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- Works-area end -->
  @if (count($after_work_process) > 0)
    @foreach ($after_work_process as $work_process)
      @if (isset($homecusSec[$work_process->id]))
        @if ($homecusSec[$work_process->id] == 1)
          @php
            $work_processContent = App\Models\CustomSectionContent::where('custom_section_id', $work_process->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $work_processContent])
        @endif
      @endif
    @endforeach
  @endif
  <!-- Service-area start -->
  @if ($secInfo->feature_section_status == 1)
    <section class="service-area service-1 ptb-100">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section-title title-inline mb-50" data-aos="fade-up">
              <h2 class="title">
                {{ !empty($sectionContent->featured_service_section_title) ? $sectionContent->featured_service_section_title : 'Our Top Featured Services' }}
              </h2>
              <!-- Slider navigation buttons -->
              @if ($featured_services->count() > 4)
                <div class="slider-navigation">
                  <button type="button" title="Slide prev" class="slider-btn rounded-circle" id="product-slider-1-prev">
                    <i class="fal fa-angle-left"></i>
                  </button>
                  <button type="button" title="Slide next" class="slider-btn rounded-circle" id="product-slider-1-next">
                    <i class="fal fa-angle-right"></i>
                  </button>
                </div>
              @endif
            </div>
          </div>
          <div class="col-12 slider-services">
            @if ($featured_services->count() == 0)
              <h4 class="text-center">{{ __('NO SERVICE FOUND') . '!' }}</h4>
            @else
              <!-- Slider main container -->
              <div class="swiper product-slider" id="product-slider-1" data-slides-per-view="4" data-swiper-loop="false"
                data-aos="fade-up">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                  <!-- Slides -->
                  @foreach ($featured_services as $service)
                    <div class="swiper-slide">
                      <div class="product-default border radius-md p-15 mb-25">
                        <figure class="product-img mb-15">
                          <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                            title="Image" target="_self" class="lazy-container radius-sm ratio ratio-2-3">
                            <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                              data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="Service">
                          </a>
                        </figure>
                        <div class="product-details">
                          <div class="d-flex align-items-center justify-content-between gap-2">
                            <a href="{{ route('frontend.services', ['category_id' => $service->categoryId]) }}">
                              <span class="tag font-sm">{{ $service->categoryName }}</span>
                            </a>
                            @if (Auth::guard('web')->check())
                              @php
                                $user_id = Auth::guard('web')->user()->id;
                                $checkWishList = checkWishList($service->id, $user_id);
                              @endphp
                            @else
                              @php
                                $checkWishList = false;
                              @endphp
                            @endif
                            <a href="{{ $checkWishList == false ? route('addto.wishlist', $service->id) : route('remove.wishlist', $service->id) }}"
                              class="btn btn-icon border rounded-circle {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                              data-tooltip="tooltip" data-bs-placement="right"
                              title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                              <i class="fal fa-heart"></i>
                            </a>
                          </div>
                          <h6 class="product-title mb-0">
                            <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                              target="_self" title="{{ $service->name }}">
                              {{ truncateString($service->name, 60) }}
                            </a>
                          </h6>




                          <!--div class="author mb-10 mt-10">
                                            @if ($service->vendor_id != 0)
                                              @if ($service->vendor->photo != null)
                                                <a href="{{ route('frontend.vendor.details', ['username' => $admin->username]) }}"
                                                  target="_self" title="{{ $service->vendor->username }}">
                                                  <img class="lazyload blur-up"
                                                    src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                    data-src="{{ asset('assets/admin/img/vendor-photo/' . $service->vendor->photo) }}"
                                                    alt="Image">
                                                </a>
                                              @else
                                                <a href="{{ route('frontend.vendor.details', ['username' => $admin->username]) }}"
                                                  target="_self" title="{{ $service->vendor->username }}">
                                                  <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                    data-src="{{ asset('assets/img/user.png') }}" alt="Vendor">
                                                </a>
                                              @endif
                                              <span class="font-sm">
                                                {{ __('By') }} <a
                                                  href="{{ route('frontend.vendor.details', ['username' => $service->vendor->username]) }}"
                                                  target="_self" title="John Doe">{{ $service->vendor->username }}</a>
                                              </span>
                                            @else
                                              <a href="{{ route('frontend.vendor.details', ['username' => $admin->username]) }}"
                                                target="_self" title="{{ $admin->username }}">
                                                <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                                  data-src="{{ asset('assets/img/admins/' . $admin->image) }}" alt="Vendor">
                                              </a>
                                              <span class="font-sm">
                                                {{ __('By') }} <a
                                                  href="{{ route('frontend.vendor.details', ['username' => $admin->username]) }}"
                                                  target="_self" title="{{ $admin->username }}">{{ $admin->username }}</a>
                                              </span>
                                            @endif
                                          </div>
                                          @if (!empty($service->address))
                                            <span class="font-sm icon-start"><i
                                                class="fal fa-map-marker-alt"></i>{{ truncateString($service->address, 30) }}</span>
                                          @endif
                                          -->


                          @if ($service->zoom_meeting == 1)
                            <span class="font-sm icon-start"><i class="fal fa-video"></i>{{ __('Online') }}</span>
                          @endif
                          <div class="d-flex align-items-center justify-content-between gap-2 mt-10">
                            <div class="product-price">
                              @if ($service->hasPricePlaceholder())
                                <span class="h6 new-price">{{ $service->price_placeholder }}</span>
                              @else
                                <span class="h6 new-price">{{ symbolPrice($service->price) }}</span>
                                <span
                                  class="prev-price font-sm">{{ $service->prev_price ? symbolPrice($service->prev_price) : '' }}</span>
                              @endif
                            </div>
                            <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST" class="m-0">
                              @csrf
                              <input type="hidden" name="service_id" value="{{ $service->id }}">
                              <button type="submit" class="btn btn-sm btn-outline-2" title="{{ __('Add to Cart') }}">
                                <i class="fal fa-shopping-cart me-1"></i>{{ __('Add to Cart') }}
                              </button>
                            </form>
                          </div>
                        </div>
                      </div><!-- product-default -->
                    </div>
                  @endforeach
                </div>

                <!-- If we need pagination -->
                <div class="swiper-pagination position-static" id="product-slider-1-pagination"></div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- Service-area end -->
  @if (count($after_featured_service) > 0)
    @foreach ($after_featured_service as $cusFeature)
      @if (isset($homecusSec[$cusFeature->id]))
        @if ($homecusSec[$cusFeature->id] == 1)
          @php
            $cusFeatureContent = App\Models\CustomSectionContent::where('custom_section_id', $cusFeature->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusFeatureContent])
        @endif
      @endif
    @endforeach
  @endif
  <!-- Banner collection start -->
  @if ($secInfo->banner_section == 1)
    <div class="banner-collection pb-70">
      <div class="container">
        <div class="row">
          @foreach ($banners as $banner)
            <div class="col-sm-6" data-aos="fade-up">
              <a href="{{ $banner->url }}">
                <div class="banner-sm content-middle bg-img mb-30 ratio ratio-21-9 radius-lg"
                  data-bg-image="{{ asset('assets/img/banners/' . $banner->image) }}">
                  <div class="banner-content p-30">
                    <div class="content-inner">
                      <h3 class="mb-30">{{ $banner->title }}</h3>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif
  <!-- Banner collection end -->
  @if (count($after_banner) > 0)
    @foreach ($after_banner as $cusBanner)
      @if (isset($homecusSec[$cusBanner->id]))
        @if ($homecusSec[$cusBanner->id] == 1)
          @php
            $cusBannerContent = App\Models\CustomSectionContent::where('custom_section_id', $cusBanner->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusBannerContent])
        @endif
      @endif
    @endforeach
  @endif

  @if (count($after_latest_service) > 0)
    @foreach ($after_latest_service as $cusLatestServie)
      @if (isset($homecusSec[$cusLatestServie->id]))
        @if ($homecusSec[$cusLatestServie->id] == 1)
          @php
            $cusLatestServieContent = App\Models\CustomSectionContent::where('custom_section_id', $cusLatestServie->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusLatestServieContent])
        @endif
      @endif
    @endforeach
  @endif
  <!-- Action banner start -->
  @if ($secInfo->call_to_action_section_status == 1)
    <section class="action-banner">
      <div class="container">
        <div class="wrapper radius-md pt-40 px-60 bg-img bg-cover"
          data-bg-image="{{ asset('assets/img/' . @$sectionContent->call_to_action_section_image) }}">
          <div class="row align-items-center gx-xl-5">
            <div class="col-lg-6">
              <div class="content-title mb-40" data-aos="fade-up">
                <h2 class="title color-white mb-25">
                  {{ !empty($sectionContent->call_to_action_section_title) ? $sectionContent->call_to_action_section_title : 'Now Get 50% Discount For First Booking' }}
                </h2>
                <p class="color-light">
                  {{ !empty($sectionContent->action_section_text) ? $sectionContent->action_section_text : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim' }}
                </p>
                @if (!empty($sectionContent->call_to_action_url))
                  <div class="mt-30">
                    <a href="{{ @$sectionContent->call_to_action_url }}"
                      class="btn btn-lg btn-primary btn-gradient icon-start rounded-pill" target="_self"><i
                        class="{{ @$sectionContent->call_to_action_icon }}"></i>{{ @$sectionContent->call_to_action_section_btn }}</a>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- Action banner end -->
  @if (count($after_call_to_action) > 0)
    @foreach ($after_call_to_action as $cusAction)
      @if (isset($homecusSec[$cusAction->id]))
        @if ($homecusSec[$cusAction->id] == 1)
          @php
            $cusActionContent = App\Models\CustomSectionContent::where('custom_section_id', $cusAction->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusActionContent])
        @endif
      @endif
    @endforeach
  @endif
  <!-- Shop-area start -->
  @if ($secInfo->vendor_featured_section_status == 1)
    <section class="shop-area shop-1 ptb-100">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section-title title-inline mb-50" data-aos="fade-up">
              <h2 class="title">
                {{ !empty($sectionContent->vendor_section_title) ? $sectionContent->vendor_section_title : 'Our Top Featured Vendor' }}
              </h2>
              @if ($vendors > 0)
                <a href="{{ route('frontend.vendors') }}" class="btn btn-lg btn-primary btn-gradient icon-start rounded-pill"
                  target="_self"><i class="fal fa-arrow-right"></i>
                  @if ($vendors > 1)
                    {{ __('View All Vendors') }}
                  @else
                    {{ __('View All Vendor') }}
                  @endif
                </a>
              @endif
            </div>
          </div>
          <div class="col-12">
            @if ($featuredVendors->count() == 0)
              <h4 class="text-center">{{ __('NO VENDOR FOUND') . '!' }}</h4>
            @else
              <!-- Slider main container -->
              <div class="swiper product-slider" id="product-slider-2" data-slides-per-view="4" data-swiper-loop="false"
                data-aos="fade-up">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                  <!-- Slides -->
                  @foreach ($featuredVendors as $vendor)
                    <div class="swiper-slide">
                      <div class="product-default border radius-md mb-25">
                        <figure class="product-img">
                          <a href="{{ route('frontend.vendor.details', ['username' => $vendor->username]) }}" title="Image"
                            target="_self" class="lazy-container radius-md ratio ratio-2-3">
                            @if ($vendor->photo)
                              <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                data-src="{{ asset('assets/admin/img/vendor-photo/' . $vendor->photo) }}" alt="Vendor">
                            @else
                              <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                data-src="{{ asset('assets/img/user.png') }}" alt="Vendor">
                            @endif
                          </a>
                        </figure>
                        <div class="product-details p-15">
                          @php
                            $vendorInfo = App\Models\VendorInfo::where([
                              ['vendor_id', $vendor->vendorId],
                              ['language_id', $language->id],
                            ])->first();
                          @endphp
                          <h6 class="product-title mb-0">
                            <a href="{{ route('frontend.vendor.details', ['username' => $vendor->username]) }}" target="_self"
                              title="{{ $vendor->username }}">
                              @if ($vendorInfo->name != null)
                                {{ $vendorInfo->name }}
                              @else
                                {{ $vendor->username }}
                              @endif
                            </a>
                          </h6>
                          @if ($vendorInfo)
                            @if ($vendorInfo->address != null)
                              <span class="font-sm icon-start"><i
                                  class="fal fa-map-marker-alt"></i>{{ truncateString($vendorInfo->address, 30) }}</span>
                            @endif
                          @endif
                          <div class="d-flex align-items-center gap-15 mt-10">
                            <a href="{{ route('frontend.vendor.details', ['username' => $vendor->username]) }}"
                              class="btn btn-sm btn-outline-2 rounded-pill" title="Visit Store"
                              target="_self">{{ __('Visit Store') }}</a>
                            @php
                              $total_service = App\Models\Services\Services::where('vendor_id', $vendor->vendorId)
                                ->where('status', 1)
                                ->count();
                            @endphp
                            <span class="font-sm">
                              @if ($vendor->total_service > 1)
                                {{ $vendor->total_service }}
                                {{ __('Services Available') }}
                              @elseif($vendor->total_service == 1)
                                {{ $vendor->total_service }}
                                {{ __('Services Available') }}
                              @else
                                {{ __('No Service Available') }}
                              @endif
                            </span>
                          </div>
                        </div>
                      </div><!-- product-default -->
                    </div>
                  @endforeach
                </div>

                <!-- If we need pagination -->
                <div class="swiper-pagination position-static" id="product-slider-2-pagination"></div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  @endif
  @if (count($after_vendor) > 0)
    @foreach ($after_vendor as $cusVendor)
      @if (isset($homecusSec[$cusVendor->id]))
        @if ($homecusSec[$cusVendor->id] == 1)
          @php
            $cusVendorContent = App\Models\CustomSectionContent::where('custom_section_id', $cusVendor->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusVendorContent])
        @endif
      @endif
    @endforeach
  @endif
  <!-- Shop-area end -->
@endsection
@section('script')
  <script defer src="https://js.stripe.com/v3/"></script>
  <script defer src="{{ $authorizeUrl }}"></script>
  <script>
    let stripe_key = "{{ $stripe_key }}";
    let authorize_login_key = "{{ $authorize_login_id }}";
    let authorize_public_key = "{{ $authorize_public_key }}";
    var complete = "{{ Session::get('complete') }}";
    var bookingInfo = {!! json_encode(Session::get('paymentInfo')) !!};
  </script>
  <script src="{{ asset('assets/frontend/js/appointment.js') }}?v={{ filemtime(public_path('assets/frontend/js/appointment.js')) }}"></script>

  <script>
    @if (old('gateway') == 'stripe')
      $('#stripe-element').removeClass('d-none');
    @endif
  </script>
@endsection
