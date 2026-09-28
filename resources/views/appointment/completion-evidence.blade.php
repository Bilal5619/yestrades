@if ($appointment->order_status === 'completed')
  <div class="row mb-2">
    <div class="col-lg-6">
      <strong>{{ __('Completed At') . ' :' }}</strong>
    </div>
    <div class="col-lg-6">
      {{ optional($appointment->completed_at)->format('M d, Y h:i A') ?? '-' }}
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-lg-6">
      <strong>{{ __('Completed By') . ' :' }}</strong>
    </div>
    <div class="col-lg-6">
      {{ ucfirst($appointment->completed_by_type ?? '-') }}
    </div>
  </div>

  @if ($appointment->job_certificate)
    <div class="row mb-2">
      <div class="col-lg-6">
        <strong>{{ __('Job Certificate') . ' :' }}</strong>
      </div>
      <div class="col-lg-6">
        <a href="{{ asset('assets/file/certificates/' . $appointment->job_certificate) }}" target="_blank"
          class="btn btn-xs btn-info">
          {{ __('View Certificate') }}
        </a>
      </div>
    </div>
  @endif

  @if ($appointment->job_certificate && Auth::guard('admin')->check())
    <div class="row mb-2">
      <div class="col-lg-6">
        <strong>{{ __('Certificate Expiry') . ' :' }}</strong>
      </div>
      <div class="col-lg-6">
        <form action="{{ route('admin.appointment.update_certificate_expiry', ['id' => $appointment->id]) }}"
          method="POST" class="form-inline">
          @csrf
          <input type="date" name="job_certificate_expiry" class="form-control form-control-sm mr-2"
            value="{{ optional($appointment->job_certificate_expiry)->format('Y-m-d') ?? ($appointment->job_certificate_expiry ?? '') }}">
          <button type="submit" class="btn btn-xs btn-primary">{{ __('Save') }}</button>
        </form>
        <small class="text-muted">
          {{ __('Yes Trades is emailed one month before this date, and it appears in Compliance & Tasks.') }}
        </small>
      </div>
    </div>
  @endif
  @if ($appointment->completion_invoice)
    <div class="row mb-2">
      <div class="col-lg-6">
        <strong>{{ __('Completion Invoice') . ' :' }}</strong>
      </div>
      <div class="col-lg-6">
        <a href="{{ asset('assets/file/completion-invoices/' . $appointment->completion_invoice) }}" target="_blank"
          class="btn btn-xs btn-info">
          {{ __('View Invoice') }}
        </a>
      </div>
    </div>
  @endif

  @if ($appointment->completionImages->isNotEmpty())
    <div class="row mt-3">
      <div class="col-12">
        <strong>{{ __('Completion Images') . ' :' }}</strong>
        <div class="row mt-2">
          @foreach ($appointment->completionImages as $completionImage)
            <div class="col-6 mb-3">
              <a href="{{ asset('assets/img/appointment-completions/' . $completionImage->image) }}" target="_blank">
                <img src="{{ asset('assets/img/appointment-completions/' . $completionImage->image) }}"
                  alt="{{ __('Completion Image') }}" class="img-fluid rounded">
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif
@endif
