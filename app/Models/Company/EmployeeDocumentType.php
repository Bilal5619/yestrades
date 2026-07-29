<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocumentType extends Model
{
    use HasFactory;

    protected $table = 'employee_document_types';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'is_required_by_default',
        'requires_expiry_date',
        'requires_review',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_required_by_default' => 'boolean',
        'requires_expiry_date' => 'boolean',
        'requires_review' => 'boolean',
        'status' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(
            EmployeeDocument::class,
            'document_type_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'created_by'
        );
    }

    public function updatedBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'updated_by'
        );
    }
}