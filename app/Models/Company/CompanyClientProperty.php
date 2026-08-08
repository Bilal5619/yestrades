<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyClientProperty extends Model
{
    use SoftDeletes;

    protected $table = 'company_client_properties';

    protected $fillable = [
        'client_id',
        'property_name',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'contact_name',
        'contact_phone',
        'contact_email',
        'access_notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(
            CompanyClient::class,
            'client_id'
        );
    }

    public function jobs()
    {
        return $this->hasMany(
            EmployeeJob::class,
            'client_property_id'
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