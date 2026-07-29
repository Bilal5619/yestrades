<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingJobDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'job_title',
        'department',
        'manager_id',
        'start_date',
        'employment_type',
        'work_location',
        'weekly_hours',
        'pay_type',
        'salary',
        'hourly_rate',
        'holiday_allowance',
        'notice_period',
        'probation_required',
        'probation_months',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'weekly_hours' => 'decimal:2',
        'salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'holiday_allowance' => 'decimal:2',
        'probation_required' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function onboarding()
    {
        return $this->belongsTo(
            EmployeeOnboarding::class,
            'employee_onboarding_id'
        );
    }

    public function manager()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'manager_id'
        );
    }

    public function confirmedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'confirmed_by'
        );
    }
}