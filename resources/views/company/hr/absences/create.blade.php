@extends('company.layouts.app')

@section('title', 'Record Absence')

@section('content')
<style>
    .absence-page-header {
        margin-bottom: 24px;
    }

    .absence-page-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
        font-weight: 600;
    }

    .absence-page-header p {
        margin: 0;
        color: #aab4ca;
        font-size: 14px;
    }

    .absence-form-card {
        max-width: 850px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
    }

    .absence-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .absence-form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .absence-form-group.full-width {
        grid-column: 1 / -1;
    }

    .absence-form-group label {
        color: #cbd4e6;
        font-size: 13px;
        font-weight: 600;
    }

    .absence-input,
    .absence-select,
    .absence-textarea {
        width: 100%;
        padding: 11px 12px;
        border-radius: 7px;
        border: 1px solid rgba(255,255,255,.12);
        background: #162138;
        color: #ffffff;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
    }

    .absence-input:focus,
    .absence-select:focus,
    .absence-textarea:focus {
        border-color: #2878dc;
        box-shadow: 0 0 0 2px rgba(40,120,220,.15);
    }

    .absence-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .absence-file {
        width: 100%;
        padding: 10px;
        border-radius: 7px;
        border: 1px dashed rgba(255,255,255,.18);
        background: #162138;
        color: #aab4ca;
    }

    .absence-actions {
        margin-top: 22px;
        display: flex;
        gap: 12px;
    }

    .absence-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        cursor: pointer;
        background: #2878dc;
        color: #ffffff;
        font-size: 14px;
        text-decoration: none;
    }

    .absence-button-secondary {
        background: #3a4967;
    }

    .absence-error {
        margin-bottom: 18px;
        padding: 12px 14px;
        border-radius: 7px;
        background: rgba(236,87,98,.15);
        color: #ff9aa2;
        font-size: 13px;
    }

    @media (max-width: 700px) {
        .absence-form-grid {
            grid-template-columns: 1fr;
        }

        .absence-form-group.full-width {
            grid-column: auto;
        }
    }
</style>

<div class="absence-page-header">
    <h1>Record Employee Absence</h1>

    <p>
        Record sickness and other non-holiday employee absences.
    </p>
</div>

@if ($errors->any())
    <div class="absence-error">
        <ul style="margin:0;padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="absence-form-card">

    <form
        method="POST"
        action="{{ route('company.hr.absences.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="absence-form-grid">

            <div class="absence-form-group">
                <label>Employee</label>

                <select
                    name="employee_id"
                    class="absence-select"
                    required
                >
                    <option value="">
                        Select employee
                    </option>

                    @foreach ($employees as $employee)
                        <option
                            value="{{ $employee->id }}"
                            {{ old('employee_id') == $employee->id
                                ? 'selected'
                                : '' }}
                        >
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="absence-form-group">
                <label>Absence Type</label>

                <select
                    name="absence_type"
                    class="absence-select"
                    required
                >
                    <option
                        value="sickness"
                        {{ old('absence_type') === 'sickness'
                            ? 'selected'
                            : '' }}
                    >
                        Sickness
                    </option>

                    <option
                        value="unpaid_leave"
                        {{ old('absence_type') === 'unpaid_leave'
                            ? 'selected'
                            : '' }}
                    >
                        Unpaid Leave
                    </option>

                    <option
                        value="compassionate_leave"
                        {{ old('absence_type') === 'compassionate_leave'
                            ? 'selected'
                            : '' }}
                    >
                        Compassionate Leave
                    </option>

                    <option
                        value="authorised_absence"
                        {{ old('absence_type') === 'authorised_absence'
                            ? 'selected'
                            : '' }}
                    >
                        Authorised Absence
                    </option>

                    <option
                        value="unauthorised_absence"
                        {{ old('absence_type') === 'unauthorised_absence'
                            ? 'selected'
                            : '' }}
                    >
                        Unauthorised Absence
                    </option>

                    <option
                        value="other"
                        {{ old('absence_type') === 'other'
                            ? 'selected'
                            : '' }}
                    >
                        Other
                    </option>
                </select>
            </div>

            <div class="absence-form-group">
                <label>Start Date</label>

                <input
                    type="date"
                    name="start_date"
                    value="{{ old('start_date') }}"
                    class="absence-input"
                    required
                >
            </div>

            <div class="absence-form-group">
                <label>End Date</label>

                <input
                    type="date"
                    name="end_date"
                    value="{{ old('end_date') }}"
                    class="absence-input"
                >
            </div>

            <div class="absence-form-group">
                <label>Days Absent</label>

                <input
                    type="number"
                    name="days_absent"
                    value="{{ old('days_absent') }}"
                    step="0.5"
                    min="0"
                    class="absence-input"
                >
            </div>

            <div class="absence-form-group full-width">
                <label>Reason / Notes</label>

                <textarea
                    name="reason"
                    class="absence-textarea"
                    placeholder="Enter sickness reason or other relevant notes"
                >{{ old('reason') }}</textarea>
            </div>

            <div class="absence-form-group full-width">
                <label>Evidence / Fit Note</label>

                <input
                    type="file"
                    name="evidence"
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="absence-file"
                >
            </div>

        </div>

        <div class="absence-actions">

            <button
                type="submit"
                class="absence-button"
            >
                <i class="fas fa-save"></i>
                Save Absence
            </button>

            <a
                href="{{ route('company.hr.absences.index') }}"
                class="absence-button absence-button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection