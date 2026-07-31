<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeShift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->filled('week')
            ? Carbon::parse($request->input('week'))->startOfWeek()
            : Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $shifts = EmployeeShift::query()
            ->with('employee')
            ->whereBetween('shift_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->input('employee_id'));
            })
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($shift) {
                return $shift->shift_date->toDateString();
            });

        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.shifts.index', [
            'shifts' => $shifts,
            'employees' => $employees,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
        ]);
    }

    public function create()
    {
        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.shifts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateShift($request);
        $validated['created_by'] = auth('company')->id();
        $validated['updated_by'] = auth('company')->id();

        EmployeeShift::create($validated);

        return redirect()
            ->route('company.hr.shifts.index')
            ->with('success', 'Shift scheduled successfully.');
    }

    public function edit(EmployeeShift $shift)
    {
        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.shifts.edit', compact('shift', 'employees'));
    }

    public function update(Request $request, EmployeeShift $shift)
    {
        $validated = $this->validateShift($request);
        $validated['updated_by'] = auth('company')->id();

        $shift->update($validated);

        return redirect()
            ->route('company.hr.shifts.index')
            ->with('success', 'Shift updated successfully.');
    }

    public function destroy(EmployeeShift $shift)
    {
        $shift->delete();

        return redirect()
            ->route('company.hr.shifts.index')
            ->with('success', 'Shift deleted successfully.');
    }

    private function validateShift(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'shift_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'break_minutes' => ['nullable', 'integer', 'min:0'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(EmployeeShift::STATUSES)],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
