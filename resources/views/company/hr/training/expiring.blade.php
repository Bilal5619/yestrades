@extends('company.layouts.app')

@section('title', 'Expiring Training')

@section('content')

<style>
    .expiring-training-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .expiring-training-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .expiring-training-header p {
        margin: 0;
        color: #aab4ca;
    }

    .expiring-training-back {
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

    .expiring-training-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .expiring-training-stat {
        padding: 19px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .expiring-training-label {
        margin-bottom: 8px;
        color: #aab4ca;
        font-size: 13px;
    }

    .expiring-training-value {
        font-size: 27px;
        font-weight: 700;
    }

    .expiring-training-filters,
    .expiring-training-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .expiring-training-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 12px;
    }

    .expiring-training-filter-grid input,
    .expiring-training-filter-grid select {
        width: 100%;
        min-height: 41px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .expiring-training-filter-button {
        min-height: 41px;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .expiring-training-table-wrapper {
        overflow-x: auto;
    }

    .expiring-training-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .expiring-training-table th,
    .expiring-training-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .expiring-training-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .expiry-warning {
        color: #ffc46d;
        font-weight: 600;
    }

    .expiry-danger {
        color: #ff8a92;
        font-weight: 600;
    }

    .expiring-training-view {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .expiring-training-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 1000px) {
        .expiring-training-filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 700px) {
        .expiring-training-header {
            flex-direction: column;
        }

        .expiring-training-stats,
        .expiring-training-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="expiring-training-header">

    <div>
        <h1>Expiring Training</h1>

        <p>
            Review training certificates that are expiring or
            have already expired.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.training.index'
        ) }}"
       class="expiring-training-back">
        <i class="fas fa-arrow-left"></i>
        Training Overview
    </a>

</div>

<div class="expiring-training-stats">

    <div class="expiring-training-stat">
        <div class="expiring-training-label">
            Expiring Within 30 Days
        </div>

        <div class="expiring-training-value">
            {{ $stats['within_30_days'] }}
        </div>
    </div>

    <div class="expiring-training-stat">
        <div class="expiring-training-label">
            Expiring Within 60 Days
        </div>

        <div class="expiring-training-value">
            {{ $stats['within_60_days'] }}
        </div>
    </div>

    <div class="expiring-training-stat">
        <div class="expiring-training-label">
            Expiring Within 90 Days
        </div>

        <div class="expiring-training-value">
            {{ $stats['within_90_days'] }}
        </div>
    </div>

    <div class="expiring-training-stat">
        <div class="expiring-training-label">
            Already Expired
        </div>

        <div class="expiring-training-value">
            {{ $stats['expired'] }}
        </div>
    </div>

</div>

<div class="expiring-training-filters">

    <form method="GET"
          action="{{ route(
              'company.hr.training.expiring'
          ) }}">

        <div class="expiring-training-filter-grid">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Employee, number or course">

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

            <select name="department">
                <option value="">All departments</option>

                @foreach ($departments as $department)
                    <option value="{{ $department }}"
                        {{ request('department') === $department
                            ? 'selected'
                            : '' }}>
                        {{ $department }}
                    </option>
                @endforeach
            </select>

            <select name="expiry_period">
                <option value="30_days"
                    {{ $expiryPeriod === '30_days'
                        ? 'selected'
                        : '' }}>
                    Within 30 Days
                </option>

                <option value="60_days"
                    {{ $expiryPeriod === '60_days'
                        ? 'selected'
                        : '' }}>
                    Within 60 Days
                </option>

                <option value="90_days"
                    {{ $expiryPeriod === '90_days'
                        ? 'selected'
                        : '' }}>
                    Within 90 Days
                </option>

                <option value="expired"
                    {{ $expiryPeriod === 'expired'
                        ? 'selected'
                        : '' }}>
                    Already Expired
                </option>
            </select>

            <button type="submit"
                    class="expiring-training-filter-button">
                Filter
            </button>

        </div>

    </form>

</div>

<div class="expiring-training-panel">

    <div class="expiring-training-table-wrapper">

        <table class="expiring-training-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Course</th>
                    <th>Department</th>
                    <th>Completed</th>
                    <th>Expiry Date</th>
                    <th>Certificate</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($records as $record)

                    @php
                        $isExpired =
                            $record->expiry_date &&
                            $record->expiry_date->lt(today());
                    @endphp

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
                            {{ optional(
                                $record->employee
                            )->department ?: '—' }}
                        </td>

                        <td>
                            {{ optional(
                                $record->completed_at
                            )->format('d M Y') ?? '—' }}
                        </td>

                        <td class="{{ $isExpired
                            ? 'expiry-danger'
                            : 'expiry-warning'
                        }}">
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
                               class="expiring-training-view">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="expiring-training-empty">
                                No training records found for this
                                expiry period.
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