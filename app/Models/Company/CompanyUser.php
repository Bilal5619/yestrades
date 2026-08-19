<?php

namespace App\Models\Company;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable
    as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword
    as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CompanyUser extends Model implements
    AuthenticatableContract,
    CanResetPasswordContract
{
    use HasFactory;
    use Authenticatable;
    use CanResetPassword;
    use Notifiable;

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
        'two_factor_secret',
        'two_factor_enabled_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'status' => 'boolean',
        'last_login_at' => 'datetime',
        'two_factor_enabled_at' => 'datetime',

        // Laravel encrypts/decrypts this automatically.
        'two_factor_secret' => 'encrypted',
    ];

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name
            . ' '
            . $this->last_name
        );
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

        if (
            $this->companyRole->slug
            === 'company-owner'
        ) {
            return true;
        }

        return $this->companyRole
            ->permissions()
            ->where(
                'slug',
                $permissionSlug
            )
            ->exists();
    }

    public function hasRole($roleSlug)
    {
        return $this->companyRole
            &&
            $this->companyRole->slug
                === $roleSlug;
    }

    public function getTwoFactorEnabledAttribute()
    {
        return !empty(
            $this->two_factor_secret
        )
        &&
        !empty(
            $this->two_factor_enabled_at
        );
    }

    public function sendPasswordResetNotification(
        $token
    ) {
        $this->notify(
            new \App\Notifications\
                CompanyResetPasswordNotification(
                    $token
                )
        );
    }
}