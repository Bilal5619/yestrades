<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
  use HasFactory;

  protected $table = 'employee_attendances';

  protected $fillable = [
    'employee_id',
    'shift_id',
    'clock_type',
    'server_timestamp',
    'device_timestamp',
    'latitude',
    'longitude',
    'accuracy_meters',
    'expected_location',
    'distance_from_site_meters',
    'within_geofence',
    'device_platform',
    'is_manual_entry',
    'override_reason',
    'recorded_by',
  ];

  protected $casts = [
    'server_timestamp' => 'datetime',
    'device_timestamp' => 'datetime',
    'within_geofence' => 'boolean',
    'is_manual_entry' => 'boolean',
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function shift()
  {
    return $this->belongsTo(EmployeeShift::class, 'shift_id');
  }

  public function recordedBy()
  {
    return $this->belongsTo(CompanyUser::class, 'recorded_by');
  }
}
