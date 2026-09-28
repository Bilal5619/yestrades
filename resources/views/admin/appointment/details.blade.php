@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Appointment Details') }}</h4>
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
        <a href="{{ route('admin.all_appointment', ['language' => $defaultLang->code]) }}">{{ __('Appointments') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.all_appointment', ['language' => $defaultLang->code]) }}">{{ __('All Appointments') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Details') }}</a>
      </li>
    </ul>
  </div>
  <div id="vendorConflictAlert" class="alert alert-warning d-none" role="alert"></div>

  <div class="text-right mb-3">
    <a href="{{ route('admin.all_appointment', ['language' => $defaultLang->code]) }}" class="btn btn-primary">{{ __('Back') }}</a>
  </div>

  <div class="row">
    @php
      $symbol = $details->currency_symbol;
      $symbol_positon = $details->currency_symbol_position;
      $canResendTenantSlot = ($details->scheduling_mode ?? null) === 'tenant'
        && in_array($details->tenant_slot_status, [
          \App\Services\TenantSlotService::STATUS_WAITING,
          \App\Services\TenantSlotService::STATUS_EXPIRED,
        ], true);
    @endphp

    <div class="col-md-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="card-title d-inline-block">
            {{ __('Booking No.') . ' ' . '#' . $details->order_number }}
          </div>
          <div class="d-flex align-items-center" style="gap: 8px;">
            @if ($canResendTenantSlot)
              <form action="{{ route('admin.appointment.tenant_slot_resend', ['id' => $details->id]) }}" method="post" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-info btn-sm">
                  {{ __('Send New Tenant Link') }}
                </button>
              </form>
            @endif
            @if ($details->order_status == 'pending')
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#rescheduleModal">
                {{ __('Change Date/Time') }}
              </button>
            @endif
          </div>
        </div>

        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ (($details->item_type ?? 'service') === 'package' ? __('Package') : __('Service Title')) . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                @if (($details->item_type ?? 'service') === 'package')
                  {{ $details->package_name ?? '-' }}
                @elseif ($details->serviceContent->isNotEmpty())
                  @foreach ($details->serviceContent as $content)
                    <a href="{{ route('frontend.service.details', ['slug' => $content->slug, 'id' => $details->service->id]) }}"
                      target="_blank">
                      {{ truncateString($content->name, 50) }}
                    </a>
                  @endforeach
                @else
                  {{ '-' }}
                @endif
              </div>
            </div>
            @if (($details->item_type ?? 'service') === 'package' && !empty($details->package_included_services))
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Included Services') . ' :' }}</strong>
                </div>
                <div class="col-lg-6">
                  <ul class="pl-3 mb-0">
                    @foreach ($details->package_included_services as $includedService)
                      <li>{{ $includedService['name'] ?? '' }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            @endif
            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Booking Date') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                {{ date_format($details->created_at, 'M d, Y') }}
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Appointment Date') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                @if (!empty($details->booking_date))
                  {{ \Carbon\Carbon::parse($details->booking_date)->format('M d, Y') }}
                @elseif (($details->scheduling_mode ?? null) === 'tenant')
                  {{ __('Waiting for tenant') }}
                @else
                  {{ '-' }}
                @endif
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Appointment Time') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                {{ $details->start_date }} - {{ $details->end_date }}
              </div>
            </div>

            @if ($details->reschedule_status == 'requested')
              <div class="alert alert-warning mt-2 mb-3">
                <strong>{{ __('Customer requested a date/time change') }}</strong>
                <div class="mt-1">
                  {{ \Carbon\Carbon::parse($details->requested_booking_date)->format('M d, Y') }}
                  ({{ \Carbon\Carbon::parse($details->requested_start_time)->format('g:i A') }}
                  - {{ \Carbon\Carbon::parse($details->requested_end_time)->format('g:i A') }})
                </div>
                <div class="mt-2 d-flex" style="gap:8px;">
                  <form action="{{ route('admin.appointment.reschedule_approve', ['id' => $details->id]) }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">{{ __('Approve') }}</button>
                  </form>
                  <form action="{{ route('admin.appointment.reschedule_reject', ['id' => $details->id]) }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">{{ __('Reject') }}</button>
                  </form>
                </div>
              </div>
            @endif

            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>
                  @if ($details->max_person > 1)
                    {{ __('Persons') . ' :' }}
                  @else
                    {{ __('Person') . ' :' }}
                  @endif
                </strong>
              </div>

              <div class="col-lg-6">
                {{ $details->max_person }}
                @if ($details->max_person > 1)
                  {{ __('Persons') }}
                @else
                  {{ __('Person') }}
                @endif
              </div>
            </div>
            @if ($details->zoom_info != null)
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Zoom Host Url') . ' :' }}</strong>
                </div>
                <div class="col-lg-6">
                  @if ($details->zoom_info)
                    @php
                      $zoom_link = json_decode($details->zoom_info, true);
                    @endphp
                    <a target="_blank" href="{{ $zoom_link['start_url'] }}">
                      {{ strlen($zoom_link['start_url']) > 50 ? mb_substr($zoom_link['start_url'], 0, 50, 'utf-8') . '...' : $zoom_link['start_url'] }}
                    </a>
                  @else
                    {{ '-' }}
                  @endif
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Zoom Join Url') . ' :' }}</strong>
                </div>
                <div class="col-lg-6">
                  @if ($details->zoom_info)
                    @php
                      $zoom_link = json_decode($details->zoom_info, true);
                    @endphp
                    <a target="_blank" href="{{ $zoom_link['join_url'] }}">
                      {{ strlen($zoom_link['join_url']) > 50 ? mb_substr($zoom_link['join_url'], 0, 50, 'utf-8') . '...' : $zoom_link['join_url'] }}
                    </a>
                  @else
                    {{ '-' }}
                  @endif
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Meeting Join Password') . ' :' }}</strong>
                </div>
                <div class="col-lg-6">
                  @if ($details->zoom_info)
                    {{ $zoom_link['password'] }}
                  @else
                    {{ '-' }}
                  @endif
                </div>
              </div>
            @endif


            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Price') . ' :' }}</strong>
              </div>
              <div class="col-lg-6">
                {{ $symbol_positon == 'left' ? $symbol : '' }}
                {{ number_format($details->customer_paid, 2, '.', ',') }}
                {{ $symbol_positon == 'right' ? $symbol : '' }}
              </div>
            </div>

            @if (!empty($details->urgency_surcharge_amount) && $details->urgency_surcharge_amount > 0)
              <div class="row mb-3">
                <div class="col-12">
                  <div style="background:#fff3cd;border:2px solid #e67e22;border-radius:6px;padding:12px 16px;">
                    <div style="font-size:15px;font-weight:700;color:#c0392b;margin-bottom:6px;">
                      ⚡ {{ __('Urgent Booking') }}
                    </div>
                    <div class="row">
                      <div class="col-6">
                        <small class="text-muted d-block">{{ __('Urgency Period') }}</small>
                        <strong>{{ $details->urgency_surcharge_period ?? '-' }}</strong>
                      </div>
                      <div class="col-6">
                        <small class="text-muted d-block">{{ __('Urgency Fee') }}</small>
                        <strong style="color:#c0392b;">
                          {{ $symbol_positon == 'left' ? $symbol : '' }}{{ number_format($details->urgency_surcharge_amount, 2, '.', ',') }}{{ $symbol_positon == 'right' ? $symbol : '' }}
                        </strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif

            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Paid via') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                {{ $details->payment_method }}
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Payment Status') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                @if ($details->payment_status == 'completed')
                  <span class="badge badge-success">{{ __('Completed') }}</span>
                @elseif ($details->payment_status == 'pending')
                  <span class="badge badge-warning">{{ __('Pending') }}</span>
                @else
                  <span class="badge badge-danger">{{ __('Rejected') }}</span>
                @endif
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-6">
                <strong>{{ __('Appointment Status') . ' :' }}</strong>
              </div>

              <div class="col-lg-6">
                @if ($details->order_status == 'accepted')
                  <span class="badge badge-success">{{ __('Accepted') }}</span>
                @elseif ($details->order_status == 'completed')
                  <span class="badge badge-info">{{ __('Completed') }}</span>
                @elseif ($details->order_status == 'pending')
                  <span class="badge badge-warning">{{ __('Pending') }}</span>
                @else
                  <span class="badge badge-danger">{{ __('Rejected') }}</span>
                @endif
              </div>
            </div>
            @include('appointment.completion-evidence', ['appointment' => $details])

            @if ($details->vendorInvoice)
              @php $vendorInvoice = $details->vendorInvoice; @endphp
              <hr>
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Vendor Invoice') . ' :' }}</strong>
                </div>
                <div class="col-lg-6">
                  <a href="{{ route('admin.invoices.show', ['id' => $vendorInvoice->id]) }}" class="btn btn-xs btn-info">
                    {{ __('View Invoice Details') }}
                  </a>
                </div>
              </div>
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Invoice No') . ' :' }}</strong>
                </div>
                <div class="col-lg-6">
                  {{ $vendorInvoice->invoice_number }}
                </div>
              </div>
              @if ($vendorInvoice->invoice_file)
                <div class="row mb-2">
                  <div class="col-lg-6">
                    <strong>{{ __('Personal Invoice File') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-6">
                    <a href="{{ asset('assets/file/invoices/vendor/' . $vendorInvoice->invoice_file) }}"
                      target="_blank" class="btn btn-xs btn-info">
                      {{ __('View File') }}
                    </a>
                  </div>
                </div>
              @endif
            @endif

            @if ($details->order_status == 'rejected')
              <div class="row mb-2">
                <div class="col-lg-6">
                  <strong>{{ __('Refund Status') . ' :' }}</strong>
                </div>
                <div class="col-lg-6">
                  @if ($details->order_status == 'rejected')
                    @if ($details->refund == 'pending')
                      <span class="badge badge-success">{{ __('Pending') }}</span>
                    @else
                      <span class="badge badge-success">{{ __('Refunded') }}</span>
                    @endif
                  @endif
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Site Details') }}
          </div>
        </div>

        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Address') . ' :' }}</strong>
              </div>
              <div class="col-lg-8">
                {{ !empty($details->site_address) ? $details->site_address : $details->customer_address }}
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Country') . ' :' }}</strong>
              </div>
              <div class="col-lg-8">
                {{ !empty($details->site_country) ? $details->site_country : $details->customer_country }}
              </div>
            </div>

            @if (!empty($details->site_zip_code) || !empty($details->customer_zip_code))
              <div class="row mb-2">
                <div class="col-lg-4">
                  <strong>{{ __('Zip Code') . ' :' }}</strong>
                </div>
                <div class="col-lg-8">
                  {{ !empty($details->site_zip_code) ? $details->site_zip_code : $details->customer_zip_code }}
                </div>
              </div>
            @endif

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Name') . ' :' }}</strong>
              </div>
              <div class="col-lg-8">
                {{ !empty($details->site_name) ? $details->site_name : $details->customer_name }}
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Email') . ' :' }}</strong>
              </div>
              <div class="col-lg-8">
                {{ !empty($details->site_email) ? $details->site_email : $details->customer_email }}
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Phone') . ' :' }}</strong>
              </div>
              <div class="col-lg-8">
                {{ !empty($details->site_phone) ? $details->site_phone : $details->customer_phone }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="card-title d-inline-block">
            {{ __('Vendor Details') }}
          </div>
          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#vendorAssignModal">
            {{ $details->vendor_id != 0 ? __('Change Vendor') : __('Assign Vendor') }}
          </button>
        </div>
        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Name') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">
                @if ($details->vendor_id != 0)
                  {{ !empty($vendor_details->name) ? $vendor_details->name : $vendor_details->username }}
                @else
                  {{ !empty($vendor_details->first_name) ? $vendor_details->first_name : $vendor_details->username }}
                @endif
              </div>
            </div>

            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Email') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">
                {{ $vendor_details->email }}
              </div>
            </div>

            @if (!empty($vendor_details->phone))
              <div class="row mb-2">
                <div class="col-lg-4">
                  <strong>{{ __('Phone') . ' :' }}</strong>
                </div>

                <div class="col-lg-8">
                  {{ $vendor_details->phone }}
                </div>
              </div>
            @endif

            @if (!empty($vendor_details->address))
              <div class="row mb-2">
                <div class="col-lg-4">
                  <strong>{{ __('Address') . ' :' }}</strong>
                </div>

                <div class="col-lg-8">
                  {{ $vendor_details->address }}
                </div>
              </div>
            @endif


            @if (!empty($vendor_details->city))
              <div class="row mb-2">
                <div class="col-lg-4">
                  <strong>{{ __('City') . ' :' }}</strong>
                </div>

                <div class="col-lg-8">
                  {{ $vendor_details->city }}
                </div>
              </div>
            @endif

            @if (!empty($vendor_details->state))
              <div class="row mb-2">
                <div class="col-lg-4">
                  <strong>{{ __('State') . ' :' }}</strong>
                </div>

                <div class="col-lg-8">
                  {{ $vendor_details->state }}
                </div>
              </div>
            @endif

            @if (!empty($vendor_details->country))
              <div class="row mb-1">
                <div class="col-lg-4">
                  <strong>{{ __('Country') . ' :' }}</strong>
                </div>

                <div class="col-lg-8">
                  {{ $vendor_details->country }}
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="card-title d-inline-block">
            {{ __('Internal Employee') }}
          </div>
          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#employeeAssignModal">
            {{ $details->employeeJob && $details->employeeJob->employee ? __('Reassign') : __('Assign to Employee') }}
          </button>
        </div>
        <div class="card-body">
          @if ($details->employeeJob && $details->employeeJob->employee)

          @php
    $assignedEmployee = $details->employeeJob->employee;
@endphp
  <div class="payment-information">
              <div class="row mb-2">
                <div class="col-lg-4"><strong>{{ __('Name') . ' :' }}</strong></div>
                <div class="col-lg-8">{{ $assignedEmployee->full_name }}</div>
              </div>
              @if ($assignedEmployee->job_title)
                <div class="row mb-2">
                  <div class="col-lg-4"><strong>{{ __('Job Title') . ' :' }}</strong></div>
                  <div class="col-lg-8">{{ $assignedEmployee->job_title }}</div>
                </div>
              @endif
              @if ($assignedEmployee->loginEmail())
                <div class="row mb-2">
                  <div class="col-lg-4"><strong>{{ __('Email') . ' :' }}</strong></div>
                  <div class="col-lg-8">{{ $assignedEmployee->loginEmail() }}</div>
                </div>
              @endif
              @if ($assignedEmployee->phone)
                <div class="row mb-2">
                  <div class="col-lg-4"><strong>{{ __('Phone') . ' :' }}</strong></div>
                  <div class="col-lg-8">{{ $assignedEmployee->phone }}</div>
                </div>
              @endif
              <div class="row mb-1">
                <div class="col-lg-4"><strong>{{ __('HR Job Status') . ' :' }}</strong></div>
                <div class="col-lg-8">
                  <a href="{{ route('company.hr.jobs.show', $details->employeeJob) }}" target="_blank">
                    {{ ucfirst(str_replace('_', ' ', $details->employeeJob->status)) }} — {{ $details->employeeJob->job_reference }}
                  </a>
                </div>
              </div>
            </div>
            @if ($details->order_status == 'accepted')
              <div class="mt-3">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#adminCompleteEmployeeJobModal">
                  <i class="fas fa-check-circle"></i> {{ __('Complete Job') }}
                </button>
              </div>
            @endif
          @else
            <p class="text-muted mb-0">{{ __('Not assigned to an internal employee. Use the external vendor assignment above, or assign this job directly to one of your own employees.') }}</p>
          @endif
        </div>
      </div>
    </div>

    @if ($details->staff_id != null && $staff != null)
      @if ($staff->role == null)
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <div class="card-title d-inline-block">
                {{ __('Staff Details') }}
              </div>
            </div>
            <div class="card-body">
              <div class="payment-information">
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Name') . ' :' }}</strong>
                  </div>

                  <div class="col-lg-8">
                    {{ $staff->name ?? $staff->username }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Email') . ' :' }}</strong>
                  </div>

                  <div class="col-lg-8">
                    {{ $staff->email }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Phone') . ' :' }}</strong>
                  </div>

                  <div class="col-lg-8">
                    {{ $staff->phone }}
                  </div>
                </div>

                @if (!empty($staff->address))
                  <div class="row mb-2">
                    <div class="col-lg-4">
                      <strong>{{ __('Address') . ' :' }}</strong>
                    </div>

                    <div class="col-lg-8">
                      {{ $staff->address }}
                    </div>
                  </div>
                @endif

                @if (!empty($staff->information))
                  <div class="row mb-2">
                    <div class="col-lg-4">
                      <strong>{{ __('Information') . ' :' }}</strong>
                    </div>
                    <div class="col-lg-8">
                      <button class="btn btn-info btn-sm" data-toggle="modal"
                        data-target="#staffInfoModal">{{ __('Show') }}</button>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endif
    @endif
    @if (!empty($details->attachment))
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <div class="card-title d-inline-block">
              {{ __('Payment Receipt') }}
            </div>
          </div>
          <div class="card-body">
            <a href="{{ asset('assets/file/attachments/service/' . $details->attachment) }}" target="_blank" class="btn btn-info btn-sm">
              {{ __('View Attachment') }}
            </a>
          </div>
        </div>
      </div>
    @endif

    @php
      $appointmentAnswers = $appointmentAnswers ?? ($details->relationLoaded('answers') ? $details->answers : $details->answers()->with(['question.options', 'option'])->get());
    @endphp
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Job Details / Customer Answers') }}
          </div>
        </div>
        <div class="card-body">
          @if ($appointmentAnswers->count() > 0)
            @include('partials.appointment-customer-answers', ['answers' => $appointmentAnswers])
          @else
            <span class="text-muted">{{ __('No customer answers were saved for this appointment.') }}</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- reschedule modal -->
  @if ($details->order_status == 'pending')
    <div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form action="{{ route('admin.appointment.reschedule', ['id' => $details->id]) }}" method="post">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">{{ __('Change Date / Time') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @php
                $rsDate = $details->booking_date ? \Carbon\Carbon::parse($details->booking_date)->format('Y-m-d') : '';
                try { $rsStart = $details->start_date ? \Carbon\Carbon::parse($details->start_date)->format('H:i') : ''; } catch (\Exception $e) { $rsStart = ''; }
                try { $rsEnd = $details->end_date ? \Carbon\Carbon::parse($details->end_date)->format('H:i') : ''; } catch (\Exception $e) { $rsEnd = ''; }
              @endphp
              <div class="form-group">
                <label>{{ __('Appointment Date') . ' *' }}</label>
                <input type="date" name="booking_date" class="form-control" value="{{ old('booking_date', $rsDate) }}" min="{{ date('Y-m-d') }}" required>
                @error('booking_date')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>{{ __('Start Time') . ' *' }}</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $rsStart) }}" required>
                    @error('start_time')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group">
                    <label>{{ __('End Time') . ' *' }}</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $rsEnd) }}" required>
                    @error('end_time')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
                  </div>
                </div>
              </div>
              <small class="text-muted">{{ __('The customer will be notified of the new schedule by email.') }}</small>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
              <button type="submit" class="btn btn-primary btn-sm">{{ __('Save') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  <!-- vendor assign modal -->
  <div class="modal fade" id="vendorAssignModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Assign Vendor') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="vendorAssignForm" action="{{ route('admin.appointment.vendor_assign') }}" method="post">
            @csrf
            <input type="hidden" name="appointment_id" value="{{ $details->id }}">
            <input type="hidden" name="force" id="vendorAssignForce" value="0">
            <div class="form-group">
              <label>{{ __('Vendor') . ' *' }}</label>
              @php
                $eligibleIds = isset($eligibleVendorIds) && is_array($eligibleVendorIds) ? $eligibleVendorIds : null;
                if (is_array($eligibleIds) && $details->vendor_id && !in_array($details->vendor_id, $eligibleIds)) {
                    $eligibleIds[] = $details->vendor_id; // keep the currently-assigned vendor visible
                }
                $assignVendors = is_array($eligibleIds)
                    ? $vendors->whereIn('id', $eligibleIds)->values()
                    : $vendors;
              @endphp
              <select name="vendor_id" id="vendor_id_select" class="form-control select2">
                <option value="0" {{ $details->vendor_id == 0 ? 'selected' : '' }}>{{ __('None (Admin)') }}</option>
                @foreach ($assignVendors as $vendor)
                  @php $isBusy = in_array($vendor->id, $busyVendorIds ?? []); @endphp
                  <option value="{{ $vendor->id }}" data-busy="{{ $isBusy ? 1 : 0 }}"
                    {{ $details->vendor_id == $vendor->id ? 'selected' : '' }}>
                    {{ $vendor->vendor_infos->first() ? $vendor->vendor_infos->first()->name : $vendor->username }}
                    ({{ $vendor->email }}){{ $isBusy ? ' — ' . __('busy') : '' }}
                  </option>
                @endforeach
                @if ($assignVendors->isEmpty())
                  <option value="" disabled>{{ __('No matching vendors for this service category') }}</option>
                @endif
              </select>
              <p id="err_vendor_id" class="mt-1 mb-0 text-danger em"></p>
              <small class="text-muted">{{ __('Vendors marked busy are unavailable for this appointment slot.') }}</small>
            </div>
            <p class="text-muted small">{{ __('Note: Changing the vendor will also clear the assigned staff.') }}</p>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
          <button type="button" id="vendorAssignForceBtn" class="btn btn-warning btn-sm" style="display:none;">{{ __('Assign anyway') }}</button>
          <button type="button" id="vendorAssignBtn" class="btn btn-primary btn-sm">{{ __('Save') }}</button>
        </div>
      </div>
    </div>
  </div>

  <!-- internal employee assign modal -->
  <div class="modal fade" id="employeeAssignModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('Assign to Internal Employee') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="employeeAssignForm" action="{{ route('admin.appointment.employee_assign') }}" method="post">
            @csrf
            <input type="hidden" name="appointment_id" value="{{ $details->id }}">
            <div class="form-group">
              <label>{{ __('Employee') . ' *' }}</label>
              <select name="employee_id" id="employee_id_select" class="form-control select2">
                <option value="">{{ __('Select employee') }}</option>
                @foreach ($fieldEngineers as $engineer)
                  <option value="{{ $engineer->id }}"
                    {{ optional(optional($details->employeeJob)->employee)->id == $engineer->id ? 'selected' : '' }}>
                    {{ $engineer->full_name }}
                  </option>
                @endforeach
                @if ($fieldEngineers->isEmpty())
                  <option value="" disabled>{{ __('No active field engineers found in HR') }}</option>
                @endif
              </select>
              <p id="err_employee_id" class="mt-1 mb-0 text-danger em"></p>
            </div>
            <p class="text-muted small">
              {{ __('This does not change the existing vendor assignment. The employee will see this job in the Yes Trades staff app and it will appear in HR > Jobs.') }}
            </p>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
          <button type="button" id="employeeAssignBtn" class="btn btn-primary btn-sm">{{ __('Save') }}</button>
        </div>
      </div>
    </div>
  </div>

  @if ($details->employeeJob && $details->employeeJob->employee && $details->order_status == 'accepted')
    <div class="modal fade" id="adminCompleteEmployeeJobModal" tabindex="-1" role="dialog" aria-labelledby="adminCompleteEmployeeJobModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form action="{{ route('admin.appointment.employee_complete', ['id' => $details->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="adminCompleteEmployeeJobModalLabel">{{ __('Complete Job') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label><strong>{{ __('Completion Photos') }}</strong> <span class="text-danger">*</span></label>
                <input type="file" name="completion_images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple required>
                <small class="form-text text-muted">{{ __('1 to 10 photos. JPG, PNG or WEBP. Max 5 MB each.') }}</small>
                @error('completion_images')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
                @error('completion_images.*')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label><strong>{{ __('Job Certificate') }}</strong> <span class="text-danger">*</span></label>
                <input type="file" name="job_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                <small class="form-text text-muted">{{ __('PDF, JPG or PNG. Max 10 MB. Will be emailed to the customer.') }}</small>
                @error('job_certificate')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label><strong>{{ __('Certificate Expiry Date') }}</strong></label>
                <input type="date" name="job_certificate_expiry" class="form-control"
                  min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                  value="{{ old('job_certificate_expiry') }}">
                <small class="form-text text-muted">{{ __('Optional. If the certificate has an expiry date, enter it here — Yes Trades is emailed one month before. Leave blank if it does not expire or you do not have the date; the office will be shown it as missing.') }}</small>
                @error('job_certificate_expiry')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label>{{ __('Completion Notes') }}</label>
                <textarea name="completion_notes" class="form-control" rows="3" maxlength="5000">{{ old('completion_notes') }}</textarea>
                @error('completion_notes')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
              <button type="submit" class="btn btn-success btn-sm">{{ __('Mark as Completed') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
  @if ($details->staff_id != null && $staff != null)
  <!-- info modal -->
  <div class="modal fade" id="staffInfoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('Information') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @php
            $information = explode("\n", $staff->information);
          @endphp
          @if (count($information) > 0)
            <ul class="list-unstyled">
              @foreach ($information as $info)
                <li>{{ $info }}</li>
              @endforeach
            </ul>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  @endif
@endsection

@section('script')
  <script>
    function vendorOptionTemplate(option) {
      if (!option.id) {
        return option.text;
      }
      var isBusy = $(option.element).data('busy') == 1;
      if (isBusy) {
        return $('<span><i class="fas fa-exclamation-triangle text-warning mr-1"></i>' +
          $('<div>').text(option.text).html() + '</span>');
      }
      return option.text;
    }

    $('#vendor_id_select').select2({
      dropdownParent: $('#vendorAssignModal'),
      templateResult: vendorOptionTemplate,
      templateSelection: vendorOptionTemplate
    });

    function submitVendorAssign(force) {
      var form = $('#vendorAssignForm');
      $('#vendorAssignForce').val(force ? '1' : '0');
      $('#err_vendor_id').removeClass('text-warning').addClass('text-danger').text('');
      $('#vendorAssignBtn').attr('disabled', true);
      $('#vendorAssignForceBtn').attr('disabled', true);

      $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (response) {
          if (response.status === 'success') {
            location.reload();
          }
        },
        error: function (xhr) {
          $('#vendorAssignBtn').attr('disabled', false);
          $('#vendorAssignForceBtn').attr('disabled', false);
          var res = xhr.responseJSON || {};

          if (xhr.status === 409 && res.status === 'conflict') {
            $('#err_vendor_id').removeClass('text-danger').addClass('text-warning').text(res.message);
            $('#vendorAssignForceBtn').show();
            $('#vendorConflictAlert').removeClass('d-none')
              .html('<i class="fas fa-exclamation-triangle"></i> ' + $('<div>').text(res.message).html());
            return;
          }

          var errors = res.errors;
          if (errors && errors.vendor_id) {
            $('#err_vendor_id').text(errors.vendor_id[0]);
          }
        }
      });
    }

    $('#vendorAssignBtn').on('click', function () {
      submitVendorAssign(false);
    });

    $('#vendorAssignForceBtn').on('click', function () {
      submitVendorAssign(true);
    });

    // Reset override state whenever the selection changes.
    $('#vendor_id_select').on('change', function () {
      $('#vendorAssignForceBtn').hide();
      $('#vendorAssignForce').val('0');
      $('#err_vendor_id').text('');
      $('#vendorConflictAlert').addClass('d-none').empty();
    });

    $('#employee_id_select').select2({
      dropdownParent: $('#employeeAssignModal')
    });

    $('#employeeAssignBtn').on('click', function () {
      var form = $('#employeeAssignForm');
      $('#err_employee_id').text('');
      $('#employeeAssignBtn').attr('disabled', true);

      $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (response) {
          if (response.status === 'success') {
            var nextUrl = new URL(window.location.href);
            nextUrl.searchParams.set('complete_employee', '1');
            window.location.href = nextUrl.toString();
          }
        },
        error: function (xhr) {
          $('#employeeAssignBtn').attr('disabled', false);
          var res = xhr.responseJSON || {};
          var errors = res.errors;
          if (errors && errors.employee_id) {
            $('#err_employee_id').text(errors.employee_id[0]);
          }
        }
      });
    });

    @if (request()->input('complete_employee') && $details->employeeJob && $details->employeeJob->employee && $details->order_status == 'accepted')
      $('#adminCompleteEmployeeJobModal').modal('show');
    @endif

    @if ($errors->has('booking_date') || $errors->has('start_time') || $errors->has('end_time'))
      $('#rescheduleModal').modal('show');
    @endif
  </script>
@endsection
