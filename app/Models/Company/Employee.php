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
        'employment_status',
        'work_location',
        'normal_weekly_hours',
        'pay_type',
        'pay_rate',
        'payroll_number',
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
        'normal_weekly_hours' => 'decimal:2',
        'pay_rate' => 'decimal:2',
        'activation_token_expires_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(
            Staff::class,
            'staff_id'
        );
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
}