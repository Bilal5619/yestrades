@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Invoices') }}</h4>
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
        <a href="#">{{ __('Invoices') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <form id="searchForm" action="{{ route('admin.invoices.index') }}" method="GET">
            <div class="row">
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Invoice No') }}</label>
                  <input name="invoice_number" type="text" class="form-control" placeholder="{{ __('Search Here...') }}"
                    value="{{ request()->input('invoice_number') }}">
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Invoice Status') }}</label>
                  <select class="form-control h-42" name="invoice_status"
                    onchange="document.getElementById('searchForm').submit()">
                    <option value="" {{ request()->input('invoice_status') === null || request()->input('invoice_status') === '' ? 'selected' : '' }}>{{ __('All') }}</option>
                    <option value="submitted" {{ request()->input('invoice_status') == 'submitted' ? 'selected' : '' }}>{{ __('Submitted') }}</option>
                    <option value="approved" {{ request()->input('invoice_status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                    <option value="rejected" {{ request()->input('invoice_status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Payment Status') }}</label>
                  <select class="form-control h-42" name="payment_status"
                    onchange="document.getElementById('searchForm').submit()">
                    <option value="" {{ request()->input('payment_status') === null || request()->input('payment_status') === '' ? 'selected' : '' }}>{{ __('All') }}</option>
                    <option value="unpaid" {{ request()->input('payment_status') == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                    <option value="paid" {{ request()->input('payment_status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                  </select>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="card-body">
          @if (count($invoices) == 0)
            <h3 class="text-center mt-3">{{ __('NO INVOICE FOUND') . '!' }}</h3>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-2">
                <thead>
                  <tr>
                    <th scope="col">{{ __('Invoice No') }}</th>
                    <th scope="col">{{ __('Booking ID') }}</th>
                    <th scope="col">{{ __('Vendor') }}</th>
                    <th scope="col">{{ __('Service') }}</th>
                    <th scope="col">{{ __('Amount') }}</th>
                    <th scope="col">{{ __('Commission') }}</th>
                    <th scope="col">{{ __('Vendor Payout') }}</th>
                    <th scope="col">{{ __('Invoice Date') }}</th>
                    <th scope="col">{{ __('Submitted') }}</th>
                    <th scope="col">{{ __('File') }}</th>
                    <th scope="col">{{ __('Invoice Status') }}</th>
                    <th scope="col">{{ __('Payment') }}</th>
                    <th scope="col">{{ __('Payment Due') }}</th>
                    <th scope="col">{{ __('Paid Date') }}</th>
                    <th scope="col">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($invoices as $invoice)
                    @php
                      $booking = $invoice->booking;
                      $symbol = $booking->currency_symbol ?? '';
                      $symbolPos = $booking->currency_symbol_position ?? 'left';
                    @endphp
                    <tr>
                      <td>{{ $invoice->invoice_number }}</td>
                      <td>{{ $booking ? '#' . $booking->order_number : '-' }}</td>
                      <td>{{ $invoice->vendor_name ?? '-' }}</td>
                      <td>{{ truncateString($invoice->service_title ?? '-', 30) }}</td>
                      <td>
                        {{ $symbolPos == 'left' ? $symbol : '' }}{{ number_format($invoice->total_amount, 2, '.', ',') }}{{ $symbolPos == 'right' ? $symbol : '' }}
                      </td>
                      <td>
                        {{ $symbolPos == 'left' ? $symbol : '' }}{{ number_format($invoice->commission_amount ?? 0, 2, '.', ',') }}{{ $symbolPos == 'right' ? $symbol : '' }}
                      </td>
                      <td>
                        {{ $symbolPos == 'left' ? $symbol : '' }}{{ number_format($invoice->vendor_payout_amount ?? 0, 2, '.', ',') }}{{ $symbolPos == 'right' ? $symbol : '' }}
                      </td>
                      <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                      <td>{{ $invoice->created_at->format('d M Y') }}</td>
                      <td>
                        @if ($invoice->invoice_file)
                          <a href="{{ asset('assets/file/invoices/vendor/' . $invoice->invoice_file) }}" target="_blank"
                            class="btn btn-xs btn-info">{{ __('View') }}</a>
                        @else
                          {{ '-' }}
                        @endif
                      </td>
                      <td>
                        @if ($invoice->invoice_status == 'approved')
                          <span class="badge badge-success">{{ __('Approved') }}</span>
                        @elseif ($invoice->invoice_status == 'rejected')
                          <span class="badge badge-danger">{{ __('Rejected') }}</span>
                        @else
                          <span class="badge badge-warning">{{ __('Submitted') }}</span>
                        @endif
                      </td>
                      <td>
                        @if ($invoice->payment_status == 'paid')
                          <span class="badge badge-success">{{ __('Paid') }}</span>
                        @else
                          <span class="badge badge-secondary">{{ __('Unpaid') }}</span>
                        @endif
                      </td>
                      <td>
                        @if ($invoice->payment_due_date)
                          {{ $invoice->payment_due_date->format('d M Y g:i A') }}
                          @if ($invoice->is_overdue)
                            <br><span class="badge badge-danger">{{ __('Overdue') }}</span>
                          @endif
                        @else
                          {{ '-' }}
                        @endif
                      </td>
                      <td>{{ $invoice->paid_date ? $invoice->paid_date->format('d M Y') : '-' }}</td>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('Select') }}
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">
                            @if ($invoice->invoice_status == 'submitted')
                              <form action="{{ route('admin.invoices.approve', ['id' => $invoice->id]) }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item text-success">{{ __('Approve') }}</button>
                              </form>
                              <form action="{{ route('admin.invoices.reject', ['id' => $invoice->id]) }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">{{ __('Reject') }}</button>
                              </form>
                            @endif
                            @if ($invoice->invoice_status == 'approved' && $invoice->payment_status != 'paid')
                              <a href="#" class="dropdown-item" data-toggle="modal"
                                data-target="#markPaidModal-{{ $invoice->id }}">{{ __('Mark as Paid') }}</a>
                            @endif
                            @if ($invoice->invoice_file)
                              <a href="{{ asset('assets/file/invoices/vendor/' . $invoice->invoice_file) }}"
                                target="_blank" class="dropdown-item">{{ __('View Invoice') }}</a>
                            @endif
                          </div>
                        </div>
                      </td>
                    </tr>

                    @if ($invoice->invoice_status == 'approved' && $invoice->payment_status != 'paid')
                      <div class="modal fade" id="markPaidModal-{{ $invoice->id }}" tabindex="-1" role="dialog"
                        aria-hidden="true">
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
                                  <input type="date" name="paid_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
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
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        <div class="card-footer">
          <div class="mt-3 text-center">
            <div class="d-inline-block mx-auto">
              {{ $invoices->appends([
                  'invoice_number' => request()->input('invoice_number'),
                  'invoice_status' => request()->input('invoice_status'),
                  'payment_status' => request()->input('payment_status'),
              ])->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
