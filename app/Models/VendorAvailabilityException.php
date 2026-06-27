<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorAvailabilityException extends Model
{
  protected $guarded = [];

  protected $casts = [
    'date' => 'date',
    'is_full_day' => 'boolean',
  ];

  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }
}
