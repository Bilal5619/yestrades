<?php

namespace App\Models;

use App\Models\Services\ServiceBooking;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceReview;
use App\Models\Services\Services;
use App\Models\Staff\Staff;
use App\Models\VendorBankAccount;
use App\Models\VendorRegistrationNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Model implements AuthenticatableContract
{
  use HasApiTokens, HasFactory, Authenticatable;

  protected $fillable = [
    'photo',
    'email',
    'recived_email',
    'phone',
    'username',
    'password',
    'status',
    'verification_status',
    'verification_rejection_reason',
    'amount',
    'fixed_commission_percent',
    'business_type',
    'right_to_work_is_british',
    'total_appointment',
    'facebook',
    'twitter',
    'linkedin',
    'avg_rating',
    'email_verified_at',
    'show_email_addresss',
    'show_phone_number',
    'show_contact_form',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'right_to_work_is_british' => 'boolean',
  ];

  public function vendor_info()
  {
    return $this->hasOne(VendorInfo::class,'vendor_id','id');
  }
  public function vendor_infos()
  {
    return $this->hasMany(VendorInfo::class);
  }

  //support ticket
  public function support_ticket()
  {
    return $this->hasMany(SupportTicket::class, 'vendor_id', 'id');
  }

  public function memberships()
  {
    return $this->hasMany(Membership::class);
  }
  public function service()
  {
    return $this->hasMany(Services::class);
  }
  public function staff()
  {
    return $this->hasMany(Staff::class);
  }
  public function appointment()
  {
    return $this->hasMany(ServiceBooking::class);
  }
  public function serviceReview()
  {
    return $this->hasMany(ServiceReview::class, 'vendor_id', 'id');
  }

  public function weeklyHours()
  {
    return $this->hasMany(VendorWeeklyHour::class, 'vendor_id', 'id');
  }

  public function availabilityExceptions()
  {
    return $this->hasMany(VendorAvailabilityException::class, 'vendor_id', 'id');
  }

  public function registrationNumbers()
  {
    return $this->hasMany(VendorRegistrationNumber::class);
  }

  public function bankAccounts()
  {
    return $this->hasMany(VendorBankAccount::class);
  }

  public function activeBankAccount()
  {
    return $this->hasOne(VendorBankAccount::class)->where('is_active', 1);
  }

  public function serviceCategories()
  {
    return $this->belongsToMany(ServiceCategory::class, 'vendor_service_categories')
      ->withTimestamps();
  }

  public function documents()
  {
    return $this->hasMany(VendorDocument::class);
  }

  public function document($type)
  {
    return $this->documents->firstWhere('type', $type);
  }
}
