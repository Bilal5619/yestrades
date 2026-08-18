@extends('company.layouts.app')

@section('title', 'Right to Work')

@section('content')

<style>
    .rtw-header {
        margin-bottom: 24px;
    }

    .rtw-header h1 {
        margin: 0 0 6px;
        font-size: 26px;
    }

    .rtw-header p {
        margin: 0;
        color: #aab4ca;
    }

    .rtw-stats {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .rtw-stat {
        padding: 18px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .rtw-stat strong {
        display: block;
        font-size: 26px;
        margin-bottom: 5px;
    }

    .rtw-stat span {
        color: #aab4ca;
        font-size: 13px;
    }

    .rtw-panel {
        padding: 20px;
        border-radius: 9px;
        background: #1e2a44;
        margin-bottom: 20px;
    }

    .rtw-filter {
        display: grid;
        grid-template-columns:
            2fr 1fr 1fr auto;
        gap: 12px;
    }

    .rtw-filter input,
    .rtw-filter select {
        width: 100%;
        padding: 10px 11px;
        border-radius: 6px;
        border:
            1px solid rgba(255,255,255,.12);
        background: #162138;
        color: #ffffff;
        box-sizing: border-box;
    }

    .rtw-button {
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #fff;
        padding: 10px 18px;
        cursor: pointer;
    }

    .rtw-table-wrap {
        overflow-x: auto;
    }

    .rtw-table {
        width: 100%;
        border-collapse: collapse;
    }

    .rtw-table th,
    .rtw-table td {
        padding: 13px 12px;
        border-bottom:
            1px solid rgba(255,255,255,.07);
        text-align: left;
        vertical-align: top;
    }

    .rtw-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .rtw-status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
    }

    .rtw-verified {
        background:
            rgba(46,191,114,.18);
        color: #6fe6a4;
    }

    .rtw-warning {
        background:
            rgba(243,166,58,.18);
        color: #ffc66d;
    }

    .rtw-danger {
        background:
            rgba(236,87,98,.18);
        color: #ff8d95;
    }

    .rtw-info {
        background:
            rgba(40,120,220,.18);
        color: #82b7ff;
    }

    .rtw-link {
        color: #77aaff;
        text-decoration: none;
    }

    .rtw-muted {
        color: #aab4ca;
        font-size: 12px;
        margin-top: 4px;
    }

    @media (max-width: 900px) {
        .rtw-stats {
            grid-template-columns: 1fr 1fr;
        }

        .rtw-filter {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .rtw-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="rtw-header">
    <h1>Right to Work</h1>

    <p>
        Monitor employee Right to Work evidence,
        reviews and expiry dates.
    </p>
</div>

<div class="rtw-stats">

    <div class="rtw-stat">
        <strong>
            {{ $stats['missing'] }}
        </strong>
        <span>Missing Evidence</span>
    </div>

    <div class="rtw-stat">
        <strong>
            {{ $stats['pending_review'] }}
        </strong>
        <span>Pending Review</span>
    </div>

    <div class="rtw-stat">
        <strong>
            {{ $stats['expiring'] }}
        </strong>
        <span>Expiring Within 30 Days</span>
    </div>

    <div class="rtw-stat">
        <strong>
            {{ $stats['expired'] }}
        </strong>
        <span>Expired</span>
    </div>

</div>

<div class="rtw-panel">

    <form
        method="GET"
        action="{{ route('company.hr.right-to-work') }}"
        class="rtw-filter"
    >
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Employee name, number or email"
        >

        <select name="status">
            <option value="">
                All statuses
            </option>

            <option
                value="missing"
                {{ request('status') === 'missing'
                    ? 'selected'
                    : '' }}
            >
                Missing
            </option>

            <option
                value="pending_review"
                {{ request('status') === 'pending_review'
                    ? 'selected'
                    : '' }}
            >
                Pending Review
            </option>

            <option
                value="expiring"
                {{ request('status') === 'expiring'
                    ? 'selected'
                    : '' }}
            >
                Expiring
            </option>

            <option
                value="expired"
                {{ request('status') === 'expired'
                    ? 'selected'
                    : '' }}
            >
                Expired
            </option>

            <option
                value="verified"
                {{ request('status') === 'verified'
                    ? 'selected'
                    : '' }}
            >
                Verified
            </option>
        </select>

        <select name="sponsored">
            <option value="">
                All employees
            </option>

            <option
                value="yes"
                {{ request('sponsored') === 'yes'
                    ? 'selected'
                    : '' }}
            >
                Sponsored workers
            </option>

            <option
                value="no"
                {{ request('sponsored') === 'no'
                    ? 'selected'
                    : '' }}
            >
                Non-sponsored
            </option>
        </select>

        <button
            type="submit"
            class="rtw-button"
        >
            Filter
        </button>
    </form>

</div>

<div class="rtw-panel rtw-table-wrap">

    <table class="rtw-table">

        <thead>
            <tr>
                <th>Employee</th>
                <th>Sponsored</th>
                <th>Status</th>
                <th>RTW Expiry</th>
                <th>Review</th>
                <th>Documents</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($employees as $employee)

                @php
                    $statusClass = match(
                        $employee->rtw_status
                    ) {
                        'verified' =>
                            'rtw-verified',

                        'expiring',
                        'pending_review' =>
                            'rtw-warning',

                        'expired',
                        'missing',
                        'rejected' =>
                            'rtw-danger',

                        default =>
                            'rtw-info',
                    };

                    $statusLabel = ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $employee->rtw_status
                        )
                    );
                @endphp

                <tr>

                    <td>
                        <a
                            href="{{ route(
                                'company.hr.employees.show',
                                $employee
                            ) }}"
                            class="rtw-link"
                        >
                            {{ $employee->full_name }}
                        </a>

                        <div class="rtw-muted">
                            {{ $employee->employee_number }}
                        </div>
                    </td>

                    <td>
                        {{ $employee->is_sponsored_worker
                            ? 'Yes'
                            : 'No' }}
                    </td>

                    <td>
                        <span
                            class="rtw-status {{ $statusClass }}"
                        >
                            {{ $statusLabel }}
                        </span>
                    </td>

                    <td>
                        @if (
                            $employee->rtw_document &&
                            $employee->rtw_document
                                ->expiry_date
                        )
                            {{
                                $employee
                                    ->rtw_document
                                    ->expiry_date
                                    ->format('d M Y')
                            }}
                        @elseif (
                            $employee->immigration_document &&
                            $employee->immigration_document
                                ->expiry_date
                        )
                            {{
                                $employee
                                    ->immigration_document
                                    ->expiry_date
                                    ->format('d M Y')
                            }}
                        @else
                            —
                        @endif
                    </td>

                    <td>
                        @if (
                            $employee->rtw_document &&
                            $employee->rtw_document
                                ->reviewed_at
                        )
                            {{
                                $employee
                                    ->rtw_document
                                    ->reviewed_at
                                    ->format('d M Y')
                            }}

                            @if (
                                $employee->rtw_document
                                    ->reviewedBy
                            )
                                <div class="rtw-muted">
                                    Reviewed by
                                    {{
                                        $employee
                                            ->rtw_document
                                            ->reviewedBy
                                            ->full_name
                                    }}
                                </div>
                            @endif
                        @else
                            Not reviewed
                        @endif
                    </td>

                    <td>
                        <a
                            href="{{ route(
                                'company.hr.employees.documents.index',
                                $employee
                            ) }}"
                            class="rtw-link"
                        >
                            Open Documents
                        </a>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        No employees found.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

{{ $employees->links() }}

@endsection