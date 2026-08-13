<?php

namespace App\Console\Commands;

use App\Models\Services\ServiceBooking;
use App\Services\TenantSlotService;
use Illuminate\Console\Command;

class ExpireTenantSlotLinks extends Command
{
  protected $signature = 'tenant-slots:expire';
  protected $description = 'Expire tenant appointment selection links that are older than three days';

  public function handle(TenantSlotService $tenantSlots): int
  {
    $expired = 0;

    ServiceBooking::query()
      ->with('user')
      ->where('tenant_slot_status', TenantSlotService::STATUS_WAITING)
      ->where('tenant_slot_expires_at', '<=', now())
      ->chunkById(100, function ($bookings) use ($tenantSlots, &$expired) {
        foreach ($bookings as $booking) {
          if ($tenantSlots->expire($booking)) {
            $expired++;
          }
        }
      });

    $this->info("Expired {$expired} tenant slot link(s).");

    return self::SUCCESS;
  }
}
