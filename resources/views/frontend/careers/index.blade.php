@extends('frontend.layout')

@section('pageHeading', 'Careers at ' . ($websiteInfo->website_title ?? config('app.name')) . ' | Join Our Team')
@section('metaDescription', 'Explore current career opportunities at ' . ($websiteInfo->website_title ?? config('app.name')) . ' and apply to join our growing UK home-services team.')
@section('metaKeywords', ($websiteInfo->website_title ?? config('app.name')) . ' careers, jobs, recruitment')

@section('content')
  <main class="cr-page">

    <!-- ══════════ Hero start ══════════ -->
    <section class="cr-hero">
      <div class="cr-hero-blob cr-hero-blob-1"></div>
      <div class="cr-hero-blob cr-hero-blob-2"></div>
      <div class="cr-hero-dots"></div>
      <div class="container">
        <div class="cr-hero-grid">
          <div data-aos="fade-up">
            <span class="cr-eyebrow"><span class="cr-eyebrow-dot"></span>{{ __('Careers at') }} {{ $websiteInfo->website_title ?? config('app.name') }}</span>
            <h1>{{ __('Build your career') }} <span>{{ __('with') }} {{ $websiteInfo->website_title ?? config('app.name') }}.</span></h1>
            <p>{{ __('Join a growing team helping customers across the UK book trusted plumbing, heating, gas, electrical, cleaning and property services.') }}</p>
            <div class="cr-hero-actions">
              <a href="#open-positions" class="cr-btn-primary">
                {{ __('View Open Positions') }} <i class="fal fa-long-arrow-right"></i>
              </a>
              <a href="{{ route('careers.general.create') }}" class="cr-btn-ghost">
                <i class="fal fa-paper-plane"></i>{{ __('Send Your CV') }}
              </a>
            </div>
          </div>
          <div data-aos="fade-up" data-aos-delay="100">
            <div class="cr-hero-panel">
              <span class="cr-panel-icon"><i class="fal fa-briefcase"></i></span>
              <h2>{{ __('Find your next role') }}</h2>
              <p>{{ __('Browse current vacancies or share your CV for future opportunities with the') }} {{ $websiteInfo->website_title ?? config('app.name') }} {{ __('recruitment team.') }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ══════════ Hero end ══════════ -->

    <!-- ══════════ Why work with us ══════════ -->
    <section class="cr-section">
      <div class="container">
        <div class="cr-section-head" data-aos="fade-up">
          <span class="cr-tag"><i class="fal fa-heart"></i>{{ __('Why work with us') }}</span>
          <h2>{{ __('A team worth joining') }}</h2>
        </div>
        <div class="row g-4">
          @foreach ([
              ['fa-rocket', 'Growing Company', 'Be part of a fast-growing UK home-services platform.'],
              ['fa-chart-line', 'Career Development', 'Develop your skills through practical experience, support and new opportunities.'],
              ['fa-users', 'Collaborative Team', 'Work with a supportive team focused on delivering excellent customer service.'],
              ['fa-hand-holding-heart', 'Meaningful Work', 'Help customers and property professionals find reliable services when they need them.'],
          ] as $card)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
              <div class="cr-why-card">
                <span class="cr-why-icon"><i class="fal {{ $card[0] }}"></i></span>
                <h3>{{ __($card[1]) }}</h3>
                <p>{{ __($card[2]) }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- ══════════ Open positions ══════════ -->
    <section id="open-positions" class="cr-section cr-jobs-section">
      <div class="container">
        <div class="cr-section-head cr-head-split" data-aos="fade-up">
          <div>
            <span class="cr-tag"><i class="fal fa-briefcase"></i>{{ __('Open Positions') }}</span>
            <h2>{{ __('Current vacancies') }}</h2>
          </div>
          <a href="{{ route('careers.general.create') }}" class="cr-btn-primary">
            <i class="fal fa-paper-plane"></i>{{ __('Send Your CV') }}
          </a>
        </div>

        <form method="GET" action="{{ route('careers.index') }}" class="cr-filter-card" data-aos="fade-up">
          <div class="row g-3 align-items-end">
            <div class="col-lg-4">
              <label for="career-search">{{ __('Search') }}</label>
              <input id="career-search" class="form-control" type="search" name="search"
                value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Job title or keyword') }}">
            </div>
            <div class="col-lg-2 col-md-6">
              <label for="department">{{ __('Department') }}</label>
              <select id="department" class="form-control" name="department">
                <option value="">{{ __('All') }}</option>
                @foreach ($filterOptions['departments'] as $option)
                  <option value="{{ $option }}" @selected(($filters['department'] ?? '') === $option)>{{ $option }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-2 col-md-6">
              <label for="employment_type">{{ __('Employment type') }}</label>
              <select id="employment_type" class="form-control" name="employment_type">
                <option value="">{{ __('All') }}</option>
                @foreach ($filterOptions['employmentTypes'] as $option)
                  <option value="{{ $option }}" @selected(($filters['employment_type'] ?? '') === $option)>{{ $option }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-2 col-md-6">
              <label for="workplace_type">{{ __('Workplace') }}</label>
              <select id="workplace_type" class="form-control" name="workplace_type">
                <option value="">{{ __('All') }}</option>
                @foreach ($filterOptions['workplaceTypes'] as $option)
                  <option value="{{ $option }}" @selected(($filters['workplace_type'] ?? '') === $option)>{{ $option }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-2 col-md-6">
              <label for="location">{{ __('Location') }}</label>
              <select id="location" class="form-control" name="location">
                <option value="">{{ __('All') }}</option>
                @foreach ($filterOptions['locations'] as $option)
                  <option value="{{ $option }}" @selected(($filters['location'] ?? '') === $option)>{{ $option }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 cr-filter-actions">
              <button class="cr-btn-primary" type="submit">
                <i class="fal fa-filter"></i>{{ __('Filter Jobs') }}
              </button>
              <a class="cr-filter-clear" href="{{ route('careers.index') }}#open-positions">{{ __('Clear filters') }}</a>
            </div>
          </div>
        </form>

        @if ($jobs->count())
          <div class="row g-4">
            @foreach ($jobs as $job)
              <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <article class="cr-job-card">
                  <div class="cr-job-tags">
                    <span><i class="fal fa-building"></i>{{ $job->department }}</span>
                    <span><i class="fal fa-map-marker-alt"></i>{{ $job->location }}</span>
                  </div>
                  <h3><a href="{{ route('careers.show', $job->slug) }}">{{ $job->title }}</a></h3>
                  <div class="cr-job-meta">
                    <span class="cr-job-pill"><i class="fal fa-clock"></i>{{ $job->employment_type }}</span>
                    <span class="cr-job-pill"><i class="fal fa-laptop"></i>{{ $job->workplace_type }}</span>
                  </div>
                  @if ($job->salary_text)
                    <p class="cr-job-salary">{{ $job->salary_text }}</p>
                  @endif
                  <p class="cr-job-summary">{{ $job->summary }}</p>
                  @if ($job->closing_date)
                    <p class="cr-job-closing"><i class="fal fa-calendar-alt"></i>{{ __('Closing date') }}: {{ $job->closing_date->format('d M Y') }}</p>
                  @endif
                  <a class="cr-job-btn" href="{{ route('careers.show', $job->slug) }}">
                    {{ __('View Job') }} <i class="fal fa-arrow-right"></i>
                  </a>
                </article>
              </div>
            @endforeach
          </div>
          <div class="cr-pagination-wrap">
            {{ $jobs->links() }}
          </div>
        @else
          <div class="cr-empty" data-aos="fade-up">
            <i class="fal fa-briefcase"></i>
            <h3>{{ __('We do not currently have any open positions.') }}</h3>
            <p>{{ __('You can still send us your CV for future opportunities.') }}</p>
            <a class="cr-btn-primary" href="{{ route('careers.general.create') }}">
              {{ __('Send Your CV') }} <i class="fal fa-paper-plane"></i>
            </a>
          </div>
        @endif
      </div>
    </section>
  </main>

  <style>
    @include('frontend.careers.partials.styles')
  </style>
@endsection
