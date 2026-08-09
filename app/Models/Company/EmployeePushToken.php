<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class EmployeePushToken extends Model
{
    protected $fillable = [
        'employee_id',
        'expo_push_token',
        'platform',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}