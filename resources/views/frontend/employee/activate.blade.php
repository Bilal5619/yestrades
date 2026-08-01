@extends('frontend.layout')

@section('pageHeading')
  {{ __('Activate your account') }}
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
    }
    .emp-activate-card h1 { font-size: 22px; margin: 0 0 10px; }
    .emp-activate-card p { color: #6b7280; margin: 0 0 20px; }
    .emp-activate-field { margin-bottom: 16px; }
    .emp-activate-field label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #374151; }
    .emp-activate-field input {
      width: 100%; padding: 11px 12px; border: 1px solid #d7dbe3; border-radius: 8px; box-sizing: border-box; font-size: 14px;
    }
    .emp-activate-error { color: #d24b4b; font-size: 12px; margin-top: 6px; }
    .emp-activate-submit {
      width: 100%; padding: 12px; border: none; border-radius: 8px; background: #0e9f6e; color: #ffffff;
      font-weight: 600; cursor: pointer; font-size: 15px;
    }
    .emp-activate-alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; background: rgba(210,75,75,0.1); color: #d24b4b; }
  </style>

  <div class="emp-activate-page">
    <div class="container">
      <div class="emp-activate-card">
        @if ($expired)
          <h1>{{ __('Link expired') }}</h1>
          <p>{{ __('This activation link is invalid or has expired. Please ask HR to send you a new activation link.') }}</p>
        @else
          <h1>{{ __('Set your password') }}</h1>
          <p>{{ __('Hi') }} {{ $employee->first_name }}, {{ __('choose a password to activate your staff account.') }}</p>

          @if (session('error'))
            <div class="emp-activate-alert">{{ session('error') }}</div>
          @endif

          <form method="POST" action="{{ route('frontend.employee.activate.store', $token) }}">
            @csrf
            <div class="emp-activate-field">
              <label>{{ __('New password') }}</label>
              <input type="password" name="password" required minlength="8">
              @error('password')<div class="emp-activate-error">{{ $message }}</div>@enderror
            </div>
            <div class="emp-activate-field">
              <label>{{ __('Confirm password') }}</label>
              <input type="password" name="password_confirmation" required minlength="8">
            </div>
            <button type="submit" class="emp-activate-submit">{{ __('Activate my account') }}</button>
          </form>
        @endif
      </div>
    </div>
  </div>
@endsection
