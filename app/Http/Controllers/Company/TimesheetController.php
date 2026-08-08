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
    // Grace period before a late clock-in/job-start counts as "late".
    private const LATE_GRACE_MINUTES = 10;

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
            $overtimeMinutes = null;
            if ($clockIn && $clockOut) {
                $workedMinutes = max(0, $clockIn->server_timestamp->diffInMinutes($clockOut->server_timestamp) - $shift->break_minutes);
                $scheduledMinutes = max(0, Carbon::parse($shift->start_time)->diffInMinutes(Carbon::parse($shift->end_time)) - $shift->break_minutes);
                $overtimeMinutes = max(0, $workedMinutes - $scheduledMinutes);
            }

            $isLate = $clockIn && $clockIn->server_timestamp->gt(
                Carbon::parse($shift->shift_date->toDateString().' '.$shift->start_time)->addMinutes(self::LATE_GRACE_MINUTES)
            );

            $missingClockOut = $clockIn && !$clockOut && Carbon::parse($shift->shift_date->toDateString().' '.$shift->end_time)->isPast();
            $isManual = ($clockIn && $clockIn->is_manual_entry) || ($clockOut && $clockOut->is_manual_entry);

            return [
                'employee' => $shift->employee,
                'shift_date' => $shift->shift_date,
                'clock_in' => $clockIn?->server_timestamp,
                'clock_out' => $clockOut?->server_timestamp,
                'break_minutes' => $shift->break_minutes,
                'worked_minutes' => $workedMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'is_late' => $isLate,
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
            $isLate = $job->started_at && $job->scheduled_start_time && $job->started_at->gt(
                Carbon::parse($job->scheduled_date->toDateString().' '.$job->scheduled_start_time)->addMinutes(self::LATE_GRACE_MINUTES)
            );

            return [
                'employee' => $job->employee,
                'job' => $job,
                'job_number' => 'JOB-'.str_pad((string) $job->id, 5, '0', STR_PAD_LEFT),
                'estimated_minutes' => $job->expected_duration_minutes,
                'actual_minutes' => $job->actualDurationMinutes(),
                'difference_minutes' => $job->durationDifferenceMinutes(),
                'is_late' => $isLate,
                'missing_finish' => $job->started_at && !$job->finished_at,
            ];
        });

        // ---- Totals & exceptions for the selected filter range ----
        $officeTotalMinutes = $officeRows->sum('worked_minutes');
        $fieldTotalMinutes = $fieldRows->sum('actual_minutes');
        $missingClockOuts = $officeRows->where('missing_clock_out', true)->count() + $fieldRows->where('missing_finish', true)->count();
        $lateStarts = $officeRows->where('is_late', true)->count() + $fieldRows->where('is_late', true)->count();
        $overtimeMinutesTotal = $officeRows->sum('overtime_minutes') + $fieldRows->filter(fn ($row) => ($row['difference_minutes'] ?? 0) > 0)->sum('difference_minutes');
        $manualAdjustments = EmployeeAttendance::query()
            ->whereBetween('server_timestamp', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->where('is_manual_entry', true)
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->with(['employee', 'recordedBy'])
            ->orderByDesc('server_timestamp')
            ->get();

        // ---- Daily / weekly / monthly totals, independent of the filter above ----
        $today = Carbon::now();
        $dailyMinutes = $this->totalMinutesForRange($today->copy()->startOfDay(), $today->copy()->endOfDay(), $employeeId);
        $weeklyMinutes = $this->totalMinutesForRange($today->copy()->startOfWeek(), $today->copy()->endOfWeek(), $employeeId);
        $monthlyMinutes = $this->totalMinutesForRange($today->copy()->startOfMonth(), $today->copy()->endOfMonth(), $employeeId);

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
            'lateStarts' => $lateStarts,
            'overtimeMinutesTotal' => $overtimeMinutesTotal,
            'manualAdjustments' => $manualAdjustments,
            'dailyMinutes' => $dailyMinutes,
            'weeklyMinutes' => $weeklyMinutes,
            'monthlyMinutes' => $monthlyMinutes,
        ]);
    }

    // Combined office (shift) + field (job) worked minutes for a fixed
    // date/time window, independent of whatever range is currently filtered
    // in the main tables above.
    private function totalMinutesForRange(Carbon $start, Carbon $end, $employeeId): int
    {
        $officeMinutes = EmployeeShift::query()
            ->with('attendances')
            ->whereBetween('shift_date', [$start->toDateString(), $end->toDateString()])
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->get()
            ->sum(function (EmployeeShift $shift) {
                $clockIn = $shift->attendances->where('clock_type', 'in')->sortByDesc('server_timestamp')->first();
                $clockOut = $shift->attendances->where('clock_type', 'out')->sortByDesc('server_timestamp')->first();
                if (!$clockIn || !$clockOut) {
                    return 0;
                }
                return max(0, $clockIn->server_timestamp->diffInMinutes($clockOut->server_timestamp) - $shift->break_minutes);
            });

        $fieldMinutes = EmployeeJob::query()
            ->whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->whereBetween('scheduled_date', [$start->toDateString(), $end->toDateString()])
            ->when($employeeId, fn ($query) => $query->where('employee_id', $employeeId))
            ->get()
            ->sum(fn (EmployeeJob $job) => $job->actualDurationMinutes() ?? 0);

        return (int) ($officeMinutes + $fieldMinutes);
    }
}
