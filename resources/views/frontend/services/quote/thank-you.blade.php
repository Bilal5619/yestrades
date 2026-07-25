@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ __('Quote request submitted') }}
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => __('Quote request submitted'),
  ])

  <div class="contact-area ptb-100">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto text-center">
          <i class="fal fa-check-circle text-success" style="font-size: 64px;"></i>
          <h2 class="mt-20">{{ __('Your quotation request has been submitted successfully.') }}</h2>
          <p class="mt-10">{{ __('Our team will review your requirements and contact you with the next steps.') }}</p>
          <p class="mt-10"><strong>{{ __('Reference number') }}:</strong> {{ $reference }}</p>
          <a href="{{ route('index') }}" class="btn btn-primary mt-20">{{ __('Back to home') }}</a>
        </div>
      </div>
    </div>
  </div>
@endsection
