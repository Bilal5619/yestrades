@extends('company.layouts.app')

@section('title', 'Archived Employees')

@section('content')

<style>
    .archived-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .archived-page-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .archived-page-header p {
        margin: 0;
        color: #aab4ca;
    }

    .archived-back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 16px;
        border-radius: 7px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .archived-message {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 7px;
    }

    .archived-success {
        background: rgba(46, 191, 114, 0.15);
        color: #59dc96;
    }

    .archived-error {
        background: rgba(236, 87, 98, 0.15);
        color: #ff929a;
    }

    .archived-summary-card {
        margin-bottom: 22px;
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .archived-summary-label {
        color: #aab4ca;
        font-size: 14px;
    }

    .archived-summary-value {
        margin-top: 7px;
        font-size: 29px;
        font-weight: 700;
    }

    .archived-filter-card {
        margin-bottom: 22px;
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .archived-filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .archived-form-group label {
        display: block;
        margin-bottom: 7px;
        color: #aab4ca;
        font-size: 13px;
    }

    .archived-control {
        width: 100%;
        min-height: 43px;
        padding: 9px 12px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    .archived-filter-actions {
        display: flex;
        gap: 8px;
    }

    .archived-filter-button,
    .archived-clear-button {
        display: inline-flex;
        min-height: 43px;
        align-items: center;
        justify-content: center;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .archived-filter-button {
        background: #2878dc;
    }

    .archived-clear-button {
        background: #3a4967;
    }

    .archived-table-card {
        overflow: hidden;
        border-radius: 10px;
        background: #1e2a44;
    }

    .archived-table-wrapper {
        overflow-x: auto;
    }

    .archived-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .archived-table th,
    .archived-table td {
        padding: 14px 13px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        vertical-align: middle;
    }

    .archived-table th {
        background: #172036;
        color: #aab4ca;
        font-size: 13px;
    }

    .archived-name {
        font-weight: 600;
    }

    .archived-email {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .archived-status {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 14px;
        background: rgba(170,180,202,0.13);
        color: #c1c9d9;
        font-size: 12px;
    }

    .archived-document-alert {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        background: rgba(236,87,98,0.15);
        color: #ff7c86;
        font-size: 12px;
    }

    .archived-document-complete {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        background: rgba(46,191,114,0.15);
        color: #59dc96;
        font-size: 12px;
    }

    .archived-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .archived-action-button {
        display: inline-flex;
        align-items: center;
        min-height: 36px;
        padding: 0 12px;
        border: 0;
        border-radius: 6px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .archived-view-button {
        background: #2878dc;
    }

    .archived-restore-button {
        background: #23985d;
    }

    .archived-empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #aab4ca;
    }

    .archived-pagination {
        margin-top: 20px;
    }

    @media (max-width: 800px) {
        .archived-page-header {
            flex-direction: column;
        }

        .archived-filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="archived-page-header">

    <div>
        <h1>Archived Employees</h1>

        <p>
            View former or archived employee records without
            permanently deleting their information.
        </p>
    </div>

    <a href="{{ route('company.hr.employees.index') }}"
       class="archived-back-button">
        <i class="fas fa-arrow-left"></i>
        View Employees
    </a>

</div>

@if (session('success'))
    <div class="archived-message archived-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="archived-message archived-error">
        {{ $errors->first() }}
    </div>
@endif

<div class="archived-summary-card">
    <div class="archived-summary-label">
        Total archived employees
    </div>

    <div class="archived-summary-value">
        {{ $employees->total() }}
    </div>
</div>

<div class="archived-filter-card">

    <form method="GET"
          action="{{ route(
              'company.hr.employees.archived'
          ) }}"
          class="archived-filter-form">

        <div class="archived-form-group">
            <label for="search">
                Search employee
            </label>

            <input
                id="search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="archived-control"
                placeholder="Name, employee ID, email or job title"
            >
        </div>

        <div class="archived-form-group">
            <label for="department">
                Department
            </label>

            <select
                id="department"
                name="department"
                class="archived-control"
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

        <div class="archived-filter-actions">
            <button type="submit"
                    class="archived-filter-button">
                Filter
            </button>

            <a href="{{ route(
                    'company.hr.employees.archived'
                ) }}"
               class="archived-clear-button">
                Clear
            </a>
        </div>

    </form>

</div>

<div class="archived-table-card">

    <div class="archived-table-wrapper">

        <table class="archived-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Employee ID</th>
                    <th>Job Title</th>
                    <th>Department</th>
                    <th>Start Date</th>
                    <th>Leaving Date</th>
                    <th>Documents</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($employees as $employee)

                    @php
                        $documentAlerts =
                            $employee->missing_documents_count +
                            $employee->expiring_documents_count +
                            $employee->expired_documents_count;
                    @endphp

                    <tr>

                        <td>
                            <div class="archived-name">
                                {{ $employee->full_name }}
                            </div>

                            <div class="archived-email">
                                {{ $employee->work_email
                                    ?: $employee->personal_email
                                    ?: 'No email'
                                }}
                            </div>
                        </td>

                        <td>
                            {{ $employee->employee_number }}
                        </td>

                        <td>
                            {{ $employee->job_title
                                ?: 'Not assigned'
                            }}
                        </td>

                        <td>
                            {{ $employee->department
                                ?: 'Not assigned'
                            }}
                        </td>

                        <td>
                            {{ $employee->start_date
                                ? $employee->start_date->format('d M Y')
                                : 'Not set'
                            }}
                        </td>

                        <td>
                            {{ $employee->leaving_date
                                ? $employee->leaving_date->format('d M Y')
                                : 'Not set'
                            }}
                        </td>

                        <td>
                            @if ($documentAlerts > 0)
                                <span class="archived-document-alert">
                                    {{ $documentAlerts }}
                                    alert{{ $documentAlerts === 1 ? '' : 's' }}
                                </span>
                            @else
                                <span class="archived-document-complete">
                                    Complete
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="archived-status">
                                Archived
                            </span>
                        </td>

                        <td>
                            <div class="archived-actions">

                                <a href="{{ route(
                                        'company.hr.employees.show',
                                        $employee
                                    ) }}"
                                   class="
                                       archived-action-button
                                       archived-view-button
                                   ">
                                    View
                                </a>

                                @if (
                                    auth('company')->user()
                                        ->hasPermission('employees.edit')
                                )
                                    <form method="POST"
                                          action="{{ route(
                                              'company.hr.employees.restore',
                                              $employee
                                          ) }}">

                                        @csrf

                                        <button type="submit"
                                                class="
                                                    archived-action-button
                                                    archived-restore-button
                                                ">
                                            Restore
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9">
                            <div class="archived-empty-state">
                                <i class="fas fa-archive"
                                   style="
                                       display:block;
                                       margin-bottom:12px;
                                       font-size:32px;
                                   ">
                                </i>

                                No archived employees found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="archived-pagination">
    {{ $employees->links() }}
</div>

@endsection      