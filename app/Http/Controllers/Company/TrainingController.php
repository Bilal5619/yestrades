<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeTrainingRecord;
use App\Models\Company\TrainingCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    public function index()
    {
        $today = today();
        $thirtyDaysFromNow = today()->addDays(30);

        $activeEmployeeStatuses = [
            'onboarding',
            'active',
            'on_leave',
            'suspended',
            'notice_period',
        ];

        $stats = [
            'total_records' => EmployeeTrainingRecord::query()
                ->whereHas('employee', function ($query) use (
                    $activeEmployeeStatuses
                ) {
                    $query->whereIn(
                        'employment_status',
                        $activeEmployeeStatuses
                    );
                })
                ->count(),

            'due_soon' => EmployeeTrainingRecord::query()
                ->where('status', 'completed')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', $today)
                ->whereDate(
                    'expiry_date',
                    '<=',
                    $thirtyDaysFromNow
                )
                ->whereHas('employee', function ($query) use (
                    $activeEmployeeStatuses
                ) {
                    $query->whereIn(
                        'employment_status',
                        $activeEmployeeStatuses
                    );
                })
                ->count(),

            'expired' => EmployeeTrainingRecord::query()
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<', $today)
                ->whereHas('employee', function ($query) use (
                    $activeEmployeeStatuses
                ) {
                    $query->whereIn(
                        'employment_status',
                        $activeEmployeeStatuses
                    );
                })
                ->count(),

            'scheduled' => EmployeeTrainingRecord::query()
                ->where('status', 'scheduled')
                ->whereHas('employee', function ($query) use (
                    $activeEmployeeStatuses
                ) {
                    $query->whereIn(
                        'employment_status',
                        $activeEmployeeStatuses
                    );
                })
                ->count(),

            'completed_this_year' =>
                EmployeeTrainingRecord::query()
                    ->where('status', 'completed')
                    ->whereYear(
                        'completed_at',
                        now()->year
                    )
                    ->whereHas('employee', function ($query) use (
                        $activeEmployeeStatuses
                    ) {
                        $query->whereIn(
                            'employment_status',
                            $activeEmployeeStatuses
                        );
                    })
                    ->count(),

            'active_courses' => TrainingCourse::query()
                ->where('is_active', true)
                ->count(),
        ];

        $dueSoonRecords = EmployeeTrainingRecord::query()
            ->with([
                'employee',
                'trainingCourse',
            ])
            ->where('status', 'completed')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate(
                'expiry_date',
                '<=',
                $thirtyDaysFromNow
            )
            ->whereHas('employee', function ($query) use (
                $activeEmployeeStatuses
            ) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->orderBy('expiry_date')
            ->take(10)
            ->get();

        $expiredRecords = EmployeeTrainingRecord::query()
            ->with([
                'employee',
                'trainingCourse',
            ])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today)
            ->whereHas('employee', function ($query) use (
                $activeEmployeeStatuses
            ) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->orderBy('expiry_date')
            ->take(10)
            ->get();

        $scheduledRecords = EmployeeTrainingRecord::query()
            ->with([
                'employee',
                'trainingCourse',
            ])
            ->where('status', 'scheduled')
            ->whereHas('employee', function ($query) use (
                $activeEmployeeStatuses
            ) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->orderBy('scheduled_date')
            ->take(10)
            ->get();

       

     $recentlyCompleted = EmployeeTrainingRecord::query()
            ->with([
                'employee',
                'trainingCourse',
            ])
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->whereHas('employee', function ($query) use (
                $activeEmployeeStatuses
            ) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->orderByDesc('completed_at')
            ->take(10)
            ->get();

        $mandatoryCourses = TrainingCourse::query()
            ->where('is_active', true)
            ->where('is_mandatory', true)
            ->get();

        $employeesMissingMandatoryTraining = collect();

        if ($mandatoryCourses->isNotEmpty()) {
            $employeesMissingMandatoryTraining = Employee::query()
                ->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                )
                ->with([
                    'trainingRecords' => function ($query) {
                        $query->where('status', 'completed');
                    },
                ])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->filter(function ($employee) use (
                    $mandatoryCourses
                ) {
                    $completedCourseIds = $employee
                        ->trainingRecords
                        ->pluck('training_course_id')
                        ->unique();

                    return $mandatoryCourses
                        ->pluck('id')
                        ->diff($completedCourseIds)
                        ->isNotEmpty();
                })
                ->take(10);
        }

        return view(
            'company.hr.training.index',
            compact(
                'stats',
                'dueSoonRecords',
                'expiredRecords',
                'scheduledRecords',
                'recentlyCompleted',
                'employeesMissingMandatoryTraining'
            )
        );
    }
    public function courses(Request $request)
{
    $query = TrainingCourse::query()
        ->withCount('trainingRecords');

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($courseQuery) use ($search) {
            $courseQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where(
            'is_active',
            $request->status === 'active'
        );
    }

    if ($request->filled('mandatory')) {
        $query->where(
            'is_mandatory',
            $request->mandatory === 'yes'
        );
    }

    $courses = $query
        ->orderBy('name')
        ->paginate(15)
        ->withQueryString();

    return view(
        'company.hr.training.courses.index',
        compact('courses')
    );
}

public function createCourse()
{
    return view(
        'company.hr.training.courses.create'
    );
}

public function storeCourse(Request $request)
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:180',
            'unique:training_courses,name',
        ],

        'category' => [
            'nullable',
            'string',
            'max:120',
        ],

        'description' => [
            'nullable',
            'string',
            'max:3000',
        ],

        'is_mandatory' => [
            'nullable',
            'boolean',
        ],

        'requires_certificate' => [
            'nullable',
            'boolean',
        ],

        'has_expiry' => [
            'nullable',
            'boolean',
        ],

        'validity_months' => [
            'nullable',
            'integer',
            'min:1',
            'max:1200',
            Rule::requiredIf(
                $request->boolean('has_expiry')
            ),
        ],

        'is_active' => [
            'nullable',
            'boolean',
        ],
    ]);

    TrainingCourse::create([
        'name' => $validated['name'],

        'slug' => $this->generateUniqueCourseSlug(
            $validated['name']
        ),

        'category' =>
            $validated['category'] ?? null,

        'description' =>
            $validated['description'] ?? null,

        'is_mandatory' =>
            $request->boolean('is_mandatory'),

        'requires_certificate' =>
            $request->boolean('requires_certificate'),

        'has_expiry' =>
            $request->boolean('has_expiry'),

        'validity_months' =>
            $request->boolean('has_expiry')
                ? $validated['validity_months']
                : null,

        'is_active' =>
            $request->boolean('is_active'),

        'created_by' =>
            auth('company')->id(),

        'updated_by' =>
            auth('company')->id(),
    ]);

    return redirect()
        ->route('company.hr.training.courses.index')
        ->with(
            'success',
            'Training course created successfully.'
        );
}

public function editCourse(TrainingCourse $course)
{
    return view(
        'company.hr.training.courses.edit',
        compact('course')
    );
}

public function updateCourse(
    Request $request,
    TrainingCourse $course
) {
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:180',
            Rule::unique(
                'training_courses',
                'name'
            )->ignore($course->id),
        ],

        'category' => [
            'nullable',
            'string',
            'max:120',
        ],

        'description' => [
            'nullable',
            'string',
            'max:3000',
        ],

        'is_mandatory' => [
            'nullable',
            'boolean',
        ],

        'requires_certificate' => [
            'nullable',
            'boolean',
        ],

        'has_expiry' => [
            'nullable',
            'boolean',
        ],

        'validity_months' => [
            'nullable',
            'integer',
            'min:1',
            'max:1200',
            Rule::requiredIf(
                $request->boolean('has_expiry')
            ),
        ],

        'is_active' => [
            'nullable',
            'boolean',
        ],
    ]);

    $course->update([
        'name' => $validated['name'],

        'slug' => $this->generateUniqueCourseSlug(
            $validated['name'],
            $course->id
        ),

        'category' =>
            $validated['category'] ?? null,

        'description' =>
            $validated['description'] ?? null,

        'is_mandatory' =>
            $request->boolean('is_mandatory'),

        'requires_certificate' =>
            $request->boolean('requires_certificate'),

        'has_expiry' =>
            $request->boolean('has_expiry'),

        'validity_months' =>
            $request->boolean('has_expiry')
                ? $validated['validity_months']
                : null,

        'is_active' =>
            $request->boolean('is_active'),

        'updated_by' =>
            auth('company')->id(),
    ]);

    return redirect()
        ->route('company.hr.training.courses.index')
        ->with(
            'success',
            'Training course updated successfully.'
        );
}

private function generateUniqueCourseSlug(
    string $name,
    ?int $ignoreCourseId = null
): string {
    $baseSlug = Str::slug($name);
    $slug = $baseSlug;
    $number = 2;

    while (
        TrainingCourse::query()
            ->where('slug', $slug)
            ->when(
                $ignoreCourseId,
                function ($query) use ($ignoreCourseId) {
                    $query->where(
                        'id',
                        '!=',
                        $ignoreCourseId
                    );
                }
            )
            ->exists()
    ) {
        $slug = $baseSlug . '-' . $number;
        $number++;
    }

    return $slug;
}

public function records(Request $request)
{
    $query = EmployeeTrainingRecord::query()
        ->with([
            'employee',
            'trainingCourse',
        ]);

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($recordQuery) use ($search) {
            $recordQuery
                ->where('provider_name', 'like', "%{$search}%")
                ->orWhereHas(
                    'employee',
                    function ($employeeQuery) use ($search) {
                        $employeeQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere(
                                'employee_number',
                                'like',
                                "%{$search}%"
                            );
                    }
                )
                ->orWhereHas(
                    'trainingCourse',
                    function ($courseQuery) use ($search) {
                        $courseQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('course_id')) {
        $query->where(
            'training_course_id',
            $request->course_id
        );
    }

    $records = $query
        ->orderByRaw("
            CASE
                WHEN status = 'scheduled' THEN 1
                WHEN status = 'not_started' THEN 2
                WHEN status = 'in_progress' THEN 3
                WHEN status = 'completed' THEN 4
                WHEN status = 'expired' THEN 5
                WHEN status = 'cancelled' THEN 6
                ELSE 7
            END
        ")
        ->orderBy('scheduled_date')
        ->paginate(15)
        ->withQueryString();

    $courses = TrainingCourse::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view(
        'company.hr.training.records.index',
        compact(
            'records',
            'courses'
        )
    );
}

public function createRecord(Request $request)
{
    $employees = Employee::query()
        ->whereNotIn('employment_status', [
            'archived',
            'left_company',
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

    $courses = TrainingCourse::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $selectedEmployeeId =
        $request->integer('employee_id');

    return view(
        'company.hr.training.records.create',
        compact(
            'employees',
            'courses',
            'selectedEmployeeId'
        )
    );
}

public function storeRecord(Request $request)
{
    $validated = $request->validate([
        'employee_id' => [
            'required',
            'integer',
            Rule::exists('employees', 'id')
                ->where(function ($query) {
                    $query->whereNotIn(
                        'employment_status',
                        [
                            'archived',
                            'left_company',
                        ]
                    );
                }),
        ],

        'training_course_id' => [
            'required',
            'integer',
            Rule::exists('training_courses', 'id')
                ->where(function ($query) {
                    $query->where('is_active', true);
                }),
        ],

        'provider_name' => [
            'nullable',
            'string',
            'max:180',
        ],

        'scheduled_date' => [
            'nullable',
            'date',
        ],

        'status' => [
            'required',
            Rule::in([
                'not_started',
                'scheduled',
                'in_progress',
            ]),
        ],

        'cost' => [
            'nullable',
            'numeric',
            'min:0',
            'max:99999999.99',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:3000',
        ],
    ]);

    if (
        $validated['status'] === 'scheduled' &&
        empty($validated['scheduled_date'])
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'scheduled_date' =>
                    'A scheduled date is required when the status is Scheduled.',
            ]);
    }

    $duplicateRecord = EmployeeTrainingRecord::query()
        ->where(
            'employee_id',
            $validated['employee_id']
        )
        ->where(
            'training_course_id',
            $validated['training_course_id']
        )
        ->whereIn('status', [
            'not_started',
            'scheduled',
            'in_progress',
        ])
        ->exists();

    if ($duplicateRecord) {
        return back()
            ->withInput()
            ->withErrors([
                'training_course_id' =>
                    'This employee already has an active record for this training course.',
            ]);
    }

    EmployeeTrainingRecord::create([
        'employee_id' =>
            $validated['employee_id'],

        'training_course_id' =>
            $validated['training_course_id'],

        'provider_name' =>
            $validated['provider_name'] ?? null,

        'scheduled_date' =>
            $validated['scheduled_date'] ?? null,

        'status' =>
            $validated['status'],

        'started_at' =>
            $validated['status'] === 'in_progress'
                ? now()
                : null,

        'cost' =>
            $validated['cost'] ?? null,

        'notes' =>
            $validated['notes'] ?? null,

        'created_by' =>
            auth('company')->id(),

        'updated_by' =>
            auth('company')->id(),
    ]);

    return redirect()
        ->route('company.hr.training.records.index')
        ->with(
            'success',
            'Training assigned successfully.'
        );
}
public function showRecord(
    EmployeeTrainingRecord $record
) {
    $record->load([
        'employee',
        'trainingCourse',
        'createdBy',
        'updatedBy',
    ]);

    return view(
        'company.hr.training.records.show',
        compact('record')
    );
}

public function completeRecord(
    Request $request,
    EmployeeTrainingRecord $record
) {
    if (
        in_array(
            $record->status,
            ['completed', 'expired', 'cancelled'],
            true
        )
    ) {
        return back()->withErrors([
            'training' =>
                'This training record cannot be completed.',
        ]);
    }

    $record->load('trainingCourse');

    $course = $record->trainingCourse;

    $validated = $request->validate([
        'completed_date' => [
            'required',
            'date',
            'before_or_equal:today',
        ],

        'certificate_number' => [
            'nullable',
            'string',
            'max:150',
        ],

        'certificate' => [
            Rule::requiredIf(
                $course &&
                $course->requires_certificate &&
                empty($record->certificate_path)
            ),
            'nullable',
            'file',
            'mimes:pdf,jpg,jpeg,png',
            'max:5120',
        ],

        'completion_notes' => [
            'nullable',
            'string',
            'max:3000',
        ],
    ]);

    $completedDate = Carbon::parse(
        $validated['completed_date']
    )->startOfDay();

    $expiryDate = null;

    if (
        $course &&
        $course->has_expiry &&
        $course->validity_months
    ) {
        $expiryDate = $completedDate
            ->copy()
            ->addMonthsNoOverflow(
                $course->validity_months
            )
            ->toDateString();
    }

    if ($request->hasFile('certificate')) {
        if ($record->certificate_path) {
            Storage::disk('local')->delete(
                $record->certificate_path
            );
        }

        $record->certificate_path = $request
            ->file('certificate')
            ->store(
                'company/training-certificates',
                'local'
            );
    }

    $record->status = 'completed';

    $record->completed_at =
        $completedDate;

    $record->expiry_date =
        $expiryDate;

    $record->certificate_number =
        $validated['certificate_number'] ?? null;

    if (! empty($validated['completion_notes'])) {
        $existingNotes = trim(
            (string) $record->notes
        );

        $completionNotes = trim(
            $validated['completion_notes']
        );

        $record->notes = $existingNotes
            ? $existingNotes .
                "\n\nCompletion notes: " .
                $completionNotes
            : 'Completion notes: ' .
                $completionNotes;
    }

    $record->updated_by =
        auth('company')->id();

    $record->save();

    return redirect()
        ->route(
            'company.hr.training.records.show',
            $record
        )
        ->with(
            'success',
            'Training marked as completed successfully.'
        );
}

public function downloadCertificate(
    EmployeeTrainingRecord $record
) {
    if (! $record->certificate_path) {
        abort(404, 'Certificate not found.');
    }

    if (
        ! Storage::disk('local')->exists(
            $record->certificate_path
        )
    ) {
        abort(404, 'Certificate file is missing.');
    }

    $record->load([
        'employee',
        'trainingCourse',
    ]);

    $extension = pathinfo(
        $record->certificate_path,
        PATHINFO_EXTENSION
    );

    $employeeName = trim(
        optional($record->employee)->first_name .
        '-' .
        optional($record->employee)->last_name
    );

    $courseName = optional(
        $record->trainingCourse
    )->name ?: 'training';

    $fileName =
        \Illuminate\Support\Str::slug(
            $employeeName . '-' . $courseName
        ) .
        '-certificate.' .
        $extension;

    return Storage::disk('local')->download(
        $record->certificate_path,
        $fileName
    );
}
public function editRecord(
    EmployeeTrainingRecord $record
) {
    if (
        in_array(
            $record->status,
            ['completed', 'expired', 'cancelled'],
            true
        )
    ) {
        return redirect()
            ->route(
                'company.hr.training.records.show',
                $record
            )
            ->withErrors([
                'training' =>
                    'Completed, expired or cancelled training records cannot be edited.',
            ]);
    }

    $employees = Employee::query()
        ->whereNotIn('employment_status', [
            'archived',
            'left_company',
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

    $courses = TrainingCourse::query()
        ->where('is_active', true)
        ->orWhere('id', $record->training_course_id)
        ->orderBy('name')
        ->get();

    return view(
        'company.hr.training.records.edit',
        compact(
            'record',
            'employees',
            'courses'
        )
    );
}

public function updateRecord(
    Request $request,
    EmployeeTrainingRecord $record
) {
    if (
        in_array(
            $record->status,
            ['completed', 'expired', 'cancelled'],
            true
        )
    ) {
        return back()->withErrors([
            'training' =>
                'Completed, expired or cancelled training records cannot be edited.',
        ]);
    }

    $validated = $request->validate([
        'employee_id' => [
            'required',
            'integer',
            Rule::exists('employees', 'id')
                ->where(function ($query) {
                    $query->whereNotIn(
                        'employment_status',
                        [
                            'archived',
                            'left_company',
                        ]
                    );
                }),
        ],

        'training_course_id' => [
            'required',
            'integer',
            'exists:training_courses,id',
        ],

        'provider_name' => [
            'nullable',
            'string',
            'max:180',
        ],

        'scheduled_date' => [
            'nullable',
            'date',
        ],

        'status' => [
            'required',
            Rule::in([
                'not_started',
                'scheduled',
                'in_progress',
            ]),
        ],

        'cost' => [
            'nullable',
            'numeric',
            'min:0',
            'max:99999999.99',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:3000',
        ],
    ]);

    if (
        $validated['status'] === 'scheduled' &&
        empty($validated['scheduled_date'])
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'scheduled_date' =>
                    'A scheduled date is required when the status is Scheduled.',
            ]);
    }

    $duplicateRecord = EmployeeTrainingRecord::query()
        ->where('id', '!=', $record->id)
        ->where(
            'employee_id',
            $validated['employee_id']
        )
        ->where(
            'training_course_id',
            $validated['training_course_id']
        )
        ->whereIn('status', [
            'not_started',
            'scheduled',
            'in_progress',
        ])
        ->exists();

    if ($duplicateRecord) {
        return back()
            ->withInput()
            ->withErrors([
                'training_course_id' =>
                    'This employee already has another active record for this course.',
            ]);
    }

    $record->employee_id =
        $validated['employee_id'];

    $record->training_course_id =
        $validated['training_course_id'];

    $record->provider_name =
        $validated['provider_name'] ?? null;

    $record->scheduled_date =
        $validated['scheduled_date'] ?? null;

    $record->status =
        $validated['status'];

    if (
        $validated['status'] === 'in_progress' &&
        ! $record->started_at
    ) {
        $record->started_at = now();
    }

    if ($validated['status'] !== 'in_progress') {
        $record->started_at = null;
    }

    $record->cost =
        $validated['cost'] ?? null;

    $record->notes =
        $validated['notes'] ?? null;

    $record->updated_by =
        auth('company')->id();

    $record->save();

    return redirect()
        ->route(
            'company.hr.training.records.show',
            $record
        )
        ->with(
            'success',
            'Training record updated successfully.'
        );
}

public function cancelRecord(
    Request $request,
    EmployeeTrainingRecord $record
) {
    if (
        ! in_array(
            $record->status,
            [
                'not_started',
                'scheduled',
                'in_progress',
            ],
            true
        )
    ) {
        return back()->withErrors([
            'training' =>
                'Only active training records can be cancelled.',
        ]);
    }

    $validated = $request->validate([
        'cancellation_reason' => [
            'required',
            'string',
            'max:2000',
        ],
    ]);

    $existingNotes = trim(
        (string) $record->notes
    );

    $cancellationNote =
        'Cancellation reason: ' .
        trim($validated['cancellation_reason']);

    $record->notes = $existingNotes
        ? $existingNotes . "\n\n" . $cancellationNote
        : $cancellationNote;

    $record->status = 'cancelled';
    $record->updated_by = auth('company')->id();
    $record->save();

    return redirect()
        ->route(
            'company.hr.training.records.show',
            $record
        )
        ->with(
            'success',
            'Training record cancelled successfully.'
        );
}


public function expiring(Request $request)
{
    $today = today();

    $query = EmployeeTrainingRecord::query()
        ->with([
            'employee',
            'trainingCourse',
        ])
        ->where('status', 'completed')
        ->whereNotNull('expiry_date')
        ->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->whereNotIn(
                'employment_status',
                [
                    'archived',
                    'left_company',
                ]
            );
        });

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($recordQuery) use ($search) {
            $recordQuery
                ->whereHas(
                    'employee',
                    function ($employeeQuery) use ($search) {
                        $employeeQuery
                            ->where(
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
                                'employee_number',
                                'like',
                                "%{$search}%"
                            );
                    }
                )
                ->orWhereHas(
                    'trainingCourse',
                    function ($courseQuery) use ($search) {
                        $courseQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
        });
    }

    if ($request->filled('course_id')) {
        $query->where(
            'training_course_id',
            $request->course_id
        );
    }

    if ($request->filled('department')) {
        $query->whereHas(
            'employee',
            function ($employeeQuery) use ($request) {
                $employeeQuery->where(
                    'department',
                    $request->department
                );
            }
        );
    }

    $expiryPeriod = $request->get(
        'expiry_period',
        '90_days'
    );

    if ($expiryPeriod === 'expired') {
        $query->whereDate(
            'expiry_date',
            '<',
            $today
        );
    } elseif ($expiryPeriod === '30_days') {
        $query
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate(
                'expiry_date',
                '<=',
                $today->copy()->addDays(30)
            );
    } elseif ($expiryPeriod === '60_days') {
        $query
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate(
                'expiry_date',
                '<=',
                $today->copy()->addDays(60)
            );
    } else {
        $query
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate(
                'expiry_date',
                '<=',
                $today->copy()->addDays(90)
            );
    }

    $records = $query
        ->orderBy('expiry_date')
        ->paginate(15)
        ->withQueryString();

    $courses = TrainingCourse::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $departments = Employee::query()
        ->whereNotNull('department')
        ->where('department', '!=', '')
        ->distinct()
        ->orderBy('department')
        ->pluck('department');

    $stats = [
        'within_30_days' => EmployeeTrainingRecord::query()
            ->where('status', 'completed')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate(
                'expiry_date',
                '<=',
                $today->copy()->addDays(30)
            )
            ->count(),

        'within_60_days' => EmployeeTrainingRecord::query()
            ->where('status', 'completed')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate(
                'expiry_date',
                '<=',
                $today->copy()->addDays(60)
            )
            ->count(),

        'within_90_days' => EmployeeTrainingRecord::query()
            ->where('status', 'completed')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate(
                'expiry_date',
                '<=',
                $today->copy()->addDays(90)
            )
            ->count(),

        'expired' => EmployeeTrainingRecord::query()
            ->where('status', 'completed')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today)
            ->count(),
    ];

    return view(
        'company.hr.training.expiring',
        compact(
            'records',
            'courses',
            'departments',
            'expiryPeriod',
            'stats'
        )
    );
}

public function history(Request $request)
{
    $query = EmployeeTrainingRecord::query()
        ->with([
            'employee',
            'trainingCourse',
        ])
        ->whereIn('status', [
            'completed',
            'expired',
            'cancelled',
        ])
        ->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->whereNotIn(
                'employment_status',
                [
                    'archived',
                    'left_company',
                ]
            );
        });

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($recordQuery) use ($search) {
            $recordQuery
                ->where('provider_name', 'like', "%{$search}%")
                ->orWhereHas(
                    'employee',
                    function ($employeeQuery) use ($search) {
                        $employeeQuery
                            ->where(
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
                                'employee_number',
                                'like',
                                "%{$search}%"
                            );
                    }
                )
                ->orWhereHas(
                    'trainingCourse',
                    function ($courseQuery) use ($search) {
                        $courseQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
        });
    }

    if ($request->filled('status')) {
        $query->where(
            'status',
            $request->status
        );
    }

    if ($request->filled('course_id')) {
        $query->where(
            'training_course_id',
            $request->course_id
        );
    }

    if ($request->filled('year')) {
        $year = (int) $request->year;

        $query->where(function ($yearQuery) use ($year) {
            $yearQuery
                ->whereYear('completed_at', $year)
                ->orWhereYear('created_at', $year);
        });
    }

    $records = $query
        ->orderByRaw("
            CASE
                WHEN status = 'expired' THEN 1
                WHEN status = 'completed' THEN 2
                WHEN status = 'cancelled' THEN 3
                ELSE 4
            END
        ")
        ->orderByDesc('completed_at')
        ->orderByDesc('created_at')
        ->paginate(15)
        ->withQueryString();

    $courses = TrainingCourse::query()
        ->orderBy('name')
        ->get();

    $years = EmployeeTrainingRecord::query()
        ->selectRaw("
            YEAR(
                COALESCE(completed_at, created_at)
            ) as training_year
        ")
        ->whereIn('status', [
            'completed',
            'expired',
            'cancelled',
        ])
        ->distinct()
        ->orderByDesc('training_year')
        ->pluck('training_year')
        ->filter();

    $stats = [
        'completed' => EmployeeTrainingRecord::query()
            ->where('status', 'completed')
            ->count(),

        'expired' => EmployeeTrainingRecord::query()
            ->where('status', 'expired')
            ->count(),

        'cancelled' => EmployeeTrainingRecord::query()
            ->where('status', 'cancelled')
            ->count(),

        'certificates' => EmployeeTrainingRecord::query()
            ->whereNotNull('certificate_path')
            ->count(),
    ];

    return view(
        'company.hr.training.history',
        compact(
            'records',
            'courses',
            'years',
            'stats'
        )
    );
}
}