<div class="dropdown">
  <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
    aria-expanded="false">
    {{ __('Select') }}
  </button>
  <div class="dropdown-menu dropdown-menu-right">
    <a href="{{ route('vendor.appointment.details', ['id' => $item->id]) }}" class="dropdown-item">
      {{ __('Details') }}
    </a>
    @if ($item->order_status == 'completed')
      @if ($item->vendorInvoice)
        @if ($item->vendorInvoice->invoice_file)
          <a href="{{ asset('assets/file/invoices/vendor/' . $item->vendorInvoice->invoice_file) }}" target="_blank"
            class="dropdown-item">
            {{ __('View Invoice') }}
          </a>
        @else
          <span class="dropdown-item text-muted">{{ __('Invoice Submitted') }}</span>
        @endif
      @else
        <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal"
          data-target="#uploadInvoiceModal-{{ $item->id }}">
          {{ __('Upload Invoice') }}
        </a>
      @endif
    @endif
  </div>
</div>
