<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyUser;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeDocument;
use App\Models\Company\EmployeeHoliday;
use App\Models\Company\EmployeeTrainingRecord;


class HRController extends Controller
{
    public function overview()
    {
        $today = now()->startOfDay();
        $thirtyDaysFromNow = now()->addDays(30)->endOfDay();
        $thirtyDaysAgo = now()->subDays(30)->startOfDay();

        $activeEmployeeStatuses = [
            'onboarding',
            'active',
            'on_leave',
            'suspended',
            'notice_period',
        ];

        $stats = [
            /*
            |--------------------------------------------------------------------------
            | Employees who are not archived or marked as having left
            |--------------------------------------------------------------------------
            */
            'total_employees' => Employee::query()
                ->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                )
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Total required documents that have not been uploaded
            |--------------------------------------------------------------------------
            */
            'missing_documents' => EmployeeDocument::query()
                ->where('is_required', true)
                ->where(function ($query) {
                    $query
                        ->whereNull('file_path')
                        ->orWhere('review_status', 'missing');
                })
                ->whereHas('employee', function ($query) use (
                    $activeEmployeeStatuses
                ) {
                    $query->whereIn(
                        'employment_status',
                        $activeEmployeeStatuses
                    );
                })
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Documents expiring in the next 30 days
            |--------------------------------------------------------------------------
            */
            'expiring_documents' => EmployeeDocument::query()
                ->whereNotNull('file_path')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', $today)
                ->whereDate(
                    'expiry_date',
                    '<=',
                    $thirtyDaysFromNow
                )
                ->whereHas('employee', function ($query) use (
                    $activeEmployeeStatuses
                ) {
                    $query->whereIn(
                        'employment_status',
                        $activeEmployeeStatuses
                    );
                })
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Holiday records will be connected later
            |--------------------------------------------------------------------------
            */
                'employees_on_holiday' => EmployeeHoliday::query()
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->whereHas('employee', function ($query) {
                    $query->whereNotIn(
                        'employment_status',
                        [
                            'archived',
                            'left_company',
                        ]
                    );
                })
                ->distinct('employee_id')
                ->count('employee_id'),      

            /*
            |--------------------------------------------------------------------------
            | Employees starting during the last 30 days
            |--------------------------------------------------------------------------
            */
            'new_starters' => Employee::query()
                ->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                )
                ->whereNotNull('start_date')
                ->whereDate('start_date', '>=', $thirtyDaysAgo)
                ->whereDate('start_date', '<=', $today)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Employees still going through onboarding
            |--------------------------------------------------------------------------
            */
            'pending_onboarding' => Employee::query()
                ->where(
                    'employment_status',
                    'onboarding'
                )
                ->count(),

                 /*
            |--------------------------------------------------------------------------
            | Employees training
            |--------------------------------------------------------------------------
            */
            'training_due' => EmployeeTrainingRecord::query()
    ->whereHas('employee', function ($query) {
        $query->whereNotIn(
            'employment_status',
            [
                'archived',
                'left_company',
            ]
        );
    })
    ->where(function ($query) {
        $query
            ->whereIn('status', [
                'not_started',
                'scheduled',
                'in_progress',
                'expired',
            ])
            ->orWhere(function ($trainingQuery) {
                $trainingQuery
                    ->where('status', 'completed')
                    ->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '>=', today())
                    ->whereDate(
                        'expiry_date',
                        '<=',
                        today()->addDays(30)
                    );
            });
    })
    ->count(),
        ];

        $recentCompanyUsers = CompanyUser::with('companyRole')
            ->latest()
            ->take(5)
            ->get();

        return view(
            'company.hr.overview',
            compact(
                'stats',
                'recentCompanyUsers'
            )
        );
    }
    public function rightToWork(\Illuminate\Http\Request $request)
{
    $today = today();
    $thirtyDaysFromNow = today()->copy()->addDays(30);

    $activeStatuses = [
        'onboarding',
        'active',
        'on_leave',
        'suspended',
        'notice_period',
    ];

   $query = Employee::query()
    ->whereIn(
        'employment_status',
        $activeStatuses
    )
    ->where(
        'work_location_type',
        'uk'
    )
        ->with([
            'documents' => function ($query) {
                $query->with([
                    'documentType',
                    'reviewedBy',
                ])
                ->whereHas(
                    'documentType',
                    function ($documentTypeQuery) {
                        $documentTypeQuery->whereIn(
                            'slug',
                            [
                                'passport',
                                'right-to-work-evidence',
                                'immigration-status-evidence',
                            ]
                        );
                    }
                );
            },
        ]);

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($query) use ($search) {
            $query
                ->where(
                    'employee_number',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
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
                    'work_email',
                    'like',
                    "%{$search}%"
                );
        });
    }

    if ($request->filled('sponsored')) {
        $query->where(
            'is_sponsored_worker',
            $request->sponsored === 'yes'
        );
    }

    $employees = $query
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->paginate(20)
        ->withQueryString();

    $employees->getCollection()->transform(
        function ($employee) use (
            $today,
            $thirtyDaysFromNow
        ) {
            $rtwDocument = $employee->documents
                ->first(function ($document) {
                    return optional(
                        $document->documentType
                    )->slug ===
                        'right-to-work-evidence';
                });

            $immigrationDocument = $employee->documents
                ->first(function ($document) {
                    return optional(
                        $document->documentType
                    )->slug ===
                        'immigration-status-evidence';
                });

            $passport = $employee->documents
                ->first(function ($document) {
                    return optional(
                        $document->documentType
                    )->slug === 'passport';
                });

            $primaryDocument =
                $rtwDocument
                ?: $immigrationDocument;

            if (
                !$primaryDocument ||
                !$primaryDocument->file_path
            ) {
                $status = 'missing';
            } elseif (
                $primaryDocument->review_status ===
                'rejected'
            ) {
                $status = 'rejected';
            } elseif (
                $primaryDocument->expiry_date &&
                $primaryDocument->expiry_date->lt(
                    $today
                )
            ) {
                $status = 'expired';
            } elseif (
                $primaryDocument->expiry_date &&
                $primaryDocument->expiry_date->lte(
                    $thirtyDaysFromNow
                )
            ) {
                $status = 'expiring';
            } elseif (
                $primaryDocument->review_status ===
                'pending_review'
            ) {
                $status = 'pending_review';
            } elseif (
                $primaryDocument->review_status ===
                'approved'
            ) {
                $status = 'verified';
            } else {
                $status = 'action_required';
            }

            $employee->rtw_document =
                $rtwDocument;

            $employee->immigration_document =
                $immigrationDocument;

            $employee->passport_document =
                $passport;

            $employee->rtw_status =
                $status;

            return $employee;
        }
    );

    if ($request->filled('status')) {
        $filtered = $employees
            ->getCollection()
            ->filter(function ($employee) use ($request) {
                return $employee->rtw_status ===
                    $request->status;
            })
            ->values();

        $employees->setCollection($filtered);
    }

    $stats = [
        'missing' => EmployeeDocument::query()
            ->whereHas(
    'employee',
    function ($query) use ($activeStatuses) {
        $query
            ->whereIn(
                'employment_status',
                $activeStatuses
            )
            ->where(
                'work_location_type',
                'uk'
            );
    }
)
            ->whereHas(
                'documentType',
                function ($query) {
                    $query->where(
                        'slug',
                        'right-to-work-evidence'
                    );
                }
            )
            ->whereNull('file_path')
            ->count(),

        'pending_review' =>
            EmployeeDocument::query()
               ->whereHas(
    'employee',
    function ($query) use ($activeStatuses) {
        $query
            ->whereIn(
                'employment_status',
                $activeStatuses
            )
            ->where(
                'work_location_type',
                'uk'
            );
    }
)
                ->whereHas(
                    'documentType',
                    function ($query) {
                        $query->whereIn(
                            'slug',
                            [
                                'right-to-work-evidence',
                                'immigration-status-evidence',
                            ]
                        );
                    }
                )
                ->where(
                    'review_status',
                    'pending_review'
                )
                ->count(),

        'expiring' =>
            EmployeeDocument::query()
                ->whereHas(
    'employee',
    function ($query) use ($activeStatuses) {
        $query
            ->whereIn(
                'employment_status',
                $activeStatuses
            )
            ->where(
                'work_location_type',
                'uk'
            );
    }
)
                ->whereHas(
                    'documentType',
                    function ($query) {
                        $query->whereIn(
                            'slug',
                            [
                                'right-to-work-evidence',
                                'immigration-status-evidence',
                            ]
                        );
                    }
                )
                ->whereNotNull('expiry_date')
                ->whereDate(
                    'expiry_date',
                    '>=',
                    $today
                )
                ->whereDate(
                    'expiry_date',
                    '<=',
                    $thirtyDaysFromNow
                )
                ->count(),

        'expired' =>
            EmployeeDocument::query()
                ->whereHas(
    'employee',
    function ($query) use ($activeStatuses) {
        $query
            ->whereIn(
                'employment_status',
                $activeStatuses
            )
            ->where(
                'work_location_type',
                'uk'
            );
    }
)
                ->whereHas(
                    'documentType',
                    function ($query) {
                        $query->whereIn(
                            'slug',
                            [
                                'right-to-work-evidence',
                                'immigration-status-evidence',
                            ]
                        );
                    }
                )
                ->whereNotNull('expiry_date')
                ->whereDate(
                    'expiry_date',
                    '<',
                    $today
                )
                ->count(),
    ];

    return view(
        'company.hr.right-to-work',
        compact(
            'employees',
            'stats'
        )
    );
}
}