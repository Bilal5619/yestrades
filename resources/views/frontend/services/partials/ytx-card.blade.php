<a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}" class="ytx-service-card">
  <div class="ytx-image-wrap">
    @if ($service->isQuoteType() || $service->hasPricePlaceholder())
      <span class="ytx-tag">{{ __('Free quote') }}</span>
    @endif
    @php $ytxCardImg = (!empty($service->service_image) && file_exists(public_path('assets/img/services/' . $service->service_image))) ? asset('assets/img/services/' . $service->service_image) : 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1200&q=80'; @endphp
    <img src="{{ $ytxCardImg }}" alt="{{ $service->name }}" loading="lazy">
  </div>
  <div class="ytx-content">
    <h3>{{ truncateString($service->name, 55) }}</h3>
    @if (!empty($service->short_description))
      <p>{{ truncateString($service->short_description, 90) }}</p>
    @endif
    <span class="ytx-link">{{ __('View service') }} →</span>
  </div>
</a>
