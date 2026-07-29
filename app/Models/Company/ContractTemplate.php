<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employment_type',
        'description',
        'content',
        'is_default',
        'is_active',
        'version',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    public function createdByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'created_by'
        );
    }

    public function updatedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'updated_by'
        );
    }
}