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

@section('style')
  <style>
    .account-type-switch {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin-bottom: 28px;
      padding: 6px;
      background: #f6f7f8;
      border: 1px solid #ecebea;
      border-radius: 8px;
    }

    .account-type-switch a,
    .account-type-switch span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      padding: 10px 14px;
      color: #404550;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 800;
      line-height: 1;
      text-decoration: none;
    }

    .account-type-switch .active {
      color: #ffffff;
      background: var(--color-primary);
      box-shadow: 0 10px 22px rgba(var(--color-primary-rgb), 0.18);
    }

    .account-type-help {
      margin: -14px 0 22px;
      color: #747984;
      font-size: 14px;
      line-height: 1.6;
      text-align: center;
    }

    .trade-dropdown {
      position: relative;
    }

    .trade-dropdown__button {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      min-height: 48px;
      padding: 0 18px;
      color: #757575;
      background: #ffffff;
      border: 1px solid #e4e4e4;
      border-radius: 5px;
      text-align: left;
    }

    .trade-dropdown__button::after {
      content: "\f107";
      font-family: "Font Awesome 5 Pro", "Font Awesome 5 Free";
      font-weight: 900;
      color: #202020;
    }

    .trade-dropdown__menu {
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
      border: 1px solid #e4e4e4;
      border-radius: 5px;
      box-shadow: 0 14px 35px rgba(0, 0, 0, 0.12);
    }

    .trade-dropdown.is-open .trade-dropdown__menu {
      display: block;
    }

    .trade-dropdown__option {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0;
      padding: 9px 10px;
      color: #202020;
      cursor: pointer;
    }

    .trade-dropdown__option:hover {
      background: #f6f7f8;
    }

    .trade-dropdown__option input {
      width: 16px;
      height: 16px;
      flex: 0 0 auto;
    }

  </style>
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => !empty($pageHeading) ? $pageHeading->vendor_signup_page_title : __('Sign up'),
  ])
  <!-- Authentication-area start -->
  <div class="authentication-area bg-light ptb-100">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="main-form">
            <div class="main-form-wrapper">
              <h3 class="title mb-30 text-center">
                {{ !empty($pageHeading) ? $pageHeading->vendor_signup_page_title : __('Sign up') }}
              </h3>
              <div class="account-type-switch" aria-label="{{ __('Choose account type') }}">
                <a href="{{ route('user.signup') }}">{{ __('Customer') }}</a>
                <span class="active">{{ __('Business') }}</span>
              </div>
              <p class="account-type-help">
                {{ __('Choose Customer to book services, or Business if you provide services on 11Done.') }}
              </p>
              @if (Session::has('success'))
                <div class="alert alert-success">{{ __(Session::get('success')) }}</div>
              @endif
              <form action="{{ route('vendor.signup_submit') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="userName" class="form-label color-dark">{{ __('Username') }}<span
                          class="color-red">*</span></label>
                      <input type="text" name="username" value="{{ old('username') }}" id="userName"
                        class="form-control" placeholder="{{ __('Username') }}">
                      @error('username')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="name" class="form-label color-dark">{{ __('Full Name / Business Name') }}<span
                          class="color-red">*</span></label>
                      <input type="text" name="name" value="{{ old('name') }}" id="name"
                        class="form-control" placeholder="{{ __('Full Name / Business Name') }}">
                      @error('name')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="email" class="form-label color-dark">{{ __('Email') }}<span
                          class="color-red">*</span></label>
                      <input type="email" value="{{ old('email') }}" name="email" id="email"
                        class="form-control" placeholder="{{ __('Email') }}">
                      @error('email')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="phone" class="form-label color-dark">{{ __('Contact Number') }}<span
                          class="color-red">*</span></label>
                      <input type="tel" value="{{ old('phone') }}" name="phone" id="phone"
                        class="form-control" placeholder="{{ __('Contact Number') }}">
                      @error('phone')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="postcode" class="form-label color-dark">{{ __('Postcode') }}<span
                          class="color-red">*</span></label>
                      <input type="text" value="{{ old('postcode', old('zip_code')) }}" name="postcode"
                        id="postcode" class="form-control" placeholder="{{ __('Postcode') }}">
                      @error('postcode')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="address" class="form-label color-dark">{{ __('Full Address') }}<span
                          class="color-red">*</span></label>
                      <textarea name="address" id="address" class="form-control" rows="3"
                        placeholder="{{ __('Enter full address') }}">{{ old('address') }}</textarea>
                      @error('address')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="businessType" class="form-label color-dark">{{ __('What type of business is it?') }}<span
                          class="color-red">*</span></label>
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
                    <div class="form-group mb-20">
                      <label class="form-label color-dark">{{ __('Service Type / Trade Category') }}<span
                          class="color-red">*</span></label>
                      <div class="trade-dropdown" id="tradeCategoryDropdown">
                        <button type="button" class="trade-dropdown__button" id="tradeCategoryButton">
                          <span id="tradeCategoryLabel">{{ __('Select trade categories') }}</span>
                        </button>
                        <div class="trade-dropdown__menu" aria-labelledby="tradeCategoryButton">
                        @foreach ($serviceCategories as $category)
                            <label class="trade-dropdown__option">
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
                    <div class="form-group mb-20">
                      <label for="details" class="form-label color-dark">{{ __('Details') }}</label>
                      <textarea name="details" id="details" class="form-control" rows="3"
                        placeholder="{{ __('e.g. I am a licensed electrician specializing in residential wiring and solar panel installation. I also offer emergency repair services.') }}">{{ old('details') }}</textarea>
                      @error('details')
                        <p class="text-danger mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="password" class="form-label color-dark">{{ __('Password') }}<span
                          class="color-red">*</span></label>
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
                  <div class="col-12">
                    <div class="form-group mb-20">
                      <label for="confirmPassword" class="form-label color-dark">{{ __('Confirm Password') }}<span
                          class="color-red">*</span></label>
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
                    <div class="form-group mb-20">
                      {!! NoCaptcha::renderJs() !!}
                      {!! NoCaptcha::display() !!}
                      @error('g-recaptcha-response')
                        <p class="mt-1 text-danger">{{ $message }}</p>
                      @enderror
                    </div>
                  @endif
                </div>
                <div class="text-center pt-10">
                  <button class="btn btn-lg btn-primary btn-gradient w-100" type="submit"
                    aria-label="Signup">{{ __('Signup') }}</button>
                </div>
              </form>
            </div>
            <div class="text-center mt-20">
              <div class="link font-sm">
                {{ __('Already a member') . '?' }} <a href="{{ route('vendor.login') }}" target="_self"
                  title="Login Now">{{ __('Login Now') }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Authentication-area end -->
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
