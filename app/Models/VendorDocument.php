<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDocument extends Model
{
  protected $guarded = [];

  protected $casts = [
    'expiry_date' => 'date',
    'date_of_birth' => 'date',
  ];

  public const TYPE_INSURANCE = 'insurance';
  public const TYPE_ID = 'id_document';
  public const TYPE_RIGHT_TO_WORK = 'right_to_work';

  public const STORAGE_DIR = 'assets/file/vendor-documents/';
  public const STATUS_PENDING = 'pending';
  public const STATUS_UNDER_REVIEW = 'under_review';
  public const STATUS_APPROVED = 'approved';
  public const STATUS_REJECTED = 'rejected';

  protected static function booted()
  {
    static::deleting(function (VendorDocument $document) {
      if ($document->file_path) {
        @unlink(public_path(self::STORAGE_DIR . $document->file_path));
      }
    });
  }

  public function vendor()
  {
    return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
  }

  public function getUrlAttribute()
  {
    return $this->file_path ? asset(self::STORAGE_DIR . $this->file_path) : null;
  }

  public function getIsApprovedAttribute()
  {
    return $this->normalized_status === self::STATUS_APPROVED;
  }

  public function getIsRejectedAttribute()
  {
    return $this->normalized_status === self::STATUS_REJECTED;
  }

  public function getNormalizedStatusAttribute()
  {
    return strtolower(trim((string) $this->status));
  }

  public function getIsPendingReviewAttribute()
  {
    return in_array($this->normalized_status, [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW], true);
  }
}
