@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')
@section('pageHeading')
  {{ __('Appointment Details') }}
@endsection
@section('style')
@endsection
@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => !empty($pageHeading) ? $pageHeading->orders_page_title : __('Appointment Details'),
  ])

  <!-- Dashboard-area start -->
  <div class="user-dashboard pt-100 pb-60">
    <div class="container">
      <div class="row gx-xl-5">
        <div class="col-lg-3">
          @includeIf('frontend.user.side-navbar')
        </div>
        <div class="col-lg-9">
          <div class="user-profile-details mb-40">
            <div class="order-details radius-md">
              <div class="title">
                <h4>{{ __('Appointment details') }}</h4>
              </div>
              <div class="view-order-page mb-40">
                <div class="order-info-area">
                  <div class="row align-items-center">
                    <div class="col-lg-8">
                      @php
                        if ($appointment->order_status == 'pending') {
                            $text_color = 'text-waring';
                        } elseif ($appointment->order_status == 'accepted') {
                            $text_color = 'text-success';
                        } elseif ($appointment->order_status == 'completed') {
                            $text_color = 'text-info';
                        } elseif ($appointment->order_status == 'rejected') {
                            $text_color = 'text-danger';
                        }
                      @endphp
                      <div class="order-info mb-20">
                        <h6>{{ __('Booking No.') }} {{ '#' . $appointment->order_number }} <span
                            class="{{ $text_color }}">
                            [{{ ucfirst(strtolower(__($appointment->order_status))) }}]

                          </span>
                        </h6>
                        <p class="m-0">{{ __('Booking Date') }}
                          {{ \Carbon\Carbon::parse($appointment->created_at)->isoFormat('Do MMMM YYYY') }}
                        </p>
                      </div>
                    </div>
                    @if (!is_null($appointment->invoice))
                      <div class="col-lg-4">
                        <div class="prinit mb-20">
                          <a href="{{ asset('assets/file/invoices/service/' . $appointment->invoice) }}" download
                            class="btn btn-md radius-sm"><i class="fas fa-print"></i>{{ __('Download Invoice') }}</a>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="billing-add-area mb-10">
                <div class="row">
                  <div class="col-md-6">
                    <div class="main-info mb-30">
                      <h5>{{ __('Billing Address') }}</h5>
                      <ul class="list">
                        <li><span>{{ __('Name') . ':' }}</span>{{ $appointment->customer_name }}</li>
                        <li><span>{{ __('Email') . ':' }}</span>{{ $appointment->customer_email }}</li>
                        <li><span>{{ __('Phone') . ':' }}</span>{{ $appointment->customer_phone }}</li>
                        <li><span>{{ __('Country') . ':' }}</span> {{ $appointment->customer_country }}</li>
                        <li><span>{{ __('Address') . ':' }}</span> {{ $appointment->customer_address }}</li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="main-info mb-30">
                      <h5>{{ __('Payment Information') }}</h5>
                      @php
                        if ($appointment->payment_status == 'pending') {
                            $payment_bg = 'bg-warning';
                        } elseif ($appointment->payment_status == 'completed') {
                            $payment_bg = 'bg-success';
                        } elseif ($appointment->payment_status == 'rejected') {
                            $payment_bg = 'bg-danger';
                        }
                        $symbol = $appointment->currency_symbol;
                        $position = $appointment->currency_symbol_position;
                      @endphp
                      <ul class="list">
                        <li>
                          <span>{{ __('Paid Amount') . ':' }}</span>
                          {{ $position == 'left' ? $symbol : '' }}{{ number_format($appointment->customer_paid, 2, '.', ',') }}{{ $position == 'right' ? $symbol : '' }}
                        </li>
                        <li>
                          <span>{{ __('Payment Method') . ':' }}</span>
                          {{ __($appointment->payment_method) }}
                        </li>
                        <li>
                          <span>{{ __('Payment Status') . ':' }}</span>
                          <span class="badge {{ $payment_bg }}">{{ __($appointment->payment_status) }}</span>
                        </li>
                        <li>
                          @php
                            if ($appointment->order_status == 'pending') {
                                $order_bg = 'bg-warning';
                            } elseif ($appointment->order_status == 'accepted') {
                                $order_bg = 'bg-success';
                            } elseif ($appointment->order_status == 'completed') {
                                $order_bg = 'bg-info';
                            } elseif ($appointment->order_status == 'rejected') {
                                $order_bg = 'bg-danger';
                            }
                          @endphp
                          <span>{{ __('Booking Status') . ':' }}</span>
                          <span class="badge {{ $order_bg }}">{{ __($appointment->order_status) }}</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  @if ($appointment->order_status === 'completed')
                    <div class="col-12">
                      <div class="main-info mb-30">
                        <h5>{{ __('Completion Evidence') }}</h5>
                        <p>{{ __('Completed At') }}:
                          {{ optional($appointment->completed_at)->format('M d, Y h:i A') ?? '-' }}</p>
                        <div class="row">
                          @foreach ($appointment->completionImages as $completionImage)
                            <div class="col-md-4 mb-3">
                              <a href="{{ asset('assets/img/appointment-completions/' . $completionImage->image) }}"
                                target="_blank">
                                <img src="{{ asset('assets/img/appointment-completions/' . $completionImage->image) }}"
                                  alt="{{ __('Completion Image') }}" class="img-fluid radius-sm">
                              </a>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  @endif

                  <div class="col-md-6">
                    <div class="main-info mb-30">
                      <h5>{{ __('Booking details') }}</h5>
                      @php
                        $content = $appointment->serviceContent->first();
                      @endphp
                      <ul class="list">
                        <li>
                          <span>{{ (($appointment->item_type ?? 'service') === 'package' ? __('Package') : __('Service Title')) . ': ' }}</span>
                          @if (($appointment->item_type ?? 'service') === 'package')
                            <p>{{ $appointment->package_name }}</p>
                          @elseif ($content)
                            <a href="{{ route('frontend.service.details', ['slug' => $content->slug, 'id' => $appointment->service->id]) }}"
                              target="_blank">
                              {{ truncateString($content->name, 38) }}
                            </a>
                          @endif
                        </li>
                        @if (($appointment->item_type ?? 'service') === 'package' && !empty($appointment->package_included_services))
                          <li>
                            <span>{{ __('Includes') . ': ' }}</span>
                            <p>{{ collect($appointment->package_included_services)->pluck('name')->filter()->implode(', ') }}</p>
                          </li>
                        @endif
                        <li>
                          <span>{{ __('Service Address') . ': ' }}</span>
                          <p>
                            {{ $appointment->site_address ?? $appointment->customer_address ?? optional($content)->address }}
                          </p>
                        </li>

                        @if ($appointment->zoom_info && $appointment->order_status == 'accepted')
                          @php
                            $zoom_link = json_decode($appointment->zoom_info, true);
                          @endphp
                          <li>
                            <span>{{ __('Meeting Url') . ':' }}</span>
                            <p>
                              <a target="_blank" href="{{ $zoom_link['join_url'] }}">
                                {{ truncateString($zoom_link['join_url'], 40) }}
                              </a>
                            </p>
                          </li>
                          <li>
                            <span>{{ __('Meeting Passcode') . ':' }}</span>
                            <p> {{ $zoom_link['password'] }}</p>
                          </li>
                        @endif
                        <li>
                          <span>{{ __('Appointment Date') . ':' }}</span>
                          {{ \Carbon\Carbon::parse($appointment->booking_date)->isoFormat('Do MMMM YYYY') }}
                        </li>
                        <li>
                          <span>{{ __('Appointment Time') . ':' }}</span>
                          <p>
                            {{ $appointment->start_date . ' - ' . $appointment->end_date }}
                          </p>
                        </li>

                        @if ($appointment->reschedule_status == 'requested')
                          <li>
                            <div class="alert alert-warning radius-sm" style="width:100%;">
                              <strong>{{ __('Date change requested') }}</strong> — {{ __('awaiting admin approval') }}<br>
                              {{ \Carbon\Carbon::parse($appointment->requested_booking_date)->isoFormat('Do MMMM YYYY') }}
                              ({{ \Carbon\Carbon::parse($appointment->requested_start_time)->format('g:i A') }}
                              - {{ \Carbon\Carbon::parse($appointment->requested_end_time)->format('g:i A') }})
                              <form action="{{ route('user.appointment.reschedule_cancel', ['id' => $appointment->id]) }}"
                                method="post" class="mt-2" onsubmit="return confirm('{{ __('Cancel this date change request?') }}');">
                                @csrf
                                <button type="submit" class="btn btn-sm radius-sm">{{ __('Cancel Request') }}</button>
                              </form>
                            </div>
                          </li>
                        @elseif ($appointment->order_status == 'pending')
                          @if ($appointment->reschedule_status == 'rejected')
                            <li>
                              <small class="text-danger">{{ __('Your previous date change request was rejected by the admin.') }}</small>
                            </li>
                          @endif
                          <li>
                            <details {{ $errors->any() ? 'open' : '' }}>
                              <summary style="cursor:pointer;color:var(--bs-primary,#0d6efd);font-weight:600;">
                                {{ __('Request Date / Time Change') }}
                              </summary>
                              <form action="{{ route('user.appointment.reschedule', ['id' => $appointment->id]) }}"
                                method="post" class="mt-3">
                                @csrf
                                @php
                                  $rsDate = $appointment->booking_date ? \Carbon\Carbon::parse($appointment->booking_date)->format('Y-m-d') : '';
                                  try { $rsStart = $appointment->start_date ? \Carbon\Carbon::parse($appointment->start_date)->format('H:i') : ''; } catch (\Exception $e) { $rsStart = ''; }
                                  try { $rsEnd = $appointment->end_date ? \Carbon\Carbon::parse($appointment->end_date)->format('H:i') : ''; } catch (\Exception $e) { $rsEnd = ''; }
                                @endphp
                                <div class="mb-2">
                                  <label>{{ __('Preferred Date') . ' *' }}</label>
                                  <input type="date" name="booking_date" class="form-control"
                                    value="{{ old('booking_date', $rsDate) }}" min="{{ date('Y-m-d') }}" required>
                                  @error('booking_date')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="row">
                                  <div class="col-6 mb-2">
                                    <label>{{ __('Start Time') . ' *' }}</label>
                                    <input type="time" name="start_time" class="form-control"
                                      value="{{ old('start_time', $rsStart) }}" required>
                                    @error('start_time')<small class="text-danger">{{ $message }}</small>@enderror
                                  </div>
                                  <div class="col-6 mb-2">
                                    <label>{{ __('End Time') . ' *' }}</label>
                                    <input type="time" name="end_time" class="form-control"
                                      value="{{ old('end_time', $rsEnd) }}" required>
                                    @error('end_time')<small class="text-danger">{{ $message }}</small>@enderror
                                  </div>
                                </div>
                                <small class="text-muted d-block mb-2">{{ __('Your request will be sent to the admin for approval.') }}</small>
                                <button type="submit" class="btn btn-md radius-sm mt-2">{{ __('Send Request') }}</button>
                              </form>
                            </details>
                          </li>
                        @endif

                        <li>
                          <span>
                            @if ($appointment->max_person > 1)
                              {{ __('Persons') . ':' }}
                            @else
                              {{ __('Person') . ':' }}
                            @endif
                          </span>
                          <p>
                            {{ $appointment->max_person }}
                          </p>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="main-info mb-30">
                      <h5>{{ __('Vendor Details') }}</h5>
                      <ul class="list">
                        <li><span>{{ __('Name') . ':' }}</span>
                          @if ($appointment->vendor_id != 0)
                            {{ !empty($vendor->name) ? $vendor->name : ($vendor->username ?? '') }}
                          @else
                            {{ !empty($vendor->first_name) ? $vendor->first_name : $vendor->username }}
                          @endif
                        </li>
                        @if ($vendor->show_email_addresss == 1)
                          <li><span>{{ __('Email') . ':' }}</span>{{ $vendor->email }}</li>
                        @endif
                        @if ($vendor->show_phone_number == 1 && !empty($vendor->phone))
                          <li><span>{{ __('Phone') . ':' }}</span>{{ $vendor->phone }}</li>
                        @endif
                        @if (!empty($vendor->country))
                          <li><span>{{ __('Country') . ':' }}</span> {{ $vendor->country }}</li>
                        @endif
                        @if (!empty($vendor->address))
                          <li><span>{{ __('Address') . ':' }}</span> {{ $vendor->address }}</li>
                        @endif
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Dashboard-area end -->
@endsection
