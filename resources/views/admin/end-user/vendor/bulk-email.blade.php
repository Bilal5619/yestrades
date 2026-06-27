@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Send Email to Vendors') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Vendor Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Send Email') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <form action="{{ route('admin.vendor_management.bulk_email.queue') }}" method="post">
          @csrf
          <div class="card-header">
            <div class="card-title d-inline-block">{{ __('Bulk Vendor Email') }}</div>
            <a class="btn btn-info btn-sm float-right d-inline-block" href="{{ route('admin.vendor_management.bulk_email.logs') }}">
              <span class="btn-label">
                <i class="fas fa-history"></i>
              </span>
              {{ __('Email Logs') }}
            </a>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-lg-8 offset-lg-2">
                <div class="form-group">
                  <label>{{ __('Send To') . '*' }}</label>
                  <select name="send_to" id="send_to" class="form-control select2">
                    <option value="all" @selected(old('send_to') == 'all')>{{ __('All registered vendors') }}</option>
                    <option value="active" @selected(old('send_to') == 'active')>{{ __('Active vendors only') }}</option>
                    <option value="verified" @selected(old('send_to') == 'verified')>{{ __('Verified vendors only') }}</option>
                    <option value="specific" @selected(old('send_to') == 'specific')>{{ __('Specific selected vendors') }}</option>
                  </select>
                  @if ($errors->has('send_to'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('send_to') }}</p>
                  @endif
                </div>

                <div class="form-group" id="specific-vendors-wrap">
                  <label>{{ __('Select Vendors') . '*' }}</label>
                  <select name="vendor_ids[]" class="form-control select2" multiple data-placeholder="{{ __('Search by vendor name or email') }}">
                    @foreach ($vendors as $vendor)
                      @php
                        $vendorName = optional($vendor->vendor_info)->name ?: $vendor->username;
                      @endphp
                      <option value="{{ $vendor->id }}" @selected(collect(old('vendor_ids', []))->contains($vendor->id))>
                        {{ $vendorName }} - {{ $vendor->email }}
                      </option>
                    @endforeach
                  </select>
                  @if ($errors->has('vendor_ids'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('vendor_ids') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Subject') . '*' }}</label>
                  <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" placeholder="{{ __('Enter Mail Subject') }}">
                  @if ($errors->has('subject'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('subject') }}</p>
                  @endif
                </div>

                <div class="form-group">
                  <label>{{ __('Message') . '*' }}</label>
                  <textarea class="summernote" name="message" data-height="300" placeholder="{{ __('Write Your Mail') }}">{{ old('message') }}</textarea>
                  @if ($errors->has('message'))
                    <p class="mt-2 mb-0 text-danger">{{ $errors->first('message') }}</p>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer">
            <div class="row">
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-success">
                  {{ __('Queue Email') }}
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    'use strict';

    function toggleSpecificVendors() {
      var sendTo = $('#send_to').val();
      $('#specific-vendors-wrap').toggle(sendTo === 'specific');
    }

    $('#send_to').on('change', toggleSpecificVendors);
    toggleSpecificVendors();
  </script>
@endsection
