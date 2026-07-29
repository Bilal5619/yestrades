@extends('company.layouts.app')

@section('title', 'Onboarding Overview')

@section('content')

<style>
    .onboarding-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .onboarding-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .onboarding-header p {
        margin: 0;
        color: #aab4ca;
    }

    .onboarding-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .onboarding-stat-card {
        padding: 19px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .onboarding-stat-label {
        margin-bottom: 8px;
        color: #aab4ca;
        font-size: 13px;
    }

    .onboarding-stat-value {
        font-size: 27px;
        font-weight: 700;
    }

    .onboarding-filters,
    .onboarding-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .onboarding-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr auto;
        gap: 12px;
    }

    .onboarding-filter-grid input,
    .onboarding-filter-grid select {
        width: 100%;
        min-height: 41px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .onboarding-filter-button {
        min-height: 41px;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .onboarding-table-wrapper {
        overflow-x: auto;
    }

    .onboarding-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: collapse;
    }

    .onboarding-table th,
    .onboarding-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .onboarding-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .onboarding-progress {
        width: 180px;
    }

    .onboarding-progress-track {
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #11192a;
    }

    .onboarding-progress-bar {
        height: 100%;
        border-radius: 999px;
        background: #2f80ed;
    }

    .onboarding-progress-text {
        margin-top: 5px;
        color: #aab4ca;
        font-size: 12px;
    }

    .onboarding-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .onboarding-status-in_progress {
        background: rgba(47,128,237,0.16);
        color: #76adf2;
    }

    .onboarding-status-pending {
        background: rgba(255,179,71,0.16);
        color: #ffc46f;
    }

    .onboarding-status-completed {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .onboarding-status-cancelled {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .onboarding-view-button {
        display: inline-flex;
        padding: 7px 12px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .onboarding-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 900px) {
        .onboarding-stats {
            grid-template-columns: 1fr 1fr;
        }

        .onboarding-filter-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .onboarding-header {
            flex-direction: column;
        }

        .onboarding-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="onboarding-header">

    <div>
        <h1>Onboarding Overview</h1>

        <p>
            Manage employees currently completing their onboarding.
        </p>
    </div>

</div>

<div class="onboarding-stats">

    <div class="onboarding-stat-card">
        <div class="onboarding-stat-label">
            Total Onboarding
        </div>

        <div class="onboarding-stat-value">
            {{ $stats['total'] }}
        </div>
    </div>

    <div class="onboarding-stat-card">
        <div class="onboarding-stat-label">
            In Progress
        </div>

        <div class="onboarding-stat-value">
            {{ $stats['in_progress'] }}
        </div>
    </div>

    <div class="onboarding-stat-card">
        <div class="onboarding-stat-label">
            Completed
        </div>

        <div class="onboarding-stat-value">
            {{ $stats['completed'] }}
        </div>
    </div>

    <div class="onboarding-stat-card">
        <div class="onboarding-stat-label">
            With Pending Sections
        </div>

        <div class="onboarding-stat-value">
            {{ $stats['pending_sections'] }}
        </div>
    </div>

</div>

<div class="onboarding-filters">

    <form method="GET"
          action="{{ route('company.hr.onboarding.index') }}">

        <div class="onboarding-filter-grid">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Employee, number or job title">

            <select name="status">
                <option value="">All statuses</option>

                <option value="pending"
                    {{ request('status') === 'pending'
                        ? 'selected'
                        : '' }}>
                    Pending
                </option>

                <option value="in_progress"
                    {{ request('status') === 'in_progress'
                        ? 'selected'
                        : '' }}>
                    In Progress
                </option>

                <option value="completed"
                    {{ request('status') === 'completed'
                        ? 'selected'
                        : '' }}>
                    Completed
                </option>

                <option value="cancelled"
                    {{ request('status') === 'cancelled'
                        ? 'selected'
                        : '' }}>
                    Cancelled
                </option>
            </select>

            <button type="submit"
                    class="onboarding-filter-button">
                Filter
            </button>

        </div>

    </form>

</div>

<div class="onboarding-panel">

    <div class="onboarding-table-wrapper">

        <table class="onboarding-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Job Title</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Started</th>
                    <th>Assigned To</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($onboardings as $onboarding)

                    <tr>
                        <td>
                            {{ optional($onboarding->employee)->first_name }}
                            {{ optional($onboarding->employee)->last_name }}

                            <div style="
                                color:#aab4ca;
                                font-size:12px;
                            ">
                                {{ optional(
                                    $onboarding->employee
                                )->employee_number }}
                            </div>
                        </td>

                        <td>
                            {{ optional(
                                $onboarding->employee
                            )->job_title ?: '—' }}
                        </td>

                        <td>
                            <span class="
                                onboarding-status
                                onboarding-status-{{ $onboarding->status }}
                            ">
                                {{ ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $onboarding->status
                                    )
                                ) }}
                            </span>
                        </td>

                        <td>
                            <div class="onboarding-progress">

                                <div class="onboarding-progress-track">
                                    <div
                                        class="onboarding-progress-bar"
                                        style="
                                            width:
                                            {{ $onboarding->progress_percentage }}%;
                                        ">
                                    </div>
                                </div>

                                <div class="onboarding-progress-text">
                                    {{ $onboarding->progress_percentage }}%
                                </div>

                            </div>
                        </td>

                        <td>
                            {{ optional(
                                $onboarding->started_at
                            )->format('d M Y') ?? '—' }}
                        </td>

                        <td>
                          @if ($onboarding->assignedUser)
                            {{ trim(
                                $onboarding->assignedUser->first_name
                                . ' '
                                . $onboarding->assignedUser->last_name
                            ) }}
                        @else
                            Not assigned
                        @endif
                        </td>

                        <td>
                            <a href="{{ route(
                            'company.hr.onboarding.show',
                            $onboarding
                        ) }}"
                    class="onboarding-view-button">
                        View
                    </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="onboarding-empty">
                                No onboarding records found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $onboardings->links() }}
    </div>

</div>

@endsection