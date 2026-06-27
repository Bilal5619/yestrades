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

        .social-auth-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0 16px;
            color: #7a7f8d;
            font-size: 14px;
            font-weight: 700;
        }

        .social-auth-divider::before,
        .social-auth-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #ecebea;
        }

        .google-auth-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            min-height: 54px;
            padding: 0 18px;
            color: #2b2f3a;
            background: #ffffff;
            border: 1px solid #dfe3ea;
            border-radius: 8px;
            box-shadow: 0 10px 24px rgba(20, 24, 35, 0.08);
            font-size: 16px;
            font-weight: 800;
            text-decoration: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .google-auth-btn:hover,
        .google-auth-btn:focus {
            color: #111827;
            border-color: #c7ceda;
            box-shadow: 0 14px 28px rgba(20, 24, 35, 0.12);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .google-auth-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            color: #ffffff;
            background: #34a853;
            border-radius: 50%;
            font-size: 15px;
        }
    </style>
@endsection

@section('content')
    @includeIf('frontend.partials.breadcrumb', [
        'breadcrumb' => $bgImg->breadcrumb,
        'title' => !empty($pageHeading) ? $pageHeading->signup_page_title : __('Sign up'),
    ])
    <!-- Authentication-area start -->
    <div class="authentication-area bg-light ptb-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="main-form">
                        <div class="main-form-wrapper">
                            <h3 class="title mb-30 text-center">
                                {{ !empty($pageHeading) ? $pageHeading->signup_page_title : __('Sign up') }}
                            </h3>
                            <div class="account-type-switch" aria-label="{{ __('Choose account type') }}">
                                <span class="active">{{ __('Customer') }}</span>
                                <a href="{{ route('vendor.signup') }}">{{ __('Business') }}</a>
                            </div>
                            <p class="account-type-help">
                                {{ __('Choose Customer to book services, or Business if you provide services on 11Done.') }}
                            </p>
                            @if (Session::has('success'))
                                <div class="alert alert-success">{{ __(Session::get('success')) }}</div>
                            @endif
                            <form id="#authForm" action="{{ route('user.signup_submit') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mb-20">
                                            <label for="userName" class="form-label color-dark">{{ __('Full Name') }}<span
                                                    class="color-red">*</span></label>
                                            <input type="text" name="username" value="{{ old('username') }}"
                                                id="userName" class="form-control" placeholder="{{ __('Full Name') }}">
                                            @error('username')
                                                <p class="text-danger mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-20">
                                            <label for="email" class="form-label color-dark">{{ __('Email') }}<span
                                                    class="color-red">*</span></label>
                                            <input type="email" value="{{ old('email') }}" name="email" id="email"
                                                class="form-control" placeholder="{{ __('Enter Your Email Address') }}">
                                            @error('email')
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
                                                    placeholder="{{ __('Enter Password') }}">
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
                                            <label for="confirmPassword"
                                                class="form-label color-dark">{{ __('Confirm Password') }}
                                                <span class="color-red">*</span>
                                            </label>
                                            <div class="position-relative">
                                                <input type="password" name="password_confirmation" id="confirmPassword"
                                                    class="form-control" placeholder="{{ __('Confirm Password') }}">
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
                                        aria-label="Signup">
                                        {{ __('Signup') }}
                                    </button>
                                </div>
                                @if ($bs->facebook_login_status == 1 || $googleLoginEnabled)
                                    <div class="form-bottom">
                                        <div class="social-auth-divider">{{ __('Or Signup With') }}</div>
                                        <div class="btn-groups justify-content-center">
                                            @if ($bs->facebook_login_status == 1)
                                                <a class="btn-icon size-md rounded-circle facebook {{ !$googleLoginEnabled ? 'aa' : 'bb' }}"
                                                    href="{{ route('user.login.facebook') }}">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                            @endif

                                            @if ($googleLoginEnabled)
                                                <a class="google-auth-btn"
                                                    href="{{ route('user.login.google') }}">
                                                    <i class="fab fa-google"></i>
                                                    <span>{{ __('Continue with Google') }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                        <div class="text-center mt-20">
                            <div class="link font-sm">
                                {{ __('Already a member') . '?' }} <a href="{{ route('user.login') }}" target="_self"
                                    title="Login Now">
                                    {{ __('Login Now') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Authentication-area end -->
@endsection
