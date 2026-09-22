@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')
@php
  $service = $details->content->first();
  $title = !empty($service) ? truncateString($service->name, 40) : 'Service Details';
@endphp
@section('pageHeading')
  @if (!empty($title))
    {{ $title ? $title : __('Service Details') }}
  @endif
@endsection

@section('metaTitle')
  @if ($service){{ $service->name }} {{ __('in Livingston, Edinburgh & West Lothian') }} | {{ $websiteInfo->website_title ?? config('app.name') }}@endif
@endsection

@section('metaKeywords')
  @if ($service)
    {{ $service->meta_keyword }}
  @endif
@endsection

@section('metaDescription')
  @if ($service)
    {{ $service->meta_description }}
  @endif
@endsection

@push('head')
  @if ($service)
    {{-- Service + Breadcrumb structured data — lets Google show this service
         directly as a rich result and understand the page hierarchy. --}}
    <script type="application/ld+json">
      {!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service->name,
        'description' => strip_tags($service->meta_description ?: $service->description ?: ''),
        'serviceType' => $service->name,
        'url' => url()->current(),
        'provider' => array_filter([
          '@type' => 'LocalBusiness',
          'name' => $websiteInfo->website_title ?? config('app.name'),
          'telephone' => $websiteInfo->contact_number ?? null,
        ]),
      ]), JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script type="application/ld+json">
      {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
          ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
          ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => route('frontend.services')],
          ['@type' => 'ListItem', 'position' => 3, 'name' => $service->name, 'item' => url()->current()],
        ],
      ], JSON_UNESCAPED_SLASHES) !!}
    </script>
  @endif
@endpush

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
    $ytxDefaultImage = 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1400&q=85';
    $ytxHeroImage = !empty($details->service_image)
      ? asset('assets/img/services/' . $details->service_image)
      : $ytxDefaultImage;
    $ytxServiceName = strtolower($service->name ?? '');
    $ytxCategoryName = strtolower(optional($service->category)->name ?? '');
    $ytxIntentImage = $ytxDefaultImage;
    foreach ($ytxCategoryImages as $needle => $url) {
      if (str_contains($ytxServiceName, $needle) || str_contains($ytxCategoryName, $needle)) {
        $ytxIntentImage = $url;
        break;
      }
    }
    $ytxIsBookable = !$details->isQuoteType();
    $ytxActionLabel = $ytxIsBookable ? __('Book this service') : __('Get my free quote');
    $ytxActionUrl = route('frontend.service_quote.questions', $details->id);
    $ytxFeatureList = !empty($service->features) ? array_filter(array_map('trim', explode("\n", $service->features))) : [];
  @endphp

  <div class="ytx-page">
    <div class="ytx-wrap ytx-breadcrumbs">
      <a href="{{ route('index') }}">{{ __('Home') }}</a> &gt;
      <a href="{{ route('frontend.services') }}">{{ __('Services') }}</a> &gt;
      {{ $service->name ?? __('Service Details') }}
    </div>

    {{-- ══════════ Page hero ══════════ --}}
    <section class="ytx-page-hero">
      <div class="ytx-wrap ytx-page-hero-grid">
        <div>
          @if (!empty($service->category))
            <div class="ytx-eyebrow">{{ $service->category->name }}</div>
          @endif
          <h1>{{ $service->name }}, {{ __('delivered by a') }} <em>{{ __('trusted local team') }}</em></h1>
          @if (!empty($service->description))
            <p class="ytx-lead">{{ Str::limit(strip_tags($service->description), 170) }}</p>
          @endif
          <div class="ytx-hero-actions">
            @if ($ytxIsBookable)
              <a class="ytx-btn" href="#ytx-book">{{ $ytxActionLabel }} →</a>
            @else
              <a class="ytx-btn" href="{{ $ytxActionUrl }}">{{ $ytxActionLabel }} →</a>
            @endif
            @if (!empty($basicInfo->contact_number))
              <a class="ytx-btn ytx-outline" href="tel:{{ preg_replace('/\s+/', '', $basicInfo->contact_number) }}">{{ __('Speak to our team') }}</a>
            @endif
          </div>
          <div class="ytx-trust-row">
            <span>{{ __('Qualified trades') }}</span>
            <span>{{ __('Clear written quote') }}</span>
            <span>{{ __('Fully insured') }}</span>
          </div>
        </div>
        <div class="ytx-hero-media">
          <img src="{{ $ytxHeroImage }}" alt="{{ $service->name }}">
          <div class="ytx-rating">★★★★★<br>{{ __('Local, trusted support') }}</div>
        </div>
      </div>
    </section>

    {{-- ══════════ Book / quote panel ══════════ --}}
    @if ($ytxIsBookable)
      @include('frontend.services.partials.booking-panel', ['details' => $details, 'service' => $service])
    @else
      <section class="ytx-finder-section" id="ytx-book">
        <div class="ytx-wrap">
          <div class="ytx-finder">
            <div class="ytx-finder-head">
              <div>
                <div class="ytx-eyebrow">{{ __('Ready when you are') }}</div>
                <h2>{{ __('Request your free quote') }}</h2>
                <p>{{ __('Tell us a little about the job and our team will confirm the best next step.') }}</p>
              </div>
            </div>
            <a href="{{ route('frontend.service_quote.questions', $details->id) }}" class="ytx-btn ytx-full">
              <i class="fal fa-file-invoice"></i> {{ __('Get a Quote') }} →
            </a>
          </div>
        </div>
      </section>
    @endif
    {{-- ══════════ Anchor bar ══════════ --}}
    <nav class="ytx-anchorbar">
      <div class="ytx-wrap">
        <a href="#ytx-overview">{{ __('Overview') }}</a>
        <a href="#ytx-included">{{ __("What's included") }}</a>
        <a href="#ytx-process">{{ __('Our process') }}</a>
        <a href="#ytx-faq">{{ __('FAQs') }}</a>
      </div>
    </nav>

    {{-- ══════════ Overview / benefits ══════════ --}}
    <section id="ytx-overview" class="ytx-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Complete local service') }}</div>
            <h2>{{ __('Everything you need, handled properly') }}</h2>
            <p>{{ __('We make it easy to understand what happens next and who is responsible at every stage.') }}</p>
          </div>
        </div>
        <div class="ytx-benefit-grid">
          @foreach ([['fa-comments', 'Friendly first conversation', 'Tell us what you need without technical jargon.'], ['fa-user-check', 'Qualified local professional', 'Your job is matched with the right skills.'], ['fa-file-invoice', 'Clear scope and quotation', 'Know what is included before work starts.'], ['fa-home', 'Careful completion', 'Respect for your property and a tidy finish.']] as $ytxBenefit)
            <div class="ytx-benefit">
              <span class="ytx-trust-icon"><i class="fal {{ $ytxBenefit[0] }}"></i></span>
              <h3>{{ __($ytxBenefit[1]) }}</h3>
              <p>{{ __($ytxBenefit[2]) }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    {{-- ══════════ What's included ══════════ --}}
    <section id="ytx-included" class="ytx-section ytx-soft">
      <div class="ytx-wrap ytx-split">
        <div>
          <div class="ytx-eyebrow">{{ __("What's included") }}</div>
          <h2>{{ __('A clear service from first contact to completion') }}</h2>
          <ul class="ytx-check-list">
            @forelse ($ytxFeatureList as $feature)
              <li>{{ $feature }}</li>
            @empty
              <li>{{ __('Initial requirements review') }}</li>
              <li>{{ __('Professional assessment') }}</li>
              <li>{{ __('Clear written quotation') }}</li>
              <li>{{ __('Materials discussed in advance') }}</li>
              <li>{{ __('Qualified workmanship') }}</li>
              <li>{{ __('Progress communication') }}</li>
              <li>{{ __('Final safety checks') }}</li>
              <li>{{ __('Clean and tidy handover') }}</li>
            @endforelse
          </ul>
        </div>
        <div class="ytx-hero-media">
          <img src="{{ $ytxIntentImage }}" alt="{{ __('Professional') }} {{ strtolower($service->name ?? '') }} {{ __('service') }}">
        </div>
      </div>
    </section>

    {{-- ══════════ Process ══════════ --}}
    <section id="ytx-process" class="ytx-section ytx-mint">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('How it works') }}</div>
            <h2>{{ __('A straightforward process') }}</h2>
          </div>
        </div>
        <div class="ytx-steps">
          @php
            $ytxProcessSteps = $ytxIsBookable
              ? ['Choose your service', 'Confirm the details', 'Select a convenient time', 'Completion and confirmation']
              : ['Tell us about your project', 'Survey or assessment', 'Receive your written quote', 'Work and handover'];
          @endphp
          @foreach ($ytxProcessSteps as $i => $ytxStep)
            <div class="ytx-step">
              <span class="ytx-step-num">{{ $i + 1 }}</span>
              <div><h3>{{ __($ytxStep) }}</h3></div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

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

    {{-- ══════════ Reviews ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Customer feedback') }}</div>
            <h2>{{ __('What customers say') }}</h2>
          </div>
        </div>
        <div class="ytx-reviews">
          @forelse ($reviews->take(3) as $review)
            <article class="ytx-review">
              <div class="ytx-stars">{{ str_repeat('★', max(0, min(5, (int) $review->rating))) }}{{ str_repeat('☆', max(0, 5 - (int) $review->rating)) }}</div>
              <p>"{{ $review->comment }}"</p>
              <strong>{{ optional($review->user)->name ?? __('Verified customer') }}</strong>
            </article>
          @empty
            <article class="ytx-review">
              <div class="ytx-stars">★★★★★</div>
              <p>{{ __('Be the first to review this service after your booking is complete.') }}</p>
            </article>
          @endforelse
        </div>
      </div>
    </section>

    {{-- ══════════ Local SEO copy + related services ══════════ --}}
    @if (!empty($service->category))
      <section class="ytx-section">
        <article class="ytx-wrap ytx-seo-copy">
          <div class="ytx-eyebrow">{{ __('Local') }} {{ strtolower($service->category->name) }} {{ __('specialists') }}</div>
          <h2>{{ $service->name }}</h2>
          <p>{{ __('Yes Trades provides') }} {{ strtolower($service->name) }} {{ __('for homeowners, landlords and property managers.') }} {{ __('We explain the next step clearly and match the work with an appropriately skilled local professional.') }}</p>
          @if ($related_services->isNotEmpty())
            <h3>{{ __('Related services') }}</h3>
            <div class="ytx-related-links">
              @foreach ($related_services->take(6) as $related)
                <a href="{{ route('frontend.service.details', ['slug' => $related->slug, 'id' => $related->id]) }}">{{ $related->name }}</a>
              @endforeach
            </div>
          @endif
        </article>
      </section>
    @endif

    {{-- ══════════ FAQ ══════════ --}}
    <section id="ytx-faq" class="ytx-section ytx-soft">
      <div class="ytx-wrap">
        <div class="ytx-section-title">
          <div>
            <div class="ytx-eyebrow">{{ __('Common questions') }}</div>
            <h2>{{ $service->name }} {{ __('FAQs') }}</h2>
          </div>
        </div>
        @php
          $ytxFaqs = [
            [__('How does the :service process start?', ['service' => strtolower($service->name)]), __('Tell us what you need online or by phone. We will confirm whether the next step is a booking, assessment or home survey.')],
            [__('Will I receive a written quotation?'), __('Yes. Where a quotation is required, the scope, assumptions and price will be set out clearly before work starts.')],
            [__('Are your trades qualified and insured?'), __('Jobs are assigned to appropriately qualified professionals and relevant registration details can be confirmed for your work.')],
            [__('How soon can the work be completed?'), __('Timescales depend on the service, materials and availability. We will provide a realistic date before you commit.')],
          ];
        @endphp
        @foreach ($ytxFaqs as $ytxFaq)
          <div class="ytx-faq">
            <button type="button">{{ $ytxFaq[0] }}<span>+</span></button>
            <div class="ytx-faq-body">{{ $ytxFaq[1] }}</div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ══════════ Final CTA / contact ══════════ --}}
    <section class="ytx-section">
      <div class="ytx-wrap ytx-quote-panel">
        <div class="ytx-contact-grid">
          <div>
            <div class="ytx-eyebrow">{{ __('Get started') }}</div>
            <h2>{{ $ytxActionLabel }}</h2>
            <p>{{ __('Share a few details and our team will contact you to confirm the best next step.') }}</p>
            @if ($ytxIsBookable)
              <a class="ytx-btn" href="#ytx-book">{{ $ytxActionLabel }} →</a>
            @else
              <a class="ytx-btn" href="{{ $ytxActionUrl }}">{{ $ytxActionLabel }} →</a>
            @endif
          </div>
          <aside class="ytx-contact-card">
            <h3>{{ __('Prefer to speak to someone?') }}</h3>
            @if (!empty($basicInfo->contact_number))
              <p class="ytx-lead"><a href="tel:{{ preg_replace('/\s+/', '', $basicInfo->contact_number) }}">{{ $basicInfo->contact_number }}</a></p>
            @endif
            @if (!empty($basicInfo->address))
              <p>{{ __('Serving') }} {{ $basicInfo->address }} {{ __('and surrounding areas.') }}</p>
            @endif
          </aside>
        </div>
      </div>
    </section>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.ytx-faq > button').forEach(function (btn) {
          btn.addEventListener('click', function () {
            btn.parentElement.classList.toggle('ytx-open');
          });
        });
      });
    </script>
  @endpush
@endsection
@section('script')
  <script src="{{ asset('assets/frontend/js/vendors/leaflet.js') }}"></script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="{{ $authorizeUrl }}"></script>
  <script>
    "use strict";
    let stripe_key = "{{ $stripe_key }}";
    let authorize_login_key = "{{ $authorize_login_id }}";
    let authorize_public_key = "{{ $authorize_public_key }}";
    var complete = "{{ Session::get('complete') }}";
    var bookingInfo = {!! json_encode(Session::get('paymentInfo')) !!}; //after success a payment show there info
    // Address to be geocoded
    var latitude = "{{ $details->latitude }}";
    var longitude = "{{ $details->longitude }}";
  </script>

  <script src="{{ asset('assets/frontend/js/appointment.js') }}?v={{ filemtime(public_path('assets/frontend/js/appointment.js')) }}"></script>
  <script src="{{ asset('assets/frontend/js/init-map.js') }}"></script>
  <script>
    @if (old('gateway') == 'stripe')
      $('#stripe-element').removeClass('d-none');
    @endif
  </script>
@endsection
