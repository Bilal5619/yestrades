<a href="{{ route('frontend.service.details', ['slug' => $service->slug, 'id' => $service->id]) }}" class="ytx-service-card">
  <div class="ytx-image-wrap">
    @if ($service->isQuoteType() || $service->hasPricePlaceholder())
      <span class="ytx-tag">{{ __('Free quote') }}</span>
    @endif
    <img src="{{ asset('assets/img/services/' . $service->service_image) }}" alt="{{ $service->name }}" loading="lazy">
  </div>
  <div class="ytx-content">
    <h3>{{ truncateString($service->name, 55) }}</h3>
    @if (!empty($service->short_description))
      <p>{{ truncateString($service->short_description, 90) }}</p>
    @endif
    <span class="ytx-link">{{ __('View service') }} →</span>
  </div>
</a>
