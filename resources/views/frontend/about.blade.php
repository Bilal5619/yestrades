@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->about_us_title : __('About Us') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keywords_about_page }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_about_page }}
  @endif
@endsection

@section('content')
  <style>
    .about-fallback-visual {
      display: grid;
      gap: 14px;
      padding: 28px;
      background: #101827;
      border-radius: 8px;
      box-shadow: 0 24px 50px rgba(16, 24, 39, 0.14);
    }

    .about-fallback-card {
      display: grid;
      grid-template-columns: 48px minmax(0, 1fr);
      gap: 6px 14px;
      align-items: center;
      padding: 18px;
      color: #ffffff;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.11);
      border-radius: 8px;
    }

    .about-fallback-card span {
      grid-row: span 2;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      color: #ffffff;
      background: var(--color-primary);
      border-radius: 8px;
      font-size: 20px;
    }

    .about-fallback-card strong {
      color: #ffffff;
      font-size: 18px;
      line-height: 1.2;
    }

    .about-fallback-card small {
      color: #c7d0df;
      font-size: 13px;
      line-height: 1.45;
    }

    @media (max-width: 575.98px) {
      .about-fallback-visual {
        padding: 16px;
      }

      .about-fallback-card {
        grid-template-columns: 42px minmax(0, 1fr);
        padding: 14px;
      }

      .about-fallback-card span {
        width: 42px;
        height: 42px;
      }
    }

    /* ═══════════════ About page — emerald & slate ═══════════════ */
    .ab-page {
      --ab-primary: #0e9f6e;
      --ab-primary-dark: #0a7d57;
      --ab-primary-deep: #06432f;
      --ab-primary-soft: #e5f5ee;
      --ab-ink: #101d2c;
      --ab-muted: #5e6b7e;
      background: #ffffff;
    }

    .ab-hero {
      position: relative;
      overflow: hidden;
      margin-top: 84px;
      padding: 64px 0 58px;
      background:
        radial-gradient(circle at 92% 6%, rgba(14, 159, 110, 0.5), transparent 40%),
        radial-gradient(circle at 4% 100%, rgba(245, 165, 36, 0.14), transparent 32%),
        linear-gradient(120deg, #0d1b16 0%, #06432f 100%);
      color: #ffffff;
    }

    .ab-hero-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1.5px, transparent 1.5px);
      background-size: 22px 22px;
      mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      pointer-events: none;
    }

    .ab-hero .container {
      position: relative;
      z-index: 1;
    }

    .ab-breadcrumb {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 16px;
      color: #a9c7ba;
      font-size: 12.5px;
      font-weight: 700;
    }

    .ab-breadcrumb a {
      color: #dbeee3;
      text-decoration: none;
    }

    .ab-breadcrumb a:hover {
      color: #ffffff;
    }

    .ab-breadcrumb i {
      font-size: 10px;
      color: #6f9a84;
    }

    .ab-breadcrumb span {
      color: #8be3bf;
    }

    .ab-hero h1 {
      margin: 0 0 10px;
      color: #ffffff;
      font-size: clamp(28px, 3.4vw, 40px);
      font-weight: 900;
      letter-spacing: -0.015em;
    }

    .ab-hero p {
      max-width: 560px;
      margin: 0;
      color: #bcd8cb;
      font-size: 14.5px;
      line-height: 1.7;
    }

    /* Reskin the existing theme components used on this page to the emerald palette */
    .ab-page .subtitle {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 14px;
      padding: 7px 14px;
      color: var(--ab-primary-dark) !important;
      background: var(--ab-primary-soft);
      border-radius: 999px;
      font-size: 11.5px;
      font-weight: 900;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    .ab-page .content-title .title,
    .ab-page .section-title .title {
      color: var(--ab-ink);
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    .ab-page .btn-primary {
      background: linear-gradient(120deg, var(--ab-primary), var(--ab-primary-dark)) !important;
      border-color: transparent !important;
      box-shadow: 0 14px 30px rgba(14, 159, 110, 0.3);
    }

    .ab-page .feature-area .card,
    .ab-page .works-area .card,
    .ab-page .testimonial-area .slider-item {
      border-radius: 18px !important;
      box-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .ab-page .feature-area .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 28px 64px rgba(16, 29, 44, 0.13);
    }

    .ab-page .feature-area .card-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 54px;
      height: 54px;
      color: var(--ab-primary-dark) !important;
      background: var(--ab-primary-soft) !important;
      font-size: 21px;
    }

    .ab-page .works-area.works-2 {
      background: #f4f7f5 !important;
    }

    .ab-page .works-area .card-icon.bg-primary {
      background: linear-gradient(135deg, var(--ab-primary), var(--ab-primary-dark)) !important;
    }

    .ab-page .stroke-gradient {
      -webkit-text-stroke: 1px var(--ab-primary);
      color: transparent !important;
    }

    .ab-page .testimonial-area {
      background: var(--ab-primary-deep);
    }
  </style>

  <section class="ab-hero">
    <div class="ab-hero-dots"></div>
    <div class="container">
      <nav aria-label="breadcrumb" class="ab-breadcrumb">
        <a href="{{ route('index') }}">{{ __('Home') }}</a>
        <i class="fal fa-angle-right"></i>
        <span>{{ !empty($pageHeading) ? $pageHeading->about_us_title : __('About Us') }}</span>
      </nav>
      <h1>{{ !empty($pageHeading) ? $pageHeading->about_us_title : (!empty($websiteInfo->website_title) ? __('About') . ' ' . $websiteInfo->website_title : __('About Us')) }}</h1>
      <p>{{ __('The UK\'s easiest way to book vetted service engineers - plumbing, gas, electrical, cleaning and landlord certificates.') }}</p>
    </div>
  </section>

  <div class="ab-page">

  @php
    $aboutDefaults = [
      'eyebrow' => 'About ' . ($websiteInfo->website_title ?? config('app.name')),
      'title' => 'Making home service booking simpler for customers and landlords.',
      'text' =>
        '<p>' . ($websiteInfo->website_title ?? config('app.name')) . ' is built to make it easier to find, compare and book essential home services from one place. From plumbing and electrical work to gas services and landlord certificates, our booking flow helps customers provide the right details before checkout.</p><p>Our aim is to keep the process clear: choose a service, confirm the property and contact information, review the price where available, and complete the booking request securely online.</p>',
    ];

    $defaultFeatures = [
      [
        'icon' => 'fal fa-search',
        'title' => 'Easy Service Discovery',
        'text' => 'Browse service categories and find the right option for your property or booking requirement.',
      ],
      [
        'icon' => 'fal fa-clipboard-check',
        'title' => 'Clear Booking Details',
        'text' => 'Provide site information, contact details and service-specific answers in a guided checkout flow.',
      ],
      [
        'icon' => 'fal fa-credit-card',
        'title' => 'Secure Online Checkout',
        'text' => 'Complete payment through secure payment providers after reviewing your selected services.',
      ],
      [
        'icon' => 'fal fa-home',
        'title' => 'Built For Property Needs',
        'text' => 'Support for everyday home services, landlord certificates and property maintenance requests.',
      ],
    ];

    $defaultProcesses = [
      [
        'icon' => 'fal fa-list-ul',
        'title' => 'Choose a service',
        'text' => 'Select the service you need and add it to your service cart.',
      ],
      [
        'icon' => 'fal fa-calendar-alt',
        'title' => 'Confirm details',
        'text' => 'Enter the property, contact and booking information required for the service.',
      ],
      [
        'icon' => 'fal fa-lock',
        'title' => 'Complete checkout',
        'text' => 'Review the total and complete payment securely online.',
      ],
      [
        'icon' => 'fal fa-check-circle',
        'title' => 'Booking recorded',
        'text' => 'Your booking details are saved so the next steps can be arranged.',
      ],
    ];
  @endphp

  @if ($secInfo->about_section_status == 1)
    <section class="about-area pt-100 pb-60">
      <div class="container">
        <div class="row align-items-center gx-xl-5">
          <div class="col-lg-6">
            <div class="image img-left mb-40">
              @if (!empty(@$about->about_section_image))
                <img class="blur-up lazyload" src="{{ asset('assets/img/about-us/' . @$about->about_section_image) }}"
                  alt="{{ (!empty($websiteInfo->website_title) ? __('About') . ' ' . $websiteInfo->website_title : __('About Us')) }}">
              @else
                <div class="about-fallback-visual">
                  <div class="about-fallback-card">
                    <span><i class="fal fa-home"></i></span>
                    <strong>{{ __('Home Services') }}</strong>
                    <small>{{ __('Plumbing, gas, electrical and certificates') }}</small>
                  </div>
                  <div class="about-fallback-card">
                    <span><i class="fal fa-clipboard-check"></i></span>
                    <strong>{{ __('Clear Details') }}</strong>
                    <small>{{ __('Guided booking information') }}</small>
                  </div>
                  <div class="about-fallback-card">
                    <span><i class="fal fa-lock"></i></span>
                    <strong>{{ __('Secure Checkout') }}</strong>
                    <small>{{ __('Online payment flow') }}</small>
                  </div>
                </div>
              @endif
            </div>
          </div>
          <div class="col-lg-6">
            <div class="content-title mb-40">
              <span class="subtitle">{{ !empty(@$about->title) ? @$about->title : __($aboutDefaults['eyebrow']) }}</span>
              <h2 class="title mb-20 mt-0">
                {{ !empty(@$about->subtitle) ? @$about->subtitle : __($aboutDefaults['title']) }}
              </h2>
              <div class="text">
                {!! !empty(@$about->text) ? @$about->text : __($aboutDefaults['text']) !!}
              </div>
              @if (!empty(@$about->button_url))
                <a href="{{ @$about->button_url }}"
                  class="btn btn-lg btn-primary icon-start ">{{ @$about->button_text }}</a>
              @else
                <a href="{{ route('frontend.services') }}" class="btn btn-lg btn-primary icon-start">
                  <i class="fal fa-arrow-right"></i>{{ __('Browse Services') }}
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif

  @if (count($after_about ?? []) > 0)
    @foreach ($after_about as $customAbout)
      @if (isset($aboutSec[$customAbout->id]))
        @if ($aboutSec[$customAbout->id] == 1)
          @php
            $afAboutCon = App\Models\CustomSectionContent::where('custom_section_id', $customAbout->id)
                ->where('language_id', $currentLanguageInfo->id)
                ->first();
          @endphp
          <section class="custom-section-area pt-100 pb-60">
            <div class="container">
              <div class="section-title title-center mb-50" data-aos="fade-up">
                <h2 class="title mb-0">
                  {{ @$afAboutCon->section_name }}
                </h2>
              </div>
              <div class="row align-items-center gx-xl-5">
                {!! $afAboutCon->content !!}
              </div>
            </div>
          </section>
        @endif
      @endif
    @endforeach
  @endif

  <!-- Feature-area start -->
  @if ($secInfo->features_section_status == 1)
    <section class="feature-area feature-1 ptb-70">
      <div class="container">
        <div class="section-title title-center mb-50" data-aos="fade-up">
          <h2 class="title mb-0">
            {{ !empty(@$about->features_title) ? @$about->features_title : __('Why customers use') . ' ' . ($websiteInfo->website_title ?? config('app.name')) }}
          </h2>
        </div>
        <div class="row">
          @if (count($features ?? []) > 0)
            @foreach ($features as $feature)
              <div class="col-xl-3 col-lg-4 col-md-6 item">
                <div class="card p-25 border radius-md mb-30">
                  <div class="card-icon radius-md mb-20">
                    <i class="{{ $feature->icon }}"></i>
                  </div>
                  <h4 class="card-title mb-15">
                    {{ $feature->title }}
                  </h4>
                  <p class="text">
                    {{ $feature->text }}
                  </p>
                </div>
              </div>
            @endforeach
          @else
            @foreach ($defaultFeatures as $feature)
              <div class="col-xl-3 col-lg-4 col-md-6 item">
                <div class="card p-25 border radius-md mb-30">
                  <div class="card-icon radius-md mb-20">
                    <i class="{{ $feature['icon'] }}"></i>
                  </div>
                  <h4 class="card-title mb-15">
                    {{ __($feature['title']) }}
                  </h4>
                  <p class="text">
                    {{ __($feature['text']) }}
                  </p>
                </div>
              </div>
            @endforeach
          @endif
        </div>
        @if (!empty(showAd(3)))
          <div class="text-center mt-4">
            {!! showAd(3) !!}
          </div>
        @endif
      </div>
    </section>
  @endif
  <!-- Feature-area end -->

  @if (count($after_features ?? []) > 0)
    @foreach ($after_features as $Cufeatures)
      @if (isset($aboutSec[$Cufeatures->id]))
        @if ($aboutSec[$Cufeatures->id] == 1)
          @php
            $cuFeatures = App\Models\CustomSectionContent::where('custom_section_id', $Cufeatures->id)
                ->where('language_id', $currentLanguageInfo->id)
                ->first();
          @endphp
          <section class="custom-section-area pt-100 pb-60">
            <div class="container">
              <div class="section-title title-center mb-50" data-aos="fade-up">
                <h2 class="title mb-0">
                  {{ @$cuFeatures->section_name }}
                </h2>
              </div>
              <div class="row align-items-center gx-xl-5">
                {!! @$cuFeatures->content !!}
              </div>
            </div>
          </section>
        @endif
      @endif
    @endforeach
  @endif


  <!-- Works-area start -->
  @if ($basicInfo->theme_version != 2)
    @if ($secInfo->about_work_status == 1)
      <section class="works-area works-1 pt-100 pb-60 bg-img bg-cover"
        data-bg-image="{{ !empty($sectionContent->work_process_background_img) ? asset('assets/img/' . $sectionContent->work_process_background_img) : asset('assets/frontend/images/work-process.png') }}">

        <div class="container">
          <div class="row align-items-center gx-xl-5">
            <div class="col-lg-5">
              <div class="content-title mb-40" data-aos="fade-up">
                <h2 class="title mb-25 color-white">
                  {{ !empty($sectionContent->workprocess_section_title) ? $sectionContent->workprocess_section_title : 'How appointment Booking System Works' }}
                </h2>
                <p class="color-white">
                  {{ !empty($sectionContent->workprocess_section_subtitle) ? $sectionContent->workprocess_section_subtitle : 'Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind. ' }}
                </p>
                @if (!empty($sectionContent->workprocess_section_url))
                  <div class="mt-30">
                    <a href="{{ @$sectionContent->workprocess_section_url }}"
                      class="btn btn-lg btn-primary btn-gradient icon-start">
                      <i class="{{ @$sectionContent->workprocess_icon }}"></i>
                      {{ @$sectionContent->workprocess_section_btn }}
                    </a>
                  </div>
                @endif
              </div>
            </div>
            <div class="col-lg-7">
              <div class="swiper works-slider mb-40" id="works-slider-1" data-aos="fade-up">
                <div class="swiper-wrapper">
                  @foreach ($processes as $processe)
                    <style>
                      .works-area.works-1 .swiper-slide .card.card-bg{{ $loop->iteration }} {
                        background-color: #{{ $processe->background_color }};
                        background-image: linear-gradient(-35deg, #{{ $processe->background_color }} 0%, #021B79 100%);
                      }
                    </style>
                    <div class="swiper-slide">
                      <div class="card card-bg{{ $loop->iteration }} p-30 radius-lg">
                        <div class="card-icon color-white">
                          <i class="{{ $processe->icon }}"></i>
                        </div>
                        <div class="line bg-white my-3 rounded-pill"></div>
                        <h4 class="card-title color-white lc-1 mb-15">
                          {{ $processe->title }}
                        </h4>
                        <p class="card-text color-light">
                          {{ $processe->text }}
                        </p>
                      </div>
                    </div>
                  @endforeach
                </div>
                <div class="swiper-pagination position-static mt-30" id="works-slider-1-pagination"></div>
              </div>
            </div>
          </div>
        </div>
      </section>
    @endif
  @endif
  @if ($basicInfo->theme_version == 2)
    @if ($secInfo->about_work_status == 1)
      <section class="works-area works-2 pt-100 pb-70 bg-primary-light">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="section-title title-center mb-50" data-aos="fade-up">
                <h2 class="title">
                  {{ !empty($sectionContent->workprocess_section_title) ? $sectionContent->workprocess_section_title : __('How') . ' ' . ($websiteInfo->website_title ?? config('app.name')) . ' ' . __('booking works') }}
                </h2>
              </div>
            </div>
            <div class="col-12">
              <div class="row">
                @if (count($processes ?? []) > 0)
                  @foreach ($processes as $processe)
                    <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
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
                @else
                  @foreach ($defaultProcesses as $processe)
                    <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
                      <div class="card p-30 radius-lg text-center mb-30 shadow-md">
                        <div class="card-icon mx-auto mb-20 rounded-circle bg-primary d-flex align-items-center justify-content-center"
                          style="width: 74px; height: 74px;">
                          <i class="{{ $processe['icon'] }} fa-2x text-white"></i>
                        </div>
                        <h4 class="card-title lc-1 mb-15">
                          {{ __($processe['title']) }}
                        </h4>
                        <p class="card-text mb-0">
                          {{ __($processe['text']) }}
                        </p>
                        <span class="h1 color-primary stroke-gradient">0{{ $loop->iteration }}</span>
                      </div>
                    </div>
                  @endforeach
                @endif
              </div>
            </div>
          </div>
        </div>
      </section>
    @endif
  @endif
  <!-- Works-area end -->
  @if (count($after_work_process ?? []) > 0)
    @foreach ($after_work_process as $Cuwork_process)
      @if (isset($aboutSec[$Cuwork_process->id]))
        @if ($aboutSec[$Cuwork_process->id] == 1)
          @php
            $cuWorkProcess = App\Models\CustomSectionContent::where('custom_section_id', $Cuwork_process->id)
                ->where('language_id', $currentLanguageInfo->id)
                ->first();
          @endphp
          <section class="custom-section-area pt-100 pb-60">
            <div class="container">
              <div class="section-title title-center mb-50" data-aos="fade-up">
                <h2 class="title mb-0">
                  {{ @$cuWorkProcess->section_name }}
                </h2>
              </div>
              <div class="row align-items-center gx-xl-5">
                {!! @$cuWorkProcess->content !!}
              </div>
            </div>
          </section>
        @endif
      @endif
    @endforeach
  @endif
  @if ($secInfo->about_testimonial_section_status == 1 && count($testimonials ?? []) > 0)
    <section class="testimonial-area testimonial-1 parallax ptb-60">
      <div class="container container-lg-fluid">
        <div class="row align-items-center gx-xl-5">
          <div class="col-lg-6">
            <div class="fluid-left">
              <div class="content-title mb-40" data-aos="fade-up">
                <h2 class="title mb-20">
                  {{ !empty($sectionContent->testimonial_section_title) ? $sectionContent->testimonial_section_title : 'What Customers Say About Our Booking Systems ' }}
                </h2>
                <div class="content-text mb-40">
                  <p>
                    {{ !empty($sectionContent->testimonial_section_subtitle) ? $sectionContent->testimonial_section_subtitle : __('Customer feedback helps us improve the booking experience and make service requests clearer for every user.') }}
                  </p>
                </div>
              </div>
              <div class="swiper mb-40" id="testimonial-slider-1" data-aos="fade-up">
                <div class="swiper-wrapper">
                  @foreach ($testimonials as $testimonial)
                    <div class="swiper-slide">
                      <div class="slider-item radius-md">
                        <div class="client gap-20 flex-wrap">
                          <div class="client-info d-flex align-items-center">
                            <div class="client-img">
                              <div class="lazy-container rounded-pill ratio ratio-1-1">
                                <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                  data-src="{{ asset('assets/img/clients/' . $testimonial->image) }}"
                                  alt="Person Image">
                              </div>
                            </div>
                            <div class="content">
                              <h6 class="name mb-0">{{ $testimonial->name }}</h6>
                              <span class="designation font-sm">{{ $testimonial->occupation }}</span>
                            </div>
                          </div>
                          <div class="rating-area flex-column align-items-start">
                            <div class="ratings">
                              <div class="rate bg-img"
                                data-bg-image="{{ asset('assets/frontend/images/rate-star.png') }}">
                                <div class="rating-icon bg-img"
                                  data-bg-image="{{ asset('assets/frontend/images/rate-star.png') }}">
                                  style="width: {{ $testimonial->rating * 20 . '%;' }}">
                                </div>
                              </div>
                            </div>
                            <span class="ratings-total">
                              {{ $testimonial->rating }} {{ __('star of') }}
                              {{ $total_testimonial }}
                              @if ($total_testimonial > 1)
                                {{ __('reviews') }}
                              @else
                                {{ __('review') }}
                              @endif
                            </span>
                          </div>
                        </div>
                        <div class="quote">
                          <span class="icon"><i class="fal fa-quote-right"></i></span>
                          <p class="text font-lg mb-0">
                            {{ $testimonial->comment }}
                          </p>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
                <div class="swiper-pagination position-static mt-30" id="testimonial-slider-1-pagination">
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-left">
            <div class="image mb-40 parallax-img" data-speed="0.5" data-revert="true">
              <img class="lazyload blur-up" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                data-src="{{ asset('assets/img/' . @$sectionContent->testimonial_section_image) }}" alt="Image">
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif
  @if (count($after_testimonial ?? []) > 0)
    @foreach ($after_testimonial as $Cutestimonial)
      @if (isset($aboutSec[$Cutestimonial->id]))
        @if ($aboutSec[$Cutestimonial->id] == 1)
          @php
            $cuTestimonial = App\Models\CustomSectionContent::where('custom_section_id', $Cutestimonial->id)
                ->where('language_id', $currentLanguageInfo->id)
                ->first();
          @endphp
          <section class="custom-section-area pt-100 pb-60">
            <div class="container">
              <div class="section-title title-center mb-50" data-aos="fade-up">
                <h2 class="title mb-0">
                  {{ @$cuTestimonial->section_name }}
                </h2>
              </div>
              <div class="row align-items-center gx-xl-5">
                {!! @$cuTestimonial->content !!}
              </div>
            </div>
          </section>
        @endif
      @endif
    @endforeach
  @endif

  @if (!empty(showAd(3)))
    <div class="text-center mt-5 mb-5">
      {!! showAd(3) !!}
    </div>
  @endif

  </div>
  <!-- /.ab-page -->
@endsection
