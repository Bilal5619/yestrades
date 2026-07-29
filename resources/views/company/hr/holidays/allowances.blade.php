@extends('company.layouts.app')

@section('title', 'Holiday Allowances')

@section('content')

<style>
    .allowance-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .allowance-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .allowance-header p {
        margin: 0;
        color: #aab4ca;
    }

    .allowance-back {
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

    .allowance-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .allowance-year-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .allowance-year-form label {
        color: #aab4ca;
    }

    .allowance-year-form select {
        min-height: 40px;
        padding: 0 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .allowance-panel {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .allowance-table-wrapper {
        overflow-x: auto;
    }

    .allowance-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .allowance-table th,
    .allowance-table td {
        padding: 13px 10px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        vertical-align: middle;
    }

    .allowance-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .allowance-employee-name {
        font-weight: 600;
    }

    .allowance-employee-id {
        margin-top: 4px;
        color: #aab4ca;
        font-size: 12px;
    }

    .allowance-input {
        width: 105px;
        min-height: 39px;
        padding: 8px 10px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .allowance-notes {
        width: 180px;
    }

    .allowance-save {
        min-height: 39px;
        padding: 0 14px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .allowance-number {
        font-weight: 600;
    }

    .allowance-remaining-low {
        color: #ff7c86;
    }

    .allowance-remaining-good {
        color: #59dc96;
    }

    .allowance-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 7px;
    }

    .allowance-alert-success {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .allowance-alert-error {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .allowance-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 800px) {
        .allowance-header,
        .allowance-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="allowance-header">

    <div>
        <h1>Holiday Allowances</h1>

        <p>
            Set annual holiday entitlement and carried-over days
            for each employee.
        </p>
    </div>

    <a href="{{ route('company.hr.holidays.index') }}"
       class="allowance-back">
        <i class="fas fa-arrow-left"></i>
        Holiday Overview
    </a>

</div>

@if (session('success'))
    <div class="
        allowance-alert
        allowance-alert-success
    ">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="
        allowance-alert
        allowance-alert-error
    ">
        <strong>Please correct the following:</strong>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="allowance-toolbar">

    <div>
        <strong>Holiday Year: {{ $year }}</strong>
    </div>

    <form method="GET"
          action="{{ route(
              'company.hr.holidays.allowances'
          ) }}"
          class="allowance-year-form">

        <label for="year">
            View year
        </label>

        <select name="year"
                id="year"
                onchange="this.form.submit()">

            @foreach ($years as $availableYear)
                <option value="{{ $availableYear }}"
                    {{ (int) $year ===
                       (int) $availableYear
                       ? 'selected'
                       : '' }}>
                    {{ $availableYear }}
                </option>
            @endforeach

        </select>

    </form>

</div>

<div class="allowance-panel">

    <div class="allowance-table-wrapper">

        <table class="allowance-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Entitlement</th>
                    <th>Carried Over</th>
                    <th>Adjustment</th>
                    <th>Total</th>
                    <th>Used</th>
                    <th>Remaining</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($employees as $employee)

                    @php
                        $allowance =
                            $employee->holidayAllowances->first();

                        $entitlement =
                            old(
                                'employee_id'
                            ) == $employee->id
                                ? old(
                                    'annual_entitlement',
                                    28
                                )
                                : optional(
                                    $allowance
                                )->annual_entitlement ?? 28;

                        $carriedOver =
                            old(
                                'employee_id'
                            ) == $employee->id
                                ? old(
                                    'carried_over',
                                    0
                                )
                                : optional(
                                    $allowance
                                )->carried_over ?? 0;

                        $adjustment =
                            old(
                                'employee_id'
                            ) == $employee->id
                                ? old(
                                    'adjustment',
                                    0
                                )
                                : optional(
                                    $allowance
                                )->adjustment ?? 0;

                        $notes =
                            old(
                                'employee_id'
                            ) == $employee->id
                                ? old('notes')
                                : optional(
                                    $allowance
                                )->notes;

                        $totalAllowance =
                            $allowance
                                ? $allowance->total_allowance
                                : (
                                    (float) $entitlement +
                                    (float) $carriedOver +
                                    (float) $adjustment
                                );

                        $used =
                            $allowance
                                ? $allowance->approved_days_used
                                : 0;

                        $remaining =
                            max(
                                0,
                                $totalAllowance - $used
                            );
                    @endphp

                    <tr>

                        <form method="POST"
                              action="{{ route(
                                  'company.hr.holidays.allowances.store'
                              ) }}">

                            @csrf

                            <input type="hidden"
                                   name="employee_id"
                                   value="{{ $employee->id }}">

                            <input type="hidden"
                                   name="holiday_year"
                                   value="{{ $year }}">

                            <td>
                                <div class="allowance-employee-name">
                                    {{ $employee->full_name }}
                                </div>

                                <div class="allowance-employee-id">
                                    {{ $employee->employee_number }}
                                </div>
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="annual_entitlement"
                                    class="allowance-input"
                                    min="0"
                                    max="366"
                                    step="0.5"
                                    value="{{ $entitlement }}"
                                    required
                                >
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="carried_over"
                                    class="allowance-input"
                                    min="0"
                                    max="366"
                                    step="0.5"
                                    value="{{ $carriedOver }}"
                                >
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="adjustment"
                                    class="allowance-input"
                                    min="-366"
                                    max="366"
                                    step="0.5"
                                    value="{{ $adjustment }}"
                                >
                            </td>

                            <td class="allowance-number">
                                {{ number_format(
                                    $totalAllowance,
                                    2
                                ) }}
                            </td>

                            <td class="allowance-number">
                                {{ number_format(
                                    $used,
                                    2
                                ) }}
                            </td>

                            <td class="
                                allowance-number
                                {{ $remaining <= 5
                                    ? 'allowance-remaining-low'
                                    : 'allowance-remaining-good'
                                }}
                            ">
                                {{ number_format(
                                    $remaining,
                                    2
                                ) }}
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="notes"
                                    class="
                                        allowance-input
                                        allowance-notes
                                    "
                                    maxlength="2000"
                                    value="{{ $notes }}"
                                    placeholder="Optional notes"
                                >
                            </td>

                            <td>
                                <button type="submit"
                                        class="allowance-save">
                                    {{ $allowance
                                        ? 'Update'
                                        : 'Save'
                                    }}
                                </button>
                            </td>

                        </form>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9">
                            <div class="allowance-empty">
                                No active employees found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection