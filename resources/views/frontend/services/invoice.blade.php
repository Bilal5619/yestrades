<!DOCTYPE html>
<html>

<head lang="{{ $currentLanguageInfo->code }}"
      @if ($currentLanguageInfo->direction == 1) dir="rtl" @endif>

  <meta charset="UTF-8">

  <title>{{ 'Service Booking Invoice | ' . config('app.name') }}</title>

  <style>
    body {
      color: #222;
      font-family: DejaVu Sans, sans-serif;
      font-size: 13px;
      line-height: 1.45;
    }

    .invoice {
      padding: 28px;
    }

    .header {
      border-bottom: 2px solid #222;
      margin-bottom: 24px;
      padding-bottom: 16px;
    }

    .logo {
      max-width: 160px;
    }

    .title {
      font-size: 24px;
      font-weight: 700;
      margin: 0 0 6px;
      text-transform: uppercase;
    }

    .muted {
      color: #666;
    }

    .company-details {
      margin-top: 8px;
      font-size: 11px;
      line-height: 1.4;
    }

    .grid {
      clear: both;
      width: 100%;
    }

    .half {
      float: left;
      width: 48%;
    }

    .half.right {
      float: right;
    }

    .section-title {
      border-bottom: 1px solid #ddd;
      font-size: 15px;
      font-weight: 700;
      margin: 18px 0 10px;
      padding-bottom: 6px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 9px;
      text-align: left;
      vertical-align: top;
    }

    th {
      background: #f5f5f5;
    }

    .summary {
      margin-left: auto;
      margin-top: 18px;
      width: 48%;
    }

    .total-row th,
    .total-row td {
      background: #222;
      color: #fff;
      font-weight: 700;
    }

    .vat-note {
      margin-top: 15px;
      font-size: 11px;
      color: #555;
    }

    .clearfix::after {
      clear: both;
      content: "";
      display: table;
    }
  </style>
</head>

<body>

@php

    $position = $orderInfo->currency_text_position;
    $currency = $orderInfo->currency_text;

    $serviceInfo = App\Models\Services\ServiceContent::where(
        'service_id',
        $orderInfo->service_id
    )
    ->select('name', 'address')
    ->first();

    $invoiceItemName =
        ($orderInfo->item_type ?? 'service') === 'package'
            ? ($orderInfo->package_name ?? __('Service Package'))
            : ($serviceInfo
                ? $serviceInfo->name
                : __('Service Booking'));

    $includedServices =
        ($orderInfo->item_type ?? 'service') === 'package'
            ? collect($orderInfo->package_included_services ?? [])
                ->pluck('name')
                ->filter()
                ->implode(', ')
            : '';

    /*
    |--------------------------------------------------------------------------
    | VAT
    |--------------------------------------------------------------------------
    |
    | customer_paid is already VAT inclusive.
    | We DO NOT add VAT to the booking total.
    |
    */

    $vatBreakdown = \App\Support\Vat::fromInclusive(
        $orderInfo->customer_paid
    );

    $grossAmount = number_format($vatBreakdown['gross'], 2);
    $netAmount = number_format($vatBreakdown['net'], 2);
    $vatAmount = number_format($vatBreakdown['vat'], 2);

    $formatMoney = function ($amount) use ($position, $currency) {

        return
            ($position == 'left' ? $currency . ' ' : '')
            . $amount
            . ($position == 'right' ? ' ' . $currency : '');

    };

    $formattedGross = $formatMoney($grossAmount);
    $formattedNet = $formatMoney($netAmount);
    $formattedVat = $formatMoney($vatAmount);

@endphp


<div class="invoice">


  {{-- HEADER --}}
  <div class="header clearfix">

    <div class="half">

      <img
        class="logo"
        src="{{ public_path('assets/img/' . $websiteInfo->logo) }}"
        alt="YesTrades logo"
      >

      <div class="company-details">

        <strong>{{ config('company.name') }}</strong><br>

        {{ config('company.address_line_1') }}<br>
        {{ config('company.address_line_2') }}<br>
        {{ config('company.city') }}<br>
        {{ config('company.postcode') }}<br>
        {{ config('company.country') }}<br><br>

        <strong>VAT Registration No:</strong>
        {{ config('company.vat_number') }}

      </div>

    </div>


    <div class="half right">

      <h1 class="title">
        {{ __('Service Booking Invoice') }}
      </h1>

      <p>
        <strong>{{ __('Invoice No.') }}:</strong>
        {{ $orderInfo->order_number }}
      </p>

      <p>
        <strong>{{ __('Issued') }}:</strong>

        {{
            \Carbon\Carbon::parse(
                $orderInfo->created_at
            )->format('M d, Y')
        }}

      </p>

    </div>

  </div>


  {{-- CUSTOMER + BOOKING --}}
  <div class="grid clearfix">

    <div class="half">

      <div class="section-title">
        {{ __('Bill To') }}
      </div>

      <p>
        <strong>{{ $orderInfo->customer_name }}</strong>
      </p>

      <p>{{ $orderInfo->customer_email }}</p>

      <p>{{ $orderInfo->customer_phone }}</p>

      <p>{{ $orderInfo->customer_address }}</p>

      @if ($orderInfo->customer_zip_code)

        <p>
          {{ __('Postcode') }}:
          {{ $orderInfo->customer_zip_code }}
        </p>

      @endif

      <p>{{ $orderInfo->customer_country }}</p>

    </div>


    <div class="half right">

      <div class="section-title">
        {{ __('Booking Details') }}
      </div>

      <p>
        <strong>{{ __('Booking No.') }}:</strong>
        #{{ $orderInfo->order_number }}
      </p>

      <p>

        <strong>

          {{
              ($orderInfo->item_type ?? 'service') === 'package'
                  ? __('Package')
                  : __('Service')
          }}:

        </strong>

        {{ $invoiceItemName }}

      </p>


      @if ($includedServices !== '')

        <p>
          <strong>{{ __('Includes') }}:</strong>
          {{ $includedServices }}
        </p>

      @endif


      @if (!empty($orderInfo->booking_date))

        <p>

          <strong>{{ __('Appointment Date') }}:</strong>

          {{
              \Carbon\Carbon::parse(
                  $orderInfo->booking_date
              )->format('M d, Y')
          }}

        </p>

      @else

        <p>

          <strong>{{ __('Appointment Date') }}:</strong>

          {{ __('Awaiting appointment selection') }}

        </p>

      @endif


      @if (!empty($orderInfo->start_date) && !empty($orderInfo->end_date))

        <p>

          <strong>{{ __('Appointment Time') }}:</strong>

          {{ $orderInfo->start_date }}
          -
          {{ $orderInfo->end_date }}

        </p>

      @endif

@php
  $jobAddress = !empty($orderInfo->site_address)
      ? $orderInfo->site_address
      : $orderInfo->customer_address;

  $jobPostcode = !empty($orderInfo->site_zip_code)
      ? $orderInfo->site_zip_code
      : $orderInfo->customer_zip_code;

  $jobCountry = !empty($orderInfo->site_country)
      ? $orderInfo->site_country
      : $orderInfo->customer_country;
@endphp

@if ($jobAddress)
  <p>
    <strong>{{ __('Property / Job Location') }}:</strong>
    {{ $jobAddress }}
    @if ($jobPostcode)
      , {{ $jobPostcode }}
    @endif
    @if ($jobCountry)
      , {{ $jobCountry }}
    @endif
  </p>
@endif

    </div>

  </div>


  {{-- INVOICE ITEMS --}}
  <div class="section-title">
    {{ __('Invoice Items') }}
  </div>


  <table>

    <thead>

      <tr>

        <th>{{ __('Description') }}</th>

        <th>{{ __('Qty') }}</th>

        <th>{{ __('Amount Incl. VAT') }}</th>

      </tr>

    </thead>


    <tbody>

      <tr>

        <td>

          {{ $invoiceItemName }}

          @if ($includedServices !== '')

            <br>

            <small>

              {{ __('Includes') }}:
              {{ $includedServices }}

            </small>

          @endif

        </td>


        <td>
          {{ $orderInfo->max_person ?: 1 }}
        </td>


        <td>
          {{ $formattedGross }}
        </td>

      </tr>

    </tbody>

  </table>


  {{-- TOTALS --}}
  <table class="summary">

    <tbody>

      <tr>

        <th>
          {{ __('Subtotal (Excl. VAT)') }}
        </th>

        <td>
          {{ $formattedNet }}
        </td>

      </tr>


      <tr>

        <th>
          VAT ({{ number_format($vatBreakdown['rate'], 0) }}%)
        </th>

        <td>
          {{ $formattedVat }}
        </td>

      </tr>


      <tr>

        <th>
          {{ __('Paid Amount') }}
        </th>

        <td>
          {{ $formattedGross }}
        </td>

      </tr>

      <tr>

        <th>
          {{ __('Payment Status') }}
        </th>

        <td>
          {{ ucfirst($orderInfo->payment_status) }}
        </td>

      </tr>


      <tr>

        <th>
          {{ __('Order Status') }}
        </th>

        <td>
          {{ ucfirst($orderInfo->order_status) }}
        </td>

      </tr>


      <tr class="total-row">

        <th>
          {{ __('Total (Incl. VAT)') }}
        </th>

        <td>
          {{ $formattedGross }}
        </td>

      </tr>

    </tbody>

  </table>


  <div class="vat-note">

    All prices are inclusive of VAT at
    {{ number_format($vatBreakdown['rate'], 0) }}%.

    VAT Registration No:
    {{ config('company.vat_number') }}

  </div>


</div>

</body>

</html>