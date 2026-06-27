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
