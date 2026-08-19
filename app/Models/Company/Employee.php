<?php

namespace App\Models\Company;

use App\Models\Staff\Staff;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model implements AuthenticatableContract
{
    use HasApiTokens;
    use HasFactory;
    use SoftDeletes;
    use Authenticatable;

    protected $table = 'employees';

    protected $fillable = [
        'employee_number',
        'staff_id',
        'is_field_engineer',
        'allow_manual_attendance',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'personal_email',
        'work_email',
        'phone',
        'alternative_phone',
        'profile_photo',
        'national_insurance_number',
        'password',
        'activation_token',
        'activation_token_expires_at',
        'activated_at',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'job_title',
        'department',
        'manager_id',
        'employment_type',
        'start_date',
        'probation_end_date',
        'leaving_date',
        'leaver_notice_date',
        'leaver_form_path',
        'leaver_form_original_name',
        'leaver_form_uploaded_at',
        'employment_status',
        'work_location',
        'normal_weekly_hours',
        'pay_type',
        'pay_rate',
        'payroll_number',
        'is_sponsored_worker',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_alternative_phone',
        'emergency_contact_address',
        'employment_notes',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'start_date' => 'date',
        'probation_end_date' => 'date',
        'leaving_date' => 'date',
        'leaver_notice_date' => 'date',
        'leaver_form_uploaded_at' => 'datetime',
        'normal_weekly_hours' => 'decimal:2',
        'pay_rate' => 'decimal:2',
        'is_sponsored_worker' => 'boolean',
        'is_field_engineer' => 'boolean',
        'allow_manual_attendance' => 'boolean',
        'activation_token_expires_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    // NOTE: staff_id/staff() intentionally still exist on this model but are
    // no longer used to determine field-engineer status. That column points
    // at the separate vendor-booking Staff table (business admin's "Staff
    // Management"), which must stay fully independent from HR/Employee data
    // per explicit client instruction. Do not use $this->staff for
    // field-engineer logic — use is_field_engineer below instead.
    public function staff()
    {
        return $this->belongsTo(
            Staff::class,
            'staff_id'
        );
    }

    // A "field engineer" is a simple, standalone flag set directly on the
    // Employee record by HR — completely independent of the vendor Staff
    // table. Office-only staff always get normal manual Clock In/Out; field
    // engineers clock in via their jobs unless admin has explicitly switched
    // on manual attendance for them.
    public function isFieldEngineer(): bool
    {
        return (bool) $this->is_field_engineer;
    }

    public function canUseManualAttendance(): bool
    {
        return !$this->isFieldEngineer() || (bool) $this->allow_manual_attendance;
    }

    public function manager()
    {
        return $this->belongsTo(
            Employee::class,
            'manager_id'
        );
    }

    public function teamMembers()
    {
        return $this->hasMany(
            Employee::class,
            'manager_id'
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

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' .
            ($this->middle_name ? $this->middle_name . ' ' : '') .
            $this->last_name
        );
    }

    public function getIsArchivedAttribute()
    {
        return $this->employment_status === 'archived';
    }

    public function loginEmail(): ?string
    {
        return $this->work_email ?: $this->personal_email;
    }

    public function generateActivationToken(int $expiresInDays = 7): string
    {
        $token = bin2hex(random_bytes(32));

        $this->forceFill([
            'activation_token' => $token,
            'activation_token_expires_at' => now()->addDays($expiresInDays),
        ])->save();

        return $token;
    }

    public function activateWithPassword(string $password): void
    {
        $this->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'activation_token' => null,
            'activation_token_expires_at' => null,
            'activated_at' => now(),
        ])->save();
    }

    public function getActivationStatusAttribute(): string
    {
        if ($this->activated_at) {
            return 'activated';
        }

        if ($this->activation_token && $this->activation_token_expires_at && $this->activation_token_expires_at->isFuture()) {
            return 'activation_sent';
        }

        if ($this->activation_token) {
            return 'link_expired';
        }

        return 'not_activated';
    }

    public function getActivationStatusLabelAttribute(): string
    {
        return match ($this->activation_status) {
            'activated' => 'Activated',
            'activation_sent' => 'Activation sent',
            'link_expired' => 'Link expired',
            default => 'Not activated',
        };
    }
    public function documents()
{
    return $this->hasMany(
        EmployeeDocument::class,
        'employee_id'
    );
}

public function missingDocuments()
{
    return $this->documents()
        ->where('is_required', true)
        ->where(function ($query) {
            $query->whereNull('file_path')
                ->orWhere('review_status', 'missing');
        });
}

public function expiringDocuments()
{
    return $this->documents()
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '>=', now()->toDateString())
        ->whereDate(
            'expiry_date',
            '<=',
            now()->addDays(30)->toDateString()
        );
}

public function expiredDocuments()
{
    return $this->documents()
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '<', now()->toDateString());
}
public function generateRequiredDocumentChecklist()
{
    $requiredTypes = EmployeeDocumentType::query()
        ->where('status', true)
        ->where('is_required_by_default', true)
        ->get();

    foreach ($requiredTypes as $documentType) {
        $this->documents()->firstOrCreate(
            [
                'document_type_id' => $documentType->id,
            ],
            [
                'title' => $documentType->name,
                'is_required' => true,
                'review_status' => 'missing',
                'created_by' => auth('company')->id(),
                'updated_by' => auth('company')->id(),
            ]
        );
    }
}
public function generateSponsoredWorkerDocumentChecklist()
{
    if (! $this->is_sponsored_worker) {
        return;
    }

    $requiredSlugs = [
        'passport',
        'right-to-work-evidence',
        'employment-contract',
        'proof-of-address',
        'immigration-status-evidence',
        'certificate-of-sponsorship-evidence',
        'sponsored-job-description',
        'recruitment-evidence',
        'qualification-evidence',
        'salary-payment-evidence',
    ];

    $documentTypes = EmployeeDocumentType::query()
        ->where('status', true)
        ->whereIn('slug', $requiredSlugs)
        ->get();

    foreach ($documentTypes as $documentType) {
        $this->documents()->firstOrCreate(
            [
                'document_type_id' => $documentType->id,
            ],
            [
                'title' => $documentType->name,
                'is_required' => true,
                'review_status' => 'missing',
                'created_by' => auth('company')->check()
                    ? auth('company')->id()
                    : null,
                'updated_by' => auth('company')->check()
                    ? auth('company')->id()
                    : null,
            ]
        );
    }
}

public function holidays()
{
    return $this->hasMany(
        EmployeeHoliday::class,
        'employee_id'
    );
}

public function holidayAllowances()
{
    return $this->hasMany(
        EmployeeHolidayAllowance::class,
        'employee_id'
    );
}
public function pushTokens()
{
    return $this->hasMany(EmployeePushToken::class);
}
public function currentHolidayAllowance()
{
    return $this->hasOne(
        EmployeeHolidayAllowance::class,
        'employee_id'
    )->where(
        'holiday_year',
        now()->year
    );
}

public function currentApprovedHoliday()
{
    return $this->hasOne(
        EmployeeHoliday::class,
        'employee_id'
    )
        ->where('status', 'approved')
        ->whereDate(
            'start_date',
            '<=',
            today()
        )
        ->whereDate(
            'end_date',
            '>=',
            today()
        );
}
public function trainingRecords()
{
    return $this->hasMany(
        EmployeeTrainingRecord::class,
        'employee_id'
    );
}

public function completedTrainingRecords()
{
    return $this->hasMany(
        EmployeeTrainingRecord::class,
        'employee_id'
    )->where('status', 'completed');
}

public function expiringTrainingRecords()
{
    return $this->hasMany(
        EmployeeTrainingRecord::class,
        'employee_id'
    )
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '>=', today())
        ->whereDate(
            'expiry_date',
            '<=',
            today()->addDays(30)
        );
}

public function expiredTrainingRecords()
{
    return $this->hasMany(
        EmployeeTrainingRecord::class,
        'employee_id'
    )
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '<', today());
}

public function onboarding()
{
    return $this->hasOne(
        EmployeeOnboarding::class,
        'employee_id'
    );
}
protected static function booted()
{
    static::saved(function (Employee $employee) {
        if (
            $employee->employment_status === 'onboarding' &&
            !$employee->onboarding()->exists()
        ) {
            EmployeeOnboarding::createForEmployee(
                $employee,
                auth('company')->check()
                    ? auth('company')->id()
                    : null
            );
        }
    });
}
public function contracts()
{
    return $this->hasMany(
        EmployeeContract::class,
        'employee_id'
    );
}
public function payslips()
{
    return $this->hasMany(
        EmployeePayslip::class,
        'employee_id'
    );
}

public function announcementReads()
{
    return $this->hasMany(
        AnnouncementRead::class,
        'employee_id'
    );
}

public function shifts()
{
    return $this->hasMany(
        EmployeeShift::class,
        'employee_id'
    );
}

public function jobs()
{
    return $this->hasMany(
        EmployeeJob::class,
        'employee_id'
    );
}

public function attendances()
{
    return $this->hasMany(
        EmployeeAttendance::class,
        'employee_id'
    );
}

public function equipmentAssignments()
{
    return $this->hasMany(EquipmentAssignment::class, 'employee_id');
}

public function expenses()
{
    return $this->hasMany(EmployeeExpense::class, 'employee_id');
}

// Equipment/vehicles currently checked out to this employee (not yet returned).
public function currentEquipmentAssignments()
{
    return $this->hasMany(EquipmentAssignment::class, 'employee_id')
        ->whereNull('returned_at')
        ->with('equipment');
}
public function sponsoredWorkerRecord()
{
    return $this->hasOne(
        SponsoredWorkerRecord::class,
        'employee_id'
    );
}
public function absences()
{
    return $this->hasMany(
        EmployeeAbsence::class,
        'employee_id'
    );
}
}
