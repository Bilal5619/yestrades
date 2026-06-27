@extends('vendors.layout')

@section('content')
  @php
    $vendor = Auth::guard('vendor')->user();
    $package = \App\Http\Helpers\VendorPermissionHelper::currentPackagePermission($vendor->id);
  @endphp

  <div class="vendor-dashboard">
    <div class="vendor-dashboard__header">
      <div>
        <span class="vendor-dashboard__eyebrow">{{ __('Vendor Dashboard') }}</span>
        <h1>{{ __('Welcome back,') }} {{ $vendor->username }}!</h1>
      </div>
    </div>

    @if ($vendor->status == 0 && $admin_setting->vendor_admin_approval == 1)
      @if ($vendor->verification_status === 'rejected')
        <div class="vendor-notice vendor-notice--danger">
          <i class="far fa-exclamation-circle"></i>
          <span>
            {{ __('Your verification was rejected.') }}
            @if (!empty($vendor->verification_rejection_reason))
              {{ __('Reason') }}: {{ $vendor->verification_rejection_reason }}.
            @endif
            {{ __('Please re-upload the required documents and submit again. If you need help, contact the admin.') }}
            <a href="{{ route('vendor.account_setup') }}">{{ __('Go to Account Setup Tasks') }}</a>
          </span>
        </div>
      @elseif ($vendor->verification_status === 'submitted')
        <div class="vendor-notice vendor-notice--warning">
          <i class="far fa-clock"></i>
          <span>{{ __('Waiting for approval. Your verification has been submitted successfully. Approval can take up to 24 hours.') }}</span>
        </div>
      @else
        <div class="vendor-notice vendor-notice--warning">
          <i class="far fa-exclamation-circle"></i>
          <span>
            {{ __('Your account is pending approval. Please complete your account setup tasks to get verified.') }}
            <a href="{{ route('vendor.account_setup') }}">{{ __('Go to Account Setup Tasks') }}</a>
          </span>
        </div>
      @endif

      <div class="card mt-3">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Provide Your Registration Numbers') }}</div>
        </div>
        <div class="card-body">
          <p class="text-muted mb-3">{{ __('To speed up your account approval, please provide your professional registration or licence numbers below. Add a title so the admin knows what each number refers to (e.g. "Gas Safe Registration", "Electrical Competent Person Scheme").') }}</p>

          <form action="{{ route('vendor.business_type.update') }}" method="POST" class="mb-4">
            @csrf
            <div class="row">
              <div class="col-md-8">
                <div class="form-group mb-md-0">
                  <label>{{ __('What type of business is it?') }} <span class="text-danger">*</span></label>
                  <select name="business_type" class="form-control" required>
                    <option value="">{{ __('Select business type') }}</option>
                    <option value="sole_trader" @selected(old('business_type', $vendor->business_type) === 'sole_trader')>
                      {{ __('Sole Trader') }}
                    </option>
                    <option value="limited_company" @selected(old('business_type', $vendor->business_type) === 'limited_company')>
                      {{ __('Limited Company') }}
                    </option>
                  </select>
                  @error('business_type')
                    <p class="text-danger mt-2">{{ $message }}</p>
                  @enderror
                </div>
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <div class="form-group w-100 mb-0">
                  <button type="submit" class="btn btn-primary w-100">{{ __('Save Business Type') }}</button>
                </div>
              </div>
            </div>
          </form>

          @if ($registrationNumbers->count() > 0)
            <table class="table table-bordered table-sm mb-3">
              <thead>
                <tr>
                  <th>{{ __('Title / Type') }}</th>
                  <th>{{ __('Registration Number') }}</th>
                  <th>{{ __('Authority') }}</th>
                  <th>{{ __('Expiry') }}</th>
                  <th style="width:80px;">{{ __('Remove') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($registrationNumbers as $regNum)
                  <tr>
                    <td>{{ $regNum->title }}</td>
                    <td>{{ $regNum->registration_number ?: $regNum->number }}</td>
                    <td>{{ $regNum->registration_authority ?: '-' }}</td>
                    <td>{{ $regNum->expiry_date ? $regNum->expiry_date->format('d M Y') : '-' }}</td>
                    <td class="text-center">
                      <form action="{{ route('vendor.registration_numbers.delete', $regNum->id) }}" method="POST"
                        onsubmit="return confirm('{{ __('Remove this registration number?') }}')">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-danger"><i class="far fa-trash-alt"></i></button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif

          <form action="{{ route('vendor.registration_numbers.store') }}" method="POST">
            @csrf
            @if ($errors->any())
              <div class="alert alert-danger py-2">
                @foreach ($errors->all() as $error)
                  <div>{{ $error }}</div>
                @endforeach
              </div>
            @endif
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>{{ __('Title / Type') }} <span class="text-danger">*</span></label>
                  <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                    placeholder="{{ __('e.g. Gas Safe Registration') }}" required maxlength="255">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>{{ __('Registration Number') }} <span class="text-danger">*</span></label>
                  <input type="text" name="number" value="{{ old('number') }}" class="form-control"
                    placeholder="{{ __('e.g. 123456') }}" required maxlength="255">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>{{ __('Registration Authority') }}</label>
                  <input type="text" name="registration_authority" value="{{ old('registration_authority') }}" class="form-control"
                    placeholder="{{ __('e.g. Gas Safe Register') }}" maxlength="255">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>{{ __('Expiry Date') }}</label>
                  <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-control">
                </div>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <div class="form-group w-100">
                  <button type="submit" class="btn btn-primary w-100">{{ __('Add') }}</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    @endif

    @if (is_null($package))
      @php
        $pendingMemb = \App\Models\Membership::query()
            ->where([['vendor_id', '=', Auth::id()], ['status', 0]])
            ->whereYear('start_date', '<>', '9999')
            ->orderBy('id', 'DESC')
            ->first();
        $pendingPackage = isset($pendingMemb) ? \App\Models\Package::query()->findOrFail($pendingMemb->package_id) : null;
      @endphp

      @if ($pendingPackage)
        <div class="vendor-notice vendor-notice--warning">
          <i class="far fa-clock"></i>
          <div>
            <strong>{{ __('Pending Package') }}: {{ $pendingPackage->title }}</strong>
            <p>{{ __('You have requested a package which needs an action (Approval / Rejection) by Admin. You will be notified via mail once an action is taken.') }}</p>
            <span class="vendor-status vendor-status--warning">{{ $pendingPackage->term }}</span>
            <span class="vendor-status vendor-status--warning">{{ __('Decision Pending') }}</span>
          </div>
        </div>
      @endif
    @endif

    @if (!empty($pendingAssignedJobsCount))
      <a class="vendor-job-alert vendor-job-alert--pulse"
        href="{{ route('vendor.pending_appointment', ['language' => $defaultLang->code]) }}">
        <span class="vendor-job-alert__icon"><i class="far fa-bell"></i></span>
        <span>
          <strong>{{ $pendingAssignedJobsCount }} {{ __('pending jobs assigned') }}</strong>
          <small>{{ __('Review the new jobs and accept or reject them.') }}</small>
        </span>
        <span class="vendor-job-alert__action">{{ __('View Jobs') }}</span>
      </a>
    @endif

    <div class="vendor-metrics">
      <a class="vendor-metric vendor-metric--earning" href="{{ route('vendor.transaction') }}">
        <span class="vendor-metric__icon"><i class="fas fa-money-check-alt"></i></span>
        <span class="vendor-metric__label">{{ __('Total Earned') }}</span>
        <strong>{{ symbolPrice($totalEarned) }}</strong>
        <span class="vendor-metric__arrow"><i class="far fa-arrow-right"></i></span>
      </a>

      <a class="vendor-metric vendor-metric--services"
        href="{{ route('vendor.service_managment', ['language' => $defaultLang->code]) }}">
        <span class="vendor-metric__icon"><i class="fas fa-wrench"></i></span>
        <span class="vendor-metric__label">{{ __('Services') }}</span>
        <strong>{{ $totalServices }}</strong>
        <span class="vendor-metric__arrow"><i class="far fa-arrow-right"></i></span>
      </a>

      <a class="vendor-metric vendor-metric--appointments"
        href="{{ route('vendor.all_appointment', ['language' => $defaultLang->code]) }}">
        <span class="vendor-metric__icon"><i class="far fa-calendar"></i></span>
        <span class="vendor-metric__label">{{ __('All Appointments') }}</span>
        <strong>{{ $totalAppointment }}</strong>
        <span class="vendor-metric__arrow"><i class="far fa-arrow-right"></i></span>
      </a>

      <a class="vendor-metric vendor-metric--pending @if (!empty($pendingAssignedJobsCount)) vendor-metric--attention @endif"
        href="{{ route('vendor.pending_appointment', ['language' => $defaultLang->code]) }}">
        <span class="vendor-metric__icon"><i class="far fa-clock"></i></span>
        <span class="vendor-metric__label">
          {{ __('Pending Appointments') }}
          @if (!empty($pendingAssignedJobsCount))
            <span class="vendor-inline-badge vendor-pulse-badge">{{ __('New') }}</span>
          @endif
        </span>
        <strong>{{ $totalPendingAppointment }}</strong>
        <span class="vendor-metric__arrow"><i class="far fa-arrow-right"></i></span>
      </a>

      <a class="vendor-metric vendor-metric--completed"
        href="{{ route('vendor.completed_appointment', ['language' => $defaultLang->code]) }}">
        <span class="vendor-metric__icon"><i class="far fa-check-circle"></i></span>
        <span class="vendor-metric__label">{{ __('Completed Appointments') }}</span>
        <strong>{{ $totalCompleteAppointment }}</strong>
        <span class="vendor-metric__arrow"><i class="far fa-arrow-right"></i></span>
      </a>

      <a class="vendor-metric vendor-metric--rejected"
        href="{{ route('vendor.rejected_appointment', ['language' => $defaultLang->code]) }}">
        <span class="vendor-metric__icon"><i class="far fa-times-circle"></i></span>
        <span class="vendor-metric__label">{{ __('Rejected Appointments') }}</span>
        <strong>{{ $totalRejectedAppointment }}</strong>
        <span class="vendor-metric__arrow"><i class="far fa-arrow-right"></i></span>
      </a>
    </div>

    @if (!empty($recentAppointments) && $recentAppointments->count() > 0)
      <section class="vendor-appointments">
        <div class="vendor-appointments__header">
          <div>
            <span>{{ __('Appointments') }}</span>
            <h2>{{ __('Recent Appointments / Jobs') }}</h2>
          </div>
          @if ($totalPendingAppointment > 0)
            <span class="vendor-status vendor-status--warning">{{ $totalPendingAppointment }} {{ __('New') }}</span>
          @endif
        </div>

        <div class="vendor-appointments__table">
          <table>
            <thead>
              <tr>
                <th>{{ __('Booking ID') }}</th>
                <th>{{ __('Service') }}</th>
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Location') }}</th>
                <th>{{ __('Appointment') }}</th>
                <th>{{ __('Payment') }}</th>
                <th>{{ __('Order') }}</th>
                <th><span class="sr-only">{{ __('Action') }}</span></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($recentAppointments as $appointment)
                <tr>
                  <td><span class="vendor-booking-id">{{ '#' . $appointment->order_number }}</span></td>
                  <td>
                    @if ($appointment->serviceContent->isNotEmpty())
                      <a class="vendor-service-link"
                        href="{{ route('vendor.appointment.details', ['id' => $appointment->id]) }}">
                        {{ truncateString($appointment->serviceContent->first()->name, 35) }}
                      </a>
                    @else
                      {{ '-' }}
                    @endif
                  </td>
                  <td>{{ $appointment->customer_email ?: ($appointment->customer_name ?: '-') }}</td>
                  <td>{{ truncateString((!empty($appointment->site_address) ? $appointment->site_address : $appointment->customer_address) ?: '-', 35) }}</td>
                  <td>
                    <span class="vendor-appointment-date">
                      {{ \Carbon\Carbon::parse($appointment->booking_date)->format('M d, Y') }}
                    </span>
                    <small>{{ $appointment->start_date }} - {{ $appointment->end_date }}</small>
                  </td>
                  <td>
                    <span class="vendor-status vendor-status--{{ $appointment->payment_status == 'completed' ? 'success' : ($appointment->payment_status == 'pending' ? 'warning' : 'danger') }}">
                      {{ ucfirst($appointment->payment_status) }}
                    </span>
                  </td>
                  <td>
                    <span class="vendor-status vendor-status--{{ in_array($appointment->order_status, ['accepted', 'completed']) ? 'success' : ($appointment->order_status == 'pending' ? 'warning' : 'danger') }}">
                      {{ ucfirst($appointment->order_status) }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('vendor.appointment.details', ['id' => $appointment->id]) }}"
                      class="vendor-details-link" title="{{ __('Details') }}">
                      <i class="far fa-arrow-right"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </section>
    @endif
  </div>
@endsection
