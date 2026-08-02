@extends('company.layouts.app')

@section('title', 'Sponsor Compliance')

@section('content')

<style>
    .sponsor-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .sponsor-detail-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
        color: #ffffff;
    }

    .sponsor-detail-header p {
        margin: 0;
        color: #aab4ca;
    }

    .sponsor-detail-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sponsor-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
        border: 0;
    }

    .sponsor-button-secondary {
        background: #3a4967;
    }

    .sponsor-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .sponsor-summary-card {
        padding: 19px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-summary-label {
        margin-bottom: 9px;
        color: #aab4ca;
        font-size: 13px;
    }

    .sponsor-summary-value {
        color: #ffffff;
        font-size: 18px;
        font-weight: 700;
    }

    .sponsor-profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .sponsor-card {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-card h2 {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        color: #ffffff;
        font-size: 19px;
    }

    .sponsor-detail-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 14px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .sponsor-detail-row:last-child {
        border-bottom: 0;
    }

    .sponsor-detail-label {
        color: #aab4ca;
        font-size: 13px;
    }

    .sponsor-detail-value {
        color: #ffffff;
        word-break: break-word;
    }

    .sponsor-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 14px;
        font-size: 12px;
    }

    .sponsor-status-success {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .sponsor-status-warning {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .sponsor-status-danger {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .sponsor-status-neutral {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .sponsor-comparison-panel {
        padding: 22px;
        margin-top: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-comparison-panel h2 {
        margin: 0 0 18px;
        color: #ffffff;
        font-size: 19px;
    }

    .sponsor-table-wrapper {
        overflow-x: auto;
    }

    .sponsor-comparison-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    .sponsor-comparison-table th,
    .sponsor-comparison-table td {
        padding: 13px 12px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .sponsor-comparison-table th {
        color: #aab4ca;
        font-size: 12px;
        text-transform: uppercase;
    }

    .sponsor-comparison-table td {
        color: #ffffff;
    }

    @media (max-width: 1000px) {
        .sponsor-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sponsor-profile-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .sponsor-detail-header {
            flex-direction: column;
        }

        .sponsor-summary-grid {
            grid-template-columns: 1fr;
        }

        .sponsor-detail-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }

        .sponsor-button {
            width: 100%;
        }
    }
</style>

@php
    $employee = $sponsoredWorker->employee;

    $address = array_filter([
        $employee->address_line_1,
        $employee->address_line_2,
        $employee->city,
        $employee->county,
        $employee->postcode,
        $employee->country,
    ]);

    $visaClass = match ($sponsoredWorker->visa_status) {
        'valid' => 'sponsor-status-success',
        'expired' => 'sponsor-status-danger',
        'urgent',
        'expiring_60_days',
        'expiring_90_days' => 'sponsor-status-warning',
        default => 'sponsor-status-neutral',
    };
@endphp

<div class="sponsor-detail-header">

    <div>
        <h1>
            Sponsor Compliance:
            {{ $employee->full_name }}
        </h1>

        <p>
            Employee ID:
            {{ $employee->employee_number }}
        </p>
    </div>

    <div class="sponsor-detail-actions">

        @if (
    auth('company')->user()
        ->hasPermission('sponsor-compliance.edit')
)
    <a
        href="{{ route(
            'company.hr.sponsor-compliance.sponsored-workers.edit',
            $sponsoredWorker
        ) }}"
        class="sponsor-button"
    >
        <i class="fas fa-edit"></i>
        Edit Sponsored Worker
    </a>
@endif

        <a
            href="{{ route(
                'company.hr.sponsor-compliance.sponsored-workers.index'
            ) }}"
            class="sponsor-button sponsor-button-secondary"
        >
            <i class="fas fa-arrow-left"></i>
            Back to Sponsored Workers
        </a>

    </div>

</div>

<div class="sponsor-summary-grid">

    <div class="sponsor-summary-card">
        <div class="sponsor-summary-label">
            Visa Status
        </div>

        <div class="sponsor-summary-value">
            <span class="sponsor-status {{ $visaClass }}">
                {{ $sponsoredWorker->visa_status_label }}
            </span>
        </div>
    </div>

    <div class="sponsor-summary-card">
        <div class="sponsor-summary-label">
            CoS Number
        </div>

        <div class="sponsor-summary-value">
            {{ $sponsoredWorker->cos_number ?: 'Not entered' }}
        </div>
    </div>

    <div class="sponsor-summary-card">
        <div class="sponsor-summary-label">
            Occupation Code
        </div>

        <div class="sponsor-summary-value">
            {{ $sponsoredWorker->occupation_code ?: 'Not entered' }}
        </div>
    </div>

    <div class="sponsor-summary-card">
        <div class="sponsor-summary-label">
            Overall Compliance
        </div>

        <div class="sponsor-summary-value">
            @if ($sponsoredWorker->hasComplianceDifference())
                <span class="sponsor-status sponsor-status-warning">
                    Review Required
                </span>
            @else
                <span class="sponsor-status sponsor-status-success">
                    Compliant
                </span>
            @endif
        </div>
    </div>

</div>

<div class="sponsor-profile-grid">

    <div class="sponsor-card">
        <h2>Immigration Status</h2>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Immigration route
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->immigration_route ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Immigration status
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->immigration_status ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Visa start date
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->visa_start_date
                    ? $sponsoredWorker->visa_start_date->format('d M Y')
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Visa expiry date
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->visa_expiry_date
                    ? $sponsoredWorker->visa_expiry_date->format('d M Y')
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Visa status
            </div>

            <div class="sponsor-detail-value">
                <span class="sponsor-status {{ $visaClass }}">
                    {{ $sponsoredWorker->visa_status_label }}
                </span>
            </div>
        </div>
    </div>

    <div class="sponsor-card">
        <h2>Certificate of Sponsorship</h2>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                CoS number
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->cos_number ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Sponsored job title
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->cos_job_title ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Occupation code
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->occupation_code ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                CoS salary
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->cos_salary !== null
                    ? '£' . number_format($sponsoredWorker->cos_salary, 2)
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                CoS weekly hours
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->cos_weekly_hours !== null
                    ? $sponsoredWorker->cos_weekly_hours . ' hours'
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                CoS work location
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->cos_work_location ?: 'Not entered' }}
            </div>
        </div>
    </div>

    <div class="sponsor-card">
        <h2>Current Employment Details</h2>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Current job title
            </div>

            <div class="sponsor-detail-value">
                {{ $employee->job_title ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Current salary
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->actual_salary !== null
                    ? '£' . number_format(
                        $sponsoredWorker->actual_salary,
                        2
                    )
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Weekly hours
            </div>

            <div class="sponsor-detail-value">
                {{ $employee->normal_weekly_hours !== null
                    ? $employee->normal_weekly_hours . ' hours'
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Work location
            </div>

            <div class="sponsor-detail-value">
                {{ $employee->work_location ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Start date
            </div>

            <div class="sponsor-detail-value">
                {{ $employee->start_date
                    ? $employee->start_date->format('d M Y')
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Actual first working date
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->actual_first_working_date
                    ? $sponsoredWorker->actual_first_working_date
                        ->format('d M Y')
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Line manager
            </div>

            <div class="sponsor-detail-value">
                {{ optional($employee->manager)->full_name
                    ?? 'Not assigned'
                }}
            </div>
        </div>
    </div>

    <div class="sponsor-card">
        <h2>Current Contact Information</h2>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Home address
            </div>

            <div class="sponsor-detail-value">
                {{ count($address)
                    ? implode(', ', $address)
                    : 'Not entered'
                }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Phone
            </div>

            <div class="sponsor-detail-value">
                {{ $employee->phone ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Personal email
            </div>

            <div class="sponsor-detail-value">
                {{ $employee->personal_email ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Work email
            </div>

            <div class="sponsor-detail-value">
                {{ $employee->work_email ?: 'Not entered' }}
            </div>
        </div>

        <div class="sponsor-detail-row">
            <div class="sponsor-detail-label">
                Permitted absence notes
            </div>

            <div class="sponsor-detail-value">
                {{ $sponsoredWorker->permitted_absence_notes
                    ?: 'No notes entered'
                }}
            </div>
        </div>
    </div>

</div>

<div class="sponsor-comparison-panel">
    <h2>CoS versus Current Employment Comparison</h2>

    <div class="sponsor-table-wrapper">
        <table class="sponsor-comparison-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>CoS Information</th>
                    <th>Current Information</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($comparisons as $comparison)
                    <tr>
                        <td>
                            {{ $comparison['label'] }}
                        </td>

                        <td>
                            {{ $comparison['cos_value']
                                ?? 'Not entered'
                            }}
                        </td>

                        <td>
                            {{ $comparison['actual_value']
                                ?? 'Not entered'
                            }}
                        </td>

                        <td>
                            @if ($comparison['matches'])
                                <span class="
                                    sponsor-status
                                    sponsor-status-success
                                ">
                                    Match
                                </span>
                            @else
                                <span class="
                                    sponsor-status
                                    sponsor-status-warning
                                ">
                                    Review Required
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<style>
    .sponsor-change-section {
        margin-top: 22px;
    }

    .sponsor-change-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .sponsor-change-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .sponsor-change-field-full {
        grid-column: 1 / -1;
    }

    .sponsor-change-field label {
        color: #d8deeb;
        font-size: 13px;
        font-weight: 600;
    }

    .sponsor-change-input,
    .sponsor-change-select,
    .sponsor-change-textarea {
        width: 100%;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #162138;
        color: #ffffff;
    }

    .sponsor-change-input,
    .sponsor-change-select {
        min-height: 43px;
        padding: 0 13px;
    }

    .sponsor-change-textarea {
        min-height: 100px;
        padding: 12px 13px;
        resize: vertical;
    }

    .sponsor-change-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .sponsor-change-table th,
    .sponsor-change-table td {
        padding: 13px 11px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
        vertical-align: top;
    }

    .sponsor-change-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .sponsor-change-table td {
        color: #ffffff;
        font-size: 14px;
    }

    @media (max-width: 750px) {
        .sponsor-change-form-grid {
            grid-template-columns: 1fr;
        }

        .sponsor-change-field-full {
            grid-column: auto;
        }
    }
</style>

@if (
    auth('company')->user()
        ->hasPermission('sponsor-compliance.edit')
)
    <div class="sponsor-card sponsor-change-section">
        <h2>Record Employment or Compliance Change</h2>

        <form
            method="POST"
            action="{{ route(
                'company.hr.sponsor-compliance.sponsored-workers.changes.store',
                $sponsoredWorker
            ) }}"
        >
            @csrf

            <div class="sponsor-change-form-grid">

                <div class="sponsor-change-field">
                    <label for="change_type">
                        Change type
                    </label>

                    <select
                        name="change_type"
                        id="change_type"
                        class="sponsor-change-select"
                        required
                    >
                        <option value="">
                            Select change type
                        </option>

                        @foreach ([
                            'job_title' =>
                                'Job Title',

                            'salary' =>
                                'Salary',

                            'weekly_hours' =>
                                'Weekly Hours',

                            'work_location' =>
                                'Work Location',

                            'duties' =>
                                'Sponsored Duties',

                            'immigration_status' =>
                                'Immigration Status',

                            'visa_expiry' =>
                                'Visa Expiry',

                            'line_manager' =>
                                'Line Manager',

                            'home_address' =>
                                'Home Address',

                            'phone' =>
                                'Phone Number',

                            'email' =>
                                'Email Address',

                            'other' =>
                                'Other',
                        ] as $value => $label)
                            <option
                                value="{{ $value }}"
                                {{ old('change_type')
                                    === $value
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sponsor-change-field">
                    <label for="effective_date">
                        Effective date
                    </label>

                    <input
                        type="date"
                        name="effective_date"
                        id="effective_date"
                        class="sponsor-change-input"
                        value="{{ old('effective_date') }}"
                    >
                </div>

                <div class="sponsor-change-field">
                    <label for="old_value">
                        Previous value
                    </label>

                    <textarea
                        name="old_value"
                        id="old_value"
                        class="sponsor-change-textarea"
                    >{{ old('old_value') }}</textarea>
                </div>

                <div class="sponsor-change-field">
                    <label for="new_value">
                        New value
                    </label>

                    <textarea
                        name="new_value"
                        id="new_value"
                        class="sponsor-change-textarea"
                        required
                    >{{ old('new_value') }}</textarea>
                </div>

                <div class="sponsor-change-field">
                    <label for="requires_ukvi_review">
                        Requires UKVI review?
                    </label>

                    <select
                        name="requires_ukvi_review"
                        id="requires_ukvi_review"
                        class="sponsor-change-select"
                        required
                    >
                        <option value="1">
                            Yes
                        </option>

                        <option
                            value="0"
                            {{ old(
                                'requires_ukvi_review'
                            ) === '0'
                                ? 'selected'
                                : ''
                            }}
                        >
                            No
                        </option>
                    </select>
                </div>

                <div class="
                    sponsor-change-field
                    sponsor-change-field-full
                ">
                    <label for="notes">
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        class="sponsor-change-textarea"
                    >{{ old('notes') }}</textarea>
                </div>

            </div>

            <div style="margin-top:18px;">
                <button
                    type="submit"
                    class="sponsor-button"
                >
                    <i class="fas fa-save"></i>
                    Record Change
                </button>
            </div>
        </form>
    </div>
@endif

<div class="sponsor-card sponsor-change-section">
    <h2>Sponsored Worker Change History</h2>

    <div class="sponsor-table-wrapper">
        <table class="sponsor-change-table">
            <thead>
                <tr>
                    <th>Change</th>
                    <th>Previous Value</th>
                    <th>New Value</th>
                    <th>Effective Date</th>
                    <th>UKVI Review</th>
                    <th>Review Status</th>
                    <th>Recorded By</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse (
                    $sponsoredWorker->changes
                    as $change
                )
                    <tr>
                        <td>
                            {{ $change->change_type_label }}
                        </td>

                        <td>
                            {{ $change->old_value
                                ?: 'Not recorded'
                            }}
                        </td>

                        <td>
                            {{ $change->new_value }}
                        </td>

                        <td>
                            {{ $change->effective_date
                                ? $change->effective_date
                                    ->format('d M Y')
                                : 'Not set'
                            }}
                        </td>

                        <td>
                            {{ $change->requires_ukvi_review
                                ? 'Required'
                                : 'Not required'
                            }}
                        </td>

                        <td>
                            {{ $change->review_status_label }}
                        </td>

                        <td>
                            {{ $change
                                    ->createdByUser
                                    ?->name
                                ?: 'System'
                            }}
                        </td>

                        <td>
                            @if (
                                auth('company')->user()
                                    ->hasPermission(
                                        'sponsor-compliance.review'
                                    )
                            )
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'company.hr.sponsor-compliance.sponsored-workers.changes.review',
                                        [
                                            $sponsoredWorker,
                                            $change,
                                        ]
                                    ) }}"
                                >
                                    @csrf
                                    @method('PUT')

                                    <select
                                        name="review_status"
                                        class="sponsor-change-select"
                                        style="
                                            min-width:180px;
                                            margin-bottom:7px;
                                        "
                                    >
                                        @foreach ([
                                            'pending' =>
                                                'Pending',

                                            'reviewed' =>
                                                'Reviewed',

                                            'ukvi_case_required' =>
                                                'UKVI Case Required',

                                            'no_report_required' =>
                                                'No Report Required',
                                        ] as $value => $label)
                                            <option
                                                value="{{ $value }}"
                                                {{ $change
                                                        ->review_status
                                                    === $value
                                                        ? 'selected'
                                                        : ''
                                                }}
                                            >
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input
                                        type="hidden"
                                        name="requires_ukvi_review"
                                        value="{{ $change
                                            ->requires_ukvi_review
                                                ? 1
                                                : 0
                                        }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="notes"
                                        value="{{ $change->notes }}"
                                    >

                                    <button
                                        type="submit"
                                        class="sponsor-button"
                                        style="
                                            min-height:34px;
                                            padding:0 11px;
                                        "
                                    >
                                        Update
                                    </button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            No sponsored-worker changes
                            have been recorded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection