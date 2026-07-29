<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPermission extends Model
{
    use HasFactory;

    protected $table = 'company_permissions';

    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            CompanyRole::class,
            'company_role_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }
}