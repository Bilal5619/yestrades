@if (!empty($renewalItems) && $renewalItems->count() > 0)
  <section class="service-area renewal-package-section pt-100 pb-60">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="section-title mb-50" data-aos="fade-up">
            <h2 class="title">{{ __('Annual Safety Checks & Packages') }}</h2>
          </div>
        </div>
      </div>
      <div class="row">
        @foreach ($renewalItems as $item)
          @php
            $isPackage = $item->renewal_item_type === 'package';
            $normalPrice = (float) $item->renewal_price;
            $offerPrice = $item->renewal_offer_price !== null ? (float) $item->renewal_offer_price : null;
            $activePrice = $offerPrice !== null ? $offerPrice : $normalPrice;
            $pricePlaceholder = $item->renewal_price_placeholder ?? null;
            $image = $item->renewal_image ?: 'noimage.jpg';
          @endphp
          <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
            <div class="product-default border radius-md p-15 mb-25 h-100">
              <figure class="product-img mb-15">
                @if ($isPackage)
                  <span class="lazy-container radius-sm ratio ratio-2-3">
                    <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                      data-src="{{ asset('assets/img/services/' . $image) }}" alt="{{ $item->renewal_title }}">
                  </span>
                @else
                  <a href="{{ route('frontend.service.details', ['slug' => $item->slug, 'id' => $item->id]) }}"
                    title="{{ $item->renewal_title }}" target="_self" class="lazy-container radius-sm ratio ratio-2-3">
                    <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                      data-src="{{ asset('assets/img/services/' . $image) }}" alt="{{ $item->renewal_title }}">
                  </a>
                @endif
              </figure>
              <div class="product-details">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                  <span class="tag font-sm">{{ $isPackage ? __('Package') : __('Service') }}</span>
                  @if (!empty($item->renewal_offer_label))
                    <span class="tag font-sm">{{ $item->renewal_offer_label }}</span>
                  @endif
                </div>
                <h6 class="product-title mb-1">{{ truncateString($item->renewal_title, 70) }}</h6>
                @if (!empty($item->renewal_description))
                  <p class="font-sm mb-10">{{ truncateString(strip_tags($item->renewal_description), 120) }}</p>
                @endif
                @if ($isPackage && $item->services->count() > 0)
                  <div class="font-sm mb-10">
                    <strong>{{ __('Includes') }}:</strong>
                    <ul class="mb-0 ps-3">
                      @foreach ($item->services as $includedService)
                        <li>{{ optional($includedService->content->first())->name ?? __('Service #:id', ['id' => $includedService->id]) }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
                <div class="d-flex align-items-center justify-content-between gap-2 mt-10">
                  <div class="product-price">
                    @if (!empty($pricePlaceholder))
                      <span class="h6 new-price">{{ $pricePlaceholder }}</span>
                    @else
                      <span class="h6 new-price">{{ symbolPrice($activePrice) }}</span>
                    @endif
                    @if (empty($pricePlaceholder) && $offerPrice !== null && $normalPrice > $offerPrice)
                      <span class="prev-price font-sm">{{ symbolPrice($normalPrice) }}</span>
                    @endif
                  </div>
                  <form action="{{ $isPackage ? route('frontend.services.cart.quick_add_package') : route('frontend.services.cart.quick_add') }}" method="POST" class="m-0">
                    @csrf
                    @if ($isPackage)
                      <input type="hidden" name="package_id" value="{{ $item->id }}">
                    @else
                      <input type="hidden" name="service_id" value="{{ $item->id }}">
                    @endif
                    <button type="submit" class="btn btn-sm btn-outline-2" title="{{ __('Book Now') }}">
                      <i class="fal fa-shopping-cart me-1"></i>{{ __('Book Now') }}
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
@endif
