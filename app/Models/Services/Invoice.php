<?php

namespace App\Models\Services;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
  protected $guarded = [];

  protected $casts = [
    'invoice_date'     => 'date',
    'approved_at'      => 'datetime',
    'payment_due_date' => 'datetime',
    'paid_date'        => 'datetime',
  ];

  protected static function booted()
  {
    static::deleting(function (Invoice $invoice) {
      if ($invoice->invoice_file) {
        @unlink(public_path('assets/file/invoices/vendor/' . $invoice->invoice_file));
      }
      if ($invoice->customer_invoice_file) {
        @unlink(public_path('assets/file/invoices/service/' . $invoice->customer_invoice_file));
      }
    });
  }

  public function booking()
  {
    return $this->belongsTo(ServiceBooking::class, 'service_booking_id');
  }

  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }

  // Unpaid invoice whose 48-hour payment window has elapsed.
  public function getIsOverdueAttribute()
  {
    return $this->payment_status !== 'paid'
      && $this->payment_due_date
      && now()->greaterThan($this->payment_due_date);
  }
}
