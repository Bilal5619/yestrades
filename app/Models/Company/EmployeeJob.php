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
  public const STATUSES = ['assigned', 'accepted', 'en_route', 'on_site', 'completed', 'cancelled'];

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
    'priority',
    'status',
    'internal_notes',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'scheduled_date' => 'date',
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class);
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
