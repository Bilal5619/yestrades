@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ __('Home') }}
@endsection

@section('metaTitle')
  {{ __('Plumbers, Electricians & Gas Safe Engineers in Livingston & Edinburgh') }} | {{ $websiteInfo->website_title ?? config('app.name') }}
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
  @php
    // ── Business facts shown on the home page ──────────────────────────
    // Update these with the real figures from Yes Trades. Everything else on
    // the page is driven by the admin panel / database.
    $ytxFounded = 2012;
    $ytxYears = max(1, (int) date('Y') - $ytxFounded);
    // Google Business Profile (YES TRADES, Livingston). Review badges link here.
    $ytxGoogleUrl = 'https://www.google.co.uk/maps/place/YES+TRADES/@55.9015079,-3.545075,17z/data=!4m6!3m5!1s0x4887d93d19ebc8b9:0x4328e70bc2a3ea10!8m2!3d55.9015079!4d-3.545075!16s%2Fg%2F1q6kk8x89';
    // Live data from Google (Admin > Basic Settings > Plugins > Google Reviews); falls back to placeholders.
    $ytxGoogle = \App\Services\GoogleReviewsService::get();
    $ytxGoogleRating = !empty($ytxGoogle['rating']) ? number_format($ytxGoogle['rating'], 1) : '5.0';
    $ytxGoogleCount = $ytxGoogle['count'] ?? null;
    if (!empty($ytxGoogle['url'])) { $ytxGoogleUrl = $ytxGoogle['url']; }
    $ytxStats = [
      ['value' => $ytxYears . '+', 'label' => __('Years in business')],
      ['value' => '5,000+', 'label' => __('Jobs completed')],           // TODO real figure
      ['value' => $ytxGoogleRating, 'label' => __('Google rating')],
      ['value' => __('Gas Safe & NICEIC'), 'label' => __('Registered engineers')],
    ];
    $ytxAreas = ['West Lothian', 'Livingston', 'Edinburgh', 'Glasgow', 'Stirling', 'Dundee', 'Bathgate', 'Broxburn', 'Linlithgow'];
    $ytxGuaranteeText = __('Every job is covered by our workmanship guarantee. If something is not right, we come back and put it right.');

    // Real photos: drop files into public/assets/img/yt/ (see README there).
    $ytxHeroPhoto = file_exists(public_path('assets/img/yt/hero.jpg'))
      ? asset('assets/img/yt/hero.jpg')
      : 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1400&q=85';
    $ytxFamilyPhoto = file_exists(public_path('assets/img/yt/family.jpg'))
      ? asset('assets/img/yt/family.jpg')
      : null;
    $ytxProjectPhotos = collect(glob(public_path('assets/img/yt/projects/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG}'), GLOB_BRACE) ?: [])
      ->map(fn ($p) => asset('assets/img/yt/projects/' . basename($p)))
      ->take(6);

    $ytxPhone = $basicInfo->contact_number ?? null;
    $ytxPhoneHref = $ytxPhone ? 'tel:' . preg_replace('/\s+/', '', $ytxPhone) : null;
    $ytxWhatsapp = !empty($basicInfo->whatsapp_number) ? preg_replace('/\D/', '', $basicInfo->whatsapp_number) : null;

    $ytxPopularServices = $services->getCollection()->take(4);
    $ytxLandlordCategory = $categories->first(fn ($c) => str_contains(strtolower($c->name), 'landlord'));
    $ytxEmergencyCategory = $categories->first(fn ($c) => str_contains(strtolower($c->name), 'emergency'));
    // Service card image: the admin upload when the file exists, otherwise a
    // sensible fallback picked from the service name (keeps cards intact on a
    // dev copy that doesn't have the live uploads).
    $ytxServiceImage = function ($service) {
      if (!empty($service->service_image) && file_exists(public_path('assets/img/services/' . $service->service_image))) {
        return asset('assets/img/services/' . $service->service_image);
      }
      $n = strtolower($service->name ?? '');
      $map = [
        'gas' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1200&q=80',
        'boiler' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1200&q=80',
        'eicr' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=80',
        'electric' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=80',
        'plumb' => 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=1200&q=80',
        'bathroom' => asset('assets/img/services/bathroom-renovation.jpg'),
        'kitchen' => asset('assets/img/services/kitchen-renovation.jpg'),
        'clean' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1200&q=80',
      ];
      foreach ($map as $k => $url) { if (str_contains($n, $k)) return $url; }
      return 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1200&q=80';
    };
    $ytxServiceBullets = function ($service) {
      $features = !empty($service->features) ? array_filter(array_map('trim', explode("\n", $service->features))) : [];
      if (count($features) >= 2) {
        return array_slice($features, 0, 3);
      }
      return [__('Qualified, registered engineer'), __('Clear price before we start'), __('Tidy, respectful work in your home')];
    };
  @endphp

  <div class="ytx-page">

    {{-- ══════════ Hero: headline + service slider ══════════ --}}
    @php
      // Slides come from Admin > Home Page > Slides: services the admin picked
      // (service image, price, booking link) plus any uploaded promo images.
      // If nothing is set up yet, the newest services fill the slider.
      $ytxSlideServiceIds = collect($heroSliderImages ?? [])->pluck('service_id')->filter()->map(fn ($i) => (int) $i)->all();
      $ytxSlideServices = collect($heroSlideServices ?? []);
      if ($ytxSlideServices->isEmpty() && $ytxSlideServiceIds) {
        $ytxSlideServices = $services->getCollection()->whereIn('id', $ytxSlideServiceIds)->keyBy('id');
      }
      $ytxServiceSlide = function ($s) use ($ytxServiceImage) {
        return [
          'image' => $ytxServiceImage($s),
          'eyebrow' => $s->categoryName ?? __('Our services'),
          'title' => $s->name,
          'subtitle' => !empty($s->short_description) ? truncateString($s->short_description, 80) : null,
          'price' => $s->isQuoteType() ? __('Free quote') : ($s->hasPricePlaceholder() ? $s->price_placeholder : __('From') . ' ' . symbolPrice($s->price)),
          'button' => $s->isQuoteType() ? __('Get a quote') : __('Book online'),
          'url' => route('frontend.service.details', ['slug' => $s->slug, 'id' => $s->id]),
          'plain' => false,
        ];
      };
      $ytxSlides = collect($heroSliderImages ?? [])->map(function ($s) use ($ytxSlideServices, $ytxServiceSlide) {
        if (!empty($s->service_id)) {
          $svc = $ytxSlideServices->get((int) $s->service_id);
          if (!$svc) { return null; }
          $slide = $ytxServiceSlide($svc);
          if (!empty($s->title)) { $slide['title'] = $s->title; }
          if (!empty($s->subtitle)) { $slide['subtitle'] = $s->subtitle; }
          if (!empty($s->button_text)) { $slide['button'] = $s->button_text; }
          if (!empty($s->url)) { $slide['url'] = $s->url; }
          return $slide;
        }
        if (empty($s->image) || !file_exists(public_path('assets/img/hero/' . $s->image))) { return null; }
        return [
          'image' => asset('assets/img/hero/' . $s->image),
          'eyebrow' => null,
          'title' => $s->title,
          'subtitle' => $s->subtitle,
          'price' => null,
          'button' => $s->button_text ?: ($s->title ? __('Find out more') : null),
          'url' => $s->url ?: route('frontend.services'),
          'plain' => empty($s->title),
        ];
      })->filter()->values();

      // Nothing set up in admin yet → advertise the newest services instead.
      if ($ytxSlides->count() < 3) {
        $ytxSlides = $ytxSlides->concat(
          $services->getCollection()
            ->reject(fn ($s) => in_array((int) $s->id, $ytxSlideServiceIds, true))
            ->take(6 - $ytxSlides->count())
            ->map($ytxServiceSlide)
        )->values();
      }
    @endphp
    <section class="ytx-hero ytx-hero-dark ytx-hero-slider">
      <div class="ytx-wrap">
        <div class="ytx-hero-top">
          <div>
            <div class="ytx-eyebrow ytx-eyebrow-light">{{ __('Family-run · Est.') }} {{ $ytxFounded }} · {{ __('Gas Safe & NICEIC registered') }}</div>
            <h1>{{ __('Family-run trades you can trust') }} — <em>{{ __('from quick repairs to complete renovations') }}</em></h1>
            <p class="ytx-lead">{{ __('A trusted family business providing professional repairs, heating, electrical, plumbing and renovation services with quality you can rely on.') }}</p>
          </div>

        </div>

        @if ($ytxSlides->isNotEmpty())
          <div class="ytx-hslider" data-count="{{ min(3, $ytxSlides->count()) }}" data-ytx-slider data-autoplay="4500" aria-roledescription="carousel" aria-label="{{ __('Popular services and offers') }}">
            <button type="button" class="ytx-hslider-arrow ytx-hslider-prev" data-ytx-prev aria-label="{{ __('Previous') }}"><i class="fal fa-chevron-left"></i></button>
            <div class="ytx-hslider-viewport" data-ytx-viewport>
              <div class="ytx-hslider-track" data-ytx-track>
                @foreach ($ytxSlides as $i => $slide)
                  <a class="ytx-hslide {{ $slide['plain'] ? 'ytx-hslide-plain' : '' }}" href="{{ $slide['url'] }}" data-ytx-slide aria-label="{{ $slide['title'] ?? __('Offer') }}">
                    <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] ?? '' }}" @if ($i > 2) loading="lazy" @else fetchpriority="high" @endif>
                    @unless ($slide['plain'])
                      <div class="ytx-hslide-body">
                        @if ($slide['eyebrow'])<span class="ytx-hslide-eyebrow">{{ $slide['eyebrow'] }}</span>@endif
                        <h3>{{ $slide['title'] }}</h3>
                        @if ($slide['subtitle'])<p>{{ $slide['subtitle'] }}</p>@endif
                        <div class="ytx-hslide-foot">
                          @if ($slide['price'])<span class="ytx-price-pill">{{ $slide['price'] }}</span>@endif
                          @if ($slide['button'])<span class="ytx-hslide-cta">{{ $slide['button'] }} →</span>@endif
                        </div>
                      </div>
                    @endunless
                  </a>
                @endforeach
              </div>
            </div>
            <button type="button" class="ytx-hslider-arrow ytx-hslider-next" data-ytx-next aria-label="{{ __('Next') }}"><i class="fal fa-chevron-right"></i></button>
            <div class="ytx-hslider-dots" data-ytx-dots role="tablist"></div>
          </div>
        @endif

        <div class="ytx-credential-row ytx-hero-credentials" aria-label="{{ __('Trust credentials') }}">
          <span class="ytx-credential ytx-credential-logo">
            <img src="{{ asset('assets/img/trust/gas-safe-register.jpg') }}" alt="{{ __('Gas Safe Register') }}">
            <strong>{{ __('Registered') }}</strong>
          </span>
          <span class="ytx-credential ytx-niceic-badge" aria-label="{{ __('NICEIC approved') }}">
            <strong>{{ __('NICEIC') }}</strong>
            <small>{{ __('Approved') }}</small>
          </span>
          <a class="ytx-credential ytx-google-badge" href="{{ $ytxGoogleUrl }}" target="_blank" rel="noopener" aria-label="{{ __('Google reviews') }}">
            <span class="ytx-google-mark" aria-hidden="true">G</span>
            <span><strong>{{ $ytxGoogleRating }} {{ __('Google') }}</strong><small>&#9733;&#9733;&#9733;&#9733;&#9733;{{ $ytxGoogleCount ? ' · ' . $ytxGoogleCount . '+ ' . __('reviews') : '' }}</small></span>
          </a>
          <span class="ytx-credential ytx-experience-badge">
            <i class="fal fa-award" aria-hidden="true"></i>
            <strong>{{ $ytxYears }} {{ __('years experience') }}</strong>
          </span>
        </div>
      </div>
    </section>

    {{-- USP bar (Currys "service bar" pattern) — scrolls one-at-a-time on phones --}}
    <div class="ytx-usp-bar" data-ytx-usp>
      <div class="ytx-wrap ytx-usp-track">
        <a class="ytx-usp" href="{{ route('frontend.services') }}"><i class="fal fa-badge-check"></i><span>{{ __('Fixed prices — see the cost before you book') }}</span></a>
        <a class="ytx-usp" href="{{ route('frontend.services') }}"><i class="fal fa-calendar-check"></i><span>{{ __('Book online in minutes, pick a date that suits you') }}</span></a>
        <a class="ytx-usp" href="{{ route('about_us') }}"><i class="fal fa-shield-check"></i><span>{{ __('Workmanship guaranteed on every job') }}</span></a>
        <a class="ytx-usp" href="{{ $ytxPhoneHref ?? route('contact') }}"><i class="fal fa-phone"></i><span>{{ __('Family-run local team — talk to a real person') }}</span></a>
      </div>
    </div>
    @if (count($after_hero) > 0)
      @foreach ($after_hero as $cusHero)
        @if (isset($homecusSec[$cusHero->id]) && $homecusSec[$cusHero->id] == 1)
          @php
            $cusHeroContent = App\Models\CustomSectionContent::where('custom_section_id', $cusHero->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusHeroContent])
        @endif
      @endforeach
    @endif

    {{-- ══════════ How can we help? ══════════ --}}
    <section class="ytx-section ytx-help-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title ytx-section-title-center">
          <div>
            <div class="ytx-eyebrow">{{ __('How can we help?') }}</div>
            <h2>{{ __('Tell us what you need and we will take it from there') }}</h2>
          </div>
        </div>
        <div class="ytx-help-grid">
          <a class="ytx-help-card" href="{{ route('frontend.services') }}">
            <span class="ytx-help-icon"><i class="fal fa-calendar-check"></i></span>
            <h3>{{ __('Book a service') }}</h3>
            <p>{{ __('Choose a date and time online — no account needed.') }}</p>
            <span class="ytx-link">{{ __('Book now') }} →</span>
          </a>
          <a class="ytx-help-card" href="{{ route('contact') }}">
            <span class="ytx-help-icon"><i class="fal fa-file-invoice"></i></span>
            <h3>{{ __('Get a free quote') }}</h3>
            <p>{{ __('Renovations, boilers and bigger jobs — we will visit and quote.') }}</p>
            <span class="ytx-link">{{ __('Request a quote') }} →</span>
          </a>
          <a class="ytx-help-card" href="{{ $ytxLandlordCategory ? route('frontend.services', ['category_id' => $ytxLandlordCategory->id]) : route('frontend.landlords') }}">
            <span class="ytx-help-icon"><i class="fal fa-file-certificate"></i></span>
            <h3>{{ __('Landlord certificates') }}</h3>
            <p>{{ __('Gas safety (CP12), EICR, EPC and PAT testing for rental properties.') }}</p>
            <span class="ytx-link">{{ __('See certificates') }} →</span>
          </a>
          <a class="ytx-help-card ytx-help-card-urgent" href="{{ $ytxPhoneHref ?? ($ytxEmergencyCategory ? route('frontend.services', ['category_id' => $ytxEmergencyCategory->id]) : route('contact')) }}">
            <span class="ytx-help-icon"><i class="fal fa-bolt"></i></span>
            <h3>{{ __('Emergency call-out') }}</h3>
            <p>{{ __('Leaks, no heating, electrical faults — call us and we will get to you fast.') }}</p>
            <span class="ytx-link">{{ $ytxPhone ? __('Call') . ' ' . $ytxPhone : __('Contact us') }} →</span>
          </a>
        </div>
      </div>
    </section>

    @if (count($after_category) > 0)
      @foreach ($after_category as $cusCategory)
        @if (isset($homecusSec[$cusCategory->id]) && $homecusSec[$cusCategory->id] == 1)
          @php
            $cusCategoryContent = App\Models\CustomSectionContent::where('custom_section_id', $cusCategory->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusCategoryContent])
        @endif
      @endforeach
    @endif

    {{-- ══════════ Family business / about ══════════ --}}
    <section class="ytx-section ytx-about">
      <div class="ytx-wrap">
        <div class="ytx-about-grid">
          <div class="ytx-about-visual">
            @if ($ytxFamilyPhoto)
              <div class="ytx-about-photo">
                <img src="{{ $ytxFamilyPhoto }}" alt="{{ __('The Yes Trades family team') }}">
                <div class="ytx-family-badge"><strong>{{ $ytxYears }}+</strong><span>{{ __('years serving local homes') }}</span></div>
              </div>
            @else
              <div class="ytx-about-card">
                <div class="ytx-about-est">
                  <span>{{ __('Established') }}</span>
                  <strong>{{ $ytxFounded }}</strong>
                </div>
                <blockquote class="ytx-about-quote">
                  “{{ __('We are the people who answer the phone, turn up at your door and stand behind the work. That is how our family has always done it.') }}”
                  <footer>— {{ __('The Yes Trades family') }}</footer>
                </blockquote>
                <div class="ytx-about-stats">
                  <div><strong>{{ $ytxYears }}+</strong><span>{{ __('years') }}</span></div>
                  <div><strong>{{ __('Local') }}</strong><span>{{ __('Livingston based') }}</span></div>
                  <div><strong>100%</strong><span>{{ __('own engineers') }}</span></div>
                </div>
              </div>
            @endif
          </div>

          <div class="ytx-about-copy">
            <div class="ytx-eyebrow">{{ __('About Yes Trades') }}</div>
            <h2>{{ __('A family business, built on doing the job properly') }}</h2>
            <p class="ytx-lead">{{ __('Yes Trades has been run by the same family since') }} {{ $ytxFounded }}. {{ __('No call centre, no lead-selling website — just a local team that knows your area and takes pride in every job.') }}</p>

            <div class="ytx-about-features">
              <div class="ytx-about-feature">
                <span class="ytx-about-feature-icon"><i class="fal fa-home-heart"></i></span>
                <div><strong>{{ __('Family-run') }}</strong><span>{{ __('Owner-led, with our name on every job.') }}</span></div>
              </div>
              <div class="ytx-about-feature">
                <span class="ytx-about-feature-icon"><i class="fal fa-user-hard-hat"></i></span>
                <div><strong>{{ __('Our own engineers') }}</strong><span>{{ __('Not subcontracted out to strangers.') }}</span></div>
              </div>
              <div class="ytx-about-feature">
                <span class="ytx-about-feature-icon"><i class="fal fa-badge-check"></i></span>
                <div><strong>{{ __('Registered & insured') }}</strong><span>{{ __('Gas Safe, NICEIC and fully insured.') }}</span></div>
              </div>
              <div class="ytx-about-feature">
                <span class="ytx-about-feature-icon"><i class="fal fa-shield-check"></i></span>
                <div><strong>{{ __('Guaranteed work') }}</strong><span>{{ __('Written quote first, workmanship guarantee after.') }}</span></div>
              </div>
            </div>

            <div class="ytx-hero-actions">
              <a class="ytx-btn" href="{{ route('about_us') }}">{{ __('Our story') }} →</a>
              @if ($ytxPhoneHref)
                <a class="ytx-btn ytx-outline" href="{{ $ytxPhoneHref }}"><i class="fal fa-phone"></i> {{ __('Speak to the team') }}</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>
    {{-- ══════════ Services (large cards) ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('What we do') }}</div>
            <h2>{{ __('Our services') }}</h2>
          </div>
          <a class="ytx-btn ytx-outline" href="{{ route('frontend.services') }}">{{ __('View all services') }}</a>
        </div>
        <div class="ytx-service-large-grid">
          @forelse ($ytxPopularServices as $service)
            <a class="ytx-service-large" href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}">
              <div class="ytx-service-large-media">
                <img src="{{ $ytxServiceImage($service) }}" alt="{{ $service->name }}" loading="lazy">
              </div>
              <div class="ytx-service-large-body">
                <div class="ytx-service-large-top">
                  <h3>{{ $service->name }}</h3>
                  @if ($service->isQuoteType())
                    <span class="ytx-price-pill ytx-price-pill-quote">{{ __('Free quote') }}</span>
                  @elseif ($service->hasPricePlaceholder())
                    <span class="ytx-price-pill">{{ $service->price_placeholder }}</span>
                  @else
                    <span class="ytx-price-pill">{{ __('From') }} {{ symbolPrice($service->price) }}</span>
                  @endif
                </div>
                <ul>
                  @foreach ($ytxServiceBullets($service) as $bullet)
                    <li>{{ $bullet }}</li>
                  @endforeach
                </ul>
                <span class="ytx-link">{{ __('Learn more') }} →</span>
              </div>
            </a>
          @empty
            <p>{{ __('Services coming soon.') }}</p>
          @endforelse
        </div>
      </div>
    </section>

    @if (count($after_featured_service) > 0)
      @foreach ($after_featured_service as $cusFeature)
        @if (isset($homecusSec[$cusFeature->id]) && $homecusSec[$cusFeature->id] == 1)
          @php
            $cusFeatureContent = App\Models\CustomSectionContent::where('custom_section_id', $cusFeature->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusFeatureContent])
        @endif
      @endforeach
    @endif

    {{-- ══════════ Stats ══════════ --}}
    <section class="ytx-stats">
      <div class="ytx-wrap ytx-stats-grid">
        @foreach ($ytxStats as $stat)
          <div class="ytx-stat">
            <strong>{{ $stat['value'] }}</strong>
            <span>{{ $stat['label'] }}</span>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ══════════ Accreditations ══════════ --}}
    <div class="ytx-accreditations">
      <div class="ytx-wrap">
        <span class="ytx-accreditations-label">{{ __('Registered & accredited') }}</span>
        <div class="ytx-accreditations-marquee" aria-label="{{ __('Accreditations') }}">
        <div class="ytx-accreditations-track">
          <div class="ytx-accreditations-row">
          <span class="ytx-accreditation"><img src="{{ asset('assets/img/trust/gas-safe-register.jpg') }}" alt="{{ __('Gas Safe Register') }}"></span>
          <span class="ytx-accreditation ytx-accreditation-text ytx-niceic-badge"><strong>NICEIC</strong></span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-shield-check"></i> {{ __('Fully insured') }}</span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-id-card"></i> {{ __('DBS checked') }}</span>
          <span class="ytx-accreditation ytx-accreditation-text ytx-google-badge"><span class="ytx-google-mark" aria-hidden="true">G</span> {{ __('Google reviews') }}</span>
          </div>
          <div class="ytx-accreditations-row" aria-hidden="true">
          <span class="ytx-accreditation"><img src="{{ asset('assets/img/trust/gas-safe-register.jpg') }}" alt="{{ __('Gas Safe Register') }}"></span>
          <span class="ytx-accreditation ytx-accreditation-text ytx-niceic-badge"><strong>NICEIC</strong></span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-shield-check"></i> {{ __('Fully insured') }}</span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-id-card"></i> {{ __('DBS checked') }}</span>
          <span class="ytx-accreditation ytx-accreditation-text ytx-google-badge"><span class="ytx-google-mark" aria-hidden="true">G</span> {{ __('Google reviews') }}</span>
          </div>
        </div>
        </div>
      </div>
    </div>

    @if (count($after_work_process) > 0)
      @foreach ($after_work_process as $work_process)
        @if (isset($homecusSec[$work_process->id]) && $homecusSec[$work_process->id] == 1)
          @php
            $cusWorkProcessContent = App\Models\CustomSectionContent::where('custom_section_id', $work_process->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusWorkProcessContent])
        @endif
      @endforeach
    @endif

    {{-- ══════════ Reviews ══════════ --}}
    <section class="ytx-section ytx-soft">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('What our customers say') }}</div>
            <h2>{{ __('Trusted by homeowners and landlords') }}</h2>
          </div>
        </div>
        @php
          $ytxReviewItems = collect($ytxGoogle['reviews'] ?? [])->take(6)->map(fn ($r) => [
            'name' => $r['name'], 'meta' => $r['time'], 'rating' => (int) $r['rating'], 'text' => $r['text'], 'photo' => $r['photo'] ?? null,
          ])->values();
          if ($ytxReviewItems->isEmpty()) {
            $ytxReviewItems = ($testimonials ?? collect())->take(6)->map(fn ($t) => [
              'name' => $t->name, 'meta' => $t->occupation, 'rating' => (int) $t->rating, 'text' => $t->comment,
            ])->values();
          }
          if ($ytxReviewItems->isEmpty()) {
            $ytxReviewItems = collect([
              ['name' => 'Sarah M.', 'meta' => 'Edinburgh · Boiler service', 'rating' => 5, 'text' => 'Excellent communication and a brilliant finish. The team respected our home throughout.'],
              ['name' => 'David R.', 'meta' => 'Livingston · EICR certificate', 'rating' => 5, 'text' => 'Everything was explained clearly and the work was completed exactly as agreed.'],
              ['name' => 'Karen T.', 'meta' => 'Bathgate · Gas safety check', 'rating' => 5, 'text' => 'Reliable, tidy and easy to deal with from the first call to completion.'],
            ]);
          }
        @endphp
        <div class="ytx-reviews-layout">
          <aside class="ytx-review-summary">
            <div class="ytx-review-summary-score">{{ $ytxGoogleRating }}</div>
            <div class="ytx-stars ytx-stars-lg">{{ str_repeat('★', (int) round((float) $ytxGoogleRating)) }}{{ str_repeat('☆', max(0, 5 - (int) round((float) $ytxGoogleRating))) }}</div>
            <p><span class="ytx-google-mark" aria-hidden="true">G</span> {{ $ytxGoogleCount ? $ytxGoogleCount . '+ ' . __('Google reviews') : __('Rated on Google') }}</p>
            <ul class="ytx-review-points">
              <li>{{ __('Family-run since') }} {{ $ytxFounded }}</li>
              <li>{{ __('Gas Safe & NICEIC registered') }}</li>
              <li>{{ __('Workmanship guaranteed') }}</li>
            </ul>
            <a class="ytx-btn ytx-full" href="{{ $ytxGoogleUrl }}" target="_blank" rel="noopener">{{ __('Read all reviews on Google') }} →</a>
          </aside>
          <div class="ytx-reviews ytx-reviews-cards">
            @foreach ($ytxReviewItems as $r)
              <article class="ytx-review">
                <div class="ytx-review-head">
                  @if (!empty($r['photo']))
                    <img class="ytx-review-avatar ytx-review-avatar-img" src="{{ $r['photo'] }}" alt="" loading="lazy" referrerpolicy="no-referrer">
                  @else
                    <span class="ytx-review-avatar">{{ strtoupper(mb_substr(trim($r['name']), 0, 1)) }}</span>
                  @endif
                  <div>
                    <strong>{{ $r['name'] }}</strong>
                    @if (!empty($r['meta']))<small>{{ $r['meta'] }}</small>@endif
                  </div>
                  <span class="ytx-google-mark ytx-review-g" aria-hidden="true">G</span>
                </div>
                <div class="ytx-stars">{{ str_repeat('★', max(0, min(5, $r['rating']))) }}{{ str_repeat('☆', max(0, 5 - $r['rating'])) }}</div>
                <p>“{{ $r['text'] }}”</p>
              </article>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    {{-- ══════════ How it works ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title ytx-section-title-center">
          <div>
            <div class="ytx-eyebrow">{{ __('Simple from start to finish') }}</div>
            <h2>{{ __('How it works') }}</h2>
          </div>
        </div>
        <div class="ytx-how-grid">
          @foreach ([['fa-comment-alt-lines', 'Tell us the job', 'Book online, call, or request a callback — takes about a minute.'], ['fa-file-invoice-dollar', 'Clear price up front', 'Fixed prices online where shown, or a written quote before any work starts.'], ['fa-tools', 'We do the work', 'A registered engineer from our own team turns up on time and gets it done properly.'], ['fa-certificate', 'Certificate & guarantee', 'You get any certificates you need, and our workmanship guarantee.']] as $i => $how)
            <div class="ytx-how-step">
              <span class="ytx-how-num">{{ $i + 1 }}</span>
              <span class="ytx-how-icon"><i class="fal {{ $how[0] }}"></i></span>
              <h3>{{ __($how[1]) }}</h3>
              <p>{{ __($how[2]) }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    {{-- ══════════ Recent work (only shown once real job photos exist) ══════════ --}}
    @if ($ytxProjectPhotos->isNotEmpty())
      <section class="ytx-section ytx-soft">
        <div class="ytx-wrap">
          <div class="ytx-section-title">
            <div>
              <div class="ytx-eyebrow">{{ __('Recent work') }}</div>
              <h2>{{ __('Jobs we have completed recently') }}</h2>
            </div>
          </div>
          <div class="ytx-project-grid">
            @foreach ($ytxProjectPhotos as $photo)
              <figure class="ytx-project">
                <img src="{{ $photo }}" alt="{{ __('Completed job by Yes Trades') }}" loading="lazy">
              </figure>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    {{-- ══════════ Areas we cover: map + SEO description ══════════ --}}
    <section class="ytx-section ytx-soft ytx-areas-section">
      <div class="ytx-wrap ytx-areas-grid">
        <div class="ytx-areas-map">
          <iframe
            title="{{ __('Map of the areas Yes Trades covers in Central Scotland') }}"
            src="https://www.google.com/maps?q=YES%20TRADES%2C%20128%20Waverley%20Crescent%2C%20Livingston%20EH54%208JR&z=9&output=embed"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
          <a class="ytx-areas-map-link" href="{{ $ytxGoogleUrl }}" target="_blank" rel="noopener"><i class="fal fa-map-marker-alt"></i> {{ __('128 Waverley Crescent, Livingston EH54 8JR') }}</a>
        </div>
        <div class="ytx-areas-copy">
          <div class="ytx-eyebrow">{{ __('Areas we cover') }}</div>
          <h2>{{ __('Local trades across West Lothian, Edinburgh & Central Scotland') }}</h2>
          <p>{{ __('Yes Trades is a family-run plumbing, heating, electrical and home improvement company based in Livingston, West Lothian. Our Gas Safe registered engineers and NICEIC approved electricians carry out boiler servicing and repairs, gas safety certificates (CP12), EICR electrical inspections, plumbing repairs, socket and lighting work, and full bathroom and kitchen renovations for homeowners, landlords and letting agents.') }}</p>
          <p>{{ __('We work across Livingston, Bathgate, Broxburn, Linlithgow and the whole of West Lothian, as well as Edinburgh, Glasgow, Stirling, Dundee and surrounding areas — with fixed online prices, dated certificates emailed after every inspection, and a workmanship guarantee on every job.') }}</p>
          <div class="ytx-filterbar ytx-areas-chips">
            @foreach ($ytxAreas as $area)
              <span class="ytx-chip">{{ $area }}</span>
            @endforeach
          </div>
          <p class="ytx-marketplace-note">{{ __('Not sure if we cover you? Give us a call — we are happy to check.') }}</p>
        </div>
      </div>
    </section>
    @if (count($after_banner) > 0)
      @foreach ($after_banner as $cusBanner)
        @if (isset($homecusSec[$cusBanner->id]) && $homecusSec[$cusBanner->id] == 1)
          @php
            $cusBannerContent = App\Models\CustomSectionContent::where('custom_section_id', $cusBanner->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusBannerContent])
        @endif
      @endforeach
    @endif

    {{-- ══════════ FAQ ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap ytx-faq-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Common questions') }}</div>
            <h2>{{ __('Things people ask us') }}</h2>
          </div>
        </div>
        @php
          $ytxFaqs = [
            [__('Are you Gas Safe and NICEIC registered?'), __('Yes. All gas work is carried out by Gas Safe registered engineers and electrical work by NICEIC approved electricians. Registration details can be provided on request.')],
            [__('Do you charge a call-out fee?'), __('For services booked online the price you see is the price you pay. For larger jobs we visit, assess and give you a written quote before any work starts.')],
            [__('How quickly can you come out?'), __('For urgent problems — leaks, no heating, electrical faults — call us and we will get to you as fast as we can, usually the same day. Routine work is booked at a time that suits you.')],
            [__('Which areas do you cover?'), __('We are based in Livingston and cover Edinburgh, West Lothian and surrounding areas across Central Scotland. Not sure? Give us a call.')],
            [__('Are you insured, and is the work guaranteed?'), __('Yes — we are fully insured and every job is covered by our workmanship guarantee. If something is not right, we come back and put it right.')],
          ];
        @endphp
        @foreach ($ytxFaqs as $faq)
          <div class="ytx-faq">
            <button type="button">{{ $faq[0] }}<span>+</span></button>
            <div class="ytx-faq-body">{{ $faq[1] }}</div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ══════════ Free quote form ══════════ --}}
    <section class="ytx-section ytx-soft" id="free-quote">
      <div class="ytx-wrap ytx-quote-panel">
        <div class="ytx-contact-grid">
          <div>
            <div class="ytx-eyebrow">{{ __('Free quote') }}</div>
            <h2>{{ __('Tell us about the job') }}</h2>
            <p>{{ __('Send a few details and one of the family will get back to you — usually the same day.') }}</p>
            @if (Session::has('success'))
              <div class="ytx-form-success">{{ __(Session::get('success')) }}</div>
            @endif
            @if (Session::has('error'))
              <div class="ytx-form-error">{{ __(Session::get('error')) }}</div>
            @endif
            <form class="ytx-quote-form" action="{{ route('contact.send_mail') }}" method="post">
              @csrf
              <input type="hidden" name="subject" value="{{ __('Free quote request from website') }}">
              <input type="text" name="name" required placeholder="{{ __('Your name') }}" value="{{ old('name') }}">
              <input type="email" name="email" required placeholder="{{ __('Email address') }}" value="{{ old('email') }}">
              <textarea class="ytx-full" name="message" rows="4" required placeholder="{{ __('What do you need done, and your postcode') }}">{{ old('message') }}</textarea>
              @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                <div class="ytx-full">
                  {!! NoCaptcha::renderJs() !!}
                  {!! NoCaptcha::display() !!}
                </div>
              @endif
              @if ($errors->any())
                <div class="ytx-full ytx-form-error">{{ $errors->first() }}</div>
              @endif
              <button type="submit" class="ytx-btn ytx-full">{{ __('Send my request') }} →</button>
            </form>
          </div>
          <aside class="ytx-contact-card">
            <h3>{{ __('Prefer to talk?') }}</h3>
            @if ($ytxPhone)
              <p class="ytx-lead"><a href="{{ $ytxPhoneHref }}">{{ $ytxPhone }}</a></p>
            @endif
            <p>{{ __('Monday – Friday: 8am – 6pm') }}</p>
            @if ($ytxWhatsapp)
              <a class="ytx-btn ytx-outline" href="https://wa.me/{{ $ytxWhatsapp }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> {{ __('WhatsApp us') }}</a>
            @endif
            @if (!empty($basicInfo->address))
              <hr>
              <p>{{ __('Serving') }} {{ $basicInfo->address }} {{ __('and surrounding areas.') }}</p>
            @endif
          </aside>
        </div>
      </div>
    </section>

    @if (count($after_latest_service) > 0)
      @foreach ($after_latest_service as $cusLatestServie)
        @if (isset($homecusSec[$cusLatestServie->id]) && $homecusSec[$cusLatestServie->id] == 1)
          @php
            $cusLatestContent = App\Models\CustomSectionContent::where('custom_section_id', $cusLatestServie->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusLatestContent])
        @endif
      @endforeach
    @endif

    @if (count($after_call_to_action) > 0)
      @foreach ($after_call_to_action as $cusAction)
        @if (isset($homecusSec[$cusAction->id]) && $homecusSec[$cusAction->id] == 1)
          @php
            $cusActionContent = App\Models\CustomSectionContent::where('custom_section_id', $cusAction->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusActionContent])
        @endif
      @endforeach
    @endif

    @if (count($after_vendor) > 0)
      @foreach ($after_vendor as $cusVendor)
        @if (isset($homecusSec[$cusVendor->id]) && $homecusSec[$cusVendor->id] == 1)
          @php
            $cusVendorContent = App\Models\CustomSectionContent::where('custom_section_id', $cusVendor->id)
              ->where('language_id', $currentLanguageInfo->id)
              ->first();
          @endphp
          @include('frontend.home.custom-section', ['data' => $cusVendorContent])
        @endif
      @endforeach
    @endif

  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.ytx-faq > button').forEach(function (btn) {
          btn.addEventListener('click', function () { btn.parentElement.classList.toggle('ytx-open'); });
        });
        /* Hero promo slider: arrows, dots, autoplay (pauses on hover/focus/drag), swipe, keyboard. */
        document.querySelectorAll('[data-ytx-slider]').forEach(function (root) {
          var track = root.querySelector('[data-ytx-track]');
          var slides = Array.prototype.slice.call(root.querySelectorAll('[data-ytx-slide]'));
          var prev = root.querySelector('[data-ytx-prev]');
          var next = root.querySelector('[data-ytx-next]');
          var dots = root.querySelector('[data-ytx-dots]');
          var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
          var delay = parseInt(root.getAttribute('data-autoplay') || '0', 10);
          var index = 0, timer = null, paused = false;
          if (!track || !slides.length) return;

          function perView() { return Math.max(1, parseInt(getComputedStyle(root).getPropertyValue('--ytx-per-view'), 10) || 1); }
          function pages() { return Math.max(1, slides.length - perView() + 1); }
          function step() { var gap = parseFloat(getComputedStyle(track).gap) || 0; return slides[0].getBoundingClientRect().width + gap; }
          function render() {
            index = Math.max(0, Math.min(index, pages() - 1));
            root.classList.toggle("is-static", slides.length <= perView());
            track.style.transform = 'translateX(' + (-index * step()) + 'px)';
            if (dots) {
              if (dots.children.length !== pages()) {
                dots.innerHTML = '';
                for (var i = 0; i < pages(); i++) {
                  var b = document.createElement('button');
                  b.type = 'button'; b.setAttribute('role', 'tab'); b.setAttribute('aria-label', 'Slide ' + (i + 1));
                  b.addEventListener('click', (function (n) { return function () { go(n, true); }; })(i));
                  dots.appendChild(b);
                }
              }
              Array.prototype.forEach.call(dots.children, function (d, i) { d.classList.toggle('is-active', i === index); });
            }
            slides.forEach(function (s, i) { s.setAttribute('aria-hidden', (i < index || i >= index + perView()) ? 'true' : 'false'); });
          }
          function go(n, user) {
            var p = pages();
            index = ((n % p) + p) % p;
            render();
            if (user) restart();
          }
          function restart() {
            if (timer) clearInterval(timer);
            timer = null;
            if (delay > 0 && !reduce && slides.length > perView()) {
              timer = setInterval(function () { if (!paused && !document.hidden) go(index + 1); }, delay);
            }
          }
          if (prev) prev.addEventListener('click', function () { go(index - 1, true); });
          if (next) next.addEventListener('click', function () { go(index + 1, true); });
          root.addEventListener('mouseenter', function () { paused = true; });
          root.addEventListener('mouseleave', function () { paused = false; });
          root.addEventListener('focusin', function () { paused = true; });
          root.addEventListener('focusout', function () { paused = false; });
          root.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') { go(index - 1, true); e.preventDefault(); }
            if (e.key === 'ArrowRight') { go(index + 1, true); e.preventDefault(); }
          });

          // Pointer drag / touch swipe
          var startX = null, dragging = false, moved = 0;
          track.addEventListener('pointerdown', function (e) {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            startX = e.clientX; moved = 0; dragging = true; paused = true;
            root.classList.add('is-dragging');
            try { track.setPointerCapture(e.pointerId); } catch (err) {}
          });
          track.addEventListener('pointermove', function (e) {
            if (!dragging) return;
            moved = e.clientX - startX;
            track.style.transform = 'translateX(' + (-index * step() + moved) + 'px)';
          });
          function endDrag(e) {
            if (!dragging) return;
            dragging = false; paused = false;
            root.classList.remove('is-dragging');
            if (Math.abs(moved) > 40) { go(index + (moved < 0 ? 1 : -1), true); } else { render(); }
            if (Math.abs(moved) > 8) { root.dataset.swiped = '1'; setTimeout(function () { delete root.dataset.swiped; }, 50); }
          }
          track.addEventListener('pointerup', endDrag);
          track.addEventListener('pointercancel', endDrag);
          track.addEventListener('click', function (e) { if (root.dataset.swiped) { e.preventDefault(); } }, true);

          window.addEventListener('resize', function () { render(); restart(); });
          render();
          restart();
        });
        // Carry the chosen service into the callback message (contact form only stores name/email/message).
        var heroForm = document.querySelector('.ytx-hero-form');
        if (heroForm) {
          heroForm.addEventListener('submit', function () {
            var svc = heroForm.querySelector('select[name="service_hint"]');
            var msg = heroForm.querySelector('textarea[name="message"]');
            if (svc && svc.value && msg && msg.value.indexOf('Service:') !== 0) {
              msg.value = 'Service: ' + svc.value + '\n' + msg.value;
            }
          });
        }
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
