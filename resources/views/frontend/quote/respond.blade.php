@extends('frontend.layout')

@section('pageHeading')
  {{ __('Your quote') }}
@endsection

@section('style')
  <style>
    .quote-respond-page { padding: 80px 0; background: #f8f9fb; }
    .quote-respond-card {
      width: min(680px, 100%);
      margin: 0 auto;
      padding: 32px;
      background: #ffffff;
      border: 1px solid #e3e6ec;
      border-radius: 14px;
      box-shadow: 0 16px 44px rgba(22, 28, 45, .08);
    }
    .quote-respond-breakdown { width: 100%; margin: 20px 0; }
    .quote-respond-breakdown td { padding: 6px 0; }
    .quote-respond-breakdown td:last-child { text-align: right; }
    .quote-respond-total { font-weight: 700; font-size: 1.1em; border-top: 1px solid #e3e6ec; }
  </style>
@endsection

@section('content')
  <div class="quote-respond-page">
    <div class="container">
      <div class="quote-respond-card">

        @if (Session::has('success'))
          <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if (Session::has('error'))
          <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif

        @if ($expired)
          <h3>{{ __('This link has expired') }}</h3>
          <p>{{ __('Please contact us and we will send you a new quote link.') }}</p>
        @elseif ($alreadyAccepted)
          <h3>{{ __('You have already accepted this quote') }}</h3>
          <p>{{ __('Thank you — our team will be in touch to confirm your booking.') }}</p>
        @elseif ($alreadyDeclined || $used)
          <h3>{{ __('This quote has already been responded to') }}</h3>
          <p>{{ __('If you have changed your mind, please contact us directly.') }}</p>
        @else
          <h3>{{ __('Your quote for') }} {{ $serviceName }}</h3>
          <p>{{ __('Reference') }}: {{ $quote->reference_number }}</p>

          <table class="quote-respond-breakdown">
            <tr><td>{{ __('Labour') }}</td><td>{{ number_format((float) $quote->labour_cost, 2) }}</td></tr>
            <tr><td>{{ __('Materials') }}</td><td>{{ number_format((float) $quote->material_cost, 2) }}</td></tr>
            <tr><td>{{ __('Call-out charge') }}</td><td>{{ number_format((float) $quote->callout_charge, 2) }}</td></tr>
            <tr><td>{{ __('Additional charges') }}</td><td>{{ number_format((float) $quote->additional_charges, 2) }}</td></tr>
            <tr><td>{{ __('Discount') }}</td><td>-{{ number_format((float) $quote->discount, 2) }}</td></tr>
            <tr><td>{{ __('VAT') }}</td><td>{{ number_format((float) $quote->vat_amount, 2) }}</td></tr>
            <tr class="quote-respond-total"><td>{{ __('Total') }}</td><td>{{ number_format((float) $quote->total_price, 2) }}</td></tr>
          </table>

          @if ($quote->customer_notes)
            <p>{{ $quote->customer_notes }}</p>
          @endif

          @if ($quote->expiry_date)
            <p class="text-muted">{{ __('This quote is valid until') }} {{ $quote->expiry_date->format('d M Y') }}.</p>
          @endif

          <div class="d-flex gap-2 mt-20">
            <form action="{{ route('frontend.quote.response.accept', $token) }}" method="POST" class="mr-2">
              @csrf
              <button type="submit" class="btn btn-lg btn-primary">{{ __('Accept quote') }}</button>
            </form>
            <form action="{{ route('frontend.quote.response.decline', $token) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to decline this quote?') }}');">
              @csrf
              <button type="submit" class="btn btn-lg btn-outline-secondary">{{ __('Decline quote') }}</button>
            </form>
          </div>
        @endif

      </div>
    </div>
  </div>
@endsection
