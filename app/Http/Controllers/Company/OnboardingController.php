<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\EmployeeOnboarding;
use Illuminate\Http\Request;
use App\Models\Company\CompanyUser;
use App\Models\Company\OnboardingSection;
use App\Models\Company\ContractTemplate;
use App\Models\Company\EmployeeContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeOnboarding::query()
            ->with([
                'employee',
                'assignedUser',
                'sections',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('employee', function ($employeeQuery) use ($search) {
                $employeeQuery
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $onboardings = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'in_progress' THEN 1
                    WHEN status = 'pending' THEN 2
                    WHEN status = 'completed' THEN 3
                    WHEN status = 'cancelled' THEN 4
                    ELSE 5
                END
            ")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => EmployeeOnboarding::count(),

            'in_progress' => EmployeeOnboarding::where(
                'status',
                'in_progress'
            )->count(),

            'completed' => EmployeeOnboarding::where(
                'status',
                'completed'
            )->count(),

            'pending_sections' => EmployeeOnboarding::whereHas(
                'sections',
                function ($query) {
                    $query->whereIn('status', [
                        'pending',
                        'in_progress',
                    ]);
                }
            )->count(),
        ];

        return view(
            'company.hr.onboarding.index',
            compact(
                'onboardings',
                'stats'
            )
        );
    }
    public function show(EmployeeOnboarding $onboarding)
{
    $onboarding->load([
        'employee',
        'assignedUser',
        'createdByUser',
        'sections.completedByUser',
    ]);

    return view(
        'company.hr.onboarding.show',
        compact('onboarding')
    );
}
public function jobManager(
    EmployeeOnboarding $onboarding
) {
    $onboarding->load([
        'employee',
        'jobDetails.manager',
        'sections',
    ]);

    $section = $onboarding->sections
        ->firstWhere(
            'section_key',
            'job_manager_confirmation'
        );

    abort_unless($section, 404);

    $managers = CompanyUser::query()
    ->orderBy('first_name')
    ->orderBy('last_name')
    ->get();
    return view(
        'company.hr.onboarding.job-manager',
        compact(
            'onboarding',
            'section',
            'managers'
        )
    );
}

public function updateJobManager(
    Request $request,
    EmployeeOnboarding $onboarding
) {
    $validated = $request->validate([
        'job_title' => [
            'required',
            'string',
            'max:255',
        ],

        'department' => [
            'nullable',
            'string',
            'max:255',
        ],

        'manager_id' => [
            'nullable',
            'exists:company_users,id',
        ],

        'start_date' => [
            'required',
            'date',
        ],

        'employment_type' => [
            'required',
            'in:full_time,part_time,temporary,contractor,apprentice,zero_hours',
        ],

        'work_location' => [
            'nullable',
            'string',
            'max:255',
        ],

        'weekly_hours' => [
            'nullable',
            'numeric',
            'min:0',
            'max:168',
        ],

        'pay_type' => [
            'required',
            'in:salary,hourly',
        ],

        'salary' => [
            'nullable',
            'numeric',
            'min:0',
            'required_if:pay_type,salary',
        ],

        'hourly_rate' => [
            'nullable',
            'numeric',
            'min:0',
            'required_if:pay_type,hourly',
        ],

        'holiday_allowance' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'notice_period' => [
            'nullable',
            'string',
            'max:255',
        ],

        'probation_required' => [
            'required',
            'boolean',
        ],

        'probation_months' => [
            'nullable',
            'integer',
            'min:1',
            'max:24',
            'required_if:probation_required,1',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    if ($validated['pay_type'] === 'salary') {
        $validated['hourly_rate'] = null;
    }

    if ($validated['pay_type'] === 'hourly') {
        $validated['salary'] = null;
    }

    if (!$validated['probation_required']) {
        $validated['probation_months'] = null;
    }

    $validated['confirmed_by'] =
        auth('company')->id();

    $validated['confirmed_at'] = now();

    $onboarding->jobDetails()->updateOrCreate(
        [
            'employee_onboarding_id' => $onboarding->id,
        ],
        $validated
    );

    $jobSection = $onboarding->sections()
        ->where(
            'section_key',
            'job_manager_confirmation'
        )
        ->firstOrFail();

    $jobSection->update([
        'status' => 'completed',
        'completed_by' => auth('company')->id(),
        'completed_at' => now(),
    ]);

    if ($validated['probation_required']) {
        $onboarding->sections()->firstOrCreate(
            [
                'section_key' => 'probation_setup',
            ],
            [
                'title' => 'Probation Setup',
                'status' => 'pending',
                'is_required' => true,
                'sort_order' => 6,
            ]
        );
    } else {
        $onboarding->sections()
            ->where('section_key', 'probation_setup')
            ->where('status', 'pending')
            ->delete();
    }

    $onboarding->recalculateProgress();

    return redirect()
        ->route(
            'company.hr.onboarding.show',
            $onboarding
        )
        ->with(
            'success',
            'Job and manager details confirmed successfully.'
        );
}
public function personalDetails(
    EmployeeOnboarding $onboarding
) {
    $onboarding->load([
        'employee',
        'personalDetails',
        'sections',
    ]);

    $section = $onboarding->sections
        ->firstWhere(
            'section_key',
            'personal_bank_details'
        );

    abort_unless($section, 404);

    return view(
        'company.hr.onboarding.personal-details',
        compact(
            'onboarding',
            'section'
        )
    );
}

public function updatePersonalDetails(
    Request $request,
    EmployeeOnboarding $onboarding
) {
    $validated = $request->validate([
        'full_name' => [
            'required',
            'string',
            'max:255',
        ],

        'date_of_birth' => [
            'nullable',
            'date',
        ],

        'personal_email' => [
            'nullable',
            'email',
            'max:255',
        ],

        'phone' => [
            'nullable',
            'string',
            'max:50',
        ],

        'home_address' => [
            'nullable',
            'string',
            'max:2000',
        ],

        'emergency_contact_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'emergency_contact_relationship' => [
            'nullable',
            'string',
            'max:255',
        ],

        'emergency_contact_phone' => [
            'nullable',
            'string',
            'max:50',
        ],

        'national_insurance_number' => [
            'nullable',
            'string',
            'max:50',
        ],

        'bank_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'account_holder_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'sort_code' => [
            'nullable',
            'string',
            'max:20',
        ],

        'account_number' => [
            'nullable',
            'string',
            'max:30',
        ],

        'details_verified' => [
            'required',
            'boolean',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    if ($validated['details_verified']) {
        $validated['verified_by'] =
            auth('company')->id();

        $validated['verified_at'] = now();
    } else {
        $validated['verified_by'] = null;
        $validated['verified_at'] = null;
    }

    $onboarding->personalDetails()->updateOrCreate(
        [
            'employee_onboarding_id' => $onboarding->id,
        ],
        $validated
    );

    $section = $onboarding->sections()
        ->where(
            'section_key',
            'personal_bank_details'
        )
        ->firstOrFail();

    $section->update([
        'status' => $validated['details_verified']
            ? 'completed'
            : 'in_progress',

        'completed_by' => $validated['details_verified']
            ? auth('company')->id()
            : null,

        'completed_at' => $validated['details_verified']
            ? now()
            : null,
    ]);

    $onboarding->recalculateProgress();

    return redirect()
        ->route(
            'company.hr.onboarding.show',
            $onboarding
        )
        ->with(
            'success',
            'Personal and bank details saved successfully.'
        );
}
public function induction(
    EmployeeOnboarding $onboarding
) {
    $onboarding->load([
        'employee',
        'induction.manager',
        'sections',
    ]);

    $section = $onboarding->sections
        ->firstWhere(
            'section_key',
            'induction_meeting'
        );

    abort_unless($section, 404);

    $managers = CompanyUser::query()
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

    return view(
        'company.hr.onboarding.induction',
        compact(
            'onboarding',
            'section',
            'managers'
        )
    );
}

public function updateInduction(
    Request $request,
    EmployeeOnboarding $onboarding
) {
    $validated = $request->validate([
        'meeting_date' => [
            'required',
            'date',
        ],

        'meeting_time' => [
            'nullable',
            'date_format:H:i',
        ],

        'location' => [
            'nullable',
            'string',
            'max:255',
        ],

        'manager_id' => [
            'nullable',
            'exists:company_users,id',
        ],

        'topics_discussed' => [
            'nullable',
            'string',
            'max:5000',
        ],

        'meeting_notes' => [
            'nullable',
            'string',
            'max:5000',
        ],

        'employee_confirmed' => [
            'required',
            'boolean',
        ],

        'meeting_completed' => [
            'required',
            'boolean',
        ],
    ]);

    if ($validated['meeting_completed']) {
        $validated['completed_by'] =
            auth('company')->id();

        $validated['completed_at'] = now();
    } else {
        $validated['completed_by'] = null;
        $validated['completed_at'] = null;
    }

    $onboarding->induction()->updateOrCreate(
        [
            'employee_onboarding_id' => $onboarding->id,
        ],
        $validated
    );

    $section = $onboarding->sections()
        ->where(
            'section_key',
            'induction_meeting'
        )
        ->firstOrFail();

    $sectionCompleted =
        $validated['meeting_completed'] &&
        $validated['employee_confirmed'];

    $section->update([
        'status' => $sectionCompleted
            ? 'completed'
            : 'in_progress',

        'completed_by' => $sectionCompleted
            ? auth('company')->id()
            : null,

        'completed_at' => $sectionCompleted
            ? now()
            : null,
    ]);

    $onboarding->recalculateProgress();

    return redirect()
        ->route(
            'company.hr.onboarding.show',
            $onboarding
        )
        ->with(
            'success',
            $sectionCompleted
                ? 'Induction meeting completed successfully.'
                : 'Induction meeting details saved successfully.'
        );
}
public function payroll(
    EmployeeOnboarding $onboarding
) {
    $onboarding->load([
        'employee',
        'payroll',
        'personalDetails',
        'sections',
    ]);

    $section = $onboarding->sections
        ->firstWhere(
            'section_key',
            'payroll_setup'
        );

    abort_unless($section, 404);

    return view(
        'company.hr.onboarding.payroll',
        compact(
            'onboarding',
            'section'
        )
    );
}

public function updatePayroll(
    Request $request,
    EmployeeOnboarding $onboarding
) {
    $validated = $request->validate([
        'payroll_number' => [
            'nullable',
            'string',
            'max:100',
        ],

        'pay_type' => [
            'required',
            'in:salary,hourly',
        ],

        'pay_rate' => [
            'required',
            'numeric',
            'min:0',
        ],

        'pay_frequency' => [
            'required',
            'in:weekly,fortnightly,four_weekly,monthly',
        ],

        'first_payment_date' => [
            'nullable',
            'date',
        ],

        'bank_details_verified' => [
            'required',
            'boolean',
        ],

        'tax_information_received' => [
            'required',
            'boolean',
        ],

        'pension_status' => [
            'nullable',
            'in:pending,enrolled,opted_out,not_eligible',
        ],

        'payroll_setup_completed' => [
            'required',
            'boolean',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    $setupCompleted =
        (bool) $validated['payroll_setup_completed'];

    if ($setupCompleted) {
        $validated['completed_by'] =
            auth('company')->id();

        $validated['completed_at'] = now();
    } else {
        $validated['completed_by'] = null;
        $validated['completed_at'] = null;
    }

    $onboarding->payroll()->updateOrCreate(
        [
            'employee_onboarding_id' =>
                $onboarding->id,
        ],
        $validated
    );

    $section = $onboarding->sections()
        ->where(
            'section_key',
            'payroll_setup'
        )
        ->firstOrFail();

    $section->update([
        'status' => $setupCompleted
            ? 'completed'
            : 'in_progress',

        'completed_by' => $setupCompleted
            ? auth('company')->id()
            : null,

        'completed_at' => $setupCompleted
            ? now()
            : null,
    ]);

    $onboarding->recalculateProgress();

    return redirect()
        ->route(
            'company.hr.onboarding.show',
            $onboarding
        )
        ->with(
            'success',
            $setupCompleted
                ? 'Payroll setup completed successfully.'
                : 'Payroll details saved successfully.'
        );
}
public function contract(
    EmployeeOnboarding $onboarding
) {
    $onboarding->load([
        'employee',
        'jobDetails.manager',
        'personalDetails',
        'contracts.template',
        'sections',
    ]);

    $section = $onboarding->sections
        ->firstWhere(
            'section_key',
            'employee_contract'
        );

    abort_unless($section, 404);

    $employmentType =
        optional($onboarding->jobDetails)
            ->employment_type
        ?? optional($onboarding->employee)
            ->employment_type;

    $templates = ContractTemplate::query()
        ->where('is_active', true)
        ->where(function ($query) use ($employmentType) {
            $query
                ->whereNull('employment_type');

            if ($employmentType) {
                $query->orWhere(
                    'employment_type',
                    $employmentType
                );
            }
        })
        ->orderByDesc('is_default')
        ->orderBy('name')
        ->get();

    $contract = $onboarding->latestContract()
        ->with('template')
        ->first();

    $selectedTemplate = null;

    if ($contract) {
        $selectedTemplate = $contract->template;
    } else {
        $selectedTemplate = $templates
            ->firstWhere('is_default', true)
            ?? $templates->first();
    }

    $previewContent = null;

    if ($selectedTemplate) {
        $previewContent = $this->replaceContractPlaceholders(
            $selectedTemplate->content,
            $onboarding
        );
    }

    return view(
        'company.hr.onboarding.contract',
        compact(
            'onboarding',
            'section',
            'templates',
            'contract',
            'selectedTemplate',
            'previewContent'
        )
    );
}
public function previewContract(
    Request $request,
    EmployeeOnboarding $onboarding
) {
    $validated = $request->validate([
        'contract_template_id' => [
            'required',
            'exists:contract_templates,id',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    $onboarding->load([
        'employee',
        'jobDetails.manager',
        'personalDetails',
    ]);

    $template = ContractTemplate::query()
        ->where('is_active', true)
        ->findOrFail(
            $validated['contract_template_id']
        );

    $previewContent = $this->replaceContractPlaceholders(
        $template->content,
        $onboarding
    );

    $contract = $onboarding->latestContract()
        ->first();

    return view(
        'company.hr.onboarding.contract',
        [
            'onboarding' => $onboarding,
            'section' => $onboarding->sections()
                ->where(
                    'section_key',
                    'employee_contract'
                )
                ->firstOrFail(),

            'templates' => ContractTemplate::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->get(),

            'contract' => $contract,
            'selectedTemplate' => $template,
            'previewContent' => $previewContent,
            'draftNotes' =>
                $validated['notes'] ?? null,
        ]
    );
}
public function saveContractDraft(
    Request $request,
    EmployeeOnboarding $onboarding
) {
    $validated = $request->validate([
        'contract_template_id' => [
            'required',
            'exists:contract_templates,id',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:5000',
        ],
    ]);

    $onboarding->load([
        'employee',
        'jobDetails.manager',
        'personalDetails',
    ]);

    $template = ContractTemplate::query()
        ->where('is_active', true)
        ->findOrFail(
            $validated['contract_template_id']
        );

    $generatedContent =
        $this->replaceContractPlaceholders(
            $template->content,
            $onboarding
        );

    $latestContract = $onboarding
        ->latestContract()
        ->first();

    if (
        $latestContract &&
        $latestContract->status === 'draft'
    ) {
        $latestContract->update([
            'contract_template_id' =>
                $template->id,

            'generated_content' =>
                $generatedContent,

            'notes' =>
                $validated['notes'] ?? null,
        ]);

        $contract = $latestContract;
    } else {
        $nextVersion = $onboarding
            ->contracts()
            ->max('version');

        $contract = EmployeeContract::create([
            'employee_onboarding_id' =>
                $onboarding->id,

            'employee_id' =>
                $onboarding->employee_id,

            'contract_template_id' =>
                $template->id,

            'version' =>
                $nextVersion
                    ? $nextVersion + 1
                    : 1,

            'status' => 'draft',

            'generated_content' =>
                $generatedContent,

            'notes' =>
                $validated['notes'] ?? null,
        ]);
    }

    $section = $onboarding->sections()
        ->where(
            'section_key',
            'employee_contract'
        )
        ->firstOrFail();

    if ($section->status === 'pending') {
        $section->update([
            'status' => 'in_progress',
        ]);
    }

    return redirect()
        ->route(
            'company.hr.onboarding.contract',
            $onboarding
        )
        ->with(
            'success',
            'Contract draft saved successfully.'
        );
}
private function replaceContractPlaceholders(
    string $content,
    EmployeeOnboarding $onboarding
): string {
    $employee = $onboarding->employee;
    $jobDetails = $onboarding->jobDetails;
    $personalDetails = $onboarding->personalDetails;

    $managerName = '';

    if ($jobDetails && $jobDetails->manager) {
        $managerName = trim(
            $jobDetails->manager->first_name
            . ' '
            . $jobDetails->manager->last_name
        );
    }

    $employeeFullName = trim(
        ($employee->first_name ?? '')
        . ' '
        . ($employee->last_name ?? '')
    );

    $employeeAddress =
        optional($personalDetails)->home_address
        ?? $employee->address
        ?? '';

    $employmentType =
        optional($jobDetails)->employment_type
        ?? $employee->employment_type
        ?? '';

    $payType =
        optional($jobDetails)->pay_type
        ?? $employee->pay_type
        ?? '';

    $payRate = '';

    if ($jobDetails) {
        $payRate =
            $jobDetails->pay_type === 'hourly'
                ? $jobDetails->hourly_rate
                : $jobDetails->salary;
    }

    if ($payRate === null || $payRate === '') {
        $payRate = $employee->pay_rate ?? '';
    }

    $replacements = [
        '{{employee_full_name}}' =>
            $employeeFullName,

        '{{employee_number}}' =>
            $employee->employee_number ?? '',

        '{{employee_address}}' =>
            $employeeAddress,

        '{{job_title}}' =>
            optional($jobDetails)->job_title
            ?? $employee->job_title
            ?? '',

        '{{department}}' =>
            optional($jobDetails)->department
            ?? $employee->department
            ?? '',

        '{{employment_type}}' =>
            ucwords(
                str_replace(
                    '_',
                    ' ',
                    $employmentType
                )
            ),

        '{{start_date}}' =>
            optional(
                optional($jobDetails)->start_date
                ?? $employee->start_date
            )->format('d M Y') ?? '',

        '{{work_location}}' =>
            optional($jobDetails)->work_location
            ?? $employee->work_location
            ?? '',

        '{{weekly_hours}}' =>
            optional($jobDetails)->weekly_hours
            ?? $employee->normal_weekly_hours
            ?? '',

        '{{pay_type}}' =>
            ucwords(
                str_replace(
                    '_',
                    ' ',
                    $payType
                )
            ),

        '{{pay_rate}}' =>
            $payRate !== ''
                ? number_format(
                    (float) $payRate,
                    2
                )
                : '',

        '{{holiday_allowance}}' =>
            optional($jobDetails)
                ->holiday_allowance
            ?? '',

        '{{notice_period}}' =>
            optional($jobDetails)
                ->notice_period
            ?? '',

        '{{manager_name}}' =>
            $managerName,

        '{{company_name}}' =>
            'Yes Trades Ltd',

        '{{company_address}}' =>
            config(
                'company.address',
                ''
            ),

        '{{contract_date}}' =>
            now()->format('d M Y'),
    ];

    return str_replace(
        array_keys($replacements),
        array_values($replacements),
        $content
    );
}
public function generateContractPdf(
    EmployeeOnboarding $onboarding
) {
    $onboarding->load([
        'employee',
        'jobDetails.manager',
        'personalDetails',
    ]);

    $contract = $onboarding->latestContract()
        ->with('template')
        ->firstOrFail();

    abort_unless(
        $contract->status === 'draft',
        422,
        'Only a draft contract can be generated.'
    );

    $content = $contract->generated_content;

    if (!$content) {
        $content = $this->replaceContractPlaceholders(
            $contract->template->content,
            $onboarding
        );
    }

    $employee = $onboarding->employee;

    $safeEmployeeName = Str::slug(
        trim(
            ($employee->first_name ?? '')
            . '-'
            . ($employee->last_name ?? '')
        )
    );

    $filename =
        'employment-contract-'
        . $safeEmployeeName
        . '-v'
        . $contract->version
        . '.pdf';

    $directory =
        'company-private/contracts/'
        . $employee->id;

    $filePath =
        $directory . '/' . $filename;

    $pdf = Pdf::loadView(
        'company.hr.onboarding.contract-pdf',
        [
            'content' => $content,
            'employee' => $employee,
            'contract' => $contract,
        ]
    )->setPaper('a4');

    Storage::disk('local')->put(
        $filePath,
        $pdf->output()
    );

    $contract->update([
        'status' => 'generated',
        'generated_content' => $content,
        'generated_file_path' => $filePath,
        'generated_filename' => $filename,
        'generated_at' => now(),
        'generated_by' => auth('company')->id(),
    ]);

    $section = $onboarding->sections()
        ->where(
            'section_key',
            'employee_contract'
        )
        ->firstOrFail();

    if ($section->status === 'pending') {
        $section->update([
            'status' => 'in_progress',
        ]);
    }

    return redirect()
        ->route(
            'company.hr.onboarding.contract',
            $onboarding
        )
        ->with(
            'success',
            'Contract PDF generated successfully.'
        );
}
public function downloadGeneratedContract(
    EmployeeOnboarding $onboarding,
    EmployeeContract $contract
) {
    abort_unless(
        $contract->employee_onboarding_id ===
            $onboarding->id,
        404
    );

    abort_unless(
        $contract->generated_file_path &&
        Storage::disk('local')->exists(
            $contract->generated_file_path
        ),
        404,
        'Generated contract file was not found.'
    );

    return Storage::disk('local')->download(
        $contract->generated_file_path,
        $contract->generated_filename
            ?: 'employment-contract.pdf',
        [
            'Content-Type' => 'application/pdf',
        ]
    );
}
public function uploadSignedContract(
    Request $request,
    EmployeeOnboarding $onboarding,
    EmployeeContract $contract
) {
    abort_unless(
        $contract->employee_onboarding_id === $onboarding->id,
        404
    );

    $validated = $request->validate([
        'signed_contract' => [
            'required',
            'file',
            'mimes:pdf',
            'max:10240',
        ],

        'signed_notes' => [
            'nullable',
            'string',
            'max:2000',
        ],
    ]);

    $file = $request->file('signed_contract');

    $employee = $onboarding->employee;

    $safeEmployeeName = Str::slug(
        trim(
            ($employee->first_name ?? '')
            . '-'
            . ($employee->last_name ?? '')
        )
    );

    $filename =
        'signed-employment-contract-'
        . $safeEmployeeName
        . '-v'
        . $contract->version
        . '.pdf';

    $directory =
        'company-private/contracts/'
        . $employee->id
        . '/signed';

    $filePath = $file->storeAs(
        $directory,
        $filename,
        'local'
    );

    if (
        $contract->signed_file_path &&
        Storage::disk('local')->exists(
            $contract->signed_file_path
        )
    ) {
        Storage::disk('local')->delete(
            $contract->signed_file_path
        );
    }

    $contract->update([
        'status' => 'signed',
        'signed_file_path' => $filePath,
        'signed_filename' => $filename,
        'signed_at' => now(),
        'signed_uploaded_by' => auth('company')->id(),
        'notes' => $validated['signed_notes']
            ?? $contract->notes,
    ]);

    $section = $onboarding->sections()
        ->where(
            'section_key',
            'employee_contract'
        )
        ->firstOrFail();

    $section->update([
        'status' => 'completed',
        'completed_by' => auth('company')->id(),
        'completed_at' => now(),
    ]);

    $onboarding->recalculateProgress();

    if ($onboarding->progress_percentage === 100) {
    $onboarding->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    if ($onboarding->employee) {
        $onboarding->employee->update([
            'employment_status' => 'active',
        ]);
    }
}

    return redirect()
        ->route(
            'company.hr.onboarding.contract',
            $onboarding
        )
        ->with(
            'success',
            'Signed contract uploaded successfully.'
        );
}
public function downloadSignedContract(
    EmployeeOnboarding $onboarding,
    EmployeeContract $contract
) {
    abort_unless(
        $contract->employee_onboarding_id === $onboarding->id,
        404
    );

    abort_unless(
        $contract->signed_file_path &&
        Storage::disk('local')->exists(
            $contract->signed_file_path
        ),
        404,
        'Signed contract file was not found.'
    );

    return Storage::disk('local')->download(
        $contract->signed_file_path,
        $contract->signed_filename
            ?: 'signed-employment-contract.pdf',
        [
            'Content-Type' => 'application/pdf',
        ]
    );
}
}