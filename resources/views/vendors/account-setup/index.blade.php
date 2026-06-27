@extends('vendors.layout')

@section('content')
  @php
    $statusBadge = function ($status) {
        $map = [
            'completed' => ['success', 'Completed'],
            'approved' => ['success', 'Approved'],
            'under_review' => ['info', 'Under Review'],
            'rejected' => ['danger', 'Rejected'],
            'pending' => ['warning', 'Pending'],
            'optional' => ['secondary', 'Optional'],
        ];
        [$class, $label] = $map[$status] ?? ['secondary', ucfirst($status)];
        return '<span class="badge badge-' . $class . '">' . __($label) . '</span>';
    };
  @endphp

  <div class="page-inner">
    <div class="page-header">
      <h4 class="page-title">{{ __('Account Setup Tasks') }}</h4>
    </div>

    {{-- Verification status banner --}}
    @if ($vendor->verification_status === 'rejected')
      <div class="alert alert-danger">
        <strong>{{ __('Your verification was rejected.') }}</strong>
        @if (!empty($vendor->verification_rejection_reason))
          <div class="mt-1">{{ __('Reason') }}: {{ $vendor->verification_rejection_reason }}</div>
        @endif
        <div class="mt-1">{{ __('Please fix the issues below, re-upload any rejected documents, and submit again. If you need help, contact the admin.') }}</div>
      </div>
    @elseif ($vendor->verification_status === 'submitted')
      <div class="alert alert-info">
        {{ __('Waiting for approval. Your verification has been submitted successfully. Approval can take up to 24 hours.') }}
      </div>
    @elseif ($vendor->verification_status === 'approved' || (int) $vendor->status === 1)
      <div class="alert alert-success">
        {{ __('Your account is approved. You can now receive and accept jobs.') }}
      </div>
    @else
      <div class="alert alert-warning">
        {{ __('Complete the tasks below and submit your verification to get your account approved.') }}
      </div>
    @endif

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">{{ __('Setup Checklist') }}</div>
          </div>
          <div class="card-body">
            <ul class="list-group">
              @foreach ($tasks as $task)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>{{ __($task['label']) }}</span>
                  {!! $statusBadge($task['status']) !!}
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>

    {{-- Optional accreditation / certification --}}
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">{{ __('Accreditation Certification') }}</div>
            {!! $statusBadge($accreditations->isNotEmpty() ? 'completed' : 'optional') !!}
          </div>
          <div class="card-body">
            @if ($accreditations->isNotEmpty())
              <table class="table table-bordered table-sm mb-3">
                <thead>
                  <tr>
                    <th>{{ __('Registration Number') }}</th>
                    <th>{{ __('Registration Authority') }}</th>
                    <th>{{ __('Expiry Date') }}</th>
                    <th style="width:80px;">{{ __('Remove') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($accreditations as $accreditation)
                    <tr>
                      <td>{{ $accreditation->registration_number ?: $accreditation->number }}</td>
                      <td>{{ $accreditation->registration_authority ?: '-' }}</td>
                      <td>{{ $accreditation->expiry_date ? $accreditation->expiry_date->format('d M Y') : '-' }}</td>
                      <td class="text-center">
                        <form action="{{ route('vendor.registration_numbers.delete', $accreditation->id) }}" method="POST"
                          onsubmit="return confirm('{{ __('Remove this accreditation certification?') }}')">
                          @csrf
                          <button type="submit" class="btn btn-xs btn-danger"><i class="far fa-trash-alt"></i></button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p class="text-muted mb-3">{{ __('Optional: add any accreditation, certification, licence or professional registration that may help the admin review your account.') }}</p>
            @endif

            <form action="{{ route('vendor.registration_numbers.store') }}" method="POST">
              @csrf
              <input type="hidden" name="category" value="accreditation">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>{{ __('Registration Number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="registration_number" value="{{ old('registration_number') }}" class="form-control" required maxlength="255">
                    @error('registration_number') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>{{ __('Registration Authority') }}</label>
                    <input type="text" name="registration_authority" value="{{ old('registration_authority') }}" class="form-control" maxlength="255">
                    @error('registration_authority') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>{{ __('Expiry Date') }}</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-control">
                    @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
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
      </div>
    </div>

    {{-- Document uploads --}}
    <div class="row">
      {{-- Public Liability Insurance --}}
      @php $insurance = $documents->get('insurance'); @endphp
      <div class="col-md-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">{{ __('Public Liability Insurance') }}</div>
            @if ($insurance) {!! $statusBadge($insurance->status) !!} @endif
          </div>
          <div class="card-body">
            @include('vendors.account-setup.partials.doc-status', ['doc' => $insurance])
            @if (!$insurance || !$insurance->is_pending_review)
              <form action="{{ route('vendor.account_setup.documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="insurance">
                <div class="form-group">
                  <label>{{ __('Document') }} (PDF/JPG/PNG) <span class="text-danger">*</span></label>
                  <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ (!$insurance || $insurance->is_rejected || $insurance->is_approved) ? 'required' : '' }}>
                  @error('file') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                  <label>{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
                  <input type="date" name="expiry_date" class="form-control"
                    value="{{ old('expiry_date', optional(optional($insurance)->expiry_date)->format('Y-m-d')) }}" required>
                  @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm">{{ $insurance ? __('Upload New Version') : __('Upload') }}</button>
              </form>
            @endif
          </div>
        </div>
      </div>

      {{-- Driving Licence or Passport --}}
      @php $idDoc = $documents->get('id_document'); @endphp
      <div class="col-md-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">{{ __('Driving Licence or Passport') }}</div>
            @if ($idDoc) {!! $statusBadge($idDoc->status) !!} @endif
          </div>
          <div class="card-body">
            @include('vendors.account-setup.partials.doc-status', ['doc' => $idDoc])
            @if (!$idDoc || !$idDoc->is_pending_review)
              <form action="{{ route('vendor.account_setup.documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="id_document">
                <div class="form-group">
                  <label>{{ __('Document Type') }} <span class="text-danger">*</span></label>
                  <select name="id_type" class="form-control" required>
                    <option value="driving_licence" @selected(optional($idDoc)->id_type === 'driving_licence')>{{ __('Driving Licence') }}</option>
                    <option value="passport" @selected(optional($idDoc)->id_type === 'passport')>{{ __('Passport') }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>{{ __('Document') }} (PDF/JPG/PNG) <span class="text-danger">*</span></label>
                  <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ (!$idDoc || $idDoc->is_rejected || $idDoc->is_approved) ? 'required' : '' }}>
                  @error('file') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                  <label>{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
                  <input type="date" name="expiry_date" class="form-control"
                    value="{{ old('expiry_date', optional(optional($idDoc)->expiry_date)->format('Y-m-d')) }}" required>
                  @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm">{{ $idDoc ? __('Upload New Version') : __('Upload') }}</button>
              </form>
            @endif
          </div>
        </div>
      </div>

      {{-- Right to Work if not British --}}
      @php
        $rtw = $documents->get('right_to_work');
        $isBritishRightToWork = (bool) $vendor->right_to_work_is_british;
      @endphp
      <div class="col-md-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">{{ __('Right to Work if not British') }}</div>
            @if ($isBritishRightToWork)
              {!! $statusBadge('completed') !!}
            @elseif ($rtw)
              {!! $statusBadge($rtw->status) !!}
            @endif
          </div>
          <div class="card-body">
            @if ($isBritishRightToWork)
              <p class="text-success">{{ __('You confirmed you are British, so a Right to Work upload is not required.') }}</p>
            @else
              @include('vendors.account-setup.partials.doc-status', ['doc' => $rtw])
            @endif
            @if ($isBritishRightToWork || !$rtw || !$rtw->is_pending_review)
              <form action="{{ route('vendor.account_setup.documents.store') }}" method="POST" enctype="multipart/form-data" id="rightToWorkForm">
                @csrf
                <input type="hidden" name="type" value="right_to_work">
                <div class="form-group">
                  <label class="d-flex align-items-center mb-2" style="gap: 8px;">
                    <input type="checkbox" name="right_to_work_is_british" value="1" id="rightToWorkBritish"
                      @checked(old('right_to_work_is_british', $isBritishRightToWork))>
                    <span>{{ __('I am British') }}</span>
                  </label>
                  <small class="text-muted">{{ __('Tick this if you are British and do not need to upload Right to Work if not British evidence.') }}</small>
                </div>
                <div class="form-group">
                  <label>{{ __('Document') }} (PDF/JPG/PNG) <span class="text-danger">*</span></label>
                  <small class="form-text text-muted mb-2">{{ __('Note: share code to prove Right to Work.') }}</small>
                  <input type="file" name="file" class="form-control" id="rightToWorkFile" accept=".pdf,.jpg,.jpeg,.png"
                    {{ (!$rtw || $rtw->is_rejected || $rtw->is_approved) ? 'data-original-required=1' : '' }}
                    {{ (!$rtw || $rtw->is_rejected || $rtw->is_approved) && !$isBritishRightToWork ? 'required' : '' }}>
                  @error('file') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="form-group right-to-work-evidence-field">
                  <label>{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
                  <input type="date" name="expiry_date" class="form-control" id="rightToWorkExpiryDate"
                    value="{{ old('expiry_date', optional(optional($rtw)->expiry_date)->format('Y-m-d')) }}"
                    {{ !$isBritishRightToWork ? 'required' : '' }}>
                  @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="form-group right-to-work-evidence-field">
                  <label>{{ __('Date of Birth') }} <span class="text-danger">*</span></label>
                  <input type="date" name="date_of_birth" class="form-control" id="rightToWorkDateOfBirth"
                    value="{{ old('date_of_birth', optional(optional($rtw)->date_of_birth)->format('Y-m-d')) }}"
                    {{ !$isBritishRightToWork ? 'required' : '' }}>
                  @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm" id="rightToWorkSubmit">{{ $rtw && !$isBritishRightToWork ? __('Re-upload') : __('Save') }}</button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Submit verification --}}
    @if (!in_array($vendor->verification_status, ['submitted', 'approved'], true) && (int) $vendor->status !== 1)
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body text-center">
              <form action="{{ route('vendor.account_setup.submit') }}" method="POST" id="verificationSubmitForm">
                @csrf
                <button type="submit" class="btn btn-success" id="verificationSubmitButton" {{ $canSubmit ? '' : 'disabled' }}>
                  {{ __('Submit Verification') }}
                </button>
                @unless ($canSubmit)
                  <div class="text-muted mt-2">
                    {{ __('Complete all required tasks (categories, postcode, insurance, ID and Right to Work if not British) to enable submission.') }}
                  </div>
                @endunless
              </form>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>

  <script>
    (function () {
      var britishCheckbox = document.getElementById('rightToWorkBritish');
      var fileInput = document.getElementById('rightToWorkFile');
      var expiryInput = document.getElementById('rightToWorkExpiryDate');
      var dateOfBirthInput = document.getElementById('rightToWorkDateOfBirth');
      var submitButton = document.getElementById('rightToWorkSubmit');
      var evidenceFields = document.querySelectorAll('.right-to-work-evidence-field');

      if (!britishCheckbox || !fileInput) {
        return;
      }

      var toggleEvidenceFields = function (disabled) {
        evidenceFields.forEach(function (field) {
          field.style.display = disabled ? 'none' : '';
        });

        [expiryInput, dateOfBirthInput].forEach(function (input) {
          if (!input) {
            return;
          }
          input.disabled = disabled;
          input.required = !disabled;
        });
      };

      var updateRightToWorkUpload = function () {
        if (britishCheckbox.checked) {
          fileInput.value = '';
          fileInput.disabled = true;
          fileInput.required = false;
          toggleEvidenceFields(true);
          if (submitButton) {
            submitButton.textContent = @json(__('Save'));
          }
          return;
        }

        fileInput.disabled = false;
        fileInput.required = fileInput.hasAttribute('data-original-required');
        toggleEvidenceFields(false);
        if (submitButton) {
          submitButton.textContent = @json($rtw && !$isBritishRightToWork ? __('Re-upload') : __('Upload'));
        }
      };

      if (fileInput.required) {
        fileInput.setAttribute('data-original-required', '1');
      }

      britishCheckbox.addEventListener('change', updateRightToWorkUpload);
      updateRightToWorkUpload();
    })();

    (function () {
      var verificationForm = document.getElementById('verificationSubmitForm');
      var verificationButton = document.getElementById('verificationSubmitButton');

      if (!verificationForm || !verificationButton) {
        return;
      }

      verificationForm.addEventListener('submit', function () {
        verificationButton.disabled = true;
        verificationButton.style.display = 'none';
      });
    })();
  </script>
@endsection
