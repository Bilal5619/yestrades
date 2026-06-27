<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankAccount extends Model
{
  use HasFactory;

  protected $fillable = [
    'vendor_id',
    'account_holder_name',
    'bank_name',
    'account_number',
    'iban',
    'branch_name',
    'swift_code',
    'routing_number',
    'notes',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function vendor()
  {
    return $this->belongsTo(Vendor::class);
  }
}
