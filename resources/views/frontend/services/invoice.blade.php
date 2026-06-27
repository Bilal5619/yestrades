<!DOCTYPE html>
<html>

<head lang="{{ $currentLanguageInfo->code }}" @if ($currentLanguageInfo->direction == 1) dir="rtl" @endif>
  <meta charset="UTF-8">
  <title>{{ 'Service Booking Invoice | ' . config('app.name') }}</title>
  <style>
    body { color: #222; font-family: DejaVu Sans, sans-serif; font-size: 13px; line-height: 1.45; }
    .invoice { padding: 28px; }
    .header { border-bottom: 2px solid #222; margin-bottom: 24px; padding-bottom: 16px; }
    .logo { max-width: 160px; }
    .title { font-size: 24px; font-weight: 700; margin: 0 0 6px; text-transform: uppercase; }
    .muted { color: #666; }
    .grid { clear: both; width: 100%; }
    .half { float: left; width: 48%; }
    .half.right { float: right; }
    .section-title { border-bottom: 1px solid #ddd; font-size: 15px; font-weight: 700; margin: 18px 0 10px; padding-bottom: 6px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 9px; text-align: left; vertical-align: top; }
    th { background: #f5f5f5; }
    .summary { margin-left: auto; margin-top: 18px; width: 45%; }
    .total-row th, .total-row td { background: #222; color: #fff; font-weight: 700; }
    .clearfix::after { clear: both; content: ""; display: table; }
  </style>
</head>

<body>
  @php
    $position = $orderInfo->currency_text_position;
    $currency = $orderInfo->currency_text;
    $serviceInfo = App\Models\Services\ServiceContent::where('service_id', $orderInfo->service_id)->select('name', 'address')->first();
    $invoiceItemName = ($orderInfo->item_type ?? 'service') === 'package'
      ? ($orderInfo->package_name ?? __('Service Package'))
      : ($serviceInfo ? $serviceInfo->name : __('Service Booking'));
    $includedServices = ($orderInfo->item_type ?? 'service') === 'package'
      ? collect($orderInfo->package_included_services ?? [])->pluck('name')->filter()->implode(', ')
      : '';
    $amount = number_format($orderInfo->customer_paid, 2);
    $formattedAmount = ($position == 'left' ? $currency . ' ' : '') . $amount . ($position == 'right' ? ' ' . $currency : '');
  @endphp

  <div class="invoice">
    <div class="header clearfix">
      <div class="half">
        <img class="logo" src="{{ public_path('assets/img/' . $websiteInfo->logo) }}" alt="website logo">
        <p class="muted">{{ $websiteInfo->website_title }}</p>
      </div>
      <div class="half right">
        <h1 class="title">{{ __('Service Booking Invoice') }}</h1>
        <p><strong>{{ __('Invoice No.') }}:</strong> {{ $orderInfo->order_number }}</p>
        <p><strong>{{ __('Issued') }}:</strong> {{ \Carbon\Carbon::parse($orderInfo->created_at)->format('M d, Y') }}</p>
      </div>
    </div>

    <div class="grid clearfix">
      <div class="half">
        <div class="section-title">{{ __('Bill To') }}</div>
        <p><strong>{{ $orderInfo->customer_name }}</strong></p>
        <p>{{ $orderInfo->customer_email }}</p>
        <p>{{ $orderInfo->customer_phone }}</p>
        <p>{{ $orderInfo->customer_address }}</p>
        @if ($orderInfo->customer_zip_code)
          <p>{{ __('Postcode') }}: {{ $orderInfo->customer_zip_code }}</p>
        @endif
        <p>{{ $orderInfo->customer_country }}</p>
      </div>
      <div class="half right">
        <div class="section-title">{{ __('Booking Details') }}</div>
        <p><strong>{{ __('Booking No.') }}:</strong> {{ '#' . $orderInfo->order_number }}</p>
        <p><strong>{{ ($orderInfo->item_type ?? 'service') === 'package' ? __('Package') : __('Service') }}:</strong> {{ $invoiceItemName }}</p>
        @if ($includedServices !== '')
          <p><strong>{{ __('Includes') }}:</strong> {{ $includedServices }}</p>
        @endif
        <p><strong>{{ __('Appointment Date') }}:</strong> {{ \Carbon\Carbon::parse($orderInfo->booking_date)->format('M d, Y') }}</p>
        <p><strong>{{ __('Appointment Time') }}:</strong> {{ $orderInfo->start_date }} - {{ $orderInfo->end_date }}</p>
        @if ($serviceInfo && $serviceInfo->address)
          <p><strong>{{ __('Location') }}:</strong> {{ $serviceInfo->address }}</p>
        @endif
      </div>
    </div>

    <div class="section-title">{{ __('Invoice Items') }}</div>
    <table>
      <thead>
        <tr>
          <th>{{ __('Description') }}</th>
          <th>{{ __('Qty') }}</th>
          <th>{{ __('Amount') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            {{ $invoiceItemName }}
            @if ($includedServices !== '')
              <br><small>{{ __('Includes') }}: {{ $includedServices }}</small>
            @endif
          </td>
          <td>{{ $orderInfo->max_person }}</td>
          <td>{{ $formattedAmount }}</td>
        </tr>
      </tbody>
    </table>

    <table class="summary">
      <tbody>
        <tr>
          <th>{{ __('Subtotal') }}</th>
          <td>{{ $formattedAmount }}</td>
        </tr>
        <tr>
          <th>{{ __('Paid Amount') }}</th>
          <td>{{ $formattedAmount }}</td>
        </tr>
        <tr>
          <th>{{ __('Payment Method') }}</th>
          <td>{{ $orderInfo->payment_method }}</td>
        </tr>
        <tr>
          <th>{{ __('Payment Status') }}</th>
          <td>{{ ucfirst($orderInfo->payment_status) }}</td>
        </tr>
        <tr>
          <th>{{ __('Order Status') }}</th>
          <td>{{ ucfirst($orderInfo->order_status) }}</td>
        </tr>
        <tr class="total-row">
          <th>{{ __('Total') }}</th>
          <td>{{ $formattedAmount }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>

</html>
