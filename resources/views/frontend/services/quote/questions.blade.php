@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ __('Get a Quote') }} &mdash; {{ $serviceName }}
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => __('Get a Quote') . ' — ' . $serviceName,
  ])

  <div class="contact-area ptb-100">
    <div class="container">
      <div class="row gx-xl-5">
        <div class="col-lg-8 mx-auto mb-30">
          <div class="mb-40 text-center">
            <h2>{{ __('Get a Quote') }}</h2>
            <p>{{ __('Tell us what you need for') }} <strong>{{ $serviceName }}</strong> {{ __('and we will send you a custom price.') }}</p>
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

          <form action="{{ route('frontend.service_quote.submit', $serviceId) }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if ($questions->isNotEmpty())
              <h4 class="mb-20">{{ __('A few questions') }}</h4>
              @foreach ($questions as $question)
                <div class="form-group mb-20">
                  <label>
                    {{ $question->title }}
                    @if ($question->is_required)
                      <span class="text-danger">*</span>
                    @endif
                  </label>

                  @if ($question->isUploadType())
                    <input type="file" name="question_files[{{ $question->id }}]" class="form-control"
                      accept="{{ $question->type === 'photo_upload' ? 'image/jpeg,image/png,image/jpg' : '.pdf,.doc,.docx,image/jpeg,image/png' }}"
                      {{ $question->is_required ? 'required' : '' }}>
                  @elseif ($question->type === 'single_select' || $question->type === 'dropdown')
                    <select name="answers[{{ $question->id }}]" class="form-control" {{ $question->is_required ? 'required' : '' }}>
                      <option value="">{{ __('-- Select --') }}</option>
                      @foreach ($question->options as $option)
                        <option value="{{ $option->id }}" {{ old('answers.' . $question->id) == $option->id ? 'selected' : '' }}>{{ $option->option_text }}</option>
                      @endforeach
                    </select>
                  @elseif ($question->type === 'multi_select')
                    @foreach ($question->options as $option)
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" id="q{{ $question->id }}_o{{ $option->id }}">
                        <label class="form-check-label" for="q{{ $question->id }}_o{{ $option->id }}">{{ $option->option_text }}</label>
                      </div>
                    @endforeach
                  @elseif ($question->type === 'yes_no')
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="1" id="q{{ $question->id }}_yes" {{ $question->is_required ? 'required' : '' }}>
                      <label class="form-check-label" for="q{{ $question->id }}_yes">{{ __('Yes') }}</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="0" id="q{{ $question->id }}_no">
                      <label class="form-check-label" for="q{{ $question->id }}_no">{{ __('No') }}</label>
                    </div>
                  @elseif ($question->type === 'long_text')
                    <textarea name="answers[{{ $question->id }}]" class="form-control" rows="4" {{ $question->is_required ? 'required' : '' }}>{{ old('answers.' . $question->id) }}</textarea>
                  @elseif ($question->type === 'number' || $question->type === 'quantity')
                    <input type="number" name="answers[{{ $question->id }}]" class="form-control"
                      @if ($question->type === 'quantity')
                        min="{{ $question->quantity_min ?? 0 }}"
                        @if ($question->quantity_max) max="{{ $question->quantity_max }}" @endif
                        value="{{ old('answers.' . $question->id, $question->quantity_default) }}"
                      @else
                        value="{{ old('answers.' . $question->id) }}"
                      @endif
                      {{ $question->is_required ? 'required' : '' }}>
                  @elseif ($question->type === 'date')
                    <input type="date" name="answers[{{ $question->id }}]" class="form-control" value="{{ old('answers.' . $question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                  @elseif ($question->type === 'time')
                    <input type="time" name="answers[{{ $question->id }}]" class="form-control" value="{{ old('answers.' . $question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                  @else
                    {{-- short_text, address, postcode all render as a plain text input --}}
                    <input type="text" name="answers[{{ $question->id }}]" class="form-control" value="{{ old('answers.' . $question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                  @endif
                </div>
              @endforeach
            @endif

            <h4 class="mb-20">{{ __('Your details') }}</h4>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <input type="text" name="name" class="form-control" required placeholder="{{ __('Full name') }}" value="{{ old('name', $prefill['name']) }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <input type="email" name="email" class="form-control" required placeholder="{{ __('Email address') }}" value="{{ old('email', $prefill['email']) }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <input type="tel" name="phone" class="form-control" required placeholder="{{ __('Phone number') }}" value="{{ old('phone', $prefill['phone']) }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <input type="text" name="postcode" class="form-control" required placeholder="{{ __('Postcode') }}" value="{{ old('postcode') }}">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group mb-20">
                  <input type="text" name="address" class="form-control" required placeholder="{{ __('Full job address') }}" value="{{ old('address') }}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <select name="preferred_contact_method" class="form-control">
                    <option value="">{{ __('Preferred contact method (optional)') }}</option>
                    <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                    <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>{{ __('Phone') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <input type="date" name="preferred_date" class="form-control" placeholder="{{ __('Preferred appointment date (optional)') }}" value="{{ old('preferred_date') }}">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group mb-20">
                  <textarea name="description" class="form-control" rows="4" placeholder="{{ __('Anything else we should know? (optional)') }}">{{ old('description') }}</textarea>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group mb-20">
                  <label>{{ __('Photos or documents (optional)') }}</label>
                  <input type="file" name="attachments[]" class="form-control" multiple accept="image/jpeg,image/png,.pdf,.doc,.docx">
                </div>
              </div>
            </div>

            <button type="submit" class="btn btn-lg btn-primary">{{ __('Submit quote request') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
