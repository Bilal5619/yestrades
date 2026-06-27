<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'slug',
    'short_description',
    'description',
    'image',
    'price',
    'offer_price',
    'offer_label',
    'price_placeholder',
    'is_active',
    'show_in_renewal_section',
    'renewal_section_sort_order',
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'show_in_renewal_section' => 'boolean',
    'price' => 'decimal:2',
    'offer_price' => 'decimal:2',
  ];

  public function items()
  {
    return $this->hasMany(ServicePackageItem::class)->orderBy('sort_order');
  }

  public function services()
  {
    return $this->belongsToMany(Services::class, 'service_package_items', 'service_package_id', 'service_id')
      ->withPivot('sort_order')
      ->orderBy('service_package_items.sort_order');
  }

  public function activePrice(): float
  {
    return round((float) ($this->offer_price ?? $this->price), 2);
  }

  public function hasPricePlaceholder(): bool
  {
    return !empty($this->price_placeholder);
  }

  public function getDisplayPrice()
  {
    if ($this->hasPricePlaceholder()) {
      return $this->price_placeholder;
    }

    return symbolPrice($this->activePrice());
  }
}
