<?php

namespace App\Models\Careers;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CareerApplication extends Model
{
  use SoftDeletes;

  public const STATUSES = ['new', 'reviewing', 'shortlisted', 'interview', 'offered', 'hired', 'rejected', 'withdrawn', 'archived'];
  public const RIGHT_TO_WORK = ['Yes', 'No', 'Prefer to discuss'];
  public const SPONSORSHIP = ['Yes', 'No', 'Prefer to discuss'];

  protected $fillable = [
    'reference_number',
    'career_job_id',
    'job_title_snapshot',
    'application_type',
    'first_name',
    'last_name',
    'email',
    'phone',
    'city',
    'postcode',
    'right_to_work',
    'sponsorship_required',
    'linkedin_url',
    'portfolio_url',
    'cover_letter_text',
    'earliest_start_date',
    'expected_salary',
    'additional_information',
    'areas_of_interest',
    'preferred_employment_type',
    'cv_path',
    'cv_original_name',
    'cv_mime_type',
    'cv_size',
    'cover_letter_path',
    'cover_letter_original_name',
    'cover_letter_mime_type',
    'cover_letter_size',
    'status',
    'source',
    'admin_notes',
    'assigned_to',
    'submitted_at',
    'reviewed_at',
    'rejected_at',
    'hired_at',
    'consent_accepted',
    'consent_accepted_at',
    'privacy_policy_reference',
    'ip_address',
    'user_agent',
  ];

  protected $casts = [
    'submitted_at' => 'datetime',
    'reviewed_at' => 'datetime',
    'rejected_at' => 'datetime',
    'hired_at' => 'datetime',
    'consent_accepted' => 'boolean',
    'consent_accepted_at' => 'datetime',
    'earliest_start_date' => 'date',
  ];

  public function job()
  {
    return $this->belongsTo(CareerJob::class, 'career_job_id');
  }

  public function answers()
  {
    return $this->hasMany(CareerApplicationAnswer::class);
  }

  public function activities()
  {
    return $this->hasMany(CareerApplicationActivity::class)->latest();
  }

  public function notes()
  {
    return $this->hasMany(CareerApplicationNote::class)->latest();
  }

  public function assignee()
  {
    return $this->belongsTo(Admin::class, 'assigned_to');
  }

  public function getApplicantNameAttribute(): string
  {
    return trim($this->first_name . ' ' . $this->last_name);
  }

  public static function makeReferenceNumber(): string
  {
    do {
      $reference = 'YT-' . now()->format('ymd') . '-' . strtoupper(Str::random(6));
    } while (self::where('reference_number', $reference)->exists());

    return $reference;
  }
}
