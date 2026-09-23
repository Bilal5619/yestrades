{{--
  On-page booking panel (MyConstructor-style): service questions, postcode,
  date/time or tenant hand-off, live price. Submits to the existing cart
  quick-add, which then opens checkout at the address + payment step.
  Expects: $details (Services), $service (ServiceContent).
--}}
@php
  $bpQuestions = \App\Models\Question::where('service_id', $details->id)->orderBy('id')->get();
  $bpOptions = \App\Models\QuestionOption::whereIn('question_id', $bpQuestions->pluck('id'))->get()->groupBy('question_id');
  $bpConditions = \App\Models\QuestionCondition::whereIn('question_id', $bpQuestions->pluck('id'))->get()->groupBy('question_id');
  $bpMinDate = \Carbon\Carbon::today()->addDays(3)->format('Y-m-d');
  $bpBasePrice = $details->hasPricePlaceholder() ? 0 : (float) $details->price;
  $bpSlots = ['08:00 AM - 10:00 AM', '10:00 AM - 12:00 PM', '02:00 PM - 04:00 PM', '04:00 PM - 06:00 PM'];
@endphp

<section class="ytx-section ytx-booking-section" id="ytx-book">
  <div class="ytx-wrap">
    <div class="ytx-booking-intro">
      <h2>{{ __('Get your price and book online') }}</h2>
      <p>{{ __('Answer a few quick questions, choose a date, then add your address and pay — all in one go. No account needed.') }}</p>
    </div>

    <form id="ytxBookingForm" class="ytx-booking-grid" action="{{ route('frontend.services.cart.quick_add') }}" method="POST"
      data-price-url="{{ route('frontend.services.booking_price') }}" data-service-id="{{ $details->id }}" data-base-price="{{ $bpBasePrice }}">
      @csrf
      <input type="hidden" name="service_id" value="{{ $details->id }}">
      <input type="hidden" name="question_answers" id="ytxAnswersJson" value="{}">
      <input type="hidden" name="scheduling_mode" id="ytxSchedulingMode" value="now">

      <div class="ytx-booking-main">
        {{-- Step 1: questions --}}
        @if ($bpQuestions->isNotEmpty())
          <div class="ytx-bstep">
            <div class="ytx-bstep-head"><span class="ytx-bstep-num">1</span><h3>{{ __('About the job') }}</h3></div>
            @foreach ($bpQuestions as $q)
              @php
                $opts = $bpOptions[$q->id] ?? collect();
                $conds = ($bpConditions[$q->id] ?? collect())->map(fn ($c) => [
                  'depends_on_question_id' => $c->depends_on_question_id,
                  'depends_on_option_id' => $c->depends_on_option_id,
                  'condition_type' => $c->condition_type ?: 'equals',
                  'operator' => $c->operator ?: 'AND',
                ])->values();
              @endphp
              <div class="ytx-q" data-question-id="{{ $q->id }}" data-type="{{ $q->type }}" data-required="{{ $q->is_required ? 'true' : 'false' }}"
                data-has-conditions="{{ $conds->isNotEmpty() ? 'true' : 'false' }}" data-conditions='@json($conds)'
                @if ($q->type === 'quantity') data-unit-price="{{ (float) $q->quantity_unit_price }}" data-default="{{ (int) ($q->quantity_default ?? 0) }}" @endif>
                <label class="ytx-q-title">{{ $q->title }} @if ($q->is_required)<span>*</span>@endif</label>

                @if (in_array($q->type, ['select', 'multi'], true) && $opts->isNotEmpty())
                  <div class="ytx-q-options">
                    @foreach ($opts as $o)
                      <label class="ytx-q-option">
                        @php $ytxIsQuoteOpt = !empty($o->is_contact_option) || (str_contains(strtolower($o->option_text), '20+') && str_contains(strtolower($o->option_text), 'circuit')); @endphp
                        <input type="{{ $q->type === 'multi' ? 'checkbox' : 'radio' }}" name="q_{{ $q->id }}{{ $q->type === 'multi' ? '[]' : '' }}" value="{{ $o->id }}" data-price="{{ (float) $o->price }}" @if ($ytxIsQuoteOpt) data-quote="1" @endif class="ytx-q-input">
                        <span class="ytx-q-option-body">
                          <strong>{{ $o->option_text }}</strong>
                          @if ($ytxIsQuoteOpt)<small>{{ __('Quote required') }}</small>@elseif ((float) $o->price > 0)<small>+{{ symbolPrice($o->price) }}</small>@endif
                          @if (!empty($o->description))<em>{{ $o->description }}</em>@endif
                        </span>
                      </label>
                    @endforeach
                  </div>
                @elseif ($q->type === 'quantity')
                  <div class="ytx-q-qty">
                    <button type="button" class="ytx-q-qty-btn" data-qty="-1" aria-label="{{ __('Decrease') }}">−</button>
                    <input type="number" class="ytx-q-input" name="q_{{ $q->id }}" value="{{ (int) ($q->quantity_default ?? ($q->quantity_min ?? 0)) }}" min="{{ (int) ($q->quantity_min ?? 0) }}" @if ($q->quantity_max !== null) max="{{ (int) $q->quantity_max }}" @endif inputmode="numeric">
                    <button type="button" class="ytx-q-qty-btn" data-qty="1" aria-label="{{ __('Increase') }}">+</button>
                    @if ((float) $q->quantity_unit_price > 0)<small>+{{ symbolPrice($q->quantity_unit_price) }} {{ __('each above') }} {{ (int) ($q->quantity_default ?? 0) }}</small>@endif
                  </div>
                @else
                  <input type="text" class="ytx-q-input ytx-q-text" name="q_{{ $q->id }}" placeholder="{{ __('Your answer') }}">
                @endif
                <span class="ytx-q-error" hidden>{{ __('Please answer this question.') }}</span>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Step 2: postcode --}}
        <div class="ytx-bstep">
          <div class="ytx-bstep-head"><span class="ytx-bstep-num">{{ $bpQuestions->isNotEmpty() ? 2 : 1 }}</span><h3>{{ __('Where is the property?') }}</h3></div>
          <p class="ytx-bstep-note">{{ __('We use your postcode to confirm coverage and any location charge. Full address is asked at checkout.') }}</p>
          <div class="ytx-postcode-row">
            <input type="text" name="postcode" id="ytxPostcode" required placeholder="{{ __('Postcode, e.g. EH54 6AA') }}" autocomplete="postal-code">
            <button type="button" class="ytx-btn ytx-outline" id="ytxCheckPostcode">{{ __('Check price') }}</button>
          </div>
          <span class="ytx-q-error" id="ytxPostcodeError" hidden>{{ __('Please enter your postcode.') }}</span>
          <p class="ytx-postcode-note" id="ytxPostcodeNote"></p>
        </div>

        {{-- Step 3: date / tenant --}}
        <div class="ytx-bstep">
          <div class="ytx-bstep-head"><span class="ytx-bstep-num">{{ $bpQuestions->isNotEmpty() ? 3 : 2 }}</span><h3>{{ __('When do you need us?') }}</h3></div>
          <div class="ytx-mode-row">
            <label class="ytx-mode-card is-selected">
              <input type="radio" name="ytx_mode" value="now" checked>
              <span><strong>{{ __('I will choose the date & time') }}</strong><small>{{ __('Pick a day and arrival window now.') }}</small></span>
            </label>
            <label class="ytx-mode-card">
              <input type="radio" name="ytx_mode" value="tenant">
              <span><strong>{{ __('Let the tenant / occupier choose') }}</strong><small>{{ __('We email them a secure link to pick a slot. Ideal for landlords.') }}</small></span>
            </label>
          </div>

          <div id="ytxModeNow">
            <div class="ytx-date-row">
              <div>
                <label class="ytx-q-title" for="ytxBookingDate">{{ __('Preferred date') }} <span>*</span></label>
                <input type="date" name="booking_date" id="ytxBookingDate" min="{{ $bpMinDate }}">
              </div>
              <div>
                <label class="ytx-q-title" for="ytxTimeSlot">{{ __('Arrival window') }} <span>*</span></label>
                <select name="time_slot" id="ytxTimeSlot">
                  <option value="">{{ __('Choose a time slot') }}</option>
                  @foreach ($bpSlots as $slot)
                    <option value="{{ $slot }}">{{ $slot }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <p class="ytx-bstep-note">{{ __('Need it sooner? Urgent (24–48h) options are available at checkout.') }}</p>
            <span class="ytx-q-error" id="ytxDateError" hidden>{{ __('Please choose a date and time slot.') }}</span>
          </div>

          <div id="ytxModeTenant" hidden>
            <div class="ytx-date-row">
              <div><label class="ytx-q-title" for="ytxTenantName">{{ __('Tenant / occupier name') }} <span>*</span></label><input type="text" name="tenant_name" id="ytxTenantName"></div>
              <div><label class="ytx-q-title" for="ytxTenantEmail">{{ __('Tenant email') }}</label><input type="email" name="tenant_email" id="ytxTenantEmail"></div>
              <div><label class="ytx-q-title" for="ytxTenantPhone">{{ __('Tenant phone') }}</label><input type="tel" name="tenant_phone" id="ytxTenantPhone"></div>
              <div><label class="ytx-q-title" for="ytxTenantNote">{{ __('Note to tenant (optional)') }}</label><input type="text" name="tenant_note" id="ytxTenantNote" maxlength="1000"></div>
            </div>
            <p class="ytx-bstep-note">{{ __('Enter at least an email or phone number. The tenant gets a 3-day link to choose a normal weekday slot; you confirm it before the visit.') }}</p>
            <span class="ytx-q-error" id="ytxTenantError" hidden>{{ __('Please enter the tenant name and an email or phone number.') }}</span>
          </div>
        </div>

        <button type="submit" class="ytx-btn ytx-full ytx-booking-submit">{{ __('Continue to address & payment') }} →</button>
        <div class="ytx-mini-trust">
          <span>{{ __('No account needed') }}</span>
          <span>{{ __('Pay by card, Apple Pay or Google Pay') }}</span>
          <span>{{ __('Nothing charged until you confirm') }}</span>
        </div>
      </div>

      <aside class="ytx-booking-summary">
        <span class="ytx-basket-pill"><i class="fal fa-shopping-bag"></i> {{ __('Your booking') }}</span>
        <div class="ytx-booking-summary-service">
          <img src="{{ (!empty($details->service_image) && file_exists(public_path('assets/img/services/' . $details->service_image))) ? asset('assets/img/services/' . $details->service_image) : $ytxHeroImage }}" alt="">
          <div><small>{{ __('Selected service') }}</small><h3>{{ $service->name }}</h3></div>
        </div>
        <div class="ytx-price-big">
          <span>{{ __('Total') }}<br><small id="ytxPriceHint">{{ __('Base price') }}</small></span>
          <strong id="ytxPriceTotal">{{ symbolPrice($bpBasePrice) }}</strong>
        </div>
        <div class="ytx-summary-line"><span>{{ __('Service') }}</span><span id="ytxPriceBase">{{ symbolPrice($bpBasePrice) }}</span></div>
        <div class="ytx-summary-line" id="ytxPriceQuestionsRow" hidden><span>{{ __('Options') }}</span><span id="ytxPriceQuestions">—</span></div>
        <div class="ytx-summary-line" id="ytxPricePostcodeRow" hidden><span>{{ __('Location charge') }}</span><span id="ytxPricePostcode">—</span></div>
        <div class="ytx-summary-line" id="ytxSummaryDate"><span>{{ __('Appointment') }}</span><span id="ytxSummaryDateValue">{{ __('Not chosen yet') }}</span></div>
        <p class="ytx-marketplace-note">{{ __('The exact total is confirmed before payment. No additional work is charged without your approval.') }}</p>
        <div class="ytx-mini-trust">
          <span>{{ __('Gas Safe & NICEIC registered') }}</span>
          <span>{{ __('Workmanship guaranteed') }}</span>
          <span>{{ __('Family-run since 2012') }}</span>
        </div>
      </aside>
    </form>
  </div>
</section>

@push('scripts')
<script>
(function () {
  const form = document.getElementById('ytxBookingForm');
  if (!form) return;
  const csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  const priceUrl = form.dataset.priceUrl;
  const serviceId = form.dataset.serviceId;
  const currency = @json(html_entity_decode(symbolPrice(0), ENT_QUOTES, 'UTF-8')).replace(/[0-9.,\s]/g, '');
  const fmt = (n) => currency + Number(n || 0).toFixed(2);
  let priceTimer = null;

  function qAnswer(card) {
    const type = card.dataset.type;
    if (type === 'multi') {
      return Array.from(card.querySelectorAll('input[type="checkbox"]:checked')).map(i => i.value);
    }
    if (type === 'select') {
      const r = card.querySelector('input[type="radio"]:checked');
      return r ? r.value : '';
    }
    const i = card.querySelector('.ytx-q-input');
    return i ? i.value.trim() : '';
  }
  function condMet(answer, optionId, type) {
    const has = Array.isArray(answer) ? answer.length > 0 : String(answer || '').trim() !== '';
    if (!has) return false;
    if (Array.isArray(answer)) {
      const found = answer.map(String).includes(String(optionId));
      return (type === 'not_equals' || type === 'not_contains') ? !found : found;
    }
    switch (type) {
      case 'not_equals': return String(answer) !== String(optionId);
      case 'contains': return String(answer).includes(String(optionId));
      case 'not_contains': return !String(answer).includes(String(optionId));
      default: return String(answer) === String(optionId);
    }
  }
  function updateConditions() {
    form.querySelectorAll('.ytx-q[data-has-conditions="true"]').forEach(card => {
      let conds = []; try { conds = JSON.parse(card.dataset.conditions || '[]'); } catch (e) {}
      if (!conds.length) return;
      let result = null;
      conds.forEach(c => {
        const dep = form.querySelector('.ytx-q[data-question-id="' + c.depends_on_question_id + '"]');
        const met = dep ? condMet(qAnswer(dep), c.depends_on_option_id, c.condition_type || 'equals') : false;
        if (result === null) { result = met; return; }
        result = (c.operator === 'OR') ? (result || met) : (result && met);
      });
      card.hidden = result !== true;
      if (card.hidden) card.querySelectorAll('input').forEach(i => { if (i.type === 'checkbox' || i.type === 'radio') i.checked = false; else if (i.type !== 'number') i.value = ''; });
    });
  }
  function collectAnswers() {
    const out = {};
    form.querySelectorAll('.ytx-q').forEach(card => {
      if (card.hidden) return;
      const a = qAnswer(card);
      if (Array.isArray(a) ? a.length : a !== '') out[card.dataset.questionId] = a;
    });
    return out;
  }
  function updateChoiceVisuals() {
    form.querySelectorAll('.ytx-q-option').forEach(l => { const i = l.querySelector('input'); l.classList.toggle('is-selected', !!(i && i.checked)); });
    form.querySelectorAll('.ytx-mode-card').forEach(l => { const i = l.querySelector('input'); l.classList.toggle('is-selected', !!(i && i.checked)); });
  }
  function updateSummaryDate() {
    const el = document.getElementById('ytxSummaryDateValue');
    if (!el) return;
    if (document.getElementById('ytxSchedulingMode').value === 'tenant') { el.textContent = @json(__('Tenant will choose')); return; }
    const d = document.getElementById('ytxBookingDate').value, s = document.getElementById('ytxTimeSlot').value;
    el.textContent = (d && s) ? (new Date(d + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) + ' · ' + s) : @json(__('Not chosen yet'));
  }
  function refreshPrice() {
    clearTimeout(priceTimer);
    priceTimer = setTimeout(() => {
      const fd = new FormData();
      fd.set('service_id', serviceId);
      fd.set('postcode', document.getElementById('ytxPostcode').value.trim());
      fd.set('question_answers', JSON.stringify(collectAnswers()));
      fetch(priceUrl, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } })
        .then(r => r.json()).then(d => {
          if (!d || !d.success) return;
          document.getElementById('ytxPriceBase').textContent = fmt(d.base);
          const qr = document.getElementById('ytxPriceQuestionsRow'); qr.hidden = !(d.questions > 0); document.getElementById('ytxPriceQuestions').textContent = '+' + fmt(d.questions);
          const pr = document.getElementById('ytxPricePostcodeRow'); pr.hidden = !(d.postcode_surcharge > 0); document.getElementById('ytxPricePostcode').textContent = '+' + fmt(d.postcode_surcharge);
          const quoteSel = !!form.querySelector('.ytx-q-input[data-quote]:checked');
          document.getElementById('ytxPriceTotal').textContent = quoteSel ? @json(__('Quote')) : fmt(d.total);
          document.getElementById('ytxPriceHint').textContent = quoteSel ? @json(__('We confirm the price before any work')) : @json(__('Incl. options & location'));
          const submitBtn = form.querySelector('.ytx-booking-submit');
          if (submitBtn) submitBtn.textContent = (quoteSel ? @json(__('Continue to request a quote')) : @json(__('Continue to address & payment'))) + ' →';
          const note = document.getElementById('ytxPostcodeNote');
          if (fd.get('postcode')) note.textContent = d.postcode_surcharge > 0 ? @json(__('Location charge applied for your postcode.')) : @json(__('Standard price applies for your postcode.'));
        }).catch(() => {});
    }, 250);
  }

  form.addEventListener('change', e => {
    if (e.target.name === 'ytx_mode') {
      const tenant = e.target.value === 'tenant';
      document.getElementById('ytxSchedulingMode').value = tenant ? 'tenant' : 'now';
      document.getElementById('ytxModeNow').hidden = tenant;
      document.getElementById('ytxModeTenant').hidden = !tenant;
    }
    updateChoiceVisuals(); updateConditions(); updateSummaryDate(); refreshPrice();
  });
  form.addEventListener('input', e => { if (e.target.classList.contains('ytx-q-input') && e.target.type === 'number') refreshPrice(); });
  form.querySelectorAll('.ytx-q-qty-btn').forEach(b => b.addEventListener('click', () => {
    const i = b.parentElement.querySelector('input'); const v = (parseInt(i.value || '0', 10) || 0) + parseInt(b.dataset.qty, 10);
    const min = i.min !== '' ? parseInt(i.min, 10) : 0, max = i.max !== '' ? parseInt(i.max, 10) : Infinity;
    i.value = Math.max(min, Math.min(max, v)); refreshPrice();
  }));
  document.getElementById('ytxCheckPostcode').addEventListener('click', refreshPrice);
  document.getElementById('ytxPostcode').addEventListener('blur', refreshPrice);

  form.addEventListener('submit', e => {
    let ok = true;
    updateConditions();
    form.querySelectorAll('.ytx-q').forEach(card => {
      const err = card.querySelector('.ytx-q-error');
      const a = qAnswer(card);
      const missing = !card.hidden && card.dataset.required === 'true' && (Array.isArray(a) ? !a.length : a === '');
      if (err) err.hidden = !missing;
      if (missing) ok = false;
    });
    const pc = document.getElementById('ytxPostcode');
    document.getElementById('ytxPostcodeError').hidden = pc.value.trim() !== '';
    if (pc.value.trim() === '') ok = false;
    if (document.getElementById('ytxSchedulingMode').value === 'tenant') {
      const n = document.getElementById('ytxTenantName').value.trim(), em = document.getElementById('ytxTenantEmail').value.trim(), ph = document.getElementById('ytxTenantPhone').value.trim();
      const bad = !n || (!em && !ph); document.getElementById('ytxTenantError').hidden = !bad; if (bad) ok = false;
    } else {
      const bad = !document.getElementById('ytxBookingDate').value || !document.getElementById('ytxTimeSlot').value;
      document.getElementById('ytxDateError').hidden = !bad; if (bad) ok = false;
    }
    if (!ok) { e.preventDefault(); const first = form.querySelector('.ytx-q-error:not([hidden])'); if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
    document.getElementById('ytxAnswersJson').value = JSON.stringify(collectAnswers());
  });

  updateChoiceVisuals(); updateConditions(); updateSummaryDate();
})();
</script>
@endpush
