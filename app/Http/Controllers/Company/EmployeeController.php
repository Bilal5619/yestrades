<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Company\EmployeeOnboarding;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
       $query = Employee::query()
    ->withCount([
        'missingDocuments',
        'expiringDocuments',
        'expiredDocuments',
    ])
            ->where('employment_status', '!=', 'archived');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($employeeQuery) use ($search) {
                $employeeQuery
                    ->where('employee_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('personal_email', 'like', "%{$search}%")
                    ->orWhere('work_email', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('employment_status')) {
            $query->where(
                'employment_status',
                $request->employment_status
            );
        }

        if ($request->filled('employment_type')) {
            $query->where(
                'employment_type',
                $request->employment_type
            );
        }

        $employees = $query
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        $departments = Employee::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view(
            'company.hr.employees.index',
            compact('employees', 'departments')
        );
    }

   public function create()
{
    $managers = Employee::query()
        ->whereNotIn('employment_status', [
            'left_company',
            'archived',
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

    return view(
        'company.hr.employees.create',
        compact('managers')
    );
}
public function store(Request $request)
{
    $validated = $request->validate([
        // Personal details
        'employee_number' => [
            'required',
            'string',
            'max:50',
            'unique:employees,employee_number',
        ],
        'first_name' => [
            'required',
            'string',
            'max:100',
        ],
        'middle_name' => [
            'nullable',
            'string',
            'max:100',
        ],
        'last_name' => [
            'required',
            'string',
            'max:100',
        ],
        'date_of_birth' => [
            'nullable',
            'date',
            'before:today',
        ],
        'gender' => [
            'nullable',
            'string',
            'max:50',
        ],
        'national_insurance_number' => [
            'nullable',
            'string',
            'max:30',
        ],

        // Contact details
        'personal_email' => [
            'nullable',
            'email',
            'max:255',
        ],
        'work_email' => [
            'nullable',
            'email',
            'max:255',
            'unique:employees,work_email',
        ],
        'phone' => [
            'nullable',
            'string',
            'max:30',
        ],
        'alternative_phone' => [
            'nullable',
            'string',
            'max:30',
        ],

        // Profile photograph
        'profile_photo' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:3072',
        ],

        // Address
        'address_line_1' => [
            'nullable',
            'string',
            'max:255',
        ],
        'address_line_2' => [
            'nullable',
            'string',
            'max:255',
        ],
        'city' => [
            'nullable',
            'string',
            'max:100',
        ],
        'county' => [
            'nullable',
            'string',
            'max:100',
        ],
        'postcode' => [
            'nullable',
            'string',
            'max:20',
        ],
        'country' => [
            'nullable',
            'string',
            'max:100',
        ],

        // Employment details
        'job_title' => [
            'required',
            'string',
            'max:150',
        ],
        'department' => [
            'required',
            'string',
            'max:150',
        ],
        'manager_id' => [
            'nullable',
            'integer',
            'exists:employees,id',
        ],
        'employment_type' => [
            'required',
            Rule::in([
                'full_time',
                'part_time',
                'temporary',
                'contractor',
                'apprentice',
                'zero_hours',
            ]),
        ],
        'start_date' => [
            'required',
            'date',
        ],
        'probation_end_date' => [
            'nullable',
            'date',
            'after_or_equal:start_date',
        ],
        'employment_status' => [
            'required',
            Rule::in([
                'onboarding',
                'active',
                'on_leave',
                'suspended',
                'notice_period',
                'left_company',
                'archived',
            ]),
        ],
        'work_location' => [
            'nullable',
            'string',
            'max:150',
        ],
        'normal_weekly_hours' => [
            'nullable',
            'numeric',
            'min:0',
            'max:168',
        ],
        'is_sponsored_worker' => [
    'required',
    'boolean',
],
        'pay_type' => [
            'nullable',
            Rule::in([
                'salary',
                'hourly',
            ]),
        ],
        'pay_rate' => [
            'nullable',
            'numeric',
            'min:0',
        ],
        'payroll_number' => [
            'nullable',
            'string',
            'max:100',
        ],

        // Emergency contact
        'emergency_contact_name' => [
            'nullable',
            'string',
            'max:150',
        ],
        'emergency_contact_relationship' => [
            'nullable',
            'string',
            'max:100',
        ],
        'emergency_contact_phone' => [
            'nullable',
            'string',
            'max:30',
        ],
        'emergency_contact_alternative_phone' => [
            'nullable',
            'string',
            'max:30',
        ],
        'emergency_contact_address' => [
            'nullable',
            'string',
            'max:1000',
        ],

        // Notes
        'employment_notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    if ($request->hasFile('profile_photo')) {
        $validated['profile_photo'] = $request
            ->file('profile_photo')
            ->store('company/employees/profile-photos', 'public');
    }

    $validated['country'] =
        $validated['country'] ?? 'United Kingdom';

    $validated['is_field_engineer'] = $request->boolean('is_field_engineer');
    $validated['allow_manual_attendance'] = $request->boolean('allow_manual_attendance');

    $validated['created_by'] = auth('company')->id();
    $validated['updated_by'] = auth('company')->id();

    $employee = Employee::create($validated);
    if ($employee->is_sponsored_worker) {
    \App\Models\Company\SponsoredWorkerRecord::firstOrCreate(
        [
            'employee_id' => $employee->id,
        ],
        [
            'record_status' => 'active',
            'created_by' => auth('company')->id(),
            'updated_by' => auth('company')->id(),
        ]
    );
}
    $employee->generateRequiredDocumentChecklist();
    $employee->generateSponsoredWorkerDocumentChecklist();

    return redirect()
        ->route('company.hr.employees.index')
        ->with(
            'success',
            $employee->full_name . ' was added successfully.'
        );
}
public function show(Employee $employee)
{
    $employee->load([
        'manager',
        'createdBy',
        'updatedBy',
        'sponsoredWorkerRecord',
        'currentHolidayAllowance',
        
        'holidays' => function ($query) {
            $query->orderByDesc('start_date');
        },
         'trainingRecords' => function ($query) {
        $query
            ->with('trainingCourse')
            ->orderByDesc('created_at');
    },
        'currentEquipmentAssignments.equipment',
    ]);

    $rightToWorkDocument = $employee->documents()
        ->whereHas('documentType', function ($query) {
            $query->where(
                'slug',
                'right-to-work-evidence'
            );
        })
        ->first();

    if (! $rightToWorkDocument ||
        ! $rightToWorkDocument->file_path) {
        $rightToWorkStatus = 'Missing';
    } elseif (
        $rightToWorkDocument->expiry_date &&
        $rightToWorkDocument->expiry_date->isPast()
    ) {
        $rightToWorkStatus = 'Expired';
    } elseif (
        $rightToWorkDocument->review_status === 'approved'
    ) {
        $rightToWorkStatus = 'Verified';
    } elseif (
        $rightToWorkDocument->review_status === 'rejected'
    ) {
        $rightToWorkStatus = 'Rejected';
    } else {
        $rightToWorkStatus = 'Pending Review';
    }

    $summary = [
        'missing_documents' => $employee->documents()
            ->where('is_required', true)
            ->where(function ($query) {
                $query->whereNull('file_path')
                    ->orWhere(
                        'review_status',
                        'missing'
                    );
            })
            ->count(),

        'expiring_documents' => $employee->documents()
            ->whereNotNull('file_path')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', today())
            ->whereDate(
                'expiry_date',
                '<=',
                today()->addDays(30)
            )
            ->count(),

        'expired_documents' => $employee->documents()
            ->whereNotNull('file_path')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today())
            ->count(),

        'approved_documents' => $employee->documents()
            ->whereNotNull('file_path')
            ->where('review_status', 'approved')
            ->count(),

        'right_to_work_status' => $rightToWorkStatus,

        // Training module has not been built yet.
'training_due' => $employee->trainingRecords()
    ->where(function ($query) {
        $query
            ->whereIn('status', [
                'not_started',
                'scheduled',
                'in_progress',
                'expired',
            ])
            ->orWhere(function ($trainingQuery) {
                $trainingQuery
                    ->where('status', 'completed')
                    ->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '>=', today())
                    ->whereDate(
                        'expiry_date',
                        '<=',
                        today()->addDays(30)
                    );
            });
    })
    ->count(),
    ];

    $currentYear = now()->year;

    $currentHoliday = $employee->holidays
        ->first(function ($holiday) {
            return
                $holiday->status === 'approved' &&
                $holiday->start_date->lte(today()) &&
                $holiday->end_date->gte(today());
        });
    $pendingHolidays = $employee->holidays
    ->filter(function ($holiday) {
        return $holiday->status === 'pending';
    })
    ->sortBy('start_date');
    $upcomingHolidays = $employee->holidays
        ->filter(function ($holiday) {
            return
                $holiday->status === 'approved' &&
                $holiday->start_date->gt(today());
        })
        ->sortBy('start_date');

    $holidayHistory = $employee->holidays
        ->filter(function ($holiday) {
            return
                $holiday->end_date->lt(today()) ||
                in_array(
                    $holiday->status,
                    ['rejected', 'cancelled'],
                    true
                );
        })
        ->sortByDesc('start_date');

    $holidayAllowance =
        $employee->currentHolidayAllowance;
        $activeTrainingRecords = $employee->trainingRecords
    ->filter(function ($record) {
        return in_array(
            $record->status,
            [
                'not_started',
                'scheduled',
                'in_progress',
            ],
            true
        );
    })
    ->sortBy(function ($record) {
        return $record->scheduled_date
            ? $record->scheduled_date->timestamp
            : PHP_INT_MAX;
    });

$expiringTrainingRecords = $employee->trainingRecords
    ->filter(function ($record) {
        return
            $record->status === 'completed' &&
            $record->expiry_date &&
            $record->expiry_date->gte(today()) &&
            $record->expiry_date->lte(
                today()->addDays(30)
            );
    })
    ->sortBy('expiry_date');

$expiredTrainingRecords = $employee->trainingRecords
    ->filter(function ($record) {
        return
            $record->status === 'expired' ||
            (
                $record->status === 'completed' &&
                $record->expiry_date &&
                $record->expiry_date->lt(today())
            );
    })
    ->sortByDesc('expiry_date');

$completedTrainingRecords = $employee->trainingRecords
    ->filter(function ($record) {
        return $record->status === 'completed';
    })
    ->sortByDesc('completed_at');

   return view(
    'company.hr.employees.show',
    compact(
        'employee',
        'summary',
        'currentYear',
        'currentHoliday',
        'pendingHolidays',
        'upcomingHolidays',
        'holidayHistory',
        'holidayAllowance',
        'activeTrainingRecords',
        'expiringTrainingRecords',
        'expiredTrainingRecords',
        'completedTrainingRecords'
    )
);
}

public function sendActivation(Employee $employee)
{
    $loginEmail = $employee->loginEmail();

    if (!$loginEmail) {
        return back()->with('error', 'This employee has no work or personal email on file, so an activation link cannot be sent.');
    }

    $token = $employee->generateActivationToken();
    $activationUrl = route('frontend.employee.activate.show', $token);

    try {
        \Illuminate\Support\Facades\Mail::to($loginEmail)->send(
            new \App\Mail\Company\EmployeeActivationMail($employee, $activationUrl)
        );
    } catch (\Throwable $exception) {
        return back()->with('warning', 'Activation link was generated, but the email could not be sent: ' . $exception->getMessage());
    }

    return back()->with('success', 'Activation link sent to ' . $loginEmail . '.');
}

public function edit(Employee $employee)
{
    $managers = Employee::query()
        ->where('id', '!=', $employee->id)
        ->whereNotIn('employment_status', [
            'left_company',
            'archived',
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

    return view(
        'company.hr.employees.edit',
        compact('employee', 'managers')
    );
}

public function update(Request $request, Employee $employee)
{
    $validated = $request->validate([
        'employee_number' => [
            'required',
            'string',
            'max:50',
            Rule::unique('employees', 'employee_number')
                ->ignore($employee->id),
        ],
        'first_name' => [
            'required',
            'string',
            'max:100',
        ],
        'middle_name' => [
            'nullable',
            'string',
            'max:100',
        ],
        'last_name' => [
            'required',
            'string',
            'max:100',
        ],
        'date_of_birth' => [
            'nullable',
            'date',
            'before:today',
        ],
        'gender' => [
            'nullable',
            'string',
            'max:50',
        ],
        'national_insurance_number' => [
            'nullable',
            'string',
            'max:30',
        ],

        'personal_email' => [
            'nullable',
            'email',
            'max:255',
        ],
        'work_email' => [
            'nullable',
            'email',
            'max:255',
            Rule::unique('employees', 'work_email')
                ->ignore($employee->id),
        ],
        'phone' => [
            'nullable',
            'string',
            'max:30',
        ],
        'alternative_phone' => [
            'nullable',
            'string',
            'max:30',
        ],
        'profile_photo' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:3072',
        ],

        'address_line_1' => [
            'nullable',
            'string',
            'max:255',
        ],
        'address_line_2' => [
            'nullable',
            'string',
            'max:255',
        ],
        'city' => [
            'nullable',
            'string',
            'max:100',
        ],
        'county' => [
            'nullable',
            'string',
            'max:100',
        ],
        'postcode' => [
            'nullable',
            'string',
            'max:20',
        ],
        'country' => [
            'nullable',
            'string',
            'max:100',
        ],

        'job_title' => [
            'required',
            'string',
            'max:150',
        ],
        'department' => [
            'required',
            'string',
            'max:150',
        ],
        'manager_id' => [
            'nullable',
            'integer',
            'exists:employees,id',
            Rule::notIn([$employee->id]),
        ],
        'employment_type' => [
            'required',
            Rule::in([
                'full_time',
                'part_time',
                'temporary',
                'contractor',
                'apprentice',
                'zero_hours',
            ]),
        ],
        'employment_status' => [
            'required',
            Rule::in([
                'onboarding',
                'active',
                'on_leave',
                'suspended',
                'notice_period',
                'left_company',
                'archived',
            ]),
        ],
        'start_date' => [
            'required',
            'date',
        ],
        'probation_end_date' => [
            'nullable',
            'date',
            'after_or_equal:start_date',
        ],
        'leaving_date' => [
            'nullable',
            'date',
            'after_or_equal:start_date',
        ],
        'work_location' => [
            'nullable',
            'string',
            'max:150',
        ],
        'normal_weekly_hours' => [
            'nullable',
            'numeric',
            'min:0',
            'max:168',
        ],
        'is_sponsored_worker' => [
    'required',
    'boolean',
],
        'pay_type' => [
            'nullable',
            Rule::in([
                'salary',
                'hourly',
            ]),
        ],
        'pay_rate' => [
            'nullable',
            'numeric',
            'min:0',
        ],
        'payroll_number' => [
            'nullable',
            'string',
            'max:100',
        ],

        'emergency_contact_name' => [
            'nullable',
            'string',
            'max:150',
        ],
        'emergency_contact_relationship' => [
            'nullable',
            'string',
            'max:100',
        ],
        'emergency_contact_phone' => [
            'nullable',
            'string',
            'max:30',
        ],
        'emergency_contact_alternative_phone' => [
            'nullable',
            'string',
            'max:30',
        ],
        'emergency_contact_address' => [
            'nullable',
            'string',
            'max:1000',
        ],
        'employment_notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    if ($request->hasFile('profile_photo')) {
        if ($employee->profile_photo) {
            Storage::disk('public')->delete(
                $employee->profile_photo
            );
        }

        $validated['profile_photo'] = $request
            ->file('profile_photo')
            ->store(
                'company/employees/profile-photos',
                'public'
            );
    }

    $validated['country'] =
        $validated['country'] ?? 'United Kingdom';

    $validated['is_field_engineer'] = $request->boolean('is_field_engineer');
    $validated['allow_manual_attendance'] = $request->boolean('allow_manual_attendance');

    $validated['updated_by'] = auth('company')->id();

    $employee->update($validated);
    if ($employee->is_sponsored_worker) {
    $sponsoredWorkerRecord =
        \App\Models\Company\SponsoredWorkerRecord::firstOrCreate(
            [
                'employee_id' => $employee->id,
            ],
            [
                'record_status' => 'active',
                'created_by' => auth('company')->id(),
                'updated_by' => auth('company')->id(),
            ]
        );

    if ($sponsoredWorkerRecord->record_status === 'inactive') {
        $sponsoredWorkerRecord->update([
            'record_status' => 'active',
            'updated_by' => auth('company')->id(),
        ]);
    }
} elseif ($employee->sponsoredWorkerRecord) {
    $employee->sponsoredWorkerRecord->update([
        'record_status' => 'inactive',
        'updated_by' => auth('company')->id(),
    ]);
}
    if (
    $employee->employment_status === 'onboarding'
) {
    EmployeeOnboarding::createForEmployee(
        $employee,
        auth('company')->id()
    );
}
if ($employee->is_sponsored_worker) {
    $employee->generateSponsoredWorkerDocumentChecklist();
}
    return redirect()
        ->route(
            'company.hr.employees.show',
            $employee
        )
        ->with(
            'success',
            'Employee details updated successfully.'
        );
}
public function missingDocuments(Request $request)
{
    $query = Employee::query()
        ->where('employment_status', '!=', 'archived')
        ->whereHas('documents', function ($documentQuery) {
            $documentQuery
                ->where('is_required', true)
                ->where(function ($missingQuery) {
                    $missingQuery
                        ->whereNull('file_path')
                        ->orWhere('review_status', 'missing');
                });
        })
        ->with([
            'documents' => function ($documentQuery) {
                $documentQuery
                    ->where('is_required', true)
                    ->where(function ($missingQuery) {
                        $missingQuery
                            ->whereNull('file_path')
                            ->orWhere('review_status', 'missing');
                    })
                    ->with('documentType');
            },
        ])
        ->withCount([
            'documents as missing_documents_count' =>
                function ($documentQuery) {
                    $documentQuery
                        ->where('is_required', true)
                        ->where(function ($missingQuery) {
                            $missingQuery
                                ->whereNull('file_path')
                                ->orWhere(
                                    'review_status',
                                    'missing'
                                );
                        });
                },
        ]);

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($employeeQuery) use ($search) {
            $employeeQuery
                ->where(
                    'employee_number',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'first_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'last_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'job_title',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'department',
                    'like',
                    "%{$search}%"
                );
        });
    }

    if ($request->filled('department')) {
        $query->where(
            'department',
            $request->department
        );
    }

    $employees = $query
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->paginate(15)
        ->withQueryString();

    $departments = Employee::query()
        ->whereNotNull('department')
        ->where('department', '!=', '')
        ->distinct()
        ->orderBy('department')
        ->pluck('department');

    return view(
        'company.hr.employees.missing-documents',
        compact('employees', 'departments')
    );
}

public function exportMissingDocuments(Request $request)
{
    $query = Employee::query()
        ->where('employment_status', '!=', 'archived')
        ->whereHas('documents', function ($documentQuery) {
            $documentQuery
                ->where('is_required', true)
                ->where(function ($missingQuery) {
                    $missingQuery
                        ->whereNull('file_path')
                        ->orWhere('review_status', 'missing');
                });
        })
        ->with([
            'documents' => function ($documentQuery) {
                $documentQuery
                    ->where('is_required', true)
                    ->where(function ($missingQuery) {
                        $missingQuery
                            ->whereNull('file_path')
                            ->orWhere('review_status', 'missing');
                    })
                    ->with('documentType');
            },
        ]);

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($employeeQuery) use ($search) {
            $employeeQuery
                ->where('employee_number', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('job_title', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%");
        });
    }

    if ($request->filled('department')) {
        $query->where('department', $request->department);
    }

    $employees = $query->orderBy('first_name')->orderBy('last_name')->get();

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\MissingDocumentsExport($employees),
        'missing-documents-report-' . now()->format('Y-m-d') . '.xlsx'
    );
}

public function archived(Request $request)
{
    $query = Employee::query()
        ->where('employment_status', 'archived');

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($employeeQuery) use ($search) {
            $employeeQuery
                ->where(
                    'employee_number',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'first_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'middle_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'last_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'personal_email',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'work_email',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'job_title',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'department',
                    'like',
                    "%{$search}%"
                );
        });
    }

    if ($request->filled('department')) {
        $query->where(
            'department',
            $request->department
        );
    }

    $employees = $query
        ->withCount([
            'missingDocuments',
            'expiringDocuments',
            'expiredDocuments',
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->paginate(15)
        ->withQueryString();

    $departments = Employee::query()
        ->where('employment_status', 'archived')
        ->whereNotNull('department')
        ->where('department', '!=', '')
        ->distinct()
        ->orderBy('department')
        ->pluck('department');

    return view(
        'company.hr.employees.archived',
        compact('employees', 'departments')
    );
}

public function restore(Employee $employee)
{
    if ($employee->employment_status !== 'archived') {
        return back()->withErrors([
            'employee' => 'This employee is not archived.',
        ]);
    }

    $employee->update([
        'employment_status' => 'active',
        'updated_by' => auth('company')->id(),
    ]);

    return redirect()
        ->route('company.hr.employees.archived')
        ->with(
            'success',
            $employee->full_name .
            ' was restored successfully.'
        );
}
}