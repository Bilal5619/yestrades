@extends('company.layouts.app')

@section('title', 'Holiday Requests')

@section('content')

<style>
    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .request-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .request-header p {
        margin: 0;
        color: #aab4ca;
    }

    .request-button {
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

    .request-filters,
    .request-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .request-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 12px;
    }

    .request-filter-grid input,
    .request-filter-grid select {
        width: 100%;
        min-height: 41px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .request-filter-submit {
        min-height: 41px;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .request-table-wrapper {
        overflow-x: auto;
    }

    .request-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .request-table th,
    .request-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .request-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .request-employee {
        font-weight: 600;
    }

    .request-employee-id {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .request-status {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 14px;
        font-size: 12px;
    }

    .request-status-pending {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .request-status-approved {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .request-status-rejected,
    .request-status-cancelled {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .request-view {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .request-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 7px;
    }

    .request-alert-success {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .request-alert-error {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .request-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 900px) {
        .request-header {
            flex-direction: column;
        }

        .request-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="request-header">

    <div>
        <h1>Holiday Requests</h1>

        <p>
            Review pending, approved, rejected and cancelled holidays.
        </p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('company.hr.holidays.export', request()->query()) }}"
           class="request-button" style="background: transparent; border: 1px solid rgba(255,255,255,0.16);">
            <i class="fas fa-file-excel"></i>
            Export to Excel
        </a>

        <a href="{{ route('company.hr.holidays.index') }}"
           class="request-button">
            <i class="fas fa-arrow-left"></i>
            Holiday Overview
        </a>
    </div>

</div>

@if (session('success'))
    <div class="request-alert request-alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="request-alert request-alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="request-filters">

    <form method="GET"
          action="{{ route(
              'company.hr.holidays.requests'
          ) }}">

        <div class="request-filter-grid">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Employee name or number">

            <select name="status">
                <option value="">All statuses</option>

                @foreach (
                    [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
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

            <select name="holiday_type">
                <option value="">All types</option>

                @foreach (
                    [
                        'annual_leave' => 'Annual Leave',
                        'sick_leave' => 'Sick Leave',
                        'unpaid_leave' => 'Unpaid Leave',
                        'maternity_leave' => 'Maternity Leave',
                        'paternity_leave' => 'Paternity Leave',
                        'compassionate_leave' => 'Compassionate Leave',
                        'other' => 'Other',
                    ] as $value => $label
                )
                    <option value="{{ $value }}"
                        {{ request('holiday_type') === $value
                            ? 'selected'
                            : '' }}>
                        {{ $label }}
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
                    class="request-filter-submit">
                Filter
            </button>

        </div>

    </form>

</div>

<div class="request-panel">

    <div class="request-table-wrapper">

        <table class="request-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Dates</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($holidays as $holiday)

                    <tr>
                        <td>
                            <div class="request-employee">
                                {{ optional($holiday->employee)->first_name }}
                                {{ optional($holiday->employee)->last_name }}
                            </div>

                            <div class="request-employee-id">
                                {{ optional(
                                    $holiday->employee
                                )->employee_number }}
                            </div>
                        </td>

                        <td>
                            {{ $holiday->holiday_type_name }}
                        </td>

                        <td>
                            {{ $holiday->start_date->format('d M Y') }}
                            –
                            {{ $holiday->end_date->format('d M Y') }}
                        </td>

                        <td>
                            {{ number_format(
                                $holiday->total_days,
                                2
                            ) }}
                        </td>

                        <td>
                            <span class="
                                request-status
                                request-status-{{ $holiday->status }}
                            ">
                                {{ $holiday->status_name }}
                            </span>
                        </td>

                        <td>
                            {{ optional(
                                $holiday->requested_at
                            )->format('d M Y H:i') ?? '—' }}
                        </td>

                        <td>
                            <a href="{{ route(
                                    'company.hr.holidays.show',
                                    $holiday
                                ) }}"
                               class="request-view">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="request-empty">
                                No holiday requests found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $holidays->links() }}
    </div>

</div>

@endsection