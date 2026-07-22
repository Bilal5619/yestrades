@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) && $pageHeading->vendor_signup_page_title ? $pageHeading->vendor_signup_page_title : __('Sign up') }}
@endsection

@section('metaKeywords')
  {{ !empty($seoInfo) ? $seoInfo->meta_keywords_vendor_signup : '' }}
@endsection

@section('metaDescription')
  {{ !empty($seoInfo) ? $seoInfo->meta_description_vendor_signup : '' }}
@endsection

@section('content')
  <main class="au-page">
    <section class="au-section">
      <div class="au-blob au-blob-1"></div>
      <div class="au-blob au-blob-2"></div>
      <div class="container">
        <div class="au-shell au-shell-wide" data-aos="fade-up">
          <div class="au-brand">
            <div class="au-brand-dots"></div>
            <div class="au-brand-inner">
              <span class="au-brand-eyebrow"><span class="au-eyebrow-dot"></span>{{ __('Grow with') }} {{ $websiteInfo->website_title ?? config('app.name') }}</span>
              <h1>{{ __('Get more jobs') }} <span>{{ __('in your area.') }}</span></h1>
              <p>{{ __('Register your business to receive booking requests, manage your calendar and get paid quickly.') }}</p>
              <ul class="au-points">
                <li><i class="fal fa-check-circle"></i>{{ __('Free to join, no monthly minimums') }}</li>
                <li><i class="fal fa-check-circle"></i>{{ __('Booking requests matched to your trade') }}</li>
                <li><i class="fal fa-check-circle"></i>{{ __('Fast payouts after job completion') }}</li>
              </ul>
            </div>
          </div>

          <div class="au-form-panel">
            <h3>{{ !empty($pageHeading) ? $pageHeading->vendor_signup_page_title : __('Sign up') }}</h3>
            <p class="au-form-sub">{{ __('Tell us about your business to get started.') }}</p>

            <div class="au-type-switch" aria-label="{{ __('Choose account type') }}">
              <a href="{{ route('user.signup') }}">{{ __('Customer') }}</a>
              <span class="active">{{ __('Business') }}</span>
            </div>
            <p class="au-type-help">
              {{ __('Choose Customer to book services, or Business if you provide services on') }} {{ $websiteInfo->website_title ?? config('app.name') }}.
            </p>

            @if (Session::has('success'))
              <div class="au-alert au-alert-success">{{ __(Session::get('success')) }}</div>
            @endif

            <form action="{{ route('vendor.signup_submit') }}" method="POST">
              @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="au-field mb-20">
                    <label for="userName">{{ __('Username') }}<span class="au-req">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" id="userName"
                      class="form-control" placeholder="{{ __('Username') }}">
                    @error('username')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="au-field mb-20">
                    <label for="name">{{ __('Full Name / Business Name') }}<span class="au-req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" id="name" class="form-control"
                      placeholder="{{ __('Full Name / Business Name') }}">
                    @error('name')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="au-field mb-20">
                    <label for="email">{{ __('Email') }}<span class="au-req">*</span></label>
                    <input type="email" value="{{ old('email') }}" name="email" id="email" class="form-control"
                      placeholder="{{ __('Email') }}">
                    @error('email')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="au-field mb-20">
                    <label for="phone">{{ __('Contact Number') }}<span class="au-req">*</span></label>
                    <input type="tel" value="{{ old('phone') }}" name="phone" id="phone" class="form-control"
                      placeholder="{{ __('Contact Number') }}">
                    @error('phone')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="au-field mb-20">
                    <label for="postcode">{{ __('Postcode') }}<span class="au-req">*</span></label>
                    <input type="text" value="{{ old('postcode', old('zip_code')) }}" name="postcode" id="postcode"
                      class="form-control" placeholder="{{ __('Postcode') }}">
                    @error('postcode')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="au-field mb-20">
                    <label for="businessType">{{ __('Business type') }}<span class="au-req">*</span></label>
                    <select name="business_type" id="businessType" class="form-control">
                      <option value="">{{ __('Select business type') }}</option>
                      <option value="sole_trader" @selected(old('business_type') === 'sole_trader')>
                        {{ __('Sole Trader') }}
                      </option>
                      <option value="limited_company" @selected(old('business_type') === 'limited_company')>
                        {{ __('Limited Company') }}
                      </option>
                    </select>
                    @error('business_type')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-12">
                  <div class="au-field mb-20">
                    <label for="address">{{ __('Full Address') }}<span class="au-req">*</span></label>
                    <textarea name="address" id="address" class="form-control" rows="3"
                      placeholder="{{ __('Enter full address') }}">{{ old('address') }}</textarea>
                    @error('address')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-12">
                  <div class="au-field mb-20">
                    <label>{{ __('Service Type / Trade Category') }}<span class="au-req">*</span></label>
                    <div class="au-trade-dropdown" id="tradeCategoryDropdown">
                      <button type="button" class="au-trade-btn" id="tradeCategoryButton">
                        <span id="tradeCategoryLabel">{{ __('Select trade categories') }}</span>
                        <i class="fal fa-chevron-down"></i>
                      </button>
                      <div class="au-trade-menu" aria-labelledby="tradeCategoryButton">
                        @foreach ($serviceCategories as $category)
                          <label class="au-trade-option">
                            <input type="checkbox" name="service_category_ids[]" value="{{ $category->id }}"
                              data-trade-category-label="{{ $category->name }}"
                              @checked(in_array($category->id, old('service_category_ids', [])))>
                            <span>{{ $category->name }}</span>
                          </label>
                        @endforeach
                      </div>
                    </div>
                    @error('service_category_ids')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                    @error('service_category_ids.*')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-12">
                  <div class="au-field mb-20">
                    <label for="details">{{ __('Details') }}</label>
                    <textarea name="details" id="details" class="form-control" rows="3"
                      placeholder="{{ __('e.g. I am a licensed electrician specializing in residential wiring and solar panel installation. I also offer emergency repair services.') }}">{{ old('details') }}</textarea>
                    @error('details')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="au-field mb-20">
                    <label for="password">{{ __('Password') }}<span class="au-req">*</span></label>
                    <div class="position-relative">
                      <input type="password" name="password" id="password" class="form-control"
                        placeholder="{{ __('Password') }}">
                      <span class="show-password-field">
                        <i class="show-icon"></i>
                      </span>
                    </div>
                    @error('password')
                      <p class="text-danger mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
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
                </div>
                @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                  <div class="col-12">
                    <div class="au-field mb-20">
                      {!! NoCaptcha::renderJs() !!}
                      {!! NoCaptcha::display() !!}
                      @error('g-recaptcha-response')
                        <p class="mt-1 text-danger">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                @endif
              </div>

              <button class="au-submit-btn" type="submit" aria-label="Signup">
                {{ __('Create business account') }} <i class="fal fa-long-arrow-right"></i>
              </button>
            </form>

            <div class="au-links au-links-center">
              <span>
                {{ __('Already a member') . '?' }}
                <a href="{{ route('vendor.login') }}" target="_self" title="Login Now">{{ __('Login Now') }}</a>
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
      align-items: stretch;
    }

    .au-shell-wide {
      max-width: 1080px;
      grid-template-columns: minmax(0, 0.78fr) minmax(0, 1.22fr);
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

    /* ── Form panel ───────────────────────────────── */
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

    /* ── Account type switch ──────────────────────── */
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

    .au-field textarea.form-control {
      min-height: 0;
      padding: 12px 16px;
    }

    .au-field select.form-control {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath fill='%235e6b7e' d='M6 8 0 0h12z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 16px center;
    }

    .au-field .form-control:focus {
      background-color: #ffffff;
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

    /* ── Trade category dropdown ───────────────────── */
    .au-trade-dropdown {
      position: relative;
    }

    .au-trade-btn {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      width: 100%;
      min-height: 46px;
      padding: 0 16px;
      color: var(--au-ink);
      background: #f4f7f5;
      border: 1px solid var(--au-line);
      border-radius: 12px;
      font-size: 14px;
      text-align: left;
    }

    .au-trade-btn span {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .au-trade-btn i {
      flex: 0 0 auto;
      color: var(--au-muted);
      font-size: 12px;
    }

    .au-trade-menu {
      position: absolute;
      z-index: 20;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      display: none;
      max-height: 240px;
      overflow-y: auto;
      padding: 8px;
      background: #ffffff;
      border: 1px solid var(--au-line);
      border-radius: 12px;
      box-shadow: 0 20px 44px rgba(16, 29, 44, 0.16);
    }

    .au-trade-dropdown.is-open .au-trade-menu {
      display: block;
    }

    .au-trade-option {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0;
      padding: 9px 10px;
      color: var(--au-ink-2);
      border-radius: 9px;
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
    }

    .au-trade-option:hover {
      background: var(--au-primary-soft);
      color: var(--au-primary-dark);
    }

    .au-trade-option input {
      width: 16px;
      height: 16px;
      flex: 0 0 auto;
      accent-color: var(--au-primary);
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

    /* ── Responsive ───────────────────────────────── */
    @media (max-width: 991.98px) {
      .au-shell,
      .au-shell-wide {
        grid-template-columns: 1fr;
        max-width: 620px;
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

@push('scripts')
  <script>
    (function () {
      var tradeDropdown = document.getElementById('tradeCategoryDropdown');
      var tradeButton = document.getElementById('tradeCategoryButton');
      var tradeLabel = document.getElementById('tradeCategoryLabel');

      if (tradeDropdown && tradeButton && tradeLabel) {
        var updateTradeLabel = function () {
          var selected = Array.prototype.slice.call(
            tradeDropdown.querySelectorAll('input[name="service_category_ids[]"]:checked')
          ).map(function (input) {
            return input.getAttribute('data-trade-category-label');
          });

          tradeLabel.textContent = selected.length > 0
            ? selected.join(', ')
            : '{{ __('Select trade categories') }}';
        };

        tradeButton.addEventListener('click', function () {
          tradeDropdown.classList.toggle('is-open');
        });

        tradeDropdown.addEventListener('change', updateTradeLabel);

        document.addEventListener('click', function (event) {
          if (!tradeDropdown.contains(event.target)) {
            tradeDropdown.classList.remove('is-open');
          }
        });

        updateTradeLabel();
      }
    })();
  </script>
@endpush
