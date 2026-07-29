@extends('company.layouts.app')

@section('title', 'Holiday Overview')

@section('content')

<style>
    .holiday-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .holiday-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .holiday-header p {
        margin: 0;
        color: #aab4ca;
    }

    .holiday-header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .holiday-primary-button,
    .holiday-secondary-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 16px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .holiday-primary-button {
        background: #2878dc;
    }

    .holiday-secondary-button {
        background: #3a4967;
    }

    .holiday-stat-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .holiday-stat-card {
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .holiday-stat-label {
        margin-bottom: 9px;
        color: #aab4ca;
        font-size: 13px;
    }

    .holiday-stat-value {
        font-size: 28px;
        font-weight: 700;
    }

    .holiday-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .holiday-panel {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .holiday-panel h2 {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 19px;
    }

    .holiday-table-wrapper {
        overflow-x: auto;
    }

    .holiday-table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
    }

    .holiday-table th,
    .holiday-table td {
        padding: 12px 10px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.07);
    }

    .holiday-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .holiday-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .holiday-status-approved {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .holiday-status-pending {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .holiday-low-balance {
        color: #ff7c86;
        font-weight: 600;
    }

    .holiday-empty {
        padding: 28px 10px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 1200px) {
        .holiday-stat-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .holiday-header {
            flex-direction: column;
        }

        .holiday-stat-grid,
        .holiday-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="holiday-header">

    <div>
        <h1>Holiday Overview</h1>

        <p>
            Manage employee holidays, requests and annual allowances.
        </p>
    </div>

    <div class="holiday-header-actions">

            @if (
            auth('company')->user()
                ->hasPermission('holidays.view')
        )
            <a href="{{ route(
                    'company.hr.holidays.requests'
                ) }}"
            class="holiday-secondary-button">
                <i class="fas fa-list"></i>
                Holiday Requests
            </a>
        @endif

        @if (
            auth('company')->user()
                ->hasPermission('holidays.create')
        )
           <a href="{{ route(
        'company.hr.holidays.create'
            ) }}"
        class="holiday-primary-button">
            <i class="fas fa-plus"></i>
            Add Holiday
        </a>
                @endif


            @if (
            auth('company')->user()
                ->hasPermission('holiday-allowances.manage')
        )
            <a href="{{ route(
                    'company.hr.holidays.allowances'
                ) }}"
            class="holiday-secondary-button">
                <i class="fas fa-calendar-check"></i>
                Holiday Allowances
            </a>
        @endif

    </div>

</div>

<div class="holiday-stat-grid">

    <div class="holiday-stat-card">
        <div class="holiday-stat-label">
            Currently on Holiday
        </div>

        <div class="holiday-stat-value">
            {{ $stats['currently_on_holiday'] }}
        </div>
    </div>

    <div class="holiday-stat-card">
        <div class="holiday-stat-label">
            Pending Requests
        </div>

        <div class="holiday-stat-value">
            {{ $stats['pending_requests'] }}
        </div>
    </div>

    <div class="holiday-stat-card">
        <div class="holiday-stat-label">
            Upcoming Approved
        </div>

        <div class="holiday-stat-value">
            {{ $stats['upcoming_approved'] }}
        </div>
    </div>

    <div class="holiday-stat-card">
        <div class="holiday-stat-label">
            Approved This Year
        </div>

        <div class="holiday-stat-value">
            {{ $stats['approved_this_year'] }}
        </div>
    </div>

    <div class="holiday-stat-card">
        <div class="holiday-stat-label">
            Missing Allowances
        </div>

        <div class="holiday-stat-value">
            {{ $stats['employees_without_allowance'] }}
        </div>
    </div>

</div>

<div class="holiday-grid">

    <div class="holiday-panel">
        <h2>Currently on Holiday</h2>

        <div class="holiday-table-wrapper">
            <table class="holiday-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Ends</th>
                        <th>Days</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($currentlyOnHoliday as $holiday)
                        <tr>
                            <td>
                                {{ optional($holiday->employee)->first_name }}
                                {{ optional($holiday->employee)->last_name }}
                            </td>

                            <td>
                                {{ $holiday->holiday_type_name }}
                            </td>

                            <td>
                                {{ $holiday->end_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $holiday->total_days }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="holiday-empty">
                                    No employees are currently on holiday.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="holiday-panel">
        <h2>Pending Requests</h2>

        <div class="holiday-table-wrapper">
            <table class="holiday-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pendingRequests as $holiday)
                        <tr>
                            <td>
                                {{ optional(
                                    $holiday->employee
                                )->full_name ?? 'Unknown employee' }}
                            </td>

                            <td>
                                {{ $holiday->start_date->format('d M Y') }}
                                –
                                {{ $holiday->end_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $holiday->total_days }}
                            </td>

                            <td>
                                <span class="
                                    holiday-status
                                    holiday-status-pending
                                ">
                                    Pending
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="holiday-empty">
                                    No pending holiday requests.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="holiday-panel">
        <h2>Upcoming Approved Holidays</h2>

        <div class="holiday-table-wrapper">
            <table class="holiday-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Days</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($upcomingHolidays as $holiday)
                        <tr>
                            <td>
                                {{ optional(
                                    $holiday->employee
                                )->full_name ?? 'Unknown employee' }}
                            </td>

                            <td>
                                {{ $holiday->start_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $holiday->end_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $holiday->total_days }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="holiday-empty">
                                    No upcoming approved holidays.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="holiday-panel">
        <h2>Low Holiday Balance</h2>

        <div class="holiday-table-wrapper">
            <table class="holiday-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Total Allowance</th>
                        <th>Used</th>
                        <th>Remaining</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($lowAllowanceEmployees as $allowance)
                        <tr>
                            <td>
                                {{ optional(
                                    $allowance->employee
                                )->full_name ?? 'Unknown employee' }}
                            </td>

                            <td>
                                {{ number_format(
                                    $allowance->total_allowance,
                                    2
                                ) }}
                            </td>

                            <td>
                                {{ number_format(
                                    $allowance->approved_days_used,
                                    2
                                ) }}
                            </td>

                            <td class="holiday-low-balance">
                                {{ number_format(
                                    $allowance->remaining_allowance,
                                    2
                                ) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="holiday-empty">
                                    No employees have a low holiday balance.
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