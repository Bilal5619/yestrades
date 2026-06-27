<!DOCTYPE html>
<html>

<head lang="{{ $currentLanguageInfo->code }}" @if ($currentLanguageInfo->direction == 1) dir="rtl" @endif>
  <meta charset="UTF-8">
  <title>{{ 'Product Invoice | ' . config('app.name') }}</title>
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
    $formatMoney = function ($amount) use ($position, $currency) {
        return ($position == 'left' ? $currency . ' ' : '') . number_format((float) $amount, 2) . ($position == 'right' ? ' ' . $currency : '');
    };
    $subtotal = floatval($orderInfo->total) - floatval($orderInfo->discount);
  @endphp

  <div class="invoice">
    <div class="header clearfix">
      <div class="half">
        <img class="logo" src="{{ public_path('assets/img/' . $websiteInfo->logo) }}" alt="website logo">
        <p class="muted">{{ $websiteInfo->website_title }}</p>
      </div>
      <div class="half right">
        <h1 class="title">{{ __('Product Order Invoice') }}</h1>
        <p><strong>{{ __('Invoice No.') }}:</strong> {{ $orderInfo->order_number }}</p>
        <p><strong>{{ __('Issued') }}:</strong> {{ date_format($orderInfo->created_at, 'M d, Y') }}</p>
      </div>
    </div>

    <div class="clearfix">
      <div class="half">
        <div class="section-title">{{ __('Billing Details') }}</div>
        <p><strong>{{ $orderInfo->billing_name }}</strong></p>
        <p>{{ $orderInfo->billing_email }}</p>
        <p>{{ $orderInfo->billing_phone }}</p>
        <p>{{ $orderInfo->billing_address }}</p>
        <p>{{ $orderInfo->billing_city }}, {{ $orderInfo->billing_state }}</p>
        <p>{{ $orderInfo->billing_country }}</p>
      </div>
      <div class="half right">
        <div class="section-title">{{ __('Order Details') }}</div>
        <p><strong>{{ __('Order No.') }}:</strong> {{ '#' . $orderInfo->order_number }}</p>
        <p><strong>{{ __('Payment Method') }}:</strong> {{ $orderInfo->payment_method }}</p>
        <p><strong>{{ __('Payment Status') }}:</strong> {{ ucfirst($orderInfo->payment_status) }}</p>
        <p><strong>{{ __('Order Status') }}:</strong> {{ ucfirst($orderInfo->order_status) }}</p>
      </div>
    </div>

    <div class="section-title">{{ __('Ordered Products') }}</div>
    <table>
      <thead>
        <tr>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Unit Price') }}</th>
          <th>{{ __('Quantity') }}</th>
          <th>{{ __('Total Price') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($productList as $product)
          @php
            $price = floatval($product['price']);
            $quantity = max(1, intval($product['quantity']));
            $unitPrice = $price / $quantity;
          @endphp
          <tr>
            <td>{{ $product['title'] }}</td>
            <td>{{ $formatMoney($unitPrice) }}</td>
            <td>{{ $product['quantity'] }}</td>
            <td>{{ $formatMoney($price) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <table class="summary">
      <tbody>
        <tr>
          <th>{{ __('Total') }}</th>
          <td>{{ $formatMoney($orderInfo->total) }}</td>
        </tr>
        <tr>
          <th>{{ __('Discount') }}</th>
          <td>{{ $formatMoney($orderInfo->discount) }}</td>
        </tr>
        <tr>
          <th>{{ __('Subtotal') }}</th>
          <td>{{ $formatMoney($subtotal) }}</td>
        </tr>
        <tr>
          <th>{{ __('Shipping Cost') }}</th>
          <td>{{ is_null($orderInfo->shipping_cost) ? '-' : $formatMoney($orderInfo->shipping_cost) }}</td>
        </tr>
        <tr>
          <th>{{ __('Tax') }} ({{ $taxData->product_tax_amount }}%)</th>
          <td>{{ $formatMoney($orderInfo->tax) }}</td>
        </tr>
        <tr class="total-row">
          <th>{{ __('Grand Total') }}</th>
          <td>{{ $formatMoney($orderInfo->grand_total) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>

</html>
