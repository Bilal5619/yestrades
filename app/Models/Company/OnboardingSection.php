<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'section_key',
        'title',
        'status',
        'is_required',
        'sort_order',
        'completed_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
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