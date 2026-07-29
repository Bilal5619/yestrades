<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTrainingRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table =
        'employee_training_records';

    protected $fillable = [
        'employee_id',
        'training_course_id',
        'provider_name',
        'scheduled_date',
        'started_at',
        'completed_at',
        'expiry_date',
        'status',
        'certificate_path',
        'certificate_number',
        'cost',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expiry_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function trainingCourse()
    {
        return $this->belongsTo(
            TrainingCourse::class,
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

    public function getStatusNameAttribute()
    {
        return ucwords(
            str_replace('_', ' ', $this->status)
        );
    }

    public function getIsExpiredAttribute()
    {
        return
            $this->expiry_date &&
            $this->expiry_date->lt(today());
    }

    public function getIsExpiringSoonAttribute()
    {
        return
            $this->expiry_date &&
            $this->expiry_date->gte(today()) &&
            $this->expiry_date->lte(
                today()->addDays(30)
            );
    }
}