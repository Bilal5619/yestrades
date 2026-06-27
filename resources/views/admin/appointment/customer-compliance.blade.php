@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Customer Compliance') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Customer Compliance') }}</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Customer Date / Time Change Requests') }}</div>
          <span class="badge badge-warning ml-2">{{ $requests->count() }}</span>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>{{ __('Booking No.') }}</th>
                  <th>{{ __('Customer') }}</th>
                  <th>{{ __('Service') }}</th>
                  <th>{{ __('Current Schedule') }}</th>
                  <th>{{ __('Requested Schedule') }}</th>
                  <th>{{ __('Requested On') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($requests as $req)
                  @php
                    $serviceName = optional($req->serviceContent->first())->name ?? ('Service #' . $req->service_id);
                    $currentTime = trim(($req->start_date ?? '') . ' - ' . ($req->end_date ?? ''), ' -');
                  @endphp
                  <tr>
                    <td>
                      <a href="{{ route('admin.appointment.details', ['id' => $req->id]) }}">
                        #{{ $req->order_number }}
                      </a>
                    </td>
                    <td>{{ $req->customer_name }}</td>
                    <td>{{ truncateString($serviceName, 30) }}</td>
                    <td>
                      {{ $req->booking_date ? \Carbon\Carbon::parse($req->booking_date)->format('M d, Y') : '-' }}<br>
                      <small class="text-muted">{{ $currentTime !== '' ? $currentTime : '-' }}</small>
                    </td>
                    <td>
                      <span class="text-success font-weight-bold">
                        {{ \Carbon\Carbon::parse($req->requested_booking_date)->format('M d, Y') }}
                      </span><br>
                      <small class="text-muted">
                        {{ \Carbon\Carbon::parse($req->requested_start_time)->format('g:i A') }}
                        - {{ \Carbon\Carbon::parse($req->requested_end_time)->format('g:i A') }}
                      </small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($req->updated_at)->format('M d, Y h:i A') }}</td>
                    <td>
                      <div class="d-flex" style="gap:6px;">
                        <form action="{{ route('admin.appointment.reschedule_approve', ['id' => $req->id]) }}" method="post">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">{{ __('Approve') }}</button>
                        </form>
                        <form action="{{ route('admin.appointment.reschedule_reject', ['id' => $req->id]) }}" method="post">
                          @csrf
                          <button type="submit" class="btn btn-danger btn-sm">{{ __('Reject') }}</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted">{{ __('No pending date change requests.') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
