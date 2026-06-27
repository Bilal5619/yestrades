@extends('frontend.layout')

@section('pageHeading')
  {{ __('Careers') }}
@endsection

@section('metaKeywords')
  {{ __('yestrades careers, home service jobs, engineer jobs, trades jobs, operations jobs') }}
@endsection

@section('metaDescription')
  {{ __('Join yestrades and help customers book trusted home services across the UK.') }}
@endsection

@section('style')
  <style>
    .careers-page {
      background: #f7f8f5;
    }

    .careers-hero {
      padding: 150px 0 78px;
      color: #ffffff;
      background:
        radial-gradient(circle at 86% 16%, rgba(var(--color-primary-rgb), 0.34), transparent 26%),
        linear-gradient(135deg, #08131f 0%, #142231 100%);
    }

    .careers-hero-grid {
      display: grid;
      grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
      gap: 52px;
      align-items: center;
    }

    .careers-eyebrow {
      display: inline-flex;
      margin-bottom: 18px;
      padding: 7px 12px;
      color: #d4efe0;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.13);
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .careers-hero h1 {
      max-width: 720px;
      margin: 0 0 20px;
      color: #ffffff;
      font-size: 54px;
      line-height: 1.08;
      font-weight: 900;
      letter-spacing: 0;
    }

    .careers-hero h1 span {
      color: var(--color-primary);
    }

    .careers-hero p {
      max-width: 640px;
      margin: 0;
      color: #cbd4df;
      font-size: 16px;
      line-height: 1.75;
    }

    .careers-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 28px;
    }

    .careers-primary-btn,
    .careers-secondary-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      min-height: 46px;
      padding: 0 18px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
    }

    .careers-primary-btn {
      color: #ffffff;
      background: var(--color-primary);
    }

    .careers-primary-btn:hover {
      color: #ffffff;
    }

    .careers-secondary-btn {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.16);
    }

    .careers-secondary-btn:hover {
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.34);
    }

    .careers-panel {
      overflow: hidden;
      padding: 24px;
      background: #ffffff;
      border-radius: 8px;
      box-shadow: 0 26px 70px rgba(0, 0, 0, 0.22);
    }

    .careers-hero-photo {
      height: 245px;
      margin: -24px -24px 22px;
      background: #e9ece8;
    }

    .careers-hero-photo img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .careers-panel h2 {
      margin: 0 0 16px;
      color: #11131a;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .careers-check-list {
      display: grid;
      gap: 12px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .careers-check-list li {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      color: #4f5663;
      font-size: 14px;
      line-height: 1.55;
    }

    .careers-check-list i {
      margin-top: 3px;
      color: #12a875;
    }

    .careers-section {
      padding: 82px 0;
    }

    .careers-section-title {
      max-width: 700px;
      margin: 0 auto 42px;
      text-align: center;
    }

    .careers-section-title h2 {
      margin: 0 0 12px;
      color: #10131a;
      font-size: 36px;
      line-height: 1.15;
      font-weight: 900;
      letter-spacing: 0;
    }

    .careers-section-title p {
      margin: 0;
      color: #6c7280;
      line-height: 1.7;
    }

    .careers-card-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .careers-card,
    .careers-role-card {
      min-height: 100%;
      padding: 24px;
      background: #ffffff;
      border: 1px solid #ecebea;
      border-radius: 8px;
      box-shadow: 0 16px 38px rgba(17, 19, 26, 0.04);
    }

    .careers-card .icon,
    .careers-role-card .icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 42px;
      height: 42px;
      margin-bottom: 18px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.09);
      border-radius: 8px;
      font-size: 19px;
    }

    .careers-card h3,
    .careers-role-card h3 {
      margin: 0 0 10px;
      color: #151720;
      font-size: 18px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .careers-card p,
    .careers-role-card p {
      margin: 0;
      color: #69707d;
      font-size: 14px;
      line-height: 1.7;
    }

    .careers-role-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
    }

    .careers-role-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 16px 0 18px;
    }

    .careers-role-meta span {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 0 10px;
      color: #4d5664;
      background: #f4f5f2;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
    }

    .careers-role-card a {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--color-primary);
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
    }

    .careers-cta {
      padding: 36px;
      color: #ffffff;
      background: #101827;
      border-radius: 8px;
    }

    .careers-cta-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 28px;
      align-items: center;
    }

    .careers-cta h2 {
      margin: 0 0 10px;
      color: #ffffff;
      font-size: 28px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .careers-cta p {
      margin: 0;
      color: #c8d0df;
      line-height: 1.7;
    }

    @media (max-width: 991.98px) {
      .careers-hero-grid,
      .careers-card-grid,
      .careers-role-grid,
      .careers-cta-grid {
        grid-template-columns: 1fr;
      }

      .careers-hero h1 {
        font-size: 40px;
      }
    }

    @media (max-width: 575.98px) {
      .careers-hero {
        padding: 128px 0 58px;
      }

      .careers-hero h1 {
        font-size: 34px;
      }

      .careers-section {
        padding: 64px 0;
      }

      .careers-section-title h2 {
        font-size: 30px;
      }

      .careers-panel,
      .careers-card,
      .careers-role-card,
      .careers-cta {
        padding: 20px;
      }
    }
  </style>
@endsection

@section('content')
  <main class="careers-page">
    <section class="careers-hero">
      <div class="container">
        <div class="careers-hero-grid">
          <div>
            <span class="careers-eyebrow">{{ __('Careers at yestrades') }}</span>
            <h1>{{ __('Help make home services') }} <span>{{ __('simple to book.') }}</span></h1>
            <p>
              {{ __('We are building a practical, trusted way for customers, landlords and property teams to book vetted home-service professionals. Join the team shaping the tools, service standards and customer experience behind every booking.') }}
            </p>
            <div class="careers-actions">
              <a href="#current-openings" class="careers-primary-btn">
                {{ __('Current openings') }} <i class="fal fa-long-arrow-right"></i>
              </a>
              <a href="{{ route('vendor.signup') }}" class="careers-secondary-btn">
                {{ __('Join as a pro') }}
              </a>
            </div>
          </div>

          <div class="careers-panel">
            <div class="careers-hero-photo">
              <img src="{{ asset('assets/img/static/careers-hero.jpg') }}"
                alt="{{ __('yestrades service professional at work') }}">
            </div>
            <h2>{{ __('Who we are looking for') }}</h2>
            <ul class="careers-check-list">
              <li><i class="fal fa-check-circle"></i> {{ __('People who care about clear, reliable service for customers') }}</li>
              <li><i class="fal fa-check-circle"></i> {{ __('Qualified tradespeople and engineers who want better booking flow') }}</li>
              <li><i class="fal fa-check-circle"></i> {{ __('Operations, support and growth teammates who enjoy practical detail') }}</li>
              <li><i class="fal fa-check-circle"></i> {{ __('Problem solvers who communicate well and follow through') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="careers-section">
      <div class="container">
        <div class="careers-section-title">
          <h2>{{ __('Work that keeps homes moving') }}</h2>
          <p>{{ __('Our team sits between customers, landlords and service professionals, so every role is close to real problems and visible outcomes.') }}</p>
        </div>

        <div class="careers-card-grid">
          <div class="careers-card">
            <div class="icon"><i class="fal fa-headset"></i></div>
            <h3>{{ __('Customer Support') }}</h3>
            <p>{{ __('Help customers choose the right service, understand booking details and get quick answers when plans change.') }}</p>
          </div>

          <div class="careers-card">
            <div class="icon"><i class="fal fa-route"></i></div>
            <h3>{{ __('Operations') }}</h3>
            <p>{{ __('Coordinate service demand, improve booking processes and keep urgent jobs moving with less back-and-forth.') }}</p>
          </div>

          <div class="careers-card">
            <div class="icon"><i class="fal fa-tools"></i></div>
            <h3>{{ __('Trade Professionals') }}</h3>
            <p>{{ __('Plumbers, gas engineers, electricians, cleaners and property specialists can join the yestrades network.') }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="careers-section pt-0" id="current-openings">
      <div class="container">
        <div class="careers-section-title">
          <h2>{{ __('No open roles available right now') }}</h2>
          <p>{{ __('We are not actively hiring for internal roles at the moment. When new opportunities become available, we will update this page with the role details and how to apply.') }}</p>
        </div>

        <div class="careers-role-grid">
          <div class="careers-role-card">
            <div class="icon"><i class="fal fa-user-hard-hat"></i></div>
            <h3>{{ __('Service professional applications') }}</h3>
            <p>{{ __('Qualified plumbers, gas engineers, electricians, cleaners and property maintenance specialists can still register interest in joining the yestrades professional network.') }}</p>
            <div class="careers-role-meta">
              <span>{{ __('UK based') }}</span>
              <span>{{ __('Flexible') }}</span>
              <span>{{ __('Pro network') }}</span>
            </div>
            <a href="{{ route('vendor.signup') }}">{{ __('Start pro signup') }} <i class="fal fa-long-arrow-right"></i></a>
          </div>

          <div class="careers-role-card">
            <div class="icon"><i class="fal fa-envelope-open-text"></i></div>
            <h3>{{ __('Future career interest') }}</h3>
            <p>{{ __('If you would like us to keep your details for future roles, send a short message with your experience and the type of work you are interested in.') }}</p>
            <div class="careers-role-meta">
              <span>{{ __('Not hiring now') }}</span>
              <span>{{ __('Future roles') }}</span>
              <span>{{ __('General interest') }}</span>
            </div>
            <a href="{{ route('contact') }}">{{ __('Send your details') }} <i class="fal fa-long-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </section>

    <section class="careers-section pt-0">
      <div class="container">
        <div class="careers-cta">
          <div class="careers-cta-grid">
            <div>
              <h2>{{ __('Do not see the right role?') }}</h2>
              <p>{{ __('Tell us where you could help. We are especially interested in people with home-services, property management, support or marketplace operations experience.') }}</p>
            </div>
            <a href="{{ route('contact') }}" class="careers-primary-btn">
              {{ __('Get in touch') }} <i class="fal fa-long-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
