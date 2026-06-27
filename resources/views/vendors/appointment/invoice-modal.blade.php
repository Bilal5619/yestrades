<div class="modal fade" id="uploadInvoiceModal-{{ $item->id }}" tabindex="-1" role="dialog"
  aria-labelledby="uploadInvoiceModalLabel-{{ $item->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('vendor.appointment.invoice.store', ['id' => $item->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="uploadInvoiceModalLabel-{{ $item->id }}">{{ __('Upload Invoice') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          {{-- Auto-filled (read-only) context --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Booking / Job ID') }}</strong></label>
                <input type="text" class="form-control" value="#{{ $item->order_number }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Service Title') }}</strong></label>
                <input type="text" class="form-control"
                  value="{{ $item->serviceContent->isNotEmpty() ? $item->serviceContent->first()->name : '-' }}"
                  readonly>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label><strong>{{ __('Vendor Name') }}</strong></label>
                <input type="text" class="form-control" value="{{ $invoiceVendorName ?? '' }}" readonly>
              </div>
            </div>
          </div>

          <hr class="my-2">

          <div class="row">
            @php
              $customerPaidAmount = round((float) $item->customer_paid, 2);
              $commissionPercentage = 20;
              $commissionAmount = round(($customerPaidAmount * $commissionPercentage) / 100, 2);
              $vendorPayoutAmount = round($customerPaidAmount - $commissionAmount, 2);
              $currencySymbol = $item->currency_symbol ?? '£';
              $currencyPosition = $item->currency_symbol_position ?? 'left';
              $formatInvoiceMoney = function ($amount) use ($currencySymbol, $currencyPosition) {
                  return ($currencyPosition == 'left' ? $currencySymbol : '') . number_format($amount, 2, '.', ',') . ($currencyPosition == 'right' ? $currencySymbol : '');
              };
            @endphp
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Invoice Number') }}</strong></label>
                <input type="text" class="form-control" value="{{ __('Auto-generated') }}" disabled>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Invoice Date') }}</strong> <span class="text-danger">*</span></label>
                <input type="date" name="invoice_date" class="form-control" required
                  value="{{ old('invoice_date', date('Y-m-d')) }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Amount') }}</strong> <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="amount" class="form-control" required
                  value="{{ old('amount', number_format($customerPaidAmount, 2, '.', '')) }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Customer Paid Amount') }}</strong></label>
                <input type="text" class="form-control" value="{{ $formatInvoiceMoney($customerPaidAmount) }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Platform Commission') }}</strong></label>
                <input type="text" class="form-control" value="{{ number_format($commissionPercentage, 0) }}%" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Commission Amount') }}</strong></label>
                <input type="text" class="form-control" value="{{ $formatInvoiceMoney($commissionAmount) }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Vendor Payout Amount') }}</strong></label>
                <input type="text" class="form-control" value="{{ $formatInvoiceMoney($vendorPayoutAmount) }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('VAT / Tax') }}</strong> <small class="text-muted">({{ __('optional') }})</small></label>
                <input type="number" step="0.01" min="0" name="vat" class="form-control" value="{{ old('vat') }}">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label><strong>{{ __('Invoice PDF / Image') }}</strong> <small class="text-muted">({{ __('optional') }})</small></label>
                <input type="file" name="invoice_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text text-muted">
                  {{ __('You can upload an invoice if you have a personal invoice. PDF, JPG or PNG. Max 10 MB.') }}
                </small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Bank Details') }}</strong> <small class="text-muted">({{ __('optional') }})</small></label>
                <textarea name="bank_details" class="form-control" rows="3">{{ old('bank_details') }}</textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>{{ __('Notes') }}</strong> <small class="text-muted">({{ __('optional') }})</small></label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-primary btn-sm">{{ __('Submit Invoice') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
