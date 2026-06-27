<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorWeeklyHour extends Model
{
  protected $guarded = [];

  protected $casts = [
    'is_open' => 'boolean',
    'day_of_week' => 'integer',
  ];

  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }
}
