<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeJob extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employee_jobs';

    public const PRIORITIES = [
        'low',
        'medium',
        'high',
        'urgent',
    ];

    public const STATUSES = [
        'assigned',
        'accepted',
        'en_route',
        'on_site',
        'in_progress',
        'completed',
        'awaiting_review',
        'cancelled',
    ];
public const APPOINTMENT_STATUSES = [
    'not_confirmed',
    'awaiting_customer',
    'proposed',
    'confirmed',
];
    public const PAYMENT_STATUSES = [
        'not_required',
        'outstanding',
        'invoice_required',
        'invoice_sent',
        'part_paid',
        'paid',
        'overdue',
    ];

    public const PAYMENT_METHODS = [
        'cash',
        'card',
        'bank_transfer',
        'invoice_client',
        'other',
    ];

    public const CERTIFICATE_STATUSES = [
        'not_required',
        'required',
        'in_progress',
        'completed',
    ];

    public const CERTIFICATE_TYPES = [
        'cp12',
        'eicr',
        'eic',
        'minor_works',
        'pat',
        'epc',
        'other',
    ];

    public const ADMIN_STATUSES = [
        'action_required',
        'ready_to_close',
        'closed',
    ];

    protected $fillable = [
        'job_reference',

        'employee_id',
        'client_id',
        'client_property_id',

        'title',
        'description',

        'customer_name',
        'customer_phone',
        'customer_email',

        'site_address',
        'site_postcode',

        'appointment_status',

        'scheduled_date',
        'scheduled_start_time',
        'expected_duration_minutes',

        'started_at',
        'finished_at',

        'priority',
        'status',

        'internal_notes',

        'payment_required',
        'agreed_amount',
        'payment_method',
        'payment_status',
        'invoice_number',
        'invoice_sent_at',
        'paid_at',

        'certificate_required',
        'certificate_type',
        'certificate_status',

        'work_summary',
        'completion_notes',

        'admin_status',
        'closed_at',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',

        'started_at' => 'datetime',
        'finished_at' => 'datetime',

        'payment_required' => 'boolean',
        'agreed_amount' => 'decimal:2',
        'invoice_sent_at' => 'datetime',
        'paid_at' => 'datetime',

        'certificate_required' => 'boolean',

        'closed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function (EmployeeJob $job) {
            if (!$job->job_reference) {
                $job->forceFill([
                    'job_reference' =>
                        'YT-' . str_pad(
                            (string) $job->id,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ),
                ])->saveQuietly();
            }
        });

        static::saving(function (EmployeeJob $job) {
            if (!$job->payment_required) {
                $job->payment_status = 'not_required';
            }

            if (!$job->certificate_required) {
                $job->certificate_status = 'not_required';
                $job->certificate_type = null;
            }

            $job->refreshAdminStatus();
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function client()
    {
        return $this->belongsTo(
            CompanyClient::class,
            'client_id'
        );
    }

    public function property()
    {
        return $this->belongsTo(
            CompanyClientProperty::class,
            'client_property_id'
        );
    }

    public function attendances()
    {
        return $this->hasMany(
            EmployeeAttendance::class,
            'job_id'
        );
    }

    public function isInProgress(): bool
    {
        return $this->started_at !== null
            && $this->finished_at === null;
    }

    public function actualDurationMinutes(): ?int
    {
        if (!$this->started_at || !$this->finished_at) {
            return null;
        }

        return $this->started_at
            ->diffInMinutes($this->finished_at);
    }

    public function durationDifferenceMinutes(): ?int
    {
        $actual = $this->actualDurationMinutes();

        if (
            $actual === null ||
            $this->expected_duration_minutes === null
        ) {
            return null;
        }

        return $actual
            - (int) $this->expected_duration_minutes;
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

    public function paymentComplete(): bool
    {
        return !$this->payment_required
            || $this->payment_status === 'paid';
    }

    public function certificateComplete(): bool
    {
        return !$this->certificate_required
            || $this->certificate_status === 'completed';
    }

    public function workComplete(): bool
    {
        return in_array(
            $this->status,
            [
                'completed',
                'awaiting_review',
            ],
            true
        );
    }

    public function canClose(): bool
    {
        return $this->workComplete()
            && $this->paymentComplete()
            && $this->certificateComplete();
    }

    public function refreshAdminStatus(): void
    {
        if ($this->admin_status === 'closed') {
            return;
        }

        $this->admin_status = $this->canClose()
            ? 'ready_to_close'
            : 'action_required';
    }
}