@extends('vendors.layout')


@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Rejected Appointments') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('vendor.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Appointments') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Rejected Appointments') }}</a>
      </li>
    </ul>

  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-10">
              <form id="searchForm" action="{{ route('vendor.rejected_appointment') }}" method="GET">
                <div class="row">
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>{{ __('Booking ID') }}</label>
                      <input name="order_no" type="text" class="form-control" placeholder="Search Here..."
                        value="{{ !empty(request()->input('order_no')) ? request()->input('order_no') : '' }}">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>{{ __('Payment') }}</label>
                      <select class="form-control select2" name="payment_status"
                        onchange="document.getElementById('searchForm').submit()">
                        <option value="" {{ empty(request()->input('payment_status')) ? 'selected' : '' }}>
                          {{ __('All') }}
                        </option>
                         <option value="pending" {{ request()->input('payment_status') == 'pending' ? 'selected' : '' }}>
                          {{ __('Pending') }}
                        </option>
                        <option value="completed"
                          {{ request()->input('payment_status') == 'completed' ? 'selected' : '' }}>
                          {{ __('Completed') }}
                        </option>

                        <option value="rejected"
                          {{ request()->input('payment_status') == 'rejected' ? 'selected' : '' }}>
                          {{ __('Rejected') }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>{{ __('Language') }}</label>
                      @includeIf('vendors.partials.languages')
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <div class="col-lg-2 mt-4 py-3">
              <button class="btn btn-danger btn-sm d-none bulk-delete float-lg-right"
                data-href="{{ route('vendor.appointment.bulk-destroy') }}" class="card-header-button">
                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($booking_item) == 0)
                <h3 class="text-center mt-3">{{ __('NO APPOINTMENT FOUND') . '!' }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-2">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{ __('Booking ID.') }}</th>
                        <th scope="col">{{ __('Service Title') }}</th>
                        <th scope="col">{{ __('Customer') }}</th>
                        <th scope="col">{{ __('Amount') }}</th>
                        <th scope="col">{{ __('Appointment Date') }}</th>
                        <th scope="col">{{ __('Payment Status') }}</th>
                        <th scope="col">{{ __('Order Status') }}</th>
                        <th scope="col">{{ __('Action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($booking_item as $item)
                        @php
                          $symbol = $item->currency_symbol;
                          $symbol_positon = $item->currency_symbol_position;
                        @endphp
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $item->id }}">
                          </td>
                          <td>{{ '#' . $item->order_number }}</td>
                          <td>
                            @if ($item->serviceContent->isNotEmpty())
                              @foreach ($item->serviceContent as $content)
                                <a href="{{ route('vendor.appointment.details', ['id' => $item->id]) }}">
                                  {{ truncateString($content->name, 30) }}
                                </a>
                              @endforeach
                            @else
                              {{ '-' }}
                            @endif
                          </td>
                          <td>{{ $item->customer_name }}</td>
                          <td>
                            {{ $symbol_positon == 'left' ? $symbol : '' }}{{ number_format($item->customer_paid, 2, '.', ',') }}{{ $symbol_positon == 'right' ? $symbol : '' }}
                          </td>
                          <td>
                            {{ \Carbon\Carbon::parse($item->booking_date)->format('M d, Y') }}
                          </td>
                          <td>
                            @if ($item->gateway_type == 'online')
                              <h2 class="d-inline-block"><span class="badge badge-success">{{ __('Completed') }}</span>
                              </h2>
                            @else
                              @if ($item->payment_status == 'pending')
                                <h2 class="d-inline-block"><span class="badge badge-warning">{{ __('Pending') }}</span>
                                </h2>
                              @else
                                <h2 class="d-inline-block"><span
                                    class="badge badge-{{ $item->payment_status == 'completed' ? 'success' : 'danger' }}">{{ ucfirst($item->payment_status) }}</span>
                                </h2>
                              @endif
                            @endif
                          </td>
                          <td>
                            @if (!empty($item->service->id))
                              @if ($item->order_status == 'pending')
                                <form id="orderStatusForm-{{ $item->id }}" class="d-inline-block"
                                  action="{{ route('vendor.appointment.update_status', ['id' => $item->id]) }}"
                                  method="post">
                                  @csrf
                                  <select
                                    class="form-control form-control-sm @if ($item->order_status == 'pending') bg-warning text-dark @elseif ($item->order_status == 'accepted') bg-success @else bg-danger @endif"
                                    style="min-width: 115px;"
                                    name="order_status"
                                    onchange="document.getElementById('orderStatusForm-{{ $item->id }}').submit()">
                                    <option value="pending" {{ $item->order_status == 'pending' ? 'selected' : '' }}>
                                      {{ __('Pending') }}
                                    </option>
                                    <option value="accepted" {{ $item->order_status == 'accepted' ? 'selected' : '' }}>
                                      {{ __('Accept') }}
                                    </option>
                                    <option value="rejected" {{ $item->order_status == 'rejected' ? 'selected' : '' }}>
                                      {{ __('Reject') }}
                                    </option>
                                  </select>
                                </form>
                              @else
                                <h2 class="d-inline-block"><span
                                    class="badge badge-{{ $item->order_status == 'accepted' ? 'success' : 'danger' }}">{{ ucfirst($item->order_status) }}</span>
                                </h2>
                              @endif
                            @else
                              {{ '-' }}
                            @endif
                          </td>
                          <td>
                            @include('vendors.appointment.action-dropdown', ['item' => $item])
                          </td>
                        </tr>
                        @if ($item->order_status == 'completed' && !$item->vendorInvoice)
                          @include('vendors.appointment.invoice-modal', ['item' => $item, 'invoiceVendorName' => $invoiceVendorName])
                        @endif
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="mt-3 text-center">
            <div class="d-inline-block mx-auto">
              {{ $booking_item->appends([
                      'order_no' => request()->input('order_no'),
                      'payment_status' => request()->input('payment_status'),
                      'order_status' => request()->input('order_status'),
                  ])->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
