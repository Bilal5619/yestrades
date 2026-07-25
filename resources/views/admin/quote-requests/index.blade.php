@extends('admin.layout')

@section('content')

  <div class="page-header">
    <h4 class="page-title">Quote Requests</h4>
  </div>

  @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif
  @if (Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

  <div class="card mb-3">
    <div class="card-body py-2">
      <form method="GET" action="{{ route('admin.quote_requests.index') }}" class="form-inline">
        <label class="mr-2">Filter by Status:</label>
        <select name="status" class="form-control form-control-sm mr-2">
          <option value="">-- All Statuses --</option>
          @foreach ($statuses as $value => $label)
            <option value="{{ $value }}" {{ $selectedStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-secondary mr-1">Filter</button>
        <a href="{{ route('admin.quote_requests.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      @if ($quotes->count() == 0)
        <h4 class="text-center text-muted">No quote requests found</h4>
      @else
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Postcode</th>
                <th>Submitted</th>
                <th>Status</th>
                <th>Assigned Staff</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($quotes as $quote)
                <tr>
                  <td>{{ $quote->reference_number }}</td>
                  <td>{{ $quote->customer_name }}</td>
                  <td>{{ $quote->serviceName() }}</td>
                  <td>{{ $quote->customer_phone }}</td>
                  <td>{{ $quote->customer_email }}</td>
                  <td>{{ $quote->customer_postcode }}</td>
                  <td>{{ $quote->created_at->format('d M Y') }}</td>
                  <td><span class="badge badge-info">{{ $quote->statusLabel() }}</span></td>
                  <td>{{ optional($quote->assignedStaff)->username ?? '—' }}</td>
                  <td>
                    <a href="{{ route('admin.quote_requests.show', $quote->id) }}" class="btn btn-info btn-sm">View</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3 text-center">
          <div class="d-inline-block mx-auto">
            {{ $quotes->appends(['status' => $selectedStatus])->links() }}
          </div>
        </div>
      @endif
    </div>
  </div>

@endsection
