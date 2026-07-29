@extends('company.layouts.app')

@section('title', 'Employee Training')

@section('content')

<style>
    .record-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .record-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .record-header p {
        margin: 0;
        color: #aab4ca;
    }

    .record-actions {
        display: flex;
        gap: 10px;
    }

    .record-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .record-primary {
        background: #2878dc;
    }

    .record-secondary {
        background: #3a4967;
    }

    .record-filters,
    .record-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .record-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 12px;
    }

    .record-filter-grid input,
    .record-filter-grid select {
        width: 100%;
        min-height: 41px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .record-filter-button {
        min-height: 41px;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .record-table-wrapper {
        overflow-x: auto;
    }

    .record-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .record-table th,
    .record-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .record-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .record-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .record-status-not_started {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .record-status-scheduled {
        background: rgba(41,120,220,0.18);
        color: #75aff7;
    }

    .record-status-in_progress {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .record-status-completed {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .record-status-expired,
    .record-status-cancelled {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .record-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 7px;
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .record-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 900px) {
        .record-header {
            flex-direction: column;
        }

        .record-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="record-header">

    <div>
        <h1>Employee Training</h1>

        <p>
            View and manage training assigned to employees.
        </p>
    </div>

    <div class="record-actions">

        <a href="{{ route(
                'company.hr.training.index'
            ) }}"
           class="record-button record-secondary">
            <i class="fas fa-arrow-left"></i>
            Training Overview
        </a>

        @if (
            auth('company')->user()
                ->hasPermission('training.create')
        )
            <a href="{{ route(
                    'company.hr.training.records.create'
                ) }}"
               class="record-button record-primary">
                <i class="fas fa-plus"></i>
                Assign Training
            </a>
        @endif

    </div>

</div>

@if (session('success'))
    <div class="record-alert">
        {{ session('success') }}
    </div>
@endif

<div class="record-filters">

    <form method="GET"
          action="{{ route(
              'company.hr.training.records.index'
          ) }}">

        <div class="record-filter-grid">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Employee, course or provider">

            <select name="status">
                <option value="">All statuses</option>

                @foreach (
                    [
                        'not_started' => 'Not Started',
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ] as $value => $label
                )
                    <option value="{{ $value }}"
                        {{ request('status') === $value
                            ? 'selected'
                            : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="course_id">
                <option value="">All courses</option>

                @foreach ($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ (int) request('course_id') ===
                           (int) $course->id
                            ? 'selected'
                            : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="record-filter-button">
                Filter
            </button>

        </div>

    </form>

</div>

<div class="record-panel">

    <div class="record-table-wrapper">

        <table class="record-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Course</th>
                    <th>Provider</th>
                    <th>Scheduled Date</th>
                    <th>Status</th>
                    <th>Cost</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($records as $record)

                    <tr>
                        <td>
                            {{ optional($record->employee)->first_name }}
                            {{ optional($record->employee)->last_name }}

                            <div style="color:#aab4ca; font-size:12px;">
                                {{ optional(
                                    $record->employee
                                )->employee_number }}
                            </div>
                        </td>

                        <td>
                            {{ optional(
                                $record->trainingCourse
                            )->name ?? 'Unknown course' }}
                        </td>

                        <td>
                            {{ $record->provider_name ?: '—' }}
                        </td>

                        <td>
                            {{ optional(
                                $record->scheduled_date
                            )->format('d M Y') ?? '—' }}
                        </td>

                        <td>
                            <span class="
                                record-status
                                record-status-{{ $record->status }}
                            ">
                                {{ $record->status_name }}
                            </span>
                        </td>

                        <td>
                            {{ $record->cost !== null
                                ? '£' . number_format(
                                    $record->cost,
                                    2
                                )
                                : '—'
                            }}
                        </td>
                        <td>
                        <a href="{{ route(
                                'company.hr.training.records.show',
                                $record
                            ) }}"
                        style="
                            display:inline-flex;
                            padding:7px 11px;
                            border-radius:6px;
                            background:#2878dc;
                            color:#ffffff;
                            text-decoration:none;
                        ">
                            View
                        </a>
                    </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="record-empty">
                                No training records found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $records->links() }}
    </div>

</div>

@endsection