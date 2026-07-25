<?php

namespace App\Models;

use App\Models\Services\ServiceBooking;
use App\Models\Services\Services;
use App\Models\Staff\Staff;
use Illuminate\Database\Eloquent\Model;

/**
 * IMPORTANT: this model carries `internal_notes`, which is admin-only and must
 * NEVER be exposed to a customer-facing view, email, or JSON response. There is
 * no automatic protection for this in Laravel — any code path that serializes
 * a QuoteRequest to a customer-facing context must explicitly exclude it
 * (e.g. ->makeHidden('internal_notes') or an explicit column select).
 */
class QuoteRequest extends Model
{
  protected $guarded = [];

  protected $casts = [
    'preferred_date' => 'date',
    'expiry_date' => 'date',
    'labour_cost' => 'decimal:2',
    'material_cost' => 'decimal:2',
    'callout_charge' => 'decimal:2',
    'additional_charges' => 'decimal:2',
    'discount' => 'decimal:2',
    'vat_amount' => 'decimal:2',
    'total_price' => 'decimal:2',
    'quote_sent_at' => 'datetime',
    'customer_responded_at' => 'datetime',
    'response_token_expires_at' => 'datetime',
    'response_token_used_at' => 'datetime',
  ];

  public const STATUS_NEW = 'new_request';
  public const STATUS_UNDER_REVIEW = 'under_review';
  public const STATUS_MORE_INFO_REQUIRED = 'more_information_required';
  public const STATUS_SITE_VISIT_REQUIRED = 'site_visit_required';
  public const STATUS_QUOTE_PREPARED = 'quote_prepared';
  public const STATUS_QUOTE_SENT = 'quote_sent';
  public const STATUS_CUSTOMER_ACCEPTED = 'customer_accepted';
  public const STATUS_CUSTOMER_DECLINED = 'customer_declined';
  public const STATUS_CONVERTED = 'converted_to_booking';
  public const STATUS_COMPLETED = 'completed';
  public const STATUS_CANCELLED = 'cancelled';

  // Display order for admin <select>/badges and Rule::in() validation.
  public const STATUSES = [
    self::STATUS_NEW,
    self::STATUS_UNDER_REVIEW,
    self::STATUS_MORE_INFO_REQUIRED,
    self::STATUS_SITE_VISIT_REQUIRED,
    self::STATUS_QUOTE_PREPARED,
    self::STATUS_QUOTE_SENT,
    self::STATUS_CUSTOMER_ACCEPTED,
    self::STATUS_CUSTOMER_DECLINED,
    self::STATUS_CONVERTED,
    self::STATUS_COMPLETED,
    self::STATUS_CANCELLED,
  ];

  public const STATUS_LABELS = [
    self::STATUS_NEW => 'New Request',
    self::STATUS_UNDER_REVIEW => 'Under Review',
    self::STATUS_MORE_INFO_REQUIRED => 'More Information Required',
    self::STATUS_SITE_VISIT_REQUIRED => 'Site Visit Required',
    self::STATUS_QUOTE_PREPARED => 'Quote Prepared',
    self::STATUS_QUOTE_SENT => 'Quote Sent',
    self::STATUS_CUSTOMER_ACCEPTED => 'Customer Accepted',
    self::STATUS_CUSTOMER_DECLINED => 'Customer Declined',
    self::STATUS_CONVERTED => 'Converted to Booking',
    self::STATUS_COMPLETED => 'Completed',
    self::STATUS_CANCELLED => 'Cancelled',
  ];

  // Statuses a request can never move backward out of.
  public const LOCKED_STATUSES = [self::STATUS_CONVERTED, self::STATUS_COMPLETED, self::STATUS_CANCELLED];

  public function service()
  {
    return $this->belongsTo(Services::class, 'service_id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function answers()
  {
    return $this->hasMany(QuoteRequestAnswer::class, 'quote_request_id');
  }

  public function attachments()
  {
    return $this->hasMany(QuoteRequestAttachment::class, 'quote_request_id');
  }

  public function statusHistory()
  {
    return $this->hasMany(QuoteStatusHistory::class, 'quote_request_id')->latest();
  }

  public function assignedStaff()
  {
    return $this->belongsTo(Staff::class, 'assigned_staff_id');
  }

  public function convertedBooking()
  {
    return $this->belongsTo(ServiceBooking::class, 'converted_booking_id');
  }

  public function statusLabel(): string
  {
    return self::STATUS_LABELS[$this->status] ?? $this->status;
  }

  /** Requires `service.content` (filtered to a language) to be eager-loaded. */
  public function serviceName(): string
  {
    $content = $this->service ? $this->service->content->first() : null;
    return $content->name ?? ('Service #' . $this->service_id);
  }

  public function getIsResponseTokenExpiredAttribute(): bool
  {
    return !$this->response_token_expires_at || $this->response_token_expires_at->isPast();
  }
}
