<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyPolicyAcknowledgement extends Model
{
    protected $fillable = [
        'company_policy_id',
        'employee_id',
        'status',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function policy()
    {
        return $this->belongsTo(
            CompanyPolicy::class,
            'company_policy_id'
        );
    }

    public function employee()
    {
        return $this->belongsTo(
            Employee::class,
            'employee_id'
        );
    }
}