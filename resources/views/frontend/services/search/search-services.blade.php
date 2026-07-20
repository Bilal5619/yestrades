@php
  $featuredServiceCount = $featuredServices->count();
  $serviceCount = $services->total();
  $serach_total = $featuredServiceCount + $serviceCount;
@endphp
<input type="hidden" id="countServie" value="{{ $serach_total }}">
<div class="row g-4">
  @if ($serach_total > 0)
    @foreach ($featuredServices as $service)
      <div class="col-xl-4 col-sm-6" data-aos="fade-up">
        @include('frontend.services.partials.card', ['service' => $service, 'featured' => true])
      </div>
    @endforeach
    @foreach ($services as $service)
      <div class="col-xl-4 col-sm-6" data-aos="fade-up">
        @include('frontend.services.partials.card', ['service' => $service, 'featured' => false])
      </div>
    @endforeach
  @else
    <div class="col-12">
      <div class="sv-empty">
        <i class="fal fa-magnifying-glass"></i>
        <h4>{{ __('NO SERVICE FOUND') }}!</h4>
        <p>{{ __('Try adjusting your filters or search a different category.') }}</p>
      </div>
    </div>
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
