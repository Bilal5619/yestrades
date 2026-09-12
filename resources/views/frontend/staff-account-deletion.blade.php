@extends('frontend.layout')

@section('pageHeading')
  {{ __('Yes Trades Staff – Account & Data Deletion') }}
@endsection

@section('metaDescription')
  {{ __('Request deletion of a Yes Trades Staff app account and associated personal data.') }}
@endsection

@section('content')
  <style>
    @include('frontend.partials.legal-page-styles')

    .staff-deletion-mail-button {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      margin: 10px 0 18px;
      padding: 12px 20px;
      color: #ffffff !important;
      background: #0a7d57;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
      transition: background 0.2s ease, transform 0.2s ease;
    }

    .staff-deletion-mail-button:hover {
      color: #ffffff !important;
      background: #0e9f6e;
      transform: translateY(-1px);
    }
  </style>

  <section class="lg-hero">
    <div class="lg-hero-dots"></div>
    <div class="container">
      <nav aria-label="breadcrumb" class="lg-breadcrumb">
        <a href="{{ route('index') }}">{{ __('Home') }}</a>
        <i class="fal fa-angle-right"></i>
        <span>{{ __('Staff account deletion') }}</span>
      </nav>
      <h1>{{ __('Yes Trades Staff – Account & Data Deletion') }}</h1>
      <p>{{ __('Request deletion of your Yes Trades Staff app account and associated personal data.') }}</p>
    </div>
  </section>

  <section class="lg-content-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <article class="legal-page-content" data-aos="fade-up">
            <h2>{{ __('Account and data deletion requests') }}</h2>
            <p>If you are a current or former Yes Trades employee and would like to request deletion of your Yes Trades Staff app account and associated personal data, please contact:</p>

            <p><strong><a href="mailto:info@yestrades.co.uk">info@yestrades.co.uk</a></strong></p>

            <a class="staff-deletion-mail-button" href="mailto:info@yestrades.co.uk?subject=Yes%20Trades%20Staff%20account%20deletion%20request">
              <i class="fal fa-envelope"></i>
              {{ __('Contact Yes Trades') }}
            </a>

            <p>{{ __('Please include:') }}</p>
            <ul>
              <li>{{ __('Your full name') }}</li>
              <li>{{ __('Employee number') }}</li>
              <li>{{ __('Email address used for the staff app') }}</li>
            </ul>

            <p>{{ __('We will verify your identity before processing the request.') }}</p>

            <p>{{ __('Some employment, payroll, tax, attendance, health and safety, expense, or compliance records may need to be retained where required by law or for legitimate business obligations.') }}</p>

            <p>Any data that does not need to be retained will be deleted in accordance with our <a href="{{ route('privacy_policy') }}">privacy policy</a>.</p>
          </article>
        </div>
      </div>
    </div>
  </section>
@endsection