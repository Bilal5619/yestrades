<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHolidayAllowance extends Model
{
    use HasFactory;

    protected $table = 'employee_holiday_allowances';

    protected $fillable = [
        'employee_id',
        'holiday_year',
        'annual_entitlement',
        'carried_over',
        'adjustment',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'holiday_year' => 'integer',
        'annual_entitlement' => 'decimal:2',
        'carried_over' => 'decimal:2',
        'adjustment' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

    public function getTotalAllowanceAttribute()
    {
        return
            (float) $this->annual_entitlement +
            (float) $this->carried_over +
            (float) $this->adjustment;
    }

    public function getApprovedDaysUsedAttribute()
    {
        return (float) $this->employee
            ->holidays()
            ->where('status', 'approved')
            ->whereYear(
                'start_date',
                $this->holiday_year
            )
            ->where('holiday_type', 'annual_leave')
            ->sum('total_days');
    }

    public function getRemainingAllowanceAttribute()
    {
        return max(
            0,
            $this->total_allowance -
            $this->approved_days_used
        );
    }
}