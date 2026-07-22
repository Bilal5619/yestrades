@extends('frontend.layout')

@section('pageHeading', 'Application Received | ' . ($websiteInfo->website_title ?? config('app.name')))
@section('metaDescription', 'Your ' . ($websiteInfo->website_title ?? config('app.name')) . ' application has been received.')
@section('content')
  <main class="cr-success-page">
    <div class="container">
      <div class="cr-success-card" data-aos="fade-up">
        <span class="cr-success-icon"><i class="fal fa-check"></i></span>
        <h1>{{ __('Application received') }}</h1>
        <p>{{ __('Thank you. Your application has been submitted successfully.') }}</p>
        <div class="cr-success-ref">{{ __('Reference') }}: {{ $application->reference_number }}</div>
        @if ($application->job_title_snapshot)
          <p>{{ __('Job') }}: {{ $application->job_title_snapshot }}</p>
        @endif
        <div class="cr-success-actions">
          <a href="{{ route('careers.index') }}" class="cr-btn-primary">
            {{ __('Return to Careers') }} <i class="fal fa-arrow-right"></i>
          </a>
          <a href="{{ route('index') }}" class="cr-btn-outline">
            {{ __('Return to Homepage') }}
          </a>
        </div>
      </div>
    </div>
  </main>

  <style>
    @include('frontend.careers.partials.styles')
  </style>
@endsection
