<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeAbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeAbsence::query()
            ->with('employee');

        if ($request->filled('employee_id')) {
            $query->where(
                'employee_id',
                $request->employee_id
            );
        }

        if ($request->filled('absence_type')) {
            $query->where(
                'absence_type',
                $request->absence_type
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $absences = $query
            ->latest('start_date')
            ->paginate(20)
            ->withQueryString();

        $employees = Employee::query()
            ->whereNotIn(
                'employment_status',
                [
                    'left_company',
                    'archived',
                ]
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'company.hr.absences.index',
            compact(
                'absences',
                'employees'
            )
        );
    }

    public function create()
    {
        $employees = Employee::query()
            ->whereNotIn(
                'employment_status',
                [
                    'left_company',
                    'archived',
                ]
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'company.hr.absences.create',
            compact('employees')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'absence_type' => [
                'required',
                Rule::in([
                    'sickness',
                    'unpaid_leave',
                    'compassionate_leave',
                    'authorised_absence',
                    'unauthorised_absence',
                    'other',
                ]),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'days_absent' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'evidence' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        if ($request->hasFile('evidence')) {
            $validated['evidence_path'] =
                $request
                    ->file('evidence')
                    ->store(
                        'company-private/employee-absences',
                        'local'
                    );
        }

        unset($validated['evidence']);

        $validated['status'] = 'open';
        $validated['created_by'] =
            auth('company')->id();
        $validated['updated_by'] =
            auth('company')->id();

        EmployeeAbsence::create($validated);

        return redirect()
            ->route('company.hr.absences.index')
            ->with(
                'success',
                'Employee absence recorded successfully.'
            );
    }

    public function completeReturnToWork(
    Request $request,
    EmployeeAbsence $absence
) {
    $validated = $request->validate([
        'return_to_work_date' => [
            'required',
            'date',
        ],

        'return_to_work_notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    if (
        $absence->start_date &&
        $validated['return_to_work_date']
            < $absence->start_date->toDateString()
    ) {
        return back()
            ->withErrors([
                'return_to_work_date' =>
                    'Return to work date cannot be before the absence start date.',
            ])
            ->withInput();
    }

    $absence->update([
        'return_to_work_date' =>
            $validated['return_to_work_date'],

        'return_to_work_completed' =>
            true,

        'return_to_work_notes' =>
            $validated['return_to_work_notes']
                ?? null,

        'status' => 'closed',

        'updated_by' =>
            auth('company')->id(),
    ]);

    return back()->with(
        'success',
        'Return-to-work review completed.'
    );
}
}