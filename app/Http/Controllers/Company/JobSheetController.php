<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyClient;
use App\Models\Company\CompanyClientProperty;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\Company\EmployeeJobVisit;

class JobSheetController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->filled('week')
            ? Carbon::parse($request->input('week'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        $query = EmployeeJob::query()
            ->with([
                'employee',
                'client',
                'property',
            ])
            ->whereBetween(
                'scheduled_date',
                [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ]
            );

        if ($request->filled('employee_id')) {
            $query->where(
                'employee_id',
                $request->input('employee_id')
            );
        }

        if ($request->filled('admin_status')) {
            $query->where(
                'admin_status',
                $request->input('admin_status')
            );
        }

        $jobs = $query
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_start_time')
            ->get()
            ->groupBy(function ($job) {
                return $job->scheduled_date->toDateString();
            });
            $unscheduledJobs = EmployeeJob::query()
    ->with([
        'employee',
        'client',
        'property',
    ])
    ->whereNull('scheduled_date')
    ->whereNotIn(
        'status',
        [
            'completed',
            'cancelled',
        ]
    )
    ->orderBy('created_at')
    ->get();

        $employees = $this->fieldEngineers();

        $actionRequiredCount = EmployeeJob::query()
            ->where('admin_status', 'action_required')
            ->count();

        $paymentOutstandingCount = EmployeeJob::query()
            ->where('payment_required', true)
            ->whereNotIn(
                'payment_status',
                ['paid', 'not_required']
            )
            ->count();

        $certificateOutstandingCount = EmployeeJob::query()
            ->where('certificate_required', true)
            ->where('certificate_status', '!=', 'completed')
            ->count();

        return view('company.hr.jobs.index', [
            'jobs' => $jobs,
            'employees' => $employees,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'actionRequiredCount' => $actionRequiredCount,
            'paymentOutstandingCount' => $paymentOutstandingCount,
            'certificateOutstandingCount' =>
                $certificateOutstandingCount,
            'unscheduledJobs' => $unscheduledJobs,
        ]);
    }

    public function create()
    {
        return view('company.hr.jobs.create', [
            'employees' => $this->fieldEngineers(),
            'clients' => $this->clients(),
            'properties' => $this->properties(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateJob($request);

        $this->applyClientPropertyDetails($validated);

        $validated['created_by'] =
            auth('company')->id();

        $validated['updated_by'] =
            auth('company')->id();

       $job = EmployeeJob::create($validated);

if ($job->employee) {
    app(\App\Services\EmployeePushNotificationService::class)
        ->sendJobAssigned($job->employee, $job);
}

return redirect()
    ->route('company.hr.jobs.index')
    ->with(
        'success',
        'Job created and assigned successfully.'
    );
    }

    public function show(EmployeeJob $job)
    {
       $job->load([
    'employee',
    'client',
    'property',
    'createdBy',
    'attendances',

    'visits' => function ($query) {
        $query
            ->with('employee')
            ->orderBy('visit_date')
            ->orderBy('start_time');
    },
      ]);

        return view(
            'company.hr.jobs.show',
            compact('job')
        );
    }

    public function edit(EmployeeJob $job)
    {
        return view('company.hr.jobs.edit', [
            'job' => $job,
            'employees' => $this->fieldEngineers(),
            'clients' => $this->clients(),
            'properties' => $this->properties(),
        ]);
    }

    public function update(
        Request $request,
        EmployeeJob $job
    ) {
        $validated = $this->validateJob($request);

        $this->applyClientPropertyDetails($validated);

        $validated['updated_by'] =
            auth('company')->id();

        if (
            ($validated['payment_status'] ?? null)
            === 'paid'
            && !$job->paid_at
        ) {
            $validated['paid_at'] = now();
        }

        if (
            ($validated['payment_status'] ?? null)
            === 'invoice_sent'
            && !$job->invoice_sent_at
        ) {
            $validated['invoice_sent_at'] = now();
        }

        $job->update($validated);

        return redirect()
            ->route(
                'company.hr.jobs.show',
                $job
            )
            ->with(
                'success',
                'Job updated successfully.'
            );
    }

    public function close(EmployeeJob $job)
    {
        if (!$job->canClose()) {
            return back()->with(
                'error',
                'This job cannot be closed until work, payment and certificate requirements are completed.'
            );
        }

        $job->update([
            'admin_status' => 'closed',
            'closed_at' => now(),
            'updated_by' => auth('company')->id(),
        ]);

        return back()->with(
            'success',
            'Job closed successfully.'
        );
    }

    public function destroy(EmployeeJob $job)
    {
        $job->delete();

        return redirect()
            ->route('company.hr.jobs.index')
            ->with(
                'success',
                'Job deleted successfully.'
            );
    }

    private function fieldEngineers()
    {
        return Employee::query()
            ->where('employment_status', 'active')
            ->where('is_field_engineer', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'id',
                'first_name',
                'middle_name',
                'last_name',
            ]);
    }

    private function clients()
    {
        return CompanyClient::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function properties()
    {
        return CompanyClientProperty::query()
            ->with('client')
            ->where('is_active', true)
            ->orderBy('postcode')
            ->get();
    }

    private function applyClientPropertyDetails(
        array &$validated
    ): void {
        if (
            !empty($validated['client_property_id'])
        ) {
            $property = CompanyClientProperty::find(
                $validated['client_property_id']
            );

            if ($property) {
                $validated['client_id'] =
                    $property->client_id;

                $validated['site_address'] =
                    collect([
                        $property->address_line_1,
                        $property->address_line_2,
                        $property->city,
                        $property->county,
                    ])
                    ->filter()
                    ->implode(', ');

                $validated['site_postcode'] =
                    $property->postcode;

                if (
                    empty($validated['customer_name'])
                    && $property->contact_name
                ) {
                    $validated['customer_name'] =
                        $property->contact_name;
                }

                if (
                    empty($validated['customer_phone'])
                    && $property->contact_phone
                ) {
                    $validated['customer_phone'] =
                        $property->contact_phone;
                }

                if (
                    empty($validated['customer_email'])
                    && $property->contact_email
                ) {
                    $validated['customer_email'] =
                        $property->contact_email;
                }
            }
        }
    }
    public function createVisit(
    EmployeeJob $job
) {
    return view(
        'company.hr.jobs.visits.create',
        [
            'job' => $job,

            'employees' =>
                $this->fieldEngineers(),
        ]
    );
}


public function storeVisit(
    Request $request,
    EmployeeJob $job
) {
    $validated =
        $this->validateVisit(
            $request
        );

    $validated['job_id'] =
        $job->id;

    $validated['created_by'] =
        auth('company')->id();

    $validated['updated_by'] =
        auth('company')->id();

    $visit =
        EmployeeJobVisit::create(
            $validated
        );

    /*
     * Parent job is not completed automatically.
     * Admin controls overall completion.
     */

    if (
        in_array(
            $job->status,
            [
                'assigned',
                'accepted',
            ],
            true
        )
    ) {
        $job->update([
            'status' => 'in_progress',
            'updated_by' =>
                auth('company')->id(),
        ]);
    }

    return redirect()
        ->route(
            'company.hr.jobs.show',
            $job
        )
        ->with(
            'success',
            'Visit scheduled successfully.'
        );
}


public function editVisit(
    EmployeeJob $job,
    EmployeeJobVisit $visit
) {
    abort_unless(
        $visit->job_id
        === $job->id,
        404
    );

    return view(
        'company.hr.jobs.visits.edit',
        [
            'job' => $job,

            'visit' => $visit,

            'employees' =>
                $this->fieldEngineers(),
        ]
    );
}


public function updateVisit(
    Request $request,
    EmployeeJob $job,
    EmployeeJobVisit $visit
) {
    abort_unless(
        $visit->job_id
        === $job->id,
        404
    );

    $validated =
        $this->validateVisit(
            $request
        );

    $validated['updated_by'] =
        auth('company')->id();

    $visit->update(
        $validated
    );

    /*
     * Do NOT automatically complete
     * the parent job here.
     */

    return redirect()
        ->route(
            'company.hr.jobs.show',
            $job
        )
        ->with(
            'success',
            'Visit updated successfully.'
        );
}


public function destroyVisit(
    EmployeeJob $job,
    EmployeeJobVisit $visit
) {
    abort_unless(
        $visit->job_id
        === $job->id,
        404
    );

    $visit->delete();

    return redirect()
        ->route(
            'company.hr.jobs.show',
            $job
        )
        ->with(
            'success',
            'Visit removed successfully.'
        );
}


private function validateVisit(
    Request $request
): array {
    return $request->validate([

        'employee_id' => [
            'nullable',

            Rule::exists(
                'employees',
                'id'
            )
                ->where(
                    fn ($query) =>
                    $query
                        ->where(
                            'employment_status',
                            'active'
                        )
                        ->where(
                            'is_field_engineer',
                            1
                        )
                ),
        ],

        'visit_date' => [
            'required',
            'date',
        ],

        'start_time' => [
            'nullable',
            'date_format:H:i',
        ],

        'expected_duration_minutes' => [
            'nullable',
            'integer',
            'min:1',
        ],

        'status' => [
            'required',

            Rule::in(
                EmployeeJobVisit::STATUSES
            ),
        ],

        'notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);
}
    private function validateJob(
        Request $request
    ): array {
        return $request->validate([

            'client_id' => [
                'nullable',
                'exists:company_clients,id',
            ],

            'client_property_id' => [
                'nullable',
                'exists:company_client_properties,id',
            ],

            'employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where(
                                'employment_status',
                                'active'
                            )
                            ->where(
                                'is_field_engineer',
                                1
                            )
                    ),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'customer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'customer_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'site_address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'site_postcode' => [
                'nullable',
                'string',
                'max:20',
            ],
            'appointment_status' => [
                'required',
                Rule::in(
                    EmployeeJob::APPOINTMENT_STATUSES
                ),
            ],

            'scheduled_date' => [
                'nullable',
                'date',
                'required_if:appointment_status,confirmed',
            ],

            'scheduled_start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'expected_duration_minutes' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'priority' => [
                'required',
                Rule::in(
                    EmployeeJob::PRIORITIES
                ),
            ],

            'status' => [
                'required',
                Rule::in(
                    EmployeeJob::STATUSES
                ),
            ],

            'internal_notes' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            'payment_required' => [
                'nullable',
                'boolean',
            ],

            'agreed_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'payment_method' => [
                'nullable',
                Rule::in(
                    EmployeeJob::PAYMENT_METHODS
                ),
            ],

            'payment_status' => [
                'required',
                Rule::in(
                    EmployeeJob::PAYMENT_STATUSES
                ),
            ],

            'invoice_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Certificate
            |--------------------------------------------------------------------------
            */

            'certificate_required' => [
                'nullable',
                'boolean',
            ],

            'certificate_type' => [
                'nullable',
                Rule::in(
                    EmployeeJob::CERTIFICATE_TYPES
                ),
            ],

            'certificate_status' => [
                'required',
                Rule::in(
                    EmployeeJob::CERTIFICATE_STATUSES
                ),
            ],

            'work_summary' => [
                'nullable',
                'string',
            ],

            'completion_notes' => [
                'nullable',
                'string',
            ],
        ]);
    }
}