<!DOCTYPE html>
<html>

<head lang="{{ $currentLanguageInfo->code }}" @if ($currentLanguageInfo->direction == 1) dir="rtl" @endif>
  <meta charset="UTF-8">
  <title>{{ 'Featured Service Request Invoice | ' . config('app.name') }}</title>
  <style>
    body { color: #222; font-family: DejaVu Sans, sans-serif; font-size: 13px; line-height: 1.45; }
    .invoice { padding: 28px; }
    .header { border-bottom: 2px solid #222; margin-bottom: 24px; padding-bottom: 16px; }
    .logo { max-width: 160px; }
    .title { font-size: 24px; font-weight: 700; margin: 0 0 6px; text-transform: uppercase; }
    .muted { color: #666; }
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
    $serviceContent = App\Models\Services\ServiceContent::where('service_id', $orderInfo->service_id)->select('name')->first();
    $amount = number_format($orderInfo->amount, 2);
    $formattedAmount = ($position == 'left' ? $currency . ' ' : '') . $amount . ($position == 'right' ? ' ' . $currency : '');
  @endphp

  <div class="invoice">
    <div class="header clearfix">
      <div class="half">
        <img class="logo" src="{{ public_path('assets/img/' . $websiteInfo->logo) }}" alt="website logo">
        <p class="muted">{{ $websiteInfo->website_title }}</p>
      </div>
      <div class="half right">
        <h1 class="title">{{ __('Featured Service Invoice') }}</h1>
        <p><strong>{{ __('Invoice No.') }}:</strong> {{ $orderInfo->order_number }}</p>
        <p><strong>{{ __('Issued') }}:</strong> {{ \Carbon\Carbon::parse($orderInfo->created_at)->format('M d, Y') }}</p>
      </div>
    </div>

    <div class="clearfix">
      <div class="half">
        <div class="section-title">{{ __('Bill To') }}</div>
        <p><strong>{{ $orderInfo->vendor->username }}</strong></p>
        <p>{{ $orderInfo->vendor->email }}</p>
      </div>
      <div class="half right">
        <div class="section-title">{{ __('Order Details') }}</div>
        <p><strong>{{ __('Order No.') }}:</strong> {{ '#' . $orderInfo->order_number }}</p>
        <p><strong>{{ __('Service') }}:</strong> {{ $serviceContent ? $serviceContent->name : '-' }}</p>
        <p><strong>{{ __('Payment Method') }}:</strong> {{ $orderInfo->payment_method }}</p>
        <p><strong>{{ __('Payment Status') }}:</strong> {{ ucfirst($orderInfo->payment_status) }}</p>
        <p><strong>{{ __('Order Status') }}:</strong> {{ ucfirst($orderInfo->order_status) }}</p>
      </div>
    </div>

    <div class="section-title">{{ __('Invoice Items') }}</div>
    <table>
      <thead>
        <tr>
          <th>{{ __('Description') }}</th>
          <th>{{ __('Amount') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ __('Featured service promotion') }} - {{ $serviceContent ? $serviceContent->name : __('Service') }}</td>
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
        <tr class="total-row">
          <th>{{ __('Total') }}</th>
          <td>{{ $formattedAmount }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>

</html>
