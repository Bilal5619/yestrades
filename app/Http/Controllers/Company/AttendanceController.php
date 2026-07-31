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
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'))
            : Carbon::now()->startOfWeek();
        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'))
            : Carbon::now()->endOfWeek();

        $records = EmployeeAttendance::query()
            ->with(['employee', 'shift', 'recordedBy'])
            ->whereBetween('server_timestamp', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->input('employee_id'));
            })
            ->orderByDesc('server_timestamp')
            ->paginate(30)
            ->withQueryString();

        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.attendance.index', [
            'records' => $records,
            'employees' => $employees,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create()
    {
        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'clock_type' => ['required', 'in:in,out'],
            'server_timestamp' => ['required', 'date'],
            'override_reason' => ['required', 'string', 'max:2000'],
        ]);

        EmployeeAttendance::create([
            'employee_id' => $validated['employee_id'],
            'clock_type' => $validated['clock_type'],
            'server_timestamp' => $validated['server_timestamp'],
            'is_manual_entry' => true,
            'override_reason' => $validated['override_reason'],
            'recorded_by' => auth('company')->id(),
        ]);

        return redirect()
            ->route('company.hr.attendance.index')
            ->with('success', 'Manual attendance record added.');
    }
}
