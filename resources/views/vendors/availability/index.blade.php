@extends('vendors.layout')

@section('style')
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
  <style>
    #availabilityCalendar {
      max-width: 100%;
    }

    .weekly-day-row {
      align-items: center;
    }

    .fc-event {
      cursor: pointer;
    }
  </style>
@endsection

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('My Availability') }}</h4>
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
        <a href="#">{{ __('My Availability') }}</a>
      </li>
    </ul>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row">
    {{-- Weekly working hours --}}
    <div class="col-md-5">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">{{ __('Weekly Working Hours') }}</h4>
        </div>
        <div class="card-body">
          <p class="text-muted small">{{ __('Admins are warned before assigning you an appointment outside these hours.') }}</p>
          <form action="{{ route('vendor.availability.weekly.save') }}" method="post">
            @csrf
            @foreach ($days as $index => $name)
              @php $row = $weekly[$index]; @endphp
              <div class="form-group row weekly-day-row">
                <div class="col-4">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="checkbox" class="form-check-input weekly-open-toggle" name="is_open_{{ $index }}"
                        data-day="{{ $index }}" {{ $row['is_open'] ? 'checked' : '' }}>
                      {{ __($name) }}
                    </label>
                  </div>
                </div>
                <div class="col-4">
                  <input type="time" class="form-control weekly-time" data-day="{{ $index }}"
                    name="start_time_{{ $index }}" value="{{ $row['start_time'] }}"
                    {{ $row['is_open'] ? '' : 'disabled' }}>
                </div>
                <div class="col-4">
                  <input type="time" class="form-control weekly-time" data-day="{{ $index }}"
                    name="end_time_{{ $index }}" value="{{ $row['end_time'] }}"
                    {{ $row['is_open'] ? '' : 'disabled' }}>
                </div>
              </div>
            @endforeach
            <button type="submit" class="btn btn-primary btn-sm">{{ __('Save Working Hours') }}</button>
          </form>
        </div>
      </div>

      {{-- Blocked dates / exceptions --}}
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">{{ __('Block Specific Dates') }}</h4>
        </div>
        <div class="card-body">
          <form action="{{ route('vendor.availability.exception.store') }}" method="post" class="mb-3">
            @csrf
            <div class="form-group">
              <label>{{ __('Date') . ' *' }}</label>
              <input type="date" name="date" id="exceptionDate" class="form-control" required>
            </div>
            <div class="form-check mb-2">
              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" id="exceptionFullDay" name="is_full_day" checked>
                {{ __('Whole day') }}
              </label>
            </div>
            <div class="form-row exception-time-range" style="display:none;">
              <div class="col">
                <label>{{ __('From') }}</label>
                <input type="time" name="start_time" class="form-control">
              </div>
              <div class="col">
                <label>{{ __('To') }}</label>
                <input type="time" name="end_time" class="form-control">
              </div>
            </div>
            <div class="form-group mt-2">
              <label>{{ __('Reason') }}</label>
              <input type="text" name="reason" class="form-control" maxlength="191"
                placeholder="{{ __('e.g. Holiday, Personal leave') }}">
            </div>
            <button type="submit" class="btn btn-danger btn-sm">{{ __('Add Unavailable Date') }}</button>
          </form>

          @if ($exceptions->isEmpty())
            <p class="text-muted mb-0">{{ __('No blocked dates yet.') }}</p>
          @else
            <div class="table-responsive">
              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Time') }}</th>
                    <th>{{ __('Reason') }}</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($exceptions as $exception)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($exception->date)->format('M d, Y') }}</td>
                      <td>
                        @if ($exception->is_full_day || !$exception->start_time || !$exception->end_time)
                          {{ __('All day') }}
                        @else
                          {{ \Carbon\Carbon::parse($exception->start_time)->format('g:i A') }} -
                          {{ \Carbon\Carbon::parse($exception->end_time)->format('g:i A') }}
                        @endif
                      </td>
                      <td>{{ $exception->reason ?: '—' }}</td>
                      <td>
                        <form action="{{ route('vendor.availability.exception.delete', $exception->id) }}" method="post"
                          onsubmit="return confirm('{{ __('Remove this blocked date?') }}');">
                          @csrf
                          <button type="submit" class="btn btn-link text-danger p-0">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Calendar --}}
    <div class="col-md-7">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">{{ __('My Calendar') }}</h4>
          <p class="text-muted small mb-0">{{ __('Blue = appointments, red = your blocked dates. Click a day to block it.') }}</p>
        </div>
        <div class="card-body">
          <div id="availabilityCalendar"></div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script>
    $(function () {
      // Enable/disable time inputs with the day's open toggle.
      $('.weekly-open-toggle').on('change', function () {
        var day = $(this).data('day');
        $('.weekly-time[data-day="' + day + '"]').prop('disabled', !this.checked);
      });

      // Toggle the partial time range for a blocked date.
      $('#exceptionFullDay').on('change', function () {
        $('.exception-time-range').toggle(!this.checked);
      });

      var calendarEl = document.getElementById('availabilityCalendar');
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
            url: '{{ route('vendor.availability.events') }}',
            data: { start: info.startStr, end: info.endStr },
            success: successCallback,
            error: failureCallback
          });
        },
        dateClick: function (info) {
          // Prefill the block-date form with the clicked day.
          $('#exceptionDate').val(info.dateStr);
          $('html, body').animate({ scrollTop: $('#exceptionDate').offset().top - 120 }, 300);
          $('#exceptionDate').focus();
        }
      });

      calendar.render();
    });
  </script>
@endsection
