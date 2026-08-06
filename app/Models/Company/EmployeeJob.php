<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeJob extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $table = 'employee_jobs';

  public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];
  public const STATUSES = ['assigned', 'accepted', 'en_route', 'on_site', 'in_progress', 'completed', 'awaiting_review', 'cancelled'];

  protected $fillable = [
    'employee_id',
    'title',
    'description',
    'customer_name',
    'customer_phone',
    'site_address',
    'site_postcode',
    'scheduled_date',
    'scheduled_start_time',
    'expected_duration_minutes',
    'started_at',
    'finished_at',
    'priority',
    'status',
    'internal_notes',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'scheduled_date' => 'date',
    'started_at' => 'datetime',
    'finished_at' => 'datetime',
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function attendances()
  {
    return $this->hasMany(EmployeeAttendance::class, 'job_id');
  }

  public function isInProgress(): bool
  {
    return $this->started_at !== null && $this->finished_at === null;
  }

  public function actualDurationMinutes(): ?int
  {
    if (!$this->started_at || !$this->finished_at) {
      return null;
    }

    return $this->started_at->diffInMinutes($this->finished_at);
  }

  public function durationDifferenceMinutes(): ?int
  {
    $actual = $this->actualDurationMinutes();

    if ($actual === null || $this->expected_duration_minutes === null) {
      return null;
    }

    return $actual - (int) $this->expected_duration_minutes;
  }

  public function createdBy()
  {
    return $this->belongsTo(CompanyUser::class, 'created_by');
  }

  public function updatedBy()
  {
    return $this->belongsTo(CompanyUser::class, 'updated_by');
  }
}
