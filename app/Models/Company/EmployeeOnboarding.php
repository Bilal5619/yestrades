<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'status',
        'progress_percentage',
        'assigned_to',
        'created_by',
        'started_at',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function sections()
    {
        return $this->hasMany(
            OnboardingSection::class,
            'employee_onboarding_id'
        )->orderBy('sort_order');
    }

    public function assignedUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'assigned_to'
        );
    }

    public function createdByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'created_by'
        );
    }
    public static function createForEmployee(
    Employee $employee,
    ?int $createdBy = null
): self {
    $onboarding = static::firstOrCreate(
        [
            'employee_id' => $employee->id,
        ],
        [
            'status' => 'in_progress',
            'progress_percentage' => 0,
            'created_by' => $createdBy,
            'started_at' => now(),
        ]
    );

    $sections = [
        [
            'section_key' => 'employee_contract',
            'title' => 'Employee Contract',
            'sort_order' => 1,
        ],
        [
            'section_key' => 'personal_bank_details',
            'title' => 'Personal Details & Bank Details',
            'sort_order' => 2,
        ],
        [
            'section_key' => 'induction_meeting',
            'title' => 'Induction Meeting',
            'sort_order' => 3,
        ],
        [
            'section_key' => 'job_manager_confirmation',
            'title' => 'Job & Manager Confirmation',
            'sort_order' => 4,
        ],
       
        [
            'section_key' => 'payroll_setup',
            'title' => 'Payroll Setup',
            'sort_order' => 5,
        ],
    ];

    foreach ($sections as $section) {
        $onboarding->sections()->firstOrCreate(
            [
                'section_key' => $section['section_key'],
            ],
            [
                'title' => $section['title'],
                'status' => 'pending',
                'is_required' => true,
                'sort_order' => $section['sort_order'],
            ]
        );
    }

    return $onboarding->load('sections');
}
public function jobDetails()
{
    return $this->hasOne(
        EmployeeOnboardingJobDetail::class,
        'employee_onboarding_id'
    );
}

public function recalculateProgress(): void
{
    $requiredSections = $this->sections()
        ->where('is_required', true)
        ->get();

    $totalRequired = $requiredSections->count();

    $completedRequired = $requiredSections
        ->where('status', 'completed')
        ->count();

    $percentage = $totalRequired > 0
        ? (int) round(
            ($completedRequired / $totalRequired) * 100
        )
        : 0;

    $this->update([
        'progress_percentage' => $percentage,
    ]);
}
public function personalDetails()
{
    return $this->hasOne(
        EmployeeOnboardingPersonalDetail::class,
        'employee_onboarding_id'
    );
}
public function induction()
{
    return $this->hasOne(
        EmployeeOnboardingInduction::class,
        'employee_onboarding_id'
    );
}
public function payroll()
{
    return $this->hasOne(
        EmployeeOnboardingPayroll::class,
        'employee_onboarding_id'
    );
}
public function contracts()
{
    return $this->hasMany(
        EmployeeContract::class,
        'employee_onboarding_id'
    );
}

public function latestContract()
{
    return $this->hasOne(
        EmployeeContract::class,
        'employee_onboarding_id'
    )->latestOfMany();
}

}