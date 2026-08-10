@extends('company.layouts.app')

@section('title', $employee->full_name)

@section('content')@php
    $summary = $summary ?? [
        'missing_documents' =>
            $employee->missingDocuments()->count(),

        'expiring_documents' =>
            $employee->expiringDocuments()->count(),

        'expired_documents' =>
            $employee->expiredDocuments()->count(),

        'approved_documents' =>
            $employee->documents()
                ->whereNotNull('file_path')
                ->where('review_status', 'approved')
                ->count(),

        'right_to_work_status' =>
            $employee->documents()
                ->whereHas('documentType', function ($query) {
                    $query->where(
                        'slug',
                        'right-to-work-evidence'
                    );
                })
                ->whereNotNull('file_path')
                ->where('review_status', 'approved')
                ->exists()
                    ? 'Verified'
                    : 'Not Verified',
       ];

    $bankDetails = \App\Models\Company\EmployeeOnboardingPersonalDetail::query()
        ->with('verifiedByUser')
        ->whereHas('onboarding', function ($query) use ($employee) {
            $query->where('employee_id', $employee->id);
        })
        ->first();
@endphp
<style>
    .employee-profile-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .employee-profile-identity {
        display: flex;
        align-items: center;
        gap: 17px;
    }

    .employee-profile-photo {
        display: flex;
        width: 78px;
        height: 78px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 50%;
        background: #2878dc;
        color: #ffffff;
        font-size: 25px;
        font-weight: 700;
    }

    .employee-profile-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .employee-profile-heading h1 {
        margin: 0 0 5px;
        font-size: 28px;
    }

    .employee-profile-heading p {
        margin: 3px 0;
        color: #aab4ca;
    }

    .employee-profile-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .employee-profile-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 16px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .employee-profile-button-secondary {
        background: #3a4967;
    }

    .employee-status {
        display: inline-block;
        margin-top: 6px;
        padding: 6px 11px;
        border-radius: 15px;
        background: rgba(46, 191, 114, 0.16);
        color: #59dc96;
        font-size: 12px;
        text-transform: capitalize;
    }

    .employee-summary-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .employee-summary-card {
        padding: 19px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .employee-summary-label {
        margin-bottom: 10px;
        color: #aab4ca;
        font-size: 13px;
    }

    .employee-summary-value {
        color: #ffffff;
        font-size: 25px;
        font-weight: 700;
    }

    .employee-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .employee-tab {
        display: inline-flex;
        align-items: center;
        min-height: 41px;
        padding: 0 15px;
        border-radius: 7px;
        background: #1e2a44;
        color: #aab4ca;
        text-decoration: none;
        white-space: nowrap;
    }

    .employee-tab.active {
        background: #2878dc;
        color: #ffffff;
    }

    .employee-profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .employee-profile-card {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .employee-profile-card h2 {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 19px;
    }

    .employee-detail-row {
        display: grid;
        grid-template-columns: 170px 1fr;
        gap: 14px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .employee-detail-row:last-child {
        border-bottom: 0;
    }

    .employee-detail-label {
        color: #aab4ca;
        font-size: 13px;
    }

    .employee-detail-value {
        color: #ffffff;
        word-break: break-word;
    }

    @media (max-width: 1200px) {
        .employee-summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .employee-profile-header {
            flex-direction: column;
        }

        .employee-profile-grid {
            grid-template-columns: 1fr;
        }

        .employee-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 550px) {
        .employee-profile-identity {
            align-items: flex-start;
        }

        .employee-summary-grid {
            grid-template-columns: 1fr;
        }

        .employee-detail-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }
</style>

<div class="employee-profile-header">

    <div class="employee-profile-identity">

        <div class="employee-profile-photo">
            @if ($employee->profile_photo)
                <img
                    src="{{ asset('storage/' . $employee->profile_photo) }}"
                    alt="{{ $employee->full_name }}"
                >
            @else
                {{ strtoupper(
                    substr($employee->first_name, 0, 1) .
                    substr($employee->last_name, 0, 1)
                ) }}
            @endif
        </div>

        <div class="employee-profile-heading">
            <h1>{{ $employee->full_name }}</h1>

            <p>
                {{ $employee->job_title ?: 'No job title' }}
                @if ($employee->department)
                    · {{ $employee->department }}
                @endif
            </p>

            <p>
                Employee ID: {{ $employee->employee_number }}
            </p>

            <span class="employee-status">
                {{ ucwords(str_replace(
                    '_',
                    ' ',
                    $employee->employment_status
                )) }}
            </span>

            <span class="employee-status" style="
                margin-left: 8px;
                background: {{ $employee->activation_status === 'activated' ? 'rgba(46,191,114,0.16)' : ($employee->activation_status === 'link_expired' ? 'rgba(255,99,99,0.16)' : ($employee->activation_status === 'activation_sent' ? 'rgba(255,179,71,0.16)' : 'rgba(170,180,202,0.14)')) }};
                color: {{ $employee->activation_status === 'activated' ? '#59dc96' : ($employee->activation_status === 'link_expired' ? '#ff8a92' : ($employee->activation_status === 'activation_sent' ? '#ffc46f' : '#c4ccdc')) }};
            ">
                App account: {{ $employee->activation_status_label }}
            </span>

            @if ($employee->isFieldEngineer())
                <span class="employee-status" style="
                    margin-left: 8px;
                    background: {{ $employee->allow_manual_attendance ? 'rgba(46,191,114,0.16)' : 'rgba(170,180,202,0.14)' }};
                    color: {{ $employee->allow_manual_attendance ? '#59dc96' : '#c4ccdc' }};
                ">
                    Field engineer &middot; Manual clock-in {{ $employee->allow_manual_attendance ? 'allowed' : 'off (jobs only)' }}
                </span>
            @endif
        </div>

    </div>

    <div class="employee-profile-actions">

        @if (auth('company')->user()->hasPermission('employees.edit'))
            <a href="{{ route(
               'company.hr.employees.edit',
                $employee
                ) }}"
              class="employee-profile-button">
                <i class="fas fa-edit"></i>
                Edit Employee
            </a>

            @if ($employee->activation_status !== 'activated')
                <form method="POST" action="{{ route('company.hr.employees.send-activation', $employee) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="employee-profile-button employee-profile-button-secondary">
                        <i class="fas fa-paper-plane"></i>
                        {{ in_array($employee->activation_status, ['activation_sent', 'link_expired']) ? 'Resend Activation Link' : 'Send Activation Link' }}
                    </button>
                </form>
            @endif
        @endif

        <a href="{{ route('company.hr.employees.index') }}"
           class="employee-profile-button employee-profile-button-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Employees
        </a>

    </div>

</div>

<div class="employee-summary-grid">

    <div class="employee-summary-card">
        <div class="employee-summary-label">
            Missing Documents
        </div>

        <div class="employee-summary-value">
            {{ $summary['missing_documents'] }}
        </div>
    </div>

    <div class="employee-summary-card">
        <div class="employee-summary-label">
            Expiring Documents
        </div>

        <div class="employee-summary-value">
            {{ $summary['expiring_documents'] }}
        </div>
    </div>

    <div class="employee-summary-card">
        <div class="employee-summary-label">
            Expired Documents
        </div>

        <div class="employee-summary-value">
            {{ $summary['expired_documents'] }}
        </div>
    </div>

    <div class="employee-summary-card">
        <div class="employee-summary-label">
            Right to Work
        </div>

        <div style="font-size:17px; font-weight:600;">
            {{ $summary['right_to_work_status'] }}
        </div>
    </div>

    <div class="employee-summary-card">
        <div class="employee-summary-label">
            Training Due
        </div>

        <div class="employee-summary-value">
            {{ $summary['training_due'] }}
        </div>
    </div>

</div>

<div class="employee-tabs">

    <a href="#overview"
       class="employee-tab active">
        Overview
    </a>



   @if (
    auth('company')->user()
        ->hasPermission('employee-documents.view')
)
    <a href="{{ route(
            'company.hr.employees.documents.index',
            $employee
        ) }}"
       class="employee-tab">
        Documents
    </a>
@endif
@if (
    auth('company')->user()
        ->hasPermission('payslips.view')
)
    <a href="{{ route(
            'company.hr.payslips.history',
            $employee
        ) }}"
       class="employee-tab">

        View Payslips
    </a>
@endif
@if (
    $employee->is_sponsored_worker
    && $employee->sponsoredWorkerRecord
)
    <a
        href="{{ route(
            'company.hr.sponsor-compliance.sponsored-workers.show',
            $employee->sponsoredWorkerRecord
        ) }}"
        class="employee-tab"
    >
        Sponsor Compliance
    </a>
@endif
</div>

<div class="employee-profile-grid">

    <div class="employee-profile-card">
        <h2>Personal and Contact Details</h2>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Full name</div>
            <div class="employee-detail-value">
                {{ $employee->full_name }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Date of birth</div>
            <div class="employee-detail-value">
                {{ $employee->date_of_birth
                    ? $employee->date_of_birth->format('d M Y')
                    : 'Not provided'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Gender</div>
            <div class="employee-detail-value">
                {{ $employee->gender
                    ? ucwords(str_replace('_', ' ', $employee->gender))
                    : 'Not provided'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Personal email</div>
            <div class="employee-detail-value">
                {{ $employee->personal_email ?: 'Not provided' }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Work email</div>
            <div class="employee-detail-value">
                {{ $employee->work_email ?: 'Not provided' }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Phone</div>
            <div class="employee-detail-value">
                {{ $employee->phone ?: 'Not provided' }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">
                National Insurance
            </div>

            <div class="employee-detail-value">
                {{ $employee->national_insurance_number
                    ?: 'Not provided'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Address</div>

            <div class="employee-detail-value">
                @php
                    $address = array_filter([
                        $employee->address_line_1,
                        $employee->address_line_2,
                        $employee->city,
                        $employee->county,
                        $employee->postcode,
                        $employee->country,
                    ]);
                @endphp

                {{ count($address)
                    ? implode(', ', $address)
                    : 'Not provided'
                }}
            </div>
        </div>

    </div>

    <div class="employee-profile-card">
        <h2>Employment Details</h2>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Job title</div>
            <div class="employee-detail-value">
                {{ $employee->job_title ?: 'Not assigned' }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Department</div>
            <div class="employee-detail-value">
                {{ $employee->department ?: 'Not assigned' }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Manager</div>
            <div class="employee-detail-value">
                {{ optional($employee->manager)->full_name
                    ?? 'No manager assigned'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">
                Employment type
            </div>

            <div class="employee-detail-value">
                {{ $employee->employment_type
                    ? ucwords(str_replace(
                        '_',
                        ' ',
                        $employee->employment_type
                    ))
                    : 'Not assigned'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Start date</div>
            <div class="employee-detail-value">
                {{ $employee->start_date
                    ? $employee->start_date->format('d M Y')
                    : 'Not set'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">
                Probation end date
            </div>

            <div class="employee-detail-value">
                {{ $employee->probation_end_date
                    ? $employee->probation_end_date->format('d M Y')
                    : 'Not set'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Work location</div>
            <div class="employee-detail-value">
                {{ $employee->work_location ?: 'Not set' }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Weekly hours</div>
            <div class="employee-detail-value">
                {{ $employee->normal_weekly_hours !== null
                    ? $employee->normal_weekly_hours . ' hours'
                    : 'Not set'
                }}
            </div>
        </div>

        <div class="employee-detail-row">
            <div class="employee-detail-label">Payroll number</div>
            <div class="employee-detail-value">
                {{ $employee->payroll_number ?: 'Not set' }}
            </div>
        </div>

    </div>

   <div class="employee-profile-card">
    <h2>Emergency Contact</h2>

    <div class="employee-detail-row">
        <div class="employee-detail-label">Name</div>
        <div class="employee-detail-value">
            {{ $employee->emergency_contact_name
                ?: 'Not provided'
            }}
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">Relationship</div>
        <div class="employee-detail-value">
            {{ $employee->emergency_contact_relationship
                ?: 'Not provided'
            }}
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">Phone</div>
        <div class="employee-detail-value">
            {{ $employee->emergency_contact_phone
                ?: 'Not provided'
            }}
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">Address</div>
        <div class="employee-detail-value">
            {{ $employee->emergency_contact_address
                ?: 'Not provided'
            }}
        </div>
    </div>

</div>

<div class="employee-profile-card">
    <h2>Bank Details</h2>

    <div class="employee-detail-row">
        <div class="employee-detail-label">
            Account holder name
        </div>

        <div class="employee-detail-value">
            {{ $bankDetails?->account_holder_name
                ?: 'Not provided'
            }}
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">
            Bank name
        </div>

        <div class="employee-detail-value">
            {{ $bankDetails?->bank_name
                ?: 'Not provided'
            }}
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">
            Sort code
        </div>

        <div class="employee-detail-value">
            @if ($bankDetails?->sort_code)
                @php
                    $cleanSortCode = preg_replace(
                        '/\D/',
                        '',
                        $bankDetails->sort_code
                    );
                @endphp

                **-**-{{ substr($cleanSortCode, -2) }}
            @else
                Not provided
            @endif
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">
            Account number
        </div>

        <div class="employee-detail-value">
            @if ($bankDetails?->account_number)
                ****{{ substr(
                    $bankDetails->account_number,
                    -4
                ) }}
            @else
                Not provided
            @endif
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">
            Verification status
        </div>

        <div class="employee-detail-value">
            {{ $bankDetails?->details_verified
                ? 'Verified'
                : 'Not verified'
            }}
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">
            Verified by
        </div>

        <div class="employee-detail-value">
            {{ $bankDetails?->verifiedByUser?->full_name
                ?: 'Not verified'
            }}
        </div>
    </div>

    <div class="employee-detail-row">
        <div class="employee-detail-label">
            Verified date
        </div>

        <div class="employee-detail-value">
            {{ $bankDetails?->verified_at
                ? $bankDetails->verified_at->format('d M Y H:i')
                : 'Not verified'
            }}
        </div>
    </div>

</div>

@if (auth('company')->user()->hasPermission('equipment-vehicles.manage'))
<div class="employee-profile-card">
    <h2>Equipment and Vehicles Assigned</h2>

    @forelse ($employee->currentEquipmentAssignments as $assignment)
        <div class="employee-detail-row">
            <div class="employee-detail-label">
                {{ optional($assignment->equipment)->name ?? 'Unknown item' }}
            </div>

            <div class="employee-detail-value">
                {{ ucfirst(optional($assignment->equipment)->category ?? '') }}
                &middot;
                since {{ $assignment->assigned_at->format('d M Y') }}
                @if (auth('company')->user()->hasPermission('equipment-vehicles.manage'))
                    &middot;
                    <a href="{{ route('company.hr.equipment.show', $assignment->equipment) }}" style="color:#6fa8ee;">Manage</a>
                @endif
            </div>
        </div>
    @empty
        <div class="employee-detail-row">
            <div class="employee-detail-value" style="color:#aab4ca;">
                No equipment or vehicles currently checked out to this employee.
            </div>
        </div>
    @endforelse
</div>
@endif

</div>

@if (
    auth('company')->user()
        ->hasPermission('holidays.view')
)

<style>
    .employee-holiday-section {
        margin-top: 24px;
    }

    .employee-holiday-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .employee-holiday-header h2 {
        margin: 0 0 5px;
        font-size: 21px;
    }

    .employee-holiday-header p {
        margin: 0;
        color: #aab4ca;
    }

    .employee-holiday-add {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .employee-holiday-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .employee-holiday-stat {
        padding: 18px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .employee-holiday-stat-label {
        margin-bottom: 7px;
        color: #aab4ca;
        font-size: 13px;
    }

    .employee-holiday-stat-value {
        font-size: 24px;
        font-weight: 700;
    }

    .employee-holiday-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .employee-holiday-panel h3 {
        margin: 0 0 15px;
        padding-bottom: 11px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 18px;
    }

    .employee-holiday-table-wrapper {
        overflow-x: auto;
    }

    .employee-holiday-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    .employee-holiday-table th,
    .employee-holiday-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .employee-holiday-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .employee-holiday-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .employee-holiday-status-approved {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .employee-holiday-status-pending {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .employee-holiday-status-rejected,
    .employee-holiday-status-cancelled {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .employee-holiday-empty {
        padding: 25px 10px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 900px) {
        .employee-holiday-header {
            flex-direction: column;
        }

        .employee-holiday-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 550px) {
        .employee-holiday-stat-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@if (
    auth('company')->user()
        ->hasPermission('training.view')
)

<style>
    .employee-training-section {
        margin-top: 24px;
    }

    .employee-training-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 18px;
    }

    .employee-training-header h2 {
        margin: 0 0 5px;
        font-size: 21px;
    }

    .employee-training-header p {
        margin: 0;
        color: #aab4ca;
    }

    .employee-training-add {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .employee-training-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .employee-training-stat {
        padding: 18px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .employee-training-stat-label {
        margin-bottom: 7px;
        color: #aab4ca;
        font-size: 13px;
    }

    .employee-training-stat-value {
        font-size: 24px;
        font-weight: 700;
    }

    .employee-training-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .employee-training-panel h3 {
        margin: 0 0 15px;
        padding-bottom: 11px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 18px;
    }

    .employee-training-table-wrapper {
        overflow-x: auto;
    }

    .employee-training-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .employee-training-table th,
    .employee-training-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .employee-training-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .employee-training-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .employee-training-status-not_started {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .employee-training-status-scheduled {
        background: rgba(41,120,220,0.18);
        color: #75aff7;
    }

    .employee-training-status-in_progress {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .employee-training-status-completed {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .employee-training-status-expired {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .employee-training-empty {
        padding: 25px 10px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 900px) {
        .employee-training-header {
            flex-direction: column;
        }

        .employee-training-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 550px) {
        .employee-training-stat-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="employee-training-section">

    <div class="employee-training-header">

        <div>
            <h2>Training</h2>

            <p>
                Assigned, completed and expiring employee training.
            </p>
        </div>

        @if (
            auth('company')->user()
                ->hasPermission('training.create')
        )
            <a href="{{ route(
                    'company.hr.training.records.create',
                    ['employee_id' => $employee->id]
                ) }}"
               class="employee-training-add">
                <i class="fas fa-plus"></i>
                Assign Training
            </a>
        @endif

    </div>

    <div class="employee-training-stat-grid">

        <div class="employee-training-stat">
            <div class="employee-training-stat-label">
                Training Due
            </div>

            <div class="employee-training-stat-value">
                {{ $summary['training_due'] }}
            </div>
        </div>

        <div class="employee-training-stat">
            <div class="employee-training-stat-label">
                Active Training
            </div>

            <div class="employee-training-stat-value">
                {{ $activeTrainingRecords->count() }}
            </div>
        </div>

        <div class="employee-training-stat">
            <div class="employee-training-stat-label">
                Completed
            </div>

            <div class="employee-training-stat-value">
                {{ $completedTrainingRecords->count() }}
            </div>
        </div>

        <div class="employee-training-stat">
            <div class="employee-training-stat-label">
                Expired
            </div>

            <div class="employee-training-stat-value">
                {{ $expiredTrainingRecords->count() }}
            </div>
        </div>

    </div>

    <div class="employee-training-panel">

        <h3>Active Training</h3>

        <div class="employee-training-table-wrapper">

            <table class="employee-training-table">

                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Provider</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($activeTrainingRecords as $record)

                        <tr>
                            <td>
                                {{ optional(
                                    $record->trainingCourse
                                )->name ?? 'Unknown course' }}
                            </td>

                            <td>
                                {{ $record->provider_name ?: '—' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->scheduled_date
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                <span class="
                                    employee-training-status
                                    employee-training-status-{{ $record->status }}
                                ">
                                    {{ $record->status_name }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route(
                                        'company.hr.training.records.show',
                                        $record
                                    ) }}"
                                   style="color:#69a9ff;">
                                    View
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
                                <div class="employee-training-empty">
                                    No active training records.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="employee-training-panel">

        <h3>Expiring Training</h3>

        <div class="employee-training-table-wrapper">

            <table class="employee-training-table">

                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Completed</th>
                        <th>Expiry</th>
                        <th>Certificate</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($expiringTrainingRecords as $record)

                        <tr>
                            <td>
                                {{ optional(
                                    $record->trainingCourse
                                )->name ?? 'Unknown course' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->completed_at
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td style="color:#ffc46d;">
                                {{ optional(
                                    $record->expiry_date
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                {{ $record->certificate_path
                                    ? 'Uploaded'
                                    : 'Not uploaded'
                                }}
                            </td>

                            <td>
                                <a href="{{ route(
                                        'company.hr.training.records.show',
                                        $record
                                    ) }}"
                                   style="color:#69a9ff;">
                                    View
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
                                <div class="employee-training-empty">
                                    No training expires within 30 days.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="employee-training-panel">

        <h3>Completed Training</h3>

        <div class="employee-training-table-wrapper">

            <table class="employee-training-table">

                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Completed</th>
                        <th>Expiry</th>
                        <th>Certificate</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($completedTrainingRecords as $record)

                        <tr>
                            <td>
                                {{ optional(
                                    $record->trainingCourse
                                )->name ?? 'Unknown course' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->completed_at
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->expiry_date
                                )->format('d M Y') ?? 'No expiry' }}
                            </td>

                            <td>
                                {{ $record->certificate_path
                                    ? 'Uploaded'
                                    : 'Not uploaded'
                                }}
                            </td>

                            <td>
                                <a href="{{ route(
                                        'company.hr.training.records.show',
                                        $record
                                    ) }}"
                                   style="color:#69a9ff;">
                                    View
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
                                <div class="employee-training-empty">
                                    No completed training records.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


@endif

<div class="employee-holiday-section">  

    <div class="employee-holiday-header">

        <div>
            <h2>Holidays</h2>

            <p>
                Holiday allowance and leave history for
                {{ $currentYear }}.
            </p>
        </div>

        @if (
            auth('company')->user()
                ->hasPermission('holidays.create')
        )
            <a href="{{ route(
                    'company.hr.holidays.create',
                    ['employee_id' => $employee->id]
                ) }}"
               class="employee-holiday-add">
                <i class="fas fa-plus"></i>
                Add Holiday
            </a>
        @endif

    </div>

    <div class="employee-holiday-stat-grid">

        <div class="employee-holiday-stat">
            <div class="employee-holiday-stat-label">
                Total Allowance
            </div>

            <div class="employee-holiday-stat-value">
                {{ $holidayAllowance
                    ? number_format(
                        $holidayAllowance->total_allowance,
                        2
                    )
                    : 'Not set'
                }}
            </div>
        </div>

        <div class="employee-holiday-stat">
            <div class="employee-holiday-stat-label">
                Used
            </div>

            <div class="employee-holiday-stat-value">
                {{ $holidayAllowance
                    ? number_format(
                        $holidayAllowance->approved_days_used,
                        2
                    )
                    : '0.00'
                }}
            </div>
        </div>

        <div class="employee-holiday-stat">
            <div class="employee-holiday-stat-label">
                Remaining
            </div>

            <div class="employee-holiday-stat-value">
                {{ $holidayAllowance
                    ? number_format(
                        $holidayAllowance->remaining_allowance,
                        2
                    )
                    : '0.00'
                }}
            </div>
        </div>

        <div class="employee-holiday-stat">
            <div class="employee-holiday-stat-label">
                Current Status
            </div>

            <div class="employee-holiday-stat-value">
                {{ $currentHoliday
                    ? 'On Holiday'
                    : 'Working'
                }}
            </div>
        </div>

    </div>

    <div class="employee-holiday-panel">

        <h3>Current Holiday</h3>

        @if ($currentHoliday)

            <div>
                <strong>
                    {{ $currentHoliday->holiday_type_name }}
                </strong>

                <div style="margin-top:6px; color:#aab4ca;">
                    {{ $currentHoliday->start_date->format('d M Y') }}
                    –
                    {{ $currentHoliday->end_date->format('d M Y') }}
                </div>
            </div>

        @else

            <div class="employee-holiday-empty">
                This employee is not currently on holiday.
            </div>

        @endif

    </div>
        <div class="employee-holiday-panel">

            <h3>Pending Holiday Requests</h3>

            <div class="employee-holiday-table-wrapper">

                <table class="employee-holiday-table">

                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($pendingHolidays as $holiday)

                            <tr>
                                <td>
                                    {{ $holiday->holiday_type_name }}
                                </td>

                                <td>
                                    {{ $holiday->start_date->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $holiday->end_date->format('d M Y') }}
                                </td>

                                <td>
                                    {{ number_format(
                                        $holiday->total_days,
                                        2
                                    ) }}
                                </td>

                                <td>
                                    <span class="
                                        employee-holiday-status
                                        employee-holiday-status-pending
                                    ">
                                        Pending
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route(
                                            'company.hr.holidays.show',
                                            $holiday
                                        ) }}"
                                    style="color:#69a9ff;">
                                        View
                                    </a>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6">
                                    <div class="employee-holiday-empty">
                                        No pending holiday requests.
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    <div class="employee-holiday-panel">

        <h3>Upcoming Holidays</h3>

        <div class="employee-holiday-table-wrapper">

            <table class="employee-holiday-table">

                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($upcomingHolidays as $holiday)

                        <tr>
                            <td>
                                {{ $holiday->holiday_type_name }}
                            </td>

                            <td>
                                {{ $holiday->start_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $holiday->end_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ number_format(
                                    $holiday->total_days,
                                    2
                                ) }}
                            </td>

                            <td>
                                <span class="
                                    employee-holiday-status
                                    employee-holiday-status-{{ $holiday->status }}
                                ">
                                    {{ $holiday->status_name }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route(
                                        'company.hr.holidays.show',
                                        $holiday
                                    ) }}"
                                   style="color:#69a9ff;">
                                    View
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6">
                                <div class="employee-holiday-empty">
                                    No upcoming approved holidays.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="employee-holiday-panel">

        <h3>Holiday History</h3>

        <div class="employee-holiday-table-wrapper">

            <table class="employee-holiday-table">

                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($holidayHistory as $holiday)

                        <tr>
                            <td>
                                {{ $holiday->holiday_type_name }}
                            </td>

                            <td>
                                {{ $holiday->start_date->format('d M Y') }}
                                –
                                {{ $holiday->end_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ number_format(
                                    $holiday->total_days,
                                    2
                                ) }}
                            </td>

                            <td>
                                <span class="
                                    employee-holiday-status
                                    employee-holiday-status-{{ $holiday->status }}
                                ">
                                    {{ $holiday->status_name }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route(
                                        'company.hr.holidays.show',
                                        $holiday
                                    ) }}"
                                   style="color:#69a9ff;">
                                    View
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
                                <div class="employee-holiday-empty">
                                    No previous holiday history.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endif

@endsection
