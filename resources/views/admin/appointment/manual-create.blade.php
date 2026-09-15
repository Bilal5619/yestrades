@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Create Manual Job') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="{{ route('admin.all_appointment', ['language' => request()->language]) }}">{{ __('Appointments') }}</a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Create Manual Job') }}</a></li>
    </ul>
  </div>

  <form action="{{ route('admin.appointment.manual_store', ['language' => request()->language]) }}" method="post">
    @csrf
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header"><div class="card-title">{{ __('Customer & Service') }}</div></div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('Service') }} *</label>
                  <select name="service_id" id="manualServiceId" class="form-control" required>
                    <option value="">{{ __('Select service') }}</option>
                    @foreach ($services as $serviceContent)
                      <option value="{{ $serviceContent->service_id }}" @selected(old('service_id') == $serviceContent->service_id)>{{ $serviceContent->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>{{ __('Price') }} *</label>
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">{{ $currencyInfo->base_currency_symbol }}</span></div>
                    <input type="number" step="0.01" min="0" name="customer_paid" id="manualCustomerPaid" class="form-control" value="{{ old('customer_paid', '0.00') }}" required readonly>
                    <small id="manualPriceStatus" class="form-text text-muted">{{ __('Amount will calculate automatically from the service, postcode and answers.') }}</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-4"><div class="form-group"><label>{{ __('Customer Name') }} *</label><input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required></div></div>
              <div class="col-lg-4"><div class="form-group"><label>{{ __('Customer Phone') }} *</label><input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}" required></div></div>
              <div class="col-lg-4"><div class="form-group"><label>{{ __('Customer Email') }} *</label><input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}" required></div></div>
            </div>
            <div class="row">
              <div class="col-lg-8"><div class="form-group"><label>{{ __('Customer Address') }} *</label><input type="text" name="customer_address" class="form-control" value="{{ old('customer_address') }}" required></div></div>
              <div class="col-lg-4"><div class="form-group"><label>{{ __('Postcode') }} *</label><input type="text" name="customer_zip_code" id="manualPostcode" class="form-control" value="{{ old('customer_zip_code') }}" required></div></div>
            </div>
            <div class="form-group"><label>{{ __('Country') }}</label><input type="text" name="customer_country" class="form-control" value="{{ old('customer_country', 'United Kingdom') }}"></div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title">{{ __('Service Questions') }}</div></div>
          <div class="card-body">
            <p class="text-muted mb-3">{{ __('Select a service above. Any questions for that service will appear here and will be saved on the booking.') }}</p>
            @foreach ($questionsByService as $serviceId => $questions)
              <div class="manual-question-group" data-service-questions="{{ $serviceId }}" style="display:none;">
                @foreach ($questions as $question)
                  @php($manualQuestionOptions = collect($optionsByQuestion[$question->id] ?? ($question->options ?? [])))
                  <div class="form-group border rounded p-3 mb-3">
                    <label class="font-weight-bold d-block">{{ $question->title }} @if($question->is_required)<span class="text-danger">*</span>@endif</label>
                    @if ($question->type === 'select')
                      <select name="question_answers[{{ $question->id }}]" class="form-control" @if($question->is_required) data-required-question="{{ $serviceId }}" @endif>
                        <option value="">{{ __('Select answer') }}</option>
                        @foreach ($manualQuestionOptions as $option)
                          <option value="{{ $option->id }}" @selected(old('question_answers.' . $question->id) == $option->id)>{{ $option->option_text }} @if((float) $option->price > 0) (+{{ $currencyInfo->base_currency_symbol }}{{ number_format((float) $option->price, 2) }}) @endif</option>
                        @endforeach
                      </select>
                      @if ($manualQuestionOptions->isEmpty())<small class="text-warning">{{ __('No options configured for this question.') }}</small>@endif
                    @elseif ($question->type === 'multi')
                      @forelse ($manualQuestionOptions as $option)
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" id="manual_q_{{ $question->id }}_{{ $option->id }}" name="question_answers[{{ $question->id }}][]" value="{{ $option->id }}" @checked(in_array($option->id, (array) old('question_answers.' . $question->id, [])))>
                          <label class="custom-control-label" for="manual_q_{{ $question->id }}_{{ $option->id }}">{{ $option->option_text }} @if((float) $option->price > 0) (+{{ $currencyInfo->base_currency_symbol }}{{ number_format((float) $option->price, 2) }}) @endif</label>
                        </div>
                      @empty
                        <small class="text-warning">{{ __('No options configured for this question.') }}</small>
                      @endforelse
                    @elseif (in_array($question->type, ['number', 'quantity']))
                      <input type="number" name="question_answers[{{ $question->id }}]" class="form-control" value="{{ old('question_answers.' . $question->id, $question->quantity_default) }}" @if($question->quantity_min !== null) min="{{ $question->quantity_min }}" @endif @if($question->quantity_max !== null) max="{{ $question->quantity_max }}" @endif @if($question->is_required) data-required-question="{{ $serviceId }}" @endif>
                      @if ($question->type === 'quantity' && $question->quantity_unit_price)
                        <small class="text-muted">{{ __('Unit price') }}: {{ $currencyInfo->base_currency_symbol }}{{ number_format((float) $question->quantity_unit_price, 2) }}</small>
                      @endif
                    @else
                      <input type="text" name="question_answers[{{ $question->id }}]" class="form-control" value="{{ old('question_answers.' . $question->id) }}" @if($question->is_required) data-required-question="{{ $serviceId }}" @endif>
                    @endif
                  </div>
                @endforeach
              </div>
            @endforeach
            <div id="manualNoQuestions" class="alert alert-info mb-0">{{ __('No service questions are loaded for the selected service.') }}</div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><div class="card-title">{{ __('Schedule & Status') }}</div></div>
          <div class="card-body">
  <div class="custom-control custom-checkbox mb-4">
  <input
    type="checkbox"
    class="custom-control-input"
    id="tenantSelectsDate"
    name="tenant_selects_date"
    value="1"
    @checked(old('tenant_selects_date'))
  >
  <label class="custom-control-label" for="tenantSelectsDate">
    {{ __('Tenant / occupier will select the appointment date and time') }}
  </label>
</div>

<div id="manualTenantFields" style="display:none;">
  <div class="border rounded p-3 mb-4">
    <h5 class="mb-3">{{ __('Tenant / Occupier Details') }}</h5>

    <div class="form-group">
      <label>{{ __('Tenant Name') }} *</label>
      <input
        type="text"
        name="tenant_name"
        id="manualTenantName"
        class="form-control"
        value="{{ old('tenant_name') }}"
      >
    </div>

    <div class="form-group">
      <label>{{ __('Tenant Email') }}</label>
      <input
        type="email"
        name="tenant_email"
        id="manualTenantEmail"
        class="form-control"
        value="{{ old('tenant_email') }}"
      >
    </div>

    <div class="form-group">
      <label>{{ __('Tenant Phone') }}</label>
      <input
        type="text"
        name="tenant_phone"
        id="manualTenantPhone"
        class="form-control"
        value="{{ old('tenant_phone') }}"
      >
      <small class="form-text text-muted">
        {{ __('Enter at least an email address or phone number.') }}
      </small>
    </div>

    <div class="form-group">
      <label>{{ __('Message / Access Note for Tenant') }}</label>
      <textarea
        name="tenant_note"
        id="manualTenantNote"
        rows="3"
        class="form-control"
      >{{ old('tenant_note') }}</textarea>
    </div>

    <div class="alert alert-info">
      {{ __('The tenant will receive a secure link to choose their appointment date and time. The link expires after 3 days.') }}
    </div>
  </div>
</div>

<div id="manualScheduleFields">
  <div class="form-group">
    <label>{{ __('Booking Date') }} *</label>
    <input
      type="date"
      name="booking_date"
      id="manualBookingDate"
      class="form-control"
      value="{{ old('booking_date') }}"
    >
  </div>
   <div class="form-group">
              <label>{{ __('Assign Staff') }}</label>
              <select name="staff_id" id="manualStaffId" class="form-control">
                <option value="">{{ __('No staff selected') }}</option>
                @foreach ($staffMembers as $staff)
                  @php($staffName = optional($staff->StaffContent->first())->name ?: ($staff->username ?: $staff->email))
                  <option value="{{ $staff->id }}" data-is-day="{{ (int) $staff->is_day }}" @selected(old('staff_id') == $staff->id)>{{ $staffName }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>{{ __('Existing Time Slot') }}</label>
              <select name="service_hour_id" id="manualServiceHourId" class="form-control">
                <option value="">{{ __('Use custom time below') }}</option>
                <optgroup label="{{ __('Staff day slots') }}" data-hour-group="day">
                  @foreach ($staffHours as $hour)
                    <option value="{{ $hour->id }}">{{ $hour->start_time }} - {{ $hour->end_time }}</option>
                  @endforeach
                </optgroup>
                <optgroup label="{{ __('Global slots') }}" data-hour-group="global">
                  @foreach ($globalHours as $hour)
                    <option value="{{ $hour->id }}">{{ $hour->start_time }} - {{ $hour->end_time }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
            <div class="row">
              <div class="col-6"><div class="form-group"><label>{{ __('Start Time') }}</label><input type="time" name="start_date" class="form-control" value="{{ old('start_date') }}"></div></div>
              <div class="col-6"><div class="form-group"><label>{{ __('End Time') }}</label><input type="time" name="end_date" class="form-control" value="{{ old('end_date') }}"></div></div>
            </div>
            </div>
            <div class="form-group"><label>{{ __('Payment Status') }}</label><select name="payment_status" class="form-control"><option value="pending" @selected(old('payment_status') === 'pending')>{{ __('Pending') }}</option><option value="completed" @selected(old('payment_status', 'completed') === 'completed')>{{ __('Completed') }}</option><option value="rejected" @selected(old('payment_status') === 'rejected')>{{ __('Rejected') }}</option></select></div>
            <div class="form-group"><label>{{ __('Job Status') }}</label><select name="order_status" class="form-control"><option value="pending" @selected(old('order_status') === 'pending')>{{ __('Pending') }}</option><option value="accepted" @selected(old('order_status', 'accepted') === 'accepted')>{{ __('Accepted') }}</option><option value="completed" @selected(old('order_status') === 'completed')>{{ __('Completed') }}</option><option value="rejected" @selected(old('order_status') === 'rejected')>{{ __('Rejected') }}</option></select></div>
            <div class="form-group"><label>{{ __('Notes') }}</label><textarea name="access_notes" rows="4" class="form-control">{{ old('access_notes') }}</textarea></div>
            <div class="custom-control custom-checkbox mb-3">
              <input type="checkbox" class="custom-control-input" id="sendCustomerEmail" name="send_customer_email" value="1" checked>
              <label class="custom-control-label" for="sendCustomerEmail">{{ __('Send customer email after creating job') }}</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">{{ __('Create Job & Send Email') }}</button>
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection

@section('script')
  <script>
    (function () {
      const serviceSelect = document.getElementById('manualServiceId');
      const postcodeInput = document.getElementById('manualPostcode');
      const priceInput = document.getElementById('manualCustomerPaid');
      const priceStatus = document.getElementById('manualPriceStatus');
      const priceUrl = @json(route('admin.appointment.manual_price', ['language' => request()->language]));
      const csrfToken = @json(csrf_token());
      const questionGroups = Array.from(document.querySelectorAll('[data-service-questions]'));
      const noQuestions = document.getElementById('manualNoQuestions');
      const staffSelect = document.getElementById('manualStaffId');
      const hourSelect = document.getElementById('manualServiceHourId');
      const tenantSelectsDate = document.getElementById('tenantSelectsDate');
const tenantFields = document.getElementById('manualTenantFields');
const scheduleFields = document.getElementById('manualScheduleFields');
const bookingDateInput = document.getElementById('manualBookingDate');
const tenantNameInput = document.getElementById('manualTenantName');
const tenantEmailInput = document.getElementById('manualTenantEmail');
const tenantPhoneInput = document.getElementById('manualTenantPhone');
      let priceTimer = null;

      function collectQuestionAnswers() {
        const answers = {};
        const serviceId = serviceSelect ? serviceSelect.value : '';
        if (!serviceId) return answers;
        const group = document.querySelector('[data-service-questions="' + serviceId + '"]');
        if (!group) return answers;

        group.querySelectorAll('[name^="question_answers"]').forEach(input => {
          const match = input.name.match(/^question_answers\[(\d+)\]/);
          if (!match) return;
          const questionId = match[1];
          if (input.type === 'checkbox') {
            if (!input.checked) return;
            if (!answers[questionId]) answers[questionId] = [];
            answers[questionId].push(input.value);
            return;
          }
          if (input.value !== '') answers[questionId] = input.value;
        });

        return answers;
      }
function syncTenantScheduling() {
  if (!tenantSelectsDate) return;

  const tenantMode = tenantSelectsDate.checked;

  if (tenantFields) {
    tenantFields.style.display = tenantMode ? '' : 'none';
  }

  if (scheduleFields) {
    scheduleFields.style.display = tenantMode ? 'none' : '';
  }

  if (bookingDateInput) {
    bookingDateInput.required = !tenantMode;
    bookingDateInput.disabled = tenantMode;
  }

  if (staffSelect) {
    staffSelect.disabled = tenantMode;
  }

  if (hourSelect) {
    hourSelect.disabled = tenantMode;
  }

  const startTime = document.querySelector('[name="start_date"]');
  const endTime = document.querySelector('[name="end_date"]');

  if (startTime) {
    startTime.disabled = tenantMode;
  }

  if (endTime) {
    endTime.disabled = tenantMode;
  }

  if (tenantNameInput) {
    tenantNameInput.required = tenantMode;
  }

  if (!tenantMode) {
    if (tenantNameInput) tenantNameInput.required = false;
  }
}
if (tenantSelectsDate) {
  tenantSelectsDate.addEventListener('change', syncTenantScheduling);
}

syncTenantScheduling();
      function schedulePriceCalculation() {
        window.clearTimeout(priceTimer);
        priceTimer = window.setTimeout(calculatePrice, 250);
      }

      async function calculatePrice() {
        if (!serviceSelect || !priceInput) return;
        const serviceId = serviceSelect.value;
        if (!serviceId) {
          priceInput.value = '0.00';
          if (priceStatus) priceStatus.textContent = @json(__('Select a service to calculate the amount.'));
          return;
        }

        if (priceStatus) priceStatus.textContent = @json(__('Calculating amount...'));

        try {
          const response = await fetch(priceUrl, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
              service_id: serviceId,
              postcode: postcodeInput ? postcodeInput.value : '',
              question_answers: collectQuestionAnswers(),
            }),
          });
          const data = await response.json();
          if (!response.ok || !data.success) throw new Error(data.message || 'Price calculation failed');
          priceInput.value = Number(data.price || 0).toFixed(2);
          if (priceStatus) {
            priceStatus.textContent = @json(__('Auto calculated')) + ': ' + data.price_display + ' (' + @json(__('questions')) + ': ' + Number(data.question_surcharge || 0).toFixed(2) + ', ' + @json(__('postcode')) + ': ' + Number(data.postcode_surcharge || 0).toFixed(2) + ')';
          }
        } catch (error) {
          if (priceStatus) priceStatus.textContent = @json(__('Could not calculate automatically. Please check the service, postcode and answers.'));
        }
      }

      function syncQuestions() {
        const serviceId = serviceSelect ? serviceSelect.value : '';
        let shown = false;
        questionGroups.forEach(group => {
          const active = group.dataset.serviceQuestions === serviceId;
          group.style.display = active ? '' : 'none';
          group.querySelectorAll('[data-required-question]').forEach(input => {
            if (active) input.setAttribute('required', 'required');
            else input.removeAttribute('required');
          });
          if (active) shown = true;
        });
        if (noQuestions) noQuestions.style.display = shown ? 'none' : '';
      }

      function syncHourGroups() {
        if (!staffSelect || !hourSelect) return;
        const selected = staffSelect.options[staffSelect.selectedIndex];
        const isDay = selected ? selected.dataset.isDay : '';
        hourSelect.querySelectorAll('optgroup').forEach(group => {
          const show = !isDay || (isDay === '1' && group.dataset.hourGroup === 'day') || (isDay === '0' && group.dataset.hourGroup === 'global');
          group.disabled = !show;
        });
      }

      if (serviceSelect) serviceSelect.addEventListener('change', function () { syncQuestions(); schedulePriceCalculation(); });
      if (postcodeInput) postcodeInput.addEventListener('input', schedulePriceCalculation);
      document.querySelectorAll('[name^="question_answers"]').forEach(input => input.addEventListener('change', schedulePriceCalculation));
      document.querySelectorAll('[name^="question_answers"]').forEach(input => input.addEventListener('input', schedulePriceCalculation));
      if (staffSelect) staffSelect.addEventListener('change', syncHourGroups);
      syncQuestions();
      syncHourGroups();
      schedulePriceCalculation();
    })();
  </script>
@endsection
