<?php

namespace Database\Seeders;

use App\Models\Company\CompanyPermission;
use Illuminate\Database\Seeder;

class CompanyPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [

            // Dashboard
            [
                'name' => 'View Dashboard',
                'slug' => 'dashboard.view',
                'module' => 'Dashboard',
            ],

            // HR
            [
                'name' => 'View HR Overview',
                'slug' => 'hr.overview.view',
                'module' => 'HR',
            ],
            [
                'name' => 'View Employees',
                'slug' => 'employees.view',
                'module' => 'HR',
            ],
            [
                'name' => 'Create Employees',
                'slug' => 'employees.create',
                'module' => 'HR',
            ],
            [
                'name' => 'Edit Employees',
                'slug' => 'employees.edit',
                'module' => 'HR',
            ],
            [
                'name' => 'Delete Employees',
                'slug' => 'employees.delete',
                'module' => 'HR',
            ],
            [
                'name' => 'Manage Recruitment',
                'slug' => 'recruitment.manage',
                'module' => 'HR',
            ],
            [
                'name' => 'Manage Onboarding',
                'slug' => 'onboarding.manage',
                'module' => 'HR',
            ],

            // Staff
            [
                'name' => 'Manage Attendance',
                'slug' => 'attendance.manage',
                'module' => 'Staff',
            ],
            [
                'name' => 'Manage Rota and Shifts',
                'slug' => 'rota.manage',
                'module' => 'Staff',
            ],
            [
                'name' => 'View Job Sheet',
                'slug' => 'jobs.view',
                'module' => 'Staff',
            ],
            [
                'name' => 'Manage Job Sheet',
                'slug' => 'jobs.manage',
                'module' => 'Staff',
            ],
            [
                'name' => 'Manage Expenses',
                'slug' => 'expenses.manage',
                'module' => 'Staff',
            ],
            [
                'name' => 'Manage Training',
                'slug' => 'training.manage',
                'module' => 'Staff',
            ],
            [
                'name' => 'Manage Equipment and Vehicles',
                'slug' => 'equipment-vehicles.manage',
                'module' => 'Staff',
            ],

            // Announcements
            [
                'name' => 'View Announcements',
                'slug' => 'announcements.view',
                'module' => 'Announcements',
            ],
            [
                'name' => 'Manage Announcements',
                'slug' => 'announcements.manage',
                'module' => 'Announcements',
            ],

            // Reports
            [
                'name' => 'View Missing Documents Report',
                'slug' => 'reports.missing-documents',
                'module' => 'Reports',
            ],
            [
                'name' => 'View Holiday Report',
                'slug' => 'reports.holiday',
                'module' => 'Reports',
            ],
            [
                'name' => 'View Attendance Report',
                'slug' => 'reports.attendance',
                'module' => 'Reports',
            ],
            [
                'name' => 'View Overtime Report',
                'slug' => 'reports.overtime',
                'module' => 'Reports',
            ],
            [
                'name' => 'View Expense Report',
                'slug' => 'reports.expense',
                'module' => 'Reports',
            ],
            [
                'name' => 'View Training Due Report',
                'slug' => 'reports.training-due',
                'module' => 'Reports',
            ],

            // Audit logs
            [
                'name' => 'View Audit Logs',
                'slug' => 'audit-logs.view',
                'module' => 'Audit Logs',
            ],

            // Users and permissions
            [
                'name' => 'View Company Users',
                'slug' => 'company-users.view',
                'module' => 'Users and Permissions',
            ],
            [
                'name' => 'Create Company Users',
                'slug' => 'company-users.create',
                'module' => 'Users and Permissions',
            ],
            [
                'name' => 'Edit Company Users',
                'slug' => 'company-users.edit',
                'module' => 'Users and Permissions',
            ],
            [
                'name' => 'Delete Company Users',
                'slug' => 'company-users.delete',
                'module' => 'Users and Permissions',
            ],
            [
                'name' => 'Manage Roles',
                'slug' => 'roles.manage',
                'module' => 'Users and Permissions',
            ],
            [
                'name' => 'Manage Permissions',
                'slug' => 'permissions.manage',
                'module' => 'Users and Permissions',
            ],
            [
    'name' => 'View Employee Documents',
    'slug' => 'employee-documents.view',
    'module' => 'HR',
],
[
    'name' => 'Upload Employee Documents',
    'slug' => 'employee-documents.upload',
    'module' => 'HR',
],
[
    'name' => 'Download Employee Documents',
    'slug' => 'employee-documents.download',
    'module' => 'HR',
],
[
    'name' => 'Approve Employee Documents',
    'slug' => 'employee-documents.approve',
    'module' => 'HR',
],
[
    'name' => 'Delete Employee Documents',
    'slug' => 'employee-documents.delete',
    'module' => 'HR',
],
[
    'name' => 'View Holidays',
    'slug' => 'holidays.view',
    'module' => 'HR',
],
[
    'name' => 'Create Holidays',
    'slug' => 'holidays.create',
    'module' => 'HR',
],
[
    'name' => 'Edit Holidays',
    'slug' => 'holidays.edit',
    'module' => 'HR',
],
[
    'name' => 'Approve Holidays',
    'slug' => 'holidays.approve',
    'module' => 'HR',
],
[
    'name' => 'Cancel Holidays',
    'slug' => 'holidays.cancel',
    'module' => 'HR',
],
[
    'name' => 'Manage Holiday Allowances',
    'slug' => 'holiday-allowances.manage',
    'module' => 'HR',
],
[
    'name' => 'View Training',
    'slug' => 'training.view',
    'module' => 'HR',
],
[
    'name' => 'Create Training Records',
    'slug' => 'training.create',
    'module' => 'HR',
],
[
    'name' => 'Edit Training Records',
    'slug' => 'training.edit',
    'module' => 'HR',
],
[
    'name' => 'Complete Training',
    'slug' => 'training.complete',
    'module' => 'HR',
],
[
    'name' => 'Manage Training Courses',
    'slug' => 'training-courses.manage',
    'module' => 'HR',
],
[
    'name' => 'Upload Training Certificates',
    'slug' => 'training-certificates.upload',
    'module' => 'HR',
],
[
    'name' => 'Download Training Certificates',
    'slug' => 'training-certificates.download',
    'module' => 'HR',
],
[
    'name' => 'Delete Training Records',
    'slug' => 'training.delete',
    'module' => 'HR',
],
[
    'name' => 'View Onboarding',
    'slug' => 'onboarding.view',
],
[
    'name' => 'Manage Onboarding',
    'slug' => 'onboarding.manage',
],
[
    'name' => 'Complete Onboarding Sections',
    'slug' => 'onboarding.sections.complete',
],
[
    'name' => 'Complete Employee Onboarding',
    'slug' => 'onboarding.complete',
],
[
    'name' => 'View Contract Templates',
    'slug' => 'contract_templates.view',
],
[
    'name' => 'Manage Contract Templates',
    'slug' => 'contract_templates.manage',
],
[
    'name' => 'Generate Employee Contracts',
    'slug' => 'employee_contracts.generate',
],
[
    'name' => 'View Employee Contracts',
    'slug' => 'employee_contracts.view',
],
[
    'name' => 'Download Employee Contracts',
    'slug' => 'employee_contracts.download',
],
[
    'name' => 'Upload Signed Contracts',
    'slug' => 'employee_contracts.upload_signed',
],
[
    'name' => 'View Payslips',
    'slug' => 'payslips.view',
],
[
    'name' => 'Upload Payslips',
    'slug' => 'payslips.upload',
],
[
    'name' => 'Download Payslips',
    'slug' => 'payslips.download',
],
[
    'name' => 'Delete Payslips',
    'slug' => 'payslips.delete',
],
[
    'name' => 'View Sponsor Compliance',
    'slug' => 'sponsor-compliance.view',
],
[
    'name' => 'Edit Sponsor Compliance',
    'slug' => 'sponsor-compliance.edit',
],
[
    'name' => 'Review Sponsor Compliance',
    'slug' => 'sponsor-compliance.review',
],
[
    'name' => 'View Employee Absences',
    'slug' => 'absences.view',
    'module' => 'HR',
],
[
    'name' => 'Manage Employee Absences',
    'slug' => 'absences.manage',
    'module' => 'HR',
],[
    'name' => 'View Company Policies',
    'slug' => 'policies.view',
    'module' => 'HR',
],
[
    'name' => 'Manage Company Policies',
    'slug' => 'policies.manage',
    'module' => 'HR',
],
        ];

        foreach ($permissions as $permission) {
            CompanyPermission::updateOrCreate(
                [
                    'slug' => $permission['slug'],
                ],
                $permission
            );
        }
    }
}