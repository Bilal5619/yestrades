@extends('company.layouts.app')

@section('title', 'Training History')

@section('content')

<style>
    .training-history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .training-history-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .training-history-header p {
        margin: 0;
        color: #aab4ca;
    }

    .training-history-back {
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

    .training-history-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .training-history-stat {
        padding: 19px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .training-history-stat-label {
        margin-bottom: 8px;
        color: #aab4ca;
        font-size: 13px;
    }

    .training-history-stat-value {
        font-size: 27px;
        font-weight: 700;
    }

    .training-history-filters,
    .training-history-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .training-history-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 12px;
    }

    .training-history-filter-grid input,
    .training-history-filter-grid select {
        width: 100%;
        min-height: 41px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .training-history-filter-button {
        min-height: 41px;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .training-history-table-wrapper {
        overflow-x: auto;
    }

    .training-history-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .training-history-table th,
    .training-history-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .training-history-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .training-history-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .training-history-status-completed {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .training-history-status-expired {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .training-history-status-cancelled {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .training-history-view {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .training-history-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 1000px) {
        .training-history-filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 700px) {
        .training-history-header {
            flex-direction: column;
        }

        .training-history-stats,
        .training-history-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="training-history-header">

    <div>
        <h1>Training History</h1>

        <p>
            Review completed, expired and cancelled training records.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.training.index'
        ) }}"
       class="training-history-back">
        <i class="fas fa-arrow-left"></i>
        Training Overview
    </a>

</div>

<div class="training-history-stats">

    <div class="training-history-stat">
        <div class="training-history-stat-label">
            Completed
        </div>

        <div class="training-history-stat-value">
            {{ $stats['completed'] }}
        </div>
    </div>

    <div class="training-history-stat">
        <div class="training-history-stat-label">
            Expired
        </div>

        <div class="training-history-stat-value">
            {{ $stats['expired'] }}
        </div>
    </div>

    <div class="training-history-stat">
        <div class="training-history-stat-label">
            Cancelled
        </div>

        <div class="training-history-stat-value">
            {{ $stats['cancelled'] }}
        </div>
    </div>

    <div class="training-history-stat">
        <div class="training-history-stat-label">
            Certificates Uploaded
        </div>

        <div class="training-history-stat-value">
            {{ $stats['certificates'] }}
        </div>
    </div>

</div>

<div class="training-history-filters">

    <form method="GET"
          action="{{ route(
              'company.hr.training.history'
          ) }}">

        <div class="training-history-filter-grid">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Employee, course or provider">

            <select name="status">
                <option value="">All statuses</option>

                <option value="completed"
                    {{ request('status') === 'completed'
                        ? 'selected'
                        : '' }}>
                    Completed
                </option>

                <option value="expired"
                    {{ request('status') === 'expired'
                        ? 'selected'
                        : '' }}>
                    Expired
                </option>

                <option value="cancelled"
                    {{ request('status') === 'cancelled'
                        ? 'selected'
                        : '' }}>
                    Cancelled
                </option>
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

            <select name="year">
                <option value="">All years</option>

                @foreach ($years as $year)
                    <option value="{{ $year }}"
                        {{ (string) request('year') ===
                           (string) $year
                            ? 'selected'
                            : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="training-history-filter-button">
                Filter
            </button>

        </div>

    </form>

</div>

<div class="training-history-panel">

    <div class="training-history-table-wrapper">

        <table class="training-history-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Course</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Completed</th>
                    <th>Expiry</th>
                    <th>Certificate</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($records as $record)

                    <tr>
                        <td>
                            {{ optional(
                                $record->employee
                            )->first_name }}

                            {{ optional(
                                $record->employee
                            )->last_name }}

                            <div style="
                                color:#aab4ca;
                                font-size:12px;
                            ">
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
                            <span class="
                                training-history-status
                                training-history-status-{{ $record->status }}
                            ">
                                {{ $record->status_name }}
                            </span>
                        </td>

                        <td>
                            {{ optional(
                                $record->completed_at
                            )->format('d M Y') ?? '—' }}
                        </td>

                        <td>
                            {{ optional(
                                $record->expiry_date
                            )->format('d M Y') ?? '—' }}
                        </td>

                        <td>
                            {{ $record->certificate_path
                                ? 'Uploaded'
                                : 'Not uploaded'
                            }}
                        </td>

                        <td>
                            <a href="{{ route(
                                    'company.hr.training.records.show',
                                    $record
                                ) }}"
                               class="training-history-view">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="8">
                            <div class="training-history-empty">
                                No training-history records found.
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