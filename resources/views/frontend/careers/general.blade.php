@extends('frontend.layout')

@section('pageHeading', 'Send Your CV | Careers at Yes Trades')
@section('metaDescription', 'Send your CV to Yes Trades for future career opportunities.')
@section('content')
  <main class="cr-form-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <a href="{{ route('careers.index') }}" class="cr-back-link">
            <i class="fal fa-arrow-left"></i>{{ __('Back to careers') }}
          </a>

          <div class="cr-form-card mt-3" data-aos="fade-up">
            <h1>{{ __('Send Your CV') }}</h1>
            <p class="lead">{{ __('Share your details for future opportunities with Yes Trades.') }}</p>
            <form method="POST" action="{{ route('careers.general.store') }}" enctype="multipart/form-data" novalidate>
              @csrf
              <input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
              @include('frontend.careers.partials.fields', ['general' => true])
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <style>
    @include('frontend.careers.partials.styles')
  </style>
@endsection
