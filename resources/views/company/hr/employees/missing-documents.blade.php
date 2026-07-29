@extends('company.layouts.app')

@section('title', 'Missing Documents')

@section('content')

<style>
    .missing-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .missing-page-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .missing-page-header p {
        margin: 0;
        color: #aab4ca;
    }

    .missing-back-button {
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

    .missing-filter-card {
        margin-bottom: 22px;
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .missing-filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .missing-form-group label {
        display: block;
        margin-bottom: 7px;
        color: #aab4ca;
        font-size: 13px;
    }

    .missing-control {
        width: 100%;
        min-height: 43px;
        padding: 9px 12px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    .missing-filter-actions {
        display: flex;
        gap: 8px;
    }

    .missing-filter-button,
    .missing-clear-button {
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

    .missing-filter-button {
        background: #2878dc;
    }

    .missing-clear-button {
        background: #3a4967;
    }

    .missing-summary-card {
        margin-bottom: 22px;
        padding: 20px;
        border-radius: 10px;
        background: rgba(236, 87, 98, 0.12);
        border: 1px solid rgba(236, 87, 98, 0.25);
    }

    .missing-summary-value {
        margin-top: 6px;
        font-size: 28px;
        font-weight: 700;
        color: #ff929a;
    }

    .missing-table-card {
        overflow: hidden;
        border-radius: 10px;
        background: #1e2a44;
    }

    .missing-table-wrapper {
        overflow-x: auto;
    }

    .missing-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: collapse;
    }

    .missing-table th,
    .missing-table td {
        padding: 14px 13px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        vertical-align: middle;
    }

    .missing-table th {
        background: #172036;
        color: #aab4ca;
        font-size: 13px;
    }

    .missing-employee-name {
        font-weight: 600;
    }

    .missing-employee-email {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .missing-count {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 14px;
        background: rgba(236, 87, 98, 0.16);
        color: #ff7c86;
        font-size: 12px;
        font-weight: 600;
    }

    .missing-document-list {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .missing-document-item {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        background: rgba(243, 166, 58, 0.15);
        color: #ffc46d;
        font-size: 12px;
    }

    .right-to-work-missing {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        background: rgba(236, 87, 98, 0.16);
        color: #ff7c86;
        font-size: 12px;
    }

    .right-to-work-complete {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        background: rgba(46, 191, 114, 0.16);
        color: #59dc96;
        font-size: 12px;
    }

    .missing-action-button {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .missing-empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #aab4ca;
    }

    .missing-pagination {
        margin-top: 20px;
    }

    @media (max-width: 800px) {
        .missing-page-header {
            flex-direction: column;
        }

        .missing-filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="missing-page-header">

    <div>
        <h1>Missing Documents</h1>

        <p>
            Employees who have required documents that have not
            yet been uploaded.
        </p>
    </div>

    <a href="{{ route('company.hr.employees.index') }}"
       class="missing-back-button">
        <i class="fas fa-arrow-left"></i>
        View Employees
    </a>

</div>

<div class="missing-summary-card">
    <div style="color:#aab4ca;">
        Employees with missing documents
    </div>

    <div class="missing-summary-value">
        {{ $employees->total() }}
    </div>
</div>

<div class="missing-filter-card">

    <form method="GET"
          action="{{ route(
              'company.hr.employees.missing-documents'
          ) }}"
          class="missing-filter-form">

        <div class="missing-form-group">
            <label for="search">Search employee</label>

            <input
                id="search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="missing-control"
                placeholder="Name, employee ID, job title or department"
            >
        </div>

        <div class="missing-form-group">
            <label for="department">Department</label>

            <select
                id="department"
                name="department"
                class="missing-control"
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

        <div class="missing-filter-actions">
            <button type="submit"
                    class="missing-filter-button">
                Filter
            </button>

            <a href="{{ route(
                    'company.hr.employees.missing-documents'
                ) }}"
               class="missing-clear-button">
                Clear
            </a>
        </div>

    </form>

</div>

<div class="missing-table-card">

    <div class="missing-table-wrapper">

        <table class="missing-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Employee ID</th>
                    <th>Job Title</th>
                    <th>Department</th>
                    <th>Missing</th>
                    <th>Missing Documents</th>
                    <th>Right to Work</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($employees as $employee)

                    @php
                        $rightToWorkMissing = $employee
                            ->documents
                            ->contains(function ($document) {
                                return optional(
                                    $document->documentType
                                )->slug ===
                                    'right-to-work-evidence';
                            });
                    @endphp

                    <tr>

                        <td>
                            <div class="missing-employee-name">
                                {{ $employee->full_name }}
                            </div>

                            <div class="missing-employee-email">
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
                            <span class="missing-count">
                                {{ $employee
                                    ->missing_documents_count }}
                                missing
                            </span>
                        </td>

                        <td>
                            <div class="missing-document-list">

                                @foreach (
                                    $employee->documents
                                    as $document
                                )
                                    <span class="missing-document-item">
                                        {{ optional(
                                            $document->documentType
                                        )->name
                                            ?? $document->title
                                        }}
                                    </span>
                                @endforeach

                            </div>
                        </td>

                        <td>
                            @if ($rightToWorkMissing)
                                <span class="right-to-work-missing">
                                    Missing
                                </span>
                            @else
                                <span class="right-to-work-complete">
                                    Not missing
                                </span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route(
                                    'company.hr.employees.documents.index',
                                    $employee
                                ) }}"
                               class="missing-action-button">
                                Open Documents
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8">
                            <div class="missing-empty-state">
                                <i class="fas fa-check-circle"
                                   style="
                                       display:block;
                                       margin-bottom:12px;
                                       font-size:32px;
                                       color:#59dc96;
                                   ">
                                </i>

                                No employees have missing required
                                documents.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="missing-pagination">
    {{ $employees->links() }}
</div>

@endsection