<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyPolicy;
use App\Models\Company\CompanyPolicyAcknowledgement;
use App\Models\Company\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyPolicyController extends Controller
{
    public function index()
    {
        $policies = CompanyPolicy::withCount([
            'acknowledgements',

            'acknowledgements as pending_count' => function ($query) {
                $query->where('status', 'pending');
            },

            'acknowledgements as acknowledged_count' => function ($query) {
                $query->where('status', 'acknowledged');
            },
        ])
            ->latest()
            ->paginate(20);

        return view(
            'company.hr.policies.index',
            compact('policies')
        );
    }

    public function create()
    {
        return view(
            'company.hr.policies.create'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'version' => [
                'nullable',
                'string',
                'max:50',
            ],

            'policy_file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ]);

        $filePath = $request
            ->file('policy_file')
            ->store(
                'company-private/policies',
                'local'
            );

        $policy = CompanyPolicy::create([
            'title' => $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'version' =>
                $validated['version'] ?? null,

            'file_path' => $filePath,

            'is_active' => true,

            'created_by' =>
                auth('company')->id(),

            'updated_by' =>
                auth('company')->id(),
        ]);

        return redirect()
            ->route(
                'company.hr.policies.show',
                $policy
            )
            ->with(
                'success',
                'Company policy created successfully.'
            );
    }

    public function show(CompanyPolicy $policy)
    {
        $policy->load([
            'acknowledgements.employee',
            'createdBy',
        ]);

        $employees = Employee::query()
            ->whereIn(
                'employment_status',
                [
                    'onboarding',
                    'active',
                    'on_leave',
                    'suspended',
                    'notice_period',
                ]
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'company.hr.policies.show',
            compact(
                'policy',
                'employees'
            )
        );
    }

    public function assign(
        Request $request,
        CompanyPolicy $policy
    ) {
        $validated = $request->validate([
            'employee_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'employee_ids.*' => [
                'required',
                'integer',
                'exists:employees,id',
            ],
        ]);

        foreach (
            $validated['employee_ids']
            as $employeeId
        ) {
            CompanyPolicyAcknowledgement::firstOrCreate(
                [
                    'company_policy_id' =>
                        $policy->id,

                    'employee_id' =>
                        $employeeId,
                ],
                [
                    'status' => 'pending',
                ]
            );
        }

        return back()->with(
            'success',
            'Policy assigned successfully.'
        );
    }

    public function acknowledge(
        CompanyPolicy $policy,
        CompanyPolicyAcknowledgement $acknowledgement
    ) {
        if (
            $acknowledgement->company_policy_id
            !== $policy->id
        ) {
            abort(404);
        }

        $acknowledgement->update([
            'status' => 'acknowledged',

            'acknowledged_at' => now(),
        ]);

        return back()->with(
            'success',
            'Policy acknowledgement recorded.'
        );
    }

    public function download(
        CompanyPolicy $policy
    ) {
        if (
            !$policy->file_path ||
            !Storage::disk('local')
                ->exists($policy->file_path)
        ) {
            abort(
                404,
                'Policy document not found.'
            );
        }

        return Storage::disk('local')
            ->download(
                $policy->file_path
            );
    }
}