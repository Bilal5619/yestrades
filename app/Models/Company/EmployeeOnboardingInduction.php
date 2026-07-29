<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingInduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'meeting_date',
        'meeting_time',
        'location',
        'manager_id',
        'topics_discussed',
        'meeting_notes',
        'employee_confirmed',
        'meeting_completed',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'employee_confirmed' => 'boolean',
        'meeting_completed' => 'boolean',
        'completed_at' => 'datetime',
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

    public function completedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'completed_by'
        );
    }
}