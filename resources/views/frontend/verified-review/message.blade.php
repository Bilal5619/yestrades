@extends('frontend.layout')

@section('pageHeading')
  {{ __($title) }}
@endsection

@section('content')
  <div class="authentication-area ptb-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="main-form p-0 text-center">
            <div class="title">
              <h4 class="mb-20">{{ __($title) }}</h4>
            </div>

            @if (Session::has('success'))
              <div class="alert alert-success">{{ __(Session::get('success')) }}</div>
            @else
              <p>{{ __($message) }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
