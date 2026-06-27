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
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => !empty($pageHeading) ? $pageHeading->faq_page_title : __('FAQ'),
  ])

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
        'question' => 'Can landlords use 11DONE?',
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

  <!-- Faq-area start -->
  <div class="faq-area pt-100 pb-75">
    <div class="container">
      <div class="accordion" id="faqAccordion">
        <div class="row justify-content-center">
          <div class="col-lg-8" data-aos="fade-up">
            @if (count($faqs) == 0)
              <div class="section-title title-center mb-50">
                <span class="subtitle">{{ __('Help Center') }}</span>
                <h2 class="title mb-20">{{ __('Frequently Asked Questions') }}</h2>
                <p class="text mx-auto">
                  {{ __('Find quick answers about booking services, payments and managing your service details online.') }}
                </p>
              </div>

              @foreach ($defaultFaqs as $faq)
                <div class="accordion-item mb-30">
                  <h6 class="accordion-header" id="defaultFaqHeading_{{ $loop->iteration }}">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                      data-bs-toggle="collapse" data-bs-target="#defaultFaqCollapse_{{ $loop->iteration }}"
                      aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                      aria-controls="defaultFaqCollapse_{{ $loop->iteration }}">
                      {{ __($faq['question']) }}
                    </button>
                  </h6>
                  <div id="defaultFaqCollapse_{{ $loop->iteration }}"
                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                    aria-labelledby="defaultFaqHeading_{{ $loop->iteration }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      <p>{{ __($faq['answer']) }}</p>
                    </div>
                  </div>
                </div>
              @endforeach
            @else
              @foreach ($faqs as $faq)
                <div class="accordion-item mb-30">
                  <h6 class="accordion-header" id="headingOne_{{ $faq->id }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapseOne_{{ $faq->id }}" aria-expanded="true"
                      aria-controls="collapseOne_{{ $faq->id }}">
                      {{ $faq->question }}
                    </button>
                  </h6>
                  <div id="collapseOne_{{ $faq->id }}"
                    class="accordion-collapse collapse {{ $loop->iteration == 1 ? 'show' : '' }}"
                    aria-labelledby="headingOne_{{ $faq->id }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      <p>
                        {{ $faq->answer }}
                      </p>
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
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Faq-area end -->
@endsection
