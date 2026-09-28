<div class="modal fade" id="completeAppointmentModal-{{ $item->id }}" tabindex="-1" role="dialog"
  aria-labelledby="completeAppointmentLabel-{{ $item->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('vendor.appointment.complete', ['id' => $item->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="completeAppointmentLabel-{{ $item->id }}">
            {{ __('Complete Appointment') }}
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>{{ __('Upload at least one image showing the completed work.') }}</p>
          <div class="form-group">
            <label>{{ __('Completion Images') }} *</label>
            <input type="file" name="completion_images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp"
              multiple required>
            <small class="form-text text-muted">
              {{ __('Maximum 10 images. JPG, PNG, or WEBP. Maximum 5 MB per image.') }}
            </small>
          </div>
          <div class="form-group">
            <label>{{ __('Job Certificate') }} *</label>
            <input type="file" name="job_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <small class="form-text text-muted">
              {{ __('PDF, JPG or PNG. Max 10 MB. The admin can view this from the job details.') }}
            </small>
          </div>
          <div class="form-group">
            <label><strong>{{ __('Certificate Expiry Date') }}</strong></label>
            <input type="date" name="job_certificate_expiry" class="form-control"
              min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
              value="{{ old('job_certificate_expiry') }}">
            <small class="form-text text-muted">{{ __('Optional. If the certificate has an expiry date, enter it here — Yes Trades is emailed one month before. Leave blank if it does not expire or you do not have the date; the office will be shown it as missing.') }}</small>
            @error('job_certificate_expiry')<p class="mt-1 mb-0 text-danger">{{ $message }}</p>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-success btn-sm">{{ __('Complete Appointment') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
