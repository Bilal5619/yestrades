@extends('frontend.layout')

@section('pageHeading', 'Application Received | Yes Trades')
@section('metaDescription', 'Your Yes Trades application has been received.')
@section('content')
  <main class="pt-100 pb-100"><div class="container"><div class="row justify-content-center"><div class="col-lg-7"><div class="card border-0 shadow-sm text-center"><div class="card-body py-5"><h1 class="h3">Application received</h1><p>Thank you. Your application has been submitted successfully.</p><p class="font-weight-bold">Reference: {{ $application->reference_number }}</p>@if($application->job_title_snapshot)<p>Job: {{ $application->job_title_snapshot }}</p>@endif<div class="mt-4"><a href="{{ route('careers.index') }}" class="btn btn-primary mr-2">Return to Careers</a><a href="{{ route('index') }}" class="btn btn-outline-primary">Return to Homepage</a></div></div></div></div></div></div></main>
@endsection
