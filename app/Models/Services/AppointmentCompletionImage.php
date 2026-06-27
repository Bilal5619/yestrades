<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Model;

class AppointmentCompletionImage extends Model
{
  protected $guarded = [];

  public function booking()
  {
    return $this->belongsTo(ServiceBooking::class, 'service_booking_id');
  }
}
