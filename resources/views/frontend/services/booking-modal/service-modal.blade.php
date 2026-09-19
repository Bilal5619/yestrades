<style>
  .question-card-quantity {
    border-left-width: 0 !important;
  }

  .question-quantity-row {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
  }

  .question-quantity-controls {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    flex-shrink: 0;
  }

  .question-quantity-btn {
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 4px;
    font-size: 2rem;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease;
  }

  .question-quantity-btn:hover:not(:disabled) {
    transform: translateY(-1px);
  }

  .question-quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .question-quantity-down {
    background: #cfd6e2;
    color: #fff;
  }

  .question-quantity-up {
    background: var(--color-primary);
    color: #fff;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
  }

  .question-quantity-input {
    width: 72px;
    height: 64px;
    border: 1px solid rgba(0, 0, 0, 0.16);
    border-radius: 4px;
    text-align: center;
    font-size: 1.85rem;
    font-weight: 500;
    color: #1f2937;
    padding: 0.4rem 0.3rem;
    background: #fff;
    appearance: textfield;
    -moz-appearance: textfield;
  }

  .question-quantity-input::-webkit-outer-spin-button,
  .question-quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .question-quantity-copy {
    min-width: 220px;
    flex: 1 1 260px;
  }

  .question-quantity-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 500;
    color: var(--heading-color, #1f2937);
  }

  .question-quantity-meta {
    display: block;
    margin-top: 0.35rem;
    font-size: 0.9rem;
    color: #6b7280;
  }

  #makeBooking .booking-question-options {
    display: grid;
    gap: 12px;
  }

  #makeBooking .booking-question-option {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    min-height: 48px;
    padding: 13px 14px;
    border: 1px solid #e3e7ed;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    position: relative;
    transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  #makeBooking .booking-question-option:hover {
    border-color: var(--color-primary);
    box-shadow: 0 10px 24px rgba(17, 24, 39, 0.06);
    transform: translateY(-1px);
  }

  #makeBooking .booking-question-option:has(.question-input:checked),
  #makeBooking .booking-question-option.is-selected {
    border-color: var(--color-primary);
    background: rgba(var(--color-primary-rgb), 0.06);
    box-shadow: inset 4px 0 0 var(--color-primary), 0 12px 26px rgba(var(--color-primary-rgb), 0.10);
  }

  #makeBooking .booking-question-option:has(.question-input:focus) {
    box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.12);
  }

  #makeBooking .booking-question-option:focus-within {
    box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.12);
  }

  #makeBooking .booking-question-option input {
    width: 16px;
    height: 16px;
    flex: 0 0 16px;
    margin: 2px 0 0;
    accent-color: var(--color-primary);
  }

  #makeBooking .booking-question-option-image {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    object-fit: cover;
    border: 1px solid #e1e5ec;
    border-radius: 8px;
    background: #ffffff;
  }

  #makeBooking .booking-question-option-text {
    color: #111827;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.45;
    overflow-wrap: anywhere;
  }

  #makeBooking .modal-dialog {
    max-width: 760px !important;
    width: calc(100% - 32px) !important;
  }

  #makeBooking .modal-content {
    border: 1px solid rgba(17, 24, 39, 0.08) !important;
    border-radius: 14px !important;
    background: #fff;
    box-shadow: 0 28px 80px rgba(17, 24, 39, 0.22) !important;
    overflow: hidden;
  }

  #makeBooking .modal-body {
    background: #fff;
    padding: 0 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    max-height: 90vh !important;
  }

  #makeBooking .btn-close {
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 8px 20px rgba(17, 24, 39, 0.08);
    z-index: 5;
  }

  #makeBooking #booking-stepper {
    background: #fff;
  }

  #makeBooking #booking-stepper .bs-stepper-header {
    gap: 8px;
    padding: 18px 54px 16px 22px !important;
    margin: 0 !important;
    background: #f7f8fa;
    border-bottom: 1px solid #e3e7ed;
    overflow-x: auto !important;
  }

  #makeBooking #booking-stepper .step {
    min-width: 96px !important;
    max-width: none !important;
    margin: 0 !important;
  }

  #makeBooking #booking-stepper .step::before {
    border-bottom-color: #e5e7eb;
    top: 45%;
  }

  #makeBooking #booking-stepper .step-trigger {
    gap: 6px;
    padding: 0 4px !important;
    background: transparent !important;
  }

  #makeBooking #booking-stepper .step .h3 {
    display: block !important;
    font-size: 11px !important;
    font-weight: 700;
    color: #9ca3af;
    -webkit-text-fill-color: currentColor;
    -webkit-text-stroke-width: 0;
  }

  #makeBooking #booking-stepper .bs-stepper-circle {
    width: 30px !important;
    height: 30px !important;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    font-size: 12px !important;
    box-shadow: 0 4px 12px rgba(17, 24, 39, 0.04);
  }

  #makeBooking #booking-stepper .active .bs-stepper-circle,
  #makeBooking #booking-stepper .active-prev .bs-stepper-circle {
    border-color: var(--color-primary);
    background: var(--color-primary);
    color: #fff;
    box-shadow: 0 10px 18px rgba(var(--color-primary-rgb), 0.20);
  }

  #makeBooking #booking-stepper .bs-stepper-label {
    max-width: 92px !important;
    color: #6b7280;
    font-size: 11px !important;
    font-weight: 600;
    line-height: 1.2;
  }

  #makeBooking #booking-stepper .active .bs-stepper-label {
    color: var(--color-primary);
  }

  #makeBooking #booking-stepper .bs-stepper-content {
    padding: 28px 36px 0 !important;
    background: #fff;
  }

  #makeBooking #booking-stepper .container {
    max-width: none;
    padding-inline: 0;
  }

  #makeBooking .booking-confirmation-only [data-target="#questions"],
  #makeBooking .booking-confirmation-only [data-target="#time"],
  #makeBooking .booking-confirmation-only [data-target="#info"],
  #makeBooking .booking-confirmation-only [data-target="#summary"],
  #makeBooking .booking-confirmation-only [data-target="#payment"],
  #makeBooking .booking-confirmation-only #questions,
  #makeBooking .booking-confirmation-only #time,
  #makeBooking .booking-confirmation-only #info,
  #makeBooking .booking-confirmation-only #summary,
  #makeBooking .booking-confirmation-only #payment {
    display: none !important;
  }

  #makeBooking .booking-confirmation-only .bs-stepper-header {
    justify-content: center;
    overflow-x: hidden !important;
  }

  #makeBooking .booking-confirmation-only .bs-stepper-content {
    padding-top: 0 !important;
  }

  #makeBooking .booking-confirmation-only #confirm {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
  }

  #makeBooking .section-title {
    margin-bottom: 16px !important;
  }

  #makeBooking .section-title .title {
    width: 100%;
    margin: 0 auto 4px !important;
    color: #111827;
    font-size: 20px !important;
    font-weight: 700;
    line-height: 1.25;
  }

  #makeBooking .section-title .text-muted {
    margin-bottom: 0 !important;
    color: #6b7280 !important;
    font-size: 13px !important;
  }

  #makeBooking .questions-area,
  #makeBooking .calender-area,
  #makeBooking .authentication-area,
  #makeBooking .summary-area,
  #makeBooking .payment-area,
  #makeBooking .confirm-area {
    padding: 8px 0 0 !important;
    background: transparent;
    border: 0;
    border-radius: 0;
    box-shadow: none;
  }

  #makeBooking .question-card {
    border: 1px solid #e6eaf0 !important;
    border-radius: 10px !important;
    background: #fff;
    box-shadow: none;
    overflow: hidden;
  }

  #makeBooking .question-card .card-body {
    padding: 18px;
  }

  #makeBooking .question-card h5,
  #makeBooking .question-card label {
    color: #111827;
    font-weight: 700;
  }

  #makeBooking .form-group {
    margin-bottom: 12px;
  }

  #makeBooking .form-group label,
  #makeBooking .form-label {
    margin-bottom: 6px;
    color: #374151 !important;
    font-size: 12px;
    font-weight: 700;
  }

  #makeBooking .form-control,
  #makeBooking .form-select,
  #makeBooking select.form-control {
    min-height: 42px;
    height: auto !important;
    border: 1px solid #dfe3e8 !important;
    border-radius: 6px !important;
    background-color: #fff !important;
    color: #111827;
    font-size: 14px !important;
    box-shadow: none !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  #makeBooking select.form-control,
  #makeBooking .form-select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 16 16'%3E%3Cpath fill='%236b7280' d='M4.2 6.2a1 1 0 0 1 1.4 0L8 8.6l2.4-2.4a1 1 0 1 1 1.4 1.4l-3.1 3.1a1 1 0 0 1-1.4 0L4.2 7.6a1 1 0 0 1 0-1.4z'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    background-size: 14px 14px !important;
    padding-right: 36px !important;
  }

  #makeBooking .form-control:focus,
  #makeBooking .form-select:focus {
    border-color: var(--color-primary) !important;
    box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.12) !important;
  }

  #makeBooking .billing-same-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 8px 0 14px;
    padding: 12px 14px;
    border: 1px solid #e3e7ed;
    border-radius: 8px;
    background: #f7f8fa;
  }

  #makeBooking .billing-same-toggle .form-check-input {
    flex: 0 0 auto;
    float: none;
    margin: 0;
  }

  #makeBooking .billing-same-toggle .form-check-label {
    margin: 0;
    color: #374151 !important;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.35;
  }

  #makeBooking .auth-form {
    max-width: 520px;
    margin: 0 auto;
    padding: 22px;
    border: 1px solid #e6eaf0;
    border-radius: 10px;
    background: #fff;
  }

  #makeBooking #login-form .title {
    margin-bottom: 20px !important;
  }

  #makeBooking #login-form .title .h3 {
    color: #111827;
    font-size: 20px;
    font-weight: 700;
  }

  #makeBooking #login-form > .btn,
  #makeBooking #guest_checkout {
    width: 100%;
    min-height: 44px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
  }

  #makeBooking #guest_checkout {
    margin-top: 18px;
    background: #fff !important;
    border: 1px solid #e3e7ed !important;
    color: #374151 !important;
    box-shadow: none !important;
    position: relative;
  }

  #makeBooking #guest_checkout::before {
    content: "";
    display: block;
    width: 100%;
    height: 1px;
    margin: -18px 0 14px;
    background: #eceff3;
  }

  #makeBooking #guest_checkout:hover {
    border-color: var(--color-primary) !important;
    background: rgba(var(--color-primary-rgb), 0.04) !important;
    color: var(--color-primary) !important;
    transform: translateY(-1px);
  }

  #makeBooking .booking-calendar,
  #makeBooking .booking-time {
    border: 1px solid #e3e7ed;
    border-radius: 10px;
    background: #f7f8fa;
  }

  #makeBooking .booking-calendar {
    max-width: 420px !important;
    margin: 0 auto 14px !important;
    padding: 12px;
  }

  #makeBooking .booking-time {
    max-width: 560px;
    margin: 14px auto 0 !important;
    padding: 16px;
  }

  #makeBooking .booking-time .houre-title-1 {
    margin-bottom: 10px;
    padding-bottom: 0 !important;
    color: #374151;
    font-size: 13px;
    font-weight: 700;
  }

  #makeBooking .booking-time-slider {
    margin-bottom: 0 !important;
  }

  #makeBooking #billing-form > .row {
    margin: 0;
    padding: 18px;
    border: 1px solid #e6eaf0;
    border-radius: 10px;
    background: #fff;
  }

  #makeBooking #billing-form > .row > [class*="col-"] {
    padding-right: 8px;
    padding-left: 8px;
  }

  #makeBooking #alternate_billing_fields {
    margin-right: 0;
    margin-left: 0;
    padding: 14px !important;
    border: 1px solid rgba(var(--color-primary-rgb), 0.18);
    border-radius: 10px;
    background: rgba(var(--color-primary-rgb), 0.04);
  }

  #makeBooking .cart-summary {
    margin-top: 12px;
    border: 1px solid #eceff3;
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
  }

  #makeBooking .cart-summary .summary-header {
    padding: 10px 12px;
    border-bottom: 1px solid #eceff3;
    background: #f9fafb;
  }

  #makeBooking .cart-summary .summary-title {
    margin: 0;
    color: #111827;
    font-size: 13px;
    font-weight: 700;
  }

  #makeBooking .cart-summary .summary-content {
    padding: 10px 12px;
  }

  #makeBooking .cart-summary .summary-item {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 6px 0;
    border-bottom: 1px solid #f0f2f5;
  }

  #makeBooking .cart-summary .summary-item:last-child {
    border-bottom: 0;
  }

  #makeBooking .urgency-surcharge-info {
    margin: 0 auto 18px;
    max-width: 760px;
    border: 1px solid #f2d39b;
    border-radius: 8px;
    background: #fff8e8;
    padding: 14px 16px;
    color: #5f4320;
  }

  #makeBooking .urgency-surcharge-info-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    color: #3f2a12;
    font-size: 14px;
    font-weight: 700;
  }

  #makeBooking .urgency-surcharge-info-title i {
    color: #c47a12;
  }

  #makeBooking .urgency-surcharge-info p {
    margin-bottom: 10px;
    color: #6f5431;
    font-size: 13px;
    line-height: 1.55;
  }

  #makeBooking .urgency-surcharge-tiers {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
    margin: 0;
  }

  #makeBooking .urgency-surcharge-tier {
    border: 1px solid rgba(196, 122, 18, 0.22);
    border-radius: 6px;
    background: #fff;
    padding: 8px 10px;
  }

  #makeBooking .urgency-surcharge-tier strong {
    display: block;
    color: #3f2a12;
    font-size: 13px;
  }

  #makeBooking .urgency-surcharge-tier span {
    display: block;
    margin-top: 2px;
    color: #8a5b1f;
    font-size: 12px;
  }

  #makeBooking .btn-groups {
    display: flex !important;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
    position: sticky;
    bottom: -1px;
    z-index: 4;
    left: 0;
    right: 0;
    margin: 28px -36px 0 !important;
    padding: 16px 36px !important;
    width: calc(100% + 72px) !important;
    border-top: 1px solid #e3e7ed;
    background: #f7f8fa;
    box-sizing: border-box;
  }

  #makeBooking .btn-groups .booking-cancel-btn {
    margin-right: auto;
  }

  #makeBooking .btn-groups > .btn-text {
    min-height: 44px;
    padding: 12px 18px !important;
    border-radius: 8px;
    font-size: 14px !important;
    font-weight: 700;
    line-height: 1.2;
    text-decoration: none !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  }

  #makeBooking .btn-groups > .btn-text.icon-start {
    border: 1px solid #dfe3e8;
    background: #fff;
    color: #374151 !important;
  }

  #makeBooking .btn-groups > .btn-text.booking-cancel-btn {
    border: 1px solid transparent;
    background: transparent;
    color: #6b7280 !important;
    box-shadow: none;
  }

  #makeBooking .btn-groups > .btn-text.icon-end {
    border: 1px solid var(--color-primary);
    background: var(--color-primary);
    color: #fff !important;
    box-shadow: 0 12px 26px rgba(var(--color-primary-rgb), 0.22);
  }

  #makeBooking .btn-groups > .btn-text:hover {
    transform: translateY(-1px);
  }

  #makeBooking .booking-summary-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: none !important;
    padding: 0 !important;
  }

  #makeBooking .summary-card {
    margin: 0 !important;
    padding: 16px 20px !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 10px !important;
    background: #fff !important;
    box-shadow: 0 2px 8px rgba(17, 24, 39, 0.04);
  }

  #makeBooking .summary-card-header {
    margin: 0 0 10px !important;
    padding: 0 0 8px !important;
    border-bottom: 1px solid #eceff3 !important;
    color: #111827 !important;
    font-size: 13px !important;
    font-weight: 700 !important;
  }

  #makeBooking .summary-row {
    gap: 12px;
    align-items: flex-start !important;
    padding: 7px 0 !important;
    border-bottom: 1px solid #f1f3f5 !important;
  }

  #makeBooking .summary-row:last-child {
    border-bottom: 0 !important;
    padding-bottom: 0 !important;
  }

  #makeBooking .summary-label {
    flex: 0 0 35%;
    color: #6b7280 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
  }

  #makeBooking .summary-value {
    flex: 1 1 auto;
    max-width: none !important;
    color: #111827 !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    line-height: 1.4;
    text-align: right !important;
    overflow-wrap: anywhere;
    white-space: normal;
  }

  #makeBooking .summary-total {
    margin-top: 10px !important;
    padding: 10px 14px !important;
    border: 0 !important;
    border-radius: 8px !important;
    background: var(--color-primary) !important;
  }

  #makeBooking .summary-total .summary-label,
  #makeBooking .summary-total .summary-value {
    color: #fff !important;
    font-size: 14px !important;
  }

  #makeBooking .summary-total .summary-label {
    flex: 0 0 auto;
  }

  #makeBooking .payment-form {
    width: 100% !important;
    max-width: 620px;
    margin-right: auto;
    margin-left: auto;
  }

  #makeBooking .featured-payment-option {
    margin-bottom: 14px;
  }

  #makeBooking .payment-methods-grid {
    gap: 10px;
    padding: 0;
    grid-template-columns: 1fr;
  }

  #makeBooking .payment-method-card {
    min-height: 76px;
    border: 1px solid #e3e7ed;
    border-radius: 10px;
    background: #fff;
    box-shadow: none;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 14px !important;
    padding: 15px 16px !important;
    text-align: left !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  #makeBooking .payment-card-featured {
    min-height: 78px !important;
    border-color: var(--color-primary) !important;
    background: rgba(var(--color-primary-rgb), 0.04) !important;
    color: #111827 !important;
    box-shadow: none !important;
  }

  #makeBooking .payment-icon {
    flex: 0 0 42px;
    width: 42px;
    height: 42px;
    margin: 0 !important;
    border-radius: 50%;
    background: #f7f8fa;
    color: #4b5563;
    font-size: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  #makeBooking .payment-name,
  #makeBooking .payment-name-featured {
    flex: 1 1 auto;
    margin: 0 !important;
    color: #111827;
    font-size: 13px !important;
    font-weight: 700;
    line-height: 1.25;
  }

  #makeBooking .payment-brands {
    flex: 0 0 auto;
    margin: 0 30px 0 auto !important;
    color: #4b5563 !important;
  }

  #makeBooking .payment-card-featured .payment-icon,
  #makeBooking .payment-card-featured .payment-name-featured,
  #makeBooking .payment-card-featured .payment-check,
  #makeBooking .payment-card-featured .payment-brands,
  #makeBooking .payment-card-featured .payment-name-featured small {
    color: inherit !important;
  }

  #makeBooking .payment-card-featured .payment-icon,
  #makeBooking .payment-method-card.selected .payment-icon,
  #makeBooking .payment-check {
    color: var(--color-primary) !important;
  }

  #makeBooking .payment-card-featured .payment-icon,
  #makeBooking .payment-method-card.selected .payment-icon {
    background: rgba(var(--color-primary-rgb), 0.10);
  }

  #makeBooking .payment-check {
    top: 50%;
    right: 14px;
    transform: translateY(-50%);
  }

  #makeBooking .payment-method-card:hover,
  #makeBooking .payment-method-card.selected {
    border-color: var(--color-primary);
    background: rgba(var(--color-primary-rgb), 0.06);
    box-shadow: 0 10px 22px rgba(var(--color-primary-rgb), 0.12);
    transform: translateY(-1px);
  }

  #makeBooking #stripe-element {
    border: 1px solid #e3e7ed !important;
    border-radius: 10px !important;
    background: #f7f8fa !important;
  }

  #makeBooking .card-payment-info {
    padding: 12px 14px;
    border: 1px solid #dbe8df;
    border-radius: 10px;
    background: #f4fbf6;
    color: #3f624b !important;
  }

  #makeBooking .confirm-area .card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: none;
    overflow: hidden;
  }

  #makeBooking .confirm-area .card-header {
    border-bottom: 1px solid #eceff3;
    background: #f9fafb;
  }

  #makeBooking .confirm-area .payment-information .row {
    padding: 7px 0;
    border-bottom: 1px solid #f1f3f5;
  }

  #makeBooking .confirm-area .payment-information .row:last-child {
    border-bottom: 0;
  }

  #makeBooking .booking-confirmation {
    max-width: 720px;
    margin: 0 auto;
    text-align: center;
  }

  #makeBooking .confirmation-badge {
    width: 58px;
    height: 58px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    border-radius: 50%;
    background: #ecfdf3;
    color: #16a34a;
    font-size: 26px;
    box-shadow: inset 0 0 0 7px #f6fef9;
  }

  #makeBooking .confirmation-title {
    margin: 0 0 5px;
    color: #111827;
    font-size: 23px;
    font-weight: 800;
    line-height: 1.2;
  }

  #makeBooking .confirmation-service {
    margin: 0 0 8px;
    color: var(--color-primary);
    font-size: 16px;
    font-weight: 800;
    line-height: 1.35;
  }

  #makeBooking .confirmation-copy {
    max-width: 460px;
    margin: 0 auto 14px;
    color: #6b7280;
    font-size: 13px;
    line-height: 1.45;
  }

  #makeBooking .confirmation-number {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    padding: 7px 13px;
    border: 1px solid rgba(var(--color-primary-rgb), 0.18);
    border-radius: 999px;
    background: rgba(var(--color-primary-rgb), 0.06);
    color: #111827;
    font-size: 13px;
    font-weight: 700;
  }

  #makeBooking .confirmation-receipt {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    text-align: left;
  }

  #makeBooking .confirmation-box {
    border: 1px solid #e3e7ed;
    border-radius: 12px;
    background: #fff;
    overflow: hidden;
  }

  #makeBooking .confirmation-box-title {
    margin: 0;
    padding: 11px 14px;
    border-bottom: 1px solid #f0f2f5;
    background: #f7f8fa;
    color: #111827;
    font-size: 13px;
    font-weight: 800;
  }

  #makeBooking .confirmation-row {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
  }

  #makeBooking .confirmation-row:last-child {
    border-bottom: 0;
  }

  #makeBooking .confirmation-label {
    flex: 0 0 42%;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
  }

  #makeBooking .confirmation-value {
    flex: 1 1 auto;
    color: #111827;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.45;
    text-align: right;
    overflow-wrap: anywhere;
  }

  @media (max-width: 575.98px) {
    #makeBooking .confirmation-receipt {
      grid-template-columns: 1fr;
    }

    #makeBooking .confirmation-row {
      flex-direction: column;
      align-items: flex-start;
      gap: 4px;
    }

    #makeBooking .confirmation-label {
      flex: 0 0 auto;
    }

    #makeBooking .confirmation-value {
      flex: 0 1 auto;
      min-width: 0;
      text-align: left;
    }
  }

  @media (max-width: 575.98px) {
    .question-quantity-row {
      align-items: flex-start;
      gap: 1rem;
    }

    .question-quantity-input {
      width: 64px;
      height: 58px;
      font-size: 1.6rem;
    }

    .question-quantity-btn {
      width: 40px;
      height: 40px;
      font-size: 1.7rem;
    }

    #makeBooking .modal-content {
      border-radius: 0;
    }

    #makeBooking .modal-dialog {
      width: 100% !important;
    }

    #makeBooking #booking-stepper .bs-stepper-header {
      padding: 12px 46px 10px 12px !important;
    }

    #makeBooking #booking-stepper .step {
      min-width: 76px !important;
    }

    #makeBooking #booking-stepper .bs-stepper-content {
      padding: 16px 12px 0 !important;
    }

    #makeBooking .questions-area,
    #makeBooking .calender-area,
    #makeBooking .authentication-area,
    #makeBooking .summary-area,
    #makeBooking .payment-area,
    #makeBooking .confirm-area {
      padding: 16px 12px !important;
      border-radius: 0;
    }

    #makeBooking .btn-groups {
      margin: 22px -12px 0 !important;
      padding: 14px 12px !important;
      width: calc(100% + 24px) !important;
    }

    #makeBooking .summary-row,
    #makeBooking .cart-summary .summary-item {
      flex-direction: column;
      gap: 3px;
    }

    #makeBooking .urgency-surcharge-tiers {
      grid-template-columns: 1fr;
    }

    #makeBooking .summary-value {
      text-align: left !important;
    }

    #makeBooking .btn-groups > .btn-text {
      width: 100%;
      justify-content: center;
      text-align: center;
    }

    #makeBooking .btn-groups .booking-cancel-btn {
      margin-right: 0;
      order: 3;
    }
  }
</style>

@php
  $isConfirmationOnly = isset($bookingInfo);
@endphp

<div class="bs-stepper {{ $isConfirmationOnly ? 'booking-confirmation-only' : '' }}" id="booking-stepper">
  <div class="bs-stepper-header" role="tablist">
    <!-- your steps here -->

    <div class="step" data-target="#questions">
      <button type="button" class="step-trigger" role="tab" aria-controls="questions" id="questions-trigger">
        <span class="h3 mb-1">01</span>
        <span class="bs-stepper-circle"><i class="fal fa-list-ul"></i></span>
        <span class="bs-stepper-label">{{ __('Questions') }}</span>
      </button>
    </div>
    <div class="step" data-target="#time">
      <button type="button" class="step-trigger" role="tab" aria-controls="time" id="time-trigger">
        <span class="h3 mb-1">02</span>
        <span class="bs-stepper-circle"><i class="fal fa-clock"></i></span>
        <span class="bs-stepper-label">{{ __('Date & Time') }}</span>
      </button>
    </div>
    <div class="step" data-target="#info">
      <button type="button" class="step-trigger" role="tab" aria-controls="info" id="info-trigger">
        <span class="h3 mb-1">03</span>
        <span class="bs-stepper-circle"><i class="fal fa-clipboard-list-check"></i></span>
        <span class="bs-stepper-label">{{ __('Information') }}</span>
      </button>
    </div>
    <div class="step" data-target="#summary">
      <button type="button" class="step-trigger" role="tab" aria-controls="summary" id="summary-trigger">
        <span class="h3 mb-1">04</span>
        <span class="bs-stepper-circle"><i class="fal fa-list-alt"></i></span>
        <span class="bs-stepper-label">{{ __('Summary') }}</span>
      </button>
    </div>
    <div class="step" data-target="#payment">
      <button type="button" class="step-trigger" role="tab" aria-controls="payment" id="payment-trigger">
        <span class="h3 mb-1">05</span>
        <span class="bs-stepper-circle"><i class="fal fa-credit-card"></i></span>
        <span class="bs-stepper-label">{{ __('Payment') }}</span>
      </button>
    </div>
    <div class="step {{ $isConfirmationOnly ? 'active' : '' }}" data-target="#confirm">
      <button type="button" class="step-trigger" role="tab" aria-controls="confirm" id="confirm-trigger">
        <span class="h3 mb-1">{{ $isConfirmationOnly ? '01' : '06' }}</span>
        <span class="bs-stepper-circle"><i class="fal fa-check-circle"></i></span>
        <span class="bs-stepper-label">{{ __('Confirmation') }}</span>
      </button>
    </div>
  </div>
  <div class="bs-stepper-content">
    <div class="container">
      <div id="time" class="bs-stepper-pane fade" role="tabpanel" aria-labelledby="time-trigger">
        <div class="calender-area pt-4">
          <div class="section-title title-center ">
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
              <div class="urgency-surcharge-tier">
                <strong>{{ __('Within 48 hours') }}</strong>
                <span>{{ __('+£50') }}</span>
              </div>
              <div class="urgency-surcharge-tier">
                <strong>{{ __('Within 24 hours') }}</strong>
                <span>{{ __('+£100') }}</span>
              </div>
              <div class="urgency-surcharge-tier">
                <strong>{{ __('Within 4 hours') }}</strong>
                <span>{{ __('+£150') }}</span>
              </div>
            </div>
          </div>
          <div class="booking-calendar mb-10"></div>
          <div class="booking-time" style="margin-bottom: 30px;">
            <h6 class="text-center pb-20 houre-title-1">
              {{ __('Please Select a Date for Available Schedule') }}
            </h6>
            <div class="swiper booking-time-slider" style="margin-bottom: 20px;">
              <!-- Here Service Hour -->
            </div>
          </div>

          <div id="urgency-surcharge-notice" style="display:none;" class="alert alert-warning mb-3">
            <strong>{{ __('Urgent Booking Fee') }}:</strong> <span id="urgency-surcharge-text"></span>
          </div>

          <div class="btn-groups justify-content-center w-100">
            <a href="javaScript:void(0)" class="btn-text booking-cancel-btn" data-bs-dismiss="modal"
              aria-label="{{ __('Cancel booking') }}">{{ __('Cancel') }}</a>
            <a href="javaScript:void(0)" class="btn-text color-primary icon-start" onclick="bookingStepper.previous()"
              target="_self">
              <i class="fal fa-long-arrow-left"></i>{{ __('Prev Step') }}</a>
            <a id="time_next_step" href="javaScript:void(0)" class="d-none btn-text color-primary icon-end"
              onclick="bookingStepper.next()" target="_self">{{ __('Next Step') }}<i
                class="fal fa-long-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <div id="questions" class="bs-stepper-pane fade" role="tabpanel" aria-labelledby="questions-trigger">
        <div class="questions-area pt-4">
          <div class="section-title title-center">
            <h3 class="title">{{ __('Answer Service Questions') }}</h3>
            <p class="text-muted">{{ __('Please answer the following questions related to your service') }}</p>
          </div>

          <div id="questions-container" class="questions-list">
            <!-- Questions will be loaded here dynamically -->
            <p class="text-center text-muted">{{ __('Loading questions...') }}</p>
          </div>

          <div id="questions-required-error" class="alert alert-danger mb-3" style="display:none;">
            {{ __('Please answer all required questions (marked with *) before continuing.') }}
          </div>

          <div class="btn-groups justify-content-center w-100">
            <a href="javaScript:void(0)" class="btn-text booking-cancel-btn" data-bs-dismiss="modal"
              aria-label="{{ __('Cancel booking') }}">{{ __('Cancel') }}</a>
            <a id="questions_next_step_button" href="javaScript:void(0)" class="btn-text color-primary icon-end"
              onclick="validateAndProceedFromQuestions()" target="_self">{{ __('Next Step') }}<i
                class="fal fa-long-arrow-right"></i></a>
            <a id="questions_contact_quote_button" href="{{ route('contact') }}"
              class="btn-text color-primary icon-end d-none" target="_self">{{ __('Contact Us For Quote') }}<i
                class="fal fa-long-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <div id="info" class="bs-stepper-pane fade" role="tabpanel" aria-labelledby="info-trigger">
        <!-- Authentication-area start -->
        <div class="authentication-area pt-1">
          <div class="row auth-info {{ !Auth::guard('web')->user() ? 'd-block' : 'd-none' }}">
            <div class="col-lg-12">
              <div class="auth-form">
                <form id="login-form">
                  <div class="title text-center mb-20">
                    <span class="h3 mb-2">{{ __('Login') }}</span>
                  </div>
                  <div class="form-group mb-20">
                    <label for="userName" class="form-label color-dark">{{ __('Username') }}<span
                        class="color-red">*</span></label>
                    <input type="text" name="username" id="username" class="form-control"
                      placeholder="{{ __('Username') }}">
                    <span id="err_username" class="mt-2 mb-0 text-danger em"></span>
                  </div>
                  <div class="form-group booking-form-group">
                    <label for="password" class="form-label color-dark">{{ __('Password') }}<span
                        class="color-red">*</span></label>
                    <div class="position-relative">
                      <input type="password" name="password" id="password" class="form-control"
                        placeholder="{{ __('Enter Password') }}">
                    </div>
                    <span id="err_password" class="mt-2 mb-0 text-danger em"></span>
                  </div>
                  @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                    <div class="form-group mb-30">
                      {!! NoCaptcha::renderJs() !!}
                      {!! NoCaptcha::display() !!}
                      <span id="err_g-recaptcha-response" class="mt-2 mb-0 text-danger em"></span>
                    </div>
                  @endif
                  <button onclick="customerLogin(event)" type="button" class="btn btn-lg btn-primary btn-gradient">
                    {{ __('Login') }}
                  </button>
                  <div class="link mt-20">
                    {{ __("Don't have an account") }}? <a href="{{ route('user.signup') }}">{{ __('Click Here') }}</a>
                    {{ __('to') }}
                    {{ __('Signup') }}
                  </div>
                  <button class="btn btn-lg btn-primary btn-gradient" id="guest_checkout"
                    type="button">{{ __('Proceed as guest checkout') }}
                  </button>
                </form>
              </div>
              <div class="btn-groups d-flex justify-content-center">
                <a href="javaScript:void(0)" class="btn-text booking-cancel-btn" data-bs-dismiss="modal"
                  aria-label="{{ __('Cancel booking') }}">{{ __('Cancel') }}</a>
                <a href="javaScript:void(0)" class="btn-text color-primary icon-start"
                  onclick="bookingStepper.previous()" target="_self"><i
                    class="fal fa-long-arrow-left"></i>{{ __('Prev Step') }}</a>
              </div>
            </div>
          </div>
          <form id="billing-form" class="{{ !Auth::guard('web')->user() ? 'd-none' : 'd-block' }}"
            action="{{ route('frontend.services.billing') }}" method="get">
            <div class="section-title title-center mb-2 mt-2">
              <h3 class="title col-lg-8" style="font-size: 14px; margin-bottom: 5px;">{{ __('Site Details') }}</h3>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group mb-2">
                  <label for="address">{{ __('Site Address') }}*</label>
                  <input id="address" type="text" name="address" class="form-control" placeholder="{{ __('Site Address') }}"
                    value="{{ $authUser ? $authUser->address : '' }}">
                  <span id="err_address" class="mt-1 mb-0 text-danger em" style="font-size: 10px;"></span>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-2">
                  <label for="zip_code">{{ __('Site Postcode/Zip') }}</label>
                  <input id="zip_code" type="text" name="zip_code" class="form-control"
                    placeholder="{{ __('Site Postcode/Zip') }}" value="{{ $authUser ? $authUser->zip_code : '' }}">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-2">
                  <label for="country">{{ __('Site Country') }}</label>
                  <input id="country" type="text" class="form-control" name="country" placeholder="{{ __('Site Country') }}"
                    value="{{ $authUser ? $authUser->country : '' }}">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group booking-form-group">
                  <label for="name">{{ __('Site Name') . '*' }}</label>
                  <input id="name" type="text" class="form-control" name="name"
                    placeholder="{{ __('Enter Site Name') }}" value="{{ $authUser ? $authUser->name : '' }}">
                  <span id="err_name" class="mt-1 mb-0 text-danger em" style="font-size: 10px;"></span>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-2">
                  <label for="phone">{{ __('Site Phone') . '*' }}</label>
                  <input id="phone" type="text" class="form-control" name="phone" placeholder="{{ __('Site Phone') }}"
                    value="{{ $authUser ? $authUser->phone : '' }}">
                  <span id="err_phone" class="mt-1 mb-0 text-danger em" style="font-size: 10px;"></span>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-group mb-2">
                  <label for="email">{{ __('Site Email') }}*</label>
                  <input id="email" type="email" class="form-control" name="email"
                    placeholder="{{ __('Site Email') }}" value="{{ $authUser ? $authUser->email : '' }}">
                  <span id="err_email" class="mt-1 mb-0 text-danger em" style="font-size: 10px;"></span>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="form-check form-switch billing-same-toggle">
                  <input class="form-check-input" type="checkbox" role="switch" id="billing_same_checkbox">
                  <input type="hidden" name="billing_same" id="billing_same" value="1">
                  <label class="form-check-label" for="billing_same_checkbox">
                    {{ __('Billing details are different') }}
                  </label>
                </div>
              </div>
              <div class="col-12">
                <div id="alternate_billing_fields" class="row gx-3 gy-2 mt-1 d-none">
                  <div class="col-lg-6">
                    <div class="form-group mb-2">
                      <label for="billing_name_alt">{{ __('Billing Name') }}*</label>
                      <input id="billing_name_alt" type="text" class="form-control" name="billing_name_alt"
                        data-billing-target="name" placeholder="{{ __('Enter Billing Name') }}">
                      <span id="err_billing_name_alt" class="mt-1 mb-0 text-danger em" style="font-size: 10px;"></span>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group mb-2">
                      <label for="billing_phone_alt">{{ __('Billing Phone') }}*</label>
                      <input id="billing_phone_alt" type="text" class="form-control" name="billing_phone_alt"
                        data-billing-target="phone" placeholder="{{ __('Enter Billing Phone') }}">
                      <span id="err_billing_phone_alt" class="mt-1 mb-0 text-danger em" style="font-size: 10px;"></span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form-group mb-2">
                      <label for="billing_email_alt">{{ __('Billing Email') }}*</label>
                      <input id="billing_email_alt" type="email" class="form-control" name="billing_email_alt"
                        data-billing-target="email" placeholder="{{ __('Enter Billing Email') }}">
                      <span id="err_billing_email_alt" class="mt-1 mb-0 text-danger em" style="font-size: 10px;"></span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form-group mb-2">
                      <label for="billing_address_alt">{{ __('Billing Address') }}*</label>
                      <input id="billing_address_alt" type="text" class="form-control" name="billing_address_alt"
                        data-billing-target="address" placeholder="{{ __('Enter Billing Address') }}">
                      <span id="err_billing_address_alt" class="mt-1 mb-0 text-danger em"
                        style="font-size: 10px;"></span>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group mb-2">
                      <label for="billing_zip_alt">{{ __('Billing Postcode/Zip') }}</label>
                      <input id="billing_zip_alt" type="text" class="form-control" name="billing_zip_alt"
                        data-billing-target="zip" placeholder="{{ __('Billing Postcode/Zip') }}">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group mb-2">
                      <label for="billing_country_alt">{{ __('Billing Country') }}</label>
                      <input id="billing_country_alt" type="text" class="form-control" name="billing_country_alt"
                        data-billing-target="country" placeholder="{{ __('Billing Country') }}">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <input hidden id="service_price_value" value="{{ $service_price ?? 0 }}">
            <input hidden id="base_service_price" value="{{ $service_price ?? 0 }}">
            <input hidden id="currency_symbol_value" value="{{ $basicInfo->base_currency_symbol ?? '£' }}">
            <input hidden id="currency_symbol_position_value" value="{{ $basicInfo->base_currency_symbol_position ?? 'left' }}">
            <input hidden id="booking_timezone_value" value="{{ $basicInfo->timezone ?? config('app.timezone', 'UTC') }}">
            <input hidden name="postal_price" id="postal_price_value" value="0.00">
            <input hidden id="question_price_total_value" value="0.00">
            <input hidden id="question_price_breakdown_value" value="[]">
            <input hidden id="urgency_surcharge_value" value="0">
            <input hidden id="urgency_surcharge_period_value" value="">
            <input hidden id="urgency_calculated_at_value" value="">
            <input hidden id="urgency_booking_starts_at_value" value="">
            <input hidden id="service_name_value" value="{{ $service_name ?? '' }}">
            <input hidden id="questions_data" value="{{ json_encode($questions ?? []) }}">
            <input hidden name="booking_date" id="booking_date">
            <input hidden name="user_id" id="user_id" value="{{ $authUser ? $authUser->id : '0' }}">
            <input hidden name="staff_id" id="staff_id">
            <input hidden name="service_hour_id" id="service_hour_id">
            <input hidden name="max_person" id="max_person">
            <input hidden name="start_time" id="start_time">
            <input hidden name="end_time" id="end_time">
            <div class="d-flex  justify-content-center">
              <div class="btn-groups">
                <a href="javaScript:void(0)" class="btn-text booking-cancel-btn" data-bs-dismiss="modal"
                  aria-label="{{ __('Cancel booking') }}">{{ __('Cancel') }}</a>
                @if (Auth::guard('web')->user())
                  <a href="javaScript:void(0)" class="btn-text color-primary icon-start"
                    onclick="bookingStepper.previous()" target="_self">
                    <i class="fal fa-long-arrow-left"></i>{{ __('Prev Step') }}</a>
                @else
                  <a href="javaScript:void(0)" id="billing_prev" class="btn-text color-primary icon-start login_prev"
                    target="_self">
                    <i class="fal fa-long-arrow-left"></i>{{ __('Prev Step') }}</a>
                @endif
                <a id="next_step_button" href="javaScript:void(0)" onclick="submitFormAndGoToSummary(event)"
                  class="btn-text color-primary icon-end" target="_self">{{ __('Next Step') }}
                  <i class="fal fa-long-arrow-right"></i></a>
              </div>
            </div>
          </form>
        </div>
        <!-- Authentication-area end -->
      </div>

      <div id="payment" class="bs-stepper-pane fade" role="tabpanel" aria-labelledby="payment-trigger">
        <div class="payment-area pt-2">
          <div class="section-title title-center mb-2">
            <h3 class="title col-lg-8" style="font-size: 14px; margin-bottom: 5px;">{{ __('Payment Method') }}</h3>
            <p class="text-muted" style="font-size: 10px; margin-bottom: 5px;">
              {{ __('Select your preferred payment method') }}</p>
          </div>
          <div class="payment-form w-75 w-sm-100 mx-auto">
            <form action="{{ route('frontend.service.payment') }}" method="POST" id="payment-form"
              enctype="multipart/form-data">
              @csrf
              <input hidden type="text" name="stripeToken" id="stripeToken">
              <input hidden type="text" name="name" id="billing_name">
              <input hidden type="text" name="phone" id="billing_phone">
              <input hidden type="text" name="email" id="billing_email">
              <input hidden type="text" name="address" id="billing_address">
              <input hidden type="text" name="zip_code" id="billing_zip_code">
              <input hidden type="text" name="country" id="billing_country">
              <input hidden type="text" name="serviceHourId" id="serviceHourId">
              <input hidden type="text" name="bookingDate" id="bookingDate">
              <input hidden type="text" name="staffId" id="staffId">
              <input hidden type="text" name="user_id" id="userId">
              <input hidden type="text" name="max_person" id="bmax_person">
              <input hidden type="text" name="service_id" id="service_id" value="{{ $service_id ?? '' }}">
              <input hidden type="text" name="cart_mode" id="cart_mode" value="0">
              <input hidden type="text" name="gateway" id="gateway">
              <input hidden type="text" name="billing_same" id="billing_same_input" value="1">
              <input hidden type="text" name="billing_name_secondary" id="billing_name_secondary">
              <input hidden type="text" name="billing_phone_secondary" id="billing_phone_secondary">
              <input hidden type="text" name="billing_email_secondary" id="billing_email_secondary">
              <input hidden type="text" name="billing_address_secondary" id="billing_address_secondary">
              <input hidden type="text" name="billing_zip_secondary" id="billing_zip_secondary">
              <input hidden type="text" name="billing_country_secondary" id="billing_country_secondary">
              <input hidden type="text" name="question_answers" id="question_answers" value="{}">
              <input hidden type="text" name="total_price" id="total_price" value="{{ $service_price ?? 0 }}">

              <!-- Featured: Direct Card Payment (Stripe) - Highlighted at Top -->
              @if($online_gateways->contains('keyword', 'stripe'))
                <div class="featured-payment-option mb-3">
                  <div class="payment-method-card payment-card-featured" data-gateway="stripe"
                    onclick="selectPaymentMethod('stripe')">
                    <div class="payment-icon">
                      <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="payment-name-featured">
                      {{ __('Pay with Debit/Credit Card') }}
                      <small class="d-block text-muted"
                        style="font-size: 8px; margin-top: 2px;">{{ __('Direct & Secure Payment') }}</small>
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
              @endif

              <!-- Other Payment Methods -->
              <div class="payment-methods-grid">
                @foreach ($online_gateways as $getway)
                  @if ($getway->keyword !== 'stripe')
                    <div class="payment-method-card" data-gateway="{{ $getway->keyword }}"
                      onclick="selectPaymentMethod('{{ $getway->keyword }}')">
                      <div class="payment-icon">
                        @if ($getway->keyword == 'paypal')
                          <i class="fab fa-paypal"></i>
                        @elseif ($getway->keyword == 'stripe')
                          <i class="fab fa-cc-stripe"></i>
                        @elseif ($getway->keyword == 'authorize.net')
                          <i class="fas fa-credit-card"></i>
                        @elseif ($getway->keyword == 'razorpay')
                          <i class="fas fa-wallet"></i>
                        @elseif ($getway->keyword == 'mollie')
                          <i class="fas fa-money-check-alt"></i>
                        @elseif ($getway->keyword == 'flutterwave')
                          <i class="fas fa-wave-square"></i>
                        @elseif ($getway->keyword == 'instamojo')
                          <i class="fas fa-shopping-bag"></i>
                        @elseif ($getway->keyword == 'paystack')
                          <i class="fas fa-layer-group"></i>
                        @elseif ($getway->keyword == 'paytm')
                          <i class="fas fa-mobile-alt"></i>
                        @elseif ($getway->keyword == 'mercadopago')
                          <i class="fas fa-shopping-cart"></i>
                        @else
                          <i class="fas fa-credit-card"></i>
                        @endif
                      </div>
                      <div class="payment-name">
                        @if ($getway->keyword == 'paypal')
                          {{ __('PayPal') }}
                        @elseif ($getway->keyword == 'stripe')
                          {{ __('Stripe') }}
                        @elseif ($getway->keyword == 'authorize.net')
                          {{ __('Authorize.net') }}
                        @elseif ($getway->keyword == 'razorpay')
                          {{ __('Razorpay') }}
                        @elseif ($getway->keyword == 'mollie')
                          {{ __('Mollie') }}
                        @elseif ($getway->keyword == 'flutterwave')
                          {{ __('Flutterwave') }}
                        @elseif ($getway->keyword == 'instamojo')
                          {{ __('Instamojo') }}
                        @elseif ($getway->keyword == 'paystack')
                          {{ __('Paystack') }}
                        @elseif ($getway->keyword == 'paytm')
                          {{ __('Paytm') }}
                        @elseif ($getway->keyword == 'mercadopago')
                          {{ __('Mercado Pago') }}
                        @else
                          {{ __(ucfirst($getway->keyword)) }}
                        @endif
                      </div>
                      <div class="payment-check">
                        <i class="fas fa-check-circle"></i>
                      </div>
                    </div>
                  @endif
                @endforeach

                {{-- @if (count($offline_gateways) > 0)
                @foreach ($offline_gateways as $offlineGateway)
                <div class="payment-method-card" data-gateway="{{ $offlineGateway->id }}"
                  onclick="selectPaymentMethod('{{ $offlineGateway->id }}')">
                  <div class="payment-icon">
                    <i class="fas fa-money-bill-wave"></i>
                  </div>
                  <div class="payment-name">{{ __($offlineGateway->name) }}</div>
                  <div class="payment-check">
                    <i class="fas fa-check-circle"></i>
                  </div>
                </div>
                @endforeach
                @endif --}}
              </div>
              <span id="err_gateway" class="mt-2 mb-0 text-danger em d-block text-center"></span>

              <!-- Stripe Payment Will be Inserted here -->
              <div id="stripe-element" class="mb-2 mt-4 d-none"
                style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 2px solid #e0e0e0;">
                <!-- A Stripe Element will be inserted here. -->
              </div>
              <div class="card-payment-info" id="card-payment-info"
                style="display:none; text-align: center; font-size: 10px; color: #6c757d; margin-top: 10px;">
                <i class="fas fa-lock" style="color: #28a745;"></i>
                {{ __('Your payment is encrypted and processed securely. Card details are never stored on our servers.') }}
                <div class="payment-brands-inline">
                  <i class="fab fa-cc-visa"></i>
                  <i class="fab fa-cc-mastercard"></i>
                  <i class="fab fa-cc-amex"></i>
                  <i class="fab fa-cc-discover"></i>
                </div>
              </div>
              <!-- Used to display form errors -->
              <div id="stripe-errors" class="pb-2" role="alert"
                style="color: #dc3545; font-size: 11px; text-align: center;"></div>

              <!-- Authorize.net Payment Will be Inserted here -->
              <div class="row gateway-details pb-4 d-none" id="authorizenet-element">
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <input class="form-control" type="text" id="anetCardNumber" placeholder="Card Number" disabled />
                  </div>
                </div>
                <div class="col-lg-6 mb-3">
                  <div class="form-group">
                    <input class="form-control" type="text" id="anetExpMonth" placeholder="Expire Month" disabled />
                  </div>
                </div>
                <div class="col-lg-6 ">
                  <div class="form-group">
                    <input class="form-control" type="text" id="anetExpYear" placeholder="Expire Year" disabled />
                  </div>
                </div>
                <div class="col-lg-6 ">
                  <div class="form-group">
                    <input class="form-control" type="text" id="anetCardCode" placeholder="Card Code" disabled />
                  </div>
                </div>
                <input type="hidden" name="opaqueDataValue" id="opaqueDataValue" disabled />
                <input type="hidden" name="opaqueDataDescriptor" id="opaqueDataDescriptor" disabled />
                @php
                  $display = 'none';
                @endphp
                <ul id="authorizeNetErrors" style="display: {{ $display }}"></ul>
              </div>
              @foreach ($offline_gateways as $offlineGateway)
                <div
                  class="@if ($errors->has('attachment') && request()->session()->get('gatewayId') == $offlineGateway->id) d-block @else d-none @endif offline-gateway-info"
                  id="{{ 'offline-gateway-' . $offlineGateway->id }}">
                  @if (!is_null($offlineGateway->short_description))
                    <div class="form-group mb-4">
                      <label class="font-weight-bold text-dark">{{ __('Description') }}</label>


                      <p>{{ $offlineGateway->short_description }}</p>
                    </div>
                  @endif

                  @if (!is_null($offlineGateway->instructions))
                    <div class="form-group mb-4">
                      <label class="font-weight-bold text-dark">{{ __('Instructions') }}</label>
                      {!! replaceBaseUrl($offlineGateway->instructions, 'summernote') !!}
                    </div>
                  @endif

                  @if ($offlineGateway->has_attachment == 1)
                    <div class="form-group mb-4">
                      <label>{{ __('Attachment') . '*' }}</label>
                      <br>
                      <input type="file" class="form-control" name="attachment" id="offline-attachment">
                      <span id="err_attachment" class="mt-2 mb-0 text-danger em"></span>
                    </div>
                  @endif

                </div>
                <span id="err_currency" class="mt-2 mb-0 text-danger em"></span>
              @endforeach
            </form>
          </div>
          <div class="btn-groups justify-content-center w-100">
            <a href="javaScript:void(0)" class="btn-text booking-cancel-btn" data-bs-dismiss="modal"
              aria-label="{{ __('Cancel booking') }}">{{ __('Cancel') }}</a>
            <a href="javaScript:void(0)" id="payment_prev" class="btn-text color-primary icon-start"
              onclick="bookingStepper.previous()" target="_self"><i
                class="fal fa-long-arrow-left"></i>{{ __('Prev Step') }}</a>
            <a href="javaScript:void(0)" id="payment_submit_btn" class="btn-text color-primary icon-end"
              target="_self">{{ __('Complete Payment') }}<i class="fal fa-long-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <div id="summary" class="bs-stepper-pane fade" role="tabpanel" aria-labelledby="summary-trigger">
        <div class="summary-area pt-2">
          <div class="section-title title-center mb-2">
            <h3 class="title col-lg-8" style="font-size: 14px; margin-bottom: 3px;">{{ __('Booking Summary') }}</h3>
            <p class="text-muted" style="font-size: 10px; margin-bottom: 5px;">{{ __('Review details before payment') }}
            </p>
          </div>

          <div class="booking-summary-container">
            <!-- Service Details Card -->
            <div class="summary-card">
              <h5 class="summary-card-header">{{ __('Service Details') }}</h5>
              <div class="summary-row">
                <span class="summary-label">{{ __('Service Name') }}:</span>
                <span class="summary-value" id="summary-service-name">-</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">{{ __('Service Price') }}:</span>
                <span class="summary-value" id="summary-service-price">-</span>
              </div>
              <!-- Dynamic question add-ons breakdown -->
              <div id="summary-question-breakdown"></div>
              <!-- Postal/Delivery - shown only when applicable -->
              <div class="summary-row" id="summary-postal-row" style="display:none">
                <span class="summary-label">{{ __('Postal/Delivery') }}:</span>
                <span class="summary-value text-success" id="summary-postal-price">-</span>
              </div>
              <!-- Urgent booking surcharge - injected dynamically when applicable -->
            </div>

            <!-- Booking Information Card -->
            <div class="summary-card">
              <h5 class="summary-card-header">{{ __('Booking Information') }}</h5>
              <div class="summary-row">
                <span class="summary-label">{{ __('Date & Time') }}:</span>
                <span class="summary-value" id="summary-booking-datetime">-</span>
              </div>
            </div>

            <!-- Site Information Card -->
            <div class="summary-card">
              <h5 class="summary-card-header">{{ __('Site Information') }}</h5>
              <div class="summary-row">
                <span class="summary-label">{{ __('Name') }}:</span>
                <span class="summary-value" id="summary-customer-name">-</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">{{ __('Email') }}:</span>
                <span class="summary-value" id="summary-customer-email">-</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">{{ __('Phone') }}:</span>
                <span class="summary-value" id="summary-customer-phone">-</span>
              </div>
              <div class="summary-row">
                <span class="summary-label">{{ __('Address') }}:</span>
                <span class="summary-value" id="summary-customer-address">-</span>
              </div>
            </div>

            <!-- Billing Information Card -->
            <div class="summary-card" id="summary-billing-card">
              <h5 class="summary-card-header">{{ __('Billing Information') }}</h5>
              <div class="summary-row" id="summary-billing-same-row">
                <span class="summary-label">{{ __('Status') }}:</span>
                <span class="summary-value" id="summary-billing-status"
                  data-text-same="{{ __('Same as site details') }}"
                  data-text-different="{{ __('Different billing address') }}">{{ __('Same as site details') }}</span>
              </div>
              <div class="billing-details-wrapper d-none">
                <div class="summary-row">
                  <span class="summary-label">{{ __('Name') }}:</span>
                  <span class="summary-value" id="summary-billing-name">-</span>
                </div>
                <div class="summary-row">
                  <span class="summary-label">{{ __('Email') }}:</span>
                  <span class="summary-value" id="summary-billing-email">-</span>
                </div>
                <div class="summary-row">
                  <span class="summary-label">{{ __('Phone') }}:</span>
                  <span class="summary-value" id="summary-billing-phone">-</span>
                </div>
                <div class="summary-row">
                  <span class="summary-label">{{ __('Address') }}:</span>
                  <span class="summary-value" id="summary-billing-address">-</span>
                </div>
              </div>
            </div>

            <!-- Payment Information Card -->
            <div class="summary-card">
              <h5 class="summary-card-header">{{ __('Payment Information') }}</h5>
              <div class="summary-row">
                <span class="summary-label">{{ __('Payment Method') }}:</span>
                <span class="summary-value" id="summary-payment-method">-</span>
              </div>
              <div class="summary-row summary-total">
                <span class="summary-label">{{ __('Total Amount') }}:</span>
                <span class="summary-value" id="summary-total-amount">-</span>
              </div>
            </div>

            <!-- Cart Overview Card -->
            {{-- <div class="summary-card">
              {{-- <div class="d-flex justify-content-between align-items-center summary-card-header">
                <h5 class="mb-0">{{ __('Your Cart') }}</h5>
                <span class="badge bg-primary" id="summary-cart-count">{{ collect($service_cart_items ?? [])->count()
                  }}</span>
              </div> --}}
              {{-- <div class="d-flex justify-content-end mt-2">
                <a href="javaScript:void(0)" id="summary_add_to_cart"
                  class="btn btn-sm btn-primary btn-gradient rounded-pill">
                  <i class="fal fa-shopping-cart me-1"></i>{{ __('Add to Cart') }}
                </a>
              </div> --}}
              {{-- <div id="service_cart_container" class="cart-mini-wrapper">
                @include('frontend.services.booking-modal.partials.cart-summary', [
                'serviceCart' => collect($service_cart_items ?? []),
                'cartCount' => collect($service_cart_items ?? [])->count(),
                'currencySymbol' => $currencyInfo->base_currency_symbol ?? '£',
                'currencyPosition' => $currencyInfo->base_currency_symbol_position ?? 'left',
                'cartTotal' => $service_cart_total ?? collect($service_cart_items ?? [])->sum('price'),
                ])
              </div> --}}
              {{-- <div class="d-flex justify-content-center mt-3">
                <a href="javaScript:void(0)"
                  onclick="$('#makeBooking').modal('hide'); window.location.href='{{ route('frontend.services') }}';"
                  class="text-decoration-none" style="color: var(--color-primary);">
                  <i class="fal fa-plus me-1"></i>{{ __('Select Other Services') }}
                </a>
              </div>
            </div> --}}
          </div>

          <div class="btn-groups justify-content-center w-100">
            <a href="javaScript:void(0)" class="btn-text booking-cancel-btn" data-bs-dismiss="modal"
              aria-label="{{ __('Cancel booking') }}">{{ __('Cancel') }}</a>
            <a href="javaScript:void(0)" class="btn-text color-primary icon-start" onclick="bookingStepper.previous()"
              target="_self">
              <i class="fal fa-long-arrow-left"></i>{{ __('Prev Step') }}</a>
            <a href="javaScript:void(0)" id="summary_proceed_payment" class="btn-text color-primary icon-end"
              onclick="bookingStepper.next()" target="_self">{{ __('Choose Payment Method') }}<i
                class="fal fa-long-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <div id="confirm" class="bs-stepper-pane fade {{ $isConfirmationOnly ? 'active show' : '' }}" role="tabpanel" aria-labelledby="confirm-trigger">
        <div class="confirm-area pt-4">
          <div class="booking-confirmation">
            @php
              $isWeekendRequestConfirmation = isset($bookingInfo) && $bookingInfo->payment_method === 'Weekend Request';
            @endphp
            <div class="confirmation-badge">
              <i class="fal fa-check"></i>
            </div>
            <h3 class="confirmation-title">
              {{ $isWeekendRequestConfirmation ? __('Weekend Request Received') : __('Booking Confirmed') }}
            </h3>
            @if (isset($bookingInfo) && $bookingInfo->serviceContent->isNotEmpty())
              <p class="confirmation-service">
                @foreach ($bookingInfo->serviceContent as $content)
                  <a href="{{ route('frontend.service.details', ['slug' => $content->slug, 'id' => $bookingInfo->service->id]) }}"
                    class="btn-text color-primary" target="_blank">
                    {{ truncateString($content->name, 70) }}
                  </a>
                @endforeach
              </p>
            @endif
            <p class="confirmation-copy">
              @if ($isWeekendRequestConfirmation)
                {{ __('No payment has been taken. Your request is pending until our team confirms weekend availability.') }}
              @elseif (isset($bookingInfo) && $bookingInfo->gateway_type == 'offline')
                {{ __('Your booking has been received. Please wait for the payment confirmation mail.') }}
              @else
                {{ __('Your booking has been received successfully. We will send the details to your email.') }}
              @endif
            </p>

            @if (isset($bookingInfo))
              <div class="confirmation-number">
                <i class="fal fa-receipt"></i>
                <span>{{ __('Booking No.') . ' ' . '#' . $bookingInfo->order_number }}</span>
              </div>

              <div class="confirmation-receipt">
                <div class="confirmation-box">
                  <h5 class="confirmation-box-title">{{ __('Appointment Details') }}</h5>
                  <div class="confirmation-row">
                    <span class="confirmation-label">{{ __('Booking Date') }}</span>
                    <span class="confirmation-value">{{ date_format($bookingInfo->created_at, 'M d, Y') }}</span>
                  </div>
                  <div class="confirmation-row">
                    <span class="confirmation-label">{{ __('Appointment Date') }}</span>
                    <span class="confirmation-value">{{ \Carbon\Carbon::parse($bookingInfo->booking_date)->format('M d, Y') }}</span>
                  </div>
                  <div class="confirmation-row">
                    <span class="confirmation-label">{{ __('Time') }}</span>
                    <span class="confirmation-value">{{ $bookingInfo->start_date }} - {{ $bookingInfo->end_date }}</span>
                  </div>
                  <div class="confirmation-row">
                    <span class="confirmation-label">{{ __('Vendor') }}</span>
                    <span class="confirmation-value">
                      @if ($bookingInfo->vendor_id != 0)
                        <a href="{{ route('frontend.vendor.details', $bookingInfo->vendor->username) }}">{{ $bookingInfo->vendor->username }}</a>
                      @else
                        <a href="{{ route('frontend.vendor.details', $admin->username) }}">{{ $admin->username }}</a>
                      @endif
                    </span>
                  </div>
                </div>
                <div class="confirmation-box">
                  <h5 class="confirmation-box-title">{{ __('Payment Details') }}</h5>
                  <div class="confirmation-row">
                    <span class="confirmation-label">{{ $isWeekendRequestConfirmation ? __('Request Amount') : __('Paid Amount') }}</span>
                    <span class="confirmation-value">
                      {{ $bookingInfo->currency_text_position == 'left' ? $bookingInfo->currency_text . ' ' : '' }}{{ number_format($bookingInfo->customer_paid, 2, '.', ',') }}{{ $bookingInfo->currency_text_position == 'right' ? ' ' . $bookingInfo->currency_text : '' }}
                    </span>
                  </div>
                  <div class="confirmation-row">
                    <span class="confirmation-label">{{ $isWeekendRequestConfirmation ? __('Payment') : __('Paid Via') }}</span>
                    <span class="confirmation-value">{{ $isWeekendRequestConfirmation ? __('Pay after approval') : $bookingInfo->payment_method }}</span>
                  </div>
                  <div class="confirmation-row">
                    <span class="confirmation-label">{{ __('Status') }}</span>
                    <span class="confirmation-value">
                      @if ($bookingInfo->payment_status == 'completed')
                        <span class="badge bg-success">{{ __('completed') }}</span>
                      @elseif ($bookingInfo->payment_status == 'pending')
                        <span class="badge bg-warning">{{ __('pending') }}</span>
                      @else
                        <span class="badge bg-danger">{{ __('rejected') }}</span>
                      @endif
                    </span>
                  </div>
                </div>
              </div>
            @endif
          </div>

          @if (isset($bookingInfo) && $bookingInfo->payment_status == 'completed' && !$isWeekendRequestConfirmation)
            {{-- Meta Pixel Purchase event — only for a genuinely completed,
                 paid booking (never for pending/offline/weekend-request
                 bookings), using the real amount charged. Guarded against
                 firing twice for the same order (e.g. on a page refresh
                 while this success screen is still open). --}}
            <script>
              (function () {
                if (typeof fbq !== 'function') return;
                var orderNumber = @json($bookingInfo->order_number);
                var storageKey = 'fb_purchase_tracked_' + orderNumber;
                try {
                  if (sessionStorage.getItem(storageKey)) return;
                  sessionStorage.setItem(storageKey, '1');
                } catch (e) {}
                fbq('track', 'Purchase', {
                  value: {{ (float) $purchaseTotal }},
                  currency: @json($purchaseCurrency),
                });
              })();
            </script>
          @endif

          <div class="btn-groups justify-content-center w-100">
            <button type="button" class="btn-text color-primary icon-end" data-bs-dismiss="modal"
              aria-label="Close">{{ __('Close') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Payment Method Selection Function
  function selectPaymentMethod(gateway) {
    // Remove selected class from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
      card.classList.remove('selected');
    });

    // Add selected class to clicked card
    const selectedCard = document.querySelector(`.payment-method-card[data-gateway="${gateway}"]`);
    if (selectedCard) {
      selectedCard.classList.add('selected');
    }

    // Set the gateway value in hidden input
    const gatewayInput = document.getElementById('gateway');
    gatewayInput.value = gateway;

    // Clear any previous error
    document.getElementById('err_gateway').textContent = '';

    // Trigger change event for legacy handlers
    const event = new Event('change', { bubbles: true });
    gatewayInput.dispatchEvent(event);

    // Toggle gateway-specific UI in main script
    if (typeof handleGatewayChange === 'function') {
      handleGatewayChange(gateway);
    }

  }

  // New function: Submit billing form and go to summary
  function submitFormAndGoToSummary(event) {
    event.preventDefault();
    let url = $("#billing-form").attr('action');
    let method = $("#billing-form").attr('method');

    if (typeof applyUrgencySurcharge === 'function') {
      applyUrgencySurcharge($('#booking_date').val(), $('#start_time').val());
    } else if (typeof updateQuestionAnswers === 'function') {
      updateQuestionAnswers();
    }

    $('.request-loader-time').addClass('show');

    const billingData = $('#billing-form').serialize();
    const extraData = `&question_answers=${encodeURIComponent($('#question_answers').val())}&total_price=${encodeURIComponent($('#total_price').val())}&urgency_surcharge_amount=${encodeURIComponent($('#urgency_surcharge_value').val() || '0')}&urgency_surcharge_period=${encodeURIComponent($('#urgency_surcharge_period_value').val() || '')}&urgency_calculated_at=${encodeURIComponent($('#urgency_calculated_at_value').val() || '')}&urgency_booking_starts_at=${encodeURIComponent($('#urgency_booking_starts_at_value').val() || '')}`;

    $.ajax({
      url: url,
      method: method,
      data: billingData + extraData,
      success: function (response) {
        $(".request-loader-time").removeClass("show");
        if (response) {
          if (typeof applyBillingResponse === 'function') {
            applyBillingResponse(response);
          } else {
            const fields = ['name', 'phone', 'email', 'address', 'zip_code', 'country'];
            fields.forEach(field => $(`#billing_${field}`).val((response.billing || response)[field]));
          }

          $('#serviceHourId').val(response.service_hour_id);
          $('#bookingDate').val(response.booking_date);
          $('#staffId').val(response.staff_id || 11);
          $('#userId').val(response.user_id || '0');
          $('#bmax_person').val(response.max_person);
          $('#start_time').val(response.start_time || '');
          $('#end_time').val(response.end_time || '');

          // Populate summary before navigating
          populateSummary();

          // Go to summary (step 3)
          bookingStepper.next();
        }
      },
      error: function (error) {
        $('.em').each(function () {
          $(this).html('');
        });

        const response = error.responseJSON || {};

        if (response.server_total !== undefined) {
          $('#total_price').val(parseFloat(response.server_total).toFixed(2));
          $('#service_price_value').val(parseFloat(response.server_total).toFixed(2));
        }

        if (response.errors) {
          for (let x in response.errors) {
            const errorEl = document.getElementById('err_' + x);
            if (errorEl) {
              errorEl.innerHTML = response.errors[x][0];
            } else if (window.toastr) {
              toastr.error(response.errors[x][0]);
            }
          }
        } else if (response.message && window.toastr) {
          toastr.error(response.message);
        } else if (window.toastr) {
          toastr.error('{{ __('Unable to validate booking details. Please review and try again.') }}');
        }

        $(".request-loader-time").removeClass("show");
      }
    });
  }

  $('#summary_add_to_cart').on('click', function (event) {
    event.preventDefault();

    const addToCartUrl = "{{ route('frontend.services.cart.add') }}";
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const payload = {
      service_id: $('#service_id').val(),
      booking_date: $('#booking_date').val(),
      service_hour_id: $('#service_hour_id').val(),
      start_time: $('#start_time').val(),
      end_time: $('#end_time').val(),
      name: $('#name').val(),
      phone: $('#phone').val(),
      email: $('#email').val(),
      address: $('#address').val(),
      zip_code: $('#zip_code').val(),
      country: $('#country').val(),
      site: {
        name: $('#name').val(),
        phone: $('#phone').val(),
        email: $('#email').val(),
        address: $('#address').val(),
        zip_code: $('#zip_code').val(),
        country: $('#country').val(),
      },
      price: $('#service_price_value').val(),
      max_person: $('#max_person').val(),
      staff_id: $('#staff_id').val(),
      billing_same: $('#billing_same').val(),
      user_id: $('#user_id').val(),
      question_answers: $('#question_answers').val(),
      billing: {
        name: $('#billing_name_secondary').val(),
        phone: $('#billing_phone_secondary').val(),
        email: $('#billing_email_secondary').val(),
        address: $('#billing_address_secondary').val(),
        zip_code: $('#billing_zip_secondary').val(),
        country: $('#billing_country_secondary').val(),
      }
    };

    if ($('#billing_same').val() === '0') {
      payload.alternate = {
        name: $('#billing_name_alt').val(),
        phone: $('#billing_phone_alt').val(),
        email: $('#billing_email_alt').val(),
        address: $('#billing_address_alt').val(),
        zip_code: $('#billing_zip_alt').val(),
        country: $('#billing_country_alt').val(),
      };
    }

    $.ajax({
      url: addToCartUrl,
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      data: payload,
      success: function (response) {
        if (window.toastr) {
          toastr.success(response.message || '{{ __('Service added to cart') }}');
        }
        $('#cart_mode').val(1);
        loadServiceCartSummary();
      },
      error: function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          Object.keys(xhr.responseJSON.errors).forEach(function (key) {
            const field = $('#err_' + key);
            if (field.length) {
              field.text(xhr.responseJSON.errors[key][0]);
            }
          });
        } else if (window.toastr) {
          toastr.error('{{ __('Unable to add service to cart. Please try again.') }}');
        }
      }
    });
  });

  function loadServiceCartSummary() {
    $.ajax({
      url: "{{ route('frontend.services.cart.summary') }}",
      method: 'GET',
      success: function (html) {
        $('#service_cart_container').html(html);
        const countBadge = $('#summary-cart-count');
        const itemsCount = $('#service_cart_container').find('.cart-items-list > div').length;
        countBadge.text(itemsCount.toString());
      }
    });
  }

  $(document).ready(function () {
    loadServiceCartSummary();

    $(document).on('change blur', '#zip_code', function () {
      const zipCode = $(this).val();
      fetchPostalCodePrice(zipCode);
    });
  });

  // Load questions based on service_id
  function loadQuestions(serviceId) {
    const questionsDataStr = $('#questions_data').val();
    let questions = [];

    try {
      if (questionsDataStr && questionsDataStr !== '' && questionsDataStr !== '[]') {
        questions = JSON.parse(questionsDataStr);
      }
    } catch (e) {
      console.error('❌ Error parsing questions data:', e);
    }

    if (questions && Array.isArray(questions) && questions.length > 0) {
      renderQuestions(questions);
    } else {
      fetchQuestionsViaAjax(serviceId);
    }
  }

  // Fetch questions via AJAX (fallback method)
  function fetchQuestionsViaAjax(serviceId) {
    var url;
    if (typeof baseURL !== 'undefined') {
      url = baseURL + '/services/questions/' + serviceId;
    } else if (typeof window.location !== 'undefined') {
      url = window.location.origin + '/services/questions/' + serviceId;
    } else {
      url = '/services/questions/' + serviceId;
    }

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'json',
      success: function (response) {
        let questions = [];
        if (response.questions && Array.isArray(response.questions)) {
          questions = response.questions;
        } else if (Array.isArray(response)) {
          questions = response;
        }

        if (questions.length > 0) {
          renderQuestions(questions);
        } else {
          $('#questions-container').html('<p class="text-center text-muted">{{ __("No questions available for this service") }}</p>');
        }
      },
      error: function (xhr, status, error) {
        console.error('❌ AJAX error fetching questions:', status, error);
        $('#questions-container').html('<p class="text-center text-danger">{{ __("Error loading questions. Please refresh and try again.") }}</p>');
      }
    });
  }

  // Render questions in the UI
  function renderQuestions(questions) {
    let html = '';

    if (!questions || !Array.isArray(questions) || questions.length === 0) {
      $('#questions-container').html('<p class="text-center text-muted">{{ __("No questions available for this service") }}</p>');
      return;
    }

    questions.forEach((question, index) => {
      if (!question || !question.id || !question.title) {
        return;
      }

      // Check if question has conditions
      const hasConditions = question.conditions && question.conditions.length > 0;
      const isVisible = !hasConditions; // Show by default if no conditions, hide if conditions exist

      const quantityQuestion = question.type === 'quantity';

      const isRequired = question.is_required ? true : false;
      const requiredBadge = isRequired ? ' <span class="text-danger" title="Required">*</span>' : '';
      const imagePath = question.image ? `{{ asset('assets/img/questions/') }}/${question.image}` : null;
      const imageThumbnail = imagePath
        ? `<img src="${imagePath}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;flex-shrink:0;">`
        : '';

      html += `
      <div class="card mb-3 question-card ${quantityQuestion ? 'question-card-quantity' : ''}" style="border-left: ${quantityQuestion ? '0' : '4px solid var(--color-primary)'}; ${!isVisible ? 'display: none;' : ''}"
           data-question-id="${question.id}"
           data-summary-label="${escapeHtml(question.summary_label || '')}"
           data-required="${isRequired ? 'true' : 'false'}"
           data-has-conditions="${hasConditions ? 'true' : 'false'}"
            ${hasConditions ? `data-conditions='${JSON.stringify(question.conditions)}'` : ''}>
        <div class="card-body">
          ${quantityQuestion
            ? (imageThumbnail ? `<div style="margin-bottom:10px;">${imageThumbnail}</div>` : '')
            : `<div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
                ${imageThumbnail}
                <h6 class="card-title mb-0" style="flex:1;">${escapeHtml(question.title)}${requiredBadge} ${question.service_id ? '<span class="badge badge-info">*</span>' : ''}</h6>
               </div>`}
     `;

      if (question.type === 'text') {
        html += `
        <input type="text"
               name="question_${question.id}"
               class="form-control question-input"
               data-question-id="${question.id}"
               placeholder="{{ __('Enter your answer') }}">
      `;
      } else if (question.type === 'number') {
        html += `
        <input type="number"
               name="question_${question.id}"
               class="form-control question-input"
               data-question-id="${question.id}"
               placeholder="{{ __('Enter a number') }}"
               step="0.01">
      `;
      } else if (question.type === 'quantity') {
        const quantityMin = question.quantity_min !== null && question.quantity_min !== undefined && question.quantity_min !== '' ? parseInt(question.quantity_min, 10) : null;
        const quantityMax = question.quantity_max !== null && question.quantity_max !== undefined && question.quantity_max !== '' ? parseInt(question.quantity_max, 10) : null;
        const defaultQuantity = normalizeQuestionQuantityDefault(question);
        const quantityMeta = buildQuantityMeta(question);

        html += `
          <div class="question-quantity-selector"
               data-question-id="${question.id}"
               data-unit-price="${question.quantity_unit_price ?? ''}"
               data-min="${quantityMin !== null && !isNaN(quantityMin) ? quantityMin : ''}"
               data-max="${quantityMax !== null && !isNaN(quantityMax) ? quantityMax : ''}"
               data-default="${defaultQuantity}">
            <div class="question-quantity-row">
              <div class="question-quantity-controls">
                <button type="button" class="question-quantity-btn question-quantity-down" data-direction="-1" aria-label="{{ __('Decrease quantity') }}">−</button>
                <input type="number"
                       name="question_${question.id}"
                       class="question-input question-quantity-input"
                       data-question-id="${question.id}"
                       min="${quantityMin !== null && !isNaN(quantityMin) ? quantityMin : 0}"
                       ${quantityMax !== null && !isNaN(quantityMax) ? `max="${quantityMax}"` : ''}
                       step="1"
                       inputmode="numeric"
                       placeholder="${defaultQuantity}"
                       value="${defaultQuantity}">
                <button type="button" class="question-quantity-btn question-quantity-up" data-direction="1" aria-label="{{ __('Increase quantity') }}">+</button>
              </div>
              <div class="question-quantity-copy">
                <p class="question-quantity-title">${escapeHtml(question.title)}${requiredBadge}</p>
                <small class="question-quantity-meta">${escapeHtml(quantityMeta)}</small>
              </div>
            </div>
          </div>
        `;
      } else if (question.type === 'select' || question.type === 'multi') {
        const inputType = question.type === 'multi' ? 'checkbox' : 'radio';
        html += `<div class="booking-question-options" role="${question.type === 'multi' ? 'group' : 'radiogroup'}">`;

        if (question.options && Array.isArray(question.options) && question.options.length > 0) {
          question.options.forEach((option) => {
            const optionPrice = option.price != null && option.price !== '' ? option.price : '';
            const optionImage = option.option_image
              ? `<img class="booking-question-option-image" src="{{ asset('assets/img/questions') }}/${encodeURIComponent(option.option_image)}" alt="">`
              : '';
            const optionLabel = `${escapeHtml(option.option_text)}${option.price || option.price === 0 ? ` - £${parseFloat(option.price).toFixed(2)}` : ''}`;
            html += `
            <label class="booking-question-option" for="question_${question.id}_option_${option.id}">
              <input type="${inputType}"
                     id="question_${question.id}_option_${option.id}"
                     name="question_${question.id}${question.type === 'multi' ? '[]' : ''}"
                     class="question-input booking-question-choice"
                     value="${option.id}"
                     data-question-id="${question.id}"
                     data-option-id="${option.id}"
                     data-price="${optionPrice}"
                     data-option-text="${escapeHtml(option.option_text)}">
              ${optionImage}
              <span class="booking-question-option-text">${optionLabel}</span>
            </label>
          `;
          });
        }

        html += `</div>`;
      }

      html += `
        </div>
      </div>
    `;
    });

    const $container = $('#questions-container');

    if ($container.length === 0) {
      console.error('❌ #questions-container not found in DOM!');
      return;
    }

    $container.html(html);

    $('.question-quantity-selector').each(function () {
      updateQuantitySelectorDisplay($(this));
    });

    setTimeout(function () {
      $('.question-quantity-selector').each(function () {
        updateQuantitySelectorDisplay($(this));
      });
      updateQuestionAnswers();
      updateQuestionChoiceVisuals();
    }, 0);

    updateQuestionAnswers();
    updateQuestionChoiceVisuals();

    // Attach change event listeners
    $(document).off('change.questionInputs input.questionInputs blur.questionInputs').on('change.questionInputs input.questionInputs blur.questionInputs', '.question-input', function () {
      if ($(this).hasClass('question-quantity-input')) {
        updateQuantitySelectorDisplay($(this).closest('.question-quantity-selector'));
      }
      updateQuestionChoiceVisuals();
      updateQuestionAnswers();
      $('#questions-required-error').hide();
    });
    $(document).off('click.questionQuantity').on('click.questionQuantity', '.question-quantity-btn', function () {
      const $selector = $(this).closest('.question-quantity-selector');
      const direction = parseInt($(this).data('direction'), 10) || 0;
      changeQuantitySelection($selector, direction);
    });
  }

  // Helper function to escape HTML special characters
  function escapeHtml(text) {
    if (!text) return '';
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
  }

  function getQuestionQuantityUnitPrice($selector) {
    const price = parseFloat($selector.data('unit-price'));
    return isNaN(price) ? null : price;
  }

  function getQuestionQuantityMin($selector) {
    const min = parseInt($selector.data('min'), 10);
    return isNaN(min) ? null : min;
  }

  function getQuestionQuantityMax($selector) {
    const max = parseInt($selector.data('max'), 10);
    return isNaN(max) ? null : max;
  }

  function getQuestionQuantityDefault($selector) {
    const fallback = parseInt($selector.data('default'), 10);
    if (!isNaN(fallback)) {
      return fallback;
    }

    const min = getQuestionQuantityMin($selector);
    return min !== null ? min : 1;
  }

  function normalizeQuestionQuantityDefault(question) {
    const min = question.quantity_min !== null && question.quantity_min !== undefined && question.quantity_min !== '' ? parseInt(question.quantity_min, 10) : null;
    const max = question.quantity_max !== null && question.quantity_max !== undefined && question.quantity_max !== '' ? parseInt(question.quantity_max, 10) : null;
    const configuredDefault = question.quantity_default !== null && question.quantity_default !== undefined && question.quantity_default !== '' ? parseInt(question.quantity_default, 10) : null;

    let defaultQuantity = configuredDefault !== null && !isNaN(configuredDefault)
      ? configuredDefault
      : (min !== null && !isNaN(min) ? min : 1);

    if (min !== null && !isNaN(min)) {
      defaultQuantity = Math.max(defaultQuantity, min);
    }

    if (max !== null && !isNaN(max)) {
      defaultQuantity = Math.min(defaultQuantity, max);
    }

    return defaultQuantity;
  }

  function clampQuantityValue(value, $selector) {
    let numericValue = parseInt(value, 10);

    if (isNaN(numericValue)) {
      numericValue = getQuestionQuantityDefault($selector);
    }

    const min = getQuestionQuantityMin($selector);
    const max = getQuestionQuantityMax($selector);

    if (min !== null) {
      numericValue = Math.max(min, numericValue);
    }

    if (max !== null) {
      numericValue = Math.min(max, numericValue);
    }

    if (numericValue < 0) {
      numericValue = 0;
    }

    return numericValue;
  }

  function buildQuantityMeta(question) {
    const unitPrice = parseFloat(question.quantity_unit_price);
    const min = question.quantity_min !== null && question.quantity_min !== undefined && question.quantity_min !== '' ? parseInt(question.quantity_min, 10) : null;
    const max = question.quantity_max !== null && question.quantity_max !== undefined && question.quantity_max !== '' ? parseInt(question.quantity_max, 10) : null;
    const defaultValue = question.quantity_default !== null && question.quantity_default !== undefined && question.quantity_default !== '' ? parseInt(question.quantity_default, 10) : null;
    const parts = [];

    if (!isNaN(unitPrice)) {
      parts.push(`{{ __('Price') }}: £${unitPrice.toFixed(2)} {{ __('each') }}`);
    }

    if (min !== null && !isNaN(min)) {
      parts.push(`{{ __('Min') }} ${min}`);
    }

    if (max !== null && !isNaN(max)) {
      parts.push(`{{ __('Max') }} ${max}`);
    }

    if (defaultValue !== null && !isNaN(defaultValue)) {
      parts.push(`{{ __('Default') }} ${defaultValue}`);
    }

    return parts.length ? parts.join(' • ') : '{{ __("Enter quantity") }}';
  }

  function updateQuantitySelectorDisplay($selector) {
    const $input = $selector.find('.question-quantity-input');
    const quantity = clampQuantityValue($input.val(), $selector);
    const min = getQuestionQuantityMin($selector);
    const max = getQuestionQuantityMax($selector);

    $input.val(quantity);
    $input.attr('value', quantity);
    if ($input[0]) {
      $input[0].defaultValue = quantity;
    }
    $selector.find('.question-quantity-down').prop('disabled', min !== null && quantity <= min);
    $selector.find('.question-quantity-up').prop('disabled', max !== null && quantity >= max);
  }

  function changeQuantitySelection($selector, direction) {
    const $input = $selector.find('.question-input');
    const currentValue = parseInt($input.val(), 10);
    const baseValue = isNaN(currentValue) ? getQuestionQuantityDefault($selector) : currentValue;
    $input.val(baseValue + direction);
    updateQuantitySelectorDisplay($selector);
    updateQuestionAnswers();
  }

  function updateQuestionChoiceVisuals() {
    $('.booking-question-option').each(function () {
      $(this).toggleClass('is-selected', $(this).find('.booking-question-choice').is(':checked'));
    });
  }

  function getQuestionTitle(questionId) {
    const $card = $('.question-card[data-question-id="' + questionId + '"]');
    const summaryLabel = $card.data('summary-label');
    if (summaryLabel) return summaryLabel;
    const titleFromCard = $card.find('.card-title').clone().children().remove().end().text().trim();
    if (titleFromCard) return titleFromCard;
    return $card.find('.question-quantity-title').text().trim() || 'Add-on';
  }

  function validateAndProceedFromQuestions() {
    var unanswered = [];

    $('#questions-container .question-card[data-required="true"]').each(function () {
      var $card = $(this);

      // Skip hidden (conditional) questions
      if ($card.css('display') === 'none') return;

      var questionId = $card.data('question-id');
      var label = ($card.data('summary-label') || '').trim()
        || $card.find('.card-title').text().replace('*', '').trim()
        || $card.find('.question-quantity-title').text().trim()
        || 'Question';

      var answered = false;

      // Radio / checkbox
      var $choices = $card.find('.booking-question-choice');
      if ($choices.length > 0) {
        answered = $choices.is(':checked');
      } else {
        // text / number / quantity
        var $input = $card.find('.question-input').first();
        var val = ($input.val() || '').toString().trim();
        answered = val !== '' && val !== '0' || $input.hasClass('question-quantity-input');
        // For quantity, any value (including 0) counts as answered
        if ($input.hasClass('question-quantity-input')) {
          answered = val !== '';
        }
      }

      if (!answered) {
        unanswered.push(label);
      }
    });

    if (unanswered.length > 0) {
      var msg = '{{ __("Please answer the following required questions:") }}\n• ' + unanswered.join('\n• ');
      // Try to use an existing alert element if present, otherwise use the site notifier.
      var $err = $('#questions-required-error');
      if ($err.length) {
        $err.text('{{ __("Please answer all required questions (marked with *) before continuing.") }}').show();
        $err[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
      } else if (window.AppNotify) {
        AppNotify.warning(msg);
      } else {
        alert(msg);
      }
      return;
    }

    // Hide any error message and proceed
    $('#questions-required-error').hide();
    bookingStepper.next();
  }

  // Update answers object and calculate total price
  function updateQuestionAnswers() {
    let answers = {};
    let questionPriceTotal = 0;
    let priceBreakdown = [];
    let quoteRequired = false;
    let seenQuestions = {};

    updateQuestionChoiceVisuals();

    $('.question-input').each(function () {
      const questionId = $(this).data('question-id');
      const value = $(this).val();

      if (value) {
        if ($(this).hasClass('booking-question-choice')) {
          if (!$(this).is(':checked')) {
            return;
          }

          const price = parseFloat($(this).data('price'));

          const optionText = $(this).data('option-text') || value;
          if ($(this).attr('type') === 'checkbox') {
            if (!Array.isArray(answers[questionId])) {
              answers[questionId] = [];
            }
            answers[questionId].push(optionText);
          } else {
            answers[questionId] = optionText;
          }

          if (!isNaN(price)) {
            if (price === 0 && value) {
              quoteRequired = true;
            }
            if (price > 0) {
              const label = getQuestionTitle(questionId);
              const optionText = $(this).data('option-text') || '';
              if (seenQuestions[questionId]) {
                seenQuestions[questionId].price += price;
                if (optionText) seenQuestions[questionId].answer += ', ' + optionText;
              } else {
                seenQuestions[questionId] = { label: label, answer: optionText, price: price };
                priceBreakdown.push(seenQuestions[questionId]);
              }
            }
            questionPriceTotal += price;
          }
        } else if ($(this).hasClass('question-quantity-input')) {
          const $quantitySelector = $(this).closest('.question-quantity-selector');
          const quantity = clampQuantityValue(value, $quantitySelector);
          const unitPrice = getQuestionQuantityUnitPrice($quantitySelector);
          const defaultQuantity = getQuestionQuantityDefault($quantitySelector);

          $(this).val(quantity);
          answers[questionId] = quantity;
          updateQuantitySelectorDisplay($quantitySelector);

          if (unitPrice !== null) {
            if (unitPrice === 0 && quantity > defaultQuantity) {
              quoteRequired = true;
            }
            if (quantity > defaultQuantity) {
              const addedPrice = quantity * unitPrice;
              questionPriceTotal += addedPrice;
              const label = getQuestionTitle(questionId);
              priceBreakdown.push({ label: label, answer: '×' + quantity, price: addedPrice });
            }
          }
        } else if ($(this).is('select')) {
          answers[questionId] = value;
          const selectedOption = $(this).find('option:selected');
          const price = parseFloat(selectedOption.data('price'));

          if (!isNaN(price)) {
            if (price === 0 && selectedOption.val()) {
              quoteRequired = true;
            }
            if (price > 0) {
              const label = getQuestionTitle(questionId);
              priceBreakdown.push({ label: label, price: price });
            }
            questionPriceTotal += price;
          }
        } else {
          answers[questionId] = value;
        }
      }
    });

    $('#question_answers').val(JSON.stringify(answers));
    $('#question_price_breakdown_value').val(JSON.stringify(priceBreakdown));

    // Update total price
    updateTotalPrice(questionPriceTotal, priceBreakdown);

    // Toggle contact-for-quote button when needed
    toggleQuoteContact(quoteRequired);

    // Check conditional questions after answers update
    checkConditionalQuestions();
  }

  function toggleQuoteContact(showContact) {
    if (showContact) {
      $('#next_step_button').addClass('d-none');
      $('#contact_quote_button').addClass('d-none');
      $('#questions_next_step_button').addClass('d-none');
      $('#questions_contact_quote_button').removeClass('d-none');
    } else {
      $('#next_step_button').removeClass('d-none');
      $('#contact_quote_button').addClass('d-none');
      $('#questions_next_step_button').removeClass('d-none');
      $('#questions_contact_quote_button').addClass('d-none');
    }
  }

  // Update the total price including question option prices
  function fetchPostalCodePrice(zipCode) {
    const url = '{{ route("frontend.services.postal_code.price") }}';

    if (!zipCode || zipCode.trim() === '') {
      $('#postal_price_value').val('0.00');
      updateQuestionAnswers();
      return;
    }

    $.ajax({
      url: url,
      method: 'GET',
      data: {
        zip_code: zipCode,
        service_id: $('#service_id').val(),
      },
      success: function (response) {
        const postalPrice = parseFloat(response.postal_price) || 0;
        $('#postal_price_value').val(postalPrice.toFixed(2));
        updateQuestionAnswers();
      },
      error: function () {
        $('#postal_price_value').val('0.00');
        updateQuestionAnswers();
      }
    });
  }

  function updateTotalPrice(questionPriceTotal, priceBreakdown) {
    const basePrice = parseFloat($('#base_service_price').val()) || 0;
    const postalPrice = parseFloat($('#postal_price_value').val()) || 0;
    const urgencySurcharge = parseFloat($('#urgency_surcharge_value').val()) || 0;
    const totalPrice = basePrice + questionPriceTotal + postalPrice + urgencySurcharge;

    $('#service_price_value').val(totalPrice.toFixed(2));
    $('#total_price').val(totalPrice.toFixed(2));
    $('#question_price_total_value').val(questionPriceTotal.toFixed(2));

    updateSummaryBreakdown(basePrice, priceBreakdown || [], postalPrice, urgencySurcharge, totalPrice);

  }

  function updateSummaryBreakdown(basePrice, priceBreakdown, postalPrice, urgencySurcharge, totalPrice) {
    console.log("[Urgency] updateSummaryBreakdown called — urgencySurcharge=" + urgencySurcharge + " | total=" + totalPrice, new Error().stack.split('\n')[2]);
    const sym = $('#currency_symbol_value').val() || '£';
    $('#summary-service-price').text(sym + basePrice.toFixed(2));

    // Render individual add-on rows
    $('#summary-question-breakdown').empty();
    if (priceBreakdown && priceBreakdown.length > 0) {
      priceBreakdown.forEach(function (item) {
        if (item.price > 0) {
          const valueText = item.answer ? '<small class="text-muted d-block">' + item.answer + '</small>' : '';
          $('#summary-question-breakdown').append(
            '<div class="summary-row">' +
              '<span class="summary-label">' + item.label + ':' + valueText + '</span>' +
              '<span class="summary-value text-success">+' + sym + item.price.toFixed(2) + '</span>' +
            '</div>'
          );
        }
      });
    }

    if (postalPrice > 0) {
      $('#summary-postal-row').show();
      $('#summary-postal-price').text('+' + sym + postalPrice.toFixed(2));
    } else {
      $('#summary-postal-row').hide();
    }

    if (urgencySurcharge > 0) {
      var label = urgencySurcharge === 150 ? 'Within 4 hours' : (urgencySurcharge === 100 ? 'Within 24 hours' : 'Within 48 hours');
      if ($('#summary-urgency-row').length === 0) {
        $('#summary-postal-row').after(
          '<div class="summary-row" id="summary-urgency-row">' +
            '<span class="summary-label">Urgent Booking Fee:<small class="text-muted d-block" id="summary-urgency-label"></small></span>' +
            '<span class="summary-value text-warning" id="summary-urgency-price"></span>' +
          '</div>'
        );
      }
      $('#summary-urgency-label').text(label);
      $('#summary-urgency-price').text('+' + sym + urgencySurcharge.toFixed(2));
    } else {
      $('#summary-urgency-row').remove();
    }

    $('#summary-total-amount').text(sym + totalPrice.toFixed(2));
  }

  // Check and show/hide conditional questions based on current answers
  function checkConditionalQuestions() {
    $('.question-card[data-has-conditions="true"]').each(function () {
      const $card = $(this);
      const conditions = $card.data('conditions');

      if (!conditions || !Array.isArray(conditions)) {
        return;
      }

      let shouldShow = false;

      // Evaluate conditions with AND/OR logic
      let result = null; // null means not evaluated yet

      for (let i = 0; i < conditions.length; i++) {
        const condition = conditions[i];
        const dependentQuestionId = condition.depends_on_question_id;
        const expectedOptionId = condition.depends_on_option_id;
        const conditionType = condition.condition_type;
        const operator = condition.operator; // 'AND' or 'OR' or null

        // Get the current answer for the dependent question
        const $dependentInputs = $(`.question-input[data-question-id="${dependentQuestionId}"]`);
        let currentAnswer = $dependentInputs.val();

        if ($dependentInputs.first().hasClass('booking-question-choice')) {
          const $checkedInputs = $dependentInputs.filter(':checked');
          if ($dependentInputs.first().attr('type') === 'checkbox') {
            currentAnswer = $checkedInputs.map(function () {
              return $(this).val();
            }).get();
          } else {
            currentAnswer = $checkedInputs.val() || '';
          }
        }
        const hasAnswer = Array.isArray(currentAnswer)
          ? currentAnswer.length > 0
          : currentAnswer !== undefined && currentAnswer !== null && String(currentAnswer).trim() !== '';

        let conditionMet = false;

        if (!hasAnswer) {
          conditionMet = false;
        } else {
          switch (conditionType) {
            case 'equals':
              conditionMet = String(currentAnswer) === String(expectedOptionId);
              break;
            case 'not_equals':
              conditionMet = String(currentAnswer) !== String(expectedOptionId);
              break;
            case 'contains':
              conditionMet = String(currentAnswer).includes(String(expectedOptionId));
              break;
            case 'not_contains':
              conditionMet = !String(currentAnswer).includes(String(expectedOptionId));
              break;
            default:
              break;
          }
        }

        // Apply operator logic
        if (result === null) {
          // First condition
          result = conditionMet;
        } else {
          // Subsequent conditions
          if (operator === 'AND') {
            result = result && conditionMet;
          } else if (operator === 'OR') {
            result = result || conditionMet;
          } else {
            // Default to AND if operator is null or invalid
            result = result && conditionMet;
          }
        }

      }

      shouldShow = result;

      // Show or hide the question
      if (shouldShow) {
        $card.slideDown(300);
      } else {
        $card.slideUp(300);
        $card.find('.booking-question-choice').prop('checked', false);
        $card.find('.question-input').not('.booking-question-choice').val('');
      }
    });
  }

  // Remove the old time next step handler since questions load automatically now
  // Regular stepper navigation will handle the flow

  // Hook into booking modal show event
  // Note: The appointment.js file also handles initial question loading
  // This listener provides additional safety and works with the event delegation
  $(document).on('show.bs.modal', '#makeBooking', function (e) {
    if ($('#booking-stepper').hasClass('booking-confirmation-only')) {
      return;
    }

    const button = $(e.relatedTarget);
    const serviceId = button.data('service-id') || button.attr('data-service-id');
    const isCartCheckout = button.data('cart-checkout') == 1 || button.attr('data-cart-checkout') == '1';

    if (serviceId && serviceId !== '' && serviceId !== 'undefined') {
      $('#service_id').val(serviceId);
      $('#cart_mode').val(isCartCheckout ? 1 : 0);
      fetchPostalCodePrice($('#zip_code').val());
      toggleQuoteContact(false);

      setTimeout(() => {
        if (typeof loadQuestions === 'function') {
          loadQuestions(serviceId);
        }
        if (typeof bookingStepper !== 'undefined') {
          try {
            bookingStepper.to(1);
          } catch (err) {
            console.error('Error resetting stepper:', err);
          }
        }
      }, 100);
    } else {
      console.error('❌ NO SERVICE ID FOUND on booking button');
      $('#questions-container').html('<p class="text-center text-danger">❌ Service ID not provided. This is a configuration issue.</p>');
    }
  });
</script>
