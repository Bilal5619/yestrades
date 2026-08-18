<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeDocument;
use App\Models\Company\EmployeeHoliday;
use App\Models\Company\EmployeeJob;
use App\Models\Company\EmployeeTrainingRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();
        $thirtyDaysFromNow = today()->copy()->addDays(30);
        $thirtyDaysAgo = today()->copy()->subDays(30);

        /*
        |--------------------------------------------------------------------------
        | Employees considered current
        |--------------------------------------------------------------------------
        */
        $activeEmployeeStatuses = [
            'onboarding',
            'active',
            'on_leave',
            'suspended',
            'notice_period',
        ];

        /*
        |--------------------------------------------------------------------------
        | Right to Work documents requiring action
        |--------------------------------------------------------------------------
        |
        | Includes:
        | - missing RTW / immigration evidence
        | - pending review
        | - rejected documents
        | - expired documents
        | - documents expiring within 30 days
        |
        */
        $rightToWorkSlugs = [
            'right-to-work-evidence',
            'immigration-status-evidence',
            'passport',
        ];

        $rightToWorkDocuments = EmployeeDocument::query()
            ->whereHas('employee', function ($query) use ($activeEmployeeStatuses) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->whereHas('documentType', function ($query) use ($rightToWorkSlugs) {
                $query->whereIn('slug', $rightToWorkSlugs);
            })
            ->where(function ($query) use ($today, $thirtyDaysFromNow) {
                $query
                    ->whereNull('file_path')
                    ->orWhereIn('review_status', [
                        'missing',
                        'pending_review',
                        'rejected',
                    ])
                    ->orWhere(function ($expiryQuery) use (
                        $today,
                        $thirtyDaysFromNow
                    ) {
                        $expiryQuery
                            ->whereNotNull('expiry_date')
                            ->whereDate(
                                'expiry_date',
                                '<=',
                                $thirtyDaysFromNow
                            );
                    });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Missing / pending employee documents
        |--------------------------------------------------------------------------
        */
        $employeeDocuments = EmployeeDocument::query()
            ->where('is_required', true)
            ->whereHas('employee', function ($query) use ($activeEmployeeStatuses) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->where(function ($query) {
                $query
                    ->whereNull('file_path')
                    ->orWhereIn('review_status', [
                        'missing',
                        'pending_review',
                        'rejected',
                    ]);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Training / certificates requiring attention
        |--------------------------------------------------------------------------
        */
        $trainingDue = EmployeeTrainingRecord::query()
            ->whereHas('employee', function ($query) use ($activeEmployeeStatuses) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->where(function ($query) use ($today, $thirtyDaysFromNow) {
                $query
                    ->whereIn('status', [
                        'not_started',
                        'scheduled',
                        'in_progress',
                        'expired',
                    ])
                    ->orWhere(function ($completedQuery) use (
                        $today,
                        $thirtyDaysFromNow
                    ) {
                        $completedQuery
                            ->where('status', 'completed')
                            ->whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '>=', $today)
                            ->whereDate(
                                'expiry_date',
                                '<=',
                                $thirtyDaysFromNow
                            );
                    });
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Pending jobs
        |--------------------------------------------------------------------------
        |
        | admin_status is already maintained by EmployeeJob.
        | "action_required" means the job still requires action.
        */
        $pendingJobs = EmployeeJob::query()
            ->where('admin_status', 'action_required')
            ->where('status', '!=', 'cancelled')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Employees currently on approved holiday
        |--------------------------------------------------------------------------
        */
        $employeesOnHoliday = EmployeeHoliday::query()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereHas('employee', function ($query) use ($activeEmployeeStatuses) {
                $query->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                );
            })
            ->distinct('employee_id')
            ->count('employee_id');

        /*
        |--------------------------------------------------------------------------
        | New starters in last 30 days
        |--------------------------------------------------------------------------
        */
        $newStarters = Employee::query()
            ->whereIn(
                'employment_status',
                $activeEmployeeStatuses
            )
            ->whereNotNull('start_date')
            ->whereDate('start_date', '>=', $thirtyDaysAgo)
            ->whereDate('start_date', '<=', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Employees still onboarding
        |--------------------------------------------------------------------------
        */
        $pendingOnboarding = Employee::query()
            ->where('employment_status', 'onboarding')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Employee overview
        |--------------------------------------------------------------------------
        */
        $employeeOverview = [
            'total' => Employee::query()
                ->whereIn(
                    'employment_status',
                    $activeEmployeeStatuses
                )
                ->count(),

            'active' => Employee::query()
                ->where('employment_status', 'active')
                ->count(),

            'onboarding' => Employee::query()
                ->where('employment_status', 'onboarding')
                ->count(),

            'on_leave' => Employee::query()
                ->where('employment_status', 'on_leave')
                ->count(),

            'notice_period' => Employee::query()
                ->where('employment_status', 'notice_period')
                ->count(),

            'suspended' => Employee::query()
                ->where('employment_status', 'suspended')
                ->count(),
        ];

        $stats = [
            'right_to_work' => $rightToWorkDocuments,
            'employee_documents' => $employeeDocuments,
            'missing_certificates' => $trainingDue,
            'pending_jobs' => $pendingJobs,
            'employees_on_holiday' => $employeesOnHoliday,
            'new_starters' => $newStarters,
            'training_due' => $trainingDue,
            'pending_onboarding' => $pendingOnboarding,
        ];

        return view(
            'company.dashboard',
            compact(
                'stats',
                'employeeOverview'
            )
        );
    }
}