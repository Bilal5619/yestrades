@extends('company.layouts.app')

@section('title', 'Training Overview')

@section('content')

<style>
    .training-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .training-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .training-header p {
        margin: 0;
        color: #aab4ca;
    }

    .training-header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .training-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .training-button-primary {
        background: #2878dc;
    }

    .training-button-secondary {
        background: #3a4967;
    }

    .training-stat-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 24px;
    }

    .training-stat-card {
        padding: 19px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .training-stat-label {
        margin-bottom: 8px;
        color: #aab4ca;
        font-size: 13px;
    }

    .training-stat-value {
        font-size: 27px;
        font-weight: 700;
    }

    .training-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .training-panel {
        padding: 21px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .training-panel h2 {
        margin: 0 0 17px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 18px;
    }

    .training-table-wrapper {
        overflow-x: auto;
    }

    .training-table {
        width: 100%;
        min-width: 620px;
        border-collapse: collapse;
    }

    .training-table th,
    .training-table td {
        padding: 12px 9px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .training-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .training-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .training-status-scheduled {
        background: rgba(41,120,220,0.18);
        color: #75aff7;
    }

    .training-status-completed {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .training-status-expiring {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .training-status-expired {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .training-empty {
        padding: 28px 10px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 1300px) {
        .training-stat-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .training-header {
            flex-direction: column;
        }

        .training-stat-grid,
        .training-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="training-header">

    <div>
        <h1>Training Overview</h1>

        <p>
            Manage employee training, certificates and expiry dates.
        </p>
    </div>

    <div class="training-header-actions">

        @if (
            auth('company')->user()
                ->hasPermission('training.create')
        )
           <a href="{{ route(
        'company.hr.training.records.create'
            ) }}"
        class="
            training-button
            training-button-primary
        ">
            <i class="fas fa-plus"></i>
            Assign Training
        </a>
        @endif
        @if (
    auth('company')->user()
        ->hasPermission('training.view')
)
    <a href="{{ route(
            'company.hr.training.expiring'
        ) }}"
       class="
           training-button
           training-button-secondary
       ">
        <i class="fas fa-clock"></i>
        Expiring Training
    </a>
@endif

@if (
    auth('company')->user()
        ->hasPermission('training.view')
)
    <a href="{{ route(
            'company.hr.training.history'
        ) }}"
       class="
           training-button
           training-button-secondary
       ">
        <i class="fas fa-history"></i>
        Training History
    </a>
@endif



        @if (
            auth('company')->user()
                ->hasPermission('training.view')
        )
            <a href="{{ route(
                    'company.hr.training.records.index'
                ) }}"
            class="
                training-button
                training-button-secondary
            ">
                <i class="fas fa-list"></i>
                Employee Training
            </a>
        @endif

        @if (
            auth('company')->user()
                ->hasPermission('training-courses.manage')
        )
            <a href="{{ route(
        'company.hr.training.courses.index'
            ) }}"
        class="
            training-button
            training-button-secondary
        ">
            <i class="fas fa-book"></i>
            Training Courses
        </a>
        @endif

    </div>

</div>

<div class="training-stat-grid">

    <div class="training-stat-card">
        <div class="training-stat-label">
            Total Records
        </div>

        <div class="training-stat-value">
            {{ $stats['total_records'] }}
        </div>
    </div>

    <div class="training-stat-card">
        <div class="training-stat-label">
            Due Soon
        </div>

        <div class="training-stat-value">
            {{ $stats['due_soon'] }}
        </div>
    </div>

    <div class="training-stat-card">
        <div class="training-stat-label">
            Expired
        </div>

        <div class="training-stat-value">
            {{ $stats['expired'] }}
        </div>
    </div>

    <div class="training-stat-card">
        <div class="training-stat-label">
            Scheduled
        </div>

        <div class="training-stat-value">
            {{ $stats['scheduled'] }}
        </div>
    </div>

    <div class="training-stat-card">
        <div class="training-stat-label">
            Completed This Year
        </div>

        <div class="training-stat-value">
            {{ $stats['completed_this_year'] }}
        </div>
    </div>

    <div class="training-stat-card">
        <div class="training-stat-label">
            Active Courses
        </div>

        <div class="training-stat-value">
            {{ $stats['active_courses'] }}
        </div>
    </div>

</div>

<div class="training-grid">

    <div class="training-panel">

        <h2>Training Due Soon</h2>

        <div class="training-table-wrapper">

            <table class="training-table">

                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Course</th>
                        <th>Expiry</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($dueSoonRecords as $record)

                        <tr>
                            <td>
                                {{ optional(
                                    $record->employee
                                )->first_name }}

                                {{ optional(
                                    $record->employee
                                )->last_name }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->trainingCourse
                                )->name ?? 'Unknown course' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->expiry_date
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                <span class="
                                    training-status
                                    training-status-expiring
                                ">
                                    Due Soon
                                </span>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                <div class="training-empty">
                                    No training is due in the next 30 days.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="training-panel">

        <h2>Expired Training</h2>

        <div class="training-table-wrapper">

            <table class="training-table">

                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Course</th>
                        <th>Expired</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($expiredRecords as $record)

                        <tr>
                            <td>
                                {{ optional(
                                    $record->employee
                                )->first_name }}

                                {{ optional(
                                    $record->employee
                                )->last_name }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->trainingCourse
                                )->name ?? 'Unknown course' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->expiry_date
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                <span class="
                                    training-status
                                    training-status-expired
                                ">
                                    Expired
                                </span>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                <div class="training-empty">
                                    No expired training records.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="training-panel">

        <h2>Scheduled Training</h2>

        <div class="training-table-wrapper">

            <table class="training-table">

                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Course</th>
                        <th>Scheduled Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($scheduledRecords as $record)

                        <tr>
                            <td>
                                {{ optional(
                                    $record->employee
                                )->first_name }}

                                {{ optional(
                                    $record->employee
                                )->last_name }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->trainingCourse
                                )->name ?? 'Unknown course' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->scheduled_date
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                <span class="
                                    training-status
                                    training-status-scheduled
                                ">
                                    Scheduled
                                </span>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                <div class="training-empty">
                                    No scheduled training records.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="training-panel">

        <h2>Recently Completed</h2>

        <div class="training-table-wrapper">

            <table class="training-table">

                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Course</th>
                        <th>Completed</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($recentlyCompleted as $record)

                        <tr>
                            <td>
                                {{ optional(
                                    $record->employee
                                )->first_name }}

                                {{ optional(
                                    $record->employee
                                )->last_name }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->trainingCourse
                                )->name ?? 'Unknown course' }}
                            </td>

                            <td>
                                {{ optional(
                                    $record->completed_at
                                )->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                <span class="
                                    training-status
                                    training-status-completed
                                ">
                                    Completed
                                </span>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                <div class="training-empty">
                                    No recently completed training.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="training-panel">

        <h2>Missing Mandatory Training</h2>

        <div class="training-table-wrapper">

            <table class="training-table">

                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Employee Number</th>
                        <th>Department</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse (
                        $employeesMissingMandatoryTraining
                        as $employee
                    )

                        <tr>
                            <td>
                                {{ $employee->first_name }}
                                {{ $employee->last_name }}
                            </td>

                            <td>
                                {{ $employee->employee_number }}
                            </td>

                            <td>
                                {{ $employee->department ?: '—' }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="3">
                                <div class="training-empty">
                                    No missing mandatory training found.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection