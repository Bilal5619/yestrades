<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingPersonalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'full_name',
        'date_of_birth',
        'personal_email',
        'phone',
        'home_address',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'national_insurance_number',
        'bank_name',
        'account_holder_name',
        'sort_code',
        'account_number',
        'details_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'details_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function onboarding()
    {
        return $this->belongsTo(
            EmployeeOnboarding::class,
            'employee_onboarding_id'
        );
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'verified_by'
        );
    }
    public function personalDetails()
{
    return $this->hasOne(
        EmployeeOnboardingPersonalDetail::class,
        'employee_onboarding_id'
    );
}
}