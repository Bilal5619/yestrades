<p>{{ __('Hello') }} {{ $vendorName }},</p>

<p>{{ __('A service appointment has been assigned to you.') }}</p>

<p>
  <strong>{{ __('Service') }}:</strong>
  <a href="{{ $serviceLink }}">{{ $serviceName }}</a>
</p>

<p>
  <strong>{{ __('Booking No.') }}:</strong> #{{ $appointment->order_number }}<br>
  <strong>{{ __('Customer') }}:</strong> {{ $appointment->customer_name }}<br>
  <strong>{{ __('Appointment Date') }}:</strong> {{ \Carbon\Carbon::parse($appointment->booking_date)->format('M d, Y') }}<br>
  <strong>{{ __('Appointment Time') }}:</strong> {{ $appointmentTime }}
</p>

<p>
  <a href="{{ $appointmentLink }}">{{ __('View Appointment Details') }}</a>
</p>

@if (!empty($websiteTitle))
  <p>{{ $websiteTitle }}</p>
@endif
