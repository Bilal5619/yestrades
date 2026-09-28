@extends('vendors.layout')


@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Appointments Details') }}</h4>
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
        <a href="#">{{ __('All Appointments') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Details') }}</a>
      </li>
    </ul>
  </div>
  <div class="text-right mb-3">
    <a href="{{ route('vendor.all_appointment', ['language' => $defaultLang->code]) }}" class="btn btn-primary">{{ __('Back') }}</a>
  </div>

  <div class="row">
    @php
      $symbol = $details->currency_symbol;
      $symbol_positon = $details->currency_symbol_position;
    @endphp

    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Booking No.') . ' ' . '#' . $details->order_number }}
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
                {{ \Carbon\Carbon::parse($details->booking_date)->format('M d, Y') }}
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
            @if ($details->order_status == 'accepted')
              <div class="mt-3">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#completeJobModal">
                  <i class="fas fa-check-circle"></i> {{ __('Complete Appointment') }}
                </button>
              </div>
            @endif

            @if ($details->order_status == 'completed')
              {{-- Vendor invoice (structured) --}}
              @if ($details->vendorInvoice)
                @php $inv = $details->vendorInvoice; @endphp
                <hr>
                <div class="row mb-2">
                  <div class="col-lg-6"><strong>{{ __('Invoice') . ' :' }}</strong></div>
                  <div class="col-lg-6">
                    @if ($inv->invoice_file)
                      <a href="{{ asset('assets/file/invoices/vendor/' . $inv->invoice_file) }}" target="_blank" class="btn btn-xs btn-info">
                        {{ __('View Invoice') }}
                      </a>
                    @else
                      {{ __('No personal invoice uploaded') }}
                    @endif
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-6"><strong>{{ __('Invoice No') . ' :' }}</strong></div>
                  <div class="col-lg-6">{{ $inv->invoice_number }}</div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-6"><strong>{{ __('Invoice Status') . ' :' }}</strong></div>
                  <div class="col-lg-6">
                    @if ($inv->invoice_status == 'approved')
                      <span class="badge badge-success">{{ __('Approved') }}</span>
                    @elseif ($inv->invoice_status == 'rejected')
                      <span class="badge badge-danger">{{ __('Rejected') }}</span>
                    @else
                      <span class="badge badge-warning">{{ __('Submitted') }}</span>
                    @endif
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-6"><strong>{{ __('Payment') . ' :' }}</strong></div>
                  <div class="col-lg-6">
                    @if ($inv->payment_status == 'paid')
                      <span class="badge badge-success">{{ __('Paid') }}</span>
                    @else
                      <span class="badge badge-secondary">{{ __('Unpaid') }}</span>
                    @endif
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-6"><strong>{{ __('Paid Date') . ' :' }}</strong></div>
                  <div class="col-lg-6">
                    {{ $inv->paid_date ? $inv->paid_date->format('M d, Y') : '-' }}
                  </div>
                </div>
              @else
                <div class="mt-3">
                  <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadInvoiceModal-{{ $details->id }}">
                    <i class="fas fa-file-invoice"></i> {{ __('Upload Invoice') }}
                  </button>
                </div>
              @endif
            @endif
            @include('appointment.completion-evidence', ['appointment' => $details])
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

            @php
              // Destination = appointment site address (falls back to customer address).
              $directionDestination = implode(', ', array_filter([
                !empty($details->site_address) ? $details->site_address : $details->customer_address,
                !empty($details->site_zip_code) ? $details->site_zip_code : $details->customer_zip_code,
                !empty($details->site_country) ? $details->site_country : $details->customer_country,
              ]));

              // Origin = vendor address. If blank, Google Maps uses the device's current location.
              $directionOrigin = implode(', ', array_filter([
                $vendor_details->address ?? null,
                $vendor_details->city ?? null,
                $vendor_details->state ?? null,
                $vendor_details->zip_code ?? null,
                $vendor_details->country ?? null,
              ]));

              $directionsUrl = null;
              if (!empty($directionDestination)) {
                $directionsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($directionDestination);
                if (!empty($directionOrigin)) {
                  $directionsUrl .= '&origin=' . urlencode($directionOrigin);
                }
              }
            @endphp

            @if ($directionsUrl)
              <div class="row mb-2">
                <div class="col-lg-12">
                  <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-directions mr-1"></i> {{ __('Get Directions') }}
                  </a>
                  @if (empty($directionOrigin))
                    <small class="text-muted d-block mt-1">
                      {{ __('Route will start from your current location.') }}
                    </small>
                  @endif
                </div>
              </div>
            @endif

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
        <div class="card-header">
          <div class="card-title d-inline-block">
            {{ __('Vendor Details') }}
          </div>
        </div>

        <div class="card-body">
          <div class="payment-information">
            <div class="row mb-2">
              <div class="col-lg-4">
                <strong>{{ __('Name') . ' :' }}</strong>
              </div>

              <div class="col-lg-8">
                {{ $vendor_details->name ?? $vendor_details->username }}
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
    @if ($details->staff_id != null && $staff != null)
      @if ($staff->role != 'vendor')
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
  @if ($details->order_status == 'accepted')
    <div class="modal fade" id="completeJobModal" tabindex="-1" role="dialog" aria-labelledby="completeJobModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form id="completeJobForm" action="{{ route('vendor.appointment.complete', ['id' => $details->id]) }}"
            method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="completeJobModalLabel">{{ __('Complete Appointment') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              {{-- Completion Photos --}}
              <div class="form-group">
                <label><strong>{{ __('Completion Photos') }}</strong> <span class="text-danger">*</span></label>
                <input type="file" name="completion_images[]" id="cj_images" class="form-control"
                  accept=".jpg,.jpeg,.png,.webp" multiple>
                <small class="form-text text-muted">{{ __('1 – 10 photos. JPG, PNG or WEBP. Max 5 MB each.') }}</small>
                <div class="text-danger mt-1" id="cj_images_err"></div>
              </div>

              {{-- Job Certificate --}}
              <div class="form-group">
                <label><strong>{{ __('Job Certificate') }}</strong> <span class="text-danger">*</span></label>
                <input type="file" name="job_certificate" id="cj_cert" class="form-control"
                  accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text text-muted">{{ __('PDF, JPG or PNG. Max 10 MB. Will be emailed to the customer.') }}</small>
                <div class="text-danger mt-1" id="cj_cert_err"></div>
              </div>

              <div class="form-group">
                <label><strong>{{ __('Certificate Expiry Date') }}</strong></label>
                <input type="date" name="job_certificate_expiry" class="form-control"
                  min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                  value="{{ old('job_certificate_expiry', \Carbon\Carbon::today()->addYear()->format('Y-m-d')) }}">
                <small class="form-text text-muted">{{ __('Yes Trades is emailed one month before this date so the customer can be booked in for a renewal. Leave blank if the certificate never expires.') }}</small>
                @error('job_certificate_expiry')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
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

    <script>
      document.getElementById('completeJobForm').addEventListener('submit', function (e) {
        var valid = true;

        // clear previous errors
        ['cj_images_err', 'cj_cert_err'].forEach(function (id) {
          document.getElementById(id).textContent = '';
        });

        var imageExts  = ['jpg', 'jpeg', 'png', 'webp'];
        var docExts    = ['pdf', 'jpg', 'jpeg', 'png'];
        var MB5        = 5  * 1024 * 1024;
        var MB10       = 10 * 1024 * 1024;

        // --- Completion photos ---
        var imagesInput = document.getElementById('cj_images');
        var imagesErr   = document.getElementById('cj_images_err');
        if (!imagesInput.files.length) {
          imagesErr.textContent = '{{ __("Please upload at least one completion photo.") }}';
          valid = false;
        } else if (imagesInput.files.length > 10) {
          imagesErr.textContent = '{{ __("You can upload a maximum of 10 photos.") }}';
          valid = false;
        } else {
          for (var i = 0; i < imagesInput.files.length; i++) {
            var f = imagesInput.files[i];
            var ext = f.name.split('.').pop().toLowerCase();
            if (imageExts.indexOf(ext) === -1) {
              imagesErr.textContent = '{{ __("Only JPG, PNG and WEBP photos are allowed.") }}';
              valid = false; break;
            }
            if (f.size > MB5) {
              imagesErr.textContent = '{{ __("Each photo must be smaller than 5 MB.") }}';
              valid = false; break;
            }
          }
        }

        // --- Certificate ---
        var certInput = document.getElementById('cj_cert');
        var certErr   = document.getElementById('cj_cert_err');
        if (!certInput.files.length) {
          certErr.textContent = '{{ __("Please upload the job certificate.") }}';
          valid = false;
        } else {
          var cf = certInput.files[0];
          var cext = cf.name.split('.').pop().toLowerCase();
          if (docExts.indexOf(cext) === -1) {
            certErr.textContent = '{{ __("Only PDF, JPG and PNG files are allowed.") }}';
            valid = false;
          } else if (cf.size > MB10) {
            certErr.textContent = '{{ __("Certificate must be smaller than 10 MB.") }}';
            valid = false;
          }
        }

        if (!valid) { e.preventDefault(); }
      });
    </script>
  @endif

  @if ($details->order_status == 'completed' && !$details->vendorInvoice)
    @include('vendors.appointment.invoice-modal', [
        'item' => $details,
        'invoiceVendorName' => optional($vendor_details)->name ?? optional($vendor_details)->username,
    ])
  @endif

  @if ($details->staff_id != null && $staff != null && !empty($staff->information))
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
