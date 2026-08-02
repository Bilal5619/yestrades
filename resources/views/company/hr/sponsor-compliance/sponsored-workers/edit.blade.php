@extends('company.layouts.app')

@section('title', 'Edit Sponsored Worker')

@section('content')

<style>
    .sponsor-edit-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .sponsor-edit-header h1 {
        margin: 0 0 7px;
        color: #ffffff;
        font-size: 28px;
    }

    .sponsor-edit-header p {
        margin: 0;
        color: #aab4ca;
    }

    .sponsor-edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .sponsor-edit-card {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-edit-card h2 {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        color: #ffffff;
        font-size: 19px;
    }

    .sponsor-form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 16px;
    }

    .sponsor-form-group:last-child {
        margin-bottom: 0;
    }

    .sponsor-form-group label {
        color: #d8deeb;
        font-size: 13px;
        font-weight: 600;
    }

    .sponsor-input,
    .sponsor-select,
    .sponsor-textarea {
        width: 100%;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        outline: none;
        background: #162138;
        color: #ffffff;
        font-size: 14px;
    }

    .sponsor-input,
    .sponsor-select {
        min-height: 43px;
        padding: 0 13px;
    }

    .sponsor-textarea {
        min-height: 125px;
        padding: 12px 13px;
        resize: vertical;
    }

    .sponsor-input:focus,
    .sponsor-select:focus,
    .sponsor-textarea:focus {
        border-color: #2878dc;
        box-shadow: 0 0 0 2px rgba(40,120,220,0.15);
    }

    .sponsor-help {
        margin-top: 2px;
        color: #8f9bb1;
        font-size: 12px;
    }

    .sponsor-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .sponsor-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
        padding: 18px 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 43px;
        padding: 0 17px;
        border: 0;
        border-radius: 7px;
        cursor: pointer;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
        font-size: 14px;
    }

    .sponsor-button-secondary {
        background: #3a4967;
    }

    @media (max-width: 900px) {
        .sponsor-edit-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .sponsor-edit-header {
            flex-direction: column;
        }

        .sponsor-form-actions {
            flex-direction: column-reverse;
        }

        .sponsor-button {
            width: 100%;
        }
    }
</style>

<div class="sponsor-edit-header">
    <div>
        <h1>
            Edit Sponsored Worker
        </h1>

        <p>
            {{ $sponsoredWorker->employee->full_name }}
            · Employee ID:
            {{ $sponsoredWorker->employee->employee_number }}
        </p>
    </div>
</div>

@if ($errors->any())
    <div
        style="
            padding:16px;
            margin-bottom:20px;
            border-radius:8px;
            background:rgba(255,93,103,0.16);
            color:#ffb1b7;
        "
    >
        <strong>Please correct the following:</strong>

        <ul style="margin:10px 0 0 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ route(
        'company.hr.sponsor-compliance.sponsored-workers.update',
        $sponsoredWorker
    ) }}"
>
    @csrf
    @method('PUT')

    <div class="sponsor-edit-grid">

        <div class="sponsor-edit-card">
            <h2>Immigration Details</h2>

            <div class="sponsor-form-group">
                <label for="immigration_route">
                    Immigration route
                </label>

                <input
                    type="text"
                    name="immigration_route"
                    id="immigration_route"
                    class="sponsor-input"
                    value="{{ old(
                        'immigration_route',
                        $sponsoredWorker->immigration_route
                    ) }}"
                    placeholder="Example: Skilled Worker"
                >

                @error('immigration_route')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="immigration_status">
                    Immigration status
                </label>

                <input
                    type="text"
                    name="immigration_status"
                    id="immigration_status"
                    class="sponsor-input"
                    value="{{ old(
                        'immigration_status',
                        $sponsoredWorker->immigration_status
                    ) }}"
                    placeholder="Example: Permission granted"
                >

                @error('immigration_status')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="visa_start_date">
                    Visa start date
                </label>

                <input
                    type="date"
                    name="visa_start_date"
                    id="visa_start_date"
                    class="sponsor-input"
                    value="{{ old(
                        'visa_start_date',
                        optional(
                            $sponsoredWorker->visa_start_date
                        )->format('Y-m-d')
                    ) }}"
                >

                @error('visa_start_date')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="visa_expiry_date">
                    Visa expiry date
                </label>

                <input
                    type="date"
                    name="visa_expiry_date"
                    id="visa_expiry_date"
                    class="sponsor-input"
                    value="{{ old(
                        'visa_expiry_date',
                        optional(
                            $sponsoredWorker->visa_expiry_date
                        )->format('Y-m-d')
                    ) }}"
                >

                @error('visa_expiry_date')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="sponsor-edit-card">
            <h2>Certificate of Sponsorship</h2>

            <div class="sponsor-form-group">
                <label for="cos_number">
                    CoS number
                </label>

                <input
                    type="text"
                    name="cos_number"
                    id="cos_number"
                    class="sponsor-input"
                    value="{{ old(
                        'cos_number',
                        $sponsoredWorker->cos_number
                    ) }}"
                >

                @error('cos_number')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="cos_job_title">
                    Sponsored job title
                </label>

                <input
                    type="text"
                    name="cos_job_title"
                    id="cos_job_title"
                    class="sponsor-input"
                    value="{{ old(
                        'cos_job_title',
                        $sponsoredWorker->cos_job_title
                    ) }}"
                >

                @error('cos_job_title')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="occupation_code">
                    Occupation code
                </label>

                <input
                    type="text"
                    name="occupation_code"
                    id="occupation_code"
                    class="sponsor-input"
                    value="{{ old(
                        'occupation_code',
                        $sponsoredWorker->occupation_code
                    ) }}"
                >

                @error('occupation_code')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="cos_salary">
                    CoS salary
                </label>

                <input
                    type="number"
                    name="cos_salary"
                    id="cos_salary"
                    class="sponsor-input"
                    step="0.01"
                    min="0"
                    value="{{ old(
                        'cos_salary',
                        $sponsoredWorker->cos_salary
                    ) }}"
                >

                <div class="sponsor-help">
                    Enter the annual salary stated on the CoS.
                </div>

                @error('cos_salary')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="actual_salary">
                    Current actual salary
                </label>

                <input
                    type="number"
                    name="actual_salary"
                    id="actual_salary"
                    class="sponsor-input"
                    step="0.01"
                    min="0"
                    value="{{ old(
                        'actual_salary',
                        $sponsoredWorker->actual_salary
                    ) }}"
                >

                @error('actual_salary')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="cos_weekly_hours">
                    CoS weekly hours
                </label>

                <input
                    type="number"
                    name="cos_weekly_hours"
                    id="cos_weekly_hours"
                    class="sponsor-input"
                    step="0.01"
                    min="0"
                    max="168"
                    value="{{ old(
                        'cos_weekly_hours',
                        $sponsoredWorker->cos_weekly_hours
                    ) }}"
                >

                @error('cos_weekly_hours')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="cos_work_location">
                    CoS work location
                </label>

                <input
                    type="text"
                    name="cos_work_location"
                    id="cos_work_location"
                    class="sponsor-input"
                    value="{{ old(
                        'cos_work_location',
                        $sponsoredWorker->cos_work_location
                    ) }}"
                >

                @error('cos_work_location')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="sponsor-edit-card">
            <h2>Employment and Sponsorship Dates</h2>

            <div class="sponsor-form-group">
                <label for="sponsorship_start_date">
                    Sponsorship start date
                </label>

                <input
                    type="date"
                    name="sponsorship_start_date"
                    id="sponsorship_start_date"
                    class="sponsor-input"
                    value="{{ old(
                        'sponsorship_start_date',
                        optional(
                            $sponsoredWorker->sponsorship_start_date
                        )->format('Y-m-d')
                    ) }}"
                >

                @error('sponsorship_start_date')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="sponsorship_end_date">
                    Sponsorship end date
                </label>

                <input
                    type="date"
                    name="sponsorship_end_date"
                    id="sponsorship_end_date"
                    class="sponsor-input"
                    value="{{ old(
                        'sponsorship_end_date',
                        optional(
                            $sponsoredWorker->sponsorship_end_date
                        )->format('Y-m-d')
                    ) }}"
                >

                @error('sponsorship_end_date')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="actual_first_working_date">
                    Actual first working date
                </label>

                <input
                    type="date"
                    name="actual_first_working_date"
                    id="actual_first_working_date"
                    class="sponsor-input"
                    value="{{ old(
                        'actual_first_working_date',
                        optional(
                            $sponsoredWorker->actual_first_working_date
                        )->format('Y-m-d')
                    ) }}"
                >

                @error('actual_first_working_date')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="record_status">
                    Record status
                </label>

                <select
                    name="record_status"
                    id="record_status"
                    class="sponsor-select"
                >
                    @foreach ([
                        'active' => 'Active',
                        'ended' => 'Ended',
                        'inactive' => 'Inactive',
                    ] as $value => $label)
                        <option
                            value="{{ $value }}"
                            {{ old(
                                'record_status',
                                $sponsoredWorker->record_status
                            ) === $value ? 'selected' : '' }}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                @error('record_status')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="sponsor-edit-card">
            <h2>Monitoring Notes</h2>

            <div class="sponsor-form-group">
                <label for="sponsored_duties">
                    Sponsored duties
                </label>

                <textarea
                    name="sponsored_duties"
                    id="sponsored_duties"
                    class="sponsor-textarea"
                >{{ old(
                    'sponsored_duties',
                    $sponsoredWorker->sponsored_duties
                ) }}</textarea>

                @error('sponsored_duties')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="permitted_absence_notes">
                    Permitted absence notes
                </label>

                <textarea
                    name="permitted_absence_notes"
                    id="permitted_absence_notes"
                    class="sponsor-textarea"
                >{{ old(
                    'permitted_absence_notes',
                    $sponsoredWorker->permitted_absence_notes
                ) }}</textarea>

                @error('permitted_absence_notes')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="sponsor-form-group">
                <label for="compliance_notes">
                    Compliance notes
                </label>

                <textarea
                    name="compliance_notes"
                    id="compliance_notes"
                    class="sponsor-textarea"
                >{{ old(
                    'compliance_notes',
                    $sponsoredWorker->compliance_notes
                ) }}</textarea>

                @error('compliance_notes')
                    <div class="sponsor-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

    </div>

    <div class="sponsor-form-actions">
        <a
            href="{{ route(
                'company.hr.sponsor-compliance.sponsored-workers.show',
                $sponsoredWorker
            ) }}"
            class="sponsor-button sponsor-button-secondary"
        >
            <i class="fas fa-times"></i>
            Cancel
        </a>

        <button
            type="submit"
            class="sponsor-button"
        >
            <i class="fas fa-save"></i>
            Save Sponsored Worker Details
        </button>
    </div>
</form>

@endsection