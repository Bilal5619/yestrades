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

@section('content')
  <main class="ld-page">

    <!-- ══════════ Hero start ══════════ -->
    <section class="ld-hero">
      <div class="ld-hero-blob ld-hero-blob-1"></div>
      <div class="ld-hero-blob ld-hero-blob-2"></div>
      <div class="ld-hero-dots"></div>
      <div class="container">
        <div class="ld-hero-grid">
          <div class="ld-hero-copy" data-aos="fade-up" data-aos-delay="100">
            <span class="ld-eyebrow">
              <span class="ld-eyebrow-dot"></span>{{ __('For Landlords & Property Managers') }}
            </span>
            <h1>{{ __('Compliance, repairs and cleaning') }} <span>{{ __('without the chasing.') }}</span></h1>
            <p>
              {{ __('Book vetted engineers for rental property certificates, urgent repairs, boiler services and end-of-tenancy jobs. Fixed prices, instant booking and documents you can share with tenants or agents.') }}
            </p>
            <div class="ld-hero-actions">
              <a href="{{ route('frontend.services', ['service_title' => 'Landlord certificate']) }}" class="ld-btn-primary">
                {{ __('Book landlord service') }} <i class="fal fa-long-arrow-right"></i>
              </a>
              <a href="{{ route('contact') }}" class="ld-btn-ghost">
                <i class="fal fa-comments"></i>{{ __('Talk to us') }}
              </a>
            </div>
            <div class="ld-hero-trust">
              <span><i class="fal fa-shield-check"></i>{{ __('Gas Safe & NICEIC engineers') }}</span>
              <span><i class="fal fa-file-certificate"></i>{{ __('Shareable digital reports') }}</span>
              <span><i class="fal fa-clock"></i>{{ __('Same-day available') }}</span>
            </div>
          </div>

          <div class="ld-hero-panel" data-aos="fade-up" data-aos-delay="150">
            <div class="ld-panel-card">
              <div class="ld-panel-head">
                <span class="ld-panel-icon"><i class="fal fa-clipboard-list-check"></i></span>
                <h2>{{ __('Popular landlord jobs') }}</h2>
              </div>
              <ul class="ld-check-list">
                <li><i class="fal fa-check-circle"></i> {{ __('Gas Safety Certificate for rented homes') }}</li>
                <li><i class="fal fa-check-circle"></i> {{ __('EICR electrical inspection and report') }}</li>
                <li><i class="fal fa-check-circle"></i> {{ __('Boiler servicing and urgent repairs') }}</li>
                <li><i class="fal fa-check-circle"></i> {{ __('End-of-tenancy and deep cleaning') }}</li>
                <li><i class="fal fa-check-circle"></i> {{ __('Clear arrival windows and job confirmation') }}</li>
              </ul>
            </div>
            <div class="ld-panel-float">
              <strong>4.9<i class="fas fa-star"></i></strong>
              <span>{{ __('Landlord satisfaction') }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="ld-statbar-wrap">
      <div class="container">
        <div class="ld-statbar" data-aos="fade-up">
          <div class="ld-stat">
            <span class="ld-stat-icon"><i class="fal fa-bolt"></i></span>
            <div>
              <strong>{{ __('60s') }}</strong>
              <span class="ld-stat-label">{{ __('Book online') }}</span>
            </div>
          </div>
          <div class="ld-stat">
            <span class="ld-stat-icon"><i class="fas fa-star"></i></span>
            <div>
              <strong>{{ __('4.9') }}</strong>
              <span class="ld-stat-label">{{ __('Average rating') }}</span>
            </div>
          </div>
          <div class="ld-stat">
            <span class="ld-stat-icon"><i class="fal fa-user-hard-hat"></i></span>
            <div>
              <strong>{{ __('180+') }}</strong>
              <span class="ld-stat-label">{{ __('Vetted pros') }}</span>
            </div>
          </div>
          <div class="ld-stat">
            <span class="ld-stat-icon"><i class="fal fa-shield-check"></i></span>
            <div>
              <strong>{{ __('12mo') }}</strong>
              <span class="ld-stat-label">{{ __('Guarantee') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ══════════ Hero end ══════════ -->

    <!-- ══════════ Services grid start ══════════ -->
    <section class="ld-section ld-services-section">
      <div class="container">
        <div class="ld-section-head" data-aos="fade-up">
          <span class="ld-tag"><i class="fal fa-house-chimney"></i>{{ __('Rental property services') }}</span>
          <h2>{{ __('Services for rental properties') }}</h2>
          <p>{{ __('Everything a landlord needs to keep properties safe, compliant and ready for tenants.') }}</p>
        </div>

        <div class="ld-service-grid">
          <div class="ld-service-card" data-aos="fade-up" data-aos-delay="0">
            <div class="ld-service-icon ld-tint-1"><i class="fal fa-fire"></i></div>
            <h3>{{ __('Gas Safety Certificate') }}</h3>
            <p>{{ __('Book Gas Safe engineers for annual CP12 checks and appliance inspections.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'Gas Safety Certificate']) }}">
              {{ __('Book certificate') }} <i class="fal fa-arrow-right"></i>
            </a>
          </div>

          <div class="ld-service-card" data-aos="fade-up" data-aos-delay="80">
            <div class="ld-service-icon ld-tint-2"><i class="fal fa-bolt"></i></div>
            <h3>{{ __('EICR Certificate') }}</h3>
            <p>{{ __('Electrical condition reports for rental homes, HMOs and managed properties.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'EICR']) }}">
              {{ __('Book EICR') }} <i class="fal fa-arrow-right"></i>
            </a>
          </div>

          <div class="ld-service-card" data-aos="fade-up" data-aos-delay="160">
            <div class="ld-service-icon ld-tint-3"><i class="fal fa-tools"></i></div>
            <h3>{{ __('Repairs & Maintenance') }}</h3>
            <p>{{ __('Plumbing, heating, electrical and general fixes with clear booking times.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'Repair']) }}">
              {{ __('Find repair') }} <i class="fal fa-arrow-right"></i>
            </a>
          </div>

          <div class="ld-service-card" data-aos="fade-up" data-aos-delay="0">
            <div class="ld-service-icon ld-tint-1"><i class="fal fa-temperature-hot"></i></div>
            <h3>{{ __('Boiler Service') }}</h3>
            <p>{{ __('Annual boiler servicing to reduce breakdowns and keep tenants protected.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'Boiler service']) }}">
              {{ __('Book boiler service') }} <i class="fal fa-arrow-right"></i>
            </a>
          </div>

          <div class="ld-service-card" data-aos="fade-up" data-aos-delay="80">
            <div class="ld-service-icon ld-tint-2"><i class="fal fa-broom"></i></div>
            <h3>{{ __('End-of-Tenancy Cleaning') }}</h3>
            <p>{{ __('Deep cleaning for move-outs, check-ins and property refreshes between tenants.') }}</p>
            <a href="{{ route('frontend.services', ['service_title' => 'End of tenancy cleaning']) }}">
              {{ __('Book cleaning') }} <i class="fal fa-arrow-right"></i>
            </a>
          </div>

          <div class="ld-service-card" data-aos="fade-up" data-aos-delay="160">
            <div class="ld-service-icon ld-tint-3"><i class="fal fa-calendar-check"></i></div>
            <h3>{{ __('Multi-Property Help') }}</h3>
            <p>{{ __('Need several jobs across different addresses? Send us the list and we will help coordinate.') }}</p>
            <a href="{{ route('contact') }}">
              {{ __('Contact team') }} <i class="fal fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </section>
    <!-- ══════════ Services grid end ══════════ -->

    <!-- ══════════ How it works start ══════════ -->
    <section class="ld-section ld-flow-section">
      <div class="container">
        <div class="ld-section-head" data-aos="fade-up">
          <span class="ld-tag"><i class="fal fa-route"></i>{{ __('Simple process') }}</span>
          <h2>{{ __('From request to certificate in three steps') }}</h2>
        </div>

        <div class="ld-flow-line">
          <div class="ld-flow-step" data-aos="fade-up" data-aos-delay="100">
            <div class="ld-flow-node"><i class="fal fa-file-edit"></i><em>1</em></div>
            <h4>{{ __('Tell us the job') }}</h4>
            <p>{{ __('Pick a certificate, repair or cleaning service and share the property address.') }}</p>
          </div>
          <div class="ld-flow-step" data-aos="fade-up" data-aos-delay="200">
            <div class="ld-flow-node"><i class="fal fa-user-hard-hat"></i><em>2</em></div>
            <h4>{{ __('We assign a vetted engineer') }}</h4>
            <p>{{ __('A Gas Safe or NICEIC registered pro is matched and confirmed with an arrival window.') }}</p>
          </div>
          <div class="ld-flow-step" data-aos="fade-up" data-aos-delay="300">
            <div class="ld-flow-node"><i class="fal fa-file-certificate"></i><em>3</em></div>
            <h4>{{ __('Get your report') }}</h4>
            <p>{{ __('Receive a digital certificate or job summary you can forward straight to tenants or agents.') }}</p>
          </div>
        </div>
      </div>
    </section>
    <!-- ══════════ How it works end ══════════ -->

    <!-- ══════════ Why landlords choose us start ══════════ -->
    <section class="ld-section ld-why-section">
      <div class="container">
        <div class="ld-why-grid">
          <div class="ld-why-intro" data-aos="fade-up">
            <span class="ld-tag"><i class="fal fa-badge-check"></i>{{ __('Built for landlords') }}</span>
            <h2>{{ __('Fewer calls, less chasing, properties that stay compliant.') }}</h2>
            <p>{{ $websiteInfo->website_title ?? config('app.name') }} {{ __('handles the coordination so you can focus on your portfolio, not your inbox.') }}</p>
            <a href="{{ route('frontend.services') }}" class="ld-btn-primary">
              {{ __('Browse all services') }} <i class="fal fa-long-arrow-right"></i>
            </a>
          </div>
          <div class="ld-why-list">
            <div class="ld-why-row" data-aos="fade-up">
              <div class="ld-why-icon ld-tint-1"><i class="fal fa-calendar-check"></i></div>
              <div>
                <h4>{{ __('Renewal reminders') }}</h4>
                <p>{{ __('Never miss a gas or electrical renewal date across your properties again.') }}</p>
              </div>
            </div>
            <div class="ld-why-row" data-aos="fade-up" data-aos-delay="100">
              <div class="ld-why-icon ld-tint-2"><i class="fal fa-file-invoice-dollar"></i></div>
              <div>
                <h4>{{ __('One invoice, fixed price') }}</h4>
                <p>{{ __('Transparent pricing agreed upfront — no surprise call-out fees.') }}</p>
              </div>
            </div>
            <div class="ld-why-row" data-aos="fade-up" data-aos-delay="200">
              <div class="ld-why-icon ld-tint-3"><i class="fal fa-comments-alt"></i></div>
              <div>
                <h4>{{ __('Direct updates') }}</h4>
                <p>{{ __('Track bookings and get notified the moment a job is completed.') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ══════════ Why landlords choose us end ══════════ -->

    <!-- ══════════ CTA start ══════════ -->
    <section class="ld-cta-section">
      <div class="container">
        <div class="ld-cta-panel" data-aos="fade-up">
          <div class="ld-cta-inner">
            <h2>{{ __('Managing multiple properties?') }}</h2>
            <p>{{ __('Send us your list of jobs across different addresses and we will help coordinate every booking.') }}</p>
            <div class="ld-cta-actions">
              <a href="{{ route('frontend.services') }}" class="ld-btn-light">
                {{ __('Book a service now') }} <i class="fal fa-long-arrow-right"></i>
              </a>
              <a href="{{ route('contact') }}" class="ld-btn-outline-light">
                <i class="fal fa-comments"></i>{{ __('Talk to us') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ══════════ CTA end ══════════ -->

  </main>

  <style>
    /* ═══════════════ Landlords page — emerald & slate ═══════════════ */

    .ld-page {
      --ld-primary: #0e9f6e;
      --ld-primary-dark: #0a7d57;
      --ld-primary-deep: #06432f;
      --ld-primary-soft: #e5f5ee;
      --ld-amber: #f5a524;
      --ld-ink: #101d2c;
      --ld-ink-2: #33415c;
      --ld-muted: #5e6b7e;
      --ld-line: #e4e9e6;
      --ld-bg: #f4f7f5;
      --ld-radius: 20px;
      --ld-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
      --ld-shadow-lg: 0 28px 64px rgba(16, 29, 44, 0.13);
      background: #ffffff;
    }

    /* ── Hero ─────────────────────────────────────── */
    .ld-hero {
      position: relative;
      overflow: hidden;
      margin-top: 84px;
      padding: 88px 0 96px;
      background:
        radial-gradient(circle at 92% 4%, rgba(14, 159, 110, 0.42), transparent 38%),
        radial-gradient(circle at 4% 100%, rgba(245, 165, 36, 0.14), transparent 32%),
        linear-gradient(135deg, #071122 0%, #06301f 100%);
      color: #ffffff;
    }

    .ld-hero-blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(85px);
      pointer-events: none;
    }

    .ld-hero-blob-1 {
      top: -150px;
      right: -110px;
      width: 480px;
      height: 480px;
      background: rgba(14, 159, 110, 0.3);
    }

    .ld-hero-blob-2 {
      bottom: -180px;
      left: -130px;
      width: 440px;
      height: 440px;
      background: rgba(245, 165, 36, 0.12);
    }

    .ld-hero-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1.5px, transparent 1.5px);
      background-size: 24px 24px;
      mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      pointer-events: none;
    }

    .ld-hero .container {
      position: relative;
      z-index: 1;
    }

    .ld-hero-grid {
      display: grid;
      grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
      align-items: center;
      gap: 60px;
    }

    .ld-hero-copy {
      max-width: 640px;
    }

    .ld-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 22px;
      padding: 8px 15px;
      color: #dbeee3;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 0.05em;
      line-height: 1.3;
      text-transform: uppercase;
    }

    .ld-eyebrow-dot {
      width: 8px;
      height: 8px;
      background: #22d98b;
      border-radius: 50%;
      box-shadow: 0 0 0 4px rgba(34, 217, 139, 0.18);
    }

    .ld-hero h1 {
      max-width: 690px;
      margin: 0 0 20px;
      color: #ffffff;
      font-size: clamp(34px, 4vw, 52px);
      line-height: 1.1;
      font-weight: 900;
      letter-spacing: -0.015em;
    }

    .ld-hero h1 span {
      display: block;
      color: #7fe0b9;
    }

    .ld-hero-copy > p {
      max-width: 600px;
      margin: 0;
      color: #b7c4d6;
      font-size: 15.5px;
      line-height: 1.75;
    }

    .ld-hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 30px;
    }

    .ld-btn-primary,
    .ld-btn-ghost {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      min-height: 48px;
      padding: 0 22px;
      border-radius: 999px;
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease, border-color 0.2s ease;
    }

    .ld-btn-primary {
      color: #ffffff;
      background: linear-gradient(120deg, var(--ld-primary), var(--ld-primary-dark));
      box-shadow: 0 14px 30px rgba(14, 159, 110, 0.32);
    }

    .ld-btn-primary:hover {
      color: #ffffff;
      filter: brightness(1.07);
      transform: translateY(-2px);
      box-shadow: 0 18px 38px rgba(14, 159, 110, 0.42);
    }

    .ld-btn-ghost {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .ld-btn-ghost:hover {
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.36);
      background: rgba(255, 255, 255, 0.13);
    }

    .ld-hero-trust {
      display: flex;
      flex-wrap: wrap;
      gap: 12px 24px;
      margin-top: 28px;
      color: #aebdcf;
      font-size: 13px;
      font-weight: 600;
    }

    .ld-hero-trust i {
      margin-right: 7px;
      color: #22d98b;
    }

    /* ── Hero side panel ──────────────────────────── */
    .ld-hero-panel {
      position: relative;
    }

    .ld-panel-card {
      padding: 26px;
      background: #ffffff;
      border-radius: var(--ld-radius);
      box-shadow: 0 34px 80px rgba(0, 0, 0, 0.35);
    }

    .ld-panel-head {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
    }

    .ld-panel-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 42px;
      width: 42px;
      height: 42px;
      color: var(--ld-primary-dark);
      background: var(--ld-primary-soft);
      border-radius: 12px;
      font-size: 18px;
    }

    .ld-panel-head h2 {
      margin: 0;
      color: var(--ld-ink);
      font-size: 19px;
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    .ld-check-list {
      display: grid;
      gap: 12px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .ld-check-list li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: var(--ld-ink-2);
      font-size: 13.5px;
      font-weight: 600;
      line-height: 1.55;
    }

    .ld-check-list i {
      margin-top: 3px;
      color: var(--ld-primary);
    }

    .ld-panel-float {
      position: absolute;
      right: -16px;
      bottom: -22px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 2px;
      padding: 14px 18px;
      background: linear-gradient(120deg, #ffc253, var(--ld-amber));
      border-radius: 16px;
      box-shadow: 0 20px 44px rgba(245, 165, 36, 0.4);
    }

    .ld-panel-float strong {
      display: flex;
      align-items: center;
      gap: 6px;
      color: #231303;
      font-size: 20px;
      font-weight: 900;
      line-height: 1;
    }

    .ld-panel-float strong i {
      font-size: 13px;
    }

    .ld-panel-float span {
      color: #4a2c04;
      font-size: 10.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    /* ── Stat bar ─────────────────────────────────── */
    .ld-statbar-wrap {
      position: relative;
      z-index: 2;
      margin-top: -46px;
      margin-bottom: 36px;
    }

    .ld-statbar {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      background: #ffffff;
      border-radius: var(--ld-radius);
      box-shadow: var(--ld-shadow-lg);
      overflow: hidden;
    }

    .ld-stat {
      display: flex;
      align-items: center;
      gap: 14px;
      position: relative;
      padding: 24px 22px;
    }

    .ld-stat + .ld-stat::before {
      content: "";
      position: absolute;
      left: 0;
      top: 20%;
      height: 60%;
      width: 1px;
      background: var(--ld-line);
    }

    .ld-stat-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 44px;
      width: 44px;
      height: 44px;
      color: var(--ld-primary-dark);
      background: var(--ld-primary-soft);
      border-radius: 12px;
      font-size: 17px;
    }

    .ld-stat strong {
      display: block;
      color: var(--ld-ink);
      font-size: 22px;
      line-height: 1.1;
      font-weight: 900;
    }

    .ld-stat-label {
      display: block;
      margin-top: 2px;
      color: var(--ld-muted);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    /* ── Shared section styling ───────────────────── */
    .ld-section {
      padding: 84px 0;
    }

    .ld-section-head {
      max-width: 720px;
      margin: 0 auto 46px;
      text-align: center;
    }

    .ld-tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 16px;
      padding: 8px 15px;
      color: var(--ld-primary-dark);
      background: var(--ld-primary-soft);
      border-radius: 999px;
      font-size: 11.5px;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .ld-section-head h2 {
      margin: 0 0 12px;
      color: var(--ld-ink);
      font-size: clamp(27px, 3.1vw, 38px);
      line-height: 1.16;
      font-weight: 900;
      letter-spacing: -0.015em;
    }

    .ld-section-head p {
      margin: 0;
      color: var(--ld-muted);
      font-size: 15px;
      line-height: 1.7;
    }

    .ld-btn-primary,
    .ld-why-intro .ld-btn-primary {
      margin-top: 6px;
    }

    /* ── Service cards ─────────────────────────────── */
    .ld-services-section {
      background: #ffffff;
    }

    .ld-service-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 20px;
    }

    .ld-service-card {
      min-height: 100%;
      padding: 26px;
      background: #ffffff;
      border: 1px solid var(--ld-line);
      border-radius: var(--ld-radius);
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .ld-service-card:hover {
      transform: translateY(-6px);
      border-color: transparent;
      box-shadow: var(--ld-shadow-lg);
    }

    .ld-service-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      margin-bottom: 20px;
      border-radius: 14px;
      font-size: 20px;
    }

    .ld-tint-1 {
      color: var(--ld-primary-dark);
      background: var(--ld-primary-soft);
    }

    .ld-tint-2 {
      color: #b06e07;
      background: rgba(245, 165, 36, 0.14);
    }

    .ld-tint-3 {
      color: #2f80ed;
      background: rgba(47, 128, 237, 0.11);
    }

    .ld-service-card h3 {
      margin: 0 0 10px;
      color: var(--ld-ink);
      font-size: 17.5px;
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    .ld-service-card p {
      margin: 0 0 18px;
      color: var(--ld-muted);
      font-size: 13.5px;
      line-height: 1.7;
    }

    .ld-service-card a {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--ld-primary-dark);
      font-size: 13.5px;
      font-weight: 800;
      text-decoration: none;
      transition: gap 0.2s ease, color 0.2s ease;
    }

    .ld-service-card a:hover {
      color: var(--ld-primary);
      gap: 12px;
    }

    /* ── How it works (flow) ──────────────────────── */
    .ld-flow-section {
      background: var(--ld-bg);
    }

    .ld-flow-line {
      position: relative;
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 28px;
    }

    .ld-flow-line::before {
      content: "";
      position: absolute;
      top: 40px;
      left: 14%;
      right: 14%;
      border-top: 2px dashed rgba(14, 159, 110, 0.35);
      z-index: 0;
    }

    .ld-flow-step {
      position: relative;
      z-index: 1;
      text-align: center;
      padding: 0 10px;
    }

    .ld-flow-node {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 80px;
      height: 80px;
      margin: 0 auto 22px;
      color: #ffffff;
      background: linear-gradient(135deg, var(--ld-primary), var(--ld-primary-dark));
      border-radius: 50%;
      font-size: 27px;
      box-shadow: 0 16px 34px rgba(14, 159, 110, 0.34);
    }

    .ld-flow-node em {
      position: absolute;
      top: -6px;
      right: -6px;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 27px;
      height: 27px;
      color: #231303;
      background: var(--ld-amber);
      border-radius: 50%;
      font-size: 12.5px;
      font-style: normal;
      font-weight: 900;
      box-shadow: 0 6px 14px rgba(245, 165, 36, 0.5);
    }

    .ld-flow-step h4 {
      margin: 0 0 10px;
      color: var(--ld-ink);
      font-size: 17px;
      font-weight: 800;
    }

    .ld-flow-step p {
      margin: 0;
      max-width: 280px;
      margin-inline: auto;
      color: var(--ld-muted);
      font-size: 13.5px;
      line-height: 1.65;
    }

    /* ── Why landlords choose us ───────────────────── */
    .ld-why-section {
      background: #ffffff;
    }

    .ld-why-grid {
      display: grid;
      grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
      gap: 64px;
      align-items: center;
    }

    .ld-why-intro {
      text-align: left;
    }

    .ld-why-intro h2 {
      margin: 0 0 16px;
      color: var(--ld-ink);
      font-size: clamp(27px, 3vw, 36px);
      line-height: 1.18;
      font-weight: 900;
      letter-spacing: -0.015em;
    }

    .ld-why-intro > p {
      margin: 0 0 26px;
      color: var(--ld-muted);
      font-size: 15px;
      line-height: 1.75;
    }

    .ld-why-list {
      display: flex;
      flex-direction: column;
    }

    .ld-why-row {
      display: flex;
      gap: 20px;
      padding: 24px;
      background: #ffffff;
      border-radius: var(--ld-radius);
      box-shadow: var(--ld-shadow);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .ld-why-row + .ld-why-row {
      margin-top: 16px;
    }

    .ld-why-row:hover {
      transform: translateX(8px);
      box-shadow: var(--ld-shadow-lg);
    }

    .ld-why-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 54px;
      width: 54px;
      height: 54px;
      border-radius: 16px;
      font-size: 21px;
    }

    .ld-why-row h4 {
      margin: 0 0 6px;
      color: var(--ld-ink);
      font-size: 16px;
      font-weight: 800;
    }

    .ld-why-row p {
      margin: 0;
      color: var(--ld-muted);
      font-size: 13.5px;
      line-height: 1.65;
    }

    /* ── CTA panel ────────────────────────────────── */
    .ld-cta-section {
      padding: 0 0 88px;
      background: #ffffff;
    }

    .ld-cta-panel {
      position: relative;
      overflow: hidden;
      border-radius: 26px;
      background-color: var(--ld-primary-deep);
    }

    .ld-cta-panel::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 88% 8%, rgba(245, 165, 36, 0.3), transparent 42%),
        linear-gradient(100deg, rgba(6, 67, 47, 0.95) 25%, rgba(7, 17, 34, 0.6));
    }

    .ld-cta-inner {
      position: relative;
      z-index: 1;
      max-width: 640px;
      margin: 0 auto;
      padding: 66px 40px;
      text-align: center;
    }

    .ld-cta-inner h2 {
      margin: 0 0 16px;
      color: #ffffff;
      font-size: clamp(26px, 3vw, 38px);
      font-weight: 900;
      line-height: 1.16;
      letter-spacing: -0.015em;
    }

    .ld-cta-inner p {
      margin: 0;
      color: #bcd8cb;
      font-size: 15px;
      line-height: 1.75;
    }

    .ld-cta-actions {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 30px;
    }

    .ld-btn-light,
    .ld-btn-outline-light {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      min-height: 48px;
      padding: 0 24px;
      border-radius: 999px;
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    }

    .ld-btn-light {
      color: var(--ld-primary-deep);
      background: #ffffff;
    }

    .ld-btn-light:hover {
      color: var(--ld-primary-deep);
      transform: translateY(-2px);
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
    }

    .ld-btn-outline-light {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.24);
    }

    .ld-btn-outline-light:hover {
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.44);
      background: rgba(255, 255, 255, 0.14);
    }

    /* ── Responsive ───────────────────────────────── */
    @media (max-width: 1199.98px) {
      .ld-hero-grid,
      .ld-why-grid {
        grid-template-columns: 1fr;
      }

      .ld-hero-copy,
      .ld-hero-panel {
        max-width: 100%;
      }

      .ld-hero-panel {
        max-width: 480px;
        margin: 0 auto;
      }

      .ld-why-intro {
        text-align: center;
      }

      .ld-why-intro .ld-btn-primary {
        margin-inline: auto;
      }
    }

    @media (max-width: 991.98px) {
      .ld-service-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .ld-flow-line {
        grid-template-columns: 1fr;
        gap: 34px;
      }

      .ld-flow-line::before {
        display: none;
      }
    }

    @media (max-width: 767.98px) {
      .ld-hero {
        padding: 64px 0 90px;
      }

      .ld-panel-float {
        right: 12px;
        bottom: -18px;
      }

      .ld-statbar-wrap {
        margin-top: -34px;
      }

      .ld-statbar {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .ld-stat {
        padding: 18px 14px;
      }

      .ld-stat:nth-child(3)::before {
        display: none;
      }

      .ld-stat:nth-child(n + 3) {
        border-top: 1px solid var(--ld-line);
      }

      .ld-stat-icon {
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        font-size: 15px;
      }

      .ld-stat strong {
        font-size: 18px;
      }

      .ld-section {
        padding: 58px 0;
      }

      .ld-section-head {
        margin-bottom: 32px;
      }

      .ld-service-grid {
        grid-template-columns: 1fr;
      }

      .ld-why-row {
        flex-direction: column;
        gap: 14px;
      }

      .ld-why-row:hover {
        transform: translateY(-4px);
      }

      .ld-cta-inner {
        padding: 44px 24px;
      }
    }

    @media (max-width: 575.98px) {
      .ld-hero-actions {
        flex-direction: column;
        align-items: stretch;
      }

      .ld-hero-trust {
        flex-direction: column;
        gap: 10px;
      }

      .ld-cta-actions {
        flex-direction: column;
        align-items: stretch;
      }
    }
  </style>
@endsection
