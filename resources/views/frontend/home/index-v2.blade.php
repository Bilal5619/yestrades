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
  <!-- ══════════ Hero start ══════════ -->
  <section class="yt-hero">
    <div class="yt-hero-blob yt-hero-blob-1"></div>
    <div class="yt-hero-blob yt-hero-blob-2"></div>
    <div class="container">
      <div class="yt-hero-grid">
        <div class="yt-hero-copy" data-aos="fade-up" data-aos-delay="100">
          <div class="yt-hero-eyebrow">
            <span class="yt-eyebrow-dot"></span>
            {{ __('Simple online booking for UK services') }}
          </div>

          <h1>
            {{ __('Services done right.') }}
            <span class="yt-hero-mark">{{ __('Book online with ease.') }}</span>
          </h1>

          <div class="yt-hero-media-inline" data-aos="fade-up" data-aos-delay="120">
            <div class="yt-hero-slide-card">
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

              <div class="yt-hero-chip">{{ __('On the way - 14 min') }}</div>
            </div>
          </div>

          <p>
            {{ __('Find plumbing, gas, electrical and landlord certificate services with clear prices, simple booking steps and helpful updates from one place.') }}
          </p>

          <div class="yt-hero-search" data-aos="fade-up" data-aos-delay="150">
            <form id="homepage_search" action="{{ route('frontend.services') }}" method="get">
              <div class="yt-hero-search-row">
                <div class="yt-hero-search-field">
                  <label for="service_name"><i class="fal fa-search"></i></label>
                  <input type="text" id="service_name" name="service_title" placeholder="{{ __('What do you need?') }}">
                </div>

                <button type="submit" class="yt-hero-search-btn">
                  {{ __('Find Now') }} <i class="fal fa-long-arrow-right"></i>
                </button>
              </div>
            </form>

            <div class="yt-hero-popular">
              <span>{{ __('Popular:') }}</span>
              <a href="{{ route('frontend.services', ['service_title' => 'Boiler service']) }}">{{ __('Boiler service') }}</a>
              <a href="{{ route('frontend.services', ['service_title' => 'EICR']) }}">{{ __('EICR') }}</a>
              <a href="{{ route('frontend.services', ['service_title' => 'Blocked drain']) }}">{{ __('Blocked drain') }}</a>
              <a href="{{ route('frontend.services', ['service_title' => 'Oven clean']) }}">{{ __('Oven clean') }}</a>
            </div>
          </div>

          <div class="yt-hero-trust">
            <span><i class="fal fa-shield-check"></i> {{ __('Gas Safe & NICEIC engineers') }}</span>
            <span><i class="fal fa-clock"></i> {{ __('Same-day available') }}</span>
            <span><i class="fal fa-pound-sign"></i> {{ __('Fixed price, no hidden fees') }}</span>
          </div>
        </div>

        <div class="yt-hero-media" data-aos="fade-up" data-aos-delay="150">
          <div class="yt-hero-media-frame">
            <div class="yt-hero-slide-card">
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

              <div class="yt-hero-chip">{{ __('Booking in progress') }}</div>
            </div>

            <div class="yt-hero-float-card">
              <div class="yt-hero-float-icon">
                <i class="fal fa-calendar-check"></i>
              </div>
              <div>
                <p>{{ __('Choose a service, confirm the details and complete your booking online.') }}</p>
                <span>{{ __('Simple booking flow') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="yt-statbar-wrap">
    <div class="container">
      <div class="yt-statbar" data-aos="fade-up">
        <div class="yt-stat">
          <strong>{{ __('Clear') }}</strong>
          <span>{{ __('Service pricing') }}</span>
        </div>
        <div class="yt-stat">
          <strong>{{ __('Secure') }}</strong>
          <span>{{ __('Online checkout') }}</span>
        </div>
        <div class="yt-stat">
          <strong>{{ __('Flexible') }}</strong>
          <span>{{ __('Booking details') }}</span>
        </div>
        <div class="yt-stat">
          <strong>{{ __('Simple') }}</strong>
          <span>{{ __('Service requests') }}</span>
        </div>
      </div>
    </div>
  </div>
  <!-- ══════════ Hero end ══════════ -->

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

  {{-- Category slider intentionally hidden (was disabled in the previous design as well) --}}

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

  <!-- ══════════ Services (tabs) start ══════════ -->
  @if ($secInfo->latest_service_section_status == 1)
    <section class="yt-section yt-services-section">
      <div class="container">
        <div class="yt-section-head yt-head-center" data-aos="fade-up">
          <span class="yt-eyebrow"><i class="fal fa-bolt"></i> {{ __('Book in minutes') }}</span>
          <h2>
            {{ !empty($sectionContent->featured_service_section_title) ? $sectionContent->featured_service_section_title : 'Services We Offer' }}
          </h2>
          @if (count($categories) > 0)
            <div class="yt-tabs">
              <button type="button" class="yt-tabs-arrow yt-tabs-prev" aria-label="{{ __('Scroll categories left') }}">
                <i class="fal fa-angle-left"></i>
              </button>
              <div class="yt-tabs-scroll">
                <ul class="nav nav-tabs" data-hover="fancyHover">
                  <li class="nav-item active">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#defaultTab"
                      type="button">{{ __('All Services') }}</button>
                  </li>
                  @foreach ($categories as $category)
                    <li class="nav-item">
                      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab{{ $category->id }}"
                        type="button">{{ $category->name }}</button>
                    </li>
                  @endforeach
                </ul>
              </div>
              <button type="button" class="yt-tabs-arrow yt-tabs-next" aria-label="{{ __('Scroll categories right') }}">
                <i class="fal fa-angle-right"></i>
              </button>
            </div>
          @endif
        </div>

        @if (count($services) == 0)
          <h4 class="text-center">{{ __('NO SERVICE FOUND') }}!</h4>
        @else
          <div class="tab-content" data-aos="fade-up">
            <div class="tab-pane fade show active" id="defaultTab">
              <div class="row g-4">
                @foreach ($services as $service)
                  <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
                    <div class="yt-scard">
                      <div class="yt-scard-media">
                        <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                          title="{{ $service->name }}" target="_self">
                          <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                            data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="Service">
                        </a>
                        <a href="{{ route('frontend.services', ['category_id' => $service->categoryId]) }}"
                          class="yt-scard-tag">{{ $service->categoryName }}</a>
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
                          class="yt-scard-wish {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                          title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                          <i class="fal fa-heart"></i>
                        </a>
                        <span class="yt-price-chip">
                          @if ($service->hasPricePlaceholder())
                            {{ $service->price_placeholder }}
                          @else
                            {{ symbolPrice($service->price) }}
                            @if ($service->prev_price)
                              <del>{{ symbolPrice($service->prev_price) }}</del>
                            @endif
                          @endif
                        </span>
                      </div>
                      <div class="yt-scard-body">
                        <h6 class="yt-scard-title">
                          <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                            target="_self" title="{{ $service->name }}">
                            {{ truncateString($service->name, 60) }}</a>
                        </h6>
                        <input type="hidden" value="{{ $service->language_id }}">
                        @if ($service->zoom_meeting == 1)
                          <span class="yt-scard-meta"><i class="fal fa-video"></i>{{ __('Online') }}</span>
                        @endif
                        <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST"
                          class="yt-scard-cartform">
                          @csrf
                          <input type="hidden" name="service_id" value="{{ $service->id }}">
                          <button type="submit" class="yt-scard-btn" title="{{ __('Book Now') }}">
                            <i class="fal fa-calendar-check"></i>{{ __('Book Now') }}
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="yt-section-cta" data-aos="fade-up">
                <a href="{{ route('frontend.services') }}" class="yt-btn-primary" title="View More" target="_self">
                  {{ __('View More') }} <i class="fal fa-arrow-right"></i></a>
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
                  <div class="row g-4">
                    @foreach ($services as $service)
                      <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
                        <div class="yt-scard">
                          <div class="yt-scard-media">
                            <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                              title="{{ $service->name }}" target="_self">
                              <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                                data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="Service">
                            </a>
                            <a href="{{ route('frontend.services', ['category_id' => $category->id]) }}"
                              class="yt-scard-tag">{{ @$category->name }}</a>
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
                              class="yt-scard-wish {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                              title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                              <i class="fal fa-heart"></i>
                            </a>
                            <span class="yt-price-chip">
                              @if ($service->hasPricePlaceholder())
                                {{ $service->price_placeholder }}
                              @else
                                {{ symbolPrice($service->price) }}
                                @if ($service->prev_price)
                                  <del>{{ symbolPrice($service->prev_price) }}</del>
                                @endif
                              @endif
                            </span>
                          </div>
                          <div class="yt-scard-body">
                            <h6 class="yt-scard-title">
                              <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                                target="_self" title="{{ $service->name }}">
                                {{ truncateString($service->name, 60) }}</a>
                            </h6>
                            <input type="hidden" value="{{ $service->language_id }}">
                            @if ($service->zoom_meeting == 1)
                              <span class="yt-scard-meta"><i class="fal fa-video"></i>{{ __('Online') }}</span>
                            @endif
                            <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST"
                              class="yt-scard-cartform">
                              @csrf
                              <input type="hidden" name="service_id" value="{{ $service->id }}">
                              <button type="submit" class="yt-scard-btn" title="{{ __('Book Now') }}">
                                <i class="fal fa-calendar-check"></i>{{ __('Book Now') }}
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                  <div class="yt-section-cta" data-aos="fade-up">
                    <a href="{{ route('frontend.services', ['category_id' => $category->id]) }}" class="yt-btn-primary"
                      target="_self">{{ __('View More') }} <i class="fal fa-arrow-right"></i></a>
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </section>
  @endif
  <!-- ══════════ Services (tabs) end ══════════ -->

  <!-- ══════════ Why Choose Us start ══════════ -->
  <section class="yt-section yt-why-section">
    <div class="container">
      <div class="yt-why-grid">
        <div class="yt-why-intro" data-aos="fade-up">
          <span class="yt-eyebrow"><i class="fal fa-badge-check"></i> {{ __('Why Choose Us') }}</span>
          <h2>{{ __('We provide the best service with experienced professionals') }}</h2>
          <a href="{{ route('frontend.services') }}" class="yt-btn-primary">{{ __('Book a service now') }} <i
              class="fal fa-long-arrow-right"></i></a>
        </div>
        <div class="yt-why-list">
          <div class="yt-why-row" data-aos="fade-up">
            <div class="yt-why-icon yt-tint-green">
              <i class="fal fa-certificate"></i>
            </div>
            <div>
              <h4>{{ __('Certified & Experienced Professionals') }}</h4>
              <p>
                {{ __('Our team consists of certified and highly experienced professionals dedicated to providing top-quality service') }}
              </p>
            </div>
          </div>
          <div class="yt-why-row" data-aos="fade-up" data-aos-delay="100">
            <div class="yt-why-icon yt-tint-amber">
              <i class="fal fa-tags"></i>
            </div>
            <div>
              <h4>{{ __('Affordable & Transparent Pricing') }}</h4>
              <p>
                {{ __('We offer competitive and transparent pricing with no hidden fees, ensuring you get the best value for your money') }}
              </p>
            </div>
          </div>
          <div class="yt-why-row" data-aos="fade-up" data-aos-delay="200">
            <div class="yt-why-icon yt-tint-blue">
              <i class="fal fa-headset"></i>
            </div>
            <div>
              <h4>{{ __('24/7 Customer Support') }}</h4>
              <p>
                {{ __('Our dedicated support team is available round the clock to assist you with any questions or concerns') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ══════════ Why Choose Us end ══════════ -->

  <!-- ══════════ How It Works start ══════════ -->
  <section class="yt-section yt-steps-section">
    <div class="container">
      <div class="yt-section-head yt-head-center" data-aos="fade-up">
        <span class="yt-eyebrow"><i class="fal fa-route"></i> {{ __('How It Works') }}</span>
        <h2>{{ __('Book your service in 4 simple steps') }}</h2>
      </div>
      <div class="yt-steps-line">
        <div class="yt-step" data-aos="fade-up" data-aos-delay="100">
          <div class="yt-step-node">
            <i class="fal fa-search"></i>
            <em>1</em>
          </div>
          <h4>{{ __('Search Service') }}</h4>
          <p>{{ __('Browse and find the perfect service that meets your needs') }}</p>
        </div>
        <div class="yt-step" data-aos="fade-up" data-aos-delay="200">
          <div class="yt-step-node">
            <i class="fal fa-calendar-alt"></i>
            <em>2</em>
          </div>
          <h4>{{ __('Select Date & Time') }}</h4>
          <p>{{ __('Choose your preferred date and time slot from available options') }}</p>
        </div>
        <div class="yt-step" data-aos="fade-up" data-aos-delay="300">
          <div class="yt-step-node">
            <i class="fal fa-credit-card"></i>
            <em>3</em>
          </div>
          <h4>{{ __('Make Payment') }}</h4>
          <p>{{ __('Complete your booking with secure online payment options') }}</p>
        </div>
        <div class="yt-step" data-aos="fade-up" data-aos-delay="400">
          <div class="yt-step-node">
            <i class="fal fa-check-circle"></i>
            <em>4</em>
          </div>
          <h4>{{ __('Get Confirmation') }}</h4>
          <p>{{ __('Receive instant confirmation and enjoy your booked service') }}</p>
        </div>
      </div>
    </div>
  </section>
  <!-- ══════════ How It Works end ══════════ -->

  <!-- ══════════ Reviews start ══════════ -->
  <section class="yt-reviews-section" id="reviews">
    <div class="container">
      <div class="yt-section-head yt-head-center" data-aos="fade-up">
        <span class="yt-eyebrow yt-eyebrow-ondark"><i class="fal fa-star"></i> {{ __('Customer reviews') }}</span>
        <h2>{{ __('Trusted by UK customers for urgent jobs, certificates and home services.') }}</h2>
      </div>

      <div class="yt-reviews-grid" data-aos="fade-up">
        <article class="yt-review-card">
          <div class="yt-review-stars" aria-label="{{ __('5 star review') }}">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <blockquote>{{ __('Booked Friday night and the boiler was fixed Saturday morning. Clear price, quick arrival, no drama.') }}</blockquote>
          <div class="yt-review-meta">
            <span><strong>{{ __('Sarah M') }}</strong>{{ __('Croydon') }}</span>
            <span class="yt-review-tag">{{ __('Boiler repair') }}</span>
          </div>
        </article>

        <article class="yt-review-card">
          <div class="yt-review-stars" aria-label="{{ __('5 star review') }}">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <blockquote>{{ __('Needed an EICR for a rental property. The engineer arrived on time and the report was easy to share with my agent.') }}</blockquote>
          <div class="yt-review-meta">
            <span><strong>{{ __('Imran K') }}</strong>{{ __('Ilford') }}</span>
            <span class="yt-review-tag">{{ __('EICR') }}</span>
          </div>
        </article>

        <article class="yt-review-card">
          <div class="yt-review-stars" aria-label="{{ __('5 star review') }}">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <blockquote>{{ __('End-of-tenancy cleaning was spotless. Booking took less than a minute and the team kept me updated.') }}</blockquote>
          <div class="yt-review-meta">
            <span><strong>{{ __('Amelia R') }}</strong>{{ __('Wembley') }}</span>
            <span class="yt-review-tag">{{ __('Cleaning') }}</span>
          </div>
        </article>
      </div>

      <div class="yt-section-cta" data-aos="fade-up">
        <a href="{{ route('frontend.services') }}" class="yt-btn-amber">
          {{ __('Book a service now') }} <i class="fal fa-long-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>
  <!-- ══════════ Reviews end ══════════ -->

  <!-- ══════════ Work process start ══════════ -->
  @if ($secInfo->work_process_section_status == 1)
    <section class="yt-section yt-process-section">
      <div class="container">
        <div class="yt-section-head yt-head-center" data-aos="fade-up">
          <span class="yt-eyebrow"><i class="fal fa-clipboard-check"></i> {{ __('Booking process') }}</span>
          <h2>
            {{ !empty($sectionContent->workprocess_section_title) ? $sectionContent->workprocess_section_title : 'How the Appointment Booking System Works ' }}
          </h2>
        </div>
        <div class="row g-4 justify-content-center">
          @foreach ($processes as $processe)
            <div class="col-xl-4 col-lg-4 col-sm-6" data-aos="fade-up">
              <div class="yt-process-card">
                <span class="yt-process-num">0{{ $loop->iteration }}</span>
                <div class="yt-process-img">
                  <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                    data-src="{{ asset('assets/img/workprocess/' . $processe->image) }}" alt="Image">
                </div>
                <h4>{{ $processe->title }}</h4>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
  <!-- ══════════ Work process end ══════════ -->

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

  <!-- ══════════ Featured services start ══════════ -->
  @if ($secInfo->feature_section_status == 1)
    <section class="yt-section yt-featured-section">
      <div class="container">
        <div class="yt-section-head yt-head-split" data-aos="fade-up">
          <div>
            <span class="yt-eyebrow"><i class="fal fa-gem"></i> {{ __('Hand-picked for you') }}</span>
            <h2>
              {{ !empty($sectionContent->featured_service_section_title) ? $sectionContent->featured_service_section_title : 'Our Top Featured Services' }}
            </h2>
          </div>
          @if ($featured_services->count() > 4)
            <div class="yt-slider-nav">
              <button type="button" title="Slide prev" class="yt-slider-btn" id="product-slider-1-prev">
                <i class="fal fa-angle-left"></i>
              </button>
              <button type="button" title="Slide next" class="yt-slider-btn" id="product-slider-1-next">
                <i class="fal fa-angle-right"></i>
              </button>
            </div>
          @endif
        </div>

        @if ($featured_services->count() == 0)
          <h4 class="text-center">{{ __('NO SERVICE FOUND') . '!' }}</h4>
        @else
          <div class="swiper product-slider yt-card-slider" id="product-slider-1" data-slides-per-view="4"
            data-swiper-loop="false" data-aos="fade-up">
            <div class="swiper-wrapper">
              @foreach ($featured_services as $service)
                <div class="swiper-slide">
                  <div class="yt-scard">
                    <div class="yt-scard-media">
                      <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                        title="{{ $service->name }}" target="_self">
                        <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                          data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="Service">
                      </a>
                      <a href="{{ route('frontend.services', ['category_id' => $service->categoryId]) }}"
                        class="yt-scard-tag">{{ $service->categoryName }}</a>
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
                        class="yt-scard-wish {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                        data-tooltip="tooltip" data-bs-placement="right"
                        title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                        <i class="fal fa-heart"></i>
                      </a>
                      <span class="yt-price-chip">
                        @if ($service->hasPricePlaceholder())
                          {{ $service->price_placeholder }}
                        @else
                          {{ symbolPrice($service->price) }}
                          @if ($service->prev_price)
                            <del>{{ symbolPrice($service->prev_price) }}</del>
                          @endif
                        @endif
                      </span>
                    </div>
                    <div class="yt-scard-body">
                      <h6 class="yt-scard-title">
                        <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                          target="_self" title="{{ $service->name }}">
                          {{ truncateString($service->name, 60) }}
                        </a>
                      </h6>
                      @if ($service->zoom_meeting == 1)
                        <span class="yt-scard-meta"><i class="fal fa-video"></i>{{ __('Online') }}</span>
                      @endif
                      <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST"
                        class="yt-scard-cartform">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="yt-scard-btn" title="{{ __('Book Now') }}">
                          <i class="fal fa-calendar-check"></i>{{ __('Book Now') }}
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="swiper-pagination position-static" id="product-slider-1-pagination"></div>
          </div>
        @endif
      </div>
    </section>
  @endif
  <!-- ══════════ Featured services end ══════════ -->

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

  <!-- ══════════ Banner collection start ══════════ -->
  @if ($secInfo->banner_section == 1)
    <div class="yt-banner-collection">
      <div class="container">
        <div class="row g-4">
          @foreach ($banners as $banner)
            <div class="col-sm-6" data-aos="fade-up">
              <a href="{{ $banner->url }}" class="yt-banner-card bg-img ratio ratio-21-9"
                data-bg-image="{{ asset('assets/img/banners/' . $banner->image) }}">
                <div class="yt-banner-overlay">
                  <h3>{{ $banner->title }}</h3>
                  <span class="yt-banner-arrow"><i class="fal fa-long-arrow-right"></i></span>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif
  <!-- ══════════ Banner collection end ══════════ -->

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

  <!-- ══════════ Action banner start ══════════ -->
  @if ($secInfo->call_to_action_section_status == 1)
    <section class="yt-cta-section">
      <div class="container">
        <div class="yt-cta-panel bg-img bg-cover"
          data-bg-image="{{ asset('assets/img/' . @$sectionContent->call_to_action_section_image) }}">
          <div class="yt-cta-inner" data-aos="fade-up">
            <h2>
              {{ !empty($sectionContent->call_to_action_section_title) ? $sectionContent->call_to_action_section_title : 'Now Get 50% Discount For First Booking' }}
            </h2>
            <p>
              {{ !empty($sectionContent->action_section_text) ? $sectionContent->action_section_text : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim' }}
            </p>
            @if (!empty($sectionContent->call_to_action_url))
              <div class="yt-cta-btn-wrap">
                <a href="{{ @$sectionContent->call_to_action_url }}" class="yt-btn-light" target="_self"><i
                    class="{{ @$sectionContent->call_to_action_icon }}"></i>{{ @$sectionContent->call_to_action_section_btn }}</a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  @endif
  <!-- ══════════ Action banner end ══════════ -->

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

  <!-- ══════════ Vendors start ══════════ -->
  @if ($secInfo->vendor_featured_section_status == 1)
    <section class="yt-section yt-vendors-section">
      <div class="container">
        <div class="yt-section-head yt-head-split" data-aos="fade-up">
          <div>
            <span class="yt-eyebrow"><i class="fal fa-store"></i> {{ __('Trusted partners') }}</span>
            <h2>
              {{ !empty($sectionContent->vendor_section_title) ? $sectionContent->vendor_section_title : 'Our Top Featured Vendor' }}
            </h2>
          </div>
          @if ($vendors > 0)
            <a href="{{ route('frontend.vendors') }}" class="yt-btn-primary" target="_self">
              @if ($vendors > 1)
                {{ __('View All Vendors') }}
              @else
                {{ __('View All Vendor') }}
              @endif
              <i class="fal fa-arrow-right"></i>
            </a>
          @endif
        </div>

        @if ($featuredVendors->count() == 0)
          <h4 class="text-center">{{ __('NO VENDOR FOUND') . '!' }}</h4>
        @else
          <div class="swiper product-slider yt-card-slider" id="product-slider-2" data-slides-per-view="4"
            data-swiper-loop="false" data-aos="fade-up">
            <div class="swiper-wrapper">
              @foreach ($featuredVendors as $vendor)
                <div class="swiper-slide">
                  <div class="yt-vendor-card">
                    <div class="yt-vendor-photo">
                      <a href="{{ route('frontend.vendor.details', ['username' => $vendor->username]) }}"
                        title="{{ $vendor->username }}" target="_self">
                        @if ($vendor->photo)
                          <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                            data-src="{{ asset('assets/admin/img/vendor-photo/' . $vendor->photo) }}" alt="Vendor">
                        @else
                          <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                            data-src="{{ asset('assets/img/user.png') }}" alt="Vendor">
                        @endif
                      </a>
                    </div>
                    <div class="yt-vendor-body">
                      @php
                        $vendorInfo = App\Models\VendorInfo::where([
                          ['vendor_id', $vendor->vendorId],
                          ['language_id', $language->id],
                        ])->first();
                      @endphp
                      <h6 class="yt-vendor-name">
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
                          <span class="yt-vendor-loc"><i
                              class="fal fa-map-marker-alt"></i>{{ truncateString($vendorInfo->address, 30) }}</span>
                        @endif
                      @endif
                      @php
                        $total_service = App\Models\Services\Services::where('vendor_id', $vendor->vendorId)
                          ->where('status', 1)
                          ->count();
                      @endphp
                      <span class="yt-vendor-count">
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
                      <a href="{{ route('frontend.vendor.details', ['username' => $vendor->username]) }}"
                        class="yt-vendor-btn" title="{{ __('Visit Store') }}"
                        target="_self">{{ __('Visit Store') }} <i class="fal fa-long-arrow-right"></i></a>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="swiper-pagination position-static" id="product-slider-2-pagination"></div>
          </div>
        @endif
      </div>
    </section>
  @endif
  <!-- ══════════ Vendors end ══════════ -->

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

  @push('scripts')
    <style>
      /* ═══════════════ Homepage redesign v2 — emerald & slate ═══════════════ */

      :root {
        /* Align the sitewide theme color with the new homepage scheme (homepage only) */
        --color-primary: #0e9f6e;
        --color-primary-rgb: 14, 159, 110;
        --yt-primary: #0e9f6e;
        --yt-primary-dark: #0a7d57;
        --yt-primary-deep: #06432f;
        --yt-primary-soft: #e5f5ee;
        --yt-amber: #f5a524;
        --yt-ink: #101d2c;
        --yt-ink-2: #33415c;
        --yt-muted: #5e6b7e;
        --yt-line: #e4e9e6;
        --yt-bg: #f4f7f5;
        --yt-radius: 20px;
        --yt-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
        --yt-shadow-lg: 0 28px 64px rgba(16, 29, 44, 0.13);
      }

      /* ── Hero (light) ─────────────────────────────── */
      .yt-hero {
        position: relative;
        overflow: hidden;
        padding: 190px 0 130px;
        background:
          radial-gradient(circle at 90% 0%, rgba(14, 159, 110, 0.12), transparent 34%),
          radial-gradient(circle at 2% 90%, rgba(245, 165, 36, 0.1), transparent 30%),
          linear-gradient(180deg, #eef4f0 0%, #f8faf8 70%, #ffffff 100%);
        color: var(--yt-ink);
      }

      .yt-hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(70px);
        pointer-events: none;
      }

      .yt-hero-blob-1 {
        top: -140px;
        right: -100px;
        width: 480px;
        height: 480px;
        background: rgba(14, 159, 110, 0.16);
      }

      .yt-hero-blob-2 {
        bottom: -200px;
        left: -140px;
        width: 460px;
        height: 460px;
        background: rgba(245, 165, 36, 0.12);
      }

      .yt-hero .container {
        position: relative;
        z-index: 1;
      }

      .yt-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 440px;
        align-items: center;
        gap: 72px;
      }

      .yt-hero-copy {
        max-width: 640px;
      }

      .yt-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 24px;
        padding: 9px 16px;
        color: var(--yt-primary-dark);
        background: #ffffff;
        border: 1px solid rgba(14, 159, 110, 0.25);
        border-radius: 999px;
        box-shadow: 0 8px 22px rgba(14, 159, 110, 0.1);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.06em;
        line-height: 1.3;
        text-transform: uppercase;
      }

      .yt-eyebrow-dot {
        width: 8px;
        height: 8px;
        background: var(--yt-primary);
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(14, 159, 110, 0.18);
        animation: ytPulse 2.2s infinite;
      }

      @keyframes ytPulse {
        0%, 100% { box-shadow: 0 0 0 4px rgba(14, 159, 110, 0.18); }
        50% { box-shadow: 0 0 0 8px rgba(14, 159, 110, 0.07); }
      }

      .yt-hero h1 {
        margin: 0 0 22px;
        color: var(--yt-ink);
        font-size: clamp(38px, 4.6vw, 62px);
        line-height: 1.06;
        font-weight: 900;
        letter-spacing: -0.02em;
      }

      .yt-hero-mark {
        position: relative;
        display: inline-block;
        color: var(--yt-primary-dark);
      }

      .yt-hero-mark::after {
        content: "";
        position: absolute;
        left: 2px;
        right: 2px;
        bottom: 4px;
        height: 12px;
        background: rgba(245, 165, 36, 0.4);
        border-radius: 6px;
        z-index: -1;
        transform: skewX(-8deg);
      }

      .yt-hero-copy > p {
        max-width: 560px;
        margin: 0;
        color: var(--yt-muted);
        font-size: 16.5px;
        line-height: 1.75;
      }

      /* search */
      .yt-hero-search {
        max-width: 600px;
        margin-top: 30px;
      }

      .yt-hero-search-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
        padding: 8px;
        background: #ffffff;
        border: 1px solid var(--yt-line);
        border-radius: 999px;
        box-shadow: 0 22px 55px rgba(16, 29, 44, 0.12);
      }

      .yt-hero-search-field {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 52px;
        padding: 0 10px 0 20px;
      }

      .yt-hero-search-field label {
        margin: 0;
        color: var(--yt-primary);
        font-size: 17px;
        line-height: 1;
      }

      .yt-hero-search-field input {
        width: 100%;
        min-width: 0;
        height: 100%;
        color: var(--yt-ink);
        background: transparent;
        border: 0;
        outline: 0;
        font-size: 15px;
        font-weight: 500;
      }

      .yt-hero-search-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 52px;
        padding: 0 30px;
        color: #ffffff;
        background: linear-gradient(120deg, var(--yt-primary), var(--yt-primary-dark));
        border: 0;
        border-radius: 999px;
        font-size: 15px;
        font-weight: 800;
        white-space: nowrap;
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
      }

      .yt-hero-search-btn:hover {
        filter: brightness(1.06);
        transform: translateY(-1px);
        box-shadow: 0 14px 32px rgba(14, 159, 110, 0.4);
        color: #fff;
      }

      .yt-hero-popular {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
        padding: 0 8px;
        color: var(--yt-muted);
        font-size: 12.5px;
        font-weight: 700;
      }

      .yt-hero-popular a {
        padding: 6px 14px;
        color: var(--yt-ink-2);
        background: #ffffff;
        border: 1px solid var(--yt-line);
        border-radius: 999px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
      }

      .yt-hero-popular a:hover {
        color: #ffffff;
        background: var(--yt-primary);
        border-color: var(--yt-primary);
        box-shadow: 0 8px 20px rgba(14, 159, 110, 0.3);
      }

      .yt-hero-trust {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px 26px;
        margin-top: 26px;
        color: var(--yt-ink-2);
        font-size: 13.5px;
        font-weight: 700;
      }

      .yt-hero-trust i {
        margin-right: 7px;
        color: var(--yt-primary);
      }

      .yt-hero-trust span:nth-child(2) i {
        color: var(--yt-amber);
      }

      .yt-hero-trust span:nth-child(3) i {
        color: #2f80ed;
      }

      /* hero media */
      .yt-hero-media {
        position: relative;
        max-width: 440px;
      }

      .yt-hero-media-frame {
        position: relative;
      }

      .yt-hero-media-frame::before {
        content: "";
        position: absolute;
        inset: -16px;
        background: linear-gradient(140deg, rgba(14, 159, 110, 0.45), rgba(245, 165, 36, 0.35));
        border-radius: 38px;
        filter: blur(22px);
        opacity: 0.65;
        z-index: 0;
      }

      .yt-hero-media-frame::after {
        content: "";
        position: absolute;
        top: -22px;
        right: -14px;
        width: 96px;
        height: 96px;
        background-image: radial-gradient(var(--yt-amber) 2.2px, transparent 2.2px);
        background-size: 15px 15px;
        opacity: 0.55;
        z-index: 0;
      }

      .yt-hero-slide-card {
        position: relative;
        overflow: hidden;
        height: 480px;
        background: #dfe8e2;
        border: 5px solid #ffffff;
        border-radius: 26px;
        box-shadow: 0 30px 70px rgba(16, 29, 44, 0.18);
        z-index: 1;
      }

      .yt-hero-slide-card img,
      .heroVisualSlider,
      .heroVisualSliderInline,
      .heroVisualSlider .swiper-wrapper,
      .heroVisualSliderInline .swiper-wrapper,
      .heroVisualSlider .swiper-slide,
      .heroVisualSliderInline .swiper-slide {
        width: 100%;
        height: 100%;
      }

      .yt-hero-slide-card img {
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

      .yt-hero-media-inline .heroVisualSliderInline {
        position: relative;
        overflow: hidden;
      }

      .yt-hero-media-inline .heroVisualSliderInline .swiper-wrapper {
        position: relative;
        transform: none !important;
      }

      .yt-hero-media-inline .heroVisualSliderInline .swiper-slide {
        position: absolute !important;
        inset: 0;
        opacity: 0 !important;
        visibility: hidden !important;
        background: #dfe8e2;
        transition: opacity 0.6s ease;
      }

      .yt-hero-media-inline .heroVisualSliderInline .swiper-slide.is-active,
      .yt-hero-media-inline .heroVisualSliderInline .swiper-slide:first-child {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
      }

      .yt-hero-media-inline .heroVisualSliderInline img {
        width: 100%;
        height: 100%;
        opacity: 1 !important;
        visibility: visible !important;
      }

      .yt-hero-media-inline {
        display: none;
      }

      .yt-hero-chip {
        position: absolute;
        left: 14px;
        top: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 15px;
        color: #ffffff;
        background: rgba(6, 67, 47, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        z-index: 2;
        backdrop-filter: blur(6px);
      }

      .yt-hero-chip::before {
        content: "";
        width: 8px;
        height: 8px;
        background: #4ade80;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.25);
        animation: ytPulse 2.2s infinite;
      }

      .hero-visual-pagination .swiper-pagination-bullet {
        width: 7px;
        height: 7px;
        opacity: 0.55;
        transition: all 0.25s ease;
      }

      .hero-visual-pagination .swiper-pagination-bullet-active {
        width: 22px;
        border-radius: 999px;
        opacity: 1;
        background: #ffffff;
      }

      .yt-hero-float-card {
        position: absolute;
        left: -40px;
        bottom: -26px;
        display: flex;
        gap: 13px;
        width: 270px;
        padding: 16px 18px;
        color: var(--yt-ink);
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 24px 55px rgba(16, 29, 44, 0.18);
        z-index: 3;
      }

      .yt-hero-float-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 46px;
        height: 46px;
        color: var(--yt-primary-dark);
        background: var(--yt-primary-soft);
        border-radius: 14px;
        font-size: 20px;
      }

      .yt-hero-float-card p {
        margin: 0 0 6px;
        color: var(--yt-ink-2);
        font-size: 12.5px;
        line-height: 1.5;
        font-weight: 600;
      }

      .yt-hero-float-card span {
        display: block;
        color: #8a95a5;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
      }

      .hero-visual-pagination {
        bottom: 12px !important;
      }

      .hero-visual-pagination .swiper-pagination-bullet,
      .hero-visual-pagination-inline .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.9);
      }

      /* stat bar — dark slab */
      .yt-statbar-wrap {
        position: relative;
        z-index: 5;
        margin-top: -60px;
        margin-bottom: 40px;
      }

      .yt-statbar {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        background: linear-gradient(115deg, #0d1b16 0%, var(--yt-primary-deep) 100%);
        border-radius: var(--yt-radius);
        box-shadow: 0 30px 60px rgba(6, 67, 47, 0.3);
        overflow: hidden;
      }

      .yt-stat {
        position: relative;
        padding: 30px 26px;
        text-align: center;
      }

      .yt-stat + .yt-stat::before {
        content: "";
        position: absolute;
        left: 0;
        top: 24%;
        height: 52%;
        width: 1px;
        background: rgba(255, 255, 255, 0.14);
      }

      .yt-stat strong {
        display: block;
        color: #ffffff;
        font-size: 28px;
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -0.01em;
      }

      .yt-stat span {
        display: block;
        margin-top: 8px;
        color: #79c4a8;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.09em;
        line-height: 1.3;
        text-transform: uppercase;
      }

      /* ── Shared ───────────────────────────────────── */
      .yt-section {
        padding: 84px 0;
      }

      .yt-section-head {
        margin-bottom: 46px;
      }

      .yt-head-center {
        max-width: 780px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
      }

      .yt-head-split {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
      }

      .yt-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        padding: 8px 15px;
        color: var(--yt-primary-dark);
        background: var(--yt-primary-soft);
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 900;
        line-height: 1.3;
        letter-spacing: 0.09em;
        text-transform: uppercase;
      }

      .yt-eyebrow-ondark {
        color: #8be3bf;
        background: rgba(139, 227, 191, 0.12);
      }

      .yt-section-head h2 {
        margin: 0;
        color: var(--yt-ink);
        font-size: clamp(28px, 3.2vw, 40px);
        line-height: 1.14;
        font-weight: 900;
        letter-spacing: -0.015em;
      }

      .yt-section-cta {
        display: flex;
        justify-content: center;
        margin-top: 42px;
      }

      .yt-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 50px;
        padding: 0 28px;
        color: #ffffff;
        background: linear-gradient(120deg, var(--yt-primary), var(--yt-primary-dark));
        border: 0;
        border-radius: 999px;
        font-size: 14.5px;
        font-weight: 800;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
      }

      .yt-btn-primary:hover {
        color: #ffffff;
        filter: brightness(1.06);
        transform: translateY(-2px);
        box-shadow: 0 16px 34px rgba(14, 159, 110, 0.38);
      }

      .yt-btn-amber {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 52px;
        padding: 0 30px;
        color: #231303;
        background: linear-gradient(120deg, #ffc253, var(--yt-amber));
        border-radius: 999px;
        font-size: 15px;
        font-weight: 900;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .yt-btn-amber:hover {
        color: #231303;
        transform: translateY(-2px);
        box-shadow: 0 18px 38px rgba(245, 165, 36, 0.4);
      }

      .yt-btn-light {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 52px;
        padding: 0 30px;
        color: var(--yt-primary-deep);
        background: #ffffff;
        border-radius: 999px;
        font-size: 15px;
        font-weight: 800;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .yt-btn-light:hover {
        color: var(--yt-primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.25);
      }

      .yt-btn-light i {
        margin-right: 2px;
      }

      /* ── Tabs ─────────────────────────────────────── */
      .yt-tabs {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 26px;
        max-width: 100%;
      }

      .yt-tabs-scroll {
        max-width: 100%;
        min-width: 0;
        background: var(--yt-bg);
        border: 1px solid var(--yt-line);
        border-radius: 999px;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
      }

      .yt-tabs-scroll::-webkit-scrollbar {
        display: none;
      }

      .yt-tabs .nav-tabs {
        display: flex;
        justify-content: flex-start;
        flex-wrap: nowrap;
        gap: 6px;
        width: max-content;
        margin: 0;
        padding: 6px;
        background: transparent;
        border: 0;
      }

      .yt-tabs-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        color: var(--yt-primary-dark);
        background: #ffffff;
        border: 1px solid var(--yt-line);
        border-radius: 50%;
        font-size: 17px;
        box-shadow: 0 8px 20px rgba(16, 29, 44, 0.08);
        transition: all 0.2s ease;
      }

      .yt-tabs-arrow:hover {
        color: #ffffff;
        background: var(--yt-primary);
        border-color: var(--yt-primary);
        box-shadow: 0 12px 26px rgba(14, 159, 110, 0.32);
      }

      .yt-tabs-arrow.is-hidden {
        display: none;
      }

      .yt-tabs-arrow:disabled {
        opacity: 0.35;
        pointer-events: none;
      }

      .yt-tabs .nav-tabs .nav-item {
        flex: 0 0 auto;
      }

      .yt-tabs .nav-tabs .nav-link {
        margin: 0;
        padding: 10px 20px;
        color: var(--yt-ink-2);
        background: transparent;
        border: 0;
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 700;
        white-space: nowrap;
        transition: all 0.2s ease;
      }

      .yt-tabs .nav-tabs .nav-link:hover {
        color: var(--yt-primary-dark);
      }

      .yt-tabs .nav-tabs .nav-link.active {
        color: #ffffff;
        background: linear-gradient(120deg, var(--yt-primary), var(--yt-primary-dark));
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.32);
      }

      /* ── Service card ─────────────────────────────── */
      .yt-scard {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #ffffff;
        border-radius: var(--yt-radius);
        box-shadow: var(--yt-shadow);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .yt-scard:hover {
        transform: translateY(-8px);
        box-shadow: var(--yt-shadow-lg);
      }

      .yt-scard-media {
        position: relative;
        overflow: hidden;
        margin: 10px 10px 0;
        border-radius: 14px;
      }

      .yt-scard-media > a {
        display: block;
      }

      .yt-scard-media img {
        display: block;
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        transition: transform 0.45s ease;
      }

      .yt-scard:hover .yt-scard-media img {
        transform: scale(1.06);
      }

      .yt-scard-tag {
        position: absolute;
        left: 10px;
        top: 10px;
        z-index: 2;
        max-width: calc(100% - 62px);
        padding: 6px 12px;
        color: var(--yt-primary-deep);
        background: #ffffff;
        border-radius: 999px;
        box-shadow: 0 6px 16px rgba(16, 29, 44, 0.12);
        font-size: 11.5px;
        font-weight: 800;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-decoration: none;
        transition: color 0.2s ease, background 0.2s ease;
      }

      .yt-scard-tag:hover {
        color: #ffffff;
        background: var(--yt-primary);
      }

      .yt-scard-media .yt-scard-wish,
      .yt-scard-media .yt-scard-wish:focus,
      .yt-scard-media .yt-scard-wish:active,
      .yt-scard-media .yt-scard-wish:visited {
        position: absolute;
        right: 10px;
        top: 10px;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        margin: 0;
        padding: 0;
        color: var(--yt-ink);
        background: #ffffff;
        border: 0;
        border-radius: 50%;
        box-shadow: 0 6px 16px rgba(16, 29, 44, 0.14);
        font-size: 14px;
        line-height: 1;
        text-decoration: none;
        outline: none;
        overflow: hidden;
        transition: color 0.2s ease, background 0.2s ease;
      }

      .yt-scard-wish::before,
      .yt-scard-wish::after {
        content: none !important;
      }

      .yt-scard-wish i {
        display: block;
        line-height: 1;
      }

      .yt-scard-media .yt-scard-wish:hover,
      .yt-scard-media .yt-scard-wish.wishlist-active {
        color: #ffffff;
        background: #e8506c;
      }

      .yt-price-chip {
        position: absolute;
        left: 10px;
        bottom: 10px;
        z-index: 2;
        display: inline-flex;
        align-items: baseline;
        gap: 7px;
        padding: 7px 13px;
        color: #ffffff;
        background: rgba(6, 67, 47, 0.88);
        border-radius: 999px;
        font-size: 14px;
        font-weight: 900;
        backdrop-filter: blur(4px);
      }

      .yt-price-chip del {
        color: #9fd3bd;
        font-size: 11.5px;
        font-weight: 600;
      }

      .yt-scard-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        gap: 9px;
        padding: 16px;
      }

      .yt-scard-title {
        margin: 0;
        font-size: 15.5px;
        line-height: 1.4;
        font-weight: 800;
      }

      .yt-scard-title a {
        color: var(--yt-ink);
        text-decoration: none;
        transition: color 0.2s ease;
      }

      .yt-scard-title a:hover {
        color: var(--yt-primary-dark);
      }

      .yt-scard-meta {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--yt-muted);
        font-size: 12.5px;
        font-weight: 600;
      }

      .yt-scard-meta i {
        color: var(--yt-primary);
      }

      .yt-scard-cartform {
        margin: auto 0 0 !important;
        padding-top: 6px;
      }

      .yt-scard-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 42px;
        padding: 0 16px;
        color: var(--yt-primary-dark);
        background: var(--yt-primary-soft);
        border: 0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.2s ease;
      }

      .yt-scard-btn:hover {
        color: #ffffff;
        background: var(--yt-primary);
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.32);
      }

      /* ── Section backgrounds ──────────────────────── */
      .yt-services-section {
        background: #ffffff;
      }

      .yt-why-section {
        background: var(--yt-bg);
      }

      .yt-steps-section {
        background: #ffffff;
      }

      .yt-process-section {
        background: #ffffff;
      }

      .yt-featured-section {
        background: var(--yt-bg);
      }

      .yt-vendors-section {
        background: var(--yt-bg);
      }

      /* ── Why choose us (split) ────────────────────── */
      .yt-why-grid {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
        gap: 64px;
        align-items: center;
      }

      .yt-why-intro h2 {
        margin: 0 0 26px;
        color: var(--yt-ink);
        font-size: clamp(28px, 3vw, 38px);
        line-height: 1.16;
        font-weight: 900;
        letter-spacing: -0.015em;
      }

      .yt-why-list {
        display: flex;
        flex-direction: column;
      }

      .yt-why-row {
        display: flex;
        gap: 22px;
        padding: 26px 24px;
        background: #ffffff;
        border-radius: var(--yt-radius);
        box-shadow: var(--yt-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .yt-why-row + .yt-why-row {
        margin-top: 16px;
      }

      .yt-why-row:hover {
        transform: translateX(8px);
        box-shadow: var(--yt-shadow-lg);
      }

      .yt-why-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 58px;
        height: 58px;
        border-radius: 16px;
        font-size: 24px;
      }

      .yt-tint-green {
        color: var(--yt-primary-dark);
        background: var(--yt-primary-soft);
      }

      .yt-tint-amber {
        color: #b06e07;
        background: rgba(245, 165, 36, 0.14);
      }

      .yt-tint-blue {
        color: #2f80ed;
        background: rgba(47, 128, 237, 0.11);
      }

      .yt-why-row h4 {
        margin: 0 0 8px;
        color: var(--yt-ink);
        font-size: 17.5px;
        font-weight: 800;
        line-height: 1.35;
      }

      .yt-why-row p {
        margin: 0;
        color: var(--yt-muted);
        font-size: 14px;
        line-height: 1.7;
      }

      /* ── How it works (timeline) ──────────────────── */
      .yt-steps-line {
        position: relative;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 26px;
      }

      .yt-steps-line::before {
        content: "";
        position: absolute;
        top: 39px;
        left: 12%;
        right: 12%;
        border-top: 2px dashed rgba(14, 159, 110, 0.35);
        z-index: 0;
      }

      .yt-step {
        position: relative;
        z-index: 1;
        text-align: center;
        padding: 0 8px;
      }

      .yt-step-node {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 78px;
        height: 78px;
        margin: 0 auto 20px;
        color: var(--yt-primary-dark);
        background: #ffffff;
        border: 2px solid rgba(14, 159, 110, 0.35);
        border-radius: 50%;
        font-size: 27px;
        box-shadow: 0 14px 30px rgba(14, 159, 110, 0.16);
        transition: all 0.25s ease;
      }

      .yt-step:hover .yt-step-node {
        color: #ffffff;
        background: linear-gradient(135deg, var(--yt-primary), var(--yt-primary-dark));
        border-color: transparent;
        transform: translateY(-4px);
      }

      .yt-step-node em {
        position: absolute;
        top: -6px;
        right: -6px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        color: #231303;
        background: var(--yt-amber);
        border-radius: 50%;
        font-size: 12.5px;
        font-style: normal;
        font-weight: 900;
        box-shadow: 0 6px 14px rgba(245, 165, 36, 0.45);
      }

      .yt-step h4 {
        margin: 0 0 9px;
        color: var(--yt-ink);
        font-size: 16.5px;
        font-weight: 800;
      }

      .yt-step p {
        margin: 0;
        color: var(--yt-muted);
        font-size: 13.5px;
        line-height: 1.65;
      }

      /* ── Reviews (dark) ───────────────────────────── */
      .yt-reviews-section {
        padding: 88px 0;
        background:
          radial-gradient(circle at 92% 6%, rgba(14, 159, 110, 0.28), transparent 34%),
          radial-gradient(circle at 4% 96%, rgba(245, 165, 36, 0.12), transparent 30%),
          #0d1b16;
      }

      .yt-reviews-section .yt-section-head h2 {
        color: #ffffff;
      }

      .yt-reviews-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
      }

      .yt-review-card {
        display: flex;
        flex-direction: column;
        padding: 28px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: var(--yt-radius);
        backdrop-filter: blur(6px);
        transition: transform 0.25s ease, background 0.25s ease;
      }

      .yt-review-card:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-6px);
      }

      .yt-review-stars {
        display: flex;
        gap: 4px;
        margin-bottom: 16px;
        color: var(--yt-amber);
        font-size: 14px;
      }

      .yt-review-card blockquote {
        margin: 0 0 20px;
        color: #d7e3dc;
        font-size: 15px;
        line-height: 1.7;
      }

      .yt-review-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: auto;
        color: #8ba296;
        font-size: 13px;
      }

      .yt-review-meta strong {
        display: block;
        color: #ffffff;
        font-size: 14px;
        font-weight: 900;
      }

      .yt-review-tag {
        padding: 6px 12px;
        color: #8be3bf;
        background: rgba(139, 227, 191, 0.12);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
      }

      /* ── Work process ─────────────────────────────── */
      .yt-process-card {
        position: relative;
        height: 100%;
        padding: 30px 26px 28px;
        text-align: center;
        background: #ffffff;
        border: 1px solid var(--yt-line);
        border-radius: var(--yt-radius);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .yt-process-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--yt-shadow-lg);
      }

      .yt-process-num {
        position: absolute;
        top: 16px;
        left: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 8px;
        color: #ffffff;
        background: linear-gradient(135deg, var(--yt-primary), var(--yt-primary-dark));
        border-radius: 14px;
        font-size: 15px;
        font-weight: 900;
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.3);
      }

      .yt-process-img {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 150px;
        margin-bottom: 18px;
      }

      .yt-process-img img {
        max-width: 100%;
        max-height: 150px;
        object-fit: contain;
      }

      .yt-process-card h4 {
        margin: 0;
        color: var(--yt-ink);
        font-size: 17.5px;
        font-weight: 800;
        line-height: 1.4;
      }

      /* ── Sliders ──────────────────────────────────── */
      .yt-slider-nav {
        display: flex;
        gap: 10px;
      }

      .yt-slider-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        color: var(--yt-ink);
        background: #ffffff;
        border: 1px solid var(--yt-line);
        border-radius: 50%;
        font-size: 17px;
        transition: all 0.2s ease;
      }

      .yt-slider-btn:hover {
        color: #ffffff;
        background: var(--yt-primary);
        border-color: var(--yt-primary);
        box-shadow: 0 12px 26px rgba(14, 159, 110, 0.32);
      }

      .yt-card-slider {
        padding: 8px 4px 4px;
        margin: -8px -4px -4px;
      }

      .yt-card-slider .swiper-slide {
        height: auto;
        padding-bottom: 14px;
      }

      .yt-card-slider .swiper-pagination {
        margin-top: 18px;
      }

      .yt-card-slider .swiper-pagination-bullet-active {
        background: var(--yt-primary);
      }

      /* ── Banners ──────────────────────────────────── */
      .yt-banner-collection {
        padding: 0 0 84px;
        background: var(--yt-bg);
      }

      .yt-banner-card {
        position: relative;
        display: block;
        border-radius: var(--yt-radius);
        overflow: hidden;
        box-shadow: var(--yt-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .yt-banner-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--yt-shadow-lg);
      }

      .yt-banner-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        padding: 26px;
        background: linear-gradient(180deg, rgba(13, 27, 22, 0) 30%, rgba(13, 27, 22, 0.8));
      }

      .yt-banner-overlay h3 {
        margin: 0;
        color: #ffffff;
        font-size: clamp(18px, 2vw, 26px);
        font-weight: 900;
        line-height: 1.25;
      }

      .yt-banner-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 46px;
        height: 46px;
        color: var(--yt-primary-deep);
        background: #ffffff;
        border-radius: 50%;
        font-size: 18px;
        transition: all 0.25s ease;
      }

      .yt-banner-card:hover .yt-banner-arrow {
        color: #ffffff;
        background: var(--yt-primary);
        transform: translateX(4px);
      }

      /* ── CTA panel ────────────────────────────────── */
      .yt-cta-section {
        padding: 0 0 84px;
        background: var(--yt-bg);
      }

      .yt-cta-panel {
        position: relative;
        border-radius: 26px;
        overflow: hidden;
        background-color: var(--yt-primary-deep);
      }

      .yt-cta-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
          radial-gradient(circle at 90% 10%, rgba(245, 165, 36, 0.3), transparent 40%),
          linear-gradient(100deg, rgba(6, 67, 47, 0.95) 30%, rgba(13, 27, 22, 0.6));
      }

      .yt-cta-inner {
        position: relative;
        z-index: 1;
        max-width: 640px;
        padding: 68px 60px;
      }

      .yt-cta-inner h2 {
        margin: 0 0 18px;
        color: #ffffff;
        font-size: clamp(26px, 3vw, 40px);
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: -0.015em;
      }

      .yt-cta-inner p {
        margin: 0;
        color: #bcd8cb;
        font-size: 15.5px;
        line-height: 1.75;
      }

      .yt-cta-btn-wrap {
        margin-top: 30px;
      }

      /* ── Vendors ──────────────────────────────────── */
      .yt-vendor-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        padding: 30px 22px 26px;
        text-align: center;
        background: #ffffff;
        border-radius: var(--yt-radius);
        box-shadow: var(--yt-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .yt-vendor-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--yt-shadow-lg);
      }

      .yt-vendor-photo a {
        display: block;
        width: 104px;
        height: 104px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid var(--yt-primary-soft);
        box-shadow: 0 12px 28px rgba(14, 159, 110, 0.18);
      }

      .yt-vendor-photo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      .yt-vendor-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        gap: 7px;
        margin-top: 16px;
        width: 100%;
      }

      .yt-vendor-name {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
      }

      .yt-vendor-name a {
        color: var(--yt-ink);
        text-decoration: none;
        transition: color 0.2s ease;
      }

      .yt-vendor-name a:hover {
        color: var(--yt-primary-dark);
      }

      .yt-vendor-loc {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--yt-muted);
        font-size: 12.5px;
        font-weight: 600;
      }

      .yt-vendor-loc i {
        color: var(--yt-primary);
      }

      .yt-vendor-count {
        color: var(--yt-muted);
        font-size: 12.5px;
        font-weight: 700;
      }

      .yt-vendor-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: auto;
        padding: 10px 22px;
        color: var(--yt-primary-dark);
        background: var(--yt-primary-soft);
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
      }

      .yt-vendor-btn:hover {
        color: #ffffff;
        background: var(--yt-primary);
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.32);
      }

      /* ── Responsive ───────────────────────────────── */
      @media (max-width: 1199.98px) {
        .yt-hero {
          padding: 150px 0 110px;
        }

        .yt-hero-grid {
          grid-template-columns: 1fr;
          gap: 46px;
        }

        .yt-hero-copy {
          max-width: 100%;
        }

        .yt-hero-media {
          display: none;
        }

        .yt-hero-media-inline {
          display: block;
          width: 100%;
          margin: 0 0 24px;
        }

        .yt-hero-media-inline .yt-hero-slide-card {
          height: 360px;
        }

        .yt-hero-search {
          max-width: 100%;
        }

        .yt-why-grid {
          grid-template-columns: 1fr;
          gap: 40px;
        }

        .yt-steps-line {
          grid-template-columns: repeat(2, minmax(0, 1fr));
          gap: 34px 26px;
        }

        .yt-steps-line::before {
          display: none;
        }
      }

      @media (max-width: 991.98px) {
        .yt-reviews-grid {
          grid-template-columns: 1fr;
        }

        .yt-cta-inner {
          padding: 52px 36px;
        }
      }

      @media (max-width: 767.98px) {
        .yt-hero {
          padding: 96px 0 100px;
        }

        .yt-hero-eyebrow {
          margin-bottom: 18px;
          font-size: 10.5px;
          white-space: normal;
        }

        .yt-hero h1 {
          margin-bottom: 16px;
        }

        .yt-hero-media-inline .yt-hero-slide-card {
          height: 280px;
        }

        .yt-hero-copy > p {
          font-size: 14.5px;
        }

        .yt-hero-search {
          margin-top: 24px;
        }

        .yt-hero-search-row {
          grid-template-columns: 1fr;
          padding: 8px;
          border-radius: 22px;
        }

        .yt-hero-search-field {
          min-height: 48px;
          padding: 0 8px 0 12px;
        }

        .yt-hero-search-btn {
          width: 100%;
          min-height: 48px;
        }

        .yt-hero-popular {
          flex-wrap: nowrap;
          overflow-x: auto;
          padding-bottom: 4px;
          scrollbar-width: none;
        }

        .yt-hero-popular::-webkit-scrollbar {
          display: none;
        }

        .yt-hero-popular span,
        .yt-hero-popular a {
          flex: 0 0 auto;
        }

        .yt-hero-trust {
          display: grid;
          grid-template-columns: 1fr;
          gap: 11px;
          margin-top: 22px;
        }

        .yt-statbar-wrap {
          margin-top: -56px;
        }

        .yt-statbar {
          grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .yt-stat {
          padding: 20px 14px;
        }

        .yt-stat:nth-child(3)::before {
          display: none;
        }

        .yt-stat:nth-child(n + 3) {
          border-top: 1px solid rgba(255, 255, 255, 0.14);
        }

        .yt-stat strong {
          font-size: 22px;
        }

        .yt-stat span {
          font-size: 10px;
        }

        .yt-section {
          padding: 56px 0;
        }

        .yt-section-head {
          margin-bottom: 30px;
        }

        .yt-section-head h2 {
          overflow-wrap: break-word;
        }

        .yt-tabs {
          gap: 8px;
          margin-top: 22px;
        }

        .yt-tabs-arrow {
          flex: 0 0 36px;
          width: 36px;
          height: 36px;
          font-size: 15px;
        }

        .yt-tabs .nav-tabs .nav-link {
          padding: 9px 16px;
          font-size: 13px;
        }

        .yt-scard-body {
          padding: 14px;
        }

        .yt-steps-line {
          grid-template-columns: 1fr;
          gap: 28px;
        }

        .yt-step-node {
          width: 68px;
          height: 68px;
          font-size: 24px;
        }

        .yt-why-grid {
          gap: 30px;
        }

        .yt-why-intro h2 {
          margin-bottom: 20px;
        }

        .yt-why-row {
          flex-direction: column;
          gap: 16px;
          padding: 22px 20px;
        }

        .yt-why-row:hover {
          transform: translateY(-4px);
        }

        .yt-head-split {
          align-items: flex-start;
          flex-direction: column;
        }

        .yt-review-card {
          padding: 22px;
        }

        .yt-process-img {
          height: 120px;
        }

        .yt-process-img img {
          max-height: 120px;
        }

        .yt-banner-overlay {
          padding: 18px;
        }

        .yt-banner-arrow {
          flex: 0 0 40px;
          height: 40px;
          font-size: 16px;
        }

        .yt-banner-collection,
        .yt-cta-section {
          padding-bottom: 56px;
        }

        .yt-cta-inner {
          max-width: 100%;
          padding: 36px 22px;
        }

        .yt-cta-inner p {
          font-size: 14px;
        }

        .yt-reviews-section {
          padding: 56px 0;
        }

        .yt-vendor-card {
          padding: 24px 18px 22px;
        }

        .yt-btn-primary,
        .yt-btn-amber,
        .yt-btn-light {
          min-height: 46px;
          padding: 0 22px;
          font-size: 13.5px;
        }

        .yt-section-cta {
          margin-top: 30px;
        }
      }

      @media (max-width: 575.98px) {
        .yt-hero {
          padding: 88px 0 96px;
        }

        .yt-hero h1 {
          font-size: 31px;
          line-height: 1.12;
          overflow-wrap: anywhere;
        }

        .yt-hero-copy > p {
          font-size: 14px;
        }

        .yt-hero-eyebrow {
          padding: 8px 13px;
          font-size: 10px;
          letter-spacing: 0.04em;
        }

        .yt-hero-media-inline .yt-hero-slide-card {
          height: 220px;
          border-width: 4px;
        }

        .yt-hero-chip {
          padding: 7px 12px;
          font-size: 11px;
        }

        .yt-hero-trust {
          font-size: 12.5px;
        }

        .yt-statbar-wrap {
          margin-top: -50px;
          margin-bottom: 26px;
        }

        .yt-stat {
          padding: 16px 10px;
        }

        .yt-stat strong {
          font-size: 19px;
        }

        .yt-stat span {
          margin-top: 6px;
          font-size: 9.5px;
          letter-spacing: 0.06em;
        }

        .yt-section {
          padding: 48px 0;
        }

        .yt-section-head h2 {
          font-size: 25px;
        }

        .yt-eyebrow {
          font-size: 10.5px;
        }

        .yt-why-intro h2 {
          font-size: 25px;
        }

        .yt-step h4 {
          font-size: 15.5px;
        }

        .yt-step p,
        .yt-why-row p {
          font-size: 13px;
        }

        .yt-scard-title {
          font-size: 15px;
        }

        .yt-price-chip {
          padding: 6px 11px;
          font-size: 13px;
        }

        .yt-review-card blockquote {
          font-size: 14px;
        }

        .yt-cta-inner h2 {
          font-size: 24px;
        }

        .yt-banner-collection,
        .yt-cta-section {
          padding-bottom: 48px;
        }

        .yt-reviews-section {
          padding: 48px 0;
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
            speed: 750,
            autoplay: { delay: 4500, disableOnInteraction: false },
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

        document.querySelectorAll('.yt-tabs').forEach(function (tabs) {
          var scroller = tabs.querySelector('.yt-tabs-scroll');
          var prevBtn = tabs.querySelector('.yt-tabs-prev');
          var nextBtn = tabs.querySelector('.yt-tabs-next');

          if (!scroller || !prevBtn || !nextBtn) {
            return;
          }

          function updateArrows() {
            var overflow = scroller.scrollWidth - scroller.clientWidth;

            if (overflow <= 2) {
              prevBtn.classList.add('is-hidden');
              nextBtn.classList.add('is-hidden');
              return;
            }

            prevBtn.classList.remove('is-hidden');
            nextBtn.classList.remove('is-hidden');
            prevBtn.disabled = scroller.scrollLeft <= 2;
            nextBtn.disabled = scroller.scrollLeft >= overflow - 2;
          }

          prevBtn.addEventListener('click', function () {
            scroller.scrollBy({ left: -Math.max(scroller.clientWidth * 0.7, 180), behavior: 'smooth' });
          });

          nextBtn.addEventListener('click', function () {
            scroller.scrollBy({ left: Math.max(scroller.clientWidth * 0.7, 180), behavior: 'smooth' });
          });

          scroller.addEventListener('scroll', updateArrows, { passive: true });
          window.addEventListener('resize', updateArrows);
          window.addEventListener('load', updateArrows);
          updateArrows();
        });

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
