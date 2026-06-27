<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkVendorEmail extends Model
{
  use HasFactory;

  protected $fillable = [
    'subject',
    'message',
    'send_type',
    'total_recipients',
    'sent_by',
  ];

  public function recipients()
  {
    return $this->hasMany(BulkVendorEmailRecipient::class);
  }
}
