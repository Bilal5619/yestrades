@php
  if (Auth::guard('web')->check()) {
    $svUserId = Auth::guard('web')->user()->id;
    $svInWishlist = checkWishList($service->id, $svUserId);
  } else {
    $svInWishlist = false;
  }
@endphp
<div class="sv-card">
  <div class="sv-card-media">
    <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
      title="{{ $service->name }}" target="_self">
      <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
        data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="{{ $service->name }}">
    </a>
    <a href="{{ route('frontend.services', ['category_id' => $service->categoryid]) }}"
      class="sv-card-tag">{{ $service->categoryName }}</a>
    <a href="{{ $svInWishlist == false ? route('addto.wishlist', $service->id) : route('remove.wishlist', $service->id) }}"
      class="sv-card-wish {{ $svInWishlist == false ? '' : 'wishlist-active' }}"
      title="{{ $svInWishlist == false ? __('Save to Wishlist') : __('Saved') }}">
      <i class="fal fa-heart"></i>
    </a>
    <span class="sv-price-chip">
      @if ($service->isQuoteType())
        {{ __('Quote Required') }}
      @elseif ($service->hasPricePlaceholder())
        {{ $service->price_placeholder }}
      @else
        {{ symbolPrice($service->price) }}
        @if ($service->prev_price)
          <del>{{ symbolPrice($service->prev_price) }}</del>
        @endif
      @endif
    </span>
    @if (!empty($featured))
      <span class="sv-featured-badge"><i class="fas fa-star"></i>{{ __('Featured') }}</span>
    @endif
  </div>
  <div class="sv-card-body">
    <h6 class="sv-card-title">
      <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
        target="_self" title="{{ $service->name }}">
        {{ truncateString($service->name, 60) }}
      </a>
    </h6>
    <input type="hidden" value="{{ $service->language_id }}">
    <div class="sv-card-meta">
      @if ($service->zoom_meeting == 1)
        <span><i class="fal fa-video"></i>{{ __('Online') }}</span>
      @endif
    </div>
    <div class="sv-card-footer">
      @if ($service->isQuoteType())
        <a href="{{ route('frontend.service_quote.questions', $service->id) }}" class="sv-card-btn" title="{{ __('Get a Quote') }}">
          <i class="fal fa-file-invoice"></i>{{ __('Get a Quote') }}
        </a>
      @else
        <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST" class="m-0 w-100">
          @csrf
          <input type="hidden" name="service_id" value="{{ $service->id }}">
          <button type="submit" class="sv-card-btn" title="{{ __('Book Now') }}">
            <i class="fal fa-calendar-check"></i>{{ __('Book Now') }}
          </button>
        </form>
      @endif
    </div>
  </div>
</div>
