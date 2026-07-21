@php
  $version = $basicInfo->theme_version;
  $suggestedPlans = [
      [
          'name' => 'Starter',
          'badge' => 'For new pros',
          'price' => 'Free',
          'period' => 'lifetime',
          'description' => 'Start listing services and taking bookings without upfront cost.',
          'features' => ['Service profile setup', 'Customer booking requests', 'Basic appointment tools', 'Email support'],
          'cta' => route('vendor.signup'),
          'ctaLabel' => 'Start free',
          'recommended' => false,
      ],
      [
          'name' => 'Growth',
          'badge' => 'Recommended',
          'price' => '&pound;29',
          'period' => 'month',
          'description' => 'Best for active engineers who want more bookings and smoother admin.',
          'features' => ['Priority booking visibility', 'More services and images', 'Staff scheduling tools', 'Support tickets', 'Google Calendar'],
          'cta' => route('contact'),
          'ctaLabel' => 'Ask about Growth',
          'recommended' => true,
      ],
      [
          'name' => 'Pro Team',
          'badge' => 'For teams',
          'price' => '&pound;79',
          'period' => 'month',
          'description' => 'For multi-staff businesses, landlord work and repeat service volume.',
          'features' => ['Team/staff management', 'Landlord certificate workflow', 'Higher appointment limits', 'Zoom meetings', 'Priority support'],
          'cta' => route('contact'),
          'ctaLabel' => 'Contact team',
          'recommended' => false,
      ],
  ];
  $pricingFaqs = [
      [
          'question' => 'Which package should I choose?',
          'answer' => 'Starter is good for testing the platform. Growth is the recommended option for active engineers, and Pro Team is better for multi-staff businesses or landlord certificate work.',
      ],
      [
          'question' => 'Can I change package later?',
          'answer' => 'Yes. You can start small and upgrade when you need more services, images, appointments or staff access.',
      ],
      [
          'question' => 'Do customers pay through yestrades?',
          'answer' => 'Customers book services through the website. Your package controls the tools and limits available for managing those services as a pro.',
      ],
      [
          'question' => 'Do you support Gas Safety and EICR certificate work?',
          'answer' => 'Yes. The platform is designed for plumbing, gas, electrical, cleaning and landlord certificate services.',
      ],
      [
          'question' => 'Is support included?',
          'answer' => 'Support is included on the suggested plans. Higher-volume teams can contact us for priority support and custom setup help.',
      ],
  ];
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->pricing_page_title : __('Pricing') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_pricing }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_pricing }}
  @endif
@endsection

@push('scripts')
  <style>
    /* Pricing page brand accent — scoped to emerald to match the rest of the redesign */
    .eleven-pricing {
      --color-primary: #0e9f6e;
      --color-primary-rgb: 14, 159, 110;
    }

    .eleven-pricing {
      background: #fbfbfa;
    }

    .pricing-hero {
      padding: 76px 0 34px;
      text-align: center;
    }

    .pricing-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      margin-bottom: 16px;
      padding: 8px 12px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.08);
      border: 1px solid rgba(var(--color-primary-rgb), 0.15);
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      line-height: 1;
      text-transform: uppercase;
    }

    .pricing-hero h1 {
      max-width: 740px;
      margin: 0 auto 16px;
      color: #090b13;
      font-size: 44px;
      line-height: 1.14;
      font-weight: 900;
      letter-spacing: 0;
    }

    .pricing-hero p {
      max-width: 650px;
      margin: 0 auto;
      color: #6e717a;
      font-size: 16px;
      line-height: 1.75;
    }

    .suggested-plan-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 22px;
      margin: 28px 0 72px;
    }

    .suggested-plan-card {
      display: flex;
      flex-direction: column;
      min-height: 100%;
      padding: 30px;
      background: #ffffff;
      border: 1px solid #e7e8eb;
      border-radius: 8px;
      box-shadow: 0 18px 45px rgba(15, 18, 28, 0.05);
    }

    .suggested-plan-card.is-recommended {
      color: #ffffff;
      background: var(--color-primary);
      border-color: var(--color-primary);
      box-shadow: 0 24px 60px rgba(var(--color-primary-rgb), 0.24);
      transform: translateY(-10px);
    }

    .plan-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 22px;
    }

    .plan-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.08);
      border-radius: 8px;
      font-size: 19px;
    }

    .is-recommended .plan-icon {
      color: var(--color-primary);
      background: #ffffff;
    }

    .plan-badge {
      padding: 7px 10px;
      color: #6f737d;
      background: #f5f5f4;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      line-height: 1;
    }

    .is-recommended .plan-badge {
      color: var(--color-primary);
      background: #ffffff;
    }

    .suggested-plan-card h2 {
      margin: 0 0 10px;
      color: #10121a;
      font-size: 28px;
      line-height: 1.2;
      font-weight: 900;
      letter-spacing: 0;
    }

    .is-recommended h2,
    .is-recommended .plan-price,
    .is-recommended .plan-description,
    .is-recommended .plan-list li {
      color: #ffffff;
    }

    .plan-price {
      display: flex;
      align-items: baseline;
      gap: 6px;
      margin-bottom: 12px;
      color: #151720;
      font-size: 38px;
      font-weight: 900;
      line-height: 1;
    }

    .plan-price span {
      color: #777b84;
      font-size: 15px;
      font-weight: 600;
    }

    .is-recommended .plan-price span {
      color: rgba(255, 255, 255, 0.78);
    }

    .plan-description {
      min-height: 54px;
      margin-bottom: 20px;
      color: #6e717a;
      font-size: 14px;
      line-height: 1.7;
    }

    .plan-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin: 0 0 28px;
      padding: 0;
      list-style: none;
    }

    .plan-list li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: #3d414b;
      font-size: 14px;
      line-height: 1.45;
    }

    .plan-list i {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 22px;
      width: 22px;
      height: 22px;
      color: #10a66a;
      background: #eafff6;
      border-radius: 50%;
      font-size: 12px;
    }

    .is-recommended .plan-list i {
      color: var(--color-primary);
      background: #ffffff;
    }

    .plan-cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 48px;
      margin-top: auto;
      padding: 13px 18px;
      color: #ffffff;
      background: var(--color-primary);
      border: 1px solid var(--color-primary);
      border-radius: 8px;
      font-size: 14px;
      font-weight: 800;
      line-height: 1;
    }

    .is-recommended .plan-cta {
      color: var(--color-primary);
      background: #ffffff;
      border-color: #ffffff;
    }

    .actual-package-area,
    .pricing-faq-area {
      padding: 64px 0;
      border-top: 1px solid #ecebea;
    }

    .pricing-section-heading {
      max-width: 650px;
      margin: 0 auto 32px;
      text-align: center;
    }

    .pricing-section-heading h2 {
      margin: 0 0 12px;
      color: #10121a;
      font-size: 32px;
      line-height: 1.2;
      font-weight: 900;
      letter-spacing: 0;
    }

    .pricing-section-heading p {
      margin: 0;
      color: #6e717a;
      font-size: 15px;
      line-height: 1.7;
    }

    .eleven-pricing-tabs {
      justify-content: center;
      gap: 8px;
      margin: 0 0 28px;
      padding: 0;
      border: 0;
    }

    .eleven-pricing-tabs .nav-link {
      color: #282b34;
      background: #ffffff;
      border: 1px solid #e4e5e8;
      border-radius: 999px;
      font-weight: 800;
    }

    .eleven-pricing-tabs .nav-link.active {
      color: #ffffff;
      background: var(--color-primary);
      border-color: var(--color-primary);
    }

    .real-plan-card {
      height: 100%;
      padding: 26px;
      background: #ffffff;
      border: 1px solid #e7e8eb;
      border-radius: 8px;
      box-shadow: 0 14px 36px rgba(15, 18, 28, 0.04);
    }

    .real-plan-card.is-active {
      border-color: rgba(var(--color-primary-rgb), 0.45);
    }

    .real-plan-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 18px;
    }

    .real-plan-header i {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.08);
      border-radius: 8px;
      font-size: 18px;
    }

    .real-plan-header h3 {
      margin: 0;
      color: #10121a;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .real-plan-recommended {
      display: inline-flex;
      margin-top: 7px;
      padding: 5px 9px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.08);
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
    }

    .real-plan-price {
      display: flex;
      align-items: baseline;
      gap: 6px;
      margin-bottom: 18px;
      color: #10121a;
      font-size: 30px;
      font-weight: 900;
      line-height: 1;
    }

    .real-plan-price span {
      color: #7a7f89;
      font-size: 14px;
      font-weight: 600;
    }

    .real-plan-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      min-height: 220px;
      margin: 0 0 24px;
      padding: 0;
      list-style: none;
    }

    .real-plan-list li {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      color: #4f5561;
      font-size: 14px;
      line-height: 1.4;
    }

    .real-plan-list i {
      margin-top: 2px;
      color: #10a66a;
    }

    .real-plan-list .fa-times {
      color: #a0a5ad;
    }

    .pricing-faq-list {
      max-width: 860px;
      margin: 0 auto;
    }

    .pricing-faq-list .accordion-item {
      overflow: hidden;
      margin-bottom: 12px;
      background: #ffffff;
      border: 1px solid #e7e8eb;
      border-radius: 8px;
      box-shadow: 0 12px 30px rgba(15, 18, 28, 0.035);
    }

    .pricing-faq-list .accordion-button {
      color: #10121a;
      background: #ffffff;
      box-shadow: none;
      font-size: 16px;
      font-weight: 800;
      letter-spacing: 0;
    }

    .pricing-faq-list .accordion-button:not(.collapsed) {
      color: var(--color-primary);
    }

    .pricing-faq-list .accordion-body {
      color: #626773;
      font-size: 15px;
      line-height: 1.7;
    }

    @media (max-width: 991px) {
      .suggested-plan-grid {
        grid-template-columns: 1fr;
      }

      .suggested-plan-card.is-recommended {
        transform: none;
      }
    }

    @media (max-width: 767px) {
      .pricing-hero {
        padding-top: 54px;
      }

      .pricing-hero h1 {
        font-size: 34px;
      }

      .suggested-plan-card {
        padding: 24px;
      }
    }
  </style>
@endpush

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => !empty($pageHeading) ? $pageHeading->pricing_page_title : __('Pricing'),
  ])

  <main class="eleven-pricing">
    <section class="pricing-hero">
      <div class="container">
        <span class="pricing-eyebrow"><i class="fal fa-star"></i> {{ __('Suggested packages') }}</span>
        <h1>{{ __('Simple packages for service pros and growing teams.') }}</h1>
        <p>
          {{ __('Choose a practical starting point for managing services, appointments, staff and customer bookings on yestrades.') }}
        </p>
      </div>
    </section>

    <section class="container">
      <div class="suggested-plan-grid">
        @foreach ($suggestedPlans as $plan)
          <article class="suggested-plan-card {{ $plan['recommended'] ? 'is-recommended' : '' }}">
            <div class="plan-top">
              <span class="plan-icon"><i class="fal fa-gift"></i></span>
              <span class="plan-badge">{{ __($plan['badge']) }}</span>
            </div>
            <h2>{{ __($plan['name']) }}</h2>
            <div class="plan-price">
              {!! $plan['price'] !!}<span>/ {{ __($plan['period']) }}</span>
            </div>
            <p class="plan-description">{{ __($plan['description']) }}</p>
            <ul class="plan-list">
              @foreach ($plan['features'] as $feature)
                <li><i class="fal fa-check"></i><span>{{ __($feature) }}</span></li>
              @endforeach
            </ul>
            <a href="{{ $plan['cta'] }}" class="plan-cta">
              {{ __($plan['ctaLabel']) }} <i class="fal fa-long-arrow-right"></i>
            </a>
          </article>
        @endforeach
      </div>
    </section>

    @if (!empty($terms) && count($terms) > 0)
      <section class="actual-package-area">
        <div class="container">
          <div class="pricing-section-heading">
            <h2>{{ __('Available platform plans') }}</h2>
            <p>{{ __('These are the live packages connected to checkout. Pick one when you are ready to activate your pro account.') }}</p>
          </div>

          <ul class="nav nav-tabs eleven-pricing-tabs" role="tablist">
            @foreach ($terms as $term)
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                  data-bs-target="#pricing-term-{{ \Illuminate\Support\Str::slug($term) }}" type="button" role="tab">
                  {{ __($term) }}
                </button>
              </li>
            @endforeach
          </ul>

          <div class="tab-content">
            @foreach ($terms as $term)
              @php
                $packages = \App\Models\Package::where('status', '1')->where('term', strtolower($term))->get();
              @endphp
              <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pricing-term-{{ \Illuminate\Support\Str::slug($term) }}"
                role="tabpanel">
                <div class="row justify-content-center">
                  @forelse ($packages as $package)
                    <div class="col-md-6 col-lg-4 mb-4">
                      <article class="real-plan-card {{ $package->recommended == 1 ? 'is-active' : '' }}">
                        <div class="real-plan-header">
                          <i class="{{ $package->icon }}"></i>
                          <div>
                            <h3>{{ $package->title }}</h3>
                            @if ($package->recommended == 1)
                              <span class="real-plan-recommended">{{ __('Recommended') }}</span>
                            @endif
                          </div>
                        </div>

                        <div class="real-plan-price">
                          @if ($package->price == 0)
                            {{ __('Free') }}
                          @else
                            {{ format_price($package->price) }}
                          @endif
                          @php
                            $termText = str_replace('ly', '', $package->term);
                          @endphp
                          <span>/ {{ __($termText) }}</span>
                        </div>

                        <ul class="real-plan-list">
                          <li><i class="fal fa-check"></i><span>{{ __('Services') }} ({{ $package->number_of_service_add === 999999 ? __('Unlimited') : $package->number_of_service_add }})</span></li>
                          <li><i class="fal fa-check"></i><span>{{ __('Images/Service') }} ({{ $package->number_of_service_image === 999999 ? __('Unlimited') : $package->number_of_service_image }})</span></li>
                          <li><i class="fal fa-check"></i><span>{{ __('Appointments') }} ({{ $package->number_of_appointment === 999999 ? __('Unlimited') : $package->number_of_appointment }})</span></li>
                          <li><i class="fal fa-check"></i><span>{{ __('Staff') }} ({{ $package->staff_limit === 999999 ? __('Unlimited') : $package->staff_limit }})</span></li>
                          <li>
                            <i class="fal {{ $package->support_ticket_status == 1 ? 'fa-check' : 'fa-times' }}"></i>
                            <span>{{ __('Support Tickets') }}</span>
                          </li>
                          <li>
                            <i class="fal {{ $package->zoom_meeting_status == 1 ? 'fa-check' : 'fa-times' }}"></i>
                            <span>{{ __('Zoom Meeting') }}</span>
                          </li>
                          <li>
                            <i class="fal {{ $package->calendar_status == 1 ? 'fa-check' : 'fa-times' }}"></i>
                            <span>{{ __('Google Calendar') }}</span>
                          </li>
                          @if (!is_null($package->custom_features))
                            @foreach (explode("\n", $package->custom_features) as $feature)
                              @if (trim($feature) !== '')
                                <li><i class="fal fa-check"></i><span>{{ $feature }}</span></li>
                              @endif
                            @endforeach
                          @endif
                        </ul>

                        @if (Auth::guard('vendor')->check())
                          <a href="{{ route('vendor.plan.extend.checkout', ['package_id' => $package->id]) }}"
                            class="btn btn-lg btn-primary radius-sm w-100 no-animation" target="_self">
                            {{ __('Extend') }}
                          </a>
                        @else
                          <a href="{{ route('vendor.login', ['redirectPath' => 'buy_plan', 'buy_package' => $package->id]) }}"
                            class="btn btn-lg btn-primary radius-sm w-100 no-animation" target="_self">
                            {{ __('Purchase') }}
                          </a>
                        @endif
                      </article>
                    </div>
                  @empty
                    <div class="col-12">
                      <p class="text-center mb-0">{{ __('No package is available for this billing term yet.') }}</p>
                    </div>
                  @endforelse
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    <section class="pricing-faq-area">
      <div class="container">
        <div class="pricing-section-heading">
          <h2>{{ __('Pricing FAQs') }}</h2>
          <p>{{ __('Quick answers before you choose a package.') }}</p>
        </div>

        <div class="accordion pricing-faq-list" id="pricingFaqAccordion">
          @foreach ($pricingFaqs as $faq)
            <div class="accordion-item">
              <h3 class="accordion-header" id="pricingFaqHeading{{ $loop->iteration }}">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                  data-bs-toggle="collapse" data-bs-target="#pricingFaqCollapse{{ $loop->iteration }}"
                  aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                  aria-controls="pricingFaqCollapse{{ $loop->iteration }}">
                  {{ __($faq['question']) }}
                </button>
              </h3>
              <div id="pricingFaqCollapse{{ $loop->iteration }}"
                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                aria-labelledby="pricingFaqHeading{{ $loop->iteration }}" data-bs-parent="#pricingFaqAccordion">
                <div class="accordion-body">
                  {{ __($faq['answer']) }}
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  </main>
@endsection
