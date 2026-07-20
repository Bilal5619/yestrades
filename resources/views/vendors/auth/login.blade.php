@extends('frontend.layout')
@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->vendor_login_page_title : __('Login') }}
@endsection
@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keywords_vendor_login }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_vendor_login }}
  @endif
@endsection

@section('content')
  <main class="au-page">
    <section class="au-section">
      <div class="au-blob au-blob-1"></div>
      <div class="au-blob au-blob-2"></div>
      <div class="container">
        <div class="au-shell" data-aos="fade-up">
          <div class="au-brand">
            <div class="au-brand-dots"></div>
            <div class="au-brand-inner">
              <span class="au-brand-eyebrow"><span class="au-eyebrow-dot"></span>{{ __('Vendor portal') }}</span>
              <h1>{{ __('Manage your jobs') }} <span>{{ __('in one dashboard.') }}</span></h1>
              <p>{{ __('Sign in to view bookings, update your services and get paid for completed jobs.') }}</p>
              <ul class="au-points">
                <li><i class="fal fa-check-circle"></i>{{ __('See new bookings the moment they land') }}</li>
                <li><i class="fal fa-check-circle"></i>{{ __('Manage services, pricing and availability') }}</li>
                <li><i class="fal fa-check-circle"></i>{{ __('Track earnings and payouts') }}</li>
              </ul>
            </div>
          </div>

          <div class="au-form-panel">
            <h3>{{ !empty($pageHeading) ? $pageHeading->vendor_login_page_title : __('Vendor Login') }}</h3>
            <p class="au-form-sub">{{ __('Enter your business account details to continue.') }}</p>

            @if (Session::has('success'))
              <div class="au-alert au-alert-success">{{ __(Session::get('success')) }}</div>
            @endif
            @if (Session::has('error'))
              <div class="au-alert au-alert-error">{{ __(Session::get('error')) }}</div>
            @endif

            <form action="{{ route('vendor.login_submit') }}" method="POST">
              @csrf
              <input type="hidden" name="package_id" value="{{ request()->buy_package }}">
              <input type="hidden" name="redirect_path" value="{{ request()->redirectPath }}">
              <div class="au-field mb-20">
                <label for="email">{{ __('Email') }}<span class="au-req">*</span></label>
                <input type="email" value="{{ old('email') }}" name="email" id="email" class="form-control"
                  placeholder="{{ __('Enter Your Email Address') }}">
                @error('email')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>
              <div class="au-field mb-20">
                <label for="password">{{ __('Password') }}<span class="au-req">*</span></label>
                <div class="position-relative">
                  <input type="password" name="password" id="password" class="form-control"
                    placeholder="{{ __('Password') }}" required>
                  <span class="show-password-field">
                    <i class="show-icon"></i>
                  </span>
                </div>
                @error('password')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>
              @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                <div class="au-field mb-20">
                  {!! NoCaptcha::renderJs() !!}
                  {!! NoCaptcha::display() !!}
                  @error('g-recaptcha-response')
                    <p class="mt-1 text-danger">{{ $message }}</p>
                  @enderror
                </div>
              @endif

              <button class="au-submit-btn" type="submit" aria-label="Login">
                {{ __('Login') }} <i class="fal fa-long-arrow-right"></i>
              </button>
            </form>

            <div class="au-links">
              <a href="{{ route('vendor.forget.password') }}">{{ __('Forgot password') . '?' }}</a>
              <span>
                {{ __("Don't have an account") . '?' }}
                <a href="{{ route('vendor.signup') }}" title="Go Signup" target="_self">{{ __('Sign up') }}</a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <style>
    .au-page {
      --au-primary: #0e9f6e;
      --au-primary-dark: #0a7d57;
      --au-primary-deep: #06432f;
      --au-primary-soft: #e5f5ee;
      --au-amber: #f5a524;
      --au-ink: #101d2c;
      --au-ink-2: #33415c;
      --au-muted: #5e6b7e;
      --au-line: #e4e9e6;
      background: #f4f7f5;
    }

    .au-section {
      position: relative;
      overflow: hidden;
      margin-top: 84px;
      padding: 56px 0;
    }

    .au-blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(90px);
      pointer-events: none;
      z-index: 0;
    }

    .au-blob-1 {
      top: -120px;
      right: -100px;
      width: 420px;
      height: 420px;
      background: rgba(14, 159, 110, 0.14);
    }

    .au-blob-2 {
      bottom: -140px;
      left: -110px;
      width: 400px;
      height: 400px;
      background: rgba(245, 165, 36, 0.1);
    }

    .au-section .container {
      position: relative;
      z-index: 1;
    }

    .au-shell {
      display: grid;
      grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
      max-width: 980px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 26px;
      box-shadow: 0 30px 70px rgba(16, 29, 44, 0.14);
      overflow: hidden;
    }

    /* ── Brand panel ──────────────────────────────── */
    .au-brand {
      position: relative;
      overflow: hidden;
      padding: 48px 40px;
      background:
        radial-gradient(circle at 90% 6%, rgba(14, 159, 110, 0.5), transparent 42%),
        radial-gradient(circle at 6% 100%, rgba(245, 165, 36, 0.14), transparent 34%),
        linear-gradient(135deg, #071122 0%, #06301f 100%);
      color: #ffffff;
    }

    .au-brand-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1.5px, transparent 1.5px);
      background-size: 22px 22px;
      mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 65%);
      -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 65%);
      pointer-events: none;
    }

    .au-brand-inner {
      position: relative;
      z-index: 1;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .au-brand-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      align-self: flex-start;
      margin-bottom: 20px;
      padding: 7px 14px;
      color: #dbeee3;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .au-eyebrow-dot {
      width: 7px;
      height: 7px;
      background: #22d98b;
      border-radius: 50%;
      box-shadow: 0 0 0 3px rgba(34, 217, 139, 0.18);
    }

    .au-brand h1 {
      margin: 0 0 14px;
      color: #ffffff;
      font-size: 28px;
      line-height: 1.22;
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    .au-brand h1 span {
      display: block;
      color: #7fe0b9;
    }

    .au-brand > .au-brand-inner > p {
      margin: 0 0 26px;
      color: #b7c4d6;
      font-size: 13.5px;
      line-height: 1.7;
      max-width: 320px;
    }

    .au-points {
      display: grid;
      gap: 13px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .au-points li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: #d6e0ec;
      font-size: 13px;
      font-weight: 600;
      line-height: 1.5;
    }

    .au-points i {
      margin-top: 2px;
      color: #22d98b;
    }

    /* ── Form panel ───────────────────────────────── */
    .au-form-panel {
      padding: 48px 42px;
    }

    .au-form-panel h3 {
      margin: 0 0 6px;
      color: var(--au-ink);
      font-size: 24px;
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    .au-form-sub {
      margin: 0 0 26px;
      color: var(--au-muted);
      font-size: 13.5px;
    }

    .au-alert {
      margin-bottom: 18px;
      padding: 12px 15px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 700;
    }

    .au-alert-success {
      color: var(--au-primary-dark);
      background: var(--au-primary-soft);
      border: 1px solid rgba(14, 159, 110, 0.25);
    }

    .au-alert-error {
      color: #b3261e;
      background: #fdecea;
      border: 1px solid rgba(179, 38, 30, 0.2);
    }

    .au-field label {
      display: block;
      margin-bottom: 7px;
      color: var(--au-ink-2);
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .au-req {
      margin-left: 3px;
      color: #e8506c;
    }

    .au-field .form-control {
      min-height: 48px;
      padding: 0 16px;
      color: var(--au-ink);
      background: #f4f7f5;
      border: 1px solid var(--au-line);
      border-radius: 12px;
      font-size: 14px;
      transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .au-field .form-control:focus {
      background: #ffffff;
      border-color: rgba(14, 159, 110, 0.45);
      box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.12);
      outline: none;
    }

    .au-submit-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      min-height: 50px;
      padding: 0 22px;
      margin-top: 4px;
      color: #ffffff;
      background: linear-gradient(120deg, var(--au-primary), var(--au-primary-dark));
      border: 0;
      border-radius: 12px;
      font-size: 14.5px;
      font-weight: 800;
      box-shadow: 0 14px 30px rgba(14, 159, 110, 0.3);
      transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .au-submit-btn:hover {
      color: #ffffff;
      filter: brightness(1.06);
      transform: translateY(-2px);
      box-shadow: 0 18px 38px rgba(14, 159, 110, 0.4);
    }

    .au-links {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px dashed var(--au-line);
      font-size: 13px;
    }

    .au-links a {
      color: var(--au-primary-dark);
      font-weight: 800;
      text-decoration: none;
    }

    .au-links a:hover {
      color: var(--au-primary);
    }

    .au-links span {
      color: var(--au-muted);
      font-weight: 600;
    }

    /* ── Responsive ───────────────────────────────── */
    @media (max-width: 991.98px) {
      .au-shell {
        grid-template-columns: 1fr;
        max-width: 560px;
      }

      .au-brand {
        padding: 36px 32px;
      }

      .au-brand-inner > p {
        max-width: 100%;
      }
    }

    @media (max-width: 575.98px) {
      .au-section {
        padding: 32px 0;
      }

      .au-form-panel {
        padding: 32px 22px;
      }

      .au-brand {
        padding: 32px 24px;
      }

      .au-links {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
@endsection
