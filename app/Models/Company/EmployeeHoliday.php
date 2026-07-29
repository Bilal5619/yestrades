<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeHoliday extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employee_holidays';

    protected $fillable = [
        'employee_id',
        'holiday_type',
        'start_date',
        'end_date',
        'start_period',
        'end_period',
        'total_days',
        'total_hours',
        'reason',
        'notes',
        'status',
        'requested_by',
        'requested_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'requested_by'
        );
    }

    public function approvedBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'approved_by'
        );
    }

    public function rejectedBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'rejected_by'
        );
    }

    public function cancelledBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'cancelled_by'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'created_by'
        );
    }

    public function updatedBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'updated_by'
        );
    }

    public function getHolidayTypeNameAttribute()
    {
        return ucwords(
            str_replace('_', ' ', $this->holiday_type)
        );
    }

    public function getStatusNameAttribute()
    {
        return ucwords(
            str_replace('_', ' ', $this->status)
        );
    }

    public function getIsCurrentlyOnHolidayAttribute()
    {
        return
            $this->status === 'approved' &&
            $this->start_date->lte(today()) &&
            $this->end_date->gte(today());
    }
}