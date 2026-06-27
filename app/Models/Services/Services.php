<?php

namespace App\Models\Services;

use App\Models\Language;
use App\Models\Vendor;
use App\Models\VendorInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
  use HasFactory;

  protected $fillable = [
    'vendor_id',
    'status',
    'price',
    'prev_price',
    'price_placeholder',
    'show_in_renewal_section',
    'renewal_section_sort_order',
    'offer_price',
    'offer_label',
    'renewal_short_description',
    'service_image',
    'zoom_meeting',
    'max_person',
    'calendar_status',
    'average_rating',
    'staff_id',
    'service_area_postcode',
    'latitude',
    'longitude',
    'approval_status',
    'service_rejection_reason',
  ];

  public function language()
  {
    return $this->belongsTo(Language::class);
  }
  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }
  public function vendorInfo()
  {
    return $this->belongsTo(VendorInfo::class, 'vendor_id', 'vendor_id');
  }
  public function content()
  {
    return $this->hasMany(ServiceContent::class, 'service_id','id');
  }
  public function sliderImage()
  {
    return $this->hasMany(ServiceImage::class, 'service_id', 'id');
  }
  public function appointment()
  {
    return $this->hasMany(ServiceBooking::class, 'service_id', 'id');
  }

  /**
   * Check if service has a price placeholder
   */
  public function hasPricePlaceholder()
  {
    return !empty($this->price_placeholder);
  }

  /**
   * Get the price to display (placeholder or actual price)
   */
  public function getDisplayPrice()
  {
    if ($this->hasPricePlaceholder()) {
      return $this->price_placeholder;
    }
    return symbolPrice($this->price);
  }
}
