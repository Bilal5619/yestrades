<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyAuditLog extends Model
{
    protected $table = 'company_audit_logs';

    protected $fillable = [
        'company_user_id',
        'action',
        'method',
        'route_name',
        'request_path',
        'ip_address',
        'user_agent',
        'response_status',
    ];

    public function companyUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'company_user_id'
        );
    }
}