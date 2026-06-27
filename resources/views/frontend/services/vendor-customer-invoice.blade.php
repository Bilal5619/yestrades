<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>{{ 'Invoice | ' . ($issuerName ?? config('app.name')) }}</title>
  <style>
    body { color: #222; font-family: DejaVu Sans, sans-serif; font-size: 13px; line-height: 1.45; }
    .invoice { padding: 28px; }
    .header { border-bottom: 2px solid #222; margin-bottom: 24px; padding-bottom: 16px; }
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
    $symbol = $booking->currency_symbol ?: $booking->currency_text;
    $symbolPosition = $booking->currency_symbol_position ?: $booking->currency_text_position;
    $money = function ($amount) use ($symbol, $symbolPosition) {
        return ($symbolPosition == 'left' ? $symbol : '') . number_format((float) $amount, 2, '.', ',') . ($symbolPosition == 'right' ? $symbol : '');
    };
    $invoiceDate = $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') : '-';
    $vendorName = $issuerName ?? ($invoice->vendor_name ?: __('Service provider'));
  @endphp

  <div class="invoice">
    <div class="header clearfix">
      <div class="half">
        <h2 style="margin: 0 0 6px;">{{ $vendorName }}</h2>
        <p class="muted">{{ __('Service Provider') }}</p>
      </div>
      <div class="half right">
        <h1 class="title">{{ __('Invoice') }}</h1>
        <p><strong>{{ __('Invoice No.') }}:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>{{ __('Issued') }}:</strong> {{ $invoiceDate }}</p>
      </div>
    </div>

    <div class="grid clearfix">
      <div class="half">
        <div class="section-title">{{ __('Invoice From') }}</div>
        <p><strong>{{ $vendorName }}</strong></p>
        @if (!empty($issuerEmail))
          <p>{{ __('Email') }}: {{ $issuerEmail }}</p>
        @endif
        @if (!empty($issuerPhone))
          <p>{{ __('Phone') }}: {{ $issuerPhone }}</p>
        @endif
      </div>
      <div class="half right">
        <div class="section-title">{{ __('Booking Details') }}</div>
        <p><strong>{{ __('Booking No.') }}:</strong> {{ '#' . $booking->order_number }}</p>
        <p><strong>{{ __('Service') }}:</strong> {{ $invoice->service_title ?: __('Service appointment') }}</p>
        <p><strong>{{ __('Vendor') }}:</strong> {{ $vendorName }}</p>
        <p><strong>{{ __('Appointment Date') }}:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</p>
        <p><strong>{{ __('Appointment Time') }}:</strong> {{ $booking->start_date }} - {{ $booking->end_date }}</p>
      </div>
    </div>

    @if (!empty($bankAccount))
      <div class="section-title">{{ __('Bank Details') }}</div>
      <table>
        <tbody>
          <tr>
            <th>{{ __('Bank Name') }}</th>
            <td>{{ $bankAccount->bank_name ?: '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('Account Name') }}</th>
            <td>{{ $bankAccount->account_holder_name ?: '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('Account Number') }}</th>
            <td>{{ $bankAccount->account_number ?: '-' }}</td>
          </tr>
        </tbody>
      </table>
    @endif

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
          <td>{{ $invoice->service_title ?: __('Service appointment') }}</td>
          <td>{{ $money($invoice->amount) }}</td>
        </tr>
        @if ($invoice->vat !== null)
          <tr>
            <td>{{ __('VAT / Tax') }}</td>
            <td>{{ $money($invoice->vat) }}</td>
          </tr>
        @endif
      </tbody>
    </table>

    <table class="summary">
      <tbody>
        <tr>
          <th>{{ __('Subtotal') }}</th>
          <td>{{ $money($invoice->amount) }}</td>
        </tr>
        @if ($invoice->vat !== null)
          <tr>
            <th>{{ __('VAT / Tax') }}</th>
            <td>{{ $money($invoice->vat) }}</td>
          </tr>
        @endif
        <tr class="total-row">
          <th>{{ __('Total') }}</th>
          <td>{{ $money($invoice->total_amount) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>

</html>
