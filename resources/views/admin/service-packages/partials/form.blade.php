@csrf
<div class="row">
  <div class="col-lg-8">
    <div class="form-group">
      <label>{{ __('Package Title') }} *</label>
      <input type="text" class="form-control" name="title" value="{{ old('title', $package->title) }}" required>
      @error('title') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Slug') }} *</label>
      <input type="text" class="form-control" name="slug" value="{{ old('slug', $package->slug) }}" required>
      @error('slug') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-12">
    <div class="form-group">
      <label>{{ __('Short Description') }}</label>
      <textarea class="form-control" name="short_description" rows="3">{{ old('short_description', $package->short_description) }}</textarea>
      @error('short_description') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-12">
    <div class="form-group">
      <label>{{ __('Full Description') }}</label>
      <textarea class="form-control summernote" name="description" rows="6">{{ old('description', $package->description) }}</textarea>
      @error('description') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Image/Icon') }}</label>
      @if ($package->image)
        <div class="mb-2">
          <img src="{{ asset('assets/img/services/' . $package->image) }}" alt="" style="max-width:120px;border-radius:6px;">
        </div>
      @endif
      <input type="file" class="form-control" name="image">
      @error('image') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Normal Price') }} *</label>
      <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price', $package->price) }}" required>
      @error('price') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Offer Price') }}</label>
      <input type="number" step="0.01" class="form-control" name="offer_price" value="{{ old('offer_price', $package->offer_price) }}">
      @error('offer_price') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Offer Label') }}</label>
      <input type="text" class="form-control" name="offer_label" value="{{ old('offer_label', $package->offer_label) }}">
      @error('offer_label') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Price Placeholder') }}</label>
      <input type="text" class="form-control" name="price_placeholder" value="{{ old('price_placeholder', $package->price_placeholder) }}" placeholder="{{ __('e.g., From £110') }}">
      <small class="form-text text-muted">{{ __('Leave empty for fixed package price. Enter text to show instead of price and make cart price pending until questions are answered.') }}</small>
      @error('price_placeholder') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Status') }}</label>
      <select class="form-control" name="is_active">
        <option value="1" {{ old('is_active', $package->is_active ?? 1) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
        <option value="0" {{ old('is_active', $package->is_active ?? 1) == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
      </select>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Show on Annual Safety Checks & Packages Section') }}</label>
      <select class="form-control" name="show_in_renewal_section">
        @php
          $showInRenewalSection = old('show_in_renewal_section', $package->exists ? $package->show_in_renewal_section : 1);
        @endphp
        <option value="1" {{ $showInRenewalSection == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
        <option value="0" {{ $showInRenewalSection == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
      </select>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="form-group">
      <label>{{ __('Homepage Section Sort Order') }}</label>
      <input type="number" class="form-control" name="renewal_section_sort_order" value="{{ old('renewal_section_sort_order', $package->renewal_section_sort_order ?? 0) }}">
    </div>
  </div>
  <div class="col-lg-12">
    <div class="form-group">
      <label>{{ __('Included Services') }} *</label>
      <select class="form-control select2" name="services[]" multiple required>
        @foreach ($services as $service)
          <option value="{{ $service->id }}" {{ in_array((string) $service->id, old('services', $selectedServices ?? []), true) ? 'selected' : '' }}>
            {{ $service->display_name }}
          </option>
        @endforeach
      </select>
      @error('services') <p class="text-danger mb-0">{{ $message }}</p> @enderror
    </div>
  </div>
</div>
<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
