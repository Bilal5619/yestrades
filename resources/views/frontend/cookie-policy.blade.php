@extends('frontend.layout')

@php
  $siteInfo = $basicInfo ?? null;
  $businessEmail = $siteInfo->email_address ?? '[ADD BUSINESS EMAIL]';
  $businessPhone = $siteInfo->contact_number ?? '[ADD BUSINESS PHONE]';
@endphp

@section('pageHeading')
  {{ __('Cookie Policy') }}
@endsection

@section('metaDescription')
  {{ __('Cookie Policy for yestrades, explaining how cookies and similar technologies are used on the website.') }}
@endsection

@section('content')
  <style>
    @include('frontend.partials.legal-page-styles')
  </style>

  <section class="lg-hero">
    <div class="lg-hero-dots"></div>
    <div class="container">
      <nav aria-label="breadcrumb" class="lg-breadcrumb">
        <a href="{{ route('index') }}">{{ __('Home') }}</a>
        <i class="fal fa-angle-right"></i>
        <span>{{ __('Cookie Policy') }}</span>
      </nav>
      <h1>{{ __('Cookie Policy') }}</h1>
      <p>{{ __('How cookies and similar technologies are used on this website.') }}</p>
    </div>
  </section>

  <section class="lg-content-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <article class="legal-page-content" data-aos="fade-up">
            <span class="lg-updated-chip"><i class="fal fa-clock"></i>{{ __('Last updated:') }} 24 June 2026</span>

            <p>This Cookie Policy explains how yestrades ("we", "our", "us") uses cookies and similar technologies on our website.</p>
            <p><strong>Website:</strong> <a href="https://yestrades.co.uk">https://yestrades.co.uk</a></p>

            <h2>1. What are cookies?</h2>
            <p>Cookies are small text files placed on your device when you visit a website. They help websites work properly, remember user preferences, improve performance and understand how visitors use the website.</p>

            <h2>2. How we use cookies</h2>
            <p>We may use cookies to:</p>
            <ul>
              <li>Keep the website working correctly</li>
              <li>Remember your preferences</li>
              <li>Keep items in your service cart</li>
              <li>Support login and account security</li>
              <li>Improve website speed and performance</li>
              <li>Understand how visitors use our website</li>
              <li>Help protect the website from fraud, spam or misuse</li>
            </ul>

            <h2>3. Types of cookies we may use</h2>
            <h3>Essential cookies</h3>
            <p>These cookies are needed for the website to work. They may be used for security, login sessions, service cart, checkout flow and basic website functions. Without these cookies, some parts of the website may not work properly.</p>

            <h3>Performance and analytics cookies</h3>
            <p>These cookies help us understand how visitors use the website, which pages are visited, and where improvements may be needed. For example, we may use analytics tools to measure website traffic and performance.</p>

            <h3>Functionality cookies</h3>
            <p>These cookies help remember choices you make, such as preferences or previous form information, so the website can provide a better experience.</p>

            <h3>Marketing cookies</h3>
            <p>If used, these cookies may help us measure advertising performance or show relevant marketing. We will only use these where required consent has been given.</p>

            <h2>4. Third-party cookies</h2>
            <p>Some cookies may be set by third-party services used on our website, such as:</p>
            <ul>
              <li>Payment providers</li>
              <li>Analytics providers</li>
              <li>Security tools</li>
              <li>Embedded content or maps</li>
              <li>Communication tools such as WhatsApp links</li>
            </ul>
            <p>These third parties may process information according to their own privacy and cookie policies.</p>

            <h2>5. Managing cookies</h2>
            <p>You can control or delete cookies through your browser settings. Most browsers allow you to block cookies, delete cookies or receive warnings before cookies are placed.</p>
            <p>Please note that blocking essential cookies may affect how the website works, including login, cart and checkout features.</p>

            <h2>6. Cookie consent</h2>
            <p>Where required by law, we will ask for your consent before using non-essential cookies such as analytics or marketing cookies. You can withdraw or change your consent at any time where this option is available.</p>

            <h2>7. Changes to this Cookie Policy</h2>
            <p>We may update this Cookie Policy from time to time. The latest version will always be available on this page.</p>

            <h2>8. Contact us</h2>
            <p>If you have any questions about this Cookie Policy, please contact us:</p>
            <ul>
              <li>Email: {{ $businessEmail }}</li>
              <li>Phone: {{ $businessPhone }}</li>
            </ul>
          </article>
        </div>
      </div>
    </div>
  </section>
@endsection
