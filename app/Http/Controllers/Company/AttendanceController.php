<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Date range
        |--------------------------------------------------------------------------
        |
        | The company operates in the UK.
        | Filters are interpreted as Europe/London dates.
        |
        */

        $timezone = 'Europe/London';

        $period = $request->input('period', 'week');

        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse(
                $request->input('date_from'),
                $timezone
            )->startOfDay();

            $dateTo = Carbon::parse(
                $request->input(
                    'date_to',
                    $request->input('date_from')
                ),
                $timezone
            )->endOfDay();
        } else {
            switch ($period) {
                case 'today':
                    $dateFrom = Carbon::now($timezone)
                        ->startOfDay();

                    $dateTo = Carbon::now($timezone)
                        ->endOfDay();

                    break;

                case 'month':
                    $dateFrom = Carbon::now($timezone)
                        ->startOfMonth();

                    $dateTo = Carbon::now($timezone)
                        ->endOfMonth();

                    break;

                case 'week':
                default:
                    $dateFrom = Carbon::now($timezone)
                        ->startOfWeek();

                    $dateTo = Carbon::now($timezone)
                        ->endOfWeek();

                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Query timestamps in UTC
        |--------------------------------------------------------------------------
        |
        | server_timestamp is created by the server, so we query using UTC
        | boundaries converted from the selected UK date range.
        |
        */

        $queryFrom = $dateFrom
            ->copy()
            ->utc();

        $queryTo = $dateTo
            ->copy()
            ->utc();

        $records = EmployeeAttendance::query()
            ->with([
                'employee',
                'shift',
                'job',
                'recordedBy',
            ])
            ->whereBetween(
                'server_timestamp',
                [
                    $queryFrom,
                    $queryTo,
                ]
            )
            ->when(
                $request->filled('employee_id'),
                function ($query) use ($request) {
                    $query->where(
                        'employee_id',
                        $request->input(
                            'employee_id'
                        )
                    );
                }
            )
            ->orderBy('server_timestamp')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | One summary row per employee per UK day
        |--------------------------------------------------------------------------
        */

        $groupedRecords = $records->groupBy(
            function ($record) use ($timezone) {
                $localDate = $record
                    ->server_timestamp
                    ->copy()
                    ->timezone($timezone)
                    ->toDateString();

                return $record->employee_id
                    . '|'
                    . $localDate;
            }
        );

        $summaries = $groupedRecords
            ->map(
                function ($dayRecords) use ($timezone) {

                    $dayRecords = $dayRecords
                        ->sortBy('server_timestamp')
                        ->values();

                    $employee =
                        $dayRecords
                            ->first()
                            ->employee;

                    $localDate =
                        $dayRecords
                            ->first()
                            ->server_timestamp
                            ->copy()
                            ->timezone($timezone)
                            ->toDateString();

                    $firstIn = null;
                    $lastOut = null;

                    $activeClockIn = null;

                    $workedMinutes = 0;

                    $hasMissingOut = false;
                    $hasUnmatchedOut = false;
                    $hasOutsideGeofence = false;
                    $hasManualRecord = false;

                    $hasJob = false;
                    $hasShift = false;

                    foreach ($dayRecords as $record) {

                        $localTime = $record
                            ->server_timestamp
                            ->copy()
                            ->timezone($timezone);

                        if ($record->job_id) {
                            $hasJob = true;
                        }

                        if ($record->shift_id) {
                            $hasShift = true;
                        }

                        if ($record->is_manual_entry) {
                            $hasManualRecord = true;
                        }

                        if (
                            $record->within_geofence
                            === false
                        ) {
                            $hasOutsideGeofence = true;
                        }

                        if (
                            $record->clock_type
                            === 'in'
                        ) {
                            if (!$firstIn) {
                                $firstIn =
                                    $localTime->copy();
                            }

                            /*
                             * If another IN occurs before
                             * an OUT, treat the previous IN
                             * as incomplete.
                             */
                            if ($activeClockIn) {
                                $hasMissingOut = true;
                            }

                            $activeClockIn =
                                $localTime->copy();
                        }

                        if (
                            $record->clock_type
                            === 'out'
                        ) {
                            $lastOut =
                                $localTime->copy();

                            if ($activeClockIn) {

                                $workedMinutes +=
                                    $activeClockIn
                                        ->diffInMinutes(
                                            $localTime
                                        );

                                $activeClockIn = null;

                            } else {
                                $hasUnmatchedOut = true;
                            }
                        }
                    }

                    if ($activeClockIn) {
                        $hasMissingOut = true;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Source
                    |--------------------------------------------------------------------------
                    */

                    $sources = [];

                    if ($hasJob) {
                        $sources[] =
                            'Field Job';
                    }

                    if ($hasShift) {
                        $sources[] =
                            'Shift';
                    }

                    if ($hasManualRecord) {
                        $sources[] =
                            'Manual';
                    }

                    if (empty($sources)) {
                        $sources[] = 'App';
                    }

                    $source =
                        implode(
                            ' + ',
                            array_unique(
                                $sources
                            )
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | Issue / status
                    |--------------------------------------------------------------------------
                    */

                    $issues = [];

                    if ($hasMissingOut) {
                        $issues[] =
                            'Missing clock out';
                    }

                    if ($hasUnmatchedOut) {
                        $issues[] =
                            'Clock out without clock in';
                    }

                    if ($hasOutsideGeofence) {
                        $issues[] =
                            'Outside geofence';
                    }

                    $status =
                        empty($issues)
                            ? 'complete'
                            : 'issue';

                    return [
                        'employee' =>
                            $employee,

                        'employee_id' =>
                            $dayRecords
                                ->first()
                                ->employee_id,

                        'date' =>
                            $localDate,

                        'first_in' =>
                            $firstIn,

                        'last_out' =>
                            $lastOut,

                        'worked_minutes' =>
                            $workedMinutes,

                        'source' =>
                            $source,

                        'status' =>
                            $status,

                        'issues' =>
                            $issues,

                        'record_count' =>
                            $dayRecords->count(),
                    ];
                }
            )
            ->sortByDesc('date')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Summary cards
        |--------------------------------------------------------------------------
        */

        $summaryStats = [
            'employee_days' =>
                $summaries->count(),

            'complete' =>
                $summaries
                    ->where(
                        'status',
                        'complete'
                    )
                    ->count(),

            'issues' =>
                $summaries
                    ->where(
                        'status',
                        'issue'
                    )
                    ->count(),

            'total_minutes' =>
                $summaries
                    ->sum(
                        'worked_minutes'
                    ),
        ];

        $employees = Employee::query()
            ->where(
                'employment_status',
                'active'
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'id',
                'first_name',
                'last_name',
            ]);

        return view(
            'company.hr.attendance.index',
            [
                'summaries' =>
                    $summaries,

                'summaryStats' =>
                    $summaryStats,

                'employees' =>
                    $employees,

                'dateFrom' =>
                    $dateFrom,

                'dateTo' =>
                    $dateTo,

                'period' =>
                    $period,
            ]
        );
    }


    public function showDay(
        Employee $employee,
        string $date
    ) {
        $timezone = 'Europe/London';

        try {
            $localDate =
                Carbon::createFromFormat(
                    'Y-m-d',
                    $date,
                    $timezone
                );
        } catch (\Throwable $e) {
            abort(404);
        }

        $queryFrom =
            $localDate
                ->copy()
                ->startOfDay()
                ->utc();

        $queryTo =
            $localDate
                ->copy()
                ->endOfDay()
                ->utc();

        $records =
            EmployeeAttendance::query()
                ->with([
                    'employee',
                    'shift',
                    'job',
                    'recordedBy',
                ])
                ->where(
                    'employee_id',
                    $employee->id
                )
                ->whereBetween(
                    'server_timestamp',
                    [
                        $queryFrom,
                        $queryTo,
                    ]
                )
                ->orderBy(
                    'server_timestamp'
                )
                ->get();

        if ($records->isEmpty()) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate worked time
        |--------------------------------------------------------------------------
        */

        $workedMinutes = 0;
        $activeClockIn = null;

        $firstIn = null;
        $lastOut = null;

        $issues = [];

        foreach ($records as $record) {

            $localTime =
                $record
                    ->server_timestamp
                    ->copy()
                    ->timezone(
                        $timezone
                    );

            if (
                $record->clock_type
                === 'in'
            ) {
                if (!$firstIn) {
                    $firstIn =
                        $localTime->copy();
                }

                if ($activeClockIn) {
                    $issues[] =
                        'Clock in recorded before previous clock out.';
                }

                $activeClockIn =
                    $localTime->copy();
            }

            if (
                $record->clock_type
                === 'out'
            ) {
                $lastOut =
                    $localTime->copy();

                if ($activeClockIn) {

                    $workedMinutes +=
                        $activeClockIn
                            ->diffInMinutes(
                                $localTime
                            );

                    $activeClockIn = null;

                } else {
                    $issues[] =
                        'Clock out recorded without a matching clock in.';
                }
            }

            if (
                $record->within_geofence
                === false
            ) {
                $issues[] =
                    'One or more records were outside the geofence.';
            }
        }

        if ($activeClockIn) {
            $issues[] =
                'Missing clock out.';
        }

        $issues =
            array_values(
                array_unique(
                    $issues
                )
            );

        return view(
            'company.hr.attendance.show',
            [
                'employee' =>
                    $employee,

                'date' =>
                    $localDate,

                'records' =>
                    $records,

                'firstIn' =>
                    $firstIn,

                'lastOut' =>
                    $lastOut,

                'workedMinutes' =>
                    $workedMinutes,

                'issues' =>
                    $issues,

                'timezone' =>
                    $timezone,
            ]
        );
    }


    public function create()
    {
        $employees = Employee::query()
            ->where(
                'employment_status',
                'active'
            )
            ->orderBy('first_name')
            ->get([
                'id',
                'first_name',
                'last_name',
            ]);

        return view(
            'company.hr.attendance.create',
            compact('employees')
        );
    }


    public function store(
        Request $request
    ) {
        $validated =
            $request->validate([
                'employee_id' => [
                    'required',
                    'exists:employees,id',
                ],

                'clock_type' => [
                    'required',
                    'in:in,out',
                ],

                'server_timestamp' => [
                    'required',
                    'date',
                ],

                'override_reason' => [
                    'required',
                    'string',
                    'max:2000',
                ],
            ]);

        EmployeeAttendance::create([
            'employee_id' =>
                $validated[
                    'employee_id'
                ],

            'clock_type' =>
                $validated[
                    'clock_type'
                ],

            'server_timestamp' =>
                $validated[
                    'server_timestamp'
                ],

            'is_manual_entry' =>
                true,

            'override_reason' =>
                $validated[
                    'override_reason'
                ],

            'recorded_by' =>
                auth('company')->id(),
        ]);

        return redirect()
            ->route(
                'company.hr.attendance.index'
            )
            ->with(
                'success',
                'Manual attendance record added.'
            );
    }
}