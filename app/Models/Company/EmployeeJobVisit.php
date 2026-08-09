<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class EmployeeJobVisit extends Model
{
    protected $table =
        'employee_job_visits';

    public const STATUSES = [
        'scheduled',
        'in_progress',
        'completed',
        'cancelled',
        'unable_to_complete',
    ];

    protected $fillable = [
        'job_id',
        'employee_id',

        'visit_date',
        'start_time',

        'expected_duration_minutes',

        'status',

        'started_at',
        'finished_at',

        'notes',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(
            EmployeeJob::class,
            'job_id'
        );
    }

    public function employee()
    {
        return $this->belongsTo(
            Employee::class,
            'employee_id'
        );
    }

    public function actualDurationMinutes(): ?int
    {
        if (
            !$this->started_at
            ||
            !$this->finished_at
        ) {
            return null;
        }

        return $this
            ->started_at
            ->diffInMinutes(
                $this->finished_at
            );
    }
}