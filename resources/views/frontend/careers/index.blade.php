@extends('frontend.layout')

@section('pageHeading', 'Careers at Yes Trades | Join Our Team')
@section('metaDescription', 'Explore current career opportunities at Yes Trades and apply to join our growing UK home-services team.')
@section('metaKeywords', 'Yes Trades careers, jobs, recruitment')

@section('content')
  <main class="careers-page">
    <section class="pt-120 pb-80 bg-light">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7">
            <span class="badge badge-primary mb-3">Careers at Yes Trades</span>
            <h1>Build your career with Yes Trades</h1>
            <p class="lead">Join a growing team helping customers across the UK book trusted plumbing, heating, gas, electrical, cleaning and property services.</p>
            <div class="mt-4">
              <a href="#open-positions" class="btn btn-primary mr-2 mb-2">View Open Positions</a>
              <a href="{{ route('careers.general.create') }}" class="btn btn-outline-primary mb-2">Send Your CV</a>
            </div>
          </div>
          <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="p-4 bg-white shadow-sm rounded">
              <h2 class="h4">Find your next role</h2>
              <p class="mb-0">Browse current vacancies or share your CV for future opportunities with the Yes Trades recruitment team.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="pt-80 pb-60">
      <div class="container">
        <div class="row mb-4"><div class="col"><h2>Why Work With Us</h2></div></div>
        <div class="row">
          @foreach ([['Growing Company','Be part of a fast-growing UK home-services platform.'],['Career Development','Develop your skills through practical experience, support and new opportunities.'],['Collaborative Team','Work with a supportive team focused on delivering excellent customer service.'],['Meaningful Work','Help customers and property professionals find reliable services when they need them.']] as $card)
            <div class="col-md-6 col-lg-3 mb-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><h3 class="h5">{{ $card[0] }}</h3><p class="mb-0">{{ $card[1] }}</p></div></div></div>
          @endforeach
        </div>
      </div>
    </section>

    <section id="open-positions" class="pt-80 pb-100 bg-light">
      <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
          <h2 class="mb-2">Open Positions</h2>
          <a href="{{ route('careers.general.create') }}" class="btn btn-outline-primary btn-sm">Send Your CV</a>
        </div>

        <form method="GET" action="{{ route('careers.index') }}" class="card border-0 shadow-sm mb-4">
          <div class="card-body"><div class="row">
            <div class="col-lg-4 mb-3"><label for="career-search">Search</label><input id="career-search" class="form-control" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Job title or keyword"></div>
            <div class="col-lg-2 col-md-6 mb-3"><label for="department">Department</label><select id="department" class="form-control" name="department"><option value="">All</option>@foreach ($filterOptions['departments'] as $option)<option value="{{ $option }}" @selected(($filters['department'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-6 mb-3"><label for="employment_type">Employment type</label><select id="employment_type" class="form-control" name="employment_type"><option value="">All</option>@foreach ($filterOptions['employmentTypes'] as $option)<option value="{{ $option }}" @selected(($filters['employment_type'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-6 mb-3"><label for="workplace_type">Workplace</label><select id="workplace_type" class="form-control" name="workplace_type"><option value="">All</option>@foreach ($filterOptions['workplaceTypes'] as $option)<option value="{{ $option }}" @selected(($filters['workplace_type'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-6 mb-3"><label for="location">Location</label><select id="location" class="form-control" name="location"><option value="">All</option>@foreach ($filterOptions['locations'] as $option)<option value="{{ $option }}" @selected(($filters['location'] ?? '') === $option)>{{ $option }}</option>@endforeach</select></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Filter Jobs</button> <a class="btn btn-link" href="{{ route('careers.index') }}#open-positions">Clear</a></div>
          </div></div>
        </form>

        @if ($jobs->count())
          <div class="row">
            @foreach ($jobs as $job)
              <div class="col-lg-4 col-md-6 mb-4"><article class="card h-100 border-0 shadow-sm"><div class="card-body d-flex flex-column">
                <h3 class="h5"><a href="{{ route('careers.show', $job->slug) }}">{{ $job->title }}</a></h3>
                <p class="small text-muted mb-2">{{ $job->department }} | {{ $job->employment_type }} | {{ $job->workplace_type }} | {{ $job->location }}</p>
                @if ($job->salary_text)<p class="font-weight-bold mb-2">{{ $job->salary_text }}</p>@endif
                <p>{{ $job->summary }}</p>
                @if ($job->closing_date)<p class="small text-muted">Closing date: {{ $job->closing_date->format('d M Y') }}</p>@endif
                <a class="btn btn-primary mt-auto" href="{{ route('careers.show', $job->slug) }}">View Job</a>
              </div></article></div>
            @endforeach
          </div>
          {{ $jobs->links() }}
        @else
          <div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><h3 class="h4">We do not currently have any open positions.</h3><p>You can still send us your CV for future opportunities.</p><a class="btn btn-primary" href="{{ route('careers.general.create') }}">Send Your CV</a></div></div>
        @endif
      </div>
    </section>
  </main>
@endsection
