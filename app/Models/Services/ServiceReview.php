<?php

namespace App\Models\Services;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReview extends Model
{
  use HasFactory;
  protected $guarded = [];

  protected $casts = [
    'reviewed_at' => 'datetime',
  ];

  public function booking()
  {
    return $this->belongsTo(ServiceBooking::class, 'booking_id', 'id');
  }

  public function service()
  {
    return $this->belongsTo(Services::class, 'service_id', 'id');
  }

  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function scopeApproved($query)
  {
    return $query->where('status', 'approved');
  }

  public static function refreshServiceRating($serviceId): void
  {
    if (!$serviceId) {
      return;
    }

    $average = static::approved()
      ->where('service_id', $serviceId)
      ->avg('rating');

    Services::where('id', $serviceId)->update([
      'average_rating' => $average ? number_format($average, 1, '.', '') : 0,
    ]);
  }

  public static function refreshVendorRating($vendorId): void
  {
    if (!$vendorId) {
      return;
    }

    $average = static::approved()
      ->where('vendor_id', $vendorId)
      ->avg('rating');

    Vendor::where('id', $vendorId)->update([
      'avg_rating' => $average ? number_format($average, 1, '.', '') : 0,
    ]);
  }

  public static function refreshRatings(?int $serviceId, ?int $vendorId): void
  {
    static::refreshServiceRating($serviceId);
    static::refreshVendorRating($vendorId);
  }
}
