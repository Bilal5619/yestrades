@extends('frontend.layout')

@section('pageHeading', $job->title . ' - Careers at ' . ($websiteInfo->website_title ?? config('app.name')))
@section('metaDescription', $job->summary)
@section('metaKeywords', $job->title . ', careers, ' . ($websiteInfo->website_title ?? config('app.name')))

@section('content')
  @php
    $structured = [
      '@context' => 'https://schema.org',
      '@type' => 'JobPosting',
      'title' => $job->title,
      'description' => trim(strip_tags($job->description)),
      'datePosted' => optional($job->published_at ?: $job->created_at)->toDateString(),
      'hiringOrganization' => ['@type' => 'Organization', 'name' => $websiteInfo->website_title ?? config('app.name'), 'sameAs' => url('/')],
      'employmentType' => strtoupper(str_replace(['-', ' '], '_', $job->employment_type)),
    ];
    if ($job->closing_date) $structured['validThrough'] = $job->closing_date->endOfDay()->toIso8601String();
    if ($job->workplace_type === 'Remote') $structured['applicantLocationRequirements'] = ['@type' => 'Country', 'name' => 'United Kingdom'];
    else $structured['jobLocation'] = ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $job->location, 'addressCountry' => 'GB']];
  @endphp
  <script type="application/ld+json">{!! json_encode($structured, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

  <main class="career-detail">
    <div class="container">
      <a href="{{ route('careers.index') }}" class="cr-back-link">
        <i class="fal fa-arrow-left"></i>{{ __('Back to careers') }}
      </a>

      <div class="row mt-3">
        <div class="col-lg-8">
          <div class="cr-job-header" data-aos="fade-up">
            <h1>{{ $job->title }}</h1>
            <div class="cr-job-header-meta">
              <span class="cr-job-pill"><i class="fal fa-building"></i>{{ $job->department }}</span>
              <span class="cr-job-pill"><i class="fal fa-map-marker-alt"></i>{{ $job->location }}</span>
              <span class="cr-job-pill"><i class="fal fa-clock"></i>{{ $job->employment_type }}</span>
              <span class="cr-job-pill"><i class="fal fa-laptop"></i>{{ $job->workplace_type }}</span>
            </div>
            @if ($job->salary_text)
              <p class="cr-job-salary-lg">{{ $job->salary_text }}</p>
            @endif
            @if ($job->closing_date)
              <p class="cr-job-closing-lg"><i class="fal fa-calendar-alt"></i> {{ __('Closing date') }}: {{ $job->closing_date->format('d M Y') }}</p>
            @endif
          </div>

          <div class="cr-detail-body" data-aos="fade-up" data-aos-delay="80">
            <p class="lead">{{ $job->summary }}</p>
            <div class="mb-0">{!! $job->description !!}</div>

            @foreach ([
                [__('Main responsibilities'), $job->responsibilities],
                [__('Required skills and experience'), $job->requirements],
                [__('Preferred skills'), $job->preferred_skills],
                [__('Benefits'), $job->benefits],
            ] as $section)
              @if ($section[1])
                <section class="cr-detail-section">
                  <h2>{{ $section[0] }}</h2>
                  <div>{!! $section[1] !!}</div>
                </section>
              @endif
            @endforeach

            <section class="cr-detail-section">
              <h2>{{ __('Equal opportunities') }}</h2>
              <p>{{ $job->equal_opportunity_statement ?: ($websiteInfo->website_title ?? config('app.name')) . ' ' . __('is committed to fair and respectful recruitment decisions for every applicant.') }}</p>
            </section>
          </div>

          <div data-aos="fade-up">
            @include('frontend.careers.partials.application-form', ['job' => $job])
          </div>
        </div>

        <aside class="col-lg-4 mt-4 mt-lg-0">
          <div class="cr-apply-sidebar" data-aos="fade-up">
            <h2>{{ __('Apply for this role') }}</h2>
            <p>{{ $job->title }}</p>
            <a href="#apply-now" class="cr-btn-primary">
              {{ __('Apply Now') }} <i class="fal fa-arrow-right"></i>
            </a>
            <div class="cr-apply-note">
              <i class="fal fa-shield-check"></i>
              <span>{{ __('Your CV is uploaded securely and is only available to authorised admins.') }}</span>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>

  <style>
    @include('frontend.careers.partials.styles')
  </style>
@endsection
