@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->faq_page_title : __('FAQ') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_faq }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_faq }}
  @endif
@endsection

@section('content')
  <main class="fq-page">

    <section class="fq-hero">
      <div class="fq-hero-dots"></div>
      <div class="container">
        <nav aria-label="breadcrumb" class="fq-breadcrumb">
          <a href="{{ route('index') }}">{{ __('Home') }}</a>
          <i class="fal fa-angle-right"></i>
          <span>{{ !empty($pageHeading) ? $pageHeading->faq_page_title : __('FAQ') }}</span>
        </nav>
        <span class="fq-eyebrow"><span class="fq-eyebrow-dot"></span>{{ __('Help Center') }}</span>
        <h1>{{ __('Frequently Asked') }} <span>{{ __('Questions') }}</span></h1>
        <p>{{ __('Find quick answers about booking services, payments and managing your service details online.') }}</p>
      </div>
    </section>

    @php
      $defaultFaqs = [
        [
          'question' => 'How do I book a service?',
          'answer' => 'Choose the service you need, add it to your service cart, confirm the required booking details and complete checkout online.',
        ],
        [
          'question' => 'Can I book more than one service at the same time?',
          'answer' => 'Yes. You can add multiple services to your cart and complete the booking details from one checkout flow.',
        ],
        [
          'question' => 'What information do I need before checkout?',
          'answer' => 'You may be asked for site address, contact details, preferred date or time, and any service-specific answers needed to prepare the booking.',
        ],
        [
          'question' => 'How are prices shown?',
          'answer' => 'Service prices are shown before checkout where available. If a service needs additional details, the website will guide you through the required steps.',
        ],
        [
          'question' => 'Is online payment secure?',
          'answer' => 'Payments are processed through secure payment providers. Card details are handled by the payment provider and are not stored directly on our website.',
        ],
        [
          'question' => 'Can landlords use ' . ($websiteInfo->website_title ?? config('app.name')) . '?',
          'answer' => 'Yes. Landlords can browse certificate and property service options, provide site details and complete booking requests online.',
        ],
        [
          'question' => 'What happens after I complete a booking?',
          'answer' => 'After checkout, your booking request details are saved and the relevant service information is used to arrange the next steps.',
        ],
        [
          'question' => 'Who can I contact if I need help?',
          'answer' => 'You can use the contact page or WhatsApp option in the header for help with service selection, booking details or general questions.',
        ],
      ];
    @endphp

    <section class="fq-section">
      <div class="container">
        <div class="accordion fq-accordion" id="faqAccordion">
          <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
              @if (count($faqs) == 0)
                @foreach ($defaultFaqs as $faq)
                  <div class="fq-item">
                    <h6 class="accordion-header" id="defaultFaqHeading_{{ $loop->iteration }}">
                      <button class="accordion-button fq-btn {{ $loop->first ? '' : 'collapsed' }}" type="button"
                        data-bs-toggle="collapse" data-bs-target="#defaultFaqCollapse_{{ $loop->iteration }}"
                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="defaultFaqCollapse_{{ $loop->iteration }}">
                        <span class="fq-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        {{ __($faq['question']) }}
                      </button>
                    </h6>
                    <div id="defaultFaqCollapse_{{ $loop->iteration }}"
                      class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                      aria-labelledby="defaultFaqHeading_{{ $loop->iteration }}" data-bs-parent="#faqAccordion">
                      <div class="accordion-body fq-body">
                        <p>{{ __($faq['answer']) }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                @foreach ($faqs as $faq)
                  <div class="fq-item">
                    <h6 class="accordion-header" id="headingOne_{{ $faq->id }}">
                      <button class="accordion-button fq-btn collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne_{{ $faq->id }}" aria-expanded="true"
                        aria-controls="collapseOne_{{ $faq->id }}">
                        <span class="fq-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        {{ $faq->question }}
                      </button>
                    </h6>
                    <div id="collapseOne_{{ $faq->id }}"
                      class="accordion-collapse collapse {{ $loop->iteration == 1 ? 'show' : '' }}"
                      aria-labelledby="headingOne_{{ $faq->id }}" data-bs-parent="#faqAccordion">
                      <div class="accordion-body fq-body">
                        <p>{{ $faq->answer }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              @endif

              @if (!empty(showAd(3)))
                <div class="text-center mt-4 mb-25">
                  {!! showAd(3) !!}
                </div>
              @endif

              <div class="fq-cta" data-aos="fade-up">
                <span class="fq-cta-icon"><i class="fal fa-comments"></i></span>
                <div>
                  <h4>{{ __('Still have questions?') }}</h4>
                  <p>{{ __('Our team is happy to help with anything not covered above.') }}</p>
                </div>
                <a href="{{ route('contact') }}" class="fq-cta-btn">
                  {{ __('Contact us') }} <i class="fal fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <style>
    .fq-page {
      --fq-primary: #0e9f6e;
      --fq-primary-dark: #0a7d57;
      --fq-primary-deep: #06432f;
      --fq-primary-soft: #e5f5ee;
      --fq-ink: #101d2c;
      --fq-muted: #5e6b7e;
      --fq-line: #e4e9e6;
      background: #ffffff;
    }

    .fq-hero {
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

    .fq-hero-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1.5px, transparent 1.5px);
      background-size: 22px 22px;
      mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      pointer-events: none;
    }

    .fq-hero .container {
      position: relative;
      z-index: 1;
      text-align: center;
    }

    .fq-breadcrumb {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      margin-bottom: 22px;
      color: #a9c7ba;
      font-size: 12.5px;
      font-weight: 700;
    }

    .fq-breadcrumb a {
      color: #dbeee3;
      text-decoration: none;
    }

    .fq-breadcrumb i {
      font-size: 10px;
      color: #6f9a84;
    }

    .fq-breadcrumb span {
      color: #8be3bf;
    }

    .fq-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 18px;
      padding: 8px 15px;
      color: #dbeee3;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .fq-eyebrow-dot {
      width: 7px;
      height: 7px;
      background: #22d98b;
      border-radius: 50%;
      box-shadow: 0 0 0 3px rgba(34, 217, 139, 0.18);
    }

    .fq-hero h1 {
      margin: 0 0 12px;
      color: #ffffff;
      font-size: clamp(28px, 3.6vw, 42px);
      font-weight: 900;
      letter-spacing: -0.015em;
    }

    .fq-hero h1 span {
      color: #7fe0b9;
    }

    .fq-hero p {
      max-width: 560px;
      margin: 0 auto;
      color: #bcd8cb;
      font-size: 14.5px;
      line-height: 1.7;
    }

    .fq-section {
      padding: 64px 0 90px;
      background: #f4f7f5;
    }

    .fq-item {
      margin-bottom: 14px;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(16, 29, 44, 0.05);
      overflow: hidden;
    }

    .fq-btn {
      display: flex;
      align-items: center;
      gap: 16px;
      width: 100%;
      padding: 20px 22px;
      color: var(--fq-ink);
      background: #ffffff;
      border: 0;
      font-size: 15px;
      font-weight: 800;
      text-align: left;
    }

    .fq-btn:not(.collapsed) {
      color: var(--fq-primary-dark) !important;
      background: #ffffff !important;
      box-shadow: none !important;
    }

    .fq-btn::after {
      margin-left: auto;
      flex-shrink: 0;
    }

    .fq-num {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 34px;
      width: 34px;
      height: 34px;
      color: var(--fq-primary-dark);
      background: var(--fq-primary-soft);
      border-radius: 10px;
      font-size: 12.5px;
      font-weight: 900;
    }

    .fq-btn:not(.collapsed) .fq-num {
      color: #ffffff;
      background: var(--fq-primary);
    }

    .fq-body {
      padding: 0 22px 22px 72px;
    }

    .fq-body p {
      margin: 0;
      color: var(--fq-muted);
      font-size: 14px;
      line-height: 1.75;
    }

    .fq-cta {
      display: flex;
      align-items: center;
      gap: 18px;
      margin-top: 30px;
      padding: 24px;
      background: linear-gradient(135deg, #071122 0%, #06301f 100%);
      border-radius: 18px;
      color: #ffffff;
    }

    .fq-cta-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 52px;
      width: 52px;
      height: 52px;
      color: #ffffff;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      font-size: 21px;
    }

    .fq-cta h4 {
      margin: 0 0 4px;
      color: #ffffff;
      font-size: 16px;
      font-weight: 800;
    }

    .fq-cta p {
      margin: 0;
      color: #bcd8cb;
      font-size: 13px;
    }

    .fq-cta-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-left: auto;
      flex-shrink: 0;
      padding: 12px 20px;
      color: #ffffff;
      background: linear-gradient(120deg, var(--fq-primary), var(--fq-primary-dark));
      border-radius: 999px;
      font-size: 13px;
      font-weight: 800;
      text-decoration: none;
      white-space: nowrap;
    }

    .fq-cta-btn:hover {
      color: #ffffff;
    }

    @media (max-width: 767.98px) {
      .fq-hero {
        padding: 48px 0 42px;
      }

      .fq-section {
        padding: 44px 0 60px;
      }

      .fq-btn {
        padding: 16px 16px;
        gap: 12px;
        font-size: 14px;
      }

      .fq-body {
        padding: 0 16px 18px 62px;
      }

      .fq-cta {
        flex-wrap: wrap;
        text-align: center;
        justify-content: center;
      }

      .fq-cta-btn {
        margin-left: 0;
        width: 100%;
        justify-content: center;
      }
    }
  </style>
@endsection
