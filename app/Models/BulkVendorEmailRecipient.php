<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkVendorEmailRecipient extends Model
{
  use HasFactory;

  protected $fillable = [
    'bulk_vendor_email_id',
    'vendor_id',
    'email',
    'status',
    'error_message',
    'sent_at',
  ];

  protected $casts = [
    'sent_at' => 'datetime',
  ];

  public function bulkEmail()
  {
    return $this->belongsTo(BulkVendorEmail::class, 'bulk_vendor_email_id');
  }

  public function vendor()
  {
    return $this->belongsTo(Vendor::class);
  }
}
