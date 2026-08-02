@extends('company.layouts.app')

@section('title', 'Sponsored Workers')

@section('content')

<style>
    .sponsor-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .sponsor-page-header h1 {
        margin: 0 0 6px;
        color: #ffffff;
        font-size: 28px;
    }

    .sponsor-page-header p {
        margin: 0;
        color: #aab4ca;
    }

    .sponsor-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .sponsor-stat-card {
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-stat-label {
        margin-bottom: 9px;
        color: #aab4ca;
        font-size: 13px;
    }

    .sponsor-stat-value {
        color: #ffffff;
        font-size: 26px;
        font-weight: 700;
    }

    .sponsor-stat-warning {
        color: #ffc46d;
    }

    .sponsor-stat-danger {
        color: #ff8a92;
    }

    .sponsor-filter-panel {
        padding: 20px;
        margin-bottom: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-filter-form {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 220px auto auto;
        gap: 12px;
        align-items: end;
    }

    .sponsor-form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .sponsor-form-group label {
        color: #aab4ca;
        font-size: 13px;
    }

    .sponsor-input,
    .sponsor-select {
        width: 100%;
        min-height: 43px;
        padding: 0 13px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        outline: none;
        background: #162138;
        color: #ffffff;
    }

    .sponsor-input::placeholder {
        color: #75819a;
    }

    .sponsor-input:focus,
    .sponsor-select:focus {
        border-color: #2878dc;
        box-shadow: 0 0 0 2px rgba(40,120,220,0.15);
    }

    .sponsor-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 43px;
        padding: 0 17px;
        border: 0;
        border-radius: 7px;
        cursor: pointer;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
        font-size: 14px;
    }

    .sponsor-button-secondary {
        background: #3a4967;
    }

    .sponsor-table-panel {
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .sponsor-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .sponsor-table-header h2 {
        margin: 0;
        color: #ffffff;
        font-size: 19px;
    }

    .sponsor-table-wrapper {
        overflow-x: auto;
    }

    .sponsor-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: collapse;
    }

    .sponsor-table th,
    .sponsor-table td {
        padding: 14px 12px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
        vertical-align: middle;
    }

    .sponsor-table th {
        color: #aab4ca;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .sponsor-table td {
        color: #ffffff;
        font-size: 14px;
    }

    .sponsor-employee-name {
        color: #ffffff;
        font-weight: 600;
    }

    .sponsor-employee-number {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .sponsor-muted {
        color: #aab4ca;
    }

    .sponsor-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 14px;
        font-size: 12px;
        white-space: nowrap;
    }

    .sponsor-status-valid {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .sponsor-status-warning {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .sponsor-status-danger {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .sponsor-status-neutral {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .sponsor-action-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #69a9ff;
        text-decoration: none;
        font-weight: 600;
    }

    .sponsor-action-link:hover {
        color: #9bc7ff;
    }

    .sponsor-empty {
        padding: 35px 15px;
        text-align: center;
        color: #aab4ca;
    }

    .sponsor-pagination {
        margin-top: 20px;
    }

    @media (max-width: 1100px) {
        .sponsor-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sponsor-filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 650px) {
        .sponsor-page-header {
            flex-direction: column;
        }

        .sponsor-stat-grid {
            grid-template-columns: 1fr;
        }

        .sponsor-filter-form {
            grid-template-columns: 1fr;
        }

        .sponsor-button {
            width: 100%;
        }
    }
</style>

<div class="sponsor-page-header">
    <div>
        <h1>Sponsored Workers</h1>

        <p>
            Monitor visa expiry dates, Certificate of Sponsorship
            information and sponsored employment details.
        </p>
    </div>
</div>

<div class="sponsor-stat-grid">

    <div class="sponsor-stat-card">
        <div class="sponsor-stat-label">
            Sponsored Workers
        </div>

        <div class="sponsor-stat-value">
            {{ $statistics['total'] }}
        </div>
    </div>

    <div class="sponsor-stat-card">
        <div class="sponsor-stat-label">
            Expiring Within 90 Days
        </div>

        <div class="sponsor-stat-value sponsor-stat-warning">
            {{ $statistics['expiring_90_days'] }}
        </div>
    </div>

    <div class="sponsor-stat-card">
        <div class="sponsor-stat-label">
            Expired Visas
        </div>

        <div class="sponsor-stat-value sponsor-stat-danger">
            {{ $statistics['expired'] }}
        </div>
    </div>

    <div class="sponsor-stat-card">
        <div class="sponsor-stat-label">
            Missing Visa Expiry
        </div>

        <div class="sponsor-stat-value sponsor-stat-warning">
            {{ $statistics['missing_expiry'] }}
        </div>
    </div>

</div>

<div class="sponsor-filter-panel">

    <form method="GET" class="sponsor-filter-form">

        <div class="sponsor-form-group">
            <label for="search">
                Search sponsored workers
            </label>

            <input
                type="text"
                name="search"
                id="search"
                class="sponsor-input"
                value="{{ request('search') }}"
                placeholder="Employee, employee ID, CoS number or occupation code"
            >
        </div>

        <div class="sponsor-form-group">
            <label for="status">
                Record status
            </label>

            <select
                name="status"
                id="status"
                class="sponsor-select"
            >
                <option value="">
                    All statuses
                </option>

                <option
                    value="active"
                    {{ request('status') === 'active'
                        ? 'selected'
                        : ''
                    }}
                >
                    Active
                </option>

                <option
                    value="ended"
                    {{ request('status') === 'ended'
                        ? 'selected'
                        : ''
                    }}
                >
                    Ended
                </option>

                <option
                    value="inactive"
                    {{ request('status') === 'inactive'
                        ? 'selected'
                        : ''
                    }}
                >
                    Inactive
                </option>
            </select>
        </div>

        <button
            type="submit"
            class="sponsor-button"
        >
            <i class="fas fa-search"></i>
            Search
        </button>

        <a
            href="{{ route(
                'company.hr.sponsor-compliance.sponsored-workers.index'
            ) }}"
            class="sponsor-button sponsor-button-secondary"
        >
            <i class="fas fa-undo"></i>
            Reset
        </a>

    </form>

</div>

<div class="sponsor-table-panel">

    <div class="sponsor-table-header">
        <h2>Sponsored Worker Register</h2>
    </div>

    <div class="sponsor-table-wrapper">

        <table class="sponsor-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>CoS Number</th>
                    <th>Occupation Code</th>
                    <th>Sponsored Job</th>
                    <th>Visa Expiry</th>
                    <th>Visa Status</th>
                    <th>Record Status</th>
                    <th>Compliance</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($sponsoredWorkers as $record)

                    @php
                        $visaClass = match ($record->visa_status) {
                            'valid' =>
                                'sponsor-status-valid',

                            'expired' =>
                                'sponsor-status-danger',

                            'urgent',
                            'expiring_60_days',
                            'expiring_90_days' =>
                                'sponsor-status-warning',

                            default =>
                                'sponsor-status-neutral',
                        };

                        $recordClass = match ($record->record_status) {
                            'active' =>
                                'sponsor-status-valid',

                            'ended' =>
                                'sponsor-status-danger',

                            default =>
                                'sponsor-status-neutral',
                        };
                    @endphp

                    <tr>
                        <td>
                            <div class="sponsor-employee-name">
                                {{ $record->employee->full_name }}
                            </div>

                            <div class="sponsor-employee-number">
                                {{ $record->employee->employee_number }}
                            </div>
                        </td>

                        <td>
                            {{ $record->cos_number
                                ?: 'Not entered'
                            }}
                        </td>

                        <td>
                            {{ $record->occupation_code
                                ?: 'Not entered'
                            }}
                        </td>

                        <td>
                            {{ $record->cos_job_title
                                ?: 'Not entered'
                            }}
                        </td>

                        <td>
                            {{ $record->visa_expiry_date
                                ? $record->visa_expiry_date
                                    ->format('d M Y')
                                : 'Not entered'
                            }}
                        </td>

                        <td>
                            <span class="sponsor-status {{ $visaClass }}">
                                {{ $record->visa_status_label }}
                            </span>
                        </td>

                        <td>
                            <span class="sponsor-status {{ $recordClass }}">
                                {{ ucwords(str_replace(
                                    '_',
                                    ' ',
                                    $record->record_status
                                )) }}
                            </span>
                        </td>

                        <td>
                            @if ($record->hasComplianceDifference())
                                <span class="
                                    sponsor-status
                                    sponsor-status-warning
                                ">
                                    Review Required
                                </span>
                            @else
                                <span class="
                                    sponsor-status
                                    sponsor-status-valid
                                ">
                                    Compliant
                                </span>
                            @endif
                        </td>

                        <td>
                            <a
                                href="{{ route(
                                    'company.hr.sponsor-compliance.sponsored-workers.show',
                                    $record
                                ) }}"
                                class="sponsor-action-link"
                            >
                                <i class="fas fa-eye"></i>
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="9">
                            <div class="sponsor-empty">
                                <i
                                    class="fas fa-passport"
                                    style="
                                        display:block;
                                        margin-bottom:10px;
                                        font-size:28px;
                                    "
                                ></i>

                                No sponsored workers were found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@if ($sponsoredWorkers->hasPages())
    <div class="sponsor-pagination">
        {{ $sponsoredWorkers->links() }}
    </div>
@endif

@endsection