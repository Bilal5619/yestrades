<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'employee_id',
        'contract_template_id',
        'version',
        'status',
        'generated_content',
        'generated_file_path',
        'generated_filename',
        'signed_file_path',
        'signed_filename',
        'generated_at',
        'sent_for_signature_at',
        'signed_at',
        'generated_by',
        'signed_uploaded_by',
        'notes',
    ];

    protected $casts = [
        'version' => 'integer',
        'generated_at' => 'datetime',
        'sent_for_signature_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function onboarding()
    {
        return $this->belongsTo(
            EmployeeOnboarding::class,
            'employee_onboarding_id'
        );
    }

    public function employee()
    {
        return $this->belongsTo(
            Employee::class,
            'employee_id'
        );
    }

    public function template()
    {
        return $this->belongsTo(
            ContractTemplate::class,
            'contract_template_id'
        );
    }

    public function generatedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'generated_by'
        );
    }

    public function signedUploadedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'signed_uploaded_by'
        );
    }
}