<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Company Own Routes
|--------------------------------------------------------------------------
*/

Route::prefix('company-own')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Guest company routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('guest:company')->group(function () {

        Route::get('/', function () {
            return redirect()->route('company.login');
        });

        Route::get('/login', 'Company\AuthController@showLogin')
            ->name('company.login');

        Route::post('/login', 'Company\AuthController@login')
            ->name('company.login.submit');
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated company routes
    |--------------------------------------------------------------------------
    */

   Route::middleware('auth:company')->group(function () {

    Route::get(
        '/dashboard',
        'Company\DashboardController@index'
    )
        ->middleware('company.permission:dashboard.view')
        ->name('company.dashboard');

    Route::post(
        '/logout',
        'Company\AuthController@logout'
    )
        ->name('company.logout');


    Route::get(
    '/hr',
    'Company\HRController@overview'
    )
    ->middleware('company.permission:hr.overview.view')
    ->name('company.hr.overview');

    Route::get(
    '/hr/employees',
    'Company\EmployeeController@index'
    )
    ->middleware('company.permission:employees.view')
    ->name('company.hr.employees.index');
   
 
    Route::get(
    '/hr/employees/create',
    'Company\EmployeeController@create'
)
    ->middleware('company.permission:employees.create')
        ->name('company.hr.employees.create');

    Route::get(
    '/hr/employees/missing-documents',
    'Company\EmployeeController@missingDocuments'
)
    ->middleware(
        'company.permission:employee-documents.view'
    )
    ->name('company.hr.employees.missing-documents');
 Route::get(
    '/hr/employees/expiring-documents',
    'Company\EmployeeDocumentController@expiring'
)
    ->middleware(
        'company.permission:employee-documents.view'
    )
    ->name('company.hr.employees.expiring-documents');

    Route::get(
            '/hr/employees/archived',
            'Company\EmployeeController@archived'
        )
            ->middleware('company.permission:employees.view')
            ->name('company.hr.employees.archived');

        Route::post(
            '/hr/employees/{employee}/restore',
            'Company\EmployeeController@restore'
        )
            ->middleware('company.permission:employees.edit')
            ->name('company.hr.employees.restore');
                
    Route::post(
        '/hr/employees',
        'Company\EmployeeController@store'
    )
        ->middleware('company.permission:employees.create')
        ->name('company.hr.employees.store');
 
     
    Route::get(
    '/hr/employees/{employee}',
    'Company\EmployeeController@show'
)
    ->middleware('company.permission:employees.view')
    ->name('company.hr.employees.show');

    Route::get(
        '/hr/employees/{employee}/edit',
        'Company\EmployeeController@edit'
    )
        ->middleware('company.permission:employees.edit')
        ->name('company.hr.employees.edit');

    Route::put(
        '/hr/employees/{employee}',
        'Company\EmployeeController@update'
    )
        ->middleware('company.permission:employees.edit')
        ->name('company.hr.employees.update');
   
      Route::get(
    '/hr/employees/{employee}/documents',
    'Company\EmployeeDocumentController@index'
)
    ->middleware(
        'company.permission:employee-documents.view'
    )
    ->name('company.hr.employees.documents.index');

Route::post(
    '/hr/employees/{employee}/documents',
    'Company\EmployeeDocumentController@store'
)
    ->middleware(
        'company.permission:employee-documents.upload'
    )
    ->name('company.hr.employees.documents.store');

Route::get(
    '/hr/employees/{employee}/documents/{document}/view',
    'Company\EmployeeDocumentController@view'
)
    ->middleware(
        'company.permission:employee-documents.view'
    )
    ->name('company.hr.employees.documents.view');

Route::get(
    '/hr/employees/{employee}/documents/{document}/download',
    'Company\EmployeeDocumentController@download'
)
    ->middleware(
        'company.permission:employee-documents.download'
    )
    ->name('company.hr.employees.documents.download');


Route::post(
    '/hr/employees/{employee}/documents/{document}/approve',
    'Company\EmployeeDocumentController@approve'
)
    ->middleware(
        'company.permission:employee-documents.approve'
    )
    ->name('company.hr.employees.documents.approve');

Route::post(
    '/hr/employees/{employee}/documents/{document}/reject',
    'Company\EmployeeDocumentController@reject'
)
    ->middleware(
        'company.permission:employee-documents.approve'
    )
    ->name('company.hr.employees.documents.reject');

    
    /*
    |--------------------------------------------------------------------------
    | holidays users
    |--------------------------------------------------------------------------
    */


Route::get(
    '/hr/holidays',
    'Company\HolidayController@index'
)
    ->middleware('company.permission:holidays.view')
    ->name('company.hr.holidays.index');
Route::get(
    '/hr/holidays/allowances',
    'Company\HolidayController@allowances'
)
    ->middleware(
        'company.permission:holiday-allowances.manage'
    )
    ->name('company.hr.holidays.allowances');

Route::post(
    '/hr/holidays/allowances',
    'Company\HolidayController@storeAllowance'
)
    ->middleware(
        'company.permission:holiday-allowances.manage'
    )
    ->name('company.hr.holidays.allowances.store');

    Route::get(
    '/hr/holidays/create',
    'Company\HolidayController@create'
)
    ->middleware('company.permission:holidays.create')
    ->name('company.hr.holidays.create');

Route::post(
    '/hr/holidays',
    'Company\HolidayController@store'
)
    ->middleware('company.permission:holidays.create')
    ->name('company.hr.holidays.store');

Route::get(
    '/hr/holidays/requests',
    'Company\HolidayController@requests'
)
    ->middleware('company.permission:holidays.view')
    ->name('company.hr.holidays.requests');

Route::get(
    '/hr/holidays/{holiday}',
    'Company\HolidayController@show'
)
    ->middleware('company.permission:holidays.view')
    ->name('company.hr.holidays.show');

Route::post(
    '/hr/holidays/{holiday}/approve',
    'Company\HolidayController@approve'
)
    ->middleware('company.permission:holidays.approve')
    ->name('company.hr.holidays.approve');

Route::post(
    '/hr/holidays/{holiday}/reject',
    'Company\HolidayController@reject'
)
    ->middleware('company.permission:holidays.approve')
    ->name('company.hr.holidays.reject');

Route::post(
    '/hr/holidays/{holiday}/cancel',
    'Company\HolidayController@cancel'
)
    ->middleware('company.permission:holidays.cancel')
    ->name('company.hr.holidays.cancel');




/*
    |--------------------------------------------------------------------------
    | Company training routes
    |--------------------------------------------------------------------------
    */


Route::get(
    '/hr/training',
    'Company\TrainingController@index'
)
    ->middleware('company.permission:training.view')
    ->name('company.hr.training.index');    


Route::get(
    '/hr/training/courses',
    'Company\TrainingController@courses'
)
    ->middleware(
        'company.permission:training-courses.manage'
    )
    ->name('company.hr.training.courses.index');

Route::get(
    '/hr/training/courses/create',
    'Company\TrainingController@createCourse'
)
    ->middleware(
        'company.permission:training-courses.manage'
    )
    ->name('company.hr.training.courses.create');

Route::post(
    '/hr/training/courses',
    'Company\TrainingController@storeCourse'
)
    ->middleware(
        'company.permission:training-courses.manage'
    )
    ->name('company.hr.training.courses.store');

Route::get(
    '/hr/training/courses/{course}/edit',
    'Company\TrainingController@editCourse'
)
    ->middleware(
        'company.permission:training-courses.manage'
    )
    ->name('company.hr.training.courses.edit');

Route::put(
    '/hr/training/courses/{course}',
    'Company\TrainingController@updateCourse'
)
    ->middleware(
        'company.permission:training-courses.manage'
    )
    ->name('company.hr.training.courses.update');

Route::get(
    '/hr/training/records',
    'Company\TrainingController@records'
)
    ->middleware('company.permission:training.view')
    ->name('company.hr.training.records.index');

Route::get(
    '/hr/training/records/create',
    'Company\TrainingController@createRecord'
)
    ->middleware('company.permission:training.create')
    ->name('company.hr.training.records.create');

Route::post(
    '/hr/training/records',
    'Company\TrainingController@storeRecord'
)
    ->middleware('company.permission:training.create')
    ->name('company.hr.training.records.store');
Route::get(
    '/hr/training/records/{record}',
    'Company\TrainingController@showRecord'
)
    ->middleware('company.permission:training.view')
    ->name('company.hr.training.records.show');

Route::post(
    '/hr/training/records/{record}/complete',
    'Company\TrainingController@completeRecord'
)
    ->middleware('company.permission:training.complete')
    ->name('company.hr.training.records.complete');

Route::get(
    '/hr/training/records/{record}/certificate',
    'Company\TrainingController@downloadCertificate'
)
    ->middleware(
        'company.permission:training-certificates.download'
    )
    ->name(
        'company.hr.training.records.certificate.download'
    );
    Route::get(
    '/hr/training/records/{record}/edit',
    'Company\TrainingController@editRecord'
)
    ->middleware('company.permission:training.edit')
    ->name('company.hr.training.records.edit');

Route::put(
    '/hr/training/records/{record}',
    'Company\TrainingController@updateRecord'
)
    ->middleware('company.permission:training.edit')
    ->name('company.hr.training.records.update');

Route::post(
    '/hr/training/records/{record}/cancel',
    'Company\TrainingController@cancelRecord'
)
    ->middleware('company.permission:training.edit')
    ->name('company.hr.training.records.cancel');
Route::get(
    '/hr/training/expiring',
    'Company\TrainingController@expiring'
)
    ->middleware('company.permission:training.view')
    ->name('company.hr.training.expiring');

 Route::get(
    '/hr/training/history',
    'Company\TrainingController@history'
)
    ->middleware('company.permission:training.view')
    ->name('company.hr.training.history');   
/*
    |--------------------------------------------------------------------------
    | Company onboarding routes
    |--------------------------------------------------------------------------
    */


Route::get(
    '/hr/onboarding',
    'Company\OnboardingController@index'
)
    ->middleware('company.permission:onboarding.view')
    ->name('company.hr.onboarding.index');    


Route::get(
    '/hr/onboarding/{onboarding}',
    'Company\OnboardingController@show'
)
    ->middleware('company.permission:onboarding.view')
    ->name('company.hr.onboarding.show');
Route::get(
    '/hr/onboarding/{onboarding}/job-manager',
    'Company\OnboardingController@jobManager'
)
    ->middleware(
        'company.permission:onboarding.view'
    )
    ->name(
        'company.hr.onboarding.job-manager'
    );

Route::put(
    '/hr/onboarding/{onboarding}/job-manager',
    'Company\OnboardingController@updateJobManager'
)
    ->middleware(
        'company.permission:onboarding.manage'
    )
    ->name(
        'company.hr.onboarding.job-manager.update'
    );
Route::get(
    '/hr/onboarding/{onboarding}/personal-details',
    'Company\OnboardingController@personalDetails'
)
    ->middleware(
        'company.permission:onboarding.view'
    )
    ->name(
        'company.hr.onboarding.personal-details'
    );

Route::put(
    '/hr/onboarding/{onboarding}/personal-details',
    'Company\OnboardingController@updatePersonalDetails'
)
    ->middleware(
        'company.permission:onboarding.manage'
    )
    ->name(
        'company.hr.onboarding.personal-details.update'
    );

    Route::get(
    '/hr/onboarding/{onboarding}/induction',
    'Company\OnboardingController@induction'
)
    ->middleware(
        'company.permission:onboarding.view'
    )
    ->name(
        'company.hr.onboarding.induction'
    );

Route::put(
    '/hr/onboarding/{onboarding}/induction',
    'Company\OnboardingController@updateInduction'
)
    ->middleware(
        'company.permission:onboarding.manage'
    )
    ->name(
        'company.hr.onboarding.induction.update'
    );
    Route::get(
    '/hr/onboarding/{onboarding}/payroll',
    'Company\OnboardingController@payroll'
)
    ->middleware(
        'company.permission:onboarding.view'
    )
    ->name(
        'company.hr.onboarding.payroll'
    );

Route::put(
    '/hr/onboarding/{onboarding}/payroll',
    'Company\OnboardingController@updatePayroll'
)
    ->middleware(
        'company.permission:onboarding.manage'
    )
    ->name(
        'company.hr.onboarding.payroll.update'
    );


    Route::get(
    '/hr/contract-templates',
    'Company\ContractTemplateController@index'
)
    ->middleware(
        'company.permission:contract_templates.view'
    )
    ->name(
        'company.hr.contract-templates.index'
    );

Route::get(
    '/hr/contract-templates/create',
    'Company\ContractTemplateController@create'
)
    ->middleware(
        'company.permission:contract_templates.manage'
    )
    ->name(
        'company.hr.contract-templates.create'
    );

Route::post(
    '/hr/contract-templates',
    'Company\ContractTemplateController@store'
)
    ->middleware(
        'company.permission:contract_templates.manage'
    )
    ->name(
        'company.hr.contract-templates.store'
    );

Route::get(
    '/hr/contract-templates/{contractTemplate}/edit',
    'Company\ContractTemplateController@edit'
)
    ->middleware(
        'company.permission:contract_templates.manage'
    )
    ->name(
        'company.hr.contract-templates.edit'
    );

Route::put(
    '/hr/contract-templates/{contractTemplate}',
    'Company\ContractTemplateController@update'
)
    ->middleware(
        'company.permission:contract_templates.manage'
    )
    ->name(
        'company.hr.contract-templates.update'
    );

    Route::get(
    '/hr/onboarding/{onboarding}/contract',
    'Company\OnboardingController@contract'
)
    ->middleware(
        'company.permission:employee_contracts.view'
    )
    ->name(
        'company.hr.onboarding.contract'
    );

Route::post(
    '/hr/onboarding/{onboarding}/contract/preview',
    'Company\OnboardingController@previewContract'
)
    ->middleware(
        'company.permission:employee_contracts.view'
    )
    ->name(
        'company.hr.onboarding.contract.preview'
    );

Route::post(
    '/hr/onboarding/{onboarding}/contract/draft',
    'Company\OnboardingController@saveContractDraft'
)
    ->middleware(
        'company.permission:employee_contracts.generate'
    )
    ->name(
        'company.hr.onboarding.contract.draft'
    );

Route::post(
    '/hr/onboarding/{onboarding}/contract/generate',
    'Company\OnboardingController@generateContractPdf'
)
    ->middleware(
        'company.permission:employee_contracts.generate'
    )
    ->name(
        'company.hr.onboarding.contract.generate'
    );

Route::get(
    '/hr/onboarding/{onboarding}/contracts/{contract}/download',
    'Company\OnboardingController@downloadGeneratedContract'
)
    ->middleware(
        'company.permission:employee_contracts.download'
    )
    ->name(
        'company.hr.onboarding.contract.download'
    );
Route::post(
    '/hr/onboarding/{onboarding}/contracts/{contract}/signed',
    'Company\OnboardingController@uploadSignedContract'
)
    ->middleware(
        'company.permission:employee_contracts.upload_signed'
    )
    ->name(
        'company.hr.onboarding.contract.signed.upload'
    );

Route::get(
    '/hr/onboarding/{onboarding}/contracts/{contract}/signed/download',
    'Company\OnboardingController@downloadSignedContract'
)
    ->middleware(
        'company.permission:employee_contracts.download'
    )
    ->name(
        'company.hr.onboarding.contract.signed.download'
    );


    
    /*
    |--------------------------------------------------------------------------
    | Company users payslips
    |--------------------------------------------------------------------------
    */
Route::get(
    '/hr/payslips',
    'Company\PayslipController@index'
)
    ->middleware(
        'company.permission:payslips.view'
    )
    ->name(
        'company.hr.payslips.index'
    );
Route::get(
    '/hr/payslips',
    'Company\PayslipController@index'
)
    ->middleware(
        'company.permission:payslips.view'
    )
    ->name(
        'company.hr.payslips.index'
    );

Route::get(
    '/hr/payslips/upload',
    'Company\PayslipController@create'
)
    ->middleware(
        'company.permission:payslips.upload'
    )
    ->name(
        'company.hr.payslips.create'
    );

Route::post(
    '/hr/payslips',
    'Company\PayslipController@store'
)
    ->middleware(
        'company.permission:payslips.upload'
    )
    ->name(
        'company.hr.payslips.store'
    );

Route::get(
    '/hr/employees/{employee}/payslips',
    'Company\PayslipController@history'
)
    ->middleware(
        'company.permission:payslips.view'
    )
    ->name(
        'company.hr.payslips.history'
    );

Route::get(
    '/hr/payslips/{payslip}/download',
    'Company\PayslipController@download'
)
    ->middleware(
        'company.permission:payslips.download'
    )
    ->name(
        'company.hr.payslips.download'
    );

Route::get(
    '/hr/payslips/{payslip}/replace',
    'Company\PayslipController@replaceForm'
)
    ->middleware(
        'company.permission:payslips.upload'
    )
    ->name(
        'company.hr.payslips.replace'
    );

Route::put(
    '/hr/payslips/{payslip}',
    'Company\PayslipController@replace'
)
    ->middleware(
        'company.permission:payslips.upload'
    )
    ->name(
        'company.hr.payslips.update'
    );

Route::delete(
    '/hr/payslips/{payslip}',
    'Company\PayslipController@destroy'
)
    ->middleware(
        'company.permission:payslips.delete'
    )
    ->name(
        'company.hr.payslips.destroy'
    );


    /*
    |--------------------------------------------------------------------------
    | Company users
    |--------------------------------------------------------------------------
    */

    Route::prefix('users')->group(function () {

        Route::get(
            '/',
            'Company\CompanyUserController@index'
        )
            ->middleware('company.permission:company-users.view')
            ->name('company.users.index');

        Route::get(
            '/create',
            'Company\CompanyUserController@create'
        )
            ->middleware('company.permission:company-users.create')
            ->name('company.users.create');

        Route::post(
            '/',
            'Company\CompanyUserController@store'
        )
            ->middleware('company.permission:company-users.create')
            ->name('company.users.store');

        Route::get(
            '/{user}/edit',
            'Company\CompanyUserController@edit'
        )
            ->middleware('company.permission:company-users.edit')
            ->name('company.users.edit');

        Route::put(
            '/{user}',
            'Company\CompanyUserController@update'
        )
            ->middleware('company.permission:company-users.edit')
            ->name('company.users.update');

        Route::post(
            '/{user}/toggle-status',
            'Company\CompanyUserController@toggleStatus'
        )
            ->middleware('company.permission:company-users.edit')
            ->name('company.users.toggle-status');
    });


    /*
    |--------------------------------------------------------------------------
    | Company roles
    |--------------------------------------------------------------------------
    */

    Route::prefix('roles')->group(function () {

        Route::get(
            '/',
            'Company\CompanyRoleController@index'
        )
            ->middleware('company.permission:roles.manage')
            ->name('company.roles.index');

        Route::get(
            '/create',
            'Company\CompanyRoleController@create'
        )
            ->middleware('company.permission:roles.manage')
            ->name('company.roles.create');

        Route::post(
            '/',
            'Company\CompanyRoleController@store'
        )
            ->middleware('company.permission:roles.manage')
            ->name('company.roles.store');

        Route::get(
            '/{role}/permissions',
            'Company\CompanyRoleController@editPermissions'
        )
            ->middleware('company.permission:roles.manage')
            ->name('company.roles.permissions.edit');

        Route::put(
            '/{role}/permissions',
            'Company\CompanyRoleController@updatePermissions'
        )
            ->middleware('company.permission:roles.manage')
            ->name('company.roles.permissions.update');

        Route::post(
            '/{role}/toggle-status',
            'Company\CompanyRoleController@toggleStatus'
        )
            ->middleware('company.permission:roles.manage')
            ->name('company.roles.toggle-status');
    });

});

       
});