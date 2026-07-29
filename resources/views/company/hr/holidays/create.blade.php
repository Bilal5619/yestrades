@extends('company.layouts.app')

@section('title', 'Add Holiday')

@section('content')

<style>
    .holiday-form-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .holiday-form-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .holiday-form-header p {
        margin: 0;
        color: #aab4ca;
    }

    .holiday-form-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .holiday-form-panel {
        max-width: 950px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .holiday-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .holiday-form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .holiday-form-full {
        grid-column: 1 / -1;
    }

    .holiday-form-group label {
        color: #d7deed;
        font-size: 14px;
        font-weight: 600;
    }

    .holiday-form-group input,
    .holiday-form-group select,
    .holiday-form-group textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .holiday-form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .holiday-form-actions {
        display: flex;
        gap: 10px;
        margin-top: 22px;
    }

    .holiday-form-submit,
    .holiday-form-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 43px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .holiday-form-submit {
        background: #2878dc;
    }

    .holiday-form-cancel {
        background: #3a4967;
    }

    .holiday-form-error {
        padding: 14px 17px;
        margin-bottom: 20px;
        border-radius: 7px;
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .holiday-form-help {
        color: #aab4ca;
        font-size: 12px;
    }

    @media (max-width: 750px) {
        .holiday-form-header {
            flex-direction: column;
        }

        .holiday-form-grid {
            grid-template-columns: 1fr;
        }

        .holiday-form-full {
            grid-column: auto;
        }
    }
</style>

<div class="holiday-form-header">

    <div>
        <h1>Add Holiday</h1>

        <p>
            Record a new employee holiday or leave request.
        </p>
    </div>

    <a href="{{ route('company.hr.holidays.index') }}"
       class="holiday-form-back">
        <i class="fas fa-arrow-left"></i>
        Holiday Overview
    </a>

</div>

@if ($errors->any())
    <div class="holiday-form-error">
        <strong>Please correct the following:</strong>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="holiday-form-panel">

    <form method="POST"
          action="{{ route(
              'company.hr.holidays.store'
          ) }}">

        @csrf

        <div class="holiday-form-grid">

            <div class="holiday-form-group holiday-form-full">
                <label for="employee_id">
                    Employee
                </label>

                <select name="employee_id"
                        id="employee_id"
                        required>

                    <option value="">
                        Select employee
                    </option>

                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}"
                            {{ (int) old(
                            'employee_id',
                            $selectedEmployeeId ?? 0
                        ) === (int) $employee->id
                        ? 'selected'
                        : '' }}>

                            {{ $employee->full_name }}
                            —
                            {{ $employee->employee_number }}

                        </option>
                    @endforeach

                </select>
            </div>

            <div class="holiday-form-group">
                <label for="holiday_type">
                    Holiday Type
                </label>

                <select name="holiday_type"
                        id="holiday_type"
                        required>

                    <option value="annual_leave"
                        {{ old('holiday_type') ===
                           'annual_leave'
                           ? 'selected'
                           : '' }}>
                        Annual Leave
                    </option>

                    <option value="sick_leave">
                        Sick Leave
                    </option>

                    <option value="unpaid_leave">
                        Unpaid Leave
                    </option>

                    <option value="maternity_leave">
                        Maternity Leave
                    </option>

                    <option value="paternity_leave">
                        Paternity Leave
                    </option>

                    <option value="compassionate_leave">
                        Compassionate Leave
                    </option>

                    <option value="other">
                        Other
                    </option>

                </select>
            </div>

            <div class="holiday-form-group">
                <label for="status">
                    Status
                </label>

                <select name="status"
                        id="status"
                        required>

                    <option value="pending"
                        {{ old('status') === 'pending'
                           ? 'selected'
                           : '' }}>
                        Pending
                    </option>

                    @if (
                        auth('company')->user()
                            ->hasPermission('holidays.approve')
                    )
                        <option value="approved"
                            {{ old('status') === 'approved'
                               ? 'selected'
                               : '' }}>
                            Approved
                        </option>
                    @endif

                </select>
            </div>

            <div class="holiday-form-group">
                <label for="start_date">
                    Start Date
                </label>

                <input type="date"
                       name="start_date"
                       id="start_date"
                       value="{{ old('start_date') }}"
                       required>
            </div>

            <div class="holiday-form-group">
                <label for="end_date">
                    End Date
                </label>

                <input type="date"
                       name="end_date"
                       id="end_date"
                       value="{{ old('end_date') }}"
                       required>
            </div>

            <div class="holiday-form-group">
                <label for="start_period">
                    First Day
                </label>

                <select name="start_period"
                        id="start_period"
                        required>

                    <option value="full_day">
                        Full Day
                    </option>

                    <option value="morning">
                        Morning Only
                    </option>

                    <option value="afternoon">
                        Afternoon Only
                    </option>

                </select>
            </div>

            <div class="holiday-form-group">
                <label for="end_period">
                    Last Day
                </label>

                <select name="end_period"
                        id="end_period"
                        required>

                    <option value="full_day">
                        Full Day
                    </option>

                    <option value="morning">
                        Morning Only
                    </option>

                    <option value="afternoon">
                        Afternoon Only
                    </option>

                </select>
            </div>

            <div class="holiday-form-group holiday-form-full">
                <span class="holiday-form-help">
                    Saturday and Sunday are excluded from the holiday-day calculation.
                </span>
            </div>

            <div class="holiday-form-group holiday-form-full">
                <label for="reason">
                    Reason
                </label>

                <textarea name="reason"
                          id="reason"
                          placeholder="Optional reason">{{ old('reason') }}</textarea>
            </div>

            <div class="holiday-form-group holiday-form-full">
                <label for="notes">
                    Internal Notes
                </label>

                <textarea name="notes"
                          id="notes"
                          placeholder="Only visible to authorised company users">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="holiday-form-actions">

            <button type="submit"
                    class="holiday-form-submit">
                Save Holiday
            </button>

            <a href="{{ route(
                    'company.hr.holidays.index'
                ) }}"
               class="holiday-form-cancel">
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection