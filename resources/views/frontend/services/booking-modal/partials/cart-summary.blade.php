@php
  $symbol = $currencySymbol ?? ($currencyInfo->base_currency_symbol ?? '£');
  $position = $currencyPosition ?? ($currencyInfo->base_currency_symbol_position ?? 'left');
  $total = isset($cartTotal) ? $cartTotal : collect($serviceCart)->sum('price');
  $displayItemPrice = function ($item) use ($symbol, $position) {
      if (($item['price_status'] ?? null) === 'pending' && (float) ($item['price'] ?? 0) <= 0) {
          return __('Price pending');
      }

      return ($position === 'left' ? $symbol : '') . number_format($item['price'], 2) . ($position === 'right' ? $symbol : '');
  };
@endphp

<div class="service-cart-mini rounded-3" style="background: transparent;">
  <div class="p-3">
     @if ($cartCount === 0)
       <p class="text-muted small mb-0">{{ __('Your service cart is empty. Add services to see them here.') }}</p>
     @else
       <div class="cart-items-list d-flex flex-column gap-2">
         @foreach ($serviceCart as $item)
           <div class="d-flex flex-column gap-1 small" style="background: transparent;">
             <div class="d-flex justify-content-between align-items-center">
               <span class="fw-semibold text-truncate me-2">{{ $item['service_name'] }}</span>
               <span class="fw-semibold" style="color: #ff004d; font-size: 0.85rem;">
                  {{ $displayItemPrice($item) }}
               </span>
             </div>
             <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
               <i class="fal fa-calendar-alt"></i>
               <span class="text-truncate">
                 {{ \Carbon\Carbon::parse($item['booking_date'])->format('d M Y') }}
                 @if (!empty($item['start_time']) || !empty($item['end_time']))
                   ·
                   {{ !empty($item['start_time']) ? \Carbon\Carbon::parse($item['start_time'])->format('h:i A') : '--' }}
                   -
                   {{ !empty($item['end_time']) ? \Carbon\Carbon::parse($item['end_time'])->format('h:i A') : '--' }}
                 @endif
               </span>
             </div>
           </div>
           @if (!$loop->last)
             <hr class="my-2 border-light">
           @endif
         @endforeach
       </div>
       <div class="d-flex justify-content-between align-items-center mt-3 small fw-semibold">
         <span>{{ __('Total') }}</span>
         <span style="color: #ff004d;">
           {{ $position === 'left' ? $symbol : '' }}{{ number_format($total, 2) }}{{ $position === 'right' ? $symbol : '' }}
         </span>
       </div>
     @endif
  </div>
</div>

