<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeShift extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $table = 'employee_shifts';

  public const STATUSES = ['scheduled', 'completed', 'missed', 'cancelled'];

  protected $fillable = [
    'employee_id',
    'shift_date',
    'start_time',
    'end_time',
    'break_minutes',
    'work_location',
    'status',
    'notes',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'shift_date' => 'date',
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function attendances()
  {
    return $this->hasMany(EmployeeAttendance::class, 'shift_id');
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
