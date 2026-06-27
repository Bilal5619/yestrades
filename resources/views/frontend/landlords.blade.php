@extends('frontend.layout')

@section('pageHeading')
  {{ __('Landlords') }}
@endsection

@section('metaKeywords')
  {{ __('landlord gas safety certificate, eicr certificate, landlord repairs, end of tenancy cleaning, property services') }}
@endsection

@section('metaDescription')
  {{ __('Book landlord certificates, compliance checks, repairs and cleaning with vetted UK engineers.') }}
@endsection

@section('style')
  <style>
    .landlord-page {
      background: #f8f8f7;
    }

    .landlord-hero {
      padding: 150px 0 82px;
      color: #ffffff;
      background:
        radial-gradient(circle at 90% 10%, rgba(var(--color-primary-rgb), 0.42), transparent 25%),
        linear-gradient(135deg, #071122 0%, #0b1628 100%);
    }

    .landlord-hero-grid {
      display: grid;
      grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
      align-items: center;
      gap: 54px;
    }

    .landlord-eyebrow {
      display: inline-flex;
      margin-bottom: 18px;
      padding: 7px 12px;
      color: #cfe8dc;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .landlord-hero h1 {
      max-width: 690px;
      margin: 0 0 20px;
      color: #ffffff;
      font-size: 54px;
      line-height: 1.08;
      font-weight: 900;
      letter-spacing: 0;
    }

    .landlord-hero h1 span {
      color: var(--color-primary);
    }

    .landlord-hero p {
      max-width: 620px;
      margin: 0;
      color: #c8d0df;
      font-size: 16px;
      line-height: 1.75;
    }

    .landlord-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 28px;
    }

    .landlord-primary-btn,
    .landlord-secondary-btn {
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

    .landlord-primary-btn {
      color: #ffffff;
      background: var(--color-primary);
    }

    .landlord-primary-btn:hover {
      color: #ffffff;
    }

    .landlord-secondary-btn {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.16);
    }

    .landlord-secondary-btn:hover {
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.34);
    }

    .landlord-panel {
      padding: 24px;
      background: #ffffff;
      border-radius: 8px;
      box-shadow: 0 26px 70px rgba(0, 0, 0, 0.22);
    }

    .landlord-panel h2 {
      margin: 0 0 16px;
      color: #11131a;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .landlord-check-list {
      display: grid;
      gap: 12px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .landlord-check-list li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: #4f5663;
      font-size: 14px;
      line-height: 1.55;
    }

    .landlord-check-list i {
      margin-top: 3px;
      color: #12a875;
    }

    .landlord-section {
      padding: 82px 0;
    }

    .landlord-section-title {
      max-width: 680px;
      margin: 0 auto 42px;
      text-align: center;
    }

    .landlord-section-title h2 {
      margin: 0 0 12px;
      color: #10131a;
      font-size: 36px;
      line-height: 1.15;
      font-weight: 900;
      letter-spacing: 0;
    }

    .landlord-section-title p {
      margin: 0;
      color: #6c7280;
      line-height: 1.7;
    }

    .landlord-service-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .landlord-service-card {
      min-height: 100%;
      padding: 24px;
      background: #ffffff;
      border: 1px solid #ecebea;
      border-radius: 8px;
      box-shadow: 0 16px 38px rgba(17, 19, 26, 0.04);
    }

    .landlord-service-card .icon {
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

    .landlord-service-card h3 {
      margin: 0 0 10px;
      color: #151720;
      font-size: 18px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .landlord-service-card p {
      margin: 0 0 18px;
      color: #69707d;
      font-size: 14px;
      line-height: 1.7;
    }

    .landlord-service-card a {
      color: var(--color-primary);
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
    }

    .landlord-strip {
      padding: 34px;
      background: #ffffff;
      border: 1px solid #ecebea;
      border-radius: 8px;
    }

    .landlord-strip-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 22px;
    }

    .landlord-strip strong {
      display: block;
      color: #10131a;
      font-size: 24px;
      font-weight: 900;
    }

    .landlord-strip span {
      color: #6c7280;
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
    }

    @media (max-width: 991.98px) {
      .landlord-hero-grid,
      .landlord-service-grid,
      .landlord-strip-grid {
        grid-template-columns: 1fr;
      }

      .landlord-hero h1 {
        font-size: 40px;
      }
    }
  </style>
@endsection

@section('content')
  <main class="landlord-page">
    <section class="landlord-hero">
      <div class="container">
        <div class="landlord-hero-grid">
          <div>
            <span class="landlord-eyebrow">{{ __('For Landlords & Property Managers') }}</span>
            <h1>{{ __('Compliance, repairs and cleaning') }} <span>{{ __('without the chasing.') }}</span></h1>
            <p>
              {{ __('Book vetted engineers for rental property certificates, urgent repairs, boiler services and end-of-tenancy jobs. Fixed prices, instant booking and documents you can share with tenants or agents.') }}
            </p>
            <div class="landlord-actions">
              <a href="{{ route('frontend.services', ['service_title' => 'Landlord certificate']) }}" class="landlord-primary-btn">
                {{ __('Book landlord service') }} <i class="fal fa-long-arrow-right"></i>
              </a>
              <a href="{{ route('contact') }}" class="landlord-secondary-btn">
                {{ __('Talk to us') }}
              </a>
            </div>
          </div>

          <div class="landlord-panel">
            <h2>{{ __('Popular landlord jobs') }}</h2>
            <ul class="landlord-check-list">
              <li><i class="fal fa-check-circle"></i> {{ __('Gas Safety Certificate for rented homes') }}</li>
              <li><i class="fal fa-check-circle"></i> {{ __('EICR electrical inspection and report') }}</li>
              <li><i class="fal fa-check-circle"></i> {{ __('Boiler servicing and urgent repairs') }}</li>
              <li><i class="fal fa-check-circle"></i> {{ __('End-of-tenancy and deep cleaning') }}</li>
              <li><i class="fal fa-check-circle"></i> {{ __('Clear arrival windows and job confirmation') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="landlord-section">
      <div class="container">
        <div class="landlord-section-title">
          <h2>{{ __('Services for rental properties') }}</h2>
          <p>{{ __('Everything a landlord needs to keep properties safe, compliant and ready for tenants.') }}</p>
        </div>

        <div class="landlord-service-grid">
          <div class="landlord-service-card">
            <div class="icon"><i class="fal fa-fire"></i></div>
            <h3>{{ __('Gas Safety Certificate') }}</h3>
            <p>{{ __('Book Gas Safe engineers for annual CP12 checks and appliance inspections.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'Gas Safety Certificate']) }}">{{ __('Book certificate') }}</a>
          </div>

          <div class="landlord-service-card">
            <div class="icon"><i class="fal fa-bolt"></i></div>
            <h3>{{ __('EICR Certificate') }}</h3>
            <p>{{ __('Electrical condition reports for rental homes, HMOs and managed properties.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'EICR']) }}">{{ __('Book EICR') }}</a>
          </div>

          <div class="landlord-service-card">
            <div class="icon"><i class="fal fa-tools"></i></div>
            <h3>{{ __('Repairs & Maintenance') }}</h3>
            <p>{{ __('Plumbing, heating, electrical and general fixes with clear booking times.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'Repair']) }}">{{ __('Find repair') }}</a>
          </div>

          <div class="landlord-service-card">
            <div class="icon"><i class="fal fa-temperature-hot"></i></div>
            <h3>{{ __('Boiler Service') }}</h3>
            <p>{{ __('Annual boiler servicing to reduce breakdowns and keep tenants protected.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'Boiler service']) }}">{{ __('Book boiler service') }}</a>
          </div>

          <div class="landlord-service-card">
            <div class="icon"><i class="fal fa-broom"></i></div>
            <h3>{{ __('End-of-Tenancy Cleaning') }}</h3>
            <p>{{ __('Deep cleaning for move-outs, check-ins and property refreshes between tenants.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'End of tenancy cleaning']) }}">{{ __('Book cleaning') }}</a>
          </div>

          <div class="landlord-service-card">
            <div class="icon"><i class="fal fa-calendar-check"></i></div>
            <h3>{{ __('Multi-Property Help') }}</h3>
            <p>{{ __('Need several jobs across different addresses? Send us the list and we will help coordinate.') }}</p>
            <a href="{{ route('contact') }}">{{ __('Contact team') }}</a>
          </div>
        </div>
      </div>
    </section>

    <section class="landlord-section pt-0">
      <div class="container">
        <div class="landlord-strip">
          <div class="landlord-strip-grid">
            <div>
              <strong>{{ __('60s') }}</strong>
              <span>{{ __('Book online') }}</span>
            </div>
            <div>
              <strong>{{ __('4.9') }}</strong>
              <span>{{ __('Average rating') }}</span>
            </div>
            <div>
              <strong>{{ __('180+') }}</strong>
              <span>{{ __('Vetted pros') }}</span>
            </div>
            <div>
              <strong>{{ __('12mo') }}</strong>
              <span>{{ __('Guarantee') }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
