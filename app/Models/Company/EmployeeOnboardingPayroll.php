<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'payroll_number',
        'pay_type',
        'pay_rate',
        'pay_frequency',
        'first_payment_date',
        'bank_details_verified',
        'tax_information_received',
        'pension_status',
        'payroll_setup_completed',
        'completed_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'pay_rate' => 'decimal:2',
        'first_payment_date' => 'date',
        'bank_details_verified' => 'boolean',
        'tax_information_received' => 'boolean',
        'payroll_setup_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function onboarding()
    {
        return $this->belongsTo(
            EmployeeOnboarding::class,
            'employee_onboarding_id'
        );
    }

    public function completedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'completed_by'
        );
    }
}