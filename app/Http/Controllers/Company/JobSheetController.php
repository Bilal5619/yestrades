<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class JobSheetController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->filled('week')
            ? Carbon::parse($request->input('week'))->startOfWeek()
            : Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $jobs = EmployeeJob::query()
            ->with('employee')
            ->whereBetween('scheduled_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->input('employee_id'));
            })
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_start_time')
            ->get()
            ->groupBy(function ($job) {
                return $job->scheduled_date->toDateString();
            });

        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.jobs.index', [
            'jobs' => $jobs,
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

        return view('company.hr.jobs.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateJob($request);
        $validated['created_by'] = auth('company')->id();
        $validated['updated_by'] = auth('company')->id();

        EmployeeJob::create($validated);

        return redirect()
            ->route('company.hr.jobs.index')
            ->with('success', 'Job created and assigned successfully.');
    }

    public function show(EmployeeJob $job)
    {
        $job->load(['employee', 'createdBy']);

        return view('company.hr.jobs.show', compact('job'));
    }

    public function edit(EmployeeJob $job)
    {
        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.jobs.edit', compact('job', 'employees'));
    }

    public function update(Request $request, EmployeeJob $job)
    {
        $validated = $this->validateJob($request);
        $validated['updated_by'] = auth('company')->id();

        $job->update($validated);

        return redirect()
            ->route('company.hr.jobs.index')
            ->with('success', 'Job updated successfully.');
    }

    public function destroy(EmployeeJob $job)
    {
        $job->delete();

        return redirect()
            ->route('company.hr.jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['nullable', 'exists:employees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:255'],
            'site_postcode' => ['nullable', 'string', 'max:20'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_start_time' => ['nullable', 'date_format:H:i'],
            'expected_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'priority' => ['required', Rule::in(EmployeeJob::PRIORITIES)],
            'status' => ['required', Rule::in(EmployeeJob::STATUSES)],
            'internal_notes' => ['nullable', 'string'],
        ]);
    }
}
