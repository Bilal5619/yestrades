<div class="row">
  @foreach ([['first_name','First name'],['last_name','Last name'],['email','Email address'],['phone','Phone number'],['city','Current town/city'],['postcode','Postcode']] as $field)
    <div class="col-md-6 mb-3">
      <label for="{{ $field[0] }}">{{ $field[1] }} <span class="text-danger">*</span></label>
      <input id="{{ $field[0] }}" name="{{ $field[0] }}" value="{{ old($field[0]) }}" class="form-control @error($field[0]) is-invalid @enderror" @if($field[0] === 'email') type="email" @else type="text" @endif required>
      @error($field[0])<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  @endforeach

  @if ($general ?? false)
    <div class="col-md-6 mb-3"><label for="areas_of_interest">Areas of interest <span class="text-danger">*</span></label><input id="areas_of_interest" name="areas_of_interest" value="{{ old('areas_of_interest') }}" class="form-control @error('areas_of_interest') is-invalid @enderror" required>@error('areas_of_interest')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6 mb-3"><label for="preferred_employment_type">Preferred employment type</label><input id="preferred_employment_type" name="preferred_employment_type" value="{{ old('preferred_employment_type') }}" class="form-control"></div>
  @else
    <div class="col-md-6 mb-3"><label for="linkedin_url">LinkedIn profile URL</label><input id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url') }}" class="form-control @error('linkedin_url') is-invalid @enderror" type="url">@error('linkedin_url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6 mb-3"><label for="portfolio_url">Portfolio or website URL</label><input id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url') }}" class="form-control @error('portfolio_url') is-invalid @enderror" type="url">@error('portfolio_url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  @endif

  <div class="col-md-6 mb-3"><label for="right_to_work">Right to work in the UK <span class="text-danger">*</span></label><select id="right_to_work" name="right_to_work" class="form-control @error('right_to_work') is-invalid @enderror" required><option value="">Select</option>@foreach (['Yes','No','Prefer to discuss'] as $option)<option value="{{ $option }}" @selected(old('right_to_work') === $option)>{{ $option }}</option>@endforeach</select>@error('right_to_work')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <div class="col-md-6 mb-3"><label for="sponsorship_required">Require visa sponsorship now or in the future? <span class="text-danger">*</span></label><select id="sponsorship_required" name="sponsorship_required" class="form-control @error('sponsorship_required') is-invalid @enderror" required><option value="">Select</option>@foreach (['Yes','No','Prefer to discuss'] as $option)<option value="{{ $option }}" @selected(old('sponsorship_required') === $option)>{{ $option }}</option>@endforeach</select>@error('sponsorship_required')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

  <div class="col-md-6 mb-3"><label for="cv">CV upload (PDF, DOC, DOCX, max 5 MB) <span class="text-danger">*</span></label><input id="cv" name="cv" class="form-control-file @error('cv') is-invalid @enderror" type="file" accept=".pdf,.doc,.docx" required>@error('cv')<div class="text-danger small">{{ $message }}</div>@enderror</div>
  <div class="col-md-6 mb-3"><label for="cover_letter_file">Cover letter file (optional)</label><input id="cover_letter_file" name="cover_letter_file" class="form-control-file @error('cover_letter_file') is-invalid @enderror" type="file" accept=".pdf,.doc,.docx">@error('cover_letter_file')<div class="text-danger small">{{ $message }}</div>@enderror</div>

  <div class="col-12 mb-3"><label for="cover_letter_text">Cover letter or message</label><textarea id="cover_letter_text" name="cover_letter_text" class="form-control" rows="5">{{ old('cover_letter_text') }}</textarea></div>

  @if (!($general ?? false))
    <div class="col-md-6 mb-3"><label for="earliest_start_date">Earliest available start date</label><input id="earliest_start_date" name="earliest_start_date" value="{{ old('earliest_start_date') }}" class="form-control" type="date"></div>
    <div class="col-md-6 mb-3"><label for="expected_salary">Expected salary</label><input id="expected_salary" name="expected_salary" value="{{ old('expected_salary') }}" class="form-control"></div>
    <div class="col-12 mb-3"><label for="additional_information">Additional information</label><textarea id="additional_information" name="additional_information" class="form-control" rows="4">{{ old('additional_information') }}</textarea></div>
    @foreach ($job->questions as $question)
      <div class="col-12 mb-3"><label for="question_{{ $question->id }}">{{ $question->question }} @if($question->is_required)<span class="text-danger">*</span>@endif</label>@if($question->help_text)<p class="small text-muted mb-1">{{ $question->help_text }}</p>@endif
        @php $name = 'questions[' . $question->id . ']'; $oldValue = old('questions.' . $question->id); @endphp
        @if ($question->question_type === 'long_text')<textarea id="question_{{ $question->id }}" name="{{ $name }}" class="form-control" rows="4">{{ $oldValue }}</textarea>
        @elseif ($question->question_type === 'yes_no')<select id="question_{{ $question->id }}" name="{{ $name }}" class="form-control"><option value="">Select</option><option value="Yes" @selected($oldValue === 'Yes')>Yes</option><option value="No" @selected($oldValue === 'No')>No</option></select>
        @elseif ($question->question_type === 'single_choice')<select id="question_{{ $question->id }}" name="{{ $name }}" class="form-control"><option value="">Select</option>@foreach (($question->options ?? []) as $option)<option value="{{ $option }}" @selected($oldValue === $option)>{{ $option }}</option>@endforeach</select>
        @elseif ($question->question_type === 'multiple_choice')@foreach (($question->options ?? []) as $option)<div class="form-check"><input id="question_{{ $question->id }}_{{ $loop->index }}" class="form-check-input" type="checkbox" name="{{ $name }}[]" value="{{ $option }}" @checked(is_array($oldValue) && in_array($option, $oldValue))><label class="form-check-label" for="question_{{ $question->id }}_{{ $loop->index }}">{{ $option }}</label></div>@endforeach
        @elseif ($question->question_type === 'date')<input id="question_{{ $question->id }}" name="{{ $name }}" value="{{ $oldValue }}" type="date" class="form-control">
        @elseif ($question->question_type === 'number')<input id="question_{{ $question->id }}" name="{{ $name }}" value="{{ $oldValue }}" type="number" step="any" class="form-control">
        @else<input id="question_{{ $question->id }}" name="{{ $name }}" value="{{ $oldValue }}" class="form-control">@endif
        @error('questions.' . $question->id)<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
    @endforeach
  @else
    <div class="col-md-6 mb-3"><label for="linkedin_url">LinkedIn URL</label><input id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url') }}" class="form-control" type="url"></div>
    <div class="col-md-6 mb-3"><label for="portfolio_url">Portfolio URL</label><input id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url') }}" class="form-control" type="url"></div>
  @endif

  <div class="col-12 mb-3"><div class="form-check"><input id="privacy_consent" name="privacy_consent" value="1" class="form-check-input @error('privacy_consent') is-invalid @enderror" type="checkbox" required><label class="form-check-label" for="privacy_consent">I confirm that the information provided is accurate and I agree that {{ $websiteInfo->website_title ?? config('app.name') }} may process my personal data for recruitment purposes in accordance with the <a href="{{ route('privacy_policy') }}" target="_blank" rel="noopener">Privacy Policy</a>.</label>@error('privacy_consent')<div class="text-danger small">{{ $message }}</div>@enderror</div></div>
  <div class="col-12"><button type="submit" class="btn btn-primary">Submit Application</button></div>
</div>
