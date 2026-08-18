<?php

namespace App\Http\Controllers\Company\HR;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\SponsoredWorkerRecord;
use Illuminate\Http\Request;
use App\Models\Company\SponsoredWorkerChange;

class SponsoredWorkerController extends Controller
{
    public function index(Request $request)
    {
       $query = SponsoredWorkerRecord::query()
    ->with([
        'employee.manager',

        'employee.documents' => function ($query) {
            $query->with('documentType')
                ->where('is_required', true);
        },

        'changes' => function ($query) {
            $query->where('review_status', 'pending');
        },
    ])
            ->whereHas('employee', function ($employeeQuery) {
                $employeeQuery->where(
                    'is_sponsored_worker',
                    true
                );
            });

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($workerQuery) use ($search) {
                $workerQuery
                    ->where('cos_number', 'like', "%{$search}%")
                    ->orWhere(
                        'occupation_code',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'employee',
                        function ($employeeQuery) use ($search) {
                            $employeeQuery
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
            });
        }
        

        if ($request->filled('status')) {
            $query->where(
                'record_status',
                $request->status
            );
        }
        if ($request->filled('visa_status')) {
    $visaStatus = $request->visa_status;

    if ($visaStatus === 'expired') {
        $query->whereNotNull('visa_expiry_date')
            ->whereDate(
                'visa_expiry_date',
                '<',
                today()
            );
    }

    if ($visaStatus === '30_days') {
        $query
            ->whereNotNull('visa_expiry_date')
            ->whereDate(
                'visa_expiry_date',
                '>=',
                today()
            )
            ->whereDate(
                'visa_expiry_date',
                '<=',
                today()->copy()->addDays(30)
            );
    }

    if ($visaStatus === '90_days') {
        $query
            ->whereNotNull('visa_expiry_date')
            ->whereDate(
                'visa_expiry_date',
                '>=',
                today()
            )
            ->whereDate(
                'visa_expiry_date',
                '<=',
                today()->copy()->addDays(90)
            );
    }

    if ($visaStatus === 'missing') {
        $query->whereNull('visa_expiry_date');
    }
}

        $sponsoredWorkers = $query
            ->orderByRaw(
                'visa_expiry_date IS NULL, visa_expiry_date ASC'
            )
            ->paginate(20)
            ->withQueryString();

$sponsoredWorkers->getCollection()->transform(
    function ($record) {

        $requiredDocuments =
            $record->employee->documents
                ->where('is_required', true);

        $record->missing_document_count =
            $requiredDocuments->filter(
                function ($document) {
                    return
                        !$document->file_path ||
                        in_array(
                            $document->review_status,
                            [
                                'missing',
                                'rejected',
                            ],
                            true
                        );
                }
            )->count();

        $record->pending_document_count =
            $requiredDocuments->where(
                'review_status',
                'pending_review'
            )->count();

        $record->pending_change_count =
            $record->changes->count();

        $record->needs_compliance_action =
            $record->visa_status !== 'valid' ||
            $record->hasComplianceDifference() ||
            $record->missing_document_count > 0 ||
            $record->pending_document_count > 0 ||
            $record->pending_change_count > 0;

        return $record;
    }
);
        $statistics = [
            'total' => SponsoredWorkerRecord::query()
                ->whereHas('employee', function ($query) {
                    $query->where(
                        'is_sponsored_worker',
                        true
                    );
                })
                ->count(),

            'expiring_90_days' => SponsoredWorkerRecord::query()
                ->whereNotNull('visa_expiry_date')
                ->whereBetween('visa_expiry_date', [
                    now()->startOfDay(),
                    now()->addDays(90)->endOfDay(),
                ])
                ->count(),

            'expired' => SponsoredWorkerRecord::query()
                ->whereDate(
                    'visa_expiry_date',
                    '<',
                    now()->startOfDay()
                )
                ->count(),

            'missing_expiry' => SponsoredWorkerRecord::query()
                ->whereNull('visa_expiry_date')
                ->count(),
                'pending_changes' =>
    SponsoredWorkerChange::query()
        ->where('review_status', 'pending')
        ->count(),

'missing_documents' =>
    \App\Models\Company\EmployeeDocument::query()
        ->where('is_required', true)
        ->whereHas(
            'employee',
            function ($query) {
                $query->where(
                    'is_sponsored_worker',
                    true
                );
            }
        )
        ->where(function ($query) {
            $query
                ->whereNull('file_path')
                ->orWhereIn(
                    'review_status',
                    [
                        'missing',
                        'rejected',
                    ]
                );
        })
        ->count(),
        ];

        return view(
            'company.hr.sponsor-compliance.sponsored-workers.index',
            compact(
                'sponsoredWorkers',
                'statistics'
            )
        );
    }

    public function show(SponsoredWorkerRecord $sponsoredWorker)
    {
       $sponsoredWorker->load([
    'employee.manager',
    'createdByUser',
    'updatedByUser',

    'changes' => function ($query) {
        $query
            ->with([
                'createdByUser',
                'reviewedByUser',
            ])
            ->latest('created_at');
    },
]);

        $comparisons = $sponsoredWorker->comparisonResults();

        return view(
            'company.hr.sponsor-compliance.sponsored-workers.show',
            compact(
                'sponsoredWorker',
                'comparisons'
            )
        );
    }

    public function edit(SponsoredWorkerRecord $sponsoredWorker)
    {
        $sponsoredWorker->load('employee');

        return view(
            'company.hr.sponsor-compliance.sponsored-workers.edit',
            compact('sponsoredWorker')
        );
    }

    public function update(
        Request $request,
        SponsoredWorkerRecord $sponsoredWorker
    ) {
        $validated = $this->validateRecord($request);

        $validated['updated_by'] = auth('company')->id();

        $sponsoredWorker->update($validated);

        return redirect()
            ->route(
                'company.hr.sponsor-compliance.sponsored-workers.show',
                $sponsoredWorker
            )
            ->with(
                'success',
                'Sponsored worker record updated successfully.'
            );
    }

    private function validateRecord(Request $request): array
    {
        return $request->validate([
            'immigration_route' => [
                'nullable',
                'string',
                'max:100',
            ],

            'immigration_status' => [
                'nullable',
                'string',
                'max:50',
            ],

            'visa_start_date' => [
                'nullable',
                'date',
            ],

            'visa_expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:visa_start_date',
            ],

            'cos_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'cos_job_title' => [
                'nullable',
                'string',
                'max:150',
            ],

            'occupation_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'cos_salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'actual_salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'cos_weekly_hours' => [
                'nullable',
                'numeric',
                'min:0',
                'max:168',
            ],

            'cos_work_location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sponsorship_start_date' => [
                'nullable',
                'date',
            ],

            'sponsorship_end_date' => [
                'nullable',
                'date',
                'after_or_equal:sponsorship_start_date',
            ],

            'actual_first_working_date' => [
                'nullable',
                'date',
            ],

            'record_status' => [
                'required',
                'in:active,ended,inactive',
            ],

            'sponsored_duties' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'permitted_absence_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'compliance_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);
    }
    public function storeChange(
    Request $request,
    SponsoredWorkerRecord $sponsoredWorker
) {
    $validated = $request->validate([
        'change_type' => [
            'required',
            'in:job_title,salary,weekly_hours,work_location,duties,immigration_status,visa_expiry,line_manager,home_address,phone,email,other',
        ],

        'field_name' => [
            'nullable',
            'string',
            'max:100',
        ],

        'old_value' => [
            'nullable',
            'string',
            'max:10000',
        ],

        'new_value' => [
            'required',
            'string',
            'max:10000',
        ],

        'effective_date' => [
            'nullable',
            'date',
        ],

        'requires_ukvi_review' => [
            'required',
            'boolean',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:10000',
        ],
    ]);

    $validated['sponsored_worker_record_id'] =
        $sponsoredWorker->id;

    $validated['detected_at'] = now();

    $validated['review_status'] = 'pending';

    $validated['created_by'] =
        auth('company')->id();

    SponsoredWorkerChange::create($validated);

    return redirect()
        ->route(
            'company.hr.sponsor-compliance.sponsored-workers.show',
            $sponsoredWorker
        )
        ->with(
            'success',
            'Sponsored worker change recorded successfully.'
        );
}

public function reviewChange(
    Request $request,
    SponsoredWorkerRecord $sponsoredWorker,
    SponsoredWorkerChange $change
) {
    abort_unless(
        $change->sponsored_worker_record_id
            === $sponsoredWorker->id,
        404
    );

    $validated = $request->validate([
        'review_status' => [
            'required',
            'in:pending,reviewed,ukvi_case_required,no_report_required',
        ],

        'requires_ukvi_review' => [
            'required',
            'boolean',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:10000',
        ],
    ]);

    $change->update([
        'review_status' =>
            $validated['review_status'],

        'requires_ukvi_review' =>
            $validated['requires_ukvi_review'],

        'notes' =>
            $validated['notes'] ?? $change->notes,

        'reviewed_by' =>
            auth('company')->id(),

        'reviewed_at' => now(),
    ]);

    return redirect()
        ->route(
            'company.hr.sponsor-compliance.sponsored-workers.show',
            $sponsoredWorker
        )
        ->with(
            'success',
            'Change review updated successfully.'
        );
}
}