@extends('frontend.layout')

@section('pageHeading')
  {{ __('Leave a Review') }}
@endsection

@section('content')
  <div class="authentication-area ptb-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
          <div class="main-form p-0">
            <div class="title">
              <h4 class="mb-20">{{ __('Leave a Verified Review') }}</h4>
            </div>

            @php
              $serviceName = optional(optional($review->booking)->serviceContent->first())->name
                ?: optional(optional($review->service)->content->first())->name
                ?: __('Service appointment');
            @endphp

            <p class="mb-20">{{ __('Booking') }}: <strong>{{ $review->booking->order_number }}</strong></p>
            <p class="mb-20">{{ __('Service') }}: <strong>{{ $serviceName }}</strong></p>

            <form action="{{ route('frontend.verified_review.store', $review->token) }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group mb-20">
                <label class="form-label color-dark">{{ __('Rating') }}<span class="color-red">*</span></label>
                <select name="rating" class="form-control" required>
                  <option value="">{{ __('Select rating') }}</option>
                  @for ($rating = 5; $rating >= 1; $rating--)
                    <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }} {{ __('Stars') }}</option>
                  @endfor
                </select>
                @error('rating')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

              <div class="form-group mb-20">
                <label class="form-label color-dark">{{ __('Review') }}<span class="color-red">*</span></label>
                <textarea name="comment" class="form-control" rows="6" required>{{ old('comment') }}</textarea>
                @error('comment')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

              <div class="form-group mb-20">
                <label class="form-label color-dark">{{ __('Image') }}</label>
                <input type="file" name="image" class="form-control" accept="image/png,image/jpeg,image/webp">
                @error('image')
                  <p class="text-danger mt-2">{{ $message }}</p>
                @enderror
              </div>

              <div class="text-center mt-10">
                <button type="submit" class="btn btn-lg btn-primary btn-gradient w-100">{{ __('Submit Review') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
