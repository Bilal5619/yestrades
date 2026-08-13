@extends('frontend.layout')

@section('pageHeading')
  {{ __('Choose appointment date and time') }}
@endsection

@section('style')
  <style>
    .tenant-slot-page {
      padding: 80px 0;
      background: #f8f9fb;
    }

    .tenant-slot-card {
      width: min(680px, 100%);
      margin: 0 auto;
      padding: 32px;
      background: #ffffff;
      border: 1px solid #e3e6ec;
      border-radius: 14px;
      box-shadow: 0 16px 44px rgba(22, 28, 45, .08);
    }

    .tenant-slot-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 54px;
      height: 54px;
      margin-bottom: 18px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), .08);
      border-radius: 50%;
      font-size: 23px;
    }

    .tenant-slot-card h1 {
      margin-bottom: 10px;
      color: #151822;
      font-size: 28px;
      font-weight: 800;
    }

    .tenant-slot-lead {
      margin-bottom: 24px;
      color: #6d7380;
      line-height: 1.65;
    }

    .tenant-booking-summary {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
      margin-bottom: 24px;
      padding: 18px;
      background: #f8fafc;
      border: 1px solid #e4e8ef;
      border-radius: 10px;
    }

    .tenant-summary-item {
      min-width: 0;
    }

    .tenant-summary-item.is-full {
      grid-column: 1 / -1;
    }

    .tenant-summary-label,
    .tenant-summary-value {
      display: block;
    }

    .tenant-summary-label {
      margin-bottom: 4px;
      color: #7b818d;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .tenant-summary-value {
      color: #242833;
      font-size: 14px;
      font-weight: 700;
      overflow-wrap: anywhere;
    }

    .tenant-slot-fields {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }

    .tenant-slot-field label {
      display: block;
      margin-bottom: 8px;
      color: #343946;
      font-size: 13px;
      font-weight: 800;
    }

    .tenant-slot-field input,
    .tenant-slot-field select {
      width: 100%;
      min-height: 50px;
      padding: 10px 13px;
      color: #171923;
      background: #ffffff;
      border: 1px solid #dce1e9;
      border-radius: 8px;
      font: inherit;
    }

    .tenant-slot-note {
      margin: 12px 0 0;
      color: #737987;
      font-size: 12px;
      line-height: 1.55;
    }

    .tenant-slot-submit {
      width: 100%;
      min-height: 50px;
      margin-top: 22px;
      padding: 11px 18px;
      color: #ffffff;
      background: var(--color-primary);
      border: 0;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 800;
      cursor: pointer;
    }

    .tenant-slot-alert {
      padding: 16px;
      border-radius: 9px;
      font-size: 14px;
      font-weight: 700;
      line-height: 1.6;
    }

    .tenant-slot-alert.is-error {
      color: #92293b;
      background: #ffedf0;
      border: 1px solid #f6ccd4;
    }

    .tenant-slot-alert.is-success {
      color: #17643c;
      background: #e8f8ee;
      border: 1px solid #c9ead5;
    }

    .tenant-field-error {
      display: block;
      margin-top: 6px;
      color: #b4233d;
      font-size: 12px;
      font-weight: 700;
    }

    @media (max-width: 767.98px) {
      .tenant-slot-page {
        padding: 48px 0;
      }

      .tenant-slot-card {
        padding: 22px 16px;
        border-radius: 10px;
      }

      .tenant-slot-card h1 {
        font-size: 23px;
      }

      .tenant-booking-summary,
      .tenant-slot-fields {
        grid-template-columns: 1fr;
      }

      .tenant-summary-item.is-full {
        grid-column: auto;
      }
    }
  </style>
@endsection

@section('content')
  <section class="tenant-slot-page">
    <div class="container">
      <div class="tenant-slot-card">
        <span class="tenant-slot-icon"><i class="fal fa-calendar-check"></i></span>

        @if ($expired)
          <h1>{{ __('Appointment link expired') }}</h1>
          <div class="tenant-slot-alert is-error">
            {{ __('This link has expired. Please ask the landlord/customer to request a new appointment link.') }}
          </div>
        @elseif ($selected)
          <h1>{{ $awaitingOwnerConfirmation ? __('Appointment awaiting confirmation') : __('Appointment confirmed') }}</h1>
          <div class="tenant-slot-alert is-success">
            @if ($awaitingOwnerConfirmation)
              {{ __('Your selected date and time have been sent to the landlord/customer for confirmation.') }}
            @else
              {{ __('The appointment date and time are confirmed for this booking.') }}
            @endif
          </div>
          <div class="tenant-booking-summary mt-4">
            <div class="tenant-summary-item">
              <span class="tenant-summary-label">{{ __('Date') }}</span>
              <span class="tenant-summary-value">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('j F Y') : '' }}</span>
            </div>
            <div class="tenant-summary-item">
              <span class="tenant-summary-label">{{ __('Time') }}</span>
              <span class="tenant-summary-value">
                {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('g:i A') : '' }}
                -
                {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('g:i A') : '' }}
              </span>
            </div>
          </div>
        @else
          <h1>{{ __('Choose appointment date and time') }}</h1>
          <p class="tenant-slot-lead">
            {{ __('Choose one normal Monday-to-Saturday appointment slot for this booking. Sunday and urgent appointment options are not available through this link.') }}
          </p>

          <div class="tenant-booking-summary">
            <div class="tenant-summary-item">
              <span class="tenant-summary-label">{{ __('Booking') }}</span>
              <span class="tenant-summary-value">#{{ $booking->order_number }}</span>
            </div>
            <div class="tenant-summary-item">
              <span class="tenant-summary-label">{{ __('Service') }}</span>
              <span class="tenant-summary-value">{{ $serviceName }}</span>
            </div>
            <div class="tenant-summary-item is-full">
              <span class="tenant-summary-label">{{ __('Property') }}</span>
              <span class="tenant-summary-value">
                {{ collect([$booking->site_address ?: $booking->customer_address, $booking->site_zip_code ?: $booking->customer_zip_code])->filter()->join(', ') }}
              </span>
            </div>
          </div>

          @if ($errors->any())
            <div class="tenant-slot-alert is-error mb-3">
              {{ __('Please check the date and time below and try again.') }}
            </div>
          @endif

          <form action="{{ route('tenant.slot.store', ['token' => $token]) }}" method="POST">
            @csrf
            <div class="tenant-slot-fields">
              <div class="tenant-slot-field">
                <label for="tenant_booking_date">{{ __('Appointment date') }} *</label>
                <input type="date" id="tenant_booking_date" name="booking_date" min="{{ $minimumDate }}" value="{{ old('booking_date') }}" required>
                @error('booking_date')<span class="tenant-field-error">{{ $message }}</span>@enderror
              </div>
              <div class="tenant-slot-field">
                <label for="tenant_time_slot">{{ __('Appointment time') }} *</label>
                <select id="tenant_time_slot" name="time_slot" required>
                  <option value="">{{ __('Choose a normal time slot') }}</option>
                  @foreach ($normalSlots as $slot)
                    <option value="{{ $slot['value'] }}" @selected(old('time_slot') === $slot['value'])>{{ __($slot['label']) }}</option>
                  @endforeach
                </select>
                @error('time_slot')<span class="tenant-field-error">{{ $message }}</span>@enderror
              </div>
            </div>
            <p class="tenant-slot-note">{{ __('Available dates begin three days from today. Sundays and urgent slots are unavailable.') }}</p>
            <button type="submit" class="tenant-slot-submit">{{ __('Send appointment choice') }}</button>
          </form>
        @endif
      </div>
    </div>
  </section>
@endsection

@section('script')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const dateInput = document.getElementById('tenant_booking_date');
      if (!dateInput) return;

      dateInput.addEventListener('change', function () {
        if (!this.value) return;
        const parts = this.value.split('-').map(Number);
        const selected = new Date(parts[0], parts[1] - 1, parts[2]);
        if (selected.getDay() === 0) {
          this.setCustomValidity(@json(__('Please choose Monday to Saturday.')));
          this.reportValidity();
          this.value = '';
        } else {
          this.setCustomValidity('');
        }
      });
    });
  </script>
@endsection
