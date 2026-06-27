@extends('admin.layout')

@section('style')
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
  <style>
    .vendor-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 15px;
    }

    .vendor-legend .legend-item {
      display: inline-flex;
      align-items: center;
      font-size: 13px;
    }

    .vendor-legend .legend-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }

    #vendorCalendar {
      max-width: 100%;
    }

    .fc-event {
      cursor: pointer;
    }
  </style>
@endsection

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Vendor Calendar') }}</h4>
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
        <a href="#">{{ __('Appointments') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Vendor Calendar') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-end">
            <div class="col-lg-4">
              <div class="form-group mb-0">
                <label>{{ __('Filter by Vendor') }}</label>
                <select id="calendarVendorFilter" class="form-control">
                  <option value="">{{ __('All Vendors') }}</option>
                  <option value="0">{{ __('Admin (Unassigned)') }}</option>
                  @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}">
                      {{ $vendor->vendor_infos->first() ? $vendor->vendor_infos->first()->name : $vendor->username }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="vendor-legend">
            <span class="legend-item"><span class="legend-dot" style="background:#6c757d"></span>{{ __('Admin (Unassigned)') }}</span>
            @foreach ($vendors as $vendor)
              <span class="legend-item">
                <span class="legend-dot" style="background: {{ ['#4e73df', '#1cc88a', '#e74a3b', '#f6c23e', '#36b9cc', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#17a2b8'][$vendor->id % 10] }}"></span>
                {{ $vendor->vendor_infos->first() ? $vendor->vendor_infos->first()->name : $vendor->username }}
              </span>
            @endforeach
          </div>
          <div id="vendorCalendar"></div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('vendorCalendar');
      var vendorFilter = document.getElementById('calendarVendorFilter');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: 'short' },
        events: function (info, successCallback, failureCallback) {
          $.ajax({
            url: '{{ route('admin.appointment.calendar_events') }}',
            data: {
              start: info.startStr,
              end: info.endStr,
              vendor_id: vendorFilter.value
            },
            success: function (data) {
              successCallback(data);
            },
            error: failureCallback
          });
        },
        eventDidMount: function (arg) {
          var p = arg.event.extendedProps;
          arg.el.setAttribute('title',
            (p.vendor ? '{{ __('Vendor') }}: ' + p.vendor + '\n' : '') +
            (p.customer ? '{{ __('Customer') }}: ' + p.customer + '\n' : '') +
            (p.status ? '{{ __('Status') }}: ' + p.status : '')
          );
        }
      });

      calendar.render();

      vendorFilter.addEventListener('change', function () {
        calendar.refetchEvents();
      });
    });
  </script>
@endsection
