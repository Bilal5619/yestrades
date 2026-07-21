@extends('frontend.layout')

@section('pageHeading', $job->title . ' - Careers at Yes Trades')
@section('metaDescription', $job->summary)
@section('metaKeywords', $job->title . ', careers, Yes Trades')

@section('content')
  @php
    $structured = [
      '@context' => 'https://schema.org',
      '@type' => 'JobPosting',
      'title' => $job->title,
      'description' => trim(strip_tags($job->description)),
      'datePosted' => optional($job->published_at ?: $job->created_at)->toDateString(),
      'hiringOrganization' => ['@type' => 'Organization', 'name' => 'Yes Trades', 'sameAs' => url('/')],
      'employmentType' => strtoupper(str_replace(['-', ' '], '_', $job->employment_type)),
    ];
    if ($job->closing_date) $structured['validThrough'] = $job->closing_date->endOfDay()->toIso8601String();
    if ($job->workplace_type === 'Remote') $structured['applicantLocationRequirements'] = ['@type' => 'Country', 'name' => 'United Kingdom'];
    else $structured['jobLocation'] = ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $job->location, 'addressCountry' => 'GB']];
  @endphp
  <script type="application/ld+json">{!! json_encode($structured, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
  <main class="career-detail pt-100 pb-100">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <a href="{{ route('careers.index') }}" class="text-muted">Back to careers</a>
          <h1 class="mt-3">{{ $job->title }}</h1>
          <p class="text-muted">{{ $job->department }} | {{ $job->location }} | {{ $job->employment_type }} | {{ $job->workplace_type }}</p>
          @if ($job->salary_text)<p class="font-weight-bold">{{ $job->salary_text }}</p>@endif
          @if ($job->closing_date)<p>Closing date: {{ $job->closing_date->format('d M Y') }}</p>@endif
          <p class="lead">{{ $job->summary }}</p>
          <div class="mb-4">{!! $job->description !!}</div>
          @foreach ([['Main responsibilities',$job->responsibilities],['Required skills and experience',$job->requirements],['Preferred skills',$job->preferred_skills],['Benefits',$job->benefits]] as $section)
            @if ($section[1])<section class="mb-4"><h2 class="h4">{{ $section[0] }}</h2><div>{!! $section[1] !!}</div></section>@endif
          @endforeach
          <section class="mb-4"><h2 class="h4">Equal opportunities</h2><p>{{ $job->equal_opportunity_statement ?: 'Yes Trades is committed to fair and respectful recruitment decisions for every applicant.' }}</p></section>
          @include('frontend.careers.partials.application-form', ['job' => $job])
        </div>
        <aside class="col-lg-4 mt-4 mt-lg-0"><div class="card border-0 shadow-sm sticky-top" style="top: 90px;"><div class="card-body"><h2 class="h4">Apply for this role</h2><p>{{ $job->title }}</p><a href="#apply-now" class="btn btn-primary btn-block">Apply Now</a><hr><p class="small text-muted mb-0">Your CV is uploaded securely and is only available to authorised admins.</p></div></div></aside>
      </div>
    </div>
  </main>
@endsection
