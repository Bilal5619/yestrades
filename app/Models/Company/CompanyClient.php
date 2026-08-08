<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyClient extends Model
{
    use SoftDeletes;

    protected $table = 'company_clients';

    protected $fillable = [
        'name',
        'client_type',
        'contact_name',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'payment_terms',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function properties()
    {
        return $this->hasMany(
            CompanyClientProperty::class,
            'client_id'
        );
    }

    public function jobs()
    {
        return $this->hasMany(
            EmployeeJob::class,
            'client_id'
        );
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->county,
            $this->postcode,
        ])->filter()->implode(', ');
    }
}