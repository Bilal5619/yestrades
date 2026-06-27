@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Reviews') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Reviews') }}</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <form action="{{ route('admin.service_reviews.index') }}" method="GET">
            <div class="row align-items-end">
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Search') }}</label>
                  <input type="text" name="term" class="form-control" value="{{ request('term') }}"
                    placeholder="{{ __('Booking, customer, email, comment') }}">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group">
                  <label>{{ __('Status') }}</label>
                  <select name="status" class="form-control">
                    <option value="">{{ __('All') }}</option>
                    @foreach (['pending', 'approved', 'rejected'] as $status)
                      <option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst($status)) }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-lg-3">
                <button type="submit" class="btn btn-primary btn-sm">{{ __('Search') }}</button>
                <a href="{{ route('admin.service_reviews.index') }}" class="btn btn-secondary btn-sm">{{ __('Reset') }}</a>
              </div>
            </div>
          </form>
        </div>

        <div class="card-body">
          @if ($reviews->count() === 0)
            <h3 class="text-center mt-3">{{ __('No reviews found') }}</h3>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-2">
                <thead>
                  <tr>
                    <th>{{ __('Booking') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Vendor / Service') }}</th>
                    <th>{{ __('Rating') }}</th>
                    <th>{{ __('Review') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Submitted') }}</th>
                    <th>{{ __('Actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($reviews as $review)
                    @php
                      $serviceName = optional(optional($review->booking)->serviceContent->first())->name
                        ?: optional(optional($review->service)->content->first())->name
                        ?: '-';
                      $customerName = optional($review->booking)->customer_name ?: optional($review->user)->name ?: __('Guest');
                      $customerEmail = $review->customer_email ?: optional($review->booking)->customer_email ?: optional($review->user)->email;
                    @endphp
                    <tr>
                      <td>
                        {{ optional($review->booking)->order_number ?: '-' }}
                        @if ($review->booking_id)
                          <div class="small text-muted">#{{ $review->booking_id }}</div>
                        @endif
                      </td>
                      <td>
                        <strong>{{ $customerName }}</strong>
                        <div class="small text-muted">{{ $customerEmail ?: '-' }}</div>
                      </td>
                      <td>
                        <strong>{{ optional($review->vendor)->username ?: '-' }}</strong>
                        <div class="small text-muted">{{ $serviceName }}</div>
                      </td>
                      <td>{{ $review->rating ?: '-' }}</td>
                      <td style="min-width: 260px;">
                        <div>{{ $review->comment ?: '-' }}</div>
                        @if ($review->image)
                          <a href="{{ asset('assets/img/service-reviews/' . $review->image) }}" target="_blank">{{ __('View image') }}</a>
                        @endif
                      </td>
                      <td>
                        <span class="badge badge-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'warning') }}">
                          {{ __(ucfirst($review->status ?? 'pending')) }}
                        </span>
                      </td>
                      <td>{{ optional($review->reviewed_at)->format('d M Y H:i') ?: '-' }}</td>
                      <td>
                        @if ($review->reviewed_at && $review->status !== 'approved')
                          <form action="{{ route('admin.service_reviews.approve', $review->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm mb-1">{{ __('Approve') }}</button>
                          </form>
                        @endif

                        @if ($review->reviewed_at && $review->status !== 'rejected')
                          <form action="{{ route('admin.service_reviews.reject', $review->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm mb-1">{{ __('Reject') }}</button>
                          </form>
                        @endif

                        <form action="{{ route('admin.service_reviews.delete', $review->id) }}" method="POST" class="d-inline-block">
                          @csrf
                          <button type="submit" class="btn btn-danger btn-sm mb-1 deleteBtn">{{ __('Delete') }}</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-3">
              {{ $reviews->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
