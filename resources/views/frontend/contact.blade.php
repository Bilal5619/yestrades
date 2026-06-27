@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ !empty($pageHeading) ? $pageHeading->contact_page_title : __('Contact') }}
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_contact }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_contact }}
  @endif
@endsection

@section('content')
  @includeIf('frontend.partials.breadcrumb', [
      'breadcrumb' => $bgImg->breadcrumb,
      'title' => !empty($pageHeading) ? $pageHeading->contact_page_title : __('Contact'),
  ])

  <!--====================================================-->
  <!--============== Start Contact Section ===============-->
  <!--====================================================-->
  <div class="contact-area ptb-100">
    <div class="container">
      <div class="row gx-xl-5">
        <div class="col-lg-8 mx-auto mb-30" data-aos="fade-right">
          <div class="mb-40 text-center">
            <h2>{{ __('Send Us a Message') }}</h2>
            <p>{{ __('Tell us what you need help with and we will get back to you.') }}</p>
          </div>
          @if (Session::has('success'))
            <div class="alert alert-success">{{ __(Session::get('success')) }}</div>
          @endif
          @if (Session::has('error'))
            <div class="alert alert-success">{{ __(Session::get('error')) }}</div>
          @endif
          <form id="contactForm" action="{{ route('contact.send_mail') }}" method="post">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-20">
                  <input type="text" name="name" class="form-control" id="name"
                    placeholder="{{ __('Enter Your Full Name') }}" />
                  @error('name')
                    <div class="help-block with-errors text-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group mb-20">
                  <input type="email" name="email" class="form-control" id="email" required
                    data-error="Enter your email" placeholder="{{ __('Enter Your Email') }}" />
                  @error('email')
                    <div class="help-block with-errors text-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group mb-20">
                  <input type="text" name="subject" class="form-control" id="" required
                    placeholder="{{ __('Enter Email Subject') }}" />
                  @error('subject')
                    <div class="help-block with-errors text-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group mb-20">
                  <textarea name="message" id="message" class="form-control" cols="30" rows="8" required
                    placeholder="{{ __('Write Your Message') }}"></textarea>
                  @error('message')
                    <div class="help-block with-errors text-danger">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              @if (\App\Models\BasicSettings\Basic::recaptchaEnabled())
                <div class="col-md-12">
                  <div class="form-group mb-20">
                    {!! NoCaptcha::renderJs() !!}
                    {!! NoCaptcha::display() !!}
                    @error('g-recaptcha-response')
                      <div class="help-block with-errors text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              @endif
              <div class="col-md-12">
                <button type="submit" class="btn btn-lg btn-primary"
                  title="{{ __('Send message') }}">{{ __('Send') }}</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">
          <div class="card mb-30 color-1" data-aos="fade-up" data-aos-delay="100">
            <div class="icon">
              <i class="fal fa-phone-plus"></i>
            </div>
            <div class="card-text">
              @if (!empty($info->contact_number))
                <p><a href="tel:{{ $info->contact_number }}">{{ $info->contact_number }}</a></p>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card mb-30 color-2" data-aos="fade-up" data-aos-delay="200">
            <div class="icon">
              <i class="fal fa-map-marker-alt"></i>
            </div>
            <div class="card-text">
              <p><a href="javascript:void(0)">{{ __('United Kingdom') }}</a></p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card mb-30 color-3" data-aos="fade-up" data-aos-delay="300">
            <div class="icon">
              <i class="fal fa-envelope"></i>
            </div>
            <div class="card-text">
              @if (!empty($info->email_address))
                <p><a href="mailTo:{{ $info->email_address }}">{{ $info->email_address }}</a></p>
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="pb-70"></div>
    </div>

    @if (!empty(showAd(3)))
      <div class="text-center">
        {!! showAd(3) !!}
      </div>
    @endif
  </div>
  <!--============ End Contact Section =============-->
@endsection
