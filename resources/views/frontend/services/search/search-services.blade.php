<div class="row">
  @php
    $featuredServiceCount = $featuredServices->count();
    $serviceCount = $services->total();
    $serach_total = $featuredServiceCount + $serviceCount;
  @endphp
  <input type="hidden" id="countServie" value="{{ $serach_total }}">
  @if ($serach_total > 0)
    @foreach ($featuredServices as $service)
      <div class="col-lg-4 col-sm-6" data-aos="fade-up">
        <div class="product-default border radius-md p-15 mb-25 border featured">
          <figure class="product-img mb-15">
            <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
              title="{{ $service->name }}" target="_self" class="lazy-container radius-sm ratio ratio-2-3">
              <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="{{ $service->name }}">
            </a>

          </figure>
          <div class="product-details">
            <div class="d-flex align-items-center justify-content-between gap-2">
              <a href="{{ route('frontend.services', ['category_id' => $service->categoryid]) }}">
                <span class="tag font-sm">{{ $service->categoryName }}</span>
              </a>
              @if (Auth::guard('web')->check())
                @php
                  $user_id = Auth::guard('web')->user()->id;
                  $checkWishList = checkWishList($service->id, $user_id);
                @endphp
              @else
                @php
                  $checkWishList = false;
                @endphp
              @endif
              <a href="{{ $checkWishList == false ? route('addto.wishlist', $service->id) : route('remove.wishlist', $service->id) }}"
                class="btn btn-icon border radius-sm {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                <i class="fal fa-heart"></i>
              </a>
            </div>
            <h6 class="product-title mb-0">
              <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                target="_self" title="{{ $service->name }}">
                {{ truncateString($service->name, 60) }}
              </a>
            </h6>
            <input type="hidden" value="{{ $service->language_id }}">
            @if ($service->zoom_meeting == 1)
              <span class="font-sm icon-start"><i class="fal fa-video"></i>{{ __('Online') }}</span>
            @endif
            <div class="d-flex align-items-center justify-content-between gap-2 mt-10">
              <div class="product-price">
                @if ($service->hasPricePlaceholder())
                  <span class="h6 new-price">{{ $service->price_placeholder }}</span>
                @else
                  <span class="h6 new-price">{{ symbolPrice($service->price) }}</span>
                  <span
                    class="prev-price font-sm">{{ $service->prev_price ? symbolPrice($service->prev_price) : '' }}</span>
                @endif
              </div>
              <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <button type="submit" class="btn btn-sm btn-outline-2" title="{{ __('Add to Cart') }}">
                  <i class="fal fa-shopping-cart me-1"></i>{{ __('Add to Cart') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
    @foreach ($services as $service)
      <div class="col-lg-4 col-sm-6" data-aos="fade-up">
        <div class="product-default border radius-md p-15 mb-25">
          <figure class="product-img mb-15">
            <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
              title="Image" target="_self" class="lazy-container radius-sm ratio ratio-2-3">
              <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                data-src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="Service">
            </a>

          </figure>
          <div class="product-details">
            <div class="d-flex align-items-center justify-content-between gap-2">
              <a href="{{ route('frontend.services', ['category_id' => $service->categoryid]) }}">
                <span class="tag font-sm">{{ $service->categoryName }}</span>
              </a>
              @if (Auth::guard('web')->check())
                @php
                  $user_id = Auth::guard('web')->user()->id;
                  $checkWishList = checkWishList($service->id, $user_id);
                @endphp
              @else
                @php
                  $checkWishList = false;
                @endphp
              @endif
              <a href="{{ $checkWishList == false ? route('addto.wishlist', $service->id) : route('remove.wishlist', $service->id) }}"
                class="btn btn-icon border radius-sm {{ $checkWishList == false ? '' : 'wishlist-active' }}"
                title="{{ $checkWishList == false ? __('Save to Wishlist') : __('Saved') }}">
                <i class="fal fa-heart"></i>
              </a>
            </div>
            <h6 class="product-title mb-0">
              <a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}"
                target="_self" title="{{ $service->name }}">
                {{ truncateString($service->name, 60) }}
              </a>
            </h6>
            <input type="hidden" value="{{ $service->language_id }}">
            @if ($service->zoom_meeting == 1)
              <span class="font-sm icon-start"><i class="fal fa-video"></i>{{ __('Online') }}</span>
            @endif
            <div class="d-flex align-items-center justify-content-between gap-2 mt-10">
              <div class="product-price">
                @if ($service->hasPricePlaceholder())
                  <span class="h6 new-price">{{ $service->price_placeholder }}</span>
                @else
                  <span class="h6 new-price">{{ symbolPrice($service->price) }}</span>
                  <span
                    class="prev-price font-sm">{{ $service->prev_price ? symbolPrice($service->prev_price) : '' }}</span>
                @endif
              </div>
              <form action="{{ route('frontend.services.cart.quick_add') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <button type="submit" class="btn btn-sm btn-outline-2" title="{{ __('Add to Cart') }}">
                  <i class="fal fa-shopping-cart me-1"></i>{{ __('Add to Cart') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @else
    <h4 class="text-center p-5">{{ __('NO SERVICE FOUND') . '!' }}</h4>
  @endif
</div>
<!--pagination -->
<nav class="pagination-nav pb-25" data-aos="fade-up">
  <ul class="pagination justify-content-center">
    {{ $services->appends([
            'category_id' => request()->input('category_id'),
            'min_val' => request()->input('min_val'),
            'max_val' => request()->input('max_val'),
            'rating' => request()->input('rating'),
            'sort_val' => request()->input('sort_val'),
        ])->links() }}

  </ul>
</nav>
