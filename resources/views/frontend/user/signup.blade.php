@extends('frontend.layout')
@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->signup_page_title : __('Sign up') }}
@endsection
@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_signup }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_signup }}
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
              <span class="au-brand-eyebrow"><span class="au-eyebrow-dot"></span>{{ __('Join') }} {{ $websiteInfo->website_title ?? config('app.name') }}</span>
              <h1>{{ __('Create your account') }} <span>{{ __('in under a minute.') }}</span></h1>
              <p>{{ __('Book certificates, repairs and cleaning with clear pricing every time.') }}</p>
              <ul class="au-points">
                <li><i class="fal fa-check-circle"></i>{{ __('Fixed prices, no hidden fees') }}</li>
                <li><i class="fal fa-check-circle"></i>{{ __('Manage all your bookings in one place') }}</li>
                <li><i class="fal fa-check-circle"></i>{{ __('Save addresses for faster checkout') }}</li>
              </ul>
            </div>
          </div>

          <div class="au-form-panel">
            <h3>{{ !empty($pageHeading) ? $pageHeading->signup_page_title : __('Sign up') }}</h3>
            <p class="au-form-sub">{{ __('Create an account to start booking services.') }}</p>

            {{-- Business signup option hidden for now (client request) - restore the toggle below to bring it back --}}
            {{-- <div class="au-type-switch" aria-label="{{ __('Choose account type') }}">
              <span class="active">{{ __('Customer') }}</span>
              <a href="{{ route('vendor.signup') }}">{{ __('Business') }}</a>
            </div>
            <p class="au-type-help">
              {{ __('Choose Customer to book services, or Business if you provide services on') }} {{ $websiteInfo->website_title ?? config('app.name') }}.
            </p> --}}

            @if (Session::has('success'))
              <div class="au-alert au-alert-success">{{ __(Session::get('success')) }}</div>
            @endif
            @if (Session::has('warning'))
              <div class="au-alert au-alert-warning">{{ __(Session::get('warning')) }}</div>
            @endif

            <form id="#authForm" action="{{ route('user.signup_submit') }}" method="POST">
              @csrf
              <div class="au-field mb-20">
                <label for="userName">{{ __('Full Name') }}<span class="au-req">*</span></label>
                <input type="text" name="username" value="{{ old('username') }}" id="userName" class="form-control"
                  placeholder="{{ __('Full Name') }}">
                @error('username')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>
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
                    placeholder="{{ __('Enter Password') }}">
                  <span class="show-password-field">
                    <i class="show-icon"></i>
                  </span>
                </div>
                @error('password')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>
              <div class="au-field mb-20">
                <label for="confirmPassword">{{ __('Confirm Password') }}<span class="au-req">*</span></label>
                <div class="position-relative">
                  <input type="password" name="password_confirmation" id="confirmPassword" class="form-control"
                    placeholder="{{ __('Confirm Password') }}">
                  <span class="show-password-field">
                    <i class="show-icon"></i>
                  </span>
                </div>
                @error('password_confirmation')
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

              <button class="au-submit-btn" type="submit" aria-label="Signup">
                {{ __('Sign up') }} <i class="fal fa-long-arrow-right"></i>
              </button>

              @if ($bs->facebook_login_status == 1 || $googleLoginEnabled)
                <div class="au-social">
                  <div class="au-social-divider">{{ __('Or Signup With') }}</div>
                  <div class="au-social-actions">
                    @if ($bs->facebook_login_status == 1)
                      <a class="au-facebook-btn" href="{{ route('user.login.facebook') }}">
                        <i class="fab fa-facebook-f"></i>
                      </a>
                    @endif

                    @if ($googleLoginEnabled)
                      <a class="au-google-btn" href="{{ route('user.login.google') }}">
                        <i class="fab fa-google"></i>
                        <span>{{ __('Continue with Google') }}</span>
                      </a>
                    @endif
                  </div>
                </div>
              @endif
            </form>

            <div class="au-links au-links-center">
              <span>
                {{ __('Already a member') . '?' }}
                <a href="{{ route('user.login') }}" target="_self" title="Login Now">{{ __('Login Now') }}</a>
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

    /* Brand panel */
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
      font-size: 27px;
      line-height: 1.24;
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

    /* Form panel */
    .au-form-panel {
      padding: 42px 42px 40px;
    }

    .au-form-panel h3 {
      margin: 0 0 6px;
      color: var(--au-ink);
      font-size: 23px;
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    .au-form-sub {
      margin: 0 0 22px;
      color: var(--au-muted);
      font-size: 13.5px;
    }

    /* Account type switch */
    .au-type-switch {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 8px;
      margin-bottom: 14px;
      padding: 5px;
      background: #f4f7f5;
      border: 1px solid var(--au-line);
      border-radius: 12px;
    }

    .au-type-switch a,
    .au-type-switch span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 42px;
      padding: 8px 12px;
      color: var(--au-ink-2);
      border-radius: 9px;
      font-size: 13.5px;
      font-weight: 800;
      line-height: 1;
      text-decoration: none;
    }

    .au-type-switch .active {
      color: #ffffff;
      background: linear-gradient(120deg, var(--au-primary), var(--au-primary-dark));
      box-shadow: 0 10px 22px rgba(14, 159, 110, 0.3);
    }

    .au-type-help {
      margin: 0 0 20px;
      color: var(--au-muted);
      font-size: 12.5px;
      line-height: 1.6;
      text-align: center;
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

    .au-alert-warning {
      color: #8a5a00;
      background: #fff4d6;
      border: 1px solid rgba(138, 90, 0, 0.2);
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
      min-height: 46px;
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

    .au-social {
      margin-top: 22px;
    }

    .au-social-divider {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
      color: var(--au-muted);
      font-size: 13px;
      font-weight: 700;
    }

    .au-social-divider::before,
    .au-social-divider::after {
      content: "";
      flex: 1;
      height: 1px;
      background: var(--au-line);
    }

    .au-social-actions {
      display: flex;
      gap: 10px;
    }

    .au-facebook-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 50px;
      width: 50px;
      height: 50px;
      color: #ffffff;
      background: #1877f2;
      border-radius: 12px;
      font-size: 17px;
      text-decoration: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .au-facebook-btn:hover {
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 12px 26px rgba(24, 119, 242, 0.32);
    }

    .au-google-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      flex: 1;
      min-height: 50px;
      padding: 0 16px;
      color: var(--au-ink);
      background: #ffffff;
      border: 1px solid var(--au-line);
      border-radius: 12px;
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .au-google-btn:hover {
      color: var(--au-ink);
      border-color: #c7ceda;
      box-shadow: 0 10px 22px rgba(20, 24, 35, 0.1);
      transform: translateY(-2px);
    }

    .au-google-btn i {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 24px;
      height: 24px;
      color: #ffffff;
      background: #34a853;
      border-radius: 50%;
      font-size: 13px;
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

    .au-links-center {
      justify-content: center;
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

    /* Responsive */
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
        padding: 30px 22px;
      }

      .au-brand {
        padding: 32px 24px;
      }
    }
  </style>
@endsection
