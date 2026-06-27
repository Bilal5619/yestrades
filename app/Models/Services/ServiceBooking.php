<?php

namespace App\Models\Services;

use App\Models\Staff\Staff;
use App\Models\Staff\StaffServiceHour;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorInfo;
use App\Models\BookingAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
  use HasFactory;
  protected $guarded = [];

  protected $casts = [
    'completed_at' => 'datetime',
    'package_included_services' => 'array',
  ];

  protected static function booted()
  {
    static::deleting(function (ServiceBooking $booking) {
      foreach ($booking->completionImages as $image) {
        @unlink(public_path('assets/img/appointment-completions/' . $image->image));
        $image->delete();
      }
      if ($booking->job_certificate) {
        @unlink(public_path('assets/file/certificates/' . $booking->job_certificate));
      }
      if ($booking->completion_invoice) {
        @unlink(public_path('assets/file/completion-invoices/' . $booking->completion_invoice));
      }
      if ($booking->vendorInvoice) {
        $booking->vendorInvoice->delete();
      }
    });
  }

  public function service()
  {
    return $this->belongsTo(Services::class, 'service_id', 'id');
  }

  public function servicePackage()
  {
    return $this->belongsTo(ServicePackage::class, 'service_package_id', 'id');
  }
  public function staff()
  {
    return $this->belongsTo(Staff::class, 'staff_id', 'id');
  }
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function serviceHoureTime()
  {
    return $this->belongsTo(StaffServiceHour::class, 'service_hour_id', 'id');
  }

  public function serviceContent()
  {
    return $this->hasMany(ServiceContent::class, 'service_id', 'service_id');
  }
  public function vendorInfo()
  {
    return $this->hasMany(VendorInfo::class, 'vendor_id', 'vendor_id');
  }
  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }

  public function answers()
  {
    return $this->hasMany(BookingAnswer::class, 'booking_id', 'id');
  }

  public function completionImages()
  {
    return $this->hasMany(AppointmentCompletionImage::class, 'service_booking_id');
  }

  public function vendorInvoice()
  {
    return $this->hasOne(Invoice::class, 'service_booking_id');
  }
}
