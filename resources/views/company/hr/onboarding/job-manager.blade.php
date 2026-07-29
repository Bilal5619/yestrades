@extends('company.layouts.app')

@section('title', 'Job & Manager Confirmation')

@section('content')

<style>
    .job-confirmation-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .job-confirmation-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .job-confirmation-header p {
        margin: 0;
        color: #aab4ca;
    }

    .job-confirmation-back {
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

    .job-confirmation-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .job-confirmation-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .job-confirmation-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .job-confirmation-field-full {
        grid-column: 1 / -1;
    }

    .job-confirmation-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .job-confirmation-field input,
    .job-confirmation-field select,
    .job-confirmation-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .job-confirmation-field textarea {
        min-height: 110px;
        resize: vertical;
    }

    .job-confirmation-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .job-confirmation-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .job-confirmation-save {
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    @media (max-width: 750px) {
        .job-confirmation-grid {
            grid-template-columns: 1fr;
        }

        .job-confirmation-field-full {
            grid-column: auto;
        }

        .job-confirmation-header {
            flex-direction: column;
        }
    }
</style>

@php
    $details = $onboarding->jobDetails;
    $employee = $onboarding->employee;
@endphp

<div class="job-confirmation-header">

    <div>
        <h1>Job & Manager Confirmation</h1>

        <p>
            Confirm employment details for
            {{ $employee->first_name }}
            {{ $employee->last_name }}.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.onboarding.show',
            $onboarding
        ) }}"
       class="job-confirmation-back">

        <i class="fas fa-arrow-left"></i>
        Employee Onboarding
    </a>

</div>

<div class="job-confirmation-card">

    <form method="POST"
          action="{{ route(
              'company.hr.onboarding.job-manager.update',
              $onboarding
          ) }}">

        @csrf
        @method('PUT')

        <div class="job-confirmation-grid">

            <div class="job-confirmation-field">
                <label>Job Title *</label>

                <input type="text"
                       name="job_title"
                       value="{{ old(
                           'job_title',
                           optional($details)->job_title
                               ?? $employee->job_title
                       ) }}"
                       required>

                @error('job_title')
                    <div class="job-confirmation-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="job-confirmation-field">
                <label>Department</label>

                <input type="text"
                       name="department"
                       value="{{ old(
                           'department',
                           optional($details)->department
                               ?? $employee->department
                       ) }}">
            </div>

            <div class="job-confirmation-field">
                <label>Line Manager</label>

                <select name="manager_id">
                    <option value="">Select manager</option>

                    @foreach ($managers as $manager)
                        <option value="{{ $manager->id }}"
                            {{ (string) old(
                                'manager_id',
                                optional($details)->manager_id
                            ) === (string) $manager->id
                                ? 'selected'
                                : ''
                            }}>
                          {{ trim($manager->first_name . ' ' . $manager->last_name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="job-confirmation-field">
                <label>Start Date *</label>

                <input type="date"
                       name="start_date"
                       value="{{ old(
                           'start_date',
                           optional(
                               optional($details)->start_date
                           )->format('Y-m-d')
                               ?? optional(
                                   $employee->start_date
                               )->format('Y-m-d')
                       ) }}"
                       required>
            </div>

            <div class="job-confirmation-field">
                <label>Employment Type *</label>

                <select name="employment_type" required>
                    @foreach ([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'temporary' => 'Temporary',
                        'contractor' => 'Contractor',
                        'apprentice' => 'Apprentice',
                        'zero_hours' => 'Zero Hours',
                    ] as $value => $label)

                        <option value="{{ $value }}"
                            {{ old(
                                'employment_type',
                                optional($details)->employment_type
                            ) === $value
                                ? 'selected'
                                : ''
                            }}>
                            {{ $label }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="job-confirmation-field">
                <label>Work Location</label>

                <input type="text"
       name="work_location"
       value="{{ old(
           'work_location',
           optional($details)->work_location
               ?? $employee->work_location
       ) }}">
            </div>

            <div class="job-confirmation-field">
                <label>Weekly Hours</label>

               <input type="number"
       step="0.01"
       min="0"
       max="168"
       name="weekly_hours"
       value="{{ old(
           'weekly_hours',
           optional($details)->weekly_hours
               ?? $employee->normal_weekly_hours
       ) }}">
            </div>

           @php
    $selectedPayType = old(
        'pay_type',
        optional($details)->pay_type
            ?? $employee->pay_type
            ?? 'salary'
    );
@endphp

<div class="job-confirmation-field">
    <label>Pay Type *</label>

    <select name="pay_type"
            id="pay_type"
            required>

        <option value="salary"
            {{ in_array(
                $selectedPayType,
                ['salary', 'annual_salary']
            ) ? 'selected' : '' }}>
            Annual Salary
        </option>

        <option value="hourly"
            {{ in_array(
                $selectedPayType,
                ['hourly', 'hourly_rate']
            ) ? 'selected' : '' }}>
            Hourly Rate
        </option>
    </select>
</div>

            <div class="job-confirmation-field"
                 id="salary_field">
                <label>Annual Salary</label>
<input type="number"
       step="0.01"
       min="0"
       name="salary"
       value="{{ old(
           'salary',
           optional($details)->salary
               ?? $employee->pay_rate
       ) }}">
            </div>

            <div class="job-confirmation-field"
                 id="hourly_rate_field">
                <label>Hourly Rate</label>

                <input type="number"
       step="0.01"
       min="0"
       name="hourly_rate"
       value="{{ old(
           'hourly_rate',
           optional($details)->hourly_rate
               ?? (
                   in_array($employee->pay_type, [
                       'hourly',
                       'hourly_rate',
                   ])
                       ? $employee->pay_rate
                       : null
               )
       ) }}">
            </div>

            <div class="job-confirmation-field">
                <label>Holiday Allowance</label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="holiday_allowance"
                       value="{{ old(
                           'holiday_allowance',
                           optional($details)->holiday_allowance
                       ) }}">
            </div>

            <div class="job-confirmation-field">
                <label>Notice Period</label>

                <input type="text"
                       name="notice_period"
                       placeholder="Example: 1 month"
                       value="{{ old(
                           'notice_period',
                           optional($details)->notice_period
                       ) }}">
            </div>

            <div class="job-confirmation-field">
                <label>Probation Required? *</label>

                <select name="probation_required"
                        id="probation_required"
                        required>
                    <option value="0"
                        {{ (string) old(
                            'probation_required',
                            optional($details)->probation_required ?? 0
                        ) === '0'
                            ? 'selected'
                            : ''
                        }}>
                        No
                    </option>

                    <option value="1"
                        {{ (string) old(
                            'probation_required',
                            optional($details)->probation_required ?? 0
                        ) === '1'
                            ? 'selected'
                            : ''
                        }}>
                        Yes
                    </option>
                </select>
            </div>

            <div class="job-confirmation-field"
                 id="probation_months_field">
                <label>Probation Length in Months</label>

                <input type="number"
                       min="1"
                       max="24"
                       name="probation_months"
                       value="{{ old(
                           'probation_months',
                           optional($details)->probation_months
                       ) }}">
            </div>

            <div class="
                job-confirmation-field
                job-confirmation-field-full
            ">
                <label>Notes</label>

                <textarea name="notes">{{ old(
                    'notes',
                    optional($details)->notes
                ) }}</textarea>
            </div>

        </div>

        @if (
            auth('company')->user()
                ->hasPermission('onboarding.manage')
        )
            <div class="job-confirmation-actions">
                <button type="submit"
                        class="job-confirmation-save">
                    Confirm Job Details
                </button>
            </div>
        @endif

    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const payType = document.getElementById('pay_type');
        const salaryField = document.getElementById('salary_field');
        const hourlyField = document.getElementById('hourly_rate_field');

        const probationRequired =
            document.getElementById('probation_required');

        const probationMonthsField =
            document.getElementById('probation_months_field');

        function updatePayFields() {
            salaryField.style.display =
                payType.value === 'salary' ? 'flex' : 'none';

            hourlyField.style.display =
                payType.value === 'hourly' ? 'flex' : 'none';
        }

        function updateProbationField() {
            probationMonthsField.style.display =
                probationRequired.value === '1'
                    ? 'flex'
                    : 'none';
        }

        payType.addEventListener('change', updatePayFields);

        probationRequired.addEventListener(
            'change',
            updateProbationField
        );

        updatePayFields();
        updateProbationField();
    });
</script>

@endsection