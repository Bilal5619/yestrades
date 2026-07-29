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
}