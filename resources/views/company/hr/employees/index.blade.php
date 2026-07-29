@extends('company.layouts.app')

@section('title', 'View Employees')

@section('content')

<style>
    .employee-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .employee-page-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .employee-page-header p {
        margin: 0;
        color: #aab4ca;
    }

    .employee-add-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
        white-space: nowrap;
    }

    .employee-filter-card {
        margin-bottom: 22px;
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .employee-filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .employee-filter-group label {
        display: block;
        margin-bottom: 7px;
        color: #aab4ca;
        font-size: 13px;
    }

    .employee-filter-control {
        width: 100%;
        height: 43px;
        padding: 0 12px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    .employee-filter-button {
        height: 43px;
        padding: 0 17px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .employee-clear-button {
        display: inline-flex;
        height: 43px;
        align-items: center;
        padding: 0 15px;
        border-radius: 6px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .employee-table-card {
        overflow: hidden;
        border-radius: 10px;
        background: #1e2a44;
    }

    .employee-table-wrapper {
        overflow-x: auto;
    }

    .employee-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .employee-table th,
    .employee-table td {
        padding: 14px 13px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        vertical-align: middle;
    }

    .employee-table th {
        background: #172036;
        color: #aab4ca;
        font-size: 13px;
    }

    .employee-name-wrapper {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .employee-avatar {
        display: flex;
        width: 39px;
        height: 39px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 50%;
        background: #2878dc;
        color: #ffffff;
        font-weight: 700;
    }

    .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .employee-name {
        font-weight: 600;
    }

    .employee-email {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .employee-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        text-transform: capitalize;
    }

    .employee-status-active {
        background: rgba(46, 191, 114, 0.16);
        color: #59dc96;
    }

    .employee-status-onboarding {
        background: rgba(40, 120, 220, 0.18);
        color: #70adff;
    }

    .employee-status-warning {
        background: rgba(243, 166, 58, 0.17);
        color: #ffc46d;
    }

    .employee-status-disabled {
        background: rgba(236, 87, 98, 0.17);
        color: #ff7c86;
    }

    .employee-document-status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 14px;
        background: rgba(170, 180, 202, 0.12);
        color: #aab4ca;
        font-size: 12px;
    }

    .employee-action-button {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .employee-empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #aab4ca;
    }

    .employee-pagination {
        margin-top: 20px;
    }

    @media (max-width: 1100px) {
        .employee-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .employee-page-header {
            flex-direction: column;
        }

        .employee-filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="employee-page-header">
    <div>
        <h1>View Employees</h1>

        <p>
            View and manage employee records, employment details
            and document status.
        </p>
    </div>

    @if (auth('company')->user()->hasPermission('employees.create'))
        <a href="{{ route('company.hr.employees.create') }}"
   class="employee-add-button">
            <i class="fas fa-user-plus"></i>
            Add Employee
        </a>
    @endif
</div>

<div class="employee-filter-card">

    <form method="GET"
          action="{{ route('company.hr.employees.index') }}"
          class="employee-filter-form">

        <div class="employee-filter-group">
            <label for="search">
                Search
            </label>

            <input
                id="search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="employee-filter-control"
                placeholder="Name, employee ID, email or job title"
            >
        </div>

        <div class="employee-filter-group">
            <label for="department">
                Department
            </label>

            <select
                id="department"
                name="department"
                class="employee-filter-control"
            >
                <option value="">All departments</option>

                @foreach ($departments as $department)
                    <option
                        value="{{ $department }}"
                        {{ request('department') === $department
                            ? 'selected'
                            : ''
                        }}
                    >
                        {{ $department }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="employee-filter-group">
            <label for="employment_type">
                Employment type
            </label>

            <select
                id="employment_type"
                name="employment_type"
                class="employee-filter-control"
            >
                <option value="">All types</option>
                <option value="full_time"
                    {{ request('employment_type') === 'full_time'
                        ? 'selected'
                        : ''
                    }}>
                    Full-time
                </option>
                <option value="part_time"
                    {{ request('employment_type') === 'part_time'
                        ? 'selected'
                        : ''
                    }}>
                    Part-time
                </option>
                <option value="temporary"
                    {{ request('employment_type') === 'temporary'
                        ? 'selected'
                        : ''
                    }}>
                    Temporary
                </option>
                <option value="contractor"
                    {{ request('employment_type') === 'contractor'
                        ? 'selected'
                        : ''
                    }}>
                    Contractor
                </option>
                <option value="apprentice"
                    {{ request('employment_type') === 'apprentice'
                        ? 'selected'
                        : ''
                    }}>
                    Apprentice
                </option>
                <option value="zero_hours"
                    {{ request('employment_type') === 'zero_hours'
                        ? 'selected'
                        : ''
                    }}>
                    Zero-hours
                </option>
            </select>
        </div>

        <div class="employee-filter-group">
            <label for="employment_status">
                Status
            </label>

            <select
                id="employment_status"
                name="employment_status"
                class="employee-filter-control"
            >
                <option value="">All statuses</option>
                <option value="onboarding"
                    {{ request('employment_status') === 'onboarding'
                        ? 'selected'
                        : ''
                    }}>
                    Onboarding
                </option>
                <option value="active"
                    {{ request('employment_status') === 'active'
                        ? 'selected'
                        : ''
                    }}>
                    Active
                </option>
                <option value="on_leave"
                    {{ request('employment_status') === 'on_leave'
                        ? 'selected'
                        : ''
                    }}>
                    On leave
                </option>
                <option value="suspended"
                    {{ request('employment_status') === 'suspended'
                        ? 'selected'
                        : ''
                    }}>
                    Suspended
                </option>
                <option value="notice_period"
                    {{ request('employment_status') === 'notice_period'
                        ? 'selected'
                        : ''
                    }}>
                    Notice period
                </option>
                <option value="left_company"
                    {{ request('employment_status') === 'left_company'
                        ? 'selected'
                        : ''
                    }}>
                    Left company
                </option>
            </select>
        </div>

        <div style="display:flex; gap:8px;">
            <button type="submit"
                    class="employee-filter-button">
                Filter
            </button>

            <a href="{{ route('company.hr.employees.index') }}"
               class="employee-clear-button">
                Clear
            </a>
        </div>

    </form>

</div>

<div class="employee-table-card">

    <div class="employee-table-wrapper">

        <table class="employee-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Employee ID</th>
                    <th>Job Title</th>
                    <th>Department</th>
                    <th>Employment Type</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Right to Work</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($employees as $employee)

                    @php
                        $statusClass = match ($employee->employment_status) {
                            'active' => 'employee-status-active',
                            'onboarding' => 'employee-status-onboarding',
                            'on_leave',
                            'notice_period' => 'employee-status-warning',
                            default => 'employee-status-disabled',
                        };
                    @endphp

                    <tr>

                        <td>
                            <div class="employee-name-wrapper">

                                <div class="employee-avatar">
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

                                <div>
                                    <div class="employee-name">
                                        {{ $employee->full_name }}
                                    </div>

                                    <div class="employee-email">
                                        {{ $employee->work_email
                                            ?: $employee->personal_email
                                            ?: 'No email'
                                        }}
                                    </div>
                                </div>

                            </div>
                        </td>

                        <td>
                            {{ $employee->employee_number }}
                        </td>

                        <td>
                            {{ $employee->job_title ?: 'Not assigned' }}
                        </td>

                        <td>
                            {{ $employee->department ?: 'Not assigned' }}
                        </td>

                        <td>
                            {{ $employee->employment_type
                                ? ucwords(str_replace(
                                    '_',
                                    ' ',
                                    $employee->employment_type
                                ))
                                : 'Not assigned'
                            }}
                        </td>

                        <td>
                            {{ $employee->start_date
                                ? $employee->start_date->format('d M Y')
                                : 'Not set'
                            }}
                        </td>

                        <td>
                            <span class="employee-status {{ $statusClass }}">
                                {{ ucwords(str_replace(
                                    '_',
                                    ' ',
                                    $employee->employment_status
                                )) }}
                            </span>
                        </td>

                        <td>
                            @if ($employee->expired_documents_count > 0)
                            <span class="employee-document-status"
                                style="color:#ff7c86;">
                                {{ $employee->expired_documents_count }} expired
                            </span>

                        @elseif ($employee->missing_documents_count > 0)
                            <span class="employee-document-status"
                                style="color:#ffc46d;">
                                {{ $employee->missing_documents_count }} missing
                            </span>

                        @elseif ($employee->expiring_documents_count > 0)
                            <span class="employee-document-status"
                                style="color:#ffc46d;">
                                {{ $employee->expiring_documents_count }} expiring
                            </span>

                        @else
                            <span class="employee-document-status"
                                style="color:#59dc96;">
                                Complete
                            </span>
                        @endif
                        </td>

                        <td>
                            <span class="employee-document-status">
                                Not checked
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('company.hr.employees.show', $employee) }}"
                                class="employee-action-button">
                                    View
                                </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="10">
                            <div class="employee-empty-state">
                                <i class="fas fa-users"
                                   style="font-size:30px; margin-bottom:12px;">
                                </i>

                                <div>
                                    No employees have been added yet.
                                </div>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="employee-pagination">
    {{ $employees->links() }}
</div>

@endsection