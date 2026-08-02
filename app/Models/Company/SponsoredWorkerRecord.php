<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsoredWorkerRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'immigration_route',
        'immigration_status',
        'visa_start_date',
        'visa_expiry_date',
        'cos_number',
        'cos_job_title',
        'occupation_code',
        'cos_salary',
        'actual_salary',
        'cos_weekly_hours',
        'cos_work_location',
        'sponsorship_start_date',
        'sponsorship_end_date',
        'actual_first_working_date',
        'record_status',
        'sponsored_duties',
        'permitted_absence_notes',
        'compliance_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'visa_start_date' => 'date',
        'visa_expiry_date' => 'date',
        'sponsorship_start_date' => 'date',
        'sponsorship_end_date' => 'date',
        'actual_first_working_date' => 'date',
        'cos_salary' => 'decimal:2',
        'actual_salary' => 'decimal:2',
        'cos_weekly_hours' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

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

    public function getVisaStatusAttribute()
    {
        if (!$this->visa_expiry_date) {
            return 'missing';
        }

        if ($this->visa_expiry_date->isPast()) {
            return 'expired';
        }

        $days = now()->startOfDay()->diffInDays(
            $this->visa_expiry_date,
            false
        );

        if ($days <= 30) {
            return 'urgent';
        }

        if ($days <= 60) {
            return 'expiring_60_days';
        }

        if ($days <= 90) {
            return 'expiring_90_days';
        }

        return 'valid';
    }

    public function getVisaStatusLabelAttribute()
    {
        return match ($this->visa_status) {
            'expired' => 'Expired',
            'urgent' => 'Expires within 30 days',
            'expiring_60_days' => 'Expires within 60 days',
            'expiring_90_days' => 'Expires within 90 days',
            'valid' => 'Valid',
            default => 'Expiry date missing',
        };
    }

    public function comparisonResults(): array
    {
        $employee = $this->employee;

        if (!$employee) {
            return [];
        }

        return [
            'job_title' => [
                'label' => 'Job title',
                'cos_value' => $this->cos_job_title,
                'actual_value' => $employee->job_title,
                'matches' => $this->normalise($this->cos_job_title)
                    === $this->normalise($employee->job_title),
            ],

            'salary' => [
                'label' => 'Salary',
                'cos_value' => $this->cos_salary,
                'actual_value' => $this->actual_salary,
                'matches' => $this->moneyMatches(
                    $this->cos_salary,
                    $this->actual_salary
                ),
            ],

            'weekly_hours' => [
                'label' => 'Weekly hours',
                'cos_value' => $this->cos_weekly_hours,
                'actual_value' => $employee->normal_weekly_hours,
                'matches' => $this->numberMatches(
                    $this->cos_weekly_hours,
                    $employee->normal_weekly_hours
                ),
            ],

            'work_location' => [
                'label' => 'Work location',
                'cos_value' => $this->cos_work_location,
                'actual_value' => $employee->work_location,
                'matches' => $this->normalise($this->cos_work_location)
                    === $this->normalise($employee->work_location),
            ],
        ];
    }

    public function hasComplianceDifference(): bool
    {
        foreach ($this->comparisonResults() as $result) {
            if (!$result['matches']) {
                return true;
            }
        }

        return false;
    }

    private function normalise($value): string
    {
        return strtolower(trim((string) $value));
    }

    private function moneyMatches($first, $second): bool
    {
        if ($first === null || $second === null) {
            return false;
        }

        return round((float) $first, 2)
            === round((float) $second, 2);
    }

    private function numberMatches($first, $second): bool
    {
        if ($first === null || $second === null) {
            return false;
        }

        return round((float) $first, 2)
            === round((float) $second, 2);
    }
    public function changes()
{
    return $this->hasMany(
        SponsoredWorkerChange::class,
        'sponsored_worker_record_id'
    );
}
}