<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    use HasFactory;

    protected $table = 'training_courses';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'is_mandatory',
        'requires_certificate',
        'has_expiry',
        'validity_months',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'requires_certificate' => 'boolean',
        'has_expiry' => 'boolean',
        'validity_months' => 'integer',
        'is_active' => 'boolean',
    ];

    public function trainingRecords()
    {
        return $this->hasMany(
            EmployeeTrainingRecord::class,
            'training_course_id'
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