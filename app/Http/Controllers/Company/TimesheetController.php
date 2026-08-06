<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeAttendance;
use App\Models\Company\EmployeeJob;
use App\Models\Company\EmployeeShift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'))
            : Carbon::now()->startOfWeek();
        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'))
            : Carbon::now()->endOfWeek();

        $employeeId = $request->input('employee_id');

        // ---- Office staff: shift-based attendance ----
        $shifts = EmployeeShift::query()
            ->with(['employee', 'attendances'])
            ->whereBetween('shift_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->orderBy('shift_date')
            ->get();

        $officeRows = $shifts->map(function (EmployeeShift $shift) {
            $clockIn = $shift->attendances->where('clock_type', 'in')->sortByDesc('server_timestamp')->first();
            $clockOut = $shift->attendances->where('clock_type', 'out')->sortByDesc('server_timestamp')->first();

            $workedMinutes = null;
            if ($clockIn && $clockOut) {
                $workedMinutes = max(0, $clockIn->server_timestamp->diffInMinutes($clockOut->server_timestamp) - $shift->break_minutes);
            }

            $missingClockOut = $clockIn && !$clockOut && Carbon::parse($shift->shift_date->toDateString().' '.$shift->end_time)->isPast();
            $isManual = ($clockIn && $clockIn->is_manual_entry) || ($clockOut && $clockOut->is_manual_entry);

            return [
                'employee' => $shift->employee,
                'shift_date' => $shift->shift_date,
                'clock_in' => $clockIn?->server_timestamp,
                'clock_out' => $clockOut?->server_timestamp,
                'break_minutes' => $shift->break_minutes,
                'worked_minutes' => $workedMinutes,
                'location' => $shift->work_location,
                'status' => $shift->status,
                'missing_clock_out' => $missingClockOut,
                'is_manual' => $isManual,
            ];
        });

        // ---- Field engineers: job-based clocking ----
        $jobs = EmployeeJob::query()
            ->with('employee')
            ->whereNotNull('started_at')
            ->whereBetween('scheduled_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->orderBy('started_at')
            ->get();

        $fieldRows = $jobs->map(function (EmployeeJob $job) {
            return [
                'employee' => $job->employee,
                'job' => $job,
                'estimated_minutes' => $job->expected_duration_minutes,
                'actual_minutes' => $job->actualDurationMinutes(),
                'difference_minutes' => $job->durationDifferenceMinutes(),
                'missing_finish' => $job->started_at && !$job->finished_at,
            ];
        });

        // ---- Totals & exceptions ----
        $officeTotalMinutes = $officeRows->sum('worked_minutes');
        $fieldTotalMinutes = $fieldRows->sum('actual_minutes');
        $missingClockOuts = $officeRows->where('missing_clock_out', true)->count() + $fieldRows->where('missing_finish', true)->count();
        $manualAdjustments = EmployeeAttendance::query()
            ->whereBetween('server_timestamp', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->where('is_manual_entry', true)
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->with(['employee', 'recordedBy'])
            ->orderByDesc('server_timestamp')
            ->get();

        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.timesheet.index', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'employees' => $employees,
            'officeRows' => $officeRows->sortBy('shift_date')->values(),
            'fieldRows' => $fieldRows->sortBy(fn ($row) => $row['job']->started_at)->values(),
            'officeTotalMinutes' => $officeTotalMinutes,
            'fieldTotalMinutes' => $fieldTotalMinutes,
            'missingClockOuts' => $missingClockOuts,
            'manualAdjustments' => $manualAdjustments,
        ]);
    }
}
