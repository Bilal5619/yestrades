<?php

namespace App\Http\Helpers;

use App\Models\Company\Employee;
use App\Models\Company\EmployeeDocument;
use App\Models\Company\EmployeeOnboarding;
use App\Models\Company\EmployeeTrainingRecord;
use App\Models\Company\SponsoredWorkerChange;
use App\Models\Company\SponsoredWorkerRecord;

class CompanyHrReminders
{
    public static function items(): array
    {
        $user = auth('company')->user();

        if (!$user) {
            return [];
        }

        $items = [];

        $activeStatuses = [
            'onboarding',
            'active',
            'on_leave',
            'suspended',
            'notice_period',
        ];

        /*
        |--------------------------------------------------------------------------
        | Right to Work
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasPermission(
                'employee-documents.view'
            )
        ) {
            $rightToWork = EmployeeDocument::query()
                ->whereHas(
                    'employee',
                    function ($query) use (
                        $activeStatuses
                    ) {
                        $query->whereIn(
                            'employment_status',
                            $activeStatuses
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
                ->where(function ($query) {
                    $query
                        ->whereNull('file_path')
                        ->orWhereIn(
                            'review_status',
                            [
                                'missing',
                                'pending_review',
                                'rejected',
                            ]
                        )
                        ->orWhere(function ($query) {
                            $query
                                ->whereNotNull(
                                    'expiry_date'
                                )
                                ->whereDate(
                                    'expiry_date',
                                    '<=',
                                    today()
                                        ->copy()
                                        ->addDays(30)
                                );
                        });
                })
                ->count();

            if ($rightToWork > 0) {
                $items[] = [
                    'title' =>
                        'Right to Work checks',

                    'message' =>
                        $rightToWork .
                        ' document(s) need attention',

                    'count' => $rightToWork,

                    'route' =>
                        'company.hr.right-to-work',

                    'type' => 'danger',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | General Employee Documents
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasPermission(
                'employee-documents.view'
            )
        ) {
            $documents = EmployeeDocument::query()
                ->where('is_required', true)
                ->whereHas(
                    'employee',
                    function ($query) use (
                        $activeStatuses
                    ) {
                        $query->whereIn(
                            'employment_status',
                            $activeStatuses
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
                                'pending_review',
                                'rejected',
                            ]
                        );
                })
                ->count();

            if ($documents > 0) {
                $items[] = [
                    'title' =>
                        'Employee documents',

                    'message' =>
                        $documents .
                        ' document(s) need action',

                    'count' => $documents,

                    'route' =>
                        'company.hr.employees.missing-documents',

                    'type' => 'warning',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Training
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasPermission(
                'training.view'
            )
        ) {
            $training = EmployeeTrainingRecord::query()
                ->whereHas(
                    'employee',
                    function ($query) use (
                        $activeStatuses
                    ) {
                        $query->whereIn(
                            'employment_status',
                            $activeStatuses
                        );
                    }
                )
                ->where(function ($query) {
                    $query
                        ->whereIn(
                            'status',
                            [
                                'not_started',
                                'scheduled',
                                'in_progress',
                                'expired',
                            ]
                        )
                        ->orWhere(function ($query) {
                            $query
                                ->where(
                                    'status',
                                    'completed'
                                )
                                ->whereNotNull(
                                    'expiry_date'
                                )
                                ->whereDate(
                                    'expiry_date',
                                    '>=',
                                    today()
                                )
                                ->whereDate(
                                    'expiry_date',
                                    '<=',
                                    today()
                                        ->copy()
                                        ->addDays(30)
                                );
                        });
                })
                ->count();

            if ($training > 0) {
                $items[] = [
                    'title' =>
                        'Training due',

                    'message' =>
                        $training .
                        ' training item(s) need attention',

                    'count' => $training,

                    'route' =>
                        'company.hr.training.expiring',

                    'type' => 'warning',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Onboarding
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasPermission(
                'onboarding.view'
            )
        ) {
            $onboarding =
                EmployeeOnboarding::query()
                    ->whereIn(
                        'status',
                        [
                            'pending',
                            'in_progress',
                        ]
                    )
                    ->count();

            if ($onboarding > 0) {
                $items[] = [
                    'title' =>
                        'Onboarding incomplete',

                    'message' =>
                        $onboarding .
                        ' employee(s) still onboarding',

                    'count' => $onboarding,

                    'route' =>
                        'company.hr.onboarding.index',

                    'type' => 'info',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sponsor Compliance
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasPermission(
                'sponsor-compliance.view'
            )
        ) {
            $visaIssues =
                SponsoredWorkerRecord::query()
                    ->where('record_status', 'active')
                    ->where(function ($query) {
                        $query
                            ->whereNull(
                                'visa_expiry_date'
                            )
                            ->orWhereDate(
                                'visa_expiry_date',
                                '<=',
                                today()
                                    ->copy()
                                    ->addDays(90)
                            );
                    })
                    ->count();

            $pendingChanges =
                SponsoredWorkerChange::query()
                    ->where(
                        'review_status',
                        'pending'
                    )
                    ->count();

            $sponsorTotal =
                $visaIssues +
                $pendingChanges;

            if ($sponsorTotal > 0) {
                $items[] = [
                    'title' =>
                        'Sponsor compliance',

                    'message' =>
                        $sponsorTotal .
                        ' compliance item(s) need review',

                    'count' => $sponsorTotal,

                    'route' =>
                        'company.hr.sponsor-compliance.sponsored-workers.index',

                    'type' => 'danger',
                ];
            }
        }

        return $items;
    }

    public static function count(): int
    {
        return collect(
            static::items()
        )->sum('count');
    }
}