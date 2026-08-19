<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class EmployeeAbsence extends Model
{
    protected $fillable = [
        'employee_id',
        'absence_type',
        'start_date',
        'end_date',
        'days_absent',
        'status',
        'reason',
        'evidence_path',
        'return_to_work_date',
        'return_to_work_completed',
        'return_to_work_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'return_to_work_date' => 'date',
        'return_to_work_completed' => 'boolean',
        'days_absent' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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