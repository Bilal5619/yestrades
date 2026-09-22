@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->about_us_title : __('About Us') }}
@endsection

@section('metaTitle')
  {{ __('About Yes Trades — Family-run trades in Livingston & Edinburgh since 2012') }} | {{ $websiteInfo->website_title ?? config('app.name') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keywords_about_page }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_about_page }}
  @endif
@endsection

@section('content')
  @php
    $ytxFounded = 2012;
    $ytxYears = max(1, (int) date('Y') - $ytxFounded);
    $ytxSiteName = $websiteInfo->website_title ?? config('app.name');
    $ytxPhone = $basicInfo->contact_number ?? null;
    $ytxPhoneHref = $ytxPhone ? 'tel:' . preg_replace('/\s+/', '', $ytxPhone) : null;
    $ytxGoogleUrl = 'https://www.google.co.uk/maps/place/YES+TRADES/@55.9015079,-3.545075,17z/data=!4m6!3m5!1s0x4887d93d19ebc8b9:0x4328e70bc2a3ea10!8m2!3d55.9015079!4d-3.545075!16s%2Fg%2F1q6kk8x89';
    $ytxGoogle = \App\Services\GoogleReviewsService::get();
    if (!empty($ytxGoogle['url'])) { $ytxGoogleUrl = $ytxGoogle['url']; }
    // Real Google reviews when connected, otherwise admin testimonials.
    $ytxAboutReviews = collect($ytxGoogle['reviews'] ?? [])->take(6)->map(fn ($r) => (object) [
      'name' => $r['name'], 'occupation' => $r['time'], 'rating' => (int) $r['rating'], 'comment' => $r['text'], 'photo' => $r['photo'] ?? null,
    ]);
    if ($ytxAboutReviews->isEmpty()) { $ytxAboutReviews = collect($testimonials ?? []); }

    $aboutImage = !empty($about->about_section_image) && file_exists(public_path('assets/img/' . $about->about_section_image))
      ? asset('assets/img/' . $about->about_section_image)
      : (file_exists(public_path('assets/img/yt/family.jpg')) ? asset('assets/img/yt/family.jpg') : null);

    $aboutEyebrow = !empty($about->title) ? $about->title : __('Our story');
    $aboutTitle = !empty($about->subtitle) ? $about->subtitle : __('A family business, built on doing the job properly');
    $aboutText = !empty($about->text)
      ? $about->text
      : '<p>' . __('Yes Trades has been run by the same family since') . ' ' . $ytxFounded . '. ' . __('We started as a small local team doing plumbing, gas and electrical work for neighbours in Livingston — and we still work the same way today: answer the phone, turn up when we say we will, do the job properly and stand behind it.') . '</p><p>' . __('Today we look after homeowners, landlords and property managers across Edinburgh, West Lothian and Central Scotland, covering everything from a dripping tap and annual gas safety certificates to full boiler installations and bathroom and kitchen renovations.') . '</p>';

    $ytxValues = collect($features ?? [])->filter(fn ($f) => !empty($f->title))->map(fn ($f) => [
      'icon' => $f->icon ?: 'fal fa-check-circle', 'title' => $f->title, 'text' => $f->text,
    ])->values();
    if ($ytxValues->isEmpty()) {
      $ytxValues = collect([
        ['icon' => 'fal fa-home-heart', 'title' => __('Family-run'), 'text' => __('Owner-led with our name on every job — no call centre, no lead-selling website in between.')],
        ['icon' => 'fal fa-user-hard-hat', 'title' => __('Our own engineers'), 'text' => __('The people who quote the work are the people who do it. Nothing is subcontracted to strangers.')],
        ['icon' => 'fal fa-badge-check', 'title' => __('Registered & insured'), 'text' => __('Gas Safe registered, NICEIC approved and fully insured, so every job is done to standard.')],
        ['icon' => 'fal fa-file-invoice', 'title' => __('Honest pricing'), 'text' => __('Clear prices online where shown, a written quote before bigger jobs, and no hidden extras.')],
        ['icon' => 'fal fa-shield-check', 'title' => __('Guaranteed work'), 'text' => __('Every job is covered by our workmanship guarantee. If something is not right, we put it right.')],
        ['icon' => 'fal fa-map-marker-alt', 'title' => __('Local, and here to stay'), 'text' => __('Based in Livingston and serving the same communities for over a decade.')],
      ]);
    }

    $ytxSteps = collect($processes ?? [])->filter(fn ($p) => !empty($p->title) && !empty($p->text))->values();
    if ($ytxSteps->isEmpty()) {
      $ytxSteps = collect([
        (object) ['title' => __('Tell us the job'), 'text' => __('Book online, call, or request a callback — it takes about a minute.')],
        (object) ['title' => __('Clear price up front'), 'text' => __('Fixed prices online where shown, or a written quote before any work starts.')],
        (object) ['title' => __('We do the work'), 'text' => __('A registered engineer from our own team turns up on time and gets it done properly.')],
        (object) ['title' => __('Certificate & guarantee'), 'text' => __('You get any certificates you need, and our workmanship guarantee.')],
      ]);
    }
    $ytxStepIcons = ['fa-comment-alt-lines', 'fa-file-invoice-dollar', 'fa-tools', 'fa-certificate'];

    $ytxCustomSection = function ($sections, $flags) use ($currentLanguageInfo) {
      $out = [];
      foreach ($sections ?? [] as $sec) {
        if (!empty($flags[$sec->id]) && $flags[$sec->id] == 1) {
          $content = App\Models\CustomSectionContent::where('custom_section_id', $sec->id)->where('language_id', $currentLanguageInfo->id)->first();
          if ($content) { $out[] = $content; }
        }
      }
      return $out;
    };
  @endphp

  <div class="ytx-page">

    {{-- Hero --}}
    <section class="ytx-page-hero ytx-about-hero">
      <div class="ytx-wrap">
        <nav class="ytx-breadcrumbs ytx-breadcrumbs-inline" aria-label="breadcrumb">
          <a href="{{ route('index') }}">{{ __('Home') }}</a> &gt; {{ !empty($pageHeading) ? $pageHeading->about_us_title : __('About Us') }}
        </nav>
        <div class="ytx-about-hero-grid">
          <div>
            <div class="ytx-eyebrow">{{ __('Family-run · Est.') }} {{ $ytxFounded }}</div>
            <h1>{{ __('Local trades,') }} <em>{{ __('family values') }}</em></h1>
            <p class="ytx-lead">{{ __('For over') }} {{ $ytxYears }} {{ __('years, one family has looked after homes and rental properties across Livingston, Edinburgh and West Lothian.') }}</p>
            <div class="ytx-hero-actions">
              <a class="ytx-btn" href="{{ route('contact') }}">{{ __('Get a free quote') }} →</a>
              @if ($ytxPhoneHref)
                <a class="ytx-btn ytx-outline" href="{{ $ytxPhoneHref }}"><i class="fal fa-phone"></i> {{ $ytxPhone }}</a>
              @endif
            </div>
          </div>
          <div class="ytx-about-hero-stats">
            <div><strong>{{ $ytxYears }}+</strong><span>{{ __('years in business') }}</span></div>
            <div><strong>{{ __('Gas Safe') }}</strong><span>{{ __('registered') }}</span></div>
            <div><strong>{{ __('NICEIC') }}</strong><span>{{ __('approved') }}</span></div>
            <div><strong>100%</strong><span>{{ __('own engineers') }}</span></div>
          </div>
        </div>
      </div>
    </section>

    {{-- Story --}}
    @if (($secInfo->about_section_status ?? 1) == 1)
      <section class="ytx-section ytx-about">
        <div class="ytx-wrap">
          <div class="ytx-about-grid">
            <div class="ytx-about-visual">
              @if ($aboutImage)
                <div class="ytx-about-photo">
                  <img src="{{ $aboutImage }}" alt="{{ $ytxSiteName }}">
                  <div class="ytx-family-badge"><strong>{{ $ytxYears }}+</strong><span>{{ __('years serving local homes') }}</span></div>
                </div>
              @else
                <div class="ytx-about-card">
                  <div class="ytx-about-est"><span>{{ __('Established') }}</span><strong>{{ $ytxFounded }}</strong></div>
                  <blockquote class="ytx-about-quote">
                    “{{ __('We are the people who answer the phone, turn up at your door and stand behind the work. That is how our family has always done it.') }}”
                    <footer>— {{ __('The Yes Trades family') }}</footer>
                  </blockquote>
                  <div class="ytx-about-stats">
                    <div><strong>{{ $ytxYears }}+</strong><span>{{ __('years') }}</span></div>
                    <div><strong>{{ __('Local') }}</strong><span>{{ __('Livingston based') }}</span></div>
                    <div><strong>100%</strong><span>{{ __('own engineers') }}</span></div>
                  </div>
                </div>
              @endif
            </div>
            <div class="ytx-about-copy">
              <div class="ytx-eyebrow">{{ $aboutEyebrow }}</div>
              <h2>{{ $aboutTitle }}</h2>
              <div class="ytx-about-text">{!! $aboutText !!}</div>
              @if (!empty($about->button_url))
                <a class="ytx-btn" href="{{ $about->button_url }}">{{ $about->button_text ?: __('Get in touch') }} →</a>
              @endif
            </div>
          </div>
        </div>
      </section>
    @endif

    @foreach ($ytxCustomSection($after_about ?? [], $aboutSec ?? []) as $cs)
      <section class="ytx-section ytx-soft"><div class="ytx-wrap"><div class="ytx-section-title ytx-section-title-center"><div><h2>{{ $cs->section_name }}</h2></div></div><div class="ytx-custom-content">{!! $cs->content !!}</div></div></section>
    @endforeach

    {{-- Values --}}
    @if (($secInfo->features_section_status ?? 1) == 1)
      <section class="ytx-section ytx-soft">
        <div class="ytx-wrap">
          <div class="ytx-section-title ytx-section-title-center">
            <div>
              <div class="ytx-eyebrow">{{ __('What we stand for') }}</div>
              <h2>{{ !empty($about->features_title) ? $about->features_title : __('Why people choose') . ' ' . $ytxSiteName }}</h2>
            </div>
          </div>
          <div class="ytx-values-grid">
            @foreach ($ytxValues as $v)
              <div class="ytx-help-card">
                <span class="ytx-help-icon"><i class="{{ $v['icon'] }}"></i></span>
                <h3>{{ $v['title'] }}</h3>
                <p>{{ $v['text'] }}</p>
              </div>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    @foreach ($ytxCustomSection($after_features ?? [], $aboutSec ?? []) as $cs)
      <section class="ytx-section"><div class="ytx-wrap"><div class="ytx-section-title ytx-section-title-center"><div><h2>{{ $cs->section_name }}</h2></div></div><div class="ytx-custom-content">{!! $cs->content !!}</div></div></section>
    @endforeach

    {{-- Stats --}}
    <section class="ytx-stats">
      <div class="ytx-wrap ytx-stats-grid">
        <div class="ytx-stat"><strong>{{ $ytxYears }}+</strong><span>{{ __('Years in business') }}</span></div>
        <div class="ytx-stat"><strong>5,000+</strong><span>{{ __('Jobs completed') }}</span></div>{{-- TODO real figure --}}
        <div class="ytx-stat"><strong>5.0</strong><span>{{ __('Google rating') }}</span></div>{{-- TODO real figure --}}
        <div class="ytx-stat"><strong>{{ __('Gas Safe & NICEIC') }}</strong><span>{{ __('Registered engineers') }}</span></div>
      </div>
    </section>

    {{-- How we work --}}
    <section class="ytx-section">
      <div class="ytx-wrap">
        <div class="ytx-section-title ytx-section-title-center">
          <div>
            <div class="ytx-eyebrow">{{ __('Simple from start to finish') }}</div>
            <h2>{{ !empty($sectionContent->workprocess_section_title) ? $sectionContent->workprocess_section_title : __('How we work') }}</h2>
          </div>
        </div>
        <div class="ytx-how-grid">
          @foreach ($ytxSteps as $i => $step)
            <div class="ytx-how-step">
              <span class="ytx-how-num">{{ $i + 1 }}</span>
              <span class="ytx-how-icon"><i class="fal {{ $ytxStepIcons[$i % 4] }}"></i></span>
              <h3>{{ $step->title }}</h3>
              <p>{{ $step->text }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    @foreach ($ytxCustomSection($after_work_process ?? [], $aboutSec ?? []) as $cs)
      <section class="ytx-section ytx-soft"><div class="ytx-wrap"><div class="ytx-section-title ytx-section-title-center"><div><h2>{{ $cs->section_name }}</h2></div></div><div class="ytx-custom-content">{!! $cs->content !!}</div></div></section>
    @endforeach

    {{-- Accreditations --}}
    <div class="ytx-accreditations">
      <div class="ytx-wrap">
        <span class="ytx-accreditations-label">{{ __('Registered & accredited') }}</span>
        <div class="ytx-accreditations-marquee" aria-label="{{ __('Accreditations') }}">
        <div class="ytx-accreditations-track">
          <div class="ytx-accreditations-row">
          <span class="ytx-accreditation"><img src="{{ asset('assets/img/trust/gas-safe-register.jpg') }}" alt="{{ __('Gas Safe Register') }}"></span>
          <span class="ytx-accreditation ytx-accreditation-text ytx-niceic-badge"><strong>NICEIC</strong></span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-shield-check"></i> {{ __('Fully insured') }}</span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-id-card"></i> {{ __('DBS checked') }}</span>
          <a class="ytx-accreditation ytx-accreditation-text ytx-google-badge" href="{{ $ytxGoogleUrl }}" target="_blank" rel="noopener"><span class="ytx-google-mark" aria-hidden="true">G</span> {{ __('Google reviews') }}</a>
          </div>
          <div class="ytx-accreditations-row" aria-hidden="true">
          <span class="ytx-accreditation"><img src="{{ asset('assets/img/trust/gas-safe-register.jpg') }}" alt="{{ __('Gas Safe Register') }}"></span>
          <span class="ytx-accreditation ytx-accreditation-text ytx-niceic-badge"><strong>NICEIC</strong></span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-shield-check"></i> {{ __('Fully insured') }}</span>
          <span class="ytx-accreditation ytx-accreditation-text"><i class="fal fa-id-card"></i> {{ __('DBS checked') }}</span>
          <a class="ytx-accreditation ytx-accreditation-text ytx-google-badge" href="{{ $ytxGoogleUrl }}" target="_blank" rel="noopener"><span class="ytx-google-mark" aria-hidden="true">G</span> {{ __('Google reviews') }}</a>
          </div>
        </div>
        </div>
      </div>
    </div>

    {{-- Testimonials --}}
    @if (($secInfo->testimonial_section_status ?? 1) == 1 && $ytxAboutReviews->isNotEmpty())
      <section class="ytx-section ytx-soft">
        <div class="ytx-wrap">
          <div class="ytx-section-title">
            <div>
              <div class="ytx-eyebrow">{{ __('What our customers say') }}</div>
              <h2>{{ !empty($sectionContent->testimonial_section_title) ? $sectionContent->testimonial_section_title : __('Trusted by homeowners and landlords') }}</h2>
            </div>
            <a class="ytx-btn ytx-outline" href="{{ $ytxGoogleUrl }}" target="_blank" rel="noopener"><span class="ytx-google-mark" aria-hidden="true">G</span> {{ __('Read our Google reviews') }}</a>
          </div>
          <div class="ytx-reviews ytx-reviews-cards ytx-reviews-3">
            @foreach ($ytxAboutReviews->take(6) as $t)
              <article class="ytx-review">
                <div class="ytx-review-head">
                  @if (!empty($t->photo))
                    <img class="ytx-review-avatar ytx-review-avatar-img" src="{{ $t->photo }}" alt="" loading="lazy" referrerpolicy="no-referrer">
                  @else
                    <span class="ytx-review-avatar">{{ strtoupper(mb_substr(trim($t->name), 0, 1)) }}</span>
                  @endif
                  <div><strong>{{ $t->name }}</strong>@if ($t->occupation)<small>{{ $t->occupation }}</small>@endif</div>
                  <span class="ytx-google-mark ytx-review-g" aria-hidden="true">G</span>
                </div>
                <div class="ytx-stars">{{ str_repeat('★', max(0, min(5, (int) $t->rating))) }}{{ str_repeat('☆', max(0, 5 - (int) $t->rating)) }}</div>
                <p>“{{ $t->comment }}”</p>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif

    @foreach ($ytxCustomSection($after_testimonial ?? [], $aboutSec ?? []) as $cs)
      <section class="ytx-section"><div class="ytx-wrap"><div class="ytx-section-title ytx-section-title-center"><div><h2>{{ $cs->section_name }}</h2></div></div><div class="ytx-custom-content">{!! $cs->content !!}</div></div></section>
    @endforeach

    {{-- CTA --}}
    <section class="ytx-section">
      <div class="ytx-wrap ytx-quote-panel">
        <div class="ytx-contact-grid">
          <div>
            <div class="ytx-eyebrow">{{ __('Work with us') }}</div>
            <h2>{{ __('Need a hand with your home or rental property?') }}</h2>
            <p class="ytx-lead">{{ __('Book online in a minute, or tell us about the job and one of the family will get back to you.') }}</p>
            <div class="ytx-hero-actions">
              <a class="ytx-btn" href="{{ route('frontend.services') }}">{{ __('Book a service') }} →</a>
              <a class="ytx-btn ytx-outline" href="{{ route('contact') }}">{{ __('Request a quote') }}</a>
            </div>
          </div>
          <aside class="ytx-contact-card">
            <h3>{{ __('Prefer to talk?') }}</h3>
            @if ($ytxPhone)
              <p class="ytx-lead"><a href="{{ $ytxPhoneHref }}">{{ $ytxPhone }}</a></p>
            @endif
            <p>{{ __('Monday – Friday: 8am – 6pm') }}</p>
            <hr>
            <p>{{ __('128 Waverley Crescent, Livingston EH54 8JR') }}</p>
          </aside>
        </div>
      </div>
    </section>

  </div>
@endsection
