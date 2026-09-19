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
  @php
    $ytxCategoryImages = [
      'landlord' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1400&q=85',
      'electric' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1400&q=85',
      'plumb' => 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=1400&q=85',
      'boiler' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1400&q=85',
      'heat' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1400&q=85',
      'clean' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1400&q=85',
      'garden' => 'https://images.unsplash.com/photo-1558904541-efa843a96f01?auto=format&fit=crop&w=1400&q=85',
      'roof' => 'https://images.unsplash.com/photo-1632759145351-1d592919f522?auto=format&fit=crop&w=1400&q=85',
      'kitchen' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=85',
      'bathroom' => 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=1400&q=85',
      'handy' => 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1400&q=85',
    ];
    $ytxDefaultCategoryImage = 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1400&q=85';
    $ytxCategoryImage = function ($category) use ($ytxCategoryImages, $ytxDefaultCategoryImage) {
      if (!empty($category->image)) {
        return asset('assets/img/category/' . $category->image);
      }
      $name = strtolower($category->name ?? '');
      foreach ($ytxCategoryImages as $needle => $url) {
        if (str_contains($name, $needle)) {
          return $url;
        }
      }
      return $ytxDefaultCategoryImage;
    };
    $ytxPopularServices = $services->getCollection()->take(6);
    $ytxBoilerService = $services->getCollection()->first(fn ($s) => str_contains(strtolower($s->name ?? ''), 'boiler'))
      ?? $services->getCollection()->first();
  @endphp

  <div class="ytx-page">

    {{-- ══════════ Hero ══════════ --}}
    <section class="ytx-hero">
      <div class="ytx-wrap ytx-hero-grid">
        <div>
          <h1>{{ __('Family-run trades you can trust') }} — <em>{{ __('from quick repairs to complete renovations') }}</em></h1>
          <p class="ytx-lead">{{ __('A trusted family business providing professional repairs, heating, electrical, plumbing and renovation services with quality you can rely on.') }}</p>
          <div class="ytx-hero-actions">
            <a class="ytx-btn" href="{{ route('contact') }}">{{ __('Get a free quote') }} →</a>
            <a class="ytx-btn ytx-outline" href="{{ route('frontend.services') }}">{{ __('Book a service') }}</a>
          </div>
          <div class="ytx-credential-row" aria-label="{{ __('Trust credentials') }}">
            <span class="ytx-credential ytx-credential-logo">
              <img src="{{ asset('assets/img/trust/gas-safe-register.jpg') }}" alt="{{ __('Gas Safe Register') }}">
              <strong>{{ __('Registered') }}</strong>
            </span>
            <span class="ytx-credential ytx-niceic-badge" aria-label="{{ __('NICEIC approved') }}">
              <strong>{{ __('NICEIC') }}</strong>
              <small>{{ __('Approved') }}</small>
            </span>
            <span class="ytx-credential ytx-google-badge" aria-label="{{ __('Google 5 star rating') }}">
              <span class="ytx-google-mark" aria-hidden="true">G</span>
              <span><strong>{{ __('Google') }}</strong><small>&#9733;&#9733;&#9733;&#9733;&#9733;</small></span>
            </span>
            <span class="ytx-credential ytx-experience-badge">
              <i class="fal fa-award" aria-hidden="true"></i>
              <strong>{{ __('14 years experience') }}</strong>
            </span>
          </div>
        </div>
        <div class="ytx-hero-media">
          <img src="https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=85" alt="{{ __('Newly renovated kitchen') }}">
          <div class="ytx-rating">★★★★★<br>{{ __('Trusted local team') }}</div>
        </div>
      </div>
    </section>

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

    {{-- ══════════ What do you need help with ══════════ --}}
    <section class="ytx-section ytx-mint">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Start here') }}</div>
            <h2>{{ __('What do you need help with?') }}</h2>
          </div>
          <p>{{ __('Choose the type of work and we will guide you to the right service.') }}</p>
        </div>
        <div class="ytx-intent-grid">
          @forelse ($categories->take(4) as $category)
            <a class="ytx-intent" href="{{ route('frontend.services', ['category_id' => $category->id]) }}">
              <img src="{{ $ytxCategoryImage($category) }}" alt="{{ $category->name }}" loading="lazy">
              <div>
                <h3>{{ $category->name }} →</h3>
                <p>{{ $category->service_count }} {{ __('services available') }}</p>
              </div>
            </a>
          @empty
            <p>{{ __('Services coming soon.') }}</p>
          @endforelse
        </div>
        <form class="ytx-availability" action="{{ route('frontend.services') }}" method="GET">
          <input type="text" name="postcode_hint" aria-label="{{ __('Postcode') }}" placeholder="{{ __('Enter your postcode') }}">
          <button type="submit" class="ytx-btn">{{ __('Check availability') }}</button>
        </form>
      </div>
    </section>

    {{-- ══════════ Popular services ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Most requested') }}</div>
            <h2>{{ __('Popular services') }}</h2>
          </div>
          <a class="ytx-btn ytx-outline" href="{{ route('frontend.services') }}">{{ __('View all services') }}</a>
        </div>
        <div class="ytx-card-grid">
          @forelse ($ytxPopularServices as $service)
            @include('frontend.services.partials.ytx-card', ['service' => $service])
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

    {{-- ══════════ Trust strip ══════════ --}}
    <div class="ytx-trust-strip">
      <div class="ytx-wrap">
        @foreach ([['fa-user-check', 'Qualified local trades'], ['fa-file-invoice', 'Clear written quotes'], ['fa-shield-check', 'Workmanship guaranteed'], ['fa-phone-volume', 'Support from start to finish']] as $ytxTrust)
          <div class="ytx-trust-item">
            <span class="ytx-trust-icon"><i class="fal {{ $ytxTrust[0] }}"></i></span>
            {{ __($ytxTrust[1]) }}
          </div>
        @endforeach
      </div>
    </div>

    {{-- ══════════ Home improvements ══════════ --}}
    <section class="ytx-section ytx-soft">
      <div class="ytx-wrap ytx-split">
        <div class="ytx-split-media">
          <img src="https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=1400&q=85" alt="{{ __('Bathroom renovation') }}">
          <img src="https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=85" alt="{{ __('Kitchen renovation') }}">
        </div>
        <div>
          <div class="ytx-eyebrow">{{ __('Home improvements') }}</div>
          <h2>{{ __('Transform your home with one trusted team') }}</h2>
          <p class="ytx-lead">{{ __('From the first ideas to the finishing touches, your project is coordinated from start to finish.') }}</p>
          <div class="ytx-steps">
            @foreach ([['Free consultation', 'Tell us what you need and when.'], ['Detailed quote and plan', 'We explain the work, timescale and cost.'], ['Installation and completion', 'Our trusted team completes the work carefully.']] as $i => $ytxStep)
              <div class="ytx-step">
                <span class="ytx-step-num">{{ $i + 1 }}</span>
                <div><h3>{{ __($ytxStep[0]) }}</h3><p>{{ __($ytxStep[1]) }}</p></div>
              </div>
            @endforeach
          </div>
          <a class="ytx-btn" href="{{ route('contact') }}">{{ __('Request a home survey') }} →</a>
        </div>
      </div>
    </section>

    {{-- ══════════ Boilers & heating ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap ytx-split">
        <div>
          <div class="ytx-eyebrow">{{ __('Boilers & heating') }}</div>
          <h2>{{ __('A warmer, more efficient home starts here') }}</h2>
          <p class="ytx-lead">{{ __('Get practical advice, a clear written quote and professional installation by Gas Safe engineers.') }}</p>
          <ul class="ytx-check-list">
            <li>{{ __('Free boiler survey') }}</li>
            <li>{{ __('Clear installation quote') }}</li>
            <li>{{ __('Qualified local engineers') }}</li>
            <li>{{ __('Warranty registration') }}</li>
          </ul>
          @if ($ytxBoilerService)
            <a class="ytx-btn" href="{{ route('frontend.service.details', ['slug' => $ytxBoilerService->slug, 'id' => $ytxBoilerService->id]) }}">{{ __('Get my boiler quote') }} →</a>
          @else
            <a class="ytx-btn" href="{{ route('frontend.services') }}">{{ __('Get my boiler quote') }} →</a>
          @endif
        </div>
        <div class="ytx-hero-media">
          <img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1400&q=85" alt="{{ __('Boiler installation') }}">
        </div>
      </div>
    </section>

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

    {{-- ══════════ How Yes Trades works ══════════ --}}
    <section class="ytx-section ytx-mint">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Straightforward from day one') }}</div>
            <h2>{{ __('How Yes Trades works') }}</h2>
          </div>
        </div>
        @php
          $ytxWorkSteps = collect($processes ?? [])->filter(fn ($p) => !empty($p->title))->values();
        @endphp
        <div class="ytx-steps">
          @forelse ($ytxWorkSteps as $i => $process)
            <div class="ytx-step">
              <span class="ytx-step-num">{{ $i + 1 }}</span>
              <div><h3>{{ $process->title }}</h3><p>{{ $process->text ?: __('We keep you informed at every step.') }}</p></div>
            </div>
          @empty
            @foreach (['Tell us about the job', 'Receive your clear quote', 'Choose a convenient date'] as $i => $ytxTitle)
              <div class="ytx-step">
                <span class="ytx-step-num">{{ $i + 1 }}</span>
                <div><h3>{{ __($ytxTitle) }}</h3></div>
              </div>
            @endforeach
          @endforelse
        </div>
      </div>
    </section>

    {{-- ══════════ Reviews ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Local customer feedback') }}</div>
            <h2>{{ __('Trusted by homeowners and landlords') }}</h2>
          </div>
        </div>
        <div class="ytx-reviews">
          @forelse (($testimonials ?? collect())->take(3) as $t)
            <article class="ytx-review">
              <div class="ytx-stars">{{ str_repeat('★', max(0, min(5, (int) $t->rating))) }}{{ str_repeat('☆', max(0, 5 - (int) $t->rating)) }}</div>
              <p>"{{ $t->comment }}"</p>
              <strong>{{ $t->name }}</strong>
              @if ($t->occupation)
                <br><small>{{ $t->occupation }}</small>
              @endif
            </article>
          @empty
            @foreach ([['Sarah M.', '"Excellent communication and a brilliant finish. The team respected our home throughout."'], ['David R.', '"Everything was explained clearly and the work was completed exactly as agreed."'], ['Karen T.', '"Reliable, tidy and easy to deal with from the first call to completion."']] as $ytxReview)
              <article class="ytx-review">
                <div class="ytx-stars">★★★★★</div>
                <p>{{ $ytxReview[1] }}</p>
                <strong>{{ $ytxReview[0] }}</strong>
              </article>
            @endforeach
          @endforelse
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
