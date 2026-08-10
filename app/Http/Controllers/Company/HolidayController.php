<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeHoliday;
use App\Models\Company\EmployeeHolidayAllowance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index()
    {
        $today = today();

        $stats = [
            'currently_on_holiday' => EmployeeHoliday::query()
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereHas('employee', function ($query) {
                    $query->whereNotIn(
                        'employment_status',
                        [
                            'archived',
                            'left_company',
                        ]
                    );
                })
                ->count(),

            'pending_requests' => EmployeeHoliday::query()
                ->where('status', 'pending')
                ->count(),

            'upcoming_approved' => EmployeeHoliday::query()
                ->where('status', 'approved')
                ->whereDate('start_date', '>', $today)
                ->count(),

            'approved_this_year' => EmployeeHoliday::query()
                ->where('status', 'approved')
                ->whereYear('start_date', now()->year)
                ->count(),

            'employees_without_allowance' => Employee::query()
                ->whereNotIn(
                    'employment_status',
                    [
                        'archived',
                        'left_company',
                    ]
                )
                ->whereDoesntHave(
                    'holidayAllowances',
                    function ($query) {
                        $query->where(
                            'holiday_year',
                            now()->year
                        );
                    }
                )
                ->count(),
        ];

        $currentlyOnHoliday = EmployeeHoliday::query()
            ->with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereHas('employee', function ($query) {
                $query->whereNotIn(
                    'employment_status',
                    [
                        'archived',
                        'left_company',
                    ]
                );
            })
            ->orderBy('end_date')
            ->take(10)
            ->get();

        $pendingRequests = EmployeeHoliday::query()
            ->with('employee')
            ->where('status', 'pending')
            ->latest('requested_at')
            ->take(10)
            ->get();

        $upcomingHolidays = EmployeeHoliday::query()
            ->with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '>', $today)
            ->orderBy('start_date')
            ->take(10)
            ->get();

        $lowAllowanceEmployees = EmployeeHolidayAllowance::query()
            ->with('employee')
            ->where('holiday_year', now()->year)
            ->get()
            ->filter(function ($allowance) {
                return $allowance->remaining_allowance <= 5;
            })
            ->sortBy('remaining_allowance')
            ->take(10);

        return view(
            'company.hr.holidays.index',
            compact(
                'stats',
                'currentlyOnHoliday',
                'pendingRequests',
                'upcomingHolidays',
                'lowAllowanceEmployees'
            )
        );
    }


    public function allowances(Request $request)
{
    $year = (int) $request->get(
        'year',
        now()->year
    );

    $employees = Employee::query()
        ->whereNotIn('employment_status', [
            'archived',
            'left_company',
        ])
        ->with([
            'holidayAllowances' => function ($query) use ($year) {
                $query->where('holiday_year', $year);
            },
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

    $years = collect(range(
        now()->year - 2,
        now()->year + 2
    ));

    return view(
        'company.hr.holidays.allowances',
        compact(
            'employees',
            'year',
            'years'
        )
    );
}

public function storeAllowance(Request $request)
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

        'holiday_year' => [
            'required',
            'integer',
            'min:2020',
            'max:2100',
        ],

        'annual_entitlement' => [
            'required',
            'numeric',
            'min:0',
            'max:366',
        ],

        'carried_over' => [
            'nullable',
            'numeric',
            'min:0',
            'max:366',
        ],

        'adjustment' => [
            'nullable',
            'numeric',
            'min:-366',
            'max:366',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:2000',
        ],
    ]);


    $allowance = EmployeeHolidayAllowance::firstOrNew([
    'employee_id' => $validated['employee_id'],
    'holiday_year' => $validated['holiday_year'],
        ]);

        if (! $allowance->exists) {
            $allowance->created_by = auth('company')->id();
        }

        $allowance->annual_entitlement =
            $validated['annual_entitlement'];

        $allowance->carried_over =
            $validated['carried_over'] ?? 0;

        $allowance->adjustment =
            $validated['adjustment'] ?? 0;

        $allowance->notes =
            $validated['notes'] ?? null;

        $allowance->updated_by =
            auth('company')->id();

        $allowance->save();

    EmployeeHolidayAllowance::updateOrCreate(
        [
            'employee_id' => $validated['employee_id'],
            'holiday_year' => $validated['holiday_year'],
        ],
        [
            'annual_entitlement' =>
                $validated['annual_entitlement'],

            'carried_over' =>
                $validated['carried_over'] ?? 0,

            'adjustment' =>
                $validated['adjustment'] ?? 0,

            'notes' =>
                $validated['notes'] ?? null,

            'updated_by' =>
                auth('company')->id(),

            'created_by' =>
                auth('company')->id(),
        ]
    );

    return redirect()
        ->route(
            'company.hr.holidays.allowances',
            [
                'year' =>
                    $validated['holiday_year'],
            ]
        )
        ->with(
            'success',
            'Holiday allowance saved successfully.'
        );
}
public function create(Request $request)
{
    $employees = Employee::query()
        ->whereNotIn('employment_status', [
            'archived',
            'left_company',
        ])
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();
     $selectedEmployeeId = $request->integer('employee_id');   

    return view(
        'company.hr.holidays.create',
        compact(
            'employees',
            'selectedEmployeeId'
        )
    );
}

public function store(Request $request)
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

        'holiday_type' => [
            'required',
            Rule::in([
                'annual_leave',
                'sick_leave',
                'unpaid_leave',
                'maternity_leave',
                'paternity_leave',
                'compassionate_leave',
                'other',
            ]),
        ],

        'start_date' => [
            'required',
            'date',
        ],

        'end_date' => [
            'required',
            'date',
            'after_or_equal:start_date',
        ],

        'start_period' => [
            'required',
            Rule::in([
                'full_day',
                'morning',
                'afternoon',
            ]),
        ],

        'end_period' => [
            'required',
            Rule::in([
                'full_day',
                'morning',
                'afternoon',
            ]),
        ],

        'reason' => [
            'nullable',
            'string',
            'max:2000',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:2000',
        ],

        'status' => [
            'required',
            Rule::in([
                'pending',
                'approved',
            ]),
        ],
    ]);

    if (
        $validated['status'] === 'approved' &&
        ! auth('company')->user()
            ->hasPermission('holidays.approve')
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'status' =>
                    'You do not have permission to approve holidays.',
            ]);
    }

    $startDate = Carbon::parse(
        $validated['start_date']
    )->startOfDay();

    $endDate = Carbon::parse(
        $validated['end_date']
    )->startOfDay();

    $totalDays = $this->calculateWorkingHolidayDays(
        $startDate,
        $endDate,
        $validated['start_period'],
        $validated['end_period']
    );

    if ($totalDays <= 0) {
        return back()
            ->withInput()
            ->withErrors([
                'start_date' =>
                    'The selected dates do not contain any working days.',
            ]);
    }

    $overlappingHoliday = EmployeeHoliday::query()
        ->where(
            'employee_id',
            $validated['employee_id']
        )
        ->whereIn('status', [
            'pending',
            'approved',
        ])
        ->where(function ($query) use (
            $startDate,
            $endDate
        ) {
            $query
                ->whereDate(
                    'start_date',
                    '<=',
                    $endDate
                )
                ->whereDate(
                    'end_date',
                    '>=',
                    $startDate
                );
        })
        ->exists();

    if ($overlappingHoliday) {
        return back()
            ->withInput()
            ->withErrors([
                'start_date' =>
                    'This employee already has a pending or approved holiday during these dates.',
            ]);
    }

    $companyUserId = auth('company')->id();

    $holiday = new EmployeeHoliday();

    $holiday->employee_id =
        $validated['employee_id'];

    $holiday->holiday_type =
        $validated['holiday_type'];

    $holiday->start_date =
        $validated['start_date'];

    $holiday->end_date =
        $validated['end_date'];

    $holiday->start_period =
        $validated['start_period'];

    $holiday->end_period =
        $validated['end_period'];

    $holiday->total_days =
        $totalDays;

    $holiday->reason =
        $validated['reason'] ?? null;

    $holiday->notes =
        $validated['notes'] ?? null;

    $holiday->status =
        $validated['status'];

    $holiday->requested_by =
        $companyUserId;

    $holiday->requested_at =
        now();

    $holiday->created_by =
        $companyUserId;

    $holiday->updated_by =
        $companyUserId;

    if ($validated['status'] === 'approved') {
        $holiday->approved_by =
            $companyUserId;

        $holiday->approved_at =
            now();
    }

    $holiday->save();

    return redirect()
        ->route('company.hr.holidays.index')
        ->with(
            'success',
            'Holiday added successfully.'
        );
}

private function calculateWorkingHolidayDays(
    Carbon $startDate,
    Carbon $endDate,
    string $startPeriod,
    string $endPeriod
): float {
    $workingDays = 0;

    $date = $startDate->copy();

    while ($date->lte($endDate)) {
        if (! $date->isWeekend()) {
            $workingDays++;
        }

        $date->addDay();
    }

    if ($workingDays === 0) {
        return 0;
    }

    if (
        ! $startDate->isWeekend() &&
        $startPeriod !== 'full_day'
    ) {
        $workingDays -= 0.5;
    }

    if (
        ! $endDate->isWeekend() &&
        $endPeriod !== 'full_day'
    ) {
        $workingDays -= 0.5;
    }

    if ($startDate->isSameDay($endDate)) {
        if (
            $startPeriod !== 'full_day' ||
            $endPeriod !== 'full_day'
        ) {
            return 0.5;
        }

        return 1;
    }

    return max(0, $workingDays);
}
public function requests(Request $request)
{
    $query = EmployeeHoliday::query()
        ->with([
            'employee',
            'requestedBy',
            'approvedBy',
            'rejectedBy',
            'cancelledBy',
        ]);

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('holiday_type')) {
        $query->where(
            'holiday_type',
            $request->holiday_type
        );
    }

    if ($request->filled('year')) {
        $query->whereYear(
            'start_date',
            $request->year
        );
    }

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->whereHas(
            'employee',
            function ($employeeQuery) use ($search) {
                $employeeQuery->where(
                    function ($nameQuery) use ($search) {
                        $nameQuery
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
                );
            }
        );
    }

    $holidays = $query
        ->orderByRaw("
            CASE
                WHEN status = 'pending' THEN 1
                WHEN status = 'approved' THEN 2
                WHEN status = 'rejected' THEN 3
                WHEN status = 'cancelled' THEN 4
                ELSE 5
            END
        ")
        ->latest('requested_at')
        ->paginate(15)
        ->withQueryString();

    $years = EmployeeHoliday::query()
        ->selectRaw('YEAR(start_date) as holiday_year')
        ->distinct()
        ->orderByDesc('holiday_year')
        ->pluck('holiday_year');

    return view(
        'company.hr.holidays.requests',
        compact(
            'holidays',
            'years'
        )
    );
}

public function exportRequests(Request $request)
{
    $query = EmployeeHoliday::query()->with('employee');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('holiday_type')) {
        $query->where('holiday_type', $request->holiday_type);
    }

    if ($request->filled('year')) {
        $query->whereYear('start_date', $request->year);
    }

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->whereHas('employee', function ($employeeQuery) use ($search) {
            $employeeQuery->where(function ($nameQuery) use ($search) {
                $nameQuery
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        });
    }

    $holidays = $query->latest('requested_at')->get();

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\HolidayReportExport($holidays),
        'holiday-report-' . now()->format('Y-m-d') . '.xlsx'
    );
}

public function show(EmployeeHoliday $holiday)
{
    $holiday->load([
        'employee',
        'requestedBy',
        'approvedBy',
        'rejectedBy',
        'cancelledBy',
        'createdBy',
        'updatedBy',
    ]);

    $allowance = null;

    if ($holiday->holiday_type === 'annual_leave') {
        $allowance = EmployeeHolidayAllowance::query()
            ->where(
                'employee_id',
                $holiday->employee_id
            )
            ->where(
                'holiday_year',
                $holiday->start_date->year
            )
            ->first();
    }

    return view(
        'company.hr.holidays.show',
        compact(
            'holiday',
            'allowance'
        )
    );
}

public function approve(EmployeeHoliday $holiday)
{
    if ($holiday->status !== 'pending') {
        return back()->withErrors([
            'holiday' =>
                'Only pending holiday requests can be approved.',
        ]);
    }

    if ($holiday->holiday_type === 'annual_leave') {
        $allowance = EmployeeHolidayAllowance::query()
            ->where(
                'employee_id',
                $holiday->employee_id
            )
            ->where(
                'holiday_year',
                $holiday->start_date->year
            )
            ->first();

        if (! $allowance) {
            return back()->withErrors([
                'holiday' =>
                    'This employee does not have a holiday allowance for the selected year.',
            ]);
        }

        if (
            $allowance->remaining_allowance <
            (float) $holiday->total_days
        ) {
            return back()->withErrors([
                'holiday' =>
                    'The employee does not have enough remaining holiday allowance.',
            ]);
        }
    }

    $holiday->status = 'approved';
    $holiday->approved_by = auth('company')->id();
    $holiday->approved_at = now();
    $holiday->updated_by = auth('company')->id();

    $holiday->rejected_by = null;
    $holiday->rejected_at = null;
    $holiday->rejection_reason = null;

    $holiday->save();

    return back()->with(
        'success',
        'Holiday request approved successfully.'
    );
}

public function reject(
    Request $request,
    EmployeeHoliday $holiday
) {
    if ($holiday->status !== 'pending') {
        return back()->withErrors([
            'holiday' =>
                'Only pending holiday requests can be rejected.',
        ]);
    }

    $validated = $request->validate([
        'rejection_reason' => [
            'required',
            'string',
            'max:2000',
        ],
    ]);

    $holiday->status = 'rejected';
    $holiday->rejected_by = auth('company')->id();
    $holiday->rejected_at = now();

    $holiday->rejection_reason =
        $validated['rejection_reason'];

    $holiday->updated_by = auth('company')->id();

    $holiday->approved_by = null;
    $holiday->approved_at = null;

    $holiday->save();

    return back()->with(
        'success',
        'Holiday request rejected successfully.'
    );
}

public function cancel(
    Request $request,
    EmployeeHoliday $holiday
) {
    if (
        ! in_array(
            $holiday->status,
            [
                'pending',
                'approved',
            ],
            true
        )
    ) {
        return back()->withErrors([
            'holiday' =>
                'Only pending or approved holidays can be cancelled.',
        ]);
    }

    $validated = $request->validate([
        'cancellation_reason' => [
            'required',
            'string',
            'max:2000',
        ],
    ]);

    $holiday->status = 'cancelled';
    $holiday->cancelled_by = auth('company')->id();
    $holiday->cancelled_at = now();

    $holiday->cancellation_reason =
        $validated['cancellation_reason'];

    $holiday->updated_by = auth('company')->id();

    $holiday->save();

    return back()->with(
        'success',
        'Holiday cancelled successfully.'
    );
}
}