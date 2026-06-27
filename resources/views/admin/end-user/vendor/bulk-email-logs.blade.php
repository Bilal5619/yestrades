@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Bulk Vendor Email Logs') }}</h4>
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
        <a href="#">{{ __('Vendor Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Email Logs') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Previous Bulk Emails') }}</div>
          <a class="btn btn-primary btn-sm float-right d-inline-block" href="{{ route('admin.vendor_management.bulk_email.create') }}">
            <span class="btn-label">
              <i class="fas fa-envelope"></i>
            </span>
            {{ __('Send Email') }}
          </a>
        </div>

        <div class="card-body">
          @if ($bulkEmails->count() == 0)
            <h3 class="text-center">{{ __('NO EMAIL LOG FOUND') . '!' }}</h3>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-3">
                <thead>
                  <tr>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Send Type') }}</th>
                    <th>{{ __('Total Recipients') }}</th>
                    <th>{{ __('Status Summary') }}</th>
                    <th>{{ __('Date/Time') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($bulkEmails as $email)
                    <tr>
                      <td>{{ $email->subject }}</td>
                      <td>{{ ucfirst($email->send_type) }}</td>
                      <td>{{ $email->total_recipients }}</td>
                      <td>
                        <span class="badge badge-warning">{{ __('Pending') }}: {{ $email->pending_count }}</span>
                        <span class="badge badge-success">{{ __('Sent') }}: {{ $email->sent_count }}</span>
                        <span class="badge badge-danger">{{ __('Failed') }}: {{ $email->failed_count }}</span>
                      </td>
                      <td>{{ $email->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{ $bulkEmails->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
