@extends('vendors.layout')

@section('content')
  <div class="page-inner">
    <div class="page-header">
      <h4 class="page-title">{{ __('Available Jobs') }}</h4>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">{{ __('Jobs matching your services & area') }}</div>
            <p class="text-muted mb-0 mt-1">
              {{ __('Unassigned jobs in your selected categories and near your service-area postcodes. Claim a job to add it to your appointments.') }}
            </p>
          </div>
          <div class="card-body">
            @if (!empty($needsCategories) && $needsCategories)
              <div class="alert alert-warning">
                {{ __('Select your service categories in your profile to see matching jobs.') }}
              </div>
            @endif

            <div class="table-responsive">
              <table class="table table-striped mt-2">
                <thead>
                  <tr>
                    <th>{{ __('Booking ID') }}</th>
                    <th>{{ __('Service') }}</th>
                    <th>{{ __('Job Postcode') }}</th>
                    <th>{{ __('Distance') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($jobs as $job)
                    <tr>
                      <td>{{ '#' . $job->order_number }}</td>
                      <td>
                        @if ($job->serviceContent && $job->serviceContent->isNotEmpty())
                          {{ $job->serviceContent->first()->name }}
                        @else
                          {{ '-' }}
                        @endif
                      </td>
                      <td>{{ $job->site_zip_code ?: '—' }}</td>
                      <td>
                        @if ($job->match_distance !== null)
                          {{ number_format($job->match_distance, 1) }} {{ __('mi') }}
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td>{{ $job->booking_date ? \Carbon\Carbon::parse($job->booking_date)->format('d M Y') : '—' }}</td>
                      <td>
                        <form action="{{ route('vendor.available_jobs.claim', $job->id) }}" method="POST"
                          onsubmit="return confirm('{{ __('Claim this job?') }}')">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">{{ __('Claim') }}</button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">
                        {{ __('No matching jobs available right now.') }}
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
