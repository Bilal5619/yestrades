@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->contact_page_title : __('Contact') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_contact }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_contact }}
  @endif
@endsection

@section('content')
  <main class="ct-page">

    <!-- ══════════ Banner start ══════════ -->
    <section class="ct-hero">
      <div class="ct-hero-blob ct-hero-blob-1"></div>
      <div class="ct-hero-blob ct-hero-blob-2"></div>
      <div class="ct-hero-dots"></div>
      <div class="container">
        <nav aria-label="breadcrumb" class="ct-breadcrumb">
          <a href="{{ route('index') }}">{{ __('Home') }}</a>
          <i class="fal fa-angle-right"></i>
          <span>{{ !empty($pageHeading) ? $pageHeading->contact_page_title : __('Contact') }}</span>
        </nav>
        <h1>{{ !empty($pageHeading) ? $pageHeading->contact_page_title : __('Get in touch') }}</h1>
        <p>{{ __('Questions about a booking, a job or a quote? Send us a message and our team will reply shortly.') }}</p>
      </div>
    </section>
    <!-- ══════════ Banner end ══════════ -->

    <section class="ct-section">
      <div class="container">
        <div class="ct-grid">
          <!-- Contact info column -->
          <div class="ct-info-col" data-aos="fade-up">
            <div class="ct-info-card">
              <span class="ct-info-icon"><i class="fal fa-phone-plus"></i></span>
              <div>
                <span class="ct-info-label">{{ __('Call us') }}</span>
                @if (!empty($info->contact_number))
                  <a href="tel:{{ $info->contact_number }}">{{ $info->contact_number }}</a>
                @else
                  <span class="ct-info-empty">{{ __('Available on request') }}</span>
                @endif
              </div>
            </div>
            <div class="ct-info-card">
              <span class="ct-info-icon"><i class="fal fa-envelope"></i></span>
              <div>
                <span class="ct-info-label">{{ __('Email us') }}</span>
                @if (!empty($info->email_address))
                  <a href="mailTo:{{ $info->email_address }}">{{ $info->email_address }}</a>
                @else
                  <span class="ct-info-empty">{{ __('Available on request') }}</span>
                @endif
              </div>
            </div>
            <div class="ct-info-card">
              <span class="ct-info-icon"><i class="fal fa-phone"></i></span>
              <div>
                <span class="ct-info-label">{{ __('Landline') }}</span>
                <a href="tel:01506560130">01506 560130</a>
              </div>
            </div>
            <div class="ct-info-card">
              <span class="ct-info-icon"><i class="fal fa-map-marker-alt"></i></span>
              <div>
                <span class="ct-info-label">{{ __('Coverage') }}</span>
                <span>{{ __('United Kingdom') }}</span>
              </div>
            </div>
            <div class="ct-info-card ct-info-whatsapp">
              <span class="ct-info-icon"><i class="fab fa-whatsapp"></i></span>
              <div>
                <span class="ct-info-label">{{ __('Fast replies') }}</span>
                <span>{{ __('Same-day response on working days') }}</span>
              </div>
            </div>
          </div>

          <!-- Form column -->
          <div class="ct-form-col" data-aos="fade-up" data-aos-delay="100">
            <div class="ct-form-card">
              <h2>{{ __('Send Us a Message') }}</h2>
              <p>{{ __('Tell us what you need help with and we will get back to you.') }}</p>

              @if (Session::has('success'))
                <div class="ct-alert ct-alert-success">{{ __(Session::get('success')) }}</div>
                <script>
                  if (typeof fbq === 'function') {
                    fbq('track', 'Contact');
                  }
                </script>
              @endif
              @if (Session::has('error'))
                <div class="ct-alert ct-alert-error">{{ __(Session::get('error')) }}</div>
              @endif

              <form id="contactForm" action="{{ route('contact.send_mail') }}" method="post">
                @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="ct-field mb-20">
                      <label for="name">{{ __('Full name') }}</label>
                      <input type="text" name="name" class="form-control" id="name"
                        placeholder="{{ __('Enter Your Full Name') }}" />
                      @error('name')
                        <div class="help-block with-errors text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="ct-field mb-20">
                      <label for="email">{{ __('Email') }}</label>
                      <input type="email" name="email" class="form-control" id="email" required
                        data-error="Enter your email" placeholder="{{ __('Enter Your Email') }}" />
                      @error('email')
                        <div class="help-block with-errors text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="ct-field mb-20">
                      <label for="subject">{{ __('Subject') }}</label>
                      <input type="text" name="subject" class="form-control" id="subject" required
                        placeholder="{{ __('Enter Email Subject') }}" />
                      @error('subject')
                        <div class="help-block with-errors text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="ct-field mb-20">
                      <label for="message">{{ __('Message') }}</label>
                      <textarea name="message" id="message" class="form-control" cols="30" rows="6" required
                        placeholder="{{ __('Write Your Message') }}"></textarea>
                      @error('message')
                        <div class="help-block with-errors text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                    <div class="col-md-12">
                      <div class="ct-field mb-20">
                        {!! NoCaptcha::renderJs() !!}
                        {!! NoCaptcha::display() !!}
                        @error('g-recaptcha-response')
                          <div class="help-block with-errors text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  @endif
                  <div class="col-md-12">
                    <button type="submit" class="ct-submit-btn" title="{{ __('Send message') }}">
                      {{ __('Send message') }} <i class="fal fa-long-arrow-right"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      @if (!empty(showAd(3)))
        <div class="ct-ad-slot">
          {!! showAd(3) !!}
        </div>
      @endif
    </section>
  </main>

  <style>
    .ct-page {
      --ct-primary: #0e9f6e;
      --ct-primary-dark: #0a7d57;
      --ct-primary-deep: #06432f;
      --ct-primary-soft: #e5f5ee;
      --ct-amber: #f5a524;
      --ct-ink: #101d2c;
      --ct-ink-2: #33415c;
      --ct-muted: #5e6b7e;
      --ct-line: #e4e9e6;
      --ct-bg: #f4f7f5;
      --ct-radius: 18px;
      --ct-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
      --ct-shadow-lg: 0 28px 64px rgba(16, 29, 44, 0.13);
      background: #ffffff;
    }

    /* ── Banner ───────────────────────────────────── */
    .ct-hero {
      position: relative;
      overflow: hidden;
      margin-top: 84px;
      padding: 64px 0 60px;
      background:
        radial-gradient(circle at 92% 6%, rgba(14, 159, 110, 0.5), transparent 40%),
        radial-gradient(circle at 4% 100%, rgba(245, 165, 36, 0.14), transparent 32%),
        linear-gradient(120deg, #0d1b16 0%, var(--ct-primary-deep) 100%);
      color: #ffffff;
    }

    .ct-hero-blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      pointer-events: none;
    }

    .ct-hero-blob-1 {
      top: -140px;
      right: -100px;
      width: 420px;
      height: 420px;
      background: rgba(14, 159, 110, 0.32);
    }

    .ct-hero-blob-2 {
      bottom: -160px;
      left: -120px;
      width: 380px;
      height: 380px;
      background: rgba(245, 165, 36, 0.1);
    }

    .ct-hero-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1.5px, transparent 1.5px);
      background-size: 22px 22px;
      mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      pointer-events: none;
    }

    .ct-hero .container {
      position: relative;
      z-index: 1;
    }

    .ct-breadcrumb {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 16px;
      color: #a9c7ba;
      font-size: 12.5px;
      font-weight: 700;
    }

    .ct-breadcrumb a {
      color: #dbeee3;
      text-decoration: none;
    }

    .ct-breadcrumb a:hover {
      color: #ffffff;
    }

    .ct-breadcrumb i {
      font-size: 10px;
      color: #6f9a84;
    }

    .ct-breadcrumb span {
      color: #8be3bf;
    }

    .ct-hero h1 {
      margin: 0 0 10px;
      color: #ffffff;
      font-size: clamp(28px, 3.4vw, 40px);
      font-weight: 900;
      letter-spacing: -0.015em;
    }

    .ct-hero p {
      max-width: 540px;
      margin: 0;
      color: #bcd8cb;
      font-size: 14.5px;
      line-height: 1.7;
    }

    /* ── Layout ───────────────────────────────────── */
    .ct-section {
      padding: 64px 0 90px;
      background: var(--ct-bg);
    }

    .ct-grid {
      display: grid;
      grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.15fr);
      gap: 28px;
      align-items: start;
    }

    /* ── Info cards ───────────────────────────────── */
    .ct-info-col {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .ct-info-card {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      padding: 22px;
      background: #ffffff;
      border-radius: var(--ct-radius);
      box-shadow: var(--ct-shadow);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .ct-info-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--ct-shadow-lg);
    }

    .ct-info-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 46px;
      width: 46px;
      height: 46px;
      color: var(--ct-primary-dark);
      background: var(--ct-primary-soft);
      border-radius: 14px;
      font-size: 18px;
    }

    .ct-info-whatsapp .ct-info-icon {
      color: #128c7e;
      background: rgba(37, 211, 102, 0.14);
    }

    .ct-info-label {
      display: block;
      margin-bottom: 4px;
      color: var(--ct-muted);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .ct-info-card a,
    .ct-info-card > div > span:not(.ct-info-label) {
      display: block;
      color: var(--ct-ink);
      font-size: 14.5px;
      font-weight: 700;
      text-decoration: none;
      line-height: 1.5;
    }

    .ct-info-card a:hover {
      color: var(--ct-primary-dark);
    }

    .ct-info-empty {
      color: var(--ct-muted);
      font-weight: 600;
    }

    /* ── Form card ────────────────────────────────── */
    .ct-form-card {
      padding: 34px;
      background: #ffffff;
      border-radius: 22px;
      box-shadow: var(--ct-shadow-lg);
    }

    .ct-form-card h2 {
      margin: 0 0 8px;
      color: var(--ct-ink);
      font-size: 24px;
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    .ct-form-card > p {
      margin: 0 0 26px;
      color: var(--ct-muted);
      font-size: 14px;
      line-height: 1.6;
    }

    .ct-alert {
      margin-bottom: 20px;
      padding: 13px 16px;
      border-radius: 12px;
      font-size: 13.5px;
      font-weight: 700;
    }

    .ct-alert-success {
      color: var(--ct-primary-dark);
      background: var(--ct-primary-soft);
      border: 1px solid rgba(14, 159, 110, 0.25);
    }

    .ct-alert-error {
      color: #b3261e;
      background: #fdecea;
      border: 1px solid rgba(179, 38, 30, 0.2);
    }

    .ct-field label {
      display: block;
      margin-bottom: 7px;
      color: var(--ct-ink-2);
      font-size: 12.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .ct-field .form-control {
      min-height: 48px;
      padding: 0 16px;
      color: var(--ct-ink);
      background: var(--ct-bg);
      border: 1px solid var(--ct-line);
      border-radius: 12px;
      font-size: 14px;
      transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .ct-field textarea.form-control {
      min-height: 0;
      padding: 14px 16px;
    }

    .ct-field .form-control:focus {
      background: #ffffff;
      border-color: rgba(14, 159, 110, 0.45);
      box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.12);
      outline: none;
    }

    .ct-submit-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      min-height: 50px;
      padding: 0 22px;
      color: #ffffff;
      background: linear-gradient(120deg, var(--ct-primary), var(--ct-primary-dark));
      border: 0;
      border-radius: 12px;
      font-size: 14.5px;
      font-weight: 800;
      box-shadow: 0 14px 30px rgba(14, 159, 110, 0.3);
      transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .ct-submit-btn:hover {
      color: #ffffff;
      filter: brightness(1.06);
      transform: translateY(-2px);
      box-shadow: 0 18px 38px rgba(14, 159, 110, 0.4);
    }

    .ct-ad-slot {
      margin-top: 40px;
      text-align: center;
    }

    /* ── Responsive ───────────────────────────────── */
    @media (max-width: 991.98px) {
      .ct-grid {
        grid-template-columns: 1fr;
      }

      .ct-info-col {
        flex-direction: row;
        flex-wrap: wrap;
      }

      .ct-info-card {
        flex: 1 1 calc(50% - 8px);
      }
    }

    @media (max-width: 767.98px) {
      .ct-hero {
        padding: 48px 0 44px;
      }

      .ct-section {
        padding: 44px 0 60px;
      }

      .ct-form-card {
        padding: 24px;
      }

      .ct-info-card {
        flex: 1 1 100%;
      }
    }
  </style>
@endsection
