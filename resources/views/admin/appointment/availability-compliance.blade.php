@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Compliance & Tasks') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Compliance & Tasks') }}</a></li>
    </ul>
  </div>

  {{-- Summary counters --}}
  <div class="row">
    @php
      $summary = [
          ['label' => 'Documents Awaiting Approval', 'count' => $taskCounts['pending_document_approvals'], 'class' => 'primary', 'icon' => 'fas fa-file-signature'],
          ['label' => 'Expired Documents', 'count' => $taskCounts['expired_documents'], 'class' => 'danger', 'icon' => 'fas fa-file-excel'],
          ['label' => 'Pending Payments', 'count' => $taskCounts['pending_payments'], 'class' => 'warning', 'icon' => 'fas fa-money-bill-wave'],
          ['label' => 'Jobs Needing a Vendor', 'count' => $taskCounts['jobs_needing_vendor'], 'class' => 'info', 'icon' => 'fas fa-user-plus'],
          ['label' => 'Availability Conflicts', 'count' => $taskCounts['availability_conflicts'], 'class' => 'secondary', 'icon' => 'fas fa-exclamation-triangle'],
      ];
    @endphp
    @foreach ($summary as $card)
      <div class="col-sm-6 col-md">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-4">
                <div class="icon-big text-center text-{{ $card['class'] }}">
                  <i class="{{ $card['icon'] }}"></i>
                </div>
              </div>
              <div class="col-8 col-stats">
                <p class="card-category mb-0">{{ __($card['label']) }}</p>
                <h4 class="card-title mb-0">{{ $card['count'] }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Uploaded vendor documents awaiting approval --}}
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Vendor Documents Awaiting Approval') }}</div>
          <span class="badge badge-primary ml-2">{{ $taskCounts['pending_document_approvals'] }}</span>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>{{ __('Vendor') }}</th>
                  <th>{{ __('Document') }}</th>
                  <th>{{ __('Uploaded On') }}</th>
                  <th>{{ __('Expiry Date') }}</th>
                  <th>{{ __('Document Status') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $docLabels = ['insurance' => 'Public Liability Insurance', 'id_document' => 'Driving Licence / Passport', 'right_to_work' => 'Right to Work if not British'];
                @endphp
                @forelse ($pendingDocumentApprovals as $doc)
                  @php $vendor = $doc->vendor; @endphp
                  <tr>
                    <td>{{ optional(optional($vendor)->vendor_info)->name ?? optional($vendor)->username ?? '—' }}</td>
                    <td>{{ __($docLabels[$doc->type] ?? ucfirst($doc->type)) }}</td>
                    <td>{{ optional($doc->created_at)->format('d M Y') }}</td>
                    <td>{{ optional($doc->expiry_date)->format('d M Y') ?: '—' }}</td>
                    <td><span class="badge badge-info">{{ __(ucwords(str_replace('_', ' ', $doc->normalized_status))) }}</span></td>
                    <td>
                      @if ($vendor)
                        <a href="{{ route('admin.vendor_management.vendor_details', ['id' => $doc->vendor_id]) }}" class="btn btn-primary btn-sm">{{ __('Review / Approve') }}</a>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-3">{{ __('No vendor documents are awaiting approval.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Expired documents --}}
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Expired Vendor Documents') }}</div>
          <span class="badge badge-danger ml-2">{{ $taskCounts['expired_documents'] }}</span>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>{{ __('Vendor') }}</th>
                  <th>{{ __('Document') }}</th>
                  <th>{{ __('Expired On') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $docLabels = ['insurance' => 'Public Liability Insurance', 'id_document' => 'Driving Licence / Passport', 'right_to_work' => 'Right to Work if not British'];
                @endphp
                @forelse ($expiredDocuments as $doc)
                  <tr>
                    <td>{{ optional(optional($doc->vendor)->vendor_info)->name ?? optional($doc->vendor)->username ?? '—' }}</td>
                    <td>{{ __($docLabels[$doc->type] ?? ucfirst($doc->type)) }}</td>
                    <td class="text-danger">{{ optional($doc->expiry_date)->format('d M Y') }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $doc->status)) }}</span></td>
                    <td>
                      @if ($doc->vendor)
                        <a href="{{ route('admin.vendor_management.vendor_details', ['id' => $doc->vendor_id]) }}" class="btn btn-primary btn-sm">{{ __('Review') }}</a>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-3">{{ __('No expired documents.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Pending payments --}}
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Jobs Awaiting Payment Decision') }}</div>
          <span class="badge badge-warning ml-2">{{ $taskCounts['pending_payments'] }}</span>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>{{ __('Booking ID') }}</th>
                  <th>{{ __('Service') }}</th>
                  <th>{{ __('Vendor') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($pendingPayments as $item)
                  <tr>
                    <td>{{ '#' . $item->order_number }}</td>
                    <td>{{ $item->serviceContent->isNotEmpty() ? $item->serviceContent->first()->name : '—' }}</td>
                    <td>{{ $item->vendor_id == 0 ? __('Unassigned') : (optional($item->vendor)->username ?? '—') }}</td>
                    <td>{{ $item->currency_symbol_position == 'left' ? $item->currency_symbol : '' }}{{ number_format($item->customer_paid, 2) }}{{ $item->currency_symbol_position == 'right' ? $item->currency_symbol : '' }}</td>
                    <td><a href="{{ route('admin.appointment.details', ['id' => $item->id]) }}" class="btn btn-primary btn-sm">{{ __('Review') }}</a></td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-3">{{ __('No payments awaiting a decision.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Jobs needing a vendor --}}
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Jobs Needing a Vendor') }}</div>
          <span class="badge badge-info ml-2">{{ $taskCounts['jobs_needing_vendor'] }}</span>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>{{ __('Booking ID') }}</th>
                  <th>{{ __('Service') }}</th>
                  <th>{{ __('Job Postcode') }}</th>
                  <th>{{ __('Date') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($jobsNeedingVendor as $item)
                  <tr>
                    <td>{{ '#' . $item->order_number }}</td>
                    <td>{{ $item->serviceContent->isNotEmpty() ? $item->serviceContent->first()->name : '—' }}</td>
                    <td>{{ $item->site_zip_code ?: '—' }}</td>
                    <td>{{ $item->booking_date ? \Carbon\Carbon::parse($item->booking_date)->format('d M Y') : '—' }}</td>
                    <td><a href="{{ route('admin.appointment.details', ['id' => $item->id]) }}" class="btn btn-primary btn-sm">{{ __('Find / Assign Vendor') }}</a></td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-3">{{ __('All jobs have a vendor assigned.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Availability conflicts --}}
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Vendor Availability Conflicts') }}</div>
          <span class="badge badge-secondary ml-2">{{ $taskCounts['availability_conflicts'] }}</span>
          <p class="text-muted mb-0 mt-1">
            {{ __('Upcoming jobs whose assigned vendor has a conflict for that slot — a double-booking, self-marked unavailability, or a clash with their weekly schedule.') }}
          </p>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>{{ __('Booking ID') }}</th>
                  <th>{{ __('Service') }}</th>
                  <th>{{ __('Vendor') }}</th>
                  <th>{{ __('Job Date') }}</th>
                  <th>{{ __('Unavailability') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($conflictBookings as $item)
                  <tr>
                    <td>{{ '#' . $item->order_number }}</td>
                    <td>{{ $item->serviceContent->isNotEmpty() ? $item->serviceContent->first()->name : '—' }}</td>
                    <td>
                      @if ($item->vendor)
                        <a href="{{ route('admin.vendor_management.vendor_details', ['id' => $item->vendor_id]) }}">
                          {{ optional($item->vendorInfo)->name ?? $item->vendor->username }}
                        </a>
                      @else
                        {{ '-' }}
                      @endif
                    </td>
                    <td>{{ $item->booking_date ? \Carbon\Carbon::parse($item->booking_date)->format('d M Y') : '—' }}</td>
                    <td>
                      @forelse ($complianceReasons[$item->id] ?? [] as $reason)
                        <span class="badge badge-danger d-block mb-1" style="white-space:normal;">{{ $reason }}</span>
                      @empty
                        <span class="text-muted">—</span>
                      @endforelse
                    </td>
                    <td><a href="{{ route('admin.appointment.details', ['id' => $item->id]) }}" class="btn btn-primary btn-sm">{{ __('View') }}</a></td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-3">{{ __('No availability conflicts.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
