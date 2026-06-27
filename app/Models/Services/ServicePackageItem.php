<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackageItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'service_package_id',
    'service_id',
    'sort_order',
  ];

  public function package()
  {
    return $this->belongsTo(ServicePackage::class, 'service_package_id');
  }

  public function service()
  {
    return $this->belongsTo(Services::class, 'service_id');
  }
}
