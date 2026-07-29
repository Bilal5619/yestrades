<?php

namespace App\Models\Company;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $table = 'company_users';

    protected $fillable = [
    'role_id',
    'first_name',
    'last_name',
    'email',
    'password',
    'role',
    'status',
    'last_login_at',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function companyRole()
{
    return $this->belongsTo(
        CompanyRole::class,
        'role_id'
    );
}

public function hasPermission($permissionSlug)
{
    if (!$this->companyRole) {
        return false;
    }

    if ($this->companyRole->slug === 'company-owner') {
        return true;
    }

    return $this->companyRole
        ->permissions()
        ->where('slug', $permissionSlug)
        ->exists();
}

public function hasRole($roleSlug)
{
    return $this->companyRole &&
        $this->companyRole->slug === $roleSlug;
}
}