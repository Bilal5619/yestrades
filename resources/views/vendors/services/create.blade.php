@extends('vendors.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Add Service') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('vendor.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Services Managment') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Add Service') }}</a>
      </li>
    </ul>
  </div>

  @php
    $vendor_id = Auth::guard('vendor')->user()->id;
    // Package limits removed from service creation. Vendors can always add a
    // service; it is submitted for admin approval before it appears live.
    $sliderImage = 999999; // unlimited gallery images
    $can_service_add = 1;
  @endphp
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Add Service') }}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ route('vendor.service_managment', ['language' => $defaultLang->code]) }}">
            <span class="btn-label">
              @php
                $fontSize = '12px';
              @endphp
              <i class="fas fa-backward" style="font-size: {{ $fontSize }}"></i>
            </span>
            {{ __('Back') }}
          </a>
        </div>

        <div class="card-body pt-5 pb-5">

          <div class="alert alert-danger pb-1 dis-none" id="service_erros">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <ul></ul>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <label for="" class="mb-2"><strong>{{ __('Gallery Images') }} *</strong></label>
              <form action="{{ route('vendor.service.imagesstore') }}" id="my-dropzone" enctype="multipart/formdata"
                class="dropzone create">
                @csrf
                <div class="fallback">
                  <input name="file" type="file" multiple />
                </div>
              </form>
              <p class="text-warning mt-2 mb-0">

                @if ($sliderImage === 999999)
                  <small>
                    {{ __('You can upload') }} {{ $sliderImage === 999999 ? __('unlimited') : $sliderImage }}
                    {{ __('images') }}.</small>
                @else
                  <small>
                    {{ __('Please note that you can upload a maximum of') }} {{ $sliderImage }}
                    @if ($sliderImage > 1)
                      {{ __('images') }}.
                    @else
                      {{ __('image') }}
                    @endif
                  </small>
                @endif

              </p>
              <p class="em text-danger mb-0" id="errslider_images"></p>

            </div>
            <div class="col-lg-12">
              <form id="serviceForm" action="{{ route('vendor.service_managment.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div id="sliders"></div>
                <input type="hidden" name="can_service_add" value="{{ $can_service_add }}">
                <input type="hidden" name="vendor_id" value="{{ Auth::guard('vendor')->user()->id }}">
                <div class="version border-0">
                  <div class="version-body">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label for="">{{ __('Featured Image') . '*' }}</label>
                          <br>
                          <div class="thumb-preview">
                            <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..." class="uploaded-img">
                          </div>

                          <div class="mt-3">
                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                              {{ __('Choose Image') }}
                              <input type="file" class="img-input" name="service_image">
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                    @php $currencyText = $currencyInfo->base_currency_text; @endphp
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>{{ __('Price') . '* (' . $currencyText . ')' }}</label>
                          <input type="number" class="form-control" name="price" value="{{ old('price') }}"
                            placeholder="{{ __('Enter Price') }}">
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>
                            {{ __('Previous Price') . ' (' . $currencyText . ')' }}</label>
                          <input type="number" class="form-control" name="prev_price" value="{{ old('price') }}"
                            placeholder="{{ __('Enter Price') }}">
                        </div>
                      </div>

                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>{{ __('Status') . '*' }}</label>
                          <select name="status" class="form-control">
                            <option selected="" disabled="">{{ __('Select a Status') }}</option>
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Deactive') }}</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="form-group">
                          <label>{{ __('Service Area Postcode') }}<span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="service_area_postcode"
                            value="{{ old('service_area_postcode') }}"
                            placeholder="{{ __('e.g. EH1, EH12, KY11') }}" required>
                          <small class="text-muted">{{ __('The postcode/area where you can provide this service.') }}</small>
                          @error('service_area_postcode')
                            <small class="text-danger d-block">{{ $message }}</small>
                          @enderror
                        </div>
                      </div>
                      <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('Zoom') }}</label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="zoom_meeting" value="1" class="selectgroup-input">
                                  <span class="selectgroup-button">{{ __('Enable') }}</span>
                                </label>

                                <label class="selectgroup-item">
                                  <input type="radio" name="zoom_meeting" value="0" class="selectgroup-input"
                                    checked="">
                                  <span class="selectgroup-button">{{ __('Disable') }}</span>
                                </label>
                              </div>
                              <p>
                                <small
                                  class="text-warning">{{ __('If you enable zoom, then you have to set your zoom credentials.') }}
                                </small>
                                <a target="_blank" class="link-primary " href="{{ route('vendor.plugins.index') }}">
                                  {{ __('Click to proceed') }}
                                </a>
                              </p>
                            </div>
                          </div>
                          <div class="col-lg-4">
                            <div class="form-group">
                              <label>{{ __('Google Calendar') }}</label>
                              <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                  <input type="radio" name="calender_status" value="1"
                                    class="selectgroup-input">
                                  <span class="selectgroup-button">{{ __('Enable') }}</span>
                                </label>

                                <label class="selectgroup-item">
                                  <input type="radio" name="calender_status" value="0"
                                    class="selectgroup-input" checked="">
                                  <span class="selectgroup-button">{{ __('Disable') }}</span>
                                </label>
                              </div>
                              <p>
                                <small class="text-warning">
                                  {{ 'If you enable calendar, then you have to set your calendar credentials' }}
                                </small>
                                <a target="_blank" class="link-primary" href="{{ route('vendor.plugins.index') }}">
                                  {{ __(' Click to proceed') }}
                                </a>
                              </p>
                            </div>
                          </div>

                    </div>
                  </div>

                  <div id="accordion" class="mt-5">
                    @foreach ($languages as $language)
                      <div class="version">
                        <div class="version-header" id="heading{{ $language->id }}">
                          <h5 class="mb-0">
                            <!--button type="button"
                              class="btn btn-link {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                              data-toggle="collapse" data-target="#collapse{{ $language->id }}"
                              aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                              aria-controls="collapse{{ $language->id }}">
                              {{ $language->name . __(' Language') }}
                              {{ $language->is_default == 1 ? '(Default)' : '' }}
                            </button-->
                          </h5>
                        </div>

                        <div id="collapse{{ $language->id }}"
                          class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                          aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                          <div class="version-body {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                            <div class="row">
                              <div class="col-lg-4">
                                <div class="form-group">
                                  <label>{{ __('Title') . '*' }}</label>
                                  <input type="text" class="form-control"
                                    value="{{ old($language->code . '_name') }}" name="{{ $language->code }}_name"
                                    placeholder="{{ __('Enter Service Title') }}">
                                </div>
                              </div>
                              <div class="col-lg-4">
                                @php
                                  $categories = App\Models\Services\ServiceCategory::where('language_id', $language->id)
                                      ->where('status', 1)
                                      ->get();
                                @endphp
                                <div class="form-group">
                                  <label for="">{{ __('Category') . '*' }}</label>
                                  <select id="category" name="{{ $language->code }}_category_id"
                                    class="form-control select2">
                                    <option selected disabled>{{ __('Select a category') }}</option>
                                    @foreach ($categories as $category)
                                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                  </select>
                                  <p id="err_service_id" class="mt-1 mb-0 text-danger em"></p>
                                </div>
                              </div>

                              <div class="col-lg-12">
                                <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                  <label>{{ __('Features') }} </label>
                                  <textarea name="{{ $language->code }}_features" class="form-control"></textarea>
                                  <p class="text-warning">
                                    {{ __('Each new line will be shown as a new feature in this service') }}</p>
                                </div>
                              </div>

                              <div class="col-lg-12">
                                <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                  <label>{{ __('Description') }} *</label>
                                  <textarea id="{{ $language->code }}_description" class="form-control summernote"
                                    name="{{ $language->code }}_description" data-height="300"></textarea>
                                </div>
                              </div>
                              <div class="col-lg-12">
                                <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                  <label>{{ __('Meta Keywords') }}</label>
                                  <input class="form-control" name="{{ $language->code }}_meta_keyword"
                                    placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>
                              </div>

                              <div class="col-lg-12">
                                <div class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                  <label>{{ __('Meta Description') }} </label>
                                  <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                    placeholder="{{ __('Enter Meta Description') }}"></textarea>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12">
                                @php $currLang = $language; @endphp
                                @foreach ($languages as $language)
                                  @continue($language->id == $currLang->id)
                                  <div class="form-check py-0">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox"
                                        onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                      <span class="form-check-sign">{{ __('Clone for') }} <strong
                                          class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                        {{ __('language') }}</span>
                                    </label>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>

                </div>
              </form>
            </div>
          </div>

          <div class="card-footer">
            <div class="row">
              <div class="col-12 text-center">
                <button type="submit" id="ServiceSubmit" class="btn btn-success">
                  {{ __('Save') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
  <script>
    'use strict';
    var storeUrl = "{{ route('vendor.service.imagesstore') }}";
    var removeUrl = "{{ route('vendor.service.imagermv') }}";
    var sliderDelete = "{{ route('vendor.service.slider.delete') }}";
    let galleryImages = "{{ $sliderImage }}";
  </script>
  <script src="{{ asset('assets/js/vendor-dropzone.js') }}"></script>
  <script src="{{ asset('assets/js/services.js') }}"></script>
@endsection
