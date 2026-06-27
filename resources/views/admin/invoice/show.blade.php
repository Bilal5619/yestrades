@extends('admin.layout')

@section('content')
  @php
    $booking = $invoice->booking;
    $symbol = $booking->currency_symbol ?? '';
    $symbolPos = $booking->currency_symbol_position ?? 'left';
    $money = function ($amount) use ($symbol, $symbolPos) {
        if ($amount === null) {
            return '-';
        }
        return ($symbolPos == 'left' ? $symbol : '') . number_format($amount, 2, '.', ',') . ($symbolPos == 'right' ? $symbol : '');
    };
  @endphp

  <div class="page-header">
    <h4 class="page-title">{{ __('Invoice Details') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.invoices.index') }}">{{ __('Invoices') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Details') }}</a>
      </li>
    </ul>
  </div>

  <div class="text-right mb-3">
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-primary">{{ __('Back') }}</a>
  </div>

  <div class="row">
    {{-- Invoice info --}}
    <div class="col-md-7">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Invoice') . ' #' . $invoice->invoice_number }}
          </div>
        </div>
        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Invoice Number') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ $invoice->invoice_number }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Invoice Date') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Submitted On') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ $invoice->created_at->format('d M Y g:i A') }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Amount') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ $money($invoice->amount) }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('VAT / Tax') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ $invoice->vat !== null ? $money($invoice->vat) : '-' }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Total Amount') . ' :' }}</strong></div>
              <div class="col-lg-7"><strong>{{ $money($invoice->total_amount) }}</strong></div>
            </div>
            <hr>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Customer Paid Amount') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ $money($invoice->customer_paid_amount ?? $invoice->amount) }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Platform Commission') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ number_format($invoice->commission_percentage ?? 20, 2) }}%</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Commission Amount') . ' :' }}</strong></div>
              <div class="col-lg-7">{{ $money($invoice->commission_amount ?? 0) }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Vendor Payout Amount') . ' :' }}</strong></div>
              <div class="col-lg-7"><strong>{{ $money($invoice->vendor_payout_amount ?? 0) }}</strong></div>
            </div>
            <hr>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Invoice File') . ' :' }}</strong></div>
              <div class="col-lg-7">
                @if ($invoice->invoice_file)
                  <a href="{{ asset('assets/file/invoices/vendor/' . $invoice->invoice_file) }}" target="_blank"
                    class="btn btn-xs btn-info">{{ __('View Invoice') }}</a>
                @else
                  {{ '-' }}
                @endif
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Bank Details') . ' :' }}</strong></div>
              <div class="col-lg-7">{!! $invoice->bank_details ? nl2br(e($invoice->bank_details)) : '-' !!}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-5"><strong>{{ __('Notes') . ' :' }}</strong></div>
              <div class="col-lg-7">{!! $invoice->notes ? nl2br(e($invoice->notes)) : '-' !!}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Status + actions --}}
    <div class="col-md-5">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Status') }}</div>
        </div>
        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Invoice Status') . ' :' }}</strong></div>
              <div class="col-lg-6">
                @if ($invoice->invoice_status == 'approved')
                  <span class="badge badge-success">{{ __('Approved') }}</span>
                @elseif ($invoice->invoice_status == 'rejected')
                  <span class="badge badge-danger">{{ __('Rejected') }}</span>
                @else
                  <span class="badge badge-warning">{{ __('Submitted') }}</span>
                @endif
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Payment Status') . ' :' }}</strong></div>
              <div class="col-lg-6">
                @if ($invoice->payment_status == 'paid')
                  <span class="badge badge-success">{{ __('Paid') }}</span>
                @else
                  <span class="badge badge-secondary">{{ __('Unpaid') }}</span>
                @endif
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Approved On') . ' :' }}</strong></div>
              <div class="col-lg-6">{{ $invoice->approved_at ? $invoice->approved_at->format('d M Y g:i A') : '-' }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Payment Due') . ' :' }}</strong></div>
              <div class="col-lg-6">
                @if ($invoice->payment_due_date)
                  {{ $invoice->payment_due_date->format('d M Y g:i A') }}
                  @if ($invoice->is_overdue)
                    <br><span class="badge badge-danger">{{ __('Overdue') }}</span>
                  @endif
                @else
                  {{ '-' }}
                @endif
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Paid Date') . ' :' }}</strong></div>
              <div class="col-lg-6">{{ $invoice->paid_date ? $invoice->paid_date->format('d M Y') : '-' }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Transaction Ref') . ' :' }}</strong></div>
              <div class="col-lg-6">{{ $invoice->payment_reference ?? '-' }}</div>
            </div>
          </div>

          <hr>

          @if ($invoice->invoice_status == 'submitted')
            <form action="{{ route('admin.invoices.approve', ['id' => $invoice->id]) }}" method="post"
              class="d-inline-block">
              @csrf
              <button type="submit" class="btn btn-success btn-sm">{{ __('Approve') }}</button>
            </form>
            <form action="{{ route('admin.invoices.reject', ['id' => $invoice->id]) }}" method="post"
              class="d-inline-block">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm">{{ __('Reject') }}</button>
            </form>
          @endif

          @if ($invoice->invoice_status == 'approved' && $invoice->payment_status != 'paid')
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
              data-target="#markPaidModal">{{ __('Mark as Paid') }}</button>
          @endif
        </div>
      </div>

      {{-- Booking context --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Job / Booking') }}</div>
        </div>
        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Booking ID') . ' :' }}</strong></div>
              <div class="col-lg-6">
                @if ($booking)
                  <a href="{{ route('admin.appointment.details', ['id' => $booking->id]) }}">#{{ $booking->order_number }}</a>
                @else
                  {{ '-' }}
                @endif
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Service') . ' :' }}</strong></div>
              <div class="col-lg-6">{{ $invoice->service_title ?? '-' }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6"><strong>{{ __('Vendor') . ' :' }}</strong></div>
              <div class="col-lg-6">{{ $invoice->vendor_name ?? '-' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($invoice->invoice_status == 'approved' && $invoice->payment_status != 'paid')
    <div class="modal fade" id="markPaidModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form action="{{ route('admin.invoices.mark_paid', ['id' => $invoice->id]) }}" method="post">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Mark Invoice as Paid') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label><strong>{{ __('Payment Date') }}</strong> <span class="text-danger">*</span></label>
                <input type="date" name="paid_date" class="form-control" required value="{{ date('Y-m-d') }}">
              </div>
              <div class="form-group">
                <label><strong>{{ __('Transaction Reference') }}</strong></label>
                <input type="text" name="payment_reference" class="form-control">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
              <button type="submit" class="btn btn-success btn-sm">{{ __('Confirm Paid') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endsection
