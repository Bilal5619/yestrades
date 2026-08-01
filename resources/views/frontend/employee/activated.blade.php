@extends('frontend.layout')

@section('pageHeading')
  {{ __('Account activated') }}
@endsection

@section('content')
  <style>
    .emp-activate-page { padding: 80px 0; background: #f8f9fb; min-height: 60vh; }
    .emp-activate-card {
      width: min(480px, 100%);
      margin: 0 auto;
      padding: 32px;
      background: #ffffff;
      border: 1px solid #e3e6ec;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
      text-align: center;
    }
    .emp-activate-card h1 { font-size: 22px; margin: 14px 0 10px; }
    .emp-activate-card p { color: #6b7280; margin: 0; }
    .emp-activate-icon { font-size: 40px; color: #0e9f6e; }
  </style>

  <div class="emp-activate-page">
    <div class="container">
      <div class="emp-activate-card">
        <div class="emp-activate-icon"><i class="fal fa-check-circle"></i></div>
        <h1>{{ __('You\'re all set,') }} {{ $employee->first_name }}!</h1>
        <p>{{ __('Your account has been activated. You can now log in from the Yes Trades staff mobile app using your email and the password you just set.') }}</p>
      </div>
    </div>
  </div>
@endsection
