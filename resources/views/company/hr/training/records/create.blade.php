@extends('company.layouts.app')

@section('title', 'Assign Training')

@section('content')

<style>
    .assign-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .assign-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .assign-header p {
        margin: 0;
        color: #aab4ca;
    }

    .assign-back {
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

    .assign-panel {
        max-width: 950px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .assign-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .assign-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .assign-full {
        grid-column: 1 / -1;
    }

    .assign-group label {
        font-weight: 600;
    }

    .assign-group input,
    .assign-group select,
    .assign-group textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .assign-group textarea {
        min-height: 110px;
        resize: vertical;
    }

    .assign-actions {
        display: flex;
        gap: 10px;
        margin-top: 22px;
    }

    .assign-submit,
    .assign-cancel {
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

    .assign-submit {
        background: #2878dc;
    }

    .assign-cancel {
        background: #3a4967;
    }

    .assign-error {
        padding: 14px 17px;
        margin-bottom: 20px;
        border-radius: 7px;
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    @media (max-width: 750px) {
        .assign-header {
            flex-direction: column;
        }

        .assign-grid {
            grid-template-columns: 1fr;
        }

        .assign-full {
            grid-column: auto;
        }
    }
</style>

<div class="assign-header">

    <div>
        <h1>Assign Training</h1>

        <p>
            Assign a training course to an employee.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.training.records.index'
        ) }}"
       class="assign-back">
        <i class="fas fa-arrow-left"></i>
        Employee Training
    </a>

</div>

@if ($errors->any())
    <div class="assign-error">
        <strong>Please correct the following:</strong>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="assign-panel">

    <form method="POST"
          action="{{ route(
              'company.hr.training.records.store'
          ) }}">

        @csrf

        <div class="assign-grid">

            <div class="assign-group assign-full">
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

                            {{ $employee->first_name }}
                            {{ $employee->last_name }}
                            —
                            {{ $employee->employee_number }}

                        </option>
                    @endforeach

                </select>
            </div>

            <div class="assign-group assign-full">
                <label for="training_course_id">
                    Training Course
                </label>

                <select name="training_course_id"
                        id="training_course_id"
                        required>

                    <option value="">
                        Select training course
                    </option>

                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ (int) old(
                                    'training_course_id'
                                ) === (int) $course->id
                                ? 'selected'
                                : '' }}>

                            {{ $course->name }}

                            @if ($course->is_mandatory)
                                — Mandatory
                            @endif

                        </option>
                    @endforeach

                </select>
            </div>

            <div class="assign-group">
                <label for="provider_name">
                    Training Provider
                </label>

                <input type="text"
                       name="provider_name"
                       id="provider_name"
                       maxlength="180"
                       value="{{ old('provider_name') }}"
                       placeholder="Training provider">
            </div>

            <div class="assign-group">
                <label for="scheduled_date">
                    Scheduled Date
                </label>

                <input type="date"
                       name="scheduled_date"
                       id="scheduled_date"
                       value="{{ old('scheduled_date') }}">
            </div>

            <div class="assign-group">
                <label for="status">
                    Status
                </label>

                <select name="status"
                        id="status"
                        required>

                    <option value="not_started"
                        {{ old('status') === 'not_started'
                            ? 'selected'
                            : '' }}>
                        Not Started
                    </option>

                    <option value="scheduled"
                        {{ old('status', 'scheduled') ===
                           'scheduled'
                            ? 'selected'
                            : '' }}>
                        Scheduled
                    </option>

                    <option value="in_progress"
                        {{ old('status') === 'in_progress'
                            ? 'selected'
                            : '' }}>
                        In Progress
                    </option>

                </select>
            </div>

            <div class="assign-group">
                <label for="cost">
                    Cost
                </label>

                <input type="number"
                       name="cost"
                       id="cost"
                       min="0"
                       step="0.01"
                       value="{{ old('cost') }}"
                       placeholder="0.00">
            </div>

            <div class="assign-group assign-full">
                <label for="notes">
                    Notes
                </label>

                <textarea name="notes"
                          id="notes"
                          maxlength="3000">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="assign-actions">

            <button type="submit"
                    class="assign-submit">
                Assign Training
            </button>

            <a href="{{ route(
                    'company.hr.training.records.index'
                ) }}"
               class="assign-cancel">
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection