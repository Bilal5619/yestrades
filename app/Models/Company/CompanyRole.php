<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRole extends Model
{
    use HasFactory;

    protected $table = 'company_roles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'status',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(CompanyUser::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            CompanyPermission::class,
            'company_role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    public function hasPermission($permissionSlug)
    {
        return $this->permissions()
            ->where('slug', $permissionSlug)
            ->exists();
    }
}