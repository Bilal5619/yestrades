@extends('frontend.layout')

@section('pageHeading')
  {{ __('Confirm tenant appointment') }}
@endsection

@section('style')
  <style>
    .owner-slot-page { padding: 80px 0; background: #f8f9fb; }
    .owner-slot-card { width: min(680px, 100%); margin: 0 auto; padding: 32px; background: #fff; border: 1px solid #e3e6ec; border-radius: 14px; box-shadow: 0 16px 44px rgba(22, 28, 45, .08); }
    .owner-slot-card h1 { margin-bottom: 10px; color: #151822; font-size: 28px; font-weight: 800; }
    .owner-slot-lead { margin-bottom: 24px; color: #6d7380; line-height: 1.65; }
    .owner-slot-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 24px; padding: 18px; background: #f8fafc; border: 1px solid #e4e8ef; border-radius: 10px; }
    .owner-slot-summary-item.is-full { grid-column: 1 / -1; }
    .owner-slot-label { display: block; margin-bottom: 4px; color: #7b818d; font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .owner-slot-value { display: block; color: #242833; font-size: 14px; font-weight: 700; overflow-wrap: anywhere; }
    .owner-slot-submit { width: 100%; min-height: 50px; padding: 11px 18px; color: #fff; background: var(--color-primary); border: 0; border-radius: 8px; font-size: 15px; font-weight: 800; cursor: pointer; }
    .owner-slot-alert { margin-bottom: 20px; padding: 16px; border-radius: 9px; font-size: 14px; font-weight: 700; line-height: 1.6; }
    .owner-slot-alert.is-success { color: #17643c; background: #e8f8ee; border: 1px solid #c9ead5; }
    .owner-slot-alert.is-error { color: #92293b; background: #ffedf0; border: 1px solid #f6ccd4; }
    @media (max-width: 767.98px) {
      .owner-slot-page { padding: 48px 0; }
      .owner-slot-card { padding: 22px 16px; border-radius: 10px; }
      .owner-slot-card h1 { font-size: 23px; }
      .owner-slot-summary { grid-template-columns: 1fr; }
      .owner-slot-summary-item.is-full { grid-column: auto; }
    }
  </style>
@endsection

@section('content')
  <section class="owner-slot-page">
    <div class="container">
      <div class="owner-slot-card">
        <h1>{{ $confirmed ? __('Appointment confirmed') : __('Confirm tenant appointment') }}</h1>
        <p class="owner-slot-lead">
          {{ $confirmed
              ? __('This tenant/occupier appointment has already been confirmed.')
              : __('Your tenant/occupier selected the appointment below. Review it before confirming the final date and time.') }}
        </p>

        @if (session('success'))
          <div class="owner-slot-alert is-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="owner-slot-alert is-error">{{ session('error') }}</div>
        @endif

        <div class="owner-slot-summary">
          <div class="owner-slot-summary-item">
            <span class="owner-slot-label">{{ __('Booking reference') }}</span>
            <span class="owner-slot-value">{{ $booking->order_number }}</span>
          </div>
          <div class="owner-slot-summary-item">
            <span class="owner-slot-label">{{ __('Service') }}</span>
            <span class="owner-slot-value">{{ $serviceName }}</span>
          </div>
          <div class="owner-slot-summary-item is-full">
            <span class="owner-slot-label">{{ __('Property') }}</span>
            <span class="owner-slot-value">{{ collect([$booking->site_address ?: $booking->customer_address, $booking->site_zip_code ?: $booking->customer_zip_code])->filter()->join(', ') }}</span>
          </div>
          <div class="owner-slot-summary-item">
            <span class="owner-slot-label">{{ __('Tenant/occupier') }}</span>
            <span class="owner-slot-value">{{ $booking->tenant_name }}</span>
          </div>
          <div class="owner-slot-summary-item">
            <span class="owner-slot-label">{{ __('Selected date') }}</span>
            <span class="owner-slot-value">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('j F Y') : '' }}</span>
          </div>
          <div class="owner-slot-summary-item">
            <span class="owner-slot-label">{{ __('Selected time') }}</span>
            <span class="owner-slot-value">
              {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('g:i A') : '' }}
              - {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('g:i A') : '' }}
            </span>
          </div>
          @if ($booking->tenant_note)
            <div class="owner-slot-summary-item is-full">
              <span class="owner-slot-label">{{ __('Tenant note') }}</span>
              <span class="owner-slot-value">{{ $booking->tenant_note }}</span>
            </div>
          @endif
        </div>

        @if ($pending)
          <form action="{{ route('tenant.slot.owner.confirm', ['token' => $token]) }}" method="POST">
            @csrf
            <button type="submit" class="owner-slot-submit">{{ __('Confirm appointment') }}</button>
          </form>
        @elseif (!$confirmed)
          <div class="owner-slot-alert is-error">{{ __('This appointment selection can no longer be confirmed.') }}</div>
        @endif
      </div>
    </div>
  </section>
@endsection
