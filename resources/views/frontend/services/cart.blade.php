@php
  $version = $basicInfo->theme_version ?? 1;
  $position = $currencyInfo->base_currency_symbol_position ?? 'left';
  $symbol = $currencyInfo->base_currency_symbol ?? '&pound;';
  $displayPrice = function ($amount) use ($position, $symbol) {
      $value = number_format((float) $amount, 2);
      return $position === 'left' ? $symbol . $value : $value . $symbol;
  };
  $displayCartItemPrice = function ($item) use ($displayPrice) {
      return ($item['price_status'] ?? null) === 'pending' && (float) ($item['price'] ?? 0) <= 0
          ? __('Price pending')
          : $displayPrice($item['price'] ?? 0);
  };
  $authUser = $authUser ?? Auth::guard('web')->user();
  $serviceCouponCode = $serviceCouponCode ?? null;
  $serviceCouponDiscount = (float) ($serviceCouponDiscount ?? 0);
  $serviceCouponType = $serviceCouponType ?? null;
  $serviceCouponValue = (float) ($serviceCouponValue ?? 0);
  $cartPayableTotal = max(0, (float) $cartTotal - $serviceCouponDiscount);
@endphp

@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) ? ($pageHeading->cart_page_title ?? __('Service Cart')) : __('Service Cart') }}
@endsection

@push('scripts')
  <style>
    /* Cart page brand accent — scoped to emerald to match the rest of the redesign */
    .service-cart-page {
      --color-primary: #0e9f6e;
      --color-primary-rgb: 14, 159, 110;
    }

    .service-cart-page {
      background: #fbfbfa;
    }

    .service-cart-heading {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 30px;
    }

    .service-cart-heading h2 {
      margin: 0 0 8px;
      color: #10121a;
      font-size: 34px;
      line-height: 1.15;
      font-weight: 900;
      letter-spacing: 0;
    }

    .service-cart-heading p {
      margin: 0;
      color: #747984;
      font-size: 15px;
      line-height: 1.7;
    }

    .service-cart-count {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 12px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.08);
      border: 1px solid rgba(var(--color-primary-rgb), 0.16);
      border-radius: 999px;
      font-size: 13px;
      font-weight: 800;
      white-space: nowrap;
    }

    .service-checkout-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 360px;
      gap: 28px;
      align-items: start;
    }

    .checkout-panel,
    .summary-panel {
      background: #ffffff;
      border: 1px solid #e7e8eb;
      border-radius: 8px;
      box-shadow: 0 18px 45px rgba(15, 18, 28, 0.05);
    }

    .checkout-panel {
      padding: 28px;
    }

    .summary-panel {
      position: sticky;
      top: 92px;
      padding: 24px;
    }

    .checkout-step {
      display: grid;
      grid-template-columns: 42px minmax(0, 1fr);
      gap: 16px;
      padding: 22px 0;
      border-bottom: 1px solid #ecebea;
    }

    .checkout-step:first-child {
      padding-top: 0;
    }

    .checkout-step:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }

    .checkout-step[data-cart-step-panel] {
      display: none;
    }

    .checkout-step[data-cart-step-panel].is-active {
      display: grid;
    }

    .checkout-step-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 42px;
      height: 42px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.08);
      border-radius: 8px;
      font-size: 16px;
    }

    .checkout-step h3,
    .summary-panel h3 {
      margin: 0 0 8px;
      color: #10121a;
      font-size: 20px;
      line-height: 1.2;
      font-weight: 900;
      letter-spacing: 0;
    }

    .checkout-step p {
      margin: 0;
      color: #747984;
      font-size: 14px;
      line-height: 1.65;
    }

    .service-cart-items {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-top: 16px;
    }

    .service-cart-item {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 16px;
      padding: 16px;
      background: #fbfbfa;
      border: 1px solid #ecebea;
      border-radius: 8px;
    }

    .service-cart-item h4 {
      margin: 0 0 8px;
      color: #171923;
      font-size: 16px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .service-cart-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px 12px;
      margin: 0;
      padding: 0;
      list-style: none;
      color: #626773;
      font-size: 13px;
      line-height: 1.45;
    }

    .cart-postcode-panel {
      margin: 4px 0 20px;
      padding: 16px;
      background: #f6f8fb;
      border: 1px solid #e1e6ee;
      border-radius: 10px;
    }

    .cart-postcode-input-row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: stretch;
    }

    .cart-postcode-input-row .form-control {
      flex: 1 1 220px;
    }

    .cart-postcode-note {
      margin-top: 10px;
      font-size: 13px;
    }

    .cart-postcode-note.is-found {
      color: #1f9d55;
    }

    .cart-postcode-note.is-standard {
      color: #6b7280;
    }

    .cart-question-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-top: 16px;
    }

    .cart-question-card {
      padding: 14px;
      background: #ffffff;
      border: 1px solid #e7e8eb;
      border-radius: 8px;
    }

    .cart-question-card h5 {
      margin: 0 0 10px;
      color: #171923;
      font-size: 14px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .cart-question-card h5 span {
      color: var(--color-primary);
    }

    .cart-question-options {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .cart-question-option {
      display: flex;
      align-items: center;
      gap: 9px;
      margin: 0;
      padding: 10px 12px;
      color: #343946;
      background: #fbfbfa;
      border: 1px solid #ecebea;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.4;
    }

    .cart-question-option:has(input:checked) {
      color: #171923;
      background: rgba(var(--color-primary-rgb), 0.07);
      border-color: var(--color-primary);
      box-shadow: 0 8px 18px rgba(var(--color-primary-rgb), 0.08);
    }

    .cart-question-option input,
    .cart-question-card input,
    .cart-question-card textarea {
      accent-color: var(--color-primary);
    }

    .cart-question-card .form-control {
      min-height: 44px;
      font-size: 14px;
    }

    .service-cart-page .question-card {
      border: 1px solid #e7e8eb;
      border-left: 4px solid var(--color-primary);
      border-radius: 8px;
      box-shadow: none;
    }

    .service-cart-page .question-card.is-sequence-hidden {
      display: none;
    }

    .service-cart-page .question-card-quantity {
      border-left: 1px solid #e7e8eb;
    }

    .service-cart-page .booking-question-options {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .service-cart-page .booking-question-option {
      display: flex;
      align-items: center;
      gap: 13px;
      margin: 0;
      padding: 14px 16px;
      color: #171923;
      background: #ffffff;
      border: 1px solid #dde2ea;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 800;
      line-height: 1.35;
      cursor: pointer;
      transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .service-cart-page .booking-question-option:hover,
    .service-cart-page .booking-question-option.is-selected,
    .service-cart-page .booking-question-option:has(.question-input:checked) {
      background: rgba(var(--color-primary-rgb), 0.06);
      border-color: var(--color-primary);
      box-shadow: 0 8px 18px rgba(var(--color-primary-rgb), 0.08);
    }

    .service-cart-page .booking-question-option input {
      width: 18px;
      height: 18px;
      flex: 0 0 auto;
      accent-color: var(--color-primary);
    }

    .service-cart-page .booking-question-option-image-wrap {
      position: relative;
      display: inline-flex;
      flex: 0 0 58px;
      width: 58px;
      height: 58px;
    }

    .service-cart-page .booking-question-option-image {
      width: 58px;
      height: 58px;
      flex: 0 0 58px;
      object-fit: cover;
      border: 1px solid #e1e5ec;
      border-radius: 8px;
      background: #ffffff;
    }

    .service-cart-page .booking-question-option-preview {
      position: absolute;
      left: calc(100% + 12px);
      top: 50%;
      z-index: 9999;
      width: 260px;
      max-width: min(260px, 70vw);
      padding: 8px;
      background: #ffffff;
      border: 1px solid #dfe4ec;
      border-radius: 10px;
      box-shadow: 0 18px 46px rgba(16, 18, 26, 0.22);
      opacity: 0;
      pointer-events: none;
      transform: translateY(-50%) scale(0.96);
      transform-origin: left center;
      transition: opacity 0.16s ease, transform 0.16s ease;
    }

    .service-cart-page .booking-question-option-preview img {
      display: block;
      width: 100%;
      height: auto;
      max-height: 300px;
      object-fit: contain;
      border-radius: 7px;
    }

    @media (hover: hover) and (pointer: fine) {
      .service-cart-page .booking-question-option-image-wrap:hover .booking-question-option-preview,
      .service-cart-page .booking-question-option-image-wrap:focus-within .booking-question-option-preview {
        opacity: 1;
        transform: translateY(-50%) scale(1);
      }
    }

    .service-cart-page .booking-question-option-text {
      display: block;
    }

    .service-cart-price {
      color: #10121a;
      font-size: 18px;
      font-weight: 900;
      white-space: nowrap;
    }

    .remove-service-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      margin-top: 10px;
      color: #9a1e36;
      background: #fff0f3;
      border: 1px solid #ffd5dd;
      border-radius: 8px;
    }

    .service-note-list {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin-top: 16px;
    }

    .service-note {
      padding: 14px;
      color: #555b67;
      background: #fbfbfa;
      border: 1px solid #ecebea;
      border-radius: 8px;
      font-size: 13px;
      line-height: 1.55;
    }

    .service-note strong {
      display: block;
      margin-bottom: 4px;
      color: #10121a;
      font-size: 14px;
      font-weight: 900;
    }

    .summary-items {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin: 18px 0;
      padding: 0;
      list-style: none;
    }

    .summary-items li {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      color: #555b67;
      font-size: 14px;
      line-height: 1.45;
    }

    .summary-items strong {
      color: #10121a;
      font-weight: 900;
      text-align: right;
      white-space: nowrap;
    }

    .summary-coupon {
      margin: 18px 0;
      padding: 14px;
      border: 1px solid #ecebea;
      border-radius: 8px;
      background: #fbfbfa;
    }

    .summary-coupon-form {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 8px;
    }

    .summary-coupon-input {
      width: 100%;
      min-height: 42px;
      padding: 10px 12px;
      border: 1px solid #dfe2e7;
      border-radius: 8px;
      color: #10121a;
      font-size: 14px;
      font-weight: 700;
      background: #ffffff;
    }

    .summary-coupon-btn {
      min-height: 42px;
      padding: 10px 14px;
      border: 0;
      border-radius: 8px;
      color: #ffffff;
      background: var(--color-primary);
      font-size: 13px;
      font-weight: 900;
      line-height: 1;
      cursor: pointer;
    }

    .summary-coupon-applied {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-top: 10px;
      color: #14825f;
      font-size: 13px;
      font-weight: 800;
    }

    .summary-coupon-remove {
      border: 0;
      color: #d91743;
      background: transparent;
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
    }

    .summary-coupon-message {
      display: none;
      margin-top: 8px;
      font-size: 12px;
      font-weight: 800;
      line-height: 1.4;
    }

    .summary-coupon-message.is-error {
      display: block;
      color: #d91743;
    }

    .summary-coupon-message.is-success {
      display: block;
      color: #14825f;
    }

    .summary-total {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding-top: 18px;
      border-top: 1px solid #ecebea;
      color: #10121a;
      font-size: 20px;
      font-weight: 900;
    }

    .summary-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 22px;
    }

    .cart-primary-btn,
    .cart-secondary-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 46px;
      padding: 13px 18px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 900;
      line-height: 1;
      text-decoration: none;
    }

    .cart-primary-btn {
      color: #ffffff;
      background: var(--color-primary);
      border: 1px solid var(--color-primary);
    }

    .cart-secondary-btn {
      color: #1f2430;
      background: #ffffff;
      border: 1px solid #e1e3e7;
    }

    .cart-primary-btn:hover {
      color: #ffffff;
    }

    .cart-secondary-btn:hover {
      color: var(--color-primary);
      border-color: rgba(var(--color-primary-rgb), 0.28);
    }

    .empty-service-cart {
      max-width: 680px;
      margin: 0 auto;
      padding: 56px 28px;
      text-align: center;
    }

    .empty-service-cart i {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      margin-bottom: 18px;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.08);
      border-radius: 50%;
      font-size: 24px;
    }

    .cart-step-actions {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-top: 20px;
      padding-top: 18px;
      border-top: 1px solid #ecebea;
    }

    .cart-step-alert {
      display: none;
      margin-top: 14px;
      padding: 12px 14px;
      color: #9a1e36;
      background: #fff0f3;
      border: 1px solid #ffd5dd;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
    }

    .cart-date-panel .section-title {
      text-align: center;
      margin-bottom: 24px;
    }

    .cart-date-panel .section-title h3 {
      margin: 0 0 8px;
      color: #10121a;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .cart-date-panel .section-title p {
      margin: 0;
      color: #747984;
      font-size: 14px;
    }

    .cart-date-panel .urgency-surcharge-info {
      padding: 18px;
      margin-bottom: 24px;
      color: #6f4b12;
      background: #fff8e8;
      border: 1px solid #ffd58a;
      border-radius: 8px;
    }

    .cart-date-panel .urgency-surcharge-info-title {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
      color: #4c3510;
      font-size: 15px;
      font-weight: 900;
    }

    .cart-date-panel .urgency-surcharge-tiers {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px;
      margin-top: 14px;
    }

    .cart-date-panel .urgency-surcharge-tier {
      padding: 12px;
      background: #ffffff;
      border: 1px solid #f3d29a;
      border-radius: 8px;
      color: #6f4b12;
      cursor: pointer;
      font: inherit;
      text-align: left;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
      width: 100%;
    }

    .cart-date-panel .urgency-surcharge-tier strong,
    .cart-date-panel .urgency-surcharge-tier span {
      display: block;
    }

    .cart-date-panel .urgency-surcharge-tier small {
      display: block;
      margin-top: 6px;
      color: inherit;
      font-size: 12px;
      font-weight: 800;
      opacity: 0.78;
    }

    .cart-date-panel .urgency-surcharge-tier:hover,
    .cart-date-panel .urgency-surcharge-tier:focus {
      border-color: var(--color-primary);
      box-shadow: 0 8px 18px rgba(var(--color-primary-rgb), 0.14);
      outline: none;
    }

    .cart-date-panel .urgency-surcharge-tier.is-selected {
      color: #ffffff;
      background: var(--color-primary);
      border-color: var(--color-primary);
      box-shadow: 0 10px 22px rgba(var(--color-primary-rgb), 0.22);
    }

    .cart-date-field:disabled,
    .cart-time-field:disabled {
      color: #7b8190;
      background: #eef1f5;
      cursor: not-allowed;
    }

    .cart-urgent-time-note {
      display: none;
      margin: 10px 0 0;
      color: #5f4620;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.5;
    }

    .cart-scheduling-options {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      max-width: 720px;
      margin: 0 auto 22px;
    }

    .cart-scheduling-option {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      min-height: 100px;
      margin: 0;
      padding: 16px;
      color: #252a35;
      background: #ffffff;
      border: 1px solid #dfe4ec;
      border-radius: 9px;
      cursor: pointer;
      transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .cart-scheduling-option:hover,
    .cart-scheduling-option.is-selected {
      background: rgba(var(--color-primary-rgb), .045);
      border-color: var(--color-primary);
      box-shadow: 0 8px 20px rgba(var(--color-primary-rgb), .08);
    }

    .cart-scheduling-option input {
      width: 19px;
      height: 19px;
      margin-top: 2px;
      flex: 0 0 auto;
      accent-color: var(--color-primary);
    }

    .cart-scheduling-option strong,
    .cart-scheduling-option small {
      display: block;
    }

    .cart-scheduling-option strong {
      margin-bottom: 5px;
      color: #151822;
      font-size: 14px;
      font-weight: 900;
    }

    .cart-scheduling-option small {
      color: #737987;
      font-size: 12px;
      font-weight: 600;
      line-height: 1.5;
    }

    .cart-tenant-panel {
      max-width: 720px;
      margin: 0 auto 18px;
      padding: 20px;
      background: #f8fafc;
      border: 1px solid #dfe5ed;
      border-radius: 10px;
    }

    .cart-tenant-panel h4 {
      margin: 0 0 5px;
      color: #171923;
      font-size: 17px;
      font-weight: 900;
    }

    .cart-tenant-panel > p {
      margin-bottom: 16px;
      color: #737987;
      font-size: 13px;
    }

    .cart-tenant-fields {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .cart-tenant-field.is-full {
      grid-column: 1 / -1;
    }

    .cart-tenant-field input,
    .cart-tenant-field textarea {
      width: 100%;
      border: 1px solid #dbe1ea;
      border-radius: 8px;
      background: #ffffff;
    }

    .cart-tenant-field input {
      min-height: 48px;
      padding: 10px 13px;
    }

    .cart-tenant-field textarea {
      min-height: 92px;
      padding: 12px 13px;
      resize: vertical;
    }

    .cart-tenant-contact-note {
      margin: 12px 0 0;
      color: #5f6673;
      font-size: 12px;
      font-weight: 700;
    }

    @media (max-width: 575px) {
      .cart-scheduling-options,
      .cart-tenant-fields {
        grid-template-columns: 1fr;
      }

      .cart-tenant-field.is-full {
        grid-column: auto;
      }
    }

    .cart-date-picker-wrap {
      max-width: 560px;
      margin: 0 auto 18px;
      padding: 22px;
      background: #f8f9fb;
      border: 1px solid #e1e6ee;
      border-radius: 8px;
    }

    .cart-date-field,
    .cart-time-field {
      width: 100%;
      min-height: 52px;
      padding: 0 14px;
      color: #171923;
      background: #ffffff;
      border: 1px solid #dbe1ea;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 700;
    }

    .cart-date-fields {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .cart-field-label {
      display: block;
      margin-bottom: 8px;
      color: #343946;
      font-size: 13px;
      font-weight: 900;
    }

    .cart-auth-form {
      max-width: 560px;
      margin-top: 24px;
    }

    .cart-auth-tabs {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 8px;
      max-width: 720px;
      margin-top: 24px;
      padding: 6px;
      background: #f6f7f9;
      border: 1px solid #e4e8ef;
      border-radius: 8px;
    }

    @media (max-width: 575px) {
      .cart-auth-tabs {
        grid-template-columns: minmax(0, 1fr);
      }
    }

    .cart-guest-note {
      margin: 20px 0;
      color: #6c7280;
      font-size: 14px;
      line-height: 1.6;
    }

    .cart-auth-tab {
      min-height: 44px;
      padding: 8px 12px;
      color: #424856;
      background: transparent;
      border: 0;
      border-radius: 7px;
      font: inherit;
      font-size: 14px;
      font-weight: 900;
      cursor: pointer;
    }

    .cart-auth-tab.is-active {
      color: #ffffff;
      background: var(--color-primary);
      box-shadow: 0 10px 22px rgba(var(--color-primary-rgb), 0.18);
    }

    .cart-auth-panel {
      display: none;
    }

    .cart-auth-panel.is-active {
      display: block;
    }

    .cart-auth-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .cart-auth-grid .form-group.is-full {
      grid-column: 1 / -1;
    }

    .cart-auth-form .link {
      color: #6f7480;
      font-size: 14px;
      line-height: 1.6;
    }

    .cart-auth-form .link a {
      color: var(--color-primary);
      font-weight: 800;
    }

    .cart-payment-area {
      margin-top: 18px;
    }

    .cart-payment-area .section-title {
      text-align: center;
      margin-bottom: 18px;
    }

    .cart-payment-area .section-title h3 {
      margin: 0 0 6px;
      color: #10121a;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: 0;
    }

    .cart-payment-area .payment-methods-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 12px;
      margin-top: 14px;
    }

    .cart-payment-area .payment-method-card {
      position: relative;
      min-height: 76px;
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 15px 16px;
      color: #111827;
      background: #ffffff;
      border: 1px solid #e3e7ed;
      border-radius: 8px;
      cursor: pointer;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .cart-payment-area .payment-card-featured {
      border-color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.04);
    }

    .cart-payment-area .payment-method-card:hover,
    .cart-payment-area .payment-method-card.selected {
      border-color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.06);
      box-shadow: 0 10px 22px rgba(var(--color-primary-rgb), 0.12);
      transform: translateY(-1px);
    }

    .cart-payment-area .payment-icon {
      flex: 0 0 42px;
      width: 42px;
      height: 42px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: var(--color-primary);
      background: rgba(var(--color-primary-rgb), 0.10);
      border-radius: 50%;
      font-size: 22px;
    }

    .cart-payment-area .payment-name,
    .cart-payment-area .payment-name-featured {
      flex: 1 1 auto;
      color: #111827;
      font-size: 14px;
      font-weight: 800;
      line-height: 1.25;
    }

    .cart-payment-area .payment-brands {
      margin-left: auto;
      color: #4b5563;
      white-space: nowrap;
    }

    .cart-payment-area .payment-check {
      color: var(--color-primary);
      opacity: 0;
    }

    .cart-payment-area .payment-method-card.selected .payment-check {
      opacity: 1;
    }

    .cart-payment-area .gateway-details,
    .cart-payment-area .offline-gateway-info,
    .cart-payment-area #stripe-element,
    .cart-payment-area #card-payment-info {
      margin-top: 16px;
    }

    /* Booking review / summary step */
    .cart-summary-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      margin-top: 26px;
    }

    .cart-summary-services-card,
    .cart-summary-payment-card {
      grid-column: 1 / -1;
    }

    .cart-summary-card {
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      background: #fff;
      padding: 18px 20px;
      box-shadow: 0 4px 18px rgba(17, 24, 39, 0.05);
    }

    .cart-summary-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 12px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eef1f5;
    }

    .cart-summary-card-head h5 {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
      font-size: 14px;
      font-weight: 700;
      color: #111827;
    }

    .cart-summary-card-head h5 i {
      color: var(--color-primary, #1971ff);
    }

    .cart-summary-edit {
      border: 0;
      background: transparent;
      padding: 0;
      font-size: 12px;
      font-weight: 600;
      color: var(--color-primary, #1971ff);
      cursor: pointer;
    }

    .cart-summary-edit:hover {
      text-decoration: underline;
    }

    .cart-summary-row {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      padding: 8px 0;
      border-bottom: 1px solid #f3f4f6;
    }

    .cart-summary-row:last-child {
      border-bottom: 0;
      padding-bottom: 0;
    }

    .cart-summary-label {
      flex: 0 0 42%;
      color: #6b7280;
      font-size: 13px;
      font-weight: 600;
    }

    .cart-summary-value {
      flex: 1 1 auto;
      text-align: right;
      color: #111827;
      font-size: 13px;
      font-weight: 700;
      line-height: 1.45;
      overflow-wrap: anywhere;
    }

    .cart-summary-service {
      padding: 14px 16px;
      border: 1px solid #eef1f5;
      border-radius: 12px;
      background: #fafbfc;
      margin-bottom: 12px;
    }

    .cart-summary-service:last-child {
      margin-bottom: 0;
    }

    .cart-summary-service-head {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 8px;
    }

    .cart-summary-service-name {
      font-size: 14px;
      font-weight: 700;
      color: #111827;
    }

    .cart-summary-service-total {
      font-size: 14px;
      font-weight: 700;
      color: var(--color-primary, #1971ff);
      white-space: nowrap;
    }

    .cart-summary-addon {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      padding: 5px 0;
      font-size: 12.5px;
    }

    .cart-summary-addon span:first-child {
      color: #6b7280;
      font-weight: 600;
    }

    .cart-summary-addon span:first-child small {
      display: block;
      color: #9ca3af;
      font-weight: 500;
    }

    .cart-summary-addon span:last-child {
      color: #16a34a;
      font-weight: 700;
      white-space: nowrap;
    }

    .cart-summary-total-row {
      margin-top: 12px;
      padding: 12px 16px !important;
      border: 0 !important;
      border-radius: 10px;
      background: var(--color-primary, #1971ff);
    }

    .cart-summary-total-row .cart-summary-label,
    .cart-summary-total-row .cart-summary-value {
      color: #fff;
      font-size: 15px;
    }

    .cart-summary-total-row .cart-summary-label {
      flex: 0 0 auto;
    }

    @media (max-width: 575px) {
      .cart-summary-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 767px) {
      .service-checkout-grid {
        grid-template-columns: 1fr;
      }

      .summary-panel {
        position: static;
      }
    }

    @media (max-width: 767px) {
      .service-cart-page {
        padding-top: 52px !important;
        padding-bottom: 52px !important;
      }

      .service-cart-heading {
        align-items: flex-start;
        flex-direction: column;
        margin-bottom: 18px;
      }

      .service-cart-heading h2 {
        font-size: 30px;
      }

      .service-cart-heading p {
        font-size: 14px;
      }

      .checkout-panel,
      .summary-panel {
        padding: 16px;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(15, 18, 28, 0.04);
      }

      .checkout-step,
      .service-cart-item {
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .checkout-step {
        padding: 18px 0;
      }

      .checkout-step h3,
      .summary-panel h3,
      .cart-date-panel .section-title h3,
      .cart-payment-area .section-title h3 {
        font-size: 19px;
      }

      .checkout-step-icon {
        width: 38px;
        height: 38px;
      }

      .service-cart-item {
        padding: 14px;
      }

      .service-cart-price {
        text-align: left;
        font-size: 17px;
      }

      .service-note-list {
        grid-template-columns: 1fr;
      }

      .cart-date-panel .urgency-surcharge-tiers,
      .cart-date-fields,
      .cart-auth-tabs,
      .cart-auth-grid {
        grid-template-columns: 1fr;
      }

      .cart-date-picker-wrap {
        padding: 14px;
      }

      .cart-step-actions {
        flex-direction: column;
      }

      .cart-primary-btn,
      .cart-secondary-btn {
        width: 100%;
        min-height: 48px;
      }

      .cart-payment-area .payment-method-card {
        min-height: auto;
        align-items: flex-start;
        padding: 14px;
        gap: 12px;
      }

      .cart-payment-area .payment-icon {
        width: 38px;
        height: 38px;
        flex-basis: 38px;
        font-size: 19px;
      }

      .cart-payment-area .payment-brands {
        display: none;
      }

      .cart-payment-area .payment-name,
      .cart-payment-area .payment-name-featured {
        font-size: 14px;
        line-height: 1.35;
      }

      .summary-actions {
        gap: 8px;
      }

      #stripe-element {
        padding: 12px !important;
      }
    }

    @media (max-width: 420px) {
      .service-cart-page .container {
        padding-left: 14px;
        padding-right: 14px;
      }

      .checkout-panel,
      .summary-panel {
        padding: 14px;
      }

      .summary-total {
        font-size: 18px;
      }
    }
  </style>
@endpush

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb ?? null,
      'title' => !empty($pageHeading) ? ($pageHeading->cart_page_title ?? __('Service Cart')) : __('Service Cart'),
  ])

  <section class="service-cart-page pt-90 pb-80">
    <div class="container">
      @if ($cartCount === 0)
        <div class="checkout-panel empty-service-cart">
          <i class="fal fa-shopping-cart"></i>
          <h2>{{ __('Your service cart is empty') }}</h2>
          <p class="text-muted mb-4">{{ __('Add services to your cart so you can organise questions, date, information and payment in one place.') }}</p>
          <a href="{{ route('frontend.services') }}" class="cart-primary-btn">
            {{ __('Browse services') }} <i class="fal fa-long-arrow-right"></i>
          </a>
        </div>
      @else
        <div class="service-cart-heading">
          <div>
            <h2>{{ __('Service cart') }}</h2>
            <p>{{ __('Review every service, then complete the remaining booking details and payment from one checkout.') }}</p>
          </div>
          <span class="service-cart-count">
            <i class="fal fa-shopping-cart"></i>
            {{ __(':count services', ['count' => $cartCount]) }}
          </span>
        </div>

        <div class="service-checkout-grid">
          <div class="checkout-panel">
            <div class="checkout-step is-active" data-cart-step-panel="questions">
              <span class="checkout-step-icon"><i class="fal fa-list-alt"></i></span>
              <div>
                <h3>{{ __('1. Services and questions') }}</h3>
                <p>{{ __('Each service can include its own required questions and extra charges. Items already added through the booking form are shown below.') }}</p>
                @php
                  $firstCartItemForPostcode = $serviceCart->first();
                  $prefillPostcode = $firstCartItemForPostcode['site']['zip_code'] ?? ($firstCartItemForPostcode['customer']['zip_code'] ?? '');
                @endphp
                <div class="cart-postcode-panel" data-cart-postcode-panel>
                  <label class="cart-field-label" for="cart_pricing_postcode">{{ __('Your postcode') }}</label>
                  <div class="cart-postcode-input-row">
                    <input type="text" id="cart_pricing_postcode" class="form-control"
                      placeholder="{{ __('Enter postcode and press Enter') }}"
                      value="{{ $prefillPostcode }}" autocomplete="postal-code" data-cart-postcode-input>
                    <button type="button" class="cart-primary-btn" data-cart-postcode-apply>{{ __('Get price') }}</button>
                  </div>
                  <p class="cart-postcode-note text-muted mb-0" data-cart-postcode-note></p>
                </div>
                <div class="cart-step-alert" data-cart-alert="postcode">
                  {{ __('Please enter your postcode and click Get price before continuing.') }}
                </div>
                <div class="service-cart-items">
                  @foreach ($serviceCart as $item)
                    <div class="service-cart-item" data-cart-item data-base-price="{{ (float) ($item['price'] ?? 0) }}"
                      data-website-price="{{ (float) ($item['price'] ?? 0) }}"
                      data-postcode-surcharge="0"
                      data-item-type="{{ $item['item_type'] ?? 'service' }}"
                      data-price-pending="{{ ($item['price_status'] ?? null) === 'pending' ? 'true' : 'false' }}"
                      data-item-key="{{ $item['key'] }}">
                      <div>
                        <h4>{{ $item['service_name'] }}</h4>
                        @if (($item['item_type'] ?? 'service') === 'package' && !empty($item['included_services']))
                          <div class="service-cart-meta mb-2">
                            <span><strong>{{ __('Includes') }}:</strong></span>
                            @foreach ($item['included_services'] as $includedService)
                              <span>{{ $includedService['name'] ?? '' }}</span>
                            @endforeach
                          </div>
                        @endif
                        <ul class="service-cart-meta">
                          <li><i class="fal fa-calendar-alt"></i> {{ !empty($item['booking_date']) ? \Carbon\Carbon::parse($item['booking_date'])->format('d M Y') : __('Date pending') }}</li>
                          <li>
                            <i class="fal fa-clock"></i>
                            @if (!empty($item['start_time']) || !empty($item['end_time']))
                              {{ !empty($item['start_time']) ? \Carbon\Carbon::parse($item['start_time'])->format('h:i A') : '--' }}
                              -
                              {{ !empty($item['end_time']) ? \Carbon\Carbon::parse($item['end_time'])->format('h:i A') : '--' }}
                            @else
                              {{ __('Time pending') }}
                            @endif
                          </li>
                          <li><i class="fal fa-map-marker-alt"></i> {{ $item['site']['zip_code'] ?? ($item['customer']['zip_code'] ?? __('Postcode pending')) }}</li>
                        </ul>
                        @php
                          if (($item['item_type'] ?? 'service') === 'service') {
                              $questions = $serviceQuestions[$item['service_id']] ?? collect();
                          } else {
                              $questions = $packageQuestions[$item['package_id'] ?? null] ?? collect();
                          }
                        @endphp
                        @if ($questions->count() > 0)
                          <div class="cart-question-list">
                            @foreach ($questions as $question)
                              @php
                                $questionOptions = collect($question->options ?? []);
                                $questionConditions = collect($question->conditions ?? []);
                                $questionConditionsPayload = $questionConditions->map(function ($condition) {
                                    return [
                                        'depends_on_question_id' => $condition->depends_on_question_id,
                                        'depends_on_option_id' => $condition->depends_on_option_id,
                                        'condition_type' => $condition->condition_type,
                                        'operator' => $condition->operator,
                                    ];
                                })->values();
                                if (
                                    $questionOptions->isEmpty() &&
                                    in_array($question->type, ['select', 'multi'], true)
                                ) {
                                    $questionOptions = \App\Models\QuestionOption::where('question_id', $question->id)->get();
                                }
                              @endphp
                              <div class="card mb-3 question-card {{ $question->type === 'quantity' ? 'question-card-quantity' : '' }} {{ !$loop->first ? 'is-sequence-hidden' : '' }}"
                                data-question-id="{{ $question->id }}"
                                data-summary-label="{{ $question->summary_label ?? '' }}"
                                data-required="{{ $question->is_required ? 'true' : 'false' }}"
                                data-has-conditions="{{ $questionConditions->isNotEmpty() ? 'true' : 'false' }}"
                                @if ($questionConditions->isNotEmpty())
                                  data-conditions='@json($questionConditionsPayload)'
                                  style="display: none;"
                                @endif>
                                <div class="card-body">
                                  @if ($question->type !== 'quantity')
                                    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
                                      @if (!empty($question->image))
                                        <img src="{{ asset('assets/img/questions/' . $question->image) }}" alt=""
                                          style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;flex-shrink:0;">
                                      @endif
                                      <h6 class="card-title mb-0" style="flex:1;">
                                        {{ $question->title }}
                                        @if ($question->is_required)
                                          <span class="text-danger" title="{{ __('Required') }}">*</span>
                                        @endif
                                      </h6>
                                    </div>
                                  @endif

                                  @if ($question->type === 'text')
                                    <input type="text"
                                      name="questions[{{ $item['key'] }}][{{ $question->id }}]"
                                      class="form-control question-input cart-question-input"
                                      data-question-id="{{ $question->id }}"
                                      placeholder="{{ __('Enter your answer') }}">
                                  @elseif ($question->type === 'number')
                                    <input type="number"
                                      name="questions[{{ $item['key'] }}][{{ $question->id }}]"
                                      class="form-control question-input cart-question-input"
                                      data-question-id="{{ $question->id }}"
                                      placeholder="{{ __('Enter a number') }}"
                                      step="0.01">
                                  @elseif ($question->type === 'quantity')
                                    @php
                                      $quantityMin = $question->quantity_min ?? null;
                                      $quantityMax = $question->quantity_max ?? null;
                                      $defaultQuantity = $question->quantity_default ?? ($quantityMin ?? 1);
                                      $quantityMetaParts = [];
                                      if ($question->quantity_unit_price !== null) {
                                          $quantityMetaParts[] = __('Price: :price each', ['price' => strip_tags($displayPrice($question->quantity_unit_price))]);
                                      }
                                      if ($quantityMin !== null) {
                                          $quantityMetaParts[] = __('Min :min', ['min' => $quantityMin]);
                                      }
                                      $quantityMetaParts[] = __('Default :default', ['default' => $defaultQuantity]);
                                    @endphp
                                    <div class="question-quantity-selector"
                                      data-question-id="{{ $question->id }}"
                                      data-unit-price="{{ $question->quantity_unit_price ?? '' }}"
                                      data-min="{{ $quantityMin ?? '' }}"
                                      data-max="{{ $quantityMax ?? '' }}"
                                      data-default="{{ $defaultQuantity }}">
                                      <div class="question-quantity-row">
                                        <div class="question-quantity-controls">
                                          <button type="button" class="question-quantity-btn question-quantity-down" data-direction="-1" aria-label="{{ __('Decrease quantity') }}">-</button>
                                          <input type="number"
                                            name="questions[{{ $item['key'] }}][{{ $question->id }}]"
                                            class="question-input cart-question-input question-quantity-input"
                                            data-question-id="{{ $question->id }}"
                                            data-question-type="quantity"
                                            data-default="{{ $defaultQuantity }}"
                                            data-unit-price="{{ (float) ($question->quantity_unit_price ?? 0) }}"
                                            min="{{ $quantityMin ?? 0 }}"
                                            @if ($quantityMax !== null) max="{{ $quantityMax }}" @endif
                                            step="1"
                                            inputmode="numeric"
                                            placeholder="{{ $defaultQuantity }}"
                                            value="{{ $defaultQuantity }}">
                                          <button type="button" class="question-quantity-btn question-quantity-up" data-direction="1" aria-label="{{ __('Increase quantity') }}">+</button>
                                        </div>
                                        <div class="question-quantity-copy">
                                          <p class="question-quantity-title">
                                            {{ $question->title }}
                                            @if ($question->is_required)
                                              <span class="text-danger" title="{{ __('Required') }}">*</span>
                                            @endif
                                          </p>
                                          <small class="question-quantity-meta">{{ implode(' • ', $quantityMetaParts) }}</small>
                                        </div>
                                      </div>
                                    </div>
                                  @elseif ($questionOptions->count() > 0 && ($question->type === 'select' || $question->type === 'multi'))
                                    <div class="booking-question-options" role="{{ $question->type === 'multi' ? 'group' : 'radiogroup' }}">
                                      @foreach ($questionOptions as $option)
                                        @php
                                          $inputType = $question->type === 'multi' ? 'checkbox' : 'radio';
                                          $inputId = 'cart_' . $item['key'] . '_question_' . $question->id . '_option_' . $option->id;
                                        @endphp
                                        <label class="booking-question-option" for="{{ $inputId }}">
                                          <input type="{{ $inputType }}"
                                            id="{{ $inputId }}"
                                            name="questions[{{ $item['key'] }}][{{ $question->id }}]{{ $question->type === 'multi' ? '[]' : '' }}"
                                            class="question-input cart-question-input booking-question-choice"
                                            value="{{ $option->id }}"
                                            data-question-id="{{ $question->id }}"
                                            data-option-id="{{ $option->id }}"
                                            data-price="{{ $option->price !== null ? (float) $option->price : '' }}"
                                            data-option-text="{{ $option->option_text }}">
                                          @if (!empty($option->option_image))
                                            <span class="booking-question-option-image-wrap">
                                              <img class="booking-question-option-image" src="{{ asset('assets/img/questions/' . $option->option_image) }}" alt="">
                                              <span class="booking-question-option-preview" aria-hidden="true">
                                                <img src="{{ asset('assets/img/questions/' . $option->option_image) }}" alt="">
                                              </span>
                                            </span>
                                          @endif
                                          <span class="booking-question-option-text">
                                            {{ $option->option_text }}
                                            @if ($option->price !== null)
                                              - {!! $displayPrice($option->price) !!}
                                            @endif
                                          </span>
                                        </label>
                                      @endforeach
                                    </div>
                                  @else
                                    <input class="form-control question-input cart-question-input" type="text"
                                      name="questions[{{ $item['key'] }}][{{ $question->id }}]"
                                      data-question-id="{{ $question->id }}"
                                      placeholder="{{ __('Enter answer') }}">
                                  @endif
                                </div>
                              </div>
                            @endforeach
                          </div>
                        @else
                          <div class="service-note mt-3">
                            <strong>{{ __('No questions required') }}</strong>
                            {{ __('This service does not need extra questions before date and payment.') }}
                          </div>
                        @endif
                      </div>
                      <div class="text-end">
                        <div class="service-cart-price" data-item-price>{!! $displayCartItemPrice($item) !!}</div>
                        <form action="{{ route('frontend.services.cart.remove', $item['key']) }}" method="POST"
                          data-custom-confirm="{{ __('Remove this service?') }}"
                          data-confirm-title="{{ __('Remove service') }}"
                          data-confirm-button="{{ __('Remove service') }}"
                          data-cancel-button="{{ __('Keep service') }}"
                          data-confirm-icon="trash-alt">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="remove-service-btn" aria-label="{{ __('Remove service') }}">
                            <i class="fal fa-times"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                  @endforeach
                </div>
                <div class="cart-step-alert" data-cart-alert="questions">
                  {{ __('Please answer all required questions before continuing.') }}
                </div>
                <div class="cart-step-actions">
                  <a href="{{ route('frontend.services') }}" class="cart-secondary-btn">
                    {{ __('Cancel') }}
                  </a>
                  <button type="button" class="cart-primary-btn" data-cart-next="date">
                    {{ __('Next Step') }} <i class="fal fa-long-arrow-right"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="checkout-step cart-date-panel" data-cart-step-panel="date">
              <span class="checkout-step-icon"><i class="fal fa-calendar-check"></i></span>
              <div>
                <h3>{{ __('2. Date and time') }}</h3>
                <p>{{ __('Choose the appointment now, or securely invite the tenant/occupier to choose a normal date and time later.') }}</p>
                <div class="calender-area pt-4">
                  <div class="cart-scheduling-options" role="radiogroup" aria-label="{{ __('Who will choose the appointment date and time?') }}">
                    <label class="cart-scheduling-option is-selected" data-cart-scheduling-card>
                      <input type="radio" name="cart_scheduling_mode" value="now" data-cart-scheduling-mode checked>
                      <span>
                        <strong>{{ __('I will choose date and time now') }}</strong>
                        <small>{{ __('Continue with the existing date, time, and urgent booking options.') }}</small>
                      </span>
                    </label>
                    <label class="cart-scheduling-option" data-cart-scheduling-card>
                      <input type="radio" name="cart_scheduling_mode" value="tenant" data-cart-scheduling-mode>
                      <span>
                        <strong>{{ __('Let tenant/occupier choose date and time') }}</strong>
                        <small>{{ __('We will send a secure 3-day link for normal appointment slots only.') }}</small>
                      </span>
                    </label>
                  </div>

                  <div data-cart-scheduling-now>
                  <div class="section-title title-center">
                    <h3 class="title">{{ __('Select Available Date & Time') }}</h3>
                    <p class="text-muted">{{ __('Choose from available time slots for this service') }}</p>
                  </div>
                  <div class="urgency-surcharge-info" role="note" aria-label="{{ __('Urgency surcharge information') }}">
                    <div class="urgency-surcharge-info-title">
                      <i class="fal fa-info-circle"></i>
                      <span>{{ __('Urgent booking fees may apply') }}</span>
                    </div>
                    <p>
                      {{ __('Bookings scheduled soon are priced with an urgency fee. If your selected slot falls within one of these windows, the fee is added immediately and shown again in your summary before payment.') }}
                    </p>
                    <div class="urgency-surcharge-tiers">
                      <button type="button" class="urgency-surcharge-tier" data-cart-urgency-fee="50" aria-pressed="false">
                        <strong>{{ __('Within 48 hours') }}</strong>
                        <span>{{ __('+£50') }}</span>
                        <small>{{ __('Select urgent fee') }}</small>
                      </button>
                      <button type="button" class="urgency-surcharge-tier" data-cart-urgency-fee="100" aria-pressed="false">
                        <strong>{{ __('Within 24 hours') }}</strong>
                        <span>{{ __('+£100') }}</span>
                        <small>{{ __('Select urgent fee') }}</small>
                      </button>
                      <button type="button" class="urgency-surcharge-tier" data-cart-urgency-fee="150" aria-pressed="false">
                        <strong>{{ __('Within 4 hours') }}</strong>
                        <span>{{ __('+£150') }}</span>
                        <small>{{ __('Select urgent fee') }}</small>
                      </button>
                    </div>
                  </div>
                  <div class="cart-date-picker-wrap">
                    <div class="cart-date-fields">
                      <div>
                        <label class="cart-field-label" for="cart_booking_date">{{ __('Booking date') }}</label>
                        <input type="date" id="cart_booking_date" class="cart-date-field" name="cart_booking_date" min="{{ \Carbon\Carbon::today()->addDays(3)->format('Y-m-d') }}">
                      </div>
                      <div>
                        <label class="cart-field-label" for="cart_service_hour_id">{{ __('Choose a time slot') }}</label>
                        <select id="cart_service_hour_id" class="cart-time-field" name="cart_service_hour_id">
                          <option value="">{{ __('Choose a time slot') }}</option>
                          <option value="08:00 AM - 10:00 AM">{{ __('08:00 AM - 10:00 AM') }}</option>
                          <option value="10:00 AM - 12:00 PM">{{ __('10:00 AM - 12:00 PM') }}</option>
                          <option value="02:00 PM - 04:00 PM">{{ __('02:00 PM - 04:00 PM') }}</option>
                          <option value="04:00 PM - 06:00 PM">{{ __('04:00 PM - 06:00 PM') }}</option>
                        </select>
                        <p class="cart-urgent-time-note" data-cart-urgent-time-note>
                          {{ __('Within 4 hours bookings are urgent bookings. Admin/vendor will confirm the earliest available time.') }}
                        </p>
                      </div>
                    </div>
                  </div>
                  </div>

                  <div class="cart-tenant-panel" data-cart-scheduling-tenant hidden>
                    <h4>{{ __('Tenant/occupier contact') }}</h4>
                    <p>{{ __('The appointment will be created without a date or time until the secure link is completed.') }}</p>
                    <div class="cart-tenant-fields">
                      <div class="cart-tenant-field">
                        <label class="cart-field-label" for="cart_tenant_name">{{ __('Tenant/occupier name') }} *</label>
                        <input type="text" id="cart_tenant_name" data-cart-tenant="name" autocomplete="name">
                        <span class="mt-2 mb-0 text-danger em" data-cart-tenant-error="name"></span>
                      </div>
                      <div class="cart-tenant-field">
                        <label class="cart-field-label" for="cart_tenant_email">{{ __('Tenant/occupier email') }}</label>
                        <input type="email" id="cart_tenant_email" data-cart-tenant="email" autocomplete="email">
                        <span class="mt-2 mb-0 text-danger em" data-cart-tenant-error="email"></span>
                      </div>
                      <div class="cart-tenant-field">
                        <label class="cart-field-label" for="cart_tenant_phone">{{ __('Tenant/occupier phone') }}</label>
                        <input type="tel" id="cart_tenant_phone" data-cart-tenant="phone" autocomplete="tel">
                        <span class="mt-2 mb-0 text-danger em" data-cart-tenant-error="phone"></span>
                      </div>
                      <div class="cart-tenant-field is-full">
                        <label class="cart-field-label" for="cart_tenant_note">{{ __('Optional note/message to tenant') }}</label>
                        <textarea id="cart_tenant_note" data-cart-tenant="note" maxlength="1000"></textarea>
                      </div>
                    </div>
                    <p class="cart-tenant-contact-note">{{ __('Enter at least an email address or phone number.') }}</p>
                    <span class="mt-2 mb-0 text-danger em" data-cart-tenant-error="contact"></span>
                  </div>

                  <div class="cart-step-alert" data-cart-alert="date">
                    {{ __('Please choose a date and time slot before continuing.') }}
                  </div>
                  <div class="cart-step-actions">
                    <button type="button" class="cart-secondary-btn" data-cart-prev="questions">
                      <i class="fal fa-long-arrow-left"></i> {{ __('Prev Step') }}
                    </button>
                    <button type="button" class="cart-primary-btn" data-cart-next="info">
                      {{ __('Next Step') }} <i class="fal fa-long-arrow-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            @if (!$authUser)
              <div class="checkout-step" data-cart-step-panel="auth">
                <span class="checkout-step-icon"><i class="fal fa-user-lock"></i></span>
                <div>
                  <h3>{{ __('3. Continue Your Booking') }}</h3>
                  <p>{{ __('Continue as a guest, or sign in / create an account so this booking stays connected to your customer profile.') }}</p>

                  <div class="cart-auth-tabs" role="tablist" aria-label="{{ __('Checkout account options') }}">
                    <button type="button" class="cart-auth-tab is-active" data-cart-auth-tab="guest" role="tab" aria-selected="true">
                      {{ __('Continue as guest') }}
                    </button>
                    <button type="button" class="cart-auth-tab" data-cart-auth-tab="login" role="tab" aria-selected="false">
                      {{ __('Existing customer: Login') }}
                    </button>
                    <button type="button" class="cart-auth-tab" data-cart-auth-tab="signup" role="tab" aria-selected="false">
                      {{ __('New customer: Create account') }}
                    </button>
                  </div>

                  <div class="cart-auth-panel is-active" data-cart-auth-panel="guest" role="tabpanel">
                    <p class="cart-guest-note">
                      {{ __('No account needed — you\'ll enter your contact and site details on the next step. You can create an account later to view your booking history.') }}
                    </p>
                    <div class="cart-step-actions">
                      <button type="button" class="cart-secondary-btn" data-cart-prev="date">
                        <i class="fal fa-long-arrow-left"></i> {{ __('Prev Step') }}
                      </button>
                      <button type="button" class="cart-primary-btn" data-cart-guest-continue>
                        {{ __('Continue as guest') }} <i class="fal fa-long-arrow-right"></i>
                      </button>
                    </div>
                  </div>

                  <div class="cart-auth-panel" data-cart-auth-panel="login" role="tabpanel">
                    <form id="cart-login-form" class="cart-auth-form">
                      <div class="form-group">
                        <label class="cart-field-label" for="cart_login_username">{{ __('Username or email') }} *</label>
                        <input type="text" name="username" id="cart_login_username" class="form-control" placeholder="{{ __('Username or email') }}">
                        <span id="err_cart_username" class="mt-2 mb-0 text-danger em"></span>
                      </div>
                      <div class="form-group">
                        <label class="cart-field-label" for="cart_login_password">{{ __('Password') }} *</label>
                        <input type="password" name="password" id="cart_login_password" class="form-control" placeholder="{{ __('Enter Password') }}">
                        <span id="err_cart_password" class="mt-2 mb-0 text-danger em"></span>
                      </div>
                      @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                        <div class="form-group">
                          {!! NoCaptcha::renderJs() !!}
                          {!! NoCaptcha::display() !!}
                          <span id="err_cart_g-recaptcha-response" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                      @endif
                      <div class="cart-step-actions">
                        <button type="button" class="cart-secondary-btn" data-cart-prev="date">
                          <i class="fal fa-long-arrow-left"></i> {{ __('Prev Step') }}
                        </button>
                        <button type="button" class="cart-primary-btn" data-cart-login>
                          {{ __('Login') }} <i class="fal fa-sign-in"></i>
                        </button>
                      </div>
                    </form>
                  </div>

                  <div class="cart-auth-panel" data-cart-auth-panel="signup" role="tabpanel">
                    <form id="cart-signup-form" class="cart-auth-form">
                      <div class="cart-auth-grid">
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_name">{{ __('Name') }} *</label>
                          <input type="text" name="name" id="cart_signup_name" class="form-control" placeholder="{{ __('Name') }}">
                          <span id="err_cart_signup_name" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_username">{{ __('Username') }} *</label>
                          <input type="text" name="username" id="cart_signup_username" class="form-control" placeholder="{{ __('Username') }}">
                          <span id="err_cart_signup_username" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_email">{{ __('Email') }} *</label>
                          <input type="email" name="email" id="cart_signup_email" class="form-control" placeholder="{{ __('Email') }}">
                          <span id="err_cart_signup_email" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_phone">{{ __('Phone') }} *</label>
                          <input type="text" name="phone" id="cart_signup_phone" class="form-control" placeholder="{{ __('Phone') }}">
                          <span id="err_cart_signup_phone" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_password">{{ __('Password') }} *</label>
                          <input type="password" name="password" id="cart_signup_password" class="form-control" placeholder="{{ __('Password') }}">
                          <span id="err_cart_signup_password" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_password_confirmation">{{ __('Confirm password') }} *</label>
                          <input type="password" name="password_confirmation" id="cart_signup_password_confirmation" class="form-control" placeholder="{{ __('Confirm password') }}">
                          <span id="err_cart_signup_password_confirmation" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group is-full">
                          <label class="cart-field-label" for="cart_signup_address">{{ __('Address') }}</label>
                          <input type="text" name="address" id="cart_signup_address" class="form-control" placeholder="{{ __('Address') }}">
                          <span id="err_cart_signup_address" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_city">{{ __('City') }}</label>
                          <input type="text" name="city" id="cart_signup_city" class="form-control" placeholder="{{ __('City') }}">
                          <span id="err_cart_signup_city" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                        <div class="form-group">
                          <label class="cart-field-label" for="cart_signup_zip_code">{{ __('Postcode') }}</label>
                          <input type="text" name="zip_code" id="cart_signup_zip_code" class="form-control" placeholder="{{ __('Postcode') }}">
                          <span id="err_cart_signup_zip_code" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                      </div>
                      @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                        <div class="form-group">
                          {!! NoCaptcha::display() !!}
                          <span id="err_cart_signup_g-recaptcha-response" class="mt-2 mb-0 text-danger em"></span>
                        </div>
                      @endif
                      <div class="cart-step-actions">
                        <button type="button" class="cart-secondary-btn" data-cart-prev="date">
                          <i class="fal fa-long-arrow-left"></i> {{ __('Prev Step') }}
                        </button>
                        <button type="button" class="cart-primary-btn" data-cart-signup>
                          {{ __('Create account') }} <i class="fal fa-user-plus"></i>
                        </button>
                      </div>
                    </form>
                  </div>

                  <div class="cart-step-alert" data-cart-alert="auth"></div>
                </div>
              </div>
            @endif

            <div class="checkout-step" data-cart-step-panel="info">
              <span class="checkout-step-icon"><i class="fal fa-user"></i></span>
              <div>
                <h3>{{ $authUser ? __('3. Customer information') : __('4. Customer information') }}</h3>
                <p>{{ __('Site contact, billing contact and alternate contact information are collected before checkout so the engineer has the right details.') }}</p>
                @php
                  $firstCartItem = $serviceCart->first();
                  $firstCustomer = $firstCartItem['site'] ?? ($firstCartItem['customer'] ?? []);
                  if ($authUser && empty(array_filter($firstCustomer))) {
                      $firstCustomer = [
                          'name' => $authUser->name,
                          'phone' => $authUser->phone,
                          'email' => $authUser->email,
                          'address' => $authUser->address,
                          'zip_code' => $authUser->zip_code,
                          'country' => $authUser->country,
                      ];
                  }
                @endphp
                <div class="row mt-4">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label class="cart-field-label" for="cart_customer_name">{{ __('Site name') }} *</label>
                      <input type="text" id="cart_customer_name" class="form-control cart-info-field" value="{{ $firstCustomer['name'] ?? '' }}" data-cart-info="name">
                      <span class="mt-2 mb-0 text-danger em" data-cart-info-error="name"></span>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label class="cart-field-label" for="cart_customer_phone">{{ __('Site phone') }} *</label>
                      <input type="text" id="cart_customer_phone" class="form-control cart-info-field" value="{{ $firstCustomer['phone'] ?? '' }}" data-cart-info="phone">
                      <span class="mt-2 mb-0 text-danger em" data-cart-info-error="phone"></span>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label class="cart-field-label" for="cart_customer_email">{{ __('Site email') }} *</label>
                      <input type="email" id="cart_customer_email" class="form-control cart-info-field" value="{{ $firstCustomer['email'] ?? '' }}" data-cart-info="email">
                      <span class="mt-2 mb-0 text-danger em" data-cart-info-error="email"></span>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label class="cart-field-label" for="cart_customer_zip">{{ __('Site postcode/zip') }}</label>
                      <input type="text" id="cart_customer_zip" class="form-control cart-info-field" value="{{ $firstCustomer['zip_code'] ?? '' }}" data-cart-info="zip_code">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label class="cart-field-label" for="cart_customer_country">{{ __('Site country') }}</label>
                      <input type="text" id="cart_customer_country" class="form-control cart-info-field" value="{{ $firstCustomer['country'] ?? '' }}" data-cart-info="country">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label class="cart-field-label" for="cart_customer_address">{{ __('Site address') }} *</label>
                      <input type="text" id="cart_customer_address" class="form-control cart-info-field" value="{{ $firstCustomer['address'] ?? '' }}" data-cart-info="address">
                      <span class="mt-2 mb-0 text-danger em" data-cart-info-error="address"></span>
                    </div>
                  </div>
                </div>
                <div class="cart-step-alert" data-cart-alert="info">
                  {{ __('Please complete the required customer information before continuing.') }}
                </div>
                <div class="cart-step-actions">
                  <button type="button" class="cart-secondary-btn" data-cart-prev="date">
                    <i class="fal fa-long-arrow-left"></i> {{ __('Prev Step') }}
                  </button>
                  <button type="button" class="cart-primary-btn" data-cart-next="review">
                    {{ __('Next Step') }} <i class="fal fa-long-arrow-right"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="checkout-step cart-summary-review" data-cart-step-panel="review">
              <span class="checkout-step-icon"><i class="fal fa-clipboard-check"></i></span>
              <div>
                <h3>{{ $authUser ? __('4. Review your booking') : __('5. Review your booking') }}</h3>
                <p>{{ __('Check every detail below before continuing to secure payment. Use the edit links to go back to any step.') }}</p>

                <div class="cart-summary-grid">
                  <div class="cart-summary-card cart-summary-services-card">
                    <div class="cart-summary-card-head">
                      <h5><i class="fal fa-concierge-bell"></i>{{ __('Service details') }}</h5>
                      <button type="button" class="cart-summary-edit" data-cart-prev="questions">{{ __('Edit') }}</button>
                    </div>
                    <div id="cart-summary-services" class="cart-summary-services"></div>
                  </div>

                  <div class="cart-summary-card">
                    <div class="cart-summary-card-head">
                      <h5><i class="fal fa-calendar-check"></i>{{ __('Booking information') }}</h5>
                      <button type="button" class="cart-summary-edit" data-cart-prev="date">{{ __('Edit') }}</button>
                    </div>
                    <div class="cart-summary-row">
                      <span class="cart-summary-label">{{ __('Date & time') }}</span>
                      <span class="cart-summary-value" id="cart-summary-datetime">-</span>
                    </div>
                  </div>

                  <div class="cart-summary-card">
                    <div class="cart-summary-card-head">
                      <h5><i class="fal fa-map-marker-alt"></i>{{ __('Site information') }}</h5>
                      <button type="button" class="cart-summary-edit" data-cart-prev="info">{{ __('Edit') }}</button>
                    </div>
                    <div class="cart-summary-row">
                      <span class="cart-summary-label">{{ __('Name') }}</span>
                      <span class="cart-summary-value" id="cart-summary-name">-</span>
                    </div>
                    <div class="cart-summary-row">
                      <span class="cart-summary-label">{{ __('Email') }}</span>
                      <span class="cart-summary-value" id="cart-summary-email">-</span>
                    </div>
                    <div class="cart-summary-row">
                      <span class="cart-summary-label">{{ __('Phone') }}</span>
                      <span class="cart-summary-value" id="cart-summary-phone">-</span>
                    </div>
                    <div class="cart-summary-row">
                      <span class="cart-summary-label">{{ __('Address') }}</span>
                      <span class="cart-summary-value" id="cart-summary-address">-</span>
                    </div>
                  </div>

                  <div class="cart-summary-card cart-summary-payment-card">
                    <div class="cart-summary-card-head">
                      <h5><i class="fal fa-receipt"></i>{{ __('Payment summary') }}</h5>
                    </div>
                    <div class="cart-summary-row">
                      <span class="cart-summary-label">{{ __('Subtotal') }}</span>
                      <span class="cart-summary-value" id="cart-summary-subtotal">-</span>
                    </div>
                    <div class="cart-summary-row" id="cart-summary-postcode-row" style="display:none;">
                      <span class="cart-summary-label">{{ __('Postcode extra charge') }}</span>
                      <span class="cart-summary-value text-success" id="cart-summary-postcode">-</span>
                    </div>
                    <div class="cart-summary-row" id="cart-summary-urgency-row" style="display:none;">
                      <span class="cart-summary-label">{{ __('Urgent booking fee') }}<small id="cart-summary-urgency-label" class="text-muted d-block"></small></span>
                      <span class="cart-summary-value text-warning" id="cart-summary-urgency">-</span>
                    </div>
                    <div class="cart-summary-row" id="cart-summary-discount-row" style="display:none;">
                      <span class="cart-summary-label">{{ __('Discount') }}</span>
                      <span class="cart-summary-value text-success" id="cart-summary-discount">-</span>
                    </div>
                    <div class="cart-summary-row cart-summary-total-row">
                      <span class="cart-summary-label">{{ __('Total payable') }}</span>
                      <span class="cart-summary-value" id="cart-summary-total">-</span>
                    </div>
                  </div>
                </div>

                <div class="cart-step-actions">
                  <button type="button" class="cart-secondary-btn" data-cart-prev="info">
                    <i class="fal fa-long-arrow-left"></i> {{ __('Prev Step') }}
                  </button>
                  <button type="button" class="cart-primary-btn" data-cart-next="payment">
                    {{ __('Confirm & pay') }} <i class="fal fa-long-arrow-right"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="checkout-step" data-cart-step-panel="payment">
              <span class="checkout-step-icon"><i class="fal fa-credit-card"></i></span>
              <div>
                <h3>{{ $authUser ? __('5. Payment') : __('6. Payment') }}</h3>
                <p>{{ __('Payment is completed after the service details are confirmed. Your right-side summary shows the payable total before you continue.') }}</p>
                <div class="cart-payment-area">
                  <div class="section-title">
                    <h3>{{ __('Pay securely by card') }}</h3>
                    <p class="text-muted">{{ __('Enter your debit or credit card details below.') }}</p>
                  </div>
                  <form action="{{ route('frontend.service.payment') }}" method="POST" id="payment-form" enctype="multipart/form-data">
                    @csrf
                    @php
                      $firstCartItem = $serviceCart->first();
                      $firstCustomer = $firstCartItem['site'] ?? ($firstCartItem['customer'] ?? []);
                    @endphp
                    <input hidden type="text" name="stripeToken" id="stripeToken">
                    <input hidden type="text" name="name" id="billing_name" value="{{ $firstCustomer['name'] ?? '' }}">
                    <input hidden type="text" name="phone" id="billing_phone" value="{{ $firstCustomer['phone'] ?? '' }}">
                    <input hidden type="text" name="email" id="billing_email" value="{{ $firstCustomer['email'] ?? '' }}">
                    <input hidden type="text" name="address" id="billing_address" value="{{ $firstCustomer['address'] ?? '' }}">
                    <input hidden type="text" name="zip_code" id="billing_zip_code" value="{{ $firstCustomer['zip_code'] ?? '' }}">
                    <input hidden type="text" name="country" id="billing_country" value="{{ $firstCustomer['country'] ?? '' }}">
                    <input hidden type="text" name="serviceHourId" id="serviceHourId">
                    <input hidden type="text" name="bookingDate" id="bookingDate">
                    <input hidden type="text" name="staffId" id="staffId" value="{{ $firstCartItem['staff_id'] ?? '' }}">
                    <input hidden type="text" name="user_id" id="userId" value="{{ $authUser ? $authUser->id : ($firstCartItem['user_id'] ?? 0) }}">
                    <input hidden type="text" name="max_person" id="bmax_person" value="{{ $firstCartItem['max_person'] ?? 1 }}">
                    <input hidden type="text" name="service_id" id="service_id" value="{{ $firstCartItem['service_id'] ?? '' }}">
                    <input hidden type="text" name="cart_mode" id="cart_mode" value="1">
                    <input hidden type="text" name="gateway" id="gateway" value="stripe">
                    <input hidden type="text" name="billing_same" id="billing_same_input" value="1">
                    <input hidden type="text" name="billing_name_secondary" id="billing_name_secondary">
                    <input hidden type="text" name="billing_phone_secondary" id="billing_phone_secondary">
                    <input hidden type="text" name="billing_email_secondary" id="billing_email_secondary">
                    <input hidden type="text" name="billing_address_secondary" id="billing_address_secondary">
                    <input hidden type="text" name="billing_zip_secondary" id="billing_zip_secondary">
                    <input hidden type="text" name="billing_country_secondary" id="billing_country_secondary">
                    <input hidden type="text" name="question_answers" id="question_answers" value="{}">
                    <input hidden type="text" name="total_price" id="total_price" value="{{ $cartPayableTotal }}">
                    <input hidden type="text" name="urgency_surcharge_amount" id="urgency_surcharge_value" value="0">
                    <input hidden type="text" name="urgency_surcharge_period" id="urgency_surcharge_period_value" value="">
                    <input hidden type="text" name="weekend_request" id="weekend_request" value="0">
                    <input hidden type="text" name="quote_request" id="quote_request" value="0">
                    <input hidden type="text" name="guest_checkout" id="guest_checkout" value="0">
                    <input hidden type="text" name="scheduling_mode" id="scheduling_mode" value="now">
                    <input hidden type="text" name="tenant_name" id="tenant_name">
                    <input hidden type="text" name="tenant_email" id="tenant_email">
                    <input hidden type="text" name="tenant_phone" id="tenant_phone">
                    <input hidden type="text" name="tenant_note" id="tenant_note">

                    <span id="err_gateway" class="mt-2 mb-0 text-danger em d-block text-center"></span>

                    <div class="cart-step-alert mb-3" data-cart-alert="weekend" style="display:none;">
                      {{ __('Weekend appointments are request-only. Submit this request and our team will confirm availability before payment.') }}
                    </div>

                    <div class="featured-payment-option mb-3" data-card-payment-ui>
                      <div class="payment-method-card payment-card-featured selected">
                        <div class="payment-icon">
                          <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="payment-name-featured">
                          {{ __('Debit/Credit Card') }}
                          <small class="d-block text-muted">{{ __('Secure payment powered by Stripe') }}</small>
                        </div>
                        <div class="payment-brands">
                          <i class="fab fa-cc-visa"></i>
                          <i class="fab fa-cc-mastercard"></i>
                          <i class="fab fa-cc-amex"></i>
                          <i class="fab fa-cc-discover"></i>
                        </div>
                        <div class="payment-check">
                          <i class="fas fa-check-circle"></i>
                        </div>
                      </div>
                    </div>

                    <div id="stripe-element" class="mb-2 mt-4" data-card-payment-ui style="background:#f8f9fa;padding:15px;border-radius:8px;border:2px solid #e0e0e0;"></div>
                    <div id="stripe-errors" class="mt-2 mb-0 text-danger em text-center" role="alert"></div>
                    <div class="card-payment-info" id="card-payment-info" data-card-payment-ui style="display:block;text-align:center;font-size:12px;color:#6c757d;margin-top:10px;">
                      <i class="fas fa-lock" style="color:#28a745;"></i>
                      {{ __('Your payment is encrypted and processed securely. Card details are never stored on our servers.') }}
                    </div>
                    <span id="err_currency" class="mt-2 mb-0 text-danger em"></span>
                  </form>
                </div>
                <div class="cart-step-actions">
                  <button type="button" class="cart-secondary-btn" data-cart-prev="review">
                    <i class="fal fa-long-arrow-left"></i> {{ __('Prev Step') }}
                  </button>
                  <button type="button" id="payment_submit_btn" class="cart-primary-btn" data-cart-pay>
                    {{ __('Complete Payment') }} <i class="fal fa-long-arrow-right"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <aside class="summary-panel">
            <h3>{{ __('Order summary') }}</h3>
            <ul class="summary-items">
              @foreach ($serviceCart as $item)
                <li>
                  <span>
                    {{ $item['service_name'] }}
                    @if (($item['item_type'] ?? 'service') === 'package' && !empty($item['included_services']))
                      <small class="d-block text-muted">
                        {{ __('Includes') }}:
                        {{ collect($item['included_services'])->pluck('name')->filter()->implode(', ') }}
                      </small>
                    @endif
                  </span>
                  <strong data-summary-item-price="{{ $item['key'] }}">{!! $displayCartItemPrice($item) !!}</strong>
                </li>
              @endforeach
              <li data-summary-postcode-row style="display: none;">
                <span>{{ __('Postcode extra charge') }}</span>
                <strong data-summary-postcode-price></strong>
              </li>
              <li data-summary-urgency-row style="display: none;">
                <span data-summary-urgency-label>{{ __('Urgent booking fee') }}</span>
                <strong data-summary-urgency-price></strong>
              </li>
              <li data-summary-discount-row style="{{ $serviceCouponDiscount > 0 ? '' : 'display: none;' }}">
                <span>{{ __('Discount') }}</span>
                <strong data-summary-discount>-{!! $displayPrice($serviceCouponDiscount) !!}</strong>
              </li>
            </ul>
            <div class="summary-coupon" data-service-coupon>
              <form class="summary-coupon-form" data-service-coupon-form>
                <input type="text" class="summary-coupon-input" data-service-coupon-input
                  placeholder="{{ __('Enter Coupon Code') }}" value="{{ $serviceCouponCode ?? '' }}">
                <button type="submit" class="summary-coupon-btn">{{ __('Apply') }}</button>
              </form>
              <div class="summary-coupon-applied" data-service-coupon-applied style="{{ $serviceCouponDiscount > 0 ? '' : 'display: none;' }}">
                <span>
                  {{ __('Applied') }}:
                  <strong data-service-coupon-code>{{ $serviceCouponCode }}</strong>
                </span>
                <button type="button" class="summary-coupon-remove" data-service-coupon-remove>{{ __('Remove') }}</button>
              </div>
              <div class="summary-coupon-message" data-service-coupon-message></div>
            </div>
            <div class="summary-total">
              <span>{{ __('Total') }}</span>
              <strong data-summary-total>{!! $displayPrice($cartPayableTotal) !!}</strong>
            </div>
            <div class="summary-actions">
              <a href="{{ route('frontend.services') }}" class="cart-primary-btn">
                {{ __('Add another service') }} <i class="fal fa-plus"></i>
              </a>
              <form action="{{ route('frontend.services.cart.clear') }}" method="POST"
                data-custom-confirm="{{ __('Clear all items?') }}"
                data-confirm-title="{{ __('Clear cart') }}"
                data-confirm-button="{{ __('Clear cart') }}"
                data-cancel-button="{{ __('Cancel') }}"
                data-confirm-icon="trash-alt">
                @csrf
                @method('DELETE')
                <button type="submit" class="cart-secondary-btn w-100">
                  <i class="fal fa-trash-alt"></i>{{ __('Clear cart') }}
                </button>
              </form>
            </div>
          </aside>
        </div>
      @endif
    </div>
  </section>
@endsection

@section('script')
  <script async id="cart-stripe-sdk" src="https://js.stripe.com/v3/"></script>
  <script>
    (function () {
      const currencySymbol = @json(html_entity_decode($symbol, ENT_QUOTES, 'UTF-8'));
      const currencyPosition = @json($position);
      const stripeKey = @json($stripe_key ?? '');
      const cartLoginUrl = @json(route('frontend.user.login'));
      const cartSignupUrl = @json(route('frontend.user.signup'));
      const couponApplyUrl = @json(route('frontend.services.cart.apply_coupon'));
      const couponRemoveUrl = @json(route('frontend.services.cart.remove_coupon'));
      const pendingPriceLabel = @json(__('Price pending'));
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') || '' : '';
      let cartIsAuthenticated = @json((bool) $authUser);
      let cartGuestCheckout = false;
      let cartSchedulingMode = 'now';
      let selectedUrgencyFee = 0;
      let selectedUrgencyLabel = '';
      let cartPostcodeCheckedValue = '';

      function isWithinFourHoursUrgency() {
        return cartSchedulingMode === 'now' && Number(selectedUrgencyFee || 0) === 150;
      }
      let appliedCoupon = {
        code: @json($serviceCouponCode),
        type: @json($serviceCouponType),
        value: Number(@json($serviceCouponValue)),
        amount: Number(@json($serviceCouponDiscount))
      };
      let stripe = null;
      let stripeCard = null;
      let stripeMounted = false;

      function initializeCartStripe() {
        if (stripe || !stripeKey || typeof Stripe === 'undefined') {
          return !!stripe;
        }

        stripe = Stripe(stripeKey);
        stripeCard = stripe.elements().create('card', {
          style: {
            base: {
              iconColor: '#454545',
              color: '#454545',
              fontWeight: '500',
              lineHeight: '48px',
              fontSmoothing: 'antialiased',
              '::placeholder': {
                color: '#777'
              }
            }
          }
        });

        const paymentPanel = document.querySelector('[data-cart-step-panel="payment"].is-active');
        if (paymentPanel) {
          mountCartStripeCard();
        }

        return true;
      }

      const stripeSdk = document.getElementById('cart-stripe-sdk');
      if (stripeSdk) {
        stripeSdk.addEventListener('load', initializeCartStripe);
      }
      initializeCartStripe();

      function mountCartStripeCard() {
        initializeCartStripe();

        if (!stripeCard || stripeMounted || !document.getElementById('stripe-element')) {
          return;
        }

        stripeCard.mount('#stripe-element');
        stripeMounted = true;
      }

      function formatCartPrice(amount) {
        const value = Number(amount || 0).toFixed(2);
        return currencyPosition === 'left' ? currencySymbol + value : value + currencySymbol;
      }

      function fetchWithTimeout(url, options, timeout) {
        return new Promise(function (resolve, reject) {
          let finished = false;
          const timer = window.setTimeout(function () {
            if (!finished) {
              finished = true;
              reject(new Error('Request timed out'));
            }
          }, timeout || 8000);

          fetch(url, options || {}).then(function (response) {
            if (finished) return;
            finished = true;
            window.clearTimeout(timer);
            resolve(response);
          }).catch(function (error) {
            if (finished) return;
            finished = true;
            window.clearTimeout(timer);
            reject(error);
          });
        });
      }

      function calculateCouponDiscount(total) {
        if (!appliedCoupon || !appliedCoupon.code) {
          return 0;
        }

        const rawDiscount = appliedCoupon.type === 'percentage'
          ? total * ((Number(appliedCoupon.value) || 0) / 100)
          : Number(appliedCoupon.value || appliedCoupon.amount || 0);

        return Math.min(Math.max(rawDiscount, 0), total);
      }

      function setCouponMessage(message, type) {
        const element = document.querySelector('[data-service-coupon-message]');

        if (!element) {
          return;
        }

        element.textContent = message || '';
        element.classList.toggle('is-error', type === 'error');
        element.classList.toggle('is-success', type === 'success');
      }

      function updateCouponUi(discount) {
        const applied = document.querySelector('[data-service-coupon-applied]');
        const code = document.querySelector('[data-service-coupon-code]');
        const input = document.querySelector('[data-service-coupon-input]');

        if (applied) {
          applied.style.display = appliedCoupon && appliedCoupon.code ? '' : 'none';
        }

        if (code) {
          code.textContent = appliedCoupon && appliedCoupon.code ? appliedCoupon.code : '';
        }

        if (input && appliedCoupon && appliedCoupon.code) {
          input.value = appliedCoupon.code;
        }
      }

      function calculateItemTotal(item) {
        let total = parseFloat(item.dataset.basePrice || '0') || 0;
        total += parseFloat(item.dataset.postcodeSurcharge || '0') || 0;

        item.querySelectorAll('.cart-question-input, .question-input').forEach(function (input) {
          const card = input.closest('.question-card');

          if (card && card.style.display === 'none') {
            return;
          }

          if (input.type === 'checkbox' || input.type === 'radio') {
            if (input.checked) {
              if (isQuoteRequestChoice(input)) {
                return;
              }

              total += parseFloat(input.dataset.price || '0') || 0;
            }

            return;
          }

          if (input.dataset.questionType === 'quantity') {
            const quantity = parseInt(input.value || '0', 10) || 0;
            const defaultQuantity = parseInt(input.dataset.default || '0', 10) || 0;
            const unitPrice = parseFloat(input.dataset.unitPrice || '0') || 0;

            if (quantity > defaultQuantity && unitPrice > 0) {
              total += quantity * unitPrice;
            }
          }
        });

        return total;
      }

      const postalCodePriceUrl = '{{ route('frontend.services.postal_code.price') }}';

      function setCartPostcodeNote(message, state) {
        const note = document.querySelector('[data-cart-postcode-note]');

        if (!note) {
          return;
        }

        note.textContent = message || '';
        note.classList.remove('is-found', 'is-standard');

        if (state) {
          note.classList.add(state);
        }
      }

      function syncCartPostcodeToCustomer(postcode) {
        const zipField = document.querySelector('[data-cart-info="zip_code"]');

        if (zipField && postcode) {
          zipField.value = postcode;
        }
      }

      function applyPostcodeSurcharge(items) {
        const itemSurcharges = {};

        (items || []).forEach(function (item) {
          if (item && item.key) {
            itemSurcharges[item.key] = parseFloat(item.surcharge || '0') || 0;
          }
        });

        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          if (item.dataset.pricePending === 'true') {
            item.dataset.postcodeSurcharge = '0';
            return;
          }

          item.dataset.basePrice = item.dataset.websitePrice || item.dataset.basePrice;
          item.dataset.postcodeSurcharge = String(itemSurcharges[item.dataset.itemKey] || 0);
        });

        updateCartTotals();
      }

      function fetchCartPostalPrice() {
        const input = document.querySelector('[data-cart-postcode-input]');

        if (!input) {
          return Promise.resolve(false);
        }

        const postcode = input.value.trim();

        if (postcode === '') {
          applyPostcodeSurcharge([]);
          cartPostcodeCheckedValue = '';
          setCartPostcodeNote('', null);
          return Promise.resolve(false);
        }

        syncCartPostcodeToCustomer(postcode);
        setCartPostcodeNote(@json(__('Checking price for your postcode…')), 'is-standard');

        return fetchWithTimeout(postalCodePriceUrl + '?zip_code=' + encodeURIComponent(postcode), {
          headers: { 'Accept': 'application/json' }
        }, 8000)
          .then(function (response) { return response.json(); })
          .then(function (response) {
            const found = !!(response && response.found);
            cartPostcodeCheckedValue = postcode;
            hideCartAlert('postcode');

            if (found) {
              applyPostcodeSurcharge(response.items || []);
              setCartPostcodeNote(@json(__('Price updated for postcode')) + ' ' + postcode + '.', 'is-found');
            } else {
              applyPostcodeSurcharge([]);
              setCartPostcodeNote(@json(__('No special pricing for this postcode — standard price applied.')), 'is-standard');
            }

            return true;
          })
          .catch(function () {
            applyPostcodeSurcharge([]);
            cartPostcodeCheckedValue = postcode;
            hideCartAlert('postcode');
            setCartPostcodeNote(@json(__('Could not check postcode pricing — standard price applied.')), 'is-standard');
            return true;
          });
      }

      function updateCartTotals() {
        let cartTotal = 0;

        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          const itemTotal = calculateItemTotal(item);
          const itemDisplayTotal = calculateItemDisplayTotal(item);
          const formatted = isCartItemQuoteRequest(item) || (item.dataset.pricePending === 'true' && itemDisplayTotal <= 0)
            ? pendingPriceLabel
            : formatCartPrice(itemDisplayTotal);
          const itemKey = item.dataset.itemKey;
          const itemPrice = item.querySelector('[data-item-price]');
          const summaryPrice = document.querySelector('[data-summary-item-price="' + itemKey + '"]');

          cartTotal += itemTotal;

          if (itemPrice) {
            itemPrice.textContent = formatted;
          }

          if (summaryPrice) {
            summaryPrice.textContent = formatted;
          }
        });

        cartTotal += selectedUrgencyFee;
        const discount = calculateCouponDiscount(cartTotal);
        const payableTotal = Math.max(cartTotal - discount, 0);
        const postcodeTotal = getPostcodeSurchargeTotal();

        const summaryPostcodeRow = document.querySelector('[data-summary-postcode-row]');
        const summaryPostcodePrice = document.querySelector('[data-summary-postcode-price]');
        const summaryUrgencyRow = document.querySelector('[data-summary-urgency-row]');
        const summaryUrgencyLabel = document.querySelector('[data-summary-urgency-label]');
        const summaryUrgencyPrice = document.querySelector('[data-summary-urgency-price]');
        const summaryDiscountRow = document.querySelector('[data-summary-discount-row]');
        const summaryDiscount = document.querySelector('[data-summary-discount]');
        const summaryTotal = document.querySelector('[data-summary-total]');
        const totalInput = document.getElementById('total_price');

        if (summaryPostcodeRow) {
          summaryPostcodeRow.style.display = postcodeTotal > 0 ? '' : 'none';
        }

        if (summaryPostcodePrice) {
          summaryPostcodePrice.textContent = '+' + formatCartPrice(postcodeTotal);
        }

        if (summaryUrgencyRow) {
          summaryUrgencyRow.style.display = selectedUrgencyFee > 0 ? '' : 'none';
        }

        if (summaryUrgencyLabel) {
          summaryUrgencyLabel.textContent = selectedUrgencyLabel || @json(__('Urgent booking fee'));
        }

        if (summaryUrgencyPrice) {
          summaryUrgencyPrice.textContent = '+' + formatCartPrice(selectedUrgencyFee);
        }

        if (summaryDiscountRow) {
          summaryDiscountRow.style.display = discount > 0 ? '' : 'none';
        }

        if (summaryDiscount) {
          summaryDiscount.textContent = '-' + formatCartPrice(discount);
        }

        updateCouponUi(discount);

        if (summaryTotal) {
          summaryTotal.textContent = formatCartPrice(payableTotal);
        }

        if (totalInput) {
          totalInput.value = Number(payableTotal || 0).toFixed(2);
        }

        updateWeekendRequestMode();
      }

      function getPreDiscountCartTotal() {
        let total = 0;

        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          total += calculateItemTotal(item);
        });

        return total + selectedUrgencyFee;
      }

      function getPostcodeSurchargeTotal() {
        let total = 0;

        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          total += parseFloat(item.dataset.postcodeSurcharge || '0') || 0;
        });

        return total;
      }

      function calculateItemDisplayTotal(item) {
        return Math.max(calculateItemTotal(item) - (parseFloat(item.dataset.postcodeSurcharge || '0') || 0), 0);
      }

      function applyServiceCoupon() {
        const input = document.querySelector('[data-service-coupon-input]');
        const code = input ? input.value.trim() : '';

        if (!code) {
          setCouponMessage(@json(__('Enter Coupon Code')), 'error');
          return;
        }

        fetchWithTimeout(couponApplyUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            coupon: code,
            total: Number(getPreDiscountCartTotal() || 0).toFixed(2)
          })
        }, 8000)
          .then(function (response) {
            return response.json().then(function (data) {
              return { ok: response.ok, data: data };
            });
          })
          .then(function (result) {
            if (!result.ok || result.data.error) {
              setCouponMessage(result.data.error || @json(__('Coupon is not valid!')), 'error');
              return;
            }

            appliedCoupon = {
              code: result.data.code,
              type: result.data.type,
              value: Number(result.data.value || 0),
              amount: Number(result.data.amount || 0)
            };
            setCouponMessage(result.data.success || @json(__('Coupon applied successfully.')), 'success');
            updateCartTotals();
          })
          .catch(function () {
            setCouponMessage(@json(__('Sorry, something went wrong!')), 'error');
          });
      }

      function removeServiceCoupon() {
        fetchWithTimeout(couponRemoveUrl, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          }
        }, 8000)
          .then(function (response) {
            return response.json().then(function (data) {
              return { ok: response.ok, data: data };
            });
          })
          .then(function (result) {
            if (!result.ok) {
              setCouponMessage(result.data.error || @json(__('Sorry, something went wrong!')), 'error');
              return;
            }

            appliedCoupon = { code: null, type: null, value: 0, amount: 0 };
            setCouponMessage(result.data.success || @json(__('Coupon removed successfully.')), 'success');
            updateCartTotals();
          })
          .catch(function () {
            setCouponMessage(@json(__('Sorry, something went wrong!')), 'error');
          });
      }

      function updateCartDateAvailability() {
        const bookingDate = document.getElementById('cart_booking_date');

        if (!bookingDate) {
          return;
        }

        bookingDate.min = getCartNormalBookingMinDateValue();
        bookingDate.disabled = selectedUrgencyFee > 0 || cartSchedulingMode === 'tenant';

        if (selectedUrgencyFee > 0) {
          bookingDate.value = '';
          hideCartAlert('date');
        } else if (isPastCartBookingDate(bookingDate.value)) {
          bookingDate.value = '';
          showCartAlert('date', @json(__('Please choose a date at least 3 days from today, or use an urgent booking option above.')));
        }

        updateWeekendRequestMode();
        updateCartTimeAvailability();
      }

      function updateCartTimeAvailability() {
        const serviceHour = document.getElementById('cart_service_hour_id');
        const urgentTimeNote = document.querySelector('[data-cart-urgent-time-note]');
        const withinFourHours = isWithinFourHoursUrgency();

        if (serviceHour) {
          serviceHour.disabled = withinFourHours || cartSchedulingMode === 'tenant';

          if (withinFourHours) {
            serviceHour.value = '';
          }
        }

        if (urgentTimeNote) {
          urgentTimeNote.style.display = withinFourHours ? 'block' : 'none';
        }
      }

      function tenantCartValue(field) {
        const input = document.querySelector('[data-cart-tenant="' + field + '"]');
        return input ? String(input.value || '').trim() : '';
      }

      function setCartSchedulingMode(mode) {
        cartSchedulingMode = mode === 'tenant' ? 'tenant' : 'now';

        document.querySelectorAll('[data-cart-scheduling-mode]').forEach(function (input) {
          const selected = input.value === cartSchedulingMode;
          input.checked = selected;
          const card = input.closest('[data-cart-scheduling-card]');
          if (card) card.classList.toggle('is-selected', selected);
        });

        const chooseNowPanel = document.querySelector('[data-cart-scheduling-now]');
        const tenantPanel = document.querySelector('[data-cart-scheduling-tenant]');
        if (chooseNowPanel) chooseNowPanel.hidden = cartSchedulingMode !== 'now';
        if (tenantPanel) tenantPanel.hidden = cartSchedulingMode !== 'tenant';

        if (cartSchedulingMode === 'tenant') {
          selectedUrgencyFee = 0;
          selectedUrgencyLabel = '';
          document.querySelectorAll('[data-cart-urgency-fee]').forEach(function (button) {
            button.setAttribute('aria-pressed', 'false');
          });
        }

        updateCartDateAvailability();
        updateCartTotals();
        hideCartAlert('date');
      }

      function getCartTodayDateValue() {
        const today = new Date();
        today.setMinutes(today.getMinutes() - today.getTimezoneOffset());

        return today.toISOString().slice(0, 10);
      }

      function getCartNormalBookingMinDateValue() {
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + 3);
        minDate.setMinutes(minDate.getMinutes() - minDate.getTimezoneOffset());

        return minDate.toISOString().slice(0, 10);
      }

      function isPastCartBookingDate(value) {
        return value !== '' && value < getCartNormalBookingMinDateValue();
      }

      function isWeekendCartBookingDate(value) {
        if (value === '') {
          return false;
        }

        const parts = value.split('-').map(Number);

        if (parts.length !== 3 || parts.some(Number.isNaN)) {
          return false;
        }

        const selected = new Date(parts[0], parts[1] - 1, parts[2]);
        const day = selected.getDay();

        return day === 0 || day === 6;
      }

      function isQuoteRequestChoice(input) {
        const optionText = String(input && input.dataset ? input.dataset.optionText || '' : '').toLowerCase();

        return optionText.includes('20+') && optionText.includes('circuit');
      }

      function isCartItemQuoteRequest(item) {
        return Array.from(item.querySelectorAll('.booking-question-choice:checked')).some(isQuoteRequestChoice);
      }

      function isCartQuoteRequestSelected() {
        return Array.from(document.querySelectorAll('[data-cart-item]')).some(isCartItemQuoteRequest);
      }

      function updateWeekendRequestMode() {
        const bookingDate = document.getElementById('cart_booking_date');
        const weekendRequestInput = document.getElementById('weekend_request');
        const quoteRequestInput = document.getElementById('quote_request');
        const paymentTitle = document.querySelector('[data-cart-step-panel="payment"] .section-title h3');
        const paymentCopy = document.querySelector('[data-cart-step-panel="payment"] .section-title .text-muted');
        const payButton = document.getElementById('payment_submit_btn');
        const summaryPayButton = document.querySelector('[data-cart-summary-pay]');
        const isWeekend = bookingDate && !bookingDate.disabled && isWeekendCartBookingDate(bookingDate.value);
        const isQuoteRequest = isCartQuoteRequestSelected();
        const isRequestOnly = isWeekend || isQuoteRequest;

        if (weekendRequestInput) {
          weekendRequestInput.value = isRequestOnly ? '1' : '0';
        }

        if (quoteRequestInput) {
          quoteRequestInput.value = isQuoteRequest ? '1' : '0';
        }

        document.querySelectorAll('[data-card-payment-ui]').forEach(function (element) {
          element.style.display = isRequestOnly ? 'none' : '';
        });

        if (isQuoteRequest) {
          showCartAlert('weekend', @json(__('20+ Circuits requires a booking request. Submit your details and our team will confirm the final price before payment.')));
        } else if (isWeekend) {
          showCartAlert('weekend', @json(__('Weekend appointments are request-only. Submit this request and our team will confirm availability before payment.')));
        } else {
          hideCartAlert('weekend');
        }

        if (paymentTitle) {
          paymentTitle.textContent = isQuoteRequest
            ? @json(__('Request booking'))
            : (isWeekend ? @json(__('Request weekend booking')) : @json(__('Pay securely by card')));
        }

        if (paymentCopy) {
          paymentCopy.textContent = isQuoteRequest
            ? @json(__('No payment will be taken now. Our team will confirm the final price first.'))
            : (isWeekend
              ? @json(__('No payment will be taken now. Our team will confirm weekend availability first.'))
              : @json(__('Enter your debit or credit card details below.')));
        }

        if (payButton) {
          payButton.innerHTML = isQuoteRequest
            ? @json(__('Request booking') . ' <i class="fal fa-long-arrow-right"></i>')
            : (isWeekend
              ? @json(__('Submit weekend request') . ' <i class="fal fa-long-arrow-right"></i>')
              : @json(__('Complete Payment') . ' <i class="fal fa-long-arrow-right"></i>'));
        }

        if (summaryPayButton) {
          summaryPayButton.innerHTML = isQuoteRequest
            ? @json(__('Request booking') . ' <i class="fal fa-long-arrow-right"></i>')
            : (isWeekend
              ? @json(__('Request weekend booking') . ' <i class="fal fa-long-arrow-right"></i>')
              : @json(__('Pay now') . ' <i class="fal fa-long-arrow-right"></i>'));
        }

        return isRequestOnly;
      }

      function toggleCartUrgency(button) {
        const wasSelected = button.classList.contains('is-selected');

        document.querySelectorAll('[data-cart-urgency-fee]').forEach(function (option) {
          option.classList.remove('is-selected');
          option.setAttribute('aria-pressed', 'false');
        });

        if (wasSelected) {
          selectedUrgencyFee = 0;
          selectedUrgencyLabel = '';
        } else {
          selectedUrgencyFee = parseFloat(button.dataset.cartUrgencyFee || '0') || 0;
          const urgencyLabel = button.querySelector('strong');
          selectedUrgencyLabel = urgencyLabel ? urgencyLabel.textContent.trim() : @json(__('Urgent booking fee'));
          button.classList.add('is-selected');
          button.setAttribute('aria-pressed', 'true');
        }

        updateCartDateAvailability();
        updateCartTotals();
      }

      document.addEventListener('change', function (event) {
        if (event.target && event.target.matches && event.target.matches('[data-cart-scheduling-mode]')) {
          setCartSchedulingMode(event.target.value);
          return;
        }

        if (event.target && event.target.id === 'cart_booking_date') {
          if (isPastCartBookingDate(event.target.value)) {
            event.target.value = '';
            showCartAlert('date', @json(__('Please choose today or a future booking date.')));
          } else {
            hideCartAlert('date');
          }

          updateWeekendRequestMode();
        }

        if (event.target.closest('.service-cart-page') && (event.target.classList.contains('cart-question-input') || event.target.classList.contains('question-input'))) {
          updateCartChoiceVisuals();
          updateSequentialCartQuestions();
          updateCartTotals();
        }

        // Keep pricing in sync if the customer edits the postcode on the info step
        // (its value is what the server uses to validate the total).
        if (event.target && event.target.matches && event.target.matches('[data-cart-info="zip_code"]')) {
          const postcodeInput = document.querySelector('[data-cart-postcode-input]');

          if (postcodeInput) {
            postcodeInput.value = event.target.value;
          }

          fetchCartPostalPrice();
        }
      });

      document.addEventListener('input', function (event) {
        if (event.target && event.target.matches && event.target.matches('[data-cart-postcode-input]')) {
          if (cartPostcodeCheckedValue !== event.target.value.trim()) {
            cartPostcodeCheckedValue = '';
            applyPostcodeSurcharge([]);
            hideCartAlert('postcode');
          }
        }

        if (event.target.closest('.service-cart-page') && (event.target.classList.contains('cart-question-input') || event.target.classList.contains('question-input'))) {
          updateCartQuantityDisplay(event.target.closest('.question-quantity-selector'));
          updateCartChoiceVisuals();
          updateSequentialCartQuestions();
          updateCartTotals();
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && event.target.closest('#cart-login-form')) {
          event.preventDefault();
          submitCartLogin();
        }

        if (event.key === 'Enter' && event.target.closest('#cart-signup-form')) {
          event.preventDefault();
          submitCartSignup();
        }

        if (event.key === 'Enter' && event.target.closest('[data-cart-postcode-panel]')) {
          event.preventDefault();
          fetchCartPostalPrice();
        }
      });

      document.addEventListener('blur', function (event) {
        if (event.target && event.target.matches && event.target.matches('[data-cart-postcode-input]')) {
          fetchCartPostalPrice();
        }
      }, true);

      document.addEventListener('submit', function (event) {
        if (event.target.closest('[data-service-coupon-form]')) {
          event.preventDefault();
          applyServiceCoupon();
        }
      });

      document.addEventListener('click', function (event) {
        const postcodeApply = event.target.closest('[data-cart-postcode-apply]');

        if (postcodeApply) {
          fetchCartPostalPrice();
          return;
        }

        const urgencyButton = event.target.closest('.service-cart-page [data-cart-urgency-fee]');
        const removeCoupon = event.target.closest('[data-service-coupon-remove]');

        if (removeCoupon) {
          removeServiceCoupon();
          return;
        }

        if (urgencyButton) {
          toggleCartUrgency(urgencyButton);
          return;
        }

        const button = event.target.closest('.service-cart-page .question-quantity-btn');

        if (!button) {
          return;
        }

        const selector = button.closest('.question-quantity-selector');
        const input = selector ? selector.querySelector('.question-quantity-input') : null;

        if (!input) {
          return;
        }

        const direction = parseInt(button.dataset.direction || '0', 10) || 0;
        const min = selector.dataset.min !== '' ? parseInt(selector.dataset.min, 10) : null;
        const max = selector.dataset.max !== '' ? parseInt(selector.dataset.max, 10) : null;
        let quantity = parseInt(input.value || selector.dataset.default || '1', 10) || 1;

        quantity += direction;

        if (min !== null && !Number.isNaN(min)) {
          quantity = Math.max(quantity, min);
        }

        if (max !== null && !Number.isNaN(max)) {
          quantity = Math.min(quantity, max);
        }

        input.value = quantity;
        updateCartQuantityDisplay(selector);
        updateCartTotals();
      });

      function updateCartQuantityDisplay(selector) {
        if (!selector) {
          return;
        }

        const input = selector.querySelector('.question-quantity-input');
        const down = selector.querySelector('.question-quantity-down');
        const up = selector.querySelector('.question-quantity-up');
        const min = selector.dataset.min !== '' ? parseInt(selector.dataset.min, 10) : null;
        const max = selector.dataset.max !== '' ? parseInt(selector.dataset.max, 10) : null;
        const quantity = parseInt(input.value || selector.dataset.default || '1', 10) || 1;

        if (down) {
          down.disabled = min !== null && !Number.isNaN(min) && quantity <= min;
        }

        if (up) {
          up.disabled = max !== null && !Number.isNaN(max) && quantity >= max;
        }
      }

      function updateCartChoiceVisuals() {
        document.querySelectorAll('.service-cart-page .booking-question-option').forEach(function (option) {
          const input = option.querySelector('input');
          option.classList.toggle('is-selected', !!input && input.checked);
        });
      }

      function isCartQuestionAnswered(card) {
        const inputs = Array.from(card.querySelectorAll('.question-input, .cart-question-input'));

        if (inputs.length === 0) {
          return true;
        }

        return inputs.some(function (input) {
          if (input.type === 'checkbox' || input.type === 'radio') {
            return input.checked;
          }

          return String(input.value || '').trim() !== '';
        });
      }

      function getCartQuestionCurrentAnswer(item, questionId) {
        const inputs = Array.from(item.querySelectorAll('.question-input[data-question-id="' + questionId + '"], .cart-question-input[data-question-id="' + questionId + '"]'));

        if (inputs.length === 0) {
          return '';
        }

        const firstInput = inputs[0];

        if (firstInput.type === 'checkbox') {
          return inputs.filter(function (input) {
            return input.checked;
          }).map(function (input) {
            return input.value;
          });
        }

        if (firstInput.type === 'radio') {
          const checkedInput = inputs.find(function (input) {
            return input.checked;
          });

          return checkedInput ? checkedInput.value : '';
        }

        return firstInput.value || '';
      }

      function evaluateCartQuestionCondition(currentAnswer, expectedOptionId, conditionType) {
        const hasAnswer = Array.isArray(currentAnswer)
          ? currentAnswer.length > 0
          : currentAnswer !== undefined && currentAnswer !== null && String(currentAnswer).trim() !== '';

        if (!hasAnswer) {
          return false;
        }

        if (Array.isArray(currentAnswer)) {
          const hasExpectedOption = currentAnswer.map(String).includes(String(expectedOptionId));

          if (conditionType === 'not_equals' || conditionType === 'not_contains') {
            return !hasExpectedOption;
          }

          return hasExpectedOption;
        }

        switch (conditionType) {
          case 'equals':
            return String(currentAnswer) === String(expectedOptionId);
          case 'not_equals':
            return String(currentAnswer) !== String(expectedOptionId);
          case 'contains':
            return String(currentAnswer).includes(String(expectedOptionId));
          case 'not_contains':
            return !String(currentAnswer).includes(String(expectedOptionId));
          default:
            return false;
        }
      }

      function clearCartQuestionInputs(card) {
        card.querySelectorAll('.question-input, .cart-question-input').forEach(function (input) {
          if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
            return;
          }

          if (input.classList.contains('question-quantity-input')) {
            const quantitySelector = input.closest('.question-quantity-selector');
            input.value = input.dataset.default || (quantitySelector && quantitySelector.dataset ? quantitySelector.dataset.default : '') || '';
            updateCartQuantityDisplay(quantitySelector);
            return;
          }

          input.value = '';
        });
      }

      function updateCartConditionalQuestions() {
        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          item.querySelectorAll('.question-card[data-has-conditions="true"]').forEach(function (card) {
            let conditions = [];

            try {
              conditions = JSON.parse(card.dataset.conditions || '[]');
            } catch (error) {
              conditions = [];
            }

            if (!Array.isArray(conditions) || conditions.length === 0) {
              return;
            }

            let result = null;

            conditions.forEach(function (condition) {
              const currentAnswer = getCartQuestionCurrentAnswer(item, condition.depends_on_question_id);
              const conditionMet = evaluateCartQuestionCondition(
                currentAnswer,
                condition.depends_on_option_id,
                condition.condition_type || 'equals'
              );

              if (result === null) {
                result = conditionMet;
                return;
              }

              if (condition.operator === 'OR') {
                result = result || conditionMet;
                return;
              }

              result = result && conditionMet;
            });

            const shouldShow = result === true;

            if (shouldShow) {
              card.style.display = '';
            } else {
              card.style.display = 'none';
              clearCartQuestionInputs(card);
            }
          });
        });
      }

      function updateSequentialCartQuestions() {
        updateCartConditionalQuestions();

        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          const cards = Array.from(item.querySelectorAll('.question-card')).filter(function (card) {
            return card.style.display !== 'none';
          });

          cards.forEach(function (card) {
            card.classList.add('is-sequence-hidden');
          });

          for (const card of cards) {
            card.classList.remove('is-sequence-hidden');

            if (card.dataset.required === 'true' && !isCartQuestionAnswered(card)) {
              break;
            }
          }
        });
      }

      function showCartStep(step) {
        document.querySelectorAll('[data-cart-step-panel]').forEach(function (panel) {
          panel.classList.toggle('is-active', panel.dataset.cartStepPanel === step);
        });

        const activePanel = document.querySelector('[data-cart-step-panel="' + step + '"]');

        if (activePanel) {
          if (window.isRestrictedInAppBrowser) {
            activePanel.scrollIntoView(true);
          } else {
            activePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }

        if (step === 'payment') {
          if (!updateWeekendRequestMode()) {
            mountCartStripeCard();
          }
        }
      }

      function hideCartAlert(name) {
        const alert = document.querySelector('[data-cart-alert="' + name + '"]');

        if (alert) {
          alert.style.display = 'none';
        }
      }

      function showCartAlert(name, message) {
        const alert = document.querySelector('[data-cart-alert="' + name + '"]');

        if (alert) {
          if (!alert.dataset.defaultMessage) {
            alert.dataset.defaultMessage = alert.textContent;
          }

          if (message) {
            alert.textContent = message;
          } else {
            alert.textContent = alert.dataset.defaultMessage;
          }

          alert.style.display = 'block';
        }
      }

      function validateCartQuestions() {
        let valid = true;

        updateSequentialCartQuestions();

        document.querySelectorAll('[data-cart-step-panel="questions"] .question-card[data-required="true"]').forEach(function (card) {
          if (card.style.display === 'none') {
            card.classList.remove('border-danger');
            return;
          }

          const hasChoice = isCartQuestionAnswered(card);

          card.classList.toggle('border-danger', !hasChoice && !card.classList.contains('is-sequence-hidden'));

          if (!hasChoice) {
            valid = false;
          }
        });

        if (valid) {
          hideCartAlert('questions');
        } else {
          showCartAlert('questions');
        }

        return valid;
      }

      function validateCartPostcode() {
        const input = document.querySelector('[data-cart-postcode-input]');
        const postcode = input ? input.value.trim() : '';

        if (postcode === '') {
          cartPostcodeCheckedValue = '';
          applyPostcodeSurcharge([]);
          showCartAlert('postcode', @json(__('Please enter your postcode and click Get price before continuing.')));
          if (input) {
            input.focus();
          }
          return false;
        }

        if (cartPostcodeCheckedValue !== postcode) {
          showCartAlert('postcode', @json(__('Please click Get price to confirm your postcode before continuing.')));
          if (input) {
            input.focus();
          }
          return false;
        }

        hideCartAlert('postcode');
        return true;
      }

      function validateCartDate() {
        if (cartSchedulingMode === 'tenant') {
          const name = tenantCartValue('name');
          const email = tenantCartValue('email');
          const phone = tenantCartValue('phone');
          let valid = true;

          document.querySelectorAll('[data-cart-tenant-error]').forEach(function (error) {
            error.textContent = '';
          });

          if (!name) {
            valid = false;
            const nameError = document.querySelector('[data-cart-tenant-error="name"]');
            if (nameError) nameError.textContent = @json(__('Tenant/occupier name is required.'));
          }

          if (!email && !phone) {
            valid = false;
            const contactError = document.querySelector('[data-cart-tenant-error="contact"]');
            if (contactError) contactError.textContent = @json(__('Enter at least an email address or phone number.'));
          }

          if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            valid = false;
            const emailError = document.querySelector('[data-cart-tenant-error="email"]');
            if (emailError) emailError.textContent = @json(__('Please enter a valid email address.'));
          }

          if (valid) {
            hideCartAlert('date');
          } else {
            showCartAlert('date', @json(__('Please complete the tenant/occupier contact details.')));
          }

          return valid;
        }

        const date = document.getElementById('cart_booking_date');
        const time = document.getElementById('cart_service_hour_id');
        const hasPastDate = !!date && isPastCartBookingDate(date.value);
        const hasDateOrUrgency = selectedUrgencyFee > 0 || (!!date && date.value !== '' && !hasPastDate);
        const hasTimeOrFourHourUrgency = isWithinFourHoursUrgency() || (!!time && time.value !== '');
        const valid = hasTimeOrFourHourUrgency && hasDateOrUrgency;

        if (valid) {
          hideCartAlert('date');
        } else if (hasPastDate) {
          showCartAlert('date', @json(__('Please choose a date at least 3 days from today, or use an urgent booking option above.')));
        } else {
          showCartAlert('date');
        }

        return valid;
      }

      function validateCartAuth() {
        if (cartIsAuthenticated || cartGuestCheckout) {
          hideCartAlert('auth');
          return true;
        }

        const authAlert = document.querySelector('[data-cart-alert="auth"]');

        if (authAlert) {
          authAlert.textContent = @json(__('Please login before continuing to customer information.'));
        }

        showCartAlert('auth');
        return false;
      }

      function applyCartLoginData(billingData) {
        const fieldMap = {
          name: 'name',
          phone: 'phone',
          email: 'email',
          address: 'address',
          zip_code: 'zip_code',
          country: 'country'
        };

        Object.keys(fieldMap).forEach(function (field) {
          const sourceValue = billingData[field] || '';
          const visibleInput = document.querySelector('[data-cart-info="' + field + '"]');
          const hiddenInput = document.getElementById('billing_' + field);

          if (visibleInput && String(visibleInput.value || '').trim() === '') {
            visibleInput.value = sourceValue;
          }

          if (hiddenInput && String(hiddenInput.value || '').trim() === '') {
            hiddenInput.value = sourceValue;
          }
        });

        const userIdInput = document.getElementById('userId');

        if (userIdInput) {
          userIdInput.value = billingData.user_id || '';
        }
      }

      function clearCartAuthErrors(scope) {
        const selector = scope ? scope + ' .em' : '[data-cart-step-panel="auth"] .em';

        document.querySelectorAll(selector).forEach(function (element) {
          element.textContent = '';
        });

        hideCartAlert('auth');
      }

      function showCartAuthErrors(response, prefix) {
        let hasFieldError = false;

        if (response.errors) {
          Object.keys(response.errors).forEach(function (field) {
            const fieldError = document.getElementById(prefix + field);

            if (fieldError) {
              fieldError.textContent = response.errors[field][0];
              hasFieldError = true;
            }
          });
        }

        if (response.error || response.message || !hasFieldError) {
          const authAlert = document.querySelector('[data-cart-alert="auth"]');

          if (authAlert) {
            authAlert.textContent = response.error || response.message || @json(__('Please check the highlighted fields and try again.'));
          }

          showCartAlert('auth');
        }
      }

      function setCartAuthMode(mode) {
        document.querySelectorAll('[data-cart-auth-tab]').forEach(function (tab) {
          const active = tab.dataset.cartAuthTab === mode;
          tab.classList.toggle('is-active', active);
          tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('[data-cart-auth-panel]').forEach(function (panel) {
          panel.classList.toggle('is-active', panel.dataset.cartAuthPanel === mode);
        });

        clearCartAuthErrors();
      }

      function submitCartLogin() {
        const form = document.getElementById('cart-login-form');

        if (!form || typeof $ === 'undefined') {
          return;
        }

        clearCartAuthErrors('#cart-login-form');
        $('.request-loader-time').addClass('show');

        $.ajax({
          type: 'POST',
          url: cartLoginUrl,
          data: $(form).serialize(),
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            $('.request-loader-time').removeClass('show');

            if (response.success) {
              cartIsAuthenticated = true;
              cartGuestCheckout = false;
              applyCartLoginData(response.billingData || {});
              hideCartAlert('auth');

              if (typeof toastr !== 'undefined') {
                toastr.success(response.success);
              }

              showCartStep('info');
              return;
            }

            showCartAuthErrors(response, 'err_cart_');
          },
          error: function (error) {
            $('.request-loader-time').removeClass('show');

            const response = error.responseJSON || {};

            showCartAuthErrors(response.error || response.message || response.errors ? response : {
              error: @json(__('Login failed. Please try again.'))
            }, 'err_cart_');
          }
        });
      }

      function submitCartSignup() {
        const form = document.getElementById('cart-signup-form');

        if (!form || typeof $ === 'undefined') {
          return;
        }

        clearCartAuthErrors('#cart-signup-form');
        $('.request-loader-time').addClass('show');

        $.ajax({
          type: 'POST',
          url: cartSignupUrl,
          data: $(form).serialize(),
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            $('.request-loader-time').removeClass('show');

            if (response.success) {
              cartIsAuthenticated = true;
              cartGuestCheckout = false;
              applyCartLoginData(response.billingData || {});
              hideCartAlert('auth');

              if (typeof toastr !== 'undefined') {
                toastr.success(response.success);
              }

              showCartStep('info');
              return;
            }

            showCartAuthErrors(response, 'err_cart_signup_');
          },
          error: function (error) {
            $('.request-loader-time').removeClass('show');

            const response = error.responseJSON || {};

            showCartAuthErrors(response.error || response.message || response.errors ? response : {
              error: @json(__('Account could not be created. Please try again.'))
            }, 'err_cart_signup_');
          }
        });
      }

      function validateCartInfo() {
        let valid = true;
        const requiredFields = ['name', 'phone', 'email', 'address'];

        document.querySelectorAll('[data-cart-info-error]').forEach(function (error) {
          error.textContent = '';
        });

        requiredFields.forEach(function (field) {
          const input = document.querySelector('[data-cart-info="' + field + '"]');
          const error = document.querySelector('[data-cart-info-error="' + field + '"]');

          if (!input || String(input.value || '').trim() !== '') {
            return;
          }

          valid = false;

          if (error) {
            error.textContent = @json(__('This field is required.'));
          }
        });

        const email = document.querySelector('[data-cart-info="email"]');
        const emailError = document.querySelector('[data-cart-info-error="email"]');

        if (email && email.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
          valid = false;

          if (emailError) {
            emailError.textContent = @json(__('Please enter a valid email address.'));
          }
        }

        if (valid) {
          hideCartAlert('info');
        } else {
          showCartAlert('info');
        }

        return valid;
      }

      function collectCartQuestionAnswers() {
        const answers = {};

        updateCartConditionalQuestions();

        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          const itemKey = item.dataset.itemKey;

          if (!itemKey) {
            return;
          }

          answers[itemKey] = {};

          item.querySelectorAll('.cart-question-input, .question-input').forEach(function (input) {
            const card = input.closest('.question-card');
            const questionId = input.dataset.questionId;

            if (!questionId || (card && card.style.display === 'none')) {
              return;
            }

            if (input.type === 'checkbox') {
              if (!Array.isArray(answers[itemKey][questionId])) {
                answers[itemKey][questionId] = [];
              }

              if (input.checked) {
                answers[itemKey][questionId].push(input.value);
              }

              return;
            }

            if (input.type === 'radio') {
              if (input.checked) {
                answers[itemKey][questionId] = input.value;
              }

              return;
            }

            answers[itemKey][questionId] = input.value;
          });
        });

        return answers;
      }

      function prepareCartPaymentForm() {
        const bookingDate = document.getElementById('cart_booking_date');
        const serviceHour = document.getElementById('cart_service_hour_id');
        const bookingDateInput = document.getElementById('bookingDate');
        const serviceHourInput = document.getElementById('serviceHourId');
        const totalInput = document.getElementById('total_price');
        const questionAnswersInput = document.getElementById('question_answers');
        const urgencyAmountInput = document.getElementById('urgency_surcharge_value');
        const urgencyPeriodInput = document.getElementById('urgency_surcharge_period_value');
        const weekendRequestInput = document.getElementById('weekend_request');
        const quoteRequestInput = document.getElementById('quote_request');
        const gatewayInput = document.getElementById('gateway');
        const stripeTokenInput = document.getElementById('stripeToken');
        const schedulingModeInput = document.getElementById('scheduling_mode');
        const isRequestOnly = updateWeekendRequestMode();

        if (schedulingModeInput) {
          schedulingModeInput.value = cartSchedulingMode;
        }

        ['name', 'email', 'phone', 'note'].forEach(function (field) {
          const target = document.getElementById('tenant_' + field);
          if (target) target.value = tenantCartValue(field);
        });

        if (bookingDateInput && bookingDate) {
          bookingDateInput.value = cartSchedulingMode === 'tenant' ? '' : bookingDate.value;
        }

        if (serviceHourInput && serviceHour) {
          serviceHourInput.value = cartSchedulingMode === 'tenant' || isWithinFourHoursUrgency() ? '' : serviceHour.value;
        }

        if (totalInput) {
          totalInput.value = String(calculateCartTotal());
        }

        if (questionAnswersInput) {
          questionAnswersInput.value = JSON.stringify(collectCartQuestionAnswers());
        }

        if (urgencyAmountInput) {
          urgencyAmountInput.value = String(selectedUrgencyFee || 0);
        }

        if (urgencyPeriodInput) {
          urgencyPeriodInput.value = selectedUrgencyLabel;
        }

        if (weekendRequestInput) {
          weekendRequestInput.value = isRequestOnly ? '1' : '0';
        }

        if (quoteRequestInput) {
          quoteRequestInput.value = isCartQuoteRequestSelected() ? '1' : '0';
        }

        const guestCheckoutInput = document.getElementById('guest_checkout');

        if (guestCheckoutInput) {
          guestCheckoutInput.value = (!cartIsAuthenticated && cartGuestCheckout) ? '1' : '0';
        }

        if (gatewayInput) {
          gatewayInput.value = isRequestOnly ? 'weekend_request' : 'stripe';
        }

        if (isRequestOnly && stripeTokenInput) {
          stripeTokenInput.value = '';
        }

        const paymentFieldMap = {
          name: 'billing_name',
          phone: 'billing_phone',
          email: 'billing_email',
          address: 'billing_address',
          zip_code: 'billing_zip_code',
          country: 'billing_country'
        };

        Object.keys(paymentFieldMap).forEach(function (field) {
          const source = document.querySelector('[data-cart-info="' + field + '"]');
          const target = document.getElementById(paymentFieldMap[field]);

          if (source && target) {
            target.value = source.value;
          }
        });
      }

      function submitCartPaymentForm(form) {
        if (!form || typeof $ === 'undefined') {
          if (form) {
            form.submit();
          }
          return;
        }

        $('.request-loader-time').addClass('show');

        const formData = new FormData(form);
        formData.set('total_price', String(calculateCartTotal()));
        formData.set('question_answers', JSON.stringify(collectCartQuestionAnswers()));
        formData.set('urgency_surcharge_amount', String(selectedUrgencyFee || 0));
        formData.set('urgency_surcharge_period', selectedUrgencyLabel);

        $.ajax({
          url: form.getAttribute('action'),
          method: form.getAttribute('method') || 'POST',
          data: formData,
          contentType: false,
          processData: false,
          headers: {
            Accept: 'application/json'
          },
          success: function (data) {
            if (data && data.redirectURL) {
              window.location.href = data.redirectURL;
              return;
            }

            if (typeof data === 'string') {
              let razorPayForm = document.getElementById('razorPayForm');

              if (!razorPayForm) {
                razorPayForm = document.createElement('div');
                razorPayForm.id = 'razorPayForm';
                document.body.appendChild(razorPayForm);
              }

              razorPayForm.innerHTML = data;
            }

            $('.request-loader-time').removeClass('show');
          },
          error: function (error) {
            const gatewayError = document.getElementById('err_gateway');
            const stripeErrors = document.getElementById('stripe-errors');
            let message = @json(__('An error occurred. Please try again.'));

            document.querySelectorAll('.em').forEach(function (element) {
              element.textContent = '';
            });

            if (error.status === 422 && error.responseJSON) {
              if (error.responseJSON.errors) {
                Object.keys(error.responseJSON.errors).forEach(function (field) {
                  const fieldError = document.getElementById('err_' + field);

                  if (fieldError) {
                    fieldError.textContent = error.responseJSON.errors[field][0];
                  }
                });
              }

              if (error.responseJSON.error) {
                message = error.responseJSON.error;
              }
            } else if (error.responseJSON && error.responseJSON.message) {
              message = error.responseJSON.message;
            } else if (error.responseJSON && error.responseJSON.error) {
              message = error.responseJSON.error;
            }

            if (gatewayError && gatewayError.textContent === '') {
              gatewayError.textContent = message;
            }

            if (stripeErrors) {
              stripeErrors.textContent = message;
            }

            $('.request-loader-time').removeClass('show');
          }
        });
      }

      document.addEventListener('click', function (event) {
        const next = event.target.closest('[data-cart-next]');
        const prev = event.target.closest('[data-cart-prev]');
        const login = event.target.closest('[data-cart-login]');
        const signup = event.target.closest('[data-cart-signup]');
        const guestContinue = event.target.closest('[data-cart-guest-continue]');
        const authTab = event.target.closest('[data-cart-auth-tab]');
        const summaryPay = event.target.closest('[data-cart-summary-pay]');
        const pay = event.target.closest('[data-cart-pay]');

        if (authTab) {
          setCartAuthMode(authTab.dataset.cartAuthTab || 'guest');
          return;
        }

        if (guestContinue) {
          cartGuestCheckout = true;
          hideCartAlert('auth');
          showCartStep('info');
          return;
        }

        if (login) {
          submitCartLogin();
          return;
        }

        if (signup) {
          submitCartSignup();
          return;
        }

        if (next) {
          const target = next.dataset.cartNext;

          if (target === 'date') {
            if (!validateCartPostcode()) {
              return;
            }

            if (!validateCartQuestions()) {
              return;
            }
          }

          if (target === 'info') {
            if (!validateCartDate()) {
              return;
            }

            if (!cartIsAuthenticated && !cartGuestCheckout) {
              showCartStep('auth');
              return;
            }
          }

          if (target === 'review') {
            if (!validateCartAuth()) {
              showCartStep('auth');
              return;
            }

            if (!validateCartInfo()) {
              return;
            }

            populateCartSummary();
          }

          if (target === 'payment') {
            if (!validateCartAuth()) {
              showCartStep('auth');
              return;
            }

            if (!validateCartInfo()) {
              return;
            }
          }

          showCartStep(target);
        }

        if (prev) {
          showCartStep(prev.dataset.cartPrev);
        }

        if (summaryPay) {
          if (!validateCartPostcode()) {
            showCartStep('questions');
            return;
          }

          if (!validateCartQuestions()) {
            showCartStep('questions');
            return;
          }

          if (!validateCartDate()) {
            showCartStep('date');
            return;
          }

          if (!validateCartAuth()) {
            showCartStep('auth');
            return;
          }

          if (!validateCartInfo()) {
            showCartStep('info');
            return;
          }

          populateCartSummary();
          showCartStep('review');
        }

        if (pay) {
          event.preventDefault();
          event.stopImmediatePropagation();

          if (!validateCartPostcode()) {
            showCartStep('questions');
            return;
          }

          if (!validateCartQuestions()) {
            showCartStep('questions');
            return;
          }

          if (!validateCartDate()) {
            showCartStep('date');
            return;
          }

          if (!validateCartAuth()) {
            showCartStep('auth');
            return;
          }

          if (!validateCartInfo()) {
            showCartStep('info');
            return;
          }

          const form = document.getElementById('payment-form');
          prepareCartPaymentForm();

          const gateway = document.getElementById('gateway');
          const gatewayError = document.getElementById('err_gateway');
          const weekendRequest = document.getElementById('weekend_request');
          const isWeekendRequest = weekendRequest && weekendRequest.value === '1';

          if (isWeekendRequest) {
            submitCartPaymentForm(form);
            return;
          }

          if (!gateway || gateway.value !== 'stripe') {
            if (gatewayError) {
              gatewayError.textContent = @json(__('Card payment is currently unavailable. Please try again later.'));
            }
            return;
          }

          if (!stripe || !stripeCard) {
            if (gatewayError) {
              gatewayError.textContent = @json(__('Card payment is currently unavailable. Please try again later.'));
            }
            return;
          }

          const requestLoader = document.querySelector('.request-loader-time');
          if (requestLoader) {
            requestLoader.classList.add('show');
          }
          stripe.createToken(stripeCard).then(function (result) {
            if (result.error) {
              if (requestLoader) {
                requestLoader.classList.remove('show');
              }
              if (gatewayError) {
                gatewayError.textContent = result.error.message;
              }
              return;
            }

            document.getElementById('stripeToken').value = result.token.id;
            submitCartPaymentForm(form);
          });
        }
      });

      function calculateCartTotal() {
        let total = 0;

        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          total += calculateItemTotal(item);
        });

        total += selectedUrgencyFee;
        total -= calculateCouponDiscount(total);

        return Number(total || 0).toFixed(2);
      }

      function escapeCartHtml(value) {
        return String(value == null ? '' : value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      function getCartItemAddons(item) {
        const addons = [];

        item.querySelectorAll('.cart-question-input, .question-input').forEach(function (input) {
          const card = input.closest('.question-card');

          if (card && card.style.display === 'none') {
            return;
          }

          const titleEl = card
            ? (card.querySelector('.card-title') || card.querySelector('.question-quantity-title'))
            : null;
          const label = titleEl ? titleEl.textContent.replace('*', '').trim() : '';

          if (input.type === 'checkbox' || input.type === 'radio') {
            if (!input.checked || isQuoteRequestChoice(input)) {
              return;
            }

            const price = parseFloat(input.dataset.price || '0') || 0;

            if (price > 0) {
              addons.push({ label: label, detail: input.dataset.optionText || '', price: price });
            }

            return;
          }

          if (input.dataset.questionType === 'quantity') {
            const quantity = parseInt(input.value || '0', 10) || 0;
            const defaultQuantity = parseInt(input.dataset.default || '0', 10) || 0;
            const unitPrice = parseFloat(input.dataset.unitPrice || '0') || 0;

            if (quantity > defaultQuantity && unitPrice > 0) {
              addons.push({ label: label, detail: '× ' + quantity, price: quantity * unitPrice });
            }
          }
        });

        return addons;
      }

      function cartInfoValue(field) {
        const input = document.querySelector('[data-cart-info="' + field + '"]');
        return input ? String(input.value || '').trim() : '';
      }

      function populateCartSummary() {
        const servicesContainer = document.getElementById('cart-summary-services');

        if (servicesContainer) {
          let servicesHtml = '';
          let subtotal = 0;

          document.querySelectorAll('[data-cart-item]').forEach(function (item) {
            const nameEl = item.querySelector('h4');
            const name = nameEl ? nameEl.textContent.trim() : @json(__('Service'));
            const basePrice = parseFloat(item.dataset.basePrice || '0') || 0;
            const postcodeSurcharge = parseFloat(item.dataset.postcodeSurcharge || '0') || 0;
            const itemTotal = calculateItemTotal(item);
            const itemDisplayTotal = calculateItemDisplayTotal(item);
            const isPending = isCartItemQuoteRequest(item) || (item.dataset.pricePending === 'true' && itemTotal <= 0);
            const addons = getCartItemAddons(item);

            subtotal += Math.max(itemTotal - postcodeSurcharge, 0);

            let addonHtml = '';
            addonHtml += '<div class="cart-summary-addon"><span>' + @json(__('Base price')) + '</span><span>' + formatCartPrice(basePrice) + '</span></div>';

            if (postcodeSurcharge > 0) {
              addonHtml += '<div class="cart-summary-addon"><span>' + @json(__('Postcode extra charge')) + '</span><span>+' + formatCartPrice(postcodeSurcharge) + '</span></div>';
            }

            addons.forEach(function (addon) {
              const detail = addon.detail ? '<small>' + escapeCartHtml(addon.detail) + '</small>' : '';
              addonHtml += '<div class="cart-summary-addon"><span>' + escapeCartHtml(addon.label) + detail + '</span><span>+' + formatCartPrice(addon.price) + '</span></div>';
            });

            const totalText = isPending ? pendingPriceLabel : formatCartPrice(itemDisplayTotal);

            servicesHtml += '<div class="cart-summary-service">'
              + '<div class="cart-summary-service-head">'
              + '<span class="cart-summary-service-name">' + escapeCartHtml(name) + '</span>'
              + '<span class="cart-summary-service-total">' + totalText + '</span>'
              + '</div>' + addonHtml + '</div>';
          });

          servicesContainer.innerHTML = servicesHtml;

          const subtotalEl = document.getElementById('cart-summary-subtotal');
          if (subtotalEl) {
            subtotalEl.textContent = formatCartPrice(subtotal);
          }
        }

        // Booking date & time
        const dateInput = document.getElementById('cart_booking_date');
        const timeInput = document.getElementById('cart_service_hour_id');
        const dateValue = dateInput ? dateInput.value || '' : '';
        const timeValue = timeInput ? timeInput.value || '' : '';
        let dateTimeText = '-';

        if (dateValue) {
          const dateObj = new Date(dateValue);
          dateTimeText = isNaN(dateObj.getTime())
            ? dateValue
            : dateObj.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });

          const timeText = isWithinFourHoursUrgency() ? (selectedUrgencyLabel || '') : timeValue;

          if (timeText) {
            dateTimeText += ' · ' + timeText;
          }
        }

        const dateTimeEl = document.getElementById('cart-summary-datetime');
        if (dateTimeEl) {
          dateTimeEl.textContent = dateTimeText;
        }

        // Site information
        const address = cartInfoValue('address');
        const zip = cartInfoValue('zip_code');
        const country = cartInfoValue('country');
        const fullAddress = [address, zip, country].filter(Boolean).join(', ');

        const fields = {
          'cart-summary-name': cartInfoValue('name') || '-',
          'cart-summary-email': cartInfoValue('email') || '-',
          'cart-summary-phone': cartInfoValue('phone') || '-',
          'cart-summary-address': fullAddress || '-'
        };

        Object.keys(fields).forEach(function (id) {
          const el = document.getElementById(id);
          if (el) {
            el.textContent = fields[id];
          }
        });

        // Payment summary
        let cartSubtotal = 0;
        let cartSubtotalWithoutPostcode = 0;
        document.querySelectorAll('[data-cart-item]').forEach(function (item) {
          const itemTotal = calculateItemTotal(item);
          const postcodeSurcharge = parseFloat(item.dataset.postcodeSurcharge || '0') || 0;

          cartSubtotal += itemTotal;
          cartSubtotalWithoutPostcode += Math.max(itemTotal - postcodeSurcharge, 0);
        });

        const urgency = Number(selectedUrgencyFee || 0);
        const postcodeTotal = getPostcodeSurchargeTotal();
        const discount = calculateCouponDiscount(cartSubtotal + urgency);

        const postcodeRow = document.getElementById('cart-summary-postcode-row');
        const postcodeValue = document.getElementById('cart-summary-postcode');
        const urgencyRow = document.getElementById('cart-summary-urgency-row');
        const urgencyLabel = document.getElementById('cart-summary-urgency-label');
        const urgencyValue = document.getElementById('cart-summary-urgency');
        const discountRow = document.getElementById('cart-summary-discount-row');
        const discountValue = document.getElementById('cart-summary-discount');
        const totalValue = document.getElementById('cart-summary-total');

        if (postcodeRow) {
          postcodeRow.style.display = postcodeTotal > 0 ? '' : 'none';
        }
        if (postcodeValue) {
          postcodeValue.textContent = '+' + formatCartPrice(postcodeTotal);
        }
        const paymentSubtotalEl = document.getElementById('cart-summary-subtotal');
        if (paymentSubtotalEl) {
          paymentSubtotalEl.textContent = formatCartPrice(cartSubtotalWithoutPostcode);
        }
        if (urgencyRow) {
          urgencyRow.style.display = urgency > 0 ? '' : 'none';
        }
        if (urgencyLabel) {
          urgencyLabel.textContent = selectedUrgencyLabel || '';
        }
        if (urgencyValue) {
          urgencyValue.textContent = '+' + formatCartPrice(urgency);
        }
        if (discountRow) {
          discountRow.style.display = discount > 0 ? '' : 'none';
        }
        if (discountValue) {
          discountValue.textContent = '-' + formatCartPrice(discount);
        }
        if (totalValue) {
          totalValue.textContent = formatCartPrice(Number(calculateCartTotal()));
        }
      }

      document.querySelectorAll('.service-cart-page .question-quantity-selector').forEach(updateCartQuantityDisplay);
      updateCartDateAvailability();
      updateCartChoiceVisuals();
      updateSequentialCartQuestions();
      updateCartTotals();

      // If a postcode is already known, resolve its price on load.
      (function () {
        const postcodeInput = document.querySelector('[data-cart-postcode-input]');

        if (postcodeInput && postcodeInput.value.trim() !== '') {
          fetchCartPostalPrice();
        }
      })();
    })();
  </script>
@endsection
