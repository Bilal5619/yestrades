@extends('frontend.layout')

@section('pageHeading')
  {{ __('Request a Job') }}
@endsection

@section('metaKeywords')
  {{ __('request a job, Yes Trades, property maintenance, plumbing, electrical, heating, gas services') }}
@endsection

@section('metaDescription')
  {{ __('Request a job from Yes Trades. Tell us what work you need and our team will review your request and arrange the next steps.') }}
@endsection

@section('content')

  <main class="yes-job-request-page">

    <section class="job-request-hero">
      <div class="container">
        <div class="job-request-hero-content">
          <span class="job-request-kicker">
            {{ __('YES TRADES') }}
          </span>

          <h1>
            {{ __('Request a Job') }}
          </h1>

         <p>
  {{ __('Already spoken to Yes Trades? Submit your job details here so our team can arrange and manage your visit.') }}
</p>
        </div>
      </div>
    </section>

    <section class="job-request-section">
      <div class="container">

        <div class="row justify-content-center">
          <div class="col-lg-9 col-xl-8">

            {{-- Success --}}
            @if (session('success'))
              <div class="alert alert-success job-request-success">
                <h4 class="mb-2">
                  <i class="fal fa-check-circle"></i>
                  {{ __('Request received') }}
                </h4>

                <p class="mb-0">
                  {{ session('success') }}
                </p>

                @if (session('job_reference'))
                  <div class="job-reference-box">
                    <span>
                      {{ __('Your job reference') }}
                    </span>

                    <strong>
                      {{ session('job_reference') }}
                    </strong>
                  </div>
                @endif
              </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
              <div class="alert alert-danger">
                <strong>
                  {{ __('Please check the form below.') }}
                </strong>

                <ul class="mb-0 mt-2">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="job-request-card">

              <div class="job-request-card-header">
                <h2>
                  {{ __('Job details') }}
                </h2>

                <p>
                  {{ __('Fields marked with * are required.') }}
                </p>
              </div>

              <form
                action="{{ route('frontend.job_request.store') }}"
                method="POST"
                id="publicJobRequestForm"
              >
                @csrf

                {{-- Honeypot --}}
                <div
                  aria-hidden="true"
                  style="
                    position:absolute;
                    left:-10000px;
                    width:1px;
                    height:1px;
                    overflow:hidden;
                  "
                >
                  <label for="website">
                    Website
                  </label>

                  <input
                    type="text"
                    name="website"
                    id="website"
                    tabindex="-1"
                    autocomplete="off"
                  >
                </div>

                {{-- Customer information --}}
                <div class="job-form-section">

                  <h3>
                    <i class="fal fa-user"></i>
                    {{ __('Your details') }}
                  </h3>

                  <div class="row">

                    <div class="col-md-6">
                      <div class="form-group mb-3">
                        <label for="customer_name">
                          {{ __('Full name') }}
                          <span class="required">*</span>
                        </label>

                        <input
                          type="text"
                          class="form-control"
                          name="customer_name"
                          id="customer_name"
                          value="{{ old('customer_name') }}"
                          maxlength="255"
                          autocomplete="name"
                          required
                        >

                        @error('customer_name')
                          <div class="text-danger mt-1">
                            {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group mb-3">
                        <label for="customer_phone">
                          {{ __('Phone number') }}
                          <span class="required">*</span>
                        </label>

                        <input
                          type="tel"
                          class="form-control"
                          name="customer_phone"
                          id="customer_phone"
                          value="{{ old('customer_phone') }}"
                          maxlength="50"
                          autocomplete="tel"
                          required
                        >

                        @error('customer_phone')
                          <div class="text-danger mt-1">
                            {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="form-group mb-3">
                        <label for="customer_email">
                          {{ __('Email address') }}
                          <span class="required">*</span>
                        </label>

                        <input
                          type="email"
                          class="form-control"
                          name="customer_email"
                          id="customer_email"
                          value="{{ old('customer_email') }}"
                          maxlength="255"
                          autocomplete="email"
                          required
                        >

                        <small class="form-text text-muted">
                          {{ __('We may use this email for your job updates, invoice and certificate.') }}
                        </small>

                        @error('customer_email')
                          <div class="text-danger mt-1">
                            {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>

                  </div>
                </div>

                {{-- Property --}}
                <div class="job-form-section">

                  <h3>
                    <i class="fal fa-map-marker-alt"></i>
                    {{ __('Job address') }}
                  </h3>

                  <div class="form-group mb-3">
                    <label for="site_address">
                      {{ __('Property / job address') }}
                      <span class="required">*</span>
                    </label>

                    <textarea
                      class="form-control"
                      name="site_address"
                      id="site_address"
                      rows="3"
                      maxlength="500"
                      autocomplete="street-address"
                      required
                    >{{ old('site_address') }}</textarea>

                    @error('site_address')
                      <div class="text-danger mt-1">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                  <div class="form-group mb-3">
                    <label for="site_postcode">
                      {{ __('Postcode') }}
                      <span class="required">*</span>
                    </label>

                    <input
                      type="text"
                      class="form-control text-uppercase"
                      name="site_postcode"
                      id="site_postcode"
                      value="{{ old('site_postcode') }}"
                      maxlength="20"
                      autocomplete="postal-code"
                      required
                    >

                    @error('site_postcode')
                      <div class="text-danger mt-1">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                </div>

                {{-- Work --}}
                <div class="job-form-section">

                  <h3>
                    <i class="fal fa-tools"></i>
                    {{ __('What work do you need?') }}
                  </h3>

                  <div class="form-group mb-3">
                    <label for="title">
                      {{ __('Job / service required') }}
                      <span class="required">*</span>
                    </label>

                    <input
                      type="text"
                      class="form-control"
                      name="title"
                      id="title"
                      value="{{ old('title') }}"
                      maxlength="255"
                      placeholder="{{ __('Example: Boiler repair, EICR, leaking tap') }}"
                      required
                    >

                    @error('title')
                      <div class="text-danger mt-1">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                  <div class="form-group mb-3">
                    <label for="description">
                      {{ __('Describe the job or problem') }}
                      <span class="required">*</span>
                    </label>

                    <textarea
                      class="form-control"
                      name="description"
                      id="description"
                      rows="5"
                      maxlength="5000"
                      placeholder="{{ __('Please give us as much useful information as possible.') }}"
                      required
                    >{{ old('description') }}</textarea>

                    @error('description')
                      <div class="text-danger mt-1">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                </div>

                {{-- Appointment --}}
                <div class="job-form-section">

                  <h3>
                    <i class="fal fa-calendar-alt"></i>
                    {{ __('Preferred appointment') }}
                  </h3>

                  <div class="job-info-note">
                    <i class="fal fa-info-circle"></i>

                    <span>
                      {{ __('A preferred date is not a confirmed appointment. Our team will review your request first.') }}
                    </span>
                  </div>

                  <div class="row">

                    <div class="col-md-6">
                      <div class="form-group mb-3">
                        <label for="preferred_date">
                          {{ __('Preferred date') }}
                        </label>

                        <input
                          type="date"
                          class="form-control"
                          name="preferred_date"
                          id="preferred_date"
                          value="{{ old('preferred_date') }}"
                          min="{{ now()->toDateString() }}"
                        >

                        <small class="form-text text-muted">
                          {{ __('Leave blank if the date has not been decided.') }}
                        </small>

                        @error('preferred_date')
                          <div class="text-danger mt-1">
                            {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group mb-3">
                        <label for="preferred_time">
                          {{ __('Preferred time') }}
                        </label>

                        <input
                          type="time"
                          class="form-control"
                          name="preferred_time"
                          id="preferred_time"
                          value="{{ old('preferred_time') }}"
                        >

                        @error('preferred_time')
                          <div class="text-danger mt-1">
                            {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>

                  </div>
                </div>

                {{-- Urgency --}}
                <div class="job-form-section">

                  <h3>
                    <i class="fal fa-exclamation-circle"></i>
                    {{ __('How urgent is the job?') }}
                  </h3>

                  <div class="form-group mb-3">
                    <select
                      class="form-control"
                      name="urgency"
                      id="urgency"
                      required
                    >
                      <option value="">
                        {{ __('Select urgency') }}
                      </option>

                      <option
                        value="normal"
                        {{ old('urgency') === 'normal' ? 'selected' : '' }}
                      >
                        {{ __('Normal') }}
                      </option>

                      <option
                        value="urgent"
                        {{ old('urgency') === 'urgent' ? 'selected' : '' }}
                      >
                        {{ __('Urgent') }}
                      </option>

                      <option
                        value="emergency"
                        {{ old('urgency') === 'emergency' ? 'selected' : '' }}
                      >
                        {{ __('Emergency') }}
                      </option>
                    </select>

                    @error('urgency')
                      <div class="text-danger mt-1">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>

                </div>

                {{-- Certificate --}}
                <div class="job-form-section">

                  <h3>
                    <i class="fal fa-file-certificate"></i>
                    {{ __('Certificate') }}
                  </h3>

                  <div class="form-check mb-3">

                    <input
                      class="form-check-input"
                      type="checkbox"
                      name="certificate_required"
                      id="certificate_required"
                      value="1"
                      {{ old('certificate_required') ? 'checked' : '' }}
                    >

                    <label
                      class="form-check-label"
                      for="certificate_required"
                    >
                      {{ __('I need a certificate for this work') }}
                    </label>

                  </div>

                  <div
                    class="form-group mb-3"
                    id="certificateTypeWrap"
                  >

                    <label for="certificate_type">
                      {{ __('Certificate type') }}
                    </label>

                    <select
                      class="form-control"
                      name="certificate_type"
                      id="certificate_type"
                    >
                      <option value="">
                        {{ __('Select certificate') }}
                      </option>

                      <option
                        value="cp12"
                        {{ old('certificate_type') === 'cp12' ? 'selected' : '' }}
                      >
                        {{ __('Gas Safety Certificate (CP12)') }}
                      </option>

                      <option
                        value="eicr"
                        {{ old('certificate_type') === 'eicr' ? 'selected' : '' }}
                      >
                        {{ __('EICR') }}
                      </option>

                      <option
                        value="eic"
                        {{ old('certificate_type') === 'eic' ? 'selected' : '' }}
                      >
                        {{ __('Electrical Installation Certificate (EIC)') }}
                      </option>

                      <option
                        value="minor_works"
                        {{ old('certificate_type') === 'minor_works' ? 'selected' : '' }}
                      >
                        {{ __('Minor Works Certificate') }}
                      </option>

                      <option
                        value="pat"
                        {{ old('certificate_type') === 'pat' ? 'selected' : '' }}
                      >
                        {{ __('PAT Certificate') }}
                      </option>

                      <option
                        value="epc"
                        {{ old('certificate_type') === 'epc' ? 'selected' : '' }}
                      >
                        {{ __('Energy Performance Certificate (EPC)') }}
                      </option>

                      <option
                        value="other"
                        {{ old('certificate_type') === 'other' ? 'selected' : '' }}
                      >
                        {{ __('Other') }}
                      </option>
                    </select>

                    @error('certificate_type')
                      <div class="text-danger mt-1">
                        {{ $message }}
                      </div>
                    @enderror

                  </div>

                </div>

                <div class="job-submit-area">

                  <button
                    type="submit"
                    class="btn btn-primary job-submit-button"
                  >
                    <i class="fal fa-paper-plane"></i>
                    {{ __('Submit Job Request') }}
                  </button>

                  <p>
                    {{ __('After submission, our team will review your request before confirming the appointment.') }}
                  </p>

                </div>

              </form>

            </div>
          </div>
        </div>

      </div>
    </section>

  </main>


  <style>
    .yes-job-request-page {
      background: #f5f7fb;
    }

    .job-request-hero {
      padding: 70px 0 55px;
      background: #14213d;
      color: #ffffff;
    }

    .job-request-hero-content {
      max-width: 760px;
      margin: 0 auto;
      text-align: center;
    }

    .job-request-kicker {
      display: inline-block;
      margin-bottom: 12px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      opacity: 0.8;
    }

    .job-request-hero h1 {
      margin: 0 0 15px;
      color: #ffffff;
      font-size: clamp(34px, 5vw, 54px);
      font-weight: 700;
    }

    .job-request-hero p {
      max-width: 650px;
      margin: 0 auto;
      color: rgba(255,255,255,.85);
      font-size: 17px;
      line-height: 1.7;
    }

    .job-request-section {
      padding: 55px 0 80px;
    }

    .job-request-card {
      background: #ffffff;
      border: 1px solid #e5e9f0;
      border-radius: 16px;
      box-shadow: 0 12px 35px rgba(20, 33, 61, 0.08);
      overflow: hidden;
    }

    .job-request-card-header {
      padding: 28px 30px;
      border-bottom: 1px solid #edf0f4;
    }

    .job-request-card-header h2 {
      margin-bottom: 6px;
      font-size: 26px;
    }

    .job-request-card-header p {
      margin: 0;
      color: #6b7280;
    }

    .job-form-section {
      padding: 28px 30px;
      border-bottom: 1px solid #edf0f4;
    }

    .job-form-section h3 {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 22px;
      font-size: 19px;
      font-weight: 700;
    }

    .job-form-section label {
      display: block;
      margin-bottom: 7px;
      font-weight: 600;
    }

    .job-form-section .form-control {
      min-height: 48px;
      border-radius: 8px;
    }

    .job-form-section textarea.form-control {
      min-height: auto;
    }

    .required {
      color: #dc3545;
    }

    .job-info-note {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      padding: 13px 15px;
      border-radius: 8px;
      background: #eef6ff;
      color: #315b82;
      font-size: 14px;
    }

    .job-submit-area {
      padding: 30px;
      text-align: center;
    }

    .job-submit-button {
      min-width: 240px;
      min-height: 52px;
      padding: 13px 26px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 700;
    }

    .job-submit-area p {
      max-width: 560px;
      margin: 14px auto 0;
      color: #6b7280;
      font-size: 13px;
    }

    .job-request-success {
      margin-bottom: 25px;
      padding: 22px;
      border-radius: 12px;
    }

    .job-reference-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 15px;
      margin-top: 16px;
      padding: 14px 16px;
      border-radius: 8px;
      background: rgba(255,255,255,.7);
    }

    .job-reference-box strong {
      font-size: 20px;
      letter-spacing: .5px;
    }

    @media (max-width: 767px) {
      .job-request-hero {
        padding: 50px 0 40px;
      }

      .job-request-section {
        padding: 35px 0 55px;
      }

      .job-request-card-header,
      .job-form-section,
      .job-submit-area {
        padding-left: 20px;
        padding-right: 20px;
      }

      .job-submit-button {
        width: 100%;
        min-width: 0;
      }

      .job-reference-box {
        align-items: flex-start;
        flex-direction: column;
      }
    }
  </style>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const certificateCheckbox =
        document.getElementById('certificate_required');

      const certificateWrap =
        document.getElementById('certificateTypeWrap');

      const certificateSelect =
        document.getElementById('certificate_type');

      const dateInput =
        document.getElementById('preferred_date');

      const timeInput =
        document.getElementById('preferred_time');

      function updateCertificateField() {
        if (!certificateCheckbox) {
          return;
        }

        const checked =
          certificateCheckbox.checked;

        certificateWrap.style.display =
          checked ? 'block' : 'none';

        certificateSelect.required =
          checked;

        if (!checked) {
          certificateSelect.value = '';
        }
      }

      function updateTimeField() {
        if (!dateInput || !timeInput) {
          return;
        }

        if (dateInput.value) {
          timeInput.disabled = false;
        } else {
          timeInput.value = '';
          timeInput.disabled = true;
        }
      }

      if (certificateCheckbox) {
        certificateCheckbox.addEventListener(
          'change',
          updateCertificateField
        );

        updateCertificateField();
      }

      if (dateInput) {
        dateInput.addEventListener(
          'change',
          updateTimeField
        );

        updateTimeField();
      }
    });
  </script>

@endsection