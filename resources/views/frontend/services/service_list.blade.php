@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->service_page_title }}
  @endif
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_services }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_services }}
  @endif
@endsection
@section('content')
  @php
    $svTitle = !empty($pageHeading) ? $pageHeading->service_page_title : __('Services');
  @endphp

  <!-- ══════════ Page banner start ══════════ -->
  <section class="sv-banner">
    <div class="sv-banner-dots"></div>
    <div class="container">
      <nav aria-label="breadcrumb" class="sv-breadcrumb">
        <a href="{{ route('index') }}">{{ __('Home') }}</a>
        <i class="fal fa-angle-right"></i>
        <span>{{ $svTitle }}</span>
      </nav>
      <h1>{{ $svTitle }}</h1>
      <p>{{ __('Browse trusted tradespeople and book online in minutes — clear pricing, no hidden fees.') }}</p>

      <div class="sv-banner-stats">
        <div class="sv-banner-stat">
          <strong id="banner-total-service">{{ $total_services }}</strong>
          <span>
            @if ($total_services == 1)
              {{ __('Service Available') }}
            @else
              {{ __('Services Available') }}
            @endif
          </span>
        </div>
        <div class="sv-banner-stat">
          <strong>{{ count($categories) }}</strong>
          <span>{{ __('Categories') }}</span>
        </div>
        <div class="sv-banner-stat">
          <strong><i class="fal fa-shield-check"></i></strong>
          <span>{{ __('Verified pros') }}</span>
        </div>
      </div>
    </div>
  </section>
  <!-- ══════════ Page banner end ══════════ -->

  <div class="sv-page">
    <div class="container">
      <!-- ══════════ Full-width category bar ══════════ -->
      <div class="sv-cat-bar" data-aos="fade-up">
        <button type="button" class="sv-cat-arrow sv-cat-prev" aria-label="{{ __('Scroll categories left') }}">
          <i class="fal fa-angle-left"></i>
        </button>
        <div class="sv-cat-scroll">
          <ul class="sv-cat-list">
            <li>
              <a href="javascript:void(0)" class="category-toggle sv-cat-pill {{ request()->category ? '' : 'active' }}">
                <i class="fal fa-grid-2"></i>{{ __('All') }}
                <span class="qty category_total_service">({{ $total_services }})</span>
              </a>
            </li>
            @foreach ($categories as $category)
              <li>
                <a href="javascript:void(0)"
                  class="category-toggle sv-cat-pill {{ request()->category == $category->slug ? 'active' : '' }}"
                  data-slug="{{ $category->slug }}">
                  <i class="{{ !empty($category->icon) ? $category->icon : 'fal fa-wrench' }}"></i>{{ $category->name }}
                  <span class="qty">({{ $category->service_count }})</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
        <button type="button" class="sv-cat-arrow sv-cat-next" aria-label="{{ __('Scroll categories right') }}">
          <i class="fal fa-angle-right"></i>
        </button>
        <a href="{{ route('frontend.services') }}" class="sv-cat-reset" title="{{ __('Reset All Filters') }}">
          <i class="fal fa-sync-alt"></i> <span>{{ __('Reset') }}</span>
        </a>
      </div>
      @if (!empty(showAd(1)))
        <div class="text-center mb-4">
          {!! showAd(1) !!}
        </div>
      @endif
      <!-- ══════════ /Full-width category bar ══════════ -->

      <div class="row">
        <div class="col-12">
          <div class="sv-toolbar" data-aos="fade-up">
            <div class="sv-toolbar-count">
              @if ($total_services > 1)
                <span id="total-service">{{ $total_services }}</span> {{ __('Services Found') }}
              @elseif ($total_services == 1)
                <span id="total-service">{{ $total_services }}</span> {{ __('Service Found') }}
              @else
                {{ __('No Service Available') }}
              @endif
            </div>
            <div class="sv-toolbar-actions">
              <div class="sv-sort">
                <label for="sv-sort-select"><i class="fal fa-arrow-down-arrow-up"></i> {{ __('Sort By') }}</label>
                <select id="sv-sort-select" name="sort" class="sort">
                  <option {{ request()->input('newest') == 'default' ? 'selected' : '' }} value="newest">
                    {{ __('Date : Newest on top') }}
                  </option>
                  <option {{ request()->input('sort') == 'oldest' ? 'selected' : '' }} value="oldest">
                    {{ __('Date : Oldest on top') }}
                  </option>
                  <option {{ request()->input('sort') == 'high-to-low' ? 'selected' : '' }} value="high-to-low">
                    {{ __('Price : High to Low') }}</option>
                  <option {{ request()->input('sort') == 'low-to-high' ? 'selected' : '' }} value="low-to-high">
                    {{ __('Price : Low to High') }}</option>
                </select>
              </div>
            </div>
          </div>

          <div id="search_container">
            {{-- Hidden for now (client request) — remove `false &&` to bring the packages section back --}}
            @if (false && !empty($renewalItems) && $renewalItems->count() > 0)
              <section class="renewal-package-section mb-40" data-aos="fade-up">
                <div class="section-title mb-25">
                  <h2 class="mb-0">{{ __('Annual Safety Checks & Packages') }}</h2>
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
                    <div class="col-xl-4 col-sm-6" data-aos="fade-up">
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
                                <i class="fal fa-calendar-check me-1"></i>{{ __('Book Now') }}
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </section>
            @endif

            <div class="row g-4">
              @forelse ($featuredServices as $service)
                <div class="col-xl-4 col-sm-6" data-aos="fade-up">
                  @include('frontend.services.partials.card', ['service' => $service, 'featured' => true])
                </div>
              @empty
              @endforelse

              @forelse ($services as $service)
                <div class="col-xl-4 col-sm-6" data-aos="fade-up">
                  @include('frontend.services.partials.card', ['service' => $service, 'featured' => false])
                </div>
              @empty
                @if ($featuredServices->count() == 0)
                  <div class="col-12">
                    <div class="sv-empty">
                      <i class="fal fa-magnifying-glass"></i>
                      <h4>{{ __('NO SERVICE FOUND') }}!</h4>
                      <p>{{ __('Try adjusting your filters or search a different category.') }}</p>
                    </div>
                  </div>
                @endif
              @endforelse
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
          </div>
          @if (!empty(showAd(3)))
            <div class="sv-ad-slot" data-aos="fade-up">
              {!! showAd(3) !!}
            </div>
          @endif
          <form id="searchForm" action="{{ route('frontend.services.category.search') }}" method="get">
            <input type="hidden" id="category" name="category" value="{{ request()->input('category') }}">
            <input type="hidden" id="min_val" name="min_val" value="{{ request()->input('min_val') }}">
            <input type="hidden" id="max_val" name="max_val" value="{{ request()->input('max_val') }}">
            <input type="hidden" id="rating" name="rating" value="{{ request()->input('rating') }}">
            <input type="hidden" id="sort_val" name="sort_val" value="{{ request()->input('sort_val') }}">
            <input type="hidden" id="page" value="{{ request()->input('page') }}">
            <input type="hidden" id="location_val" name="location_val"
              value="{{ request()->input('location_val') }}">
            <input type="hidden" id="service_title" name="service_title"
              value="{{ request()->input('service_title') }}">
            <input type="hidden" id="service_type" name="service_type"
              value="{{ request()->input('service_type') }}">
          </form>

          <!-- Spacer -->
          <div class="mb-15"></div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <style>
      /* ═══════════════ Services listing redesign — emerald & slate ═══════════════ */

      :root {
        --sv-primary: #0e9f6e;
        --sv-primary-dark: #0a7d57;
        --sv-primary-deep: #06432f;
        --sv-primary-soft: #e5f5ee;
        --sv-amber: #f5a524;
        --sv-ink: #101d2c;
        --sv-ink-2: #33415c;
        --sv-muted: #5e6b7e;
        --sv-line: #e4e9e6;
        --sv-bg: #f4f7f5;
        --sv-radius: 18px;
        --sv-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
        --sv-shadow-lg: 0 26px 60px rgba(16, 29, 44, 0.13);
      }

      /* ── Banner ───────────────────────────────────── */
      .sv-banner {
        position: relative;
        overflow: hidden;
        margin-top: 84px;
        padding: 56px 0 46px;
        background:
          radial-gradient(circle at 92% 8%, rgba(14, 159, 110, 0.5), transparent 40%),
          radial-gradient(circle at 6% 100%, rgba(245, 165, 36, 0.16), transparent 34%),
          linear-gradient(120deg, #0d1b16 0%, var(--sv-primary-deep) 100%);
        color: #ffffff;
      }

      .sv-banner-dots {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.5px, transparent 1.5px);
        background-size: 22px 22px;
        mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 65%);
        -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 65%);
        pointer-events: none;
      }

      .sv-banner .container {
        position: relative;
        z-index: 1;
      }

      .sv-breadcrumb {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 16px;
        color: #a9c7ba;
        font-size: 12.5px;
        font-weight: 700;
      }

      .sv-breadcrumb a {
        color: #dbeee3;
        text-decoration: none;
        transition: color 0.2s ease;
      }

      .sv-breadcrumb a:hover {
        color: #ffffff;
      }

      .sv-breadcrumb i {
        font-size: 10px;
        color: #6f9a84;
      }

      .sv-breadcrumb span {
        color: #8be3bf;
      }

      .sv-banner h1 {
        margin: 0 0 12px;
        color: #ffffff;
        font-size: clamp(28px, 3.4vw, 42px);
        font-weight: 900;
        letter-spacing: -0.015em;
      }

      .sv-banner p {
        max-width: 560px;
        margin: 0 0 30px;
        color: #bcd8cb;
        font-size: 15px;
        line-height: 1.7;
      }

      .sv-banner-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
      }

      .sv-banner-stat {
        display: flex;
        align-items: baseline;
        gap: 9px;
        padding: 12px 20px;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 999px;
        backdrop-filter: blur(6px);
      }

      .sv-banner-stat strong {
        color: #ffffff;
        font-size: 19px;
        font-weight: 900;
        line-height: 1;
      }

      .sv-banner-stat strong i {
        font-size: 15px;
        color: #8be3bf;
      }

      .sv-banner-stat span {
        color: #a9c7ba;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
      }

      /* ── Page wrapper ─────────────────────────────── */
      .sv-page {
        padding: 44px 0 90px;
        background: #ffffff;
      }

      /* ── Full-width category bar ──────────────────── */
      .sv-cat-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
      }

      .sv-cat-scroll {
        flex: 1;
        min-width: 0;
        background: var(--sv-bg);
        border: 1px solid var(--sv-line);
        border-radius: 999px;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
      }

      .sv-cat-scroll::-webkit-scrollbar {
        display: none;
      }

      .sv-cat-list {
        display: flex;
        flex-wrap: nowrap;
        gap: 8px;
        width: max-content;
        margin: 0;
        padding: 8px;
        list-style: none;
      }

      .sv-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 11px 18px;
        color: var(--sv-ink-2);
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 700;
        white-space: nowrap;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.18s ease;
      }

      .sv-cat-pill i {
        color: var(--sv-primary);
        font-size: 13px;
      }

      .sv-cat-pill:hover {
        color: var(--sv-primary-dark);
        border-color: rgba(14, 159, 110, 0.35);
      }

      .sv-cat-pill.active {
        color: #ffffff;
        background: linear-gradient(120deg, var(--sv-primary), var(--sv-primary-dark));
        border-color: transparent;
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.3);
      }

      .sv-cat-pill.active i {
        color: #ffffff;
      }

      .sv-cat-pill .qty {
        padding: 1px 8px;
        color: inherit;
        background: rgba(0, 0, 0, 0.06);
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
      }

      .sv-cat-pill.active .qty {
        background: rgba(255, 255, 255, 0.22);
      }

      .sv-cat-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        color: var(--sv-primary-dark);
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: 50%;
        font-size: 15px;
        box-shadow: 0 8px 20px rgba(16, 29, 44, 0.08);
        transition: all 0.2s ease;
      }

      .sv-cat-arrow:hover {
        color: #ffffff;
        background: var(--sv-primary);
        border-color: var(--sv-primary);
      }

      .sv-cat-arrow.is-hidden {
        display: none;
      }

      .sv-cat-arrow:disabled {
        opacity: 0.35;
        pointer-events: none;
      }

      .sv-cat-reset {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        flex: 0 0 auto;
        padding: 11px 16px;
        color: var(--sv-muted);
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
      }

      .sv-cat-reset:hover {
        color: var(--sv-primary-dark);
        border-color: rgba(14, 159, 110, 0.35);
        background: var(--sv-primary-soft);
      }

      @media (max-width: 575.98px) {
        .sv-cat-reset span {
          display: none;
        }
      }

      /* ── Sidebar (kept for backward compatibility, unused) ── */
      .sv-page .widget-area {
        position: sticky;
        top: 96px;
      }

      .sv-page .widget {
        margin-bottom: 20px;
        padding: 0;
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: var(--sv-radius);
        box-shadow: var(--sv-shadow);
        overflow: hidden;
      }

      .sv-page .widget .title {
        margin: 0;
      }

      .sv-page .widget .accordion-button {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 18px 20px;
        color: var(--sv-ink);
        background: #ffffff;
        border: 0;
        font-size: 14.5px;
        font-weight: 800;
        text-align: left;
      }

      .sv-page .widget .accordion-button::before {
        content: "\f0b0";
        font-family: "Font Awesome 6 Pro";
        font-weight: 400;
        color: var(--sv-primary);
        font-size: 15px;
      }

      .sv-page .widget .accordion-body {
        padding: 4px 14px 16px !important;
        margin-top: 0 !important;
      }

      .sv-page .widget-categories .list-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: none;
        overflow-y: visible;
      }

      .sv-page .widget-categories {
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 200px);
      }

      .sv-page .widget-categories #categories {
        display: flex;
        flex-direction: column;
        flex: 1;
      }

      .sv-page .widget-categories .accordion-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding-bottom: 20px !important;
      }

      .sv-page .widget-categories .list-item > a.category-toggle {
        padding: 14px 14px !important;
      }

      .sv-page .widget-categories .list-item {
        list-style: none;
      }

      /* Neutralise the theme's default radio-dot markers so our own layout applies */
      .sv-page .widget-categories .list-item > a::before,
      .sv-page .widget-categories .list-item > a::after {
        content: none !important;
      }

      .sv-page .widget-categories .list-item > a.category-toggle {
        display: flex !important;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 12px !important;
        color: var(--sv-ink-2) !important;
        background: transparent;
        border-radius: 12px;
        border-left: 3px solid transparent;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.4;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.18s ease;
      }

      .sv-page .widget-categories .list-item > a.category-toggle:hover {
        color: var(--sv-primary-dark) !important;
        background: var(--sv-bg);
      }

      .sv-page .widget-categories .list-item > a.category-toggle.active {
        color: var(--sv-primary-dark) !important;
        background: var(--sv-primary-soft);
        border-left-color: var(--sv-primary);
      }

      .sv-page .category-toggle-label {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
      }

      .sv-page .category-toggle-label i {
        flex: 0 0 auto;
        width: 8px;
        height: 8px;
        margin-top: 1px;
        display: inline-block;
        background: var(--sv-line);
        border-radius: 50%;
        transition: background 0.18s ease, transform 0.18s ease;
      }

      .sv-page .category-toggle.active .category-toggle-label i {
        background: var(--sv-primary);
        transform: scale(1.3);
      }

      .sv-page .category-toggle .qty {
        flex: 0 0 auto;
        margin-top: 1px;
        padding: 2px 9px;
        color: var(--sv-muted);
        background: var(--sv-bg);
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
      }

      .sv-page .category-toggle.active .qty {
        color: #ffffff;
        background: var(--sv-primary);
      }

      .sv-page .widget-select .form-group {
        margin-bottom: 16px !important;
      }

      .sv-page .widget-select label {
        display: block;
        margin-bottom: 7px !important;
        color: var(--sv-ink-2);
        font-size: 12.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
      }

      .sv-page .widget-select .form-control {
        min-height: 46px;
        padding: 0 14px;
        color: var(--sv-ink);
        background: var(--sv-bg);
        border: 1px solid var(--sv-line);
        border-radius: 12px;
        font-size: 13.5px;
        transition: border-color 0.2s ease, background 0.2s ease;
      }

      .sv-page .widget-select .form-control:focus {
        background: #ffffff;
        border-color: rgba(14, 159, 110, 0.45);
        box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.12);
        outline: none;
      }

      .sv-page .widget-select .text-center {
        padding: 0 14px 18px;
      }

      .sv-apply-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        min-height: 46px;
        padding: 0 20px;
        color: #ffffff;
        background: linear-gradient(120deg, var(--sv-primary), var(--sv-primary-dark));
        border: 0;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 800;
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.28);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .sv-apply-btn:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(14, 159, 110, 0.36);
      }

      .sv-reset-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        min-height: 46px;
        padding: 0 20px;
        color: var(--sv-ink-2);
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
      }

      .sv-reset-btn:hover {
        color: var(--sv-primary-dark);
        border-color: rgba(14, 159, 110, 0.4);
        background: var(--sv-primary-soft);
      }

      /* ── Toolbar ──────────────────────────────────── */
      .sv-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 28px;
        padding: 16px 20px;
        background: var(--sv-bg);
        border: 1px solid var(--sv-line);
        border-radius: var(--sv-radius);
      }

      .sv-toolbar-count {
        color: var(--sv-ink-2);
        font-size: 14.5px;
        font-weight: 700;
      }

      .sv-toolbar-count span {
        color: var(--sv-primary-dark);
        font-weight: 900;
      }

      .sv-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
      }

      .sv-filter-trigger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        color: var(--sv-primary-dark);
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
      }

      .sv-sort {
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .sv-sort label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        color: var(--sv-muted);
        font-size: 12.5px;
        font-weight: 700;
        white-space: nowrap;
      }

      .sv-sort label i {
        color: var(--sv-primary);
      }

      .sv-sort select {
        min-height: 42px;
        padding: 0 14px;
        color: var(--sv-ink);
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
      }

      .sv-sort select:focus {
        border-color: rgba(14, 159, 110, 0.45);
        outline: none;
      }

      /* ── Service card ─────────────────────────────── */
      .sv-card {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #ffffff;
        border-radius: var(--sv-radius);
        box-shadow: var(--sv-shadow);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .sv-card:hover {
        transform: translateY(-7px);
        box-shadow: var(--sv-shadow-lg);
      }

      .sv-card-media {
        position: relative;
        overflow: hidden;
        margin: 10px 10px 0;
        border-radius: 13px;
      }

      .sv-card-media > a {
        display: block;
      }

      .sv-card-media img {
        display: block;
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        transition: transform 0.45s ease;
      }

      .sv-card:hover .sv-card-media img {
        transform: scale(1.06);
      }

      .sv-card-tag {
        position: absolute;
        left: 10px;
        top: 10px;
        z-index: 2;
        max-width: calc(100% - 58px);
        padding: 6px 12px;
        color: var(--sv-primary-deep);
        background: #ffffff;
        border-radius: 999px;
        box-shadow: 0 6px 16px rgba(16, 29, 44, 0.12);
        font-size: 11px;
        font-weight: 800;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-decoration: none;
        transition: color 0.2s ease, background 0.2s ease;
      }

      .sv-card-tag:hover {
        color: #ffffff;
        background: var(--sv-primary);
      }

      .sv-card-media .sv-card-wish,
      .sv-card-media .sv-card-wish:focus,
      .sv-card-media .sv-card-wish:active,
      .sv-card-media .sv-card-wish:visited {
        position: absolute;
        right: 10px;
        top: 10px;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        margin: 0;
        padding: 0;
        color: var(--sv-ink);
        background: #ffffff;
        border: 0;
        border-radius: 50%;
        box-shadow: 0 6px 16px rgba(16, 29, 44, 0.14);
        font-size: 13px;
        line-height: 1;
        text-decoration: none;
        outline: none;
        overflow: hidden;
        transition: color 0.2s ease, background 0.2s ease;
      }

      .sv-card-wish::before,
      .sv-card-wish::after {
        content: none !important;
      }

      .sv-card-wish i {
        display: block;
        line-height: 1;
      }

      .sv-card-media .sv-card-wish:hover,
      .sv-card-media .sv-card-wish.wishlist-active {
        color: #ffffff;
        background: #e8506c;
      }

      .sv-price-chip {
        position: absolute;
        left: 10px;
        bottom: 10px;
        z-index: 2;
        display: inline-flex;
        align-items: baseline;
        gap: 7px;
        padding: 7px 13px;
        color: #ffffff;
        background: rgba(6, 67, 47, 0.88);
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 900;
        backdrop-filter: blur(4px);
      }

      .sv-price-chip del {
        color: #9fd3bd;
        font-size: 11px;
        font-weight: 600;
      }

      .sv-featured-badge {
        position: absolute;
        right: 10px;
        bottom: 10px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        color: #231303;
        background: linear-gradient(120deg, #ffc253, var(--sv-amber));
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        box-shadow: 0 8px 18px rgba(245, 165, 36, 0.35);
      }

      .sv-card-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        gap: 9px;
        padding: 16px;
      }

      .sv-card-title {
        margin: 0;
        font-size: 15.5px;
        line-height: 1.4;
        font-weight: 800;
      }

      .sv-card-title a {
        color: var(--sv-ink);
        text-decoration: none;
        transition: color 0.2s ease;
      }

      .sv-card-title a:hover {
        color: var(--sv-primary-dark);
      }

      .sv-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
      }

      .sv-card-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--sv-muted);
        font-size: 12px;
        font-weight: 600;
      }

      .sv-card-meta i {
        color: var(--sv-primary);
      }

      .sv-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: auto;
        padding-top: 6px;
      }

      .sv-card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 42px;
        padding: 0 16px;
        color: var(--sv-primary-dark);
        background: var(--sv-primary-soft);
        border: 0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.2s ease;
      }

      .sv-card-btn:hover {
        color: #ffffff;
        background: var(--sv-primary);
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.32);
      }

      /* ── Empty state ──────────────────────────────── */
      .sv-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        padding: 70px 24px;
        text-align: center;
        background: var(--sv-bg);
        border-radius: var(--sv-radius);
      }

      .sv-empty i {
        font-size: 34px;
        color: var(--sv-primary);
      }

      .sv-empty h4 {
        margin: 0;
        color: var(--sv-ink);
        font-weight: 800;
      }

      .sv-empty p {
        margin: 0;
        color: var(--sv-muted);
        font-size: 14px;
      }

      /* ── Ad slot ──────────────────────────────────── */
      .sv-ad-slot {
        margin-top: 8px;
        margin-bottom: 40px;
        text-align: center;
      }

      /* ── Pagination ───────────────────────────────── */
      .sv-page .pagination-nav {
        margin-top: 8px;
      }

      .sv-page .pagination {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
      }

      .sv-page .pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        color: var(--sv-ink-2);
        background: #ffffff;
        border: 1px solid var(--sv-line);
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.2s ease;
      }

      .sv-page .pagination .page-item .page-link:hover {
        color: var(--sv-primary-dark);
        border-color: rgba(14, 159, 110, 0.4);
        background: var(--sv-primary-soft);
      }

      .sv-page .pagination .page-item.active .page-link {
        color: #ffffff;
        background: linear-gradient(120deg, var(--sv-primary), var(--sv-primary-dark));
        border-color: transparent;
        box-shadow: 0 10px 22px rgba(14, 159, 110, 0.3);
      }

      .sv-page .pagination .page-item.disabled .page-link {
        color: #b7bfc9;
        background: #ffffff;
        opacity: 0.7;
      }

      /* ── Offcanvas (mobile filters) ───────────────── */
      .sv-page .offcanvas-header {
        border-bottom: 1px solid var(--sv-line);
      }

      .sv-page .offcanvas-title {
        color: var(--sv-ink);
        font-weight: 800;
      }

      /* ── Responsive ───────────────────────────────── */
      @media (max-width: 991.98px) {
        .sv-page .widget-area {
          position: static;
          top: auto;
        }

        .sv-page .widget-categories {
          min-height: 0;
        }
      }

      @media (max-width: 767.98px) {
        .sv-banner {
          padding: 42px 0 34px;
        }

        .sv-banner h1 {
          font-size: 27px;
        }

        .sv-banner p {
          font-size: 13.5px;
        }

        .sv-banner-stat {
          padding: 10px 15px;
        }

        .sv-banner-stat strong {
          font-size: 16px;
        }

        .sv-page {
          padding: 32px 0 60px;
        }

        .sv-toolbar {
          padding: 14px 16px;
        }

        .sv-toolbar-actions {
          width: 100%;
          justify-content: space-between;
        }

        .sv-sort {
          flex: 1;
        }

        .sv-sort select {
          width: 100%;
        }

        .sv-card-body {
          padding: 14px;
        }
      }

      @media (max-width: 575.98px) {
        .sv-banner-stats {
          gap: 10px;
        }

        .sv-banner-stat {
          flex: 1 1 calc(50% - 5px);
          justify-content: center;
        }

        .sv-sort label span {
          display: none;
        }
      }
    </style>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var catBar = document.querySelector('.sv-cat-bar');
        if (!catBar) {
          return;
        }

        var scroller = catBar.querySelector('.sv-cat-scroll');
        var prevBtn = catBar.querySelector('.sv-cat-prev');
        var nextBtn = catBar.querySelector('.sv-cat-next');

        if (!scroller || !prevBtn || !nextBtn) {
          return;
        }

        function updateArrows() {
          var overflow = scroller.scrollWidth - scroller.clientWidth;

          if (overflow <= 2) {
            prevBtn.classList.add('is-hidden');
            nextBtn.classList.add('is-hidden');
            return;
          }

          prevBtn.classList.remove('is-hidden');
          nextBtn.classList.remove('is-hidden');
          prevBtn.disabled = scroller.scrollLeft <= 2;
          nextBtn.disabled = scroller.scrollLeft >= overflow - 2;
        }

        prevBtn.addEventListener('click', function () {
          scroller.scrollBy({ left: -Math.max(scroller.clientWidth * 0.7, 180), behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function () {
          scroller.scrollBy({ left: Math.max(scroller.clientWidth * 0.7, 180), behavior: 'smooth' });
        });

        scroller.addEventListener('scroll', updateArrows, { passive: true });
        window.addEventListener('resize', updateArrows);
        window.addEventListener('load', updateArrows);
        updateArrows();

        var scrollObserver = new MutationObserver(updateArrows);
        scrollObserver.observe(scroller, { childList: true, subtree: true });
      });
    </script>
  @endpush
@endsection
@section('script')
  <script src="{{ asset('assets/frontend/js/service_search.js') }}?v={{ filemtime(public_path('assets/frontend/js/service_search.js')) }}"></script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="{{ $authorizeUrl }}"></script>
  <script>
    let searchUrl = "{{ route('frontend.services.category.search') }}";
    let stripe_key = "{{ $stripe_key }}";
    let authorize_login_key = "{{ $authorize_login_id }}";
    let authorize_public_key = "{{ $authorize_public_key }}";
    var complete = "{{ Session::get('complete') }}";
    var bookingInfo = {!! json_encode(Session::get('paymentInfo')) !!};
  </script>
  <script src="{{ asset('assets/frontend/js/appointment.js') }}?v={{ filemtime(public_path('assets/frontend/js/appointment.js')) }}"></script>

  <script>
    @if (old('gateway') == 'stripe')
      $('#stripe-element').removeClass('d-none');
    @endif
  </script>
@endsection
