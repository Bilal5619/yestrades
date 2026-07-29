@extends('company.layouts.app')

@section('title', 'Expiring Documents')

@section('content')

<style>
    .expiry-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .expiry-page-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .expiry-page-header p {
        margin: 0;
        color: #aab4ca;
    }

    .expiry-back-button {
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

    .expiry-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .expiry-summary-card {
        padding: 21px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .expiry-summary-card.expired {
        border: 1px solid rgba(236, 87, 98, 0.28);
    }

    .expiry-summary-card.expiring {
        border: 1px solid rgba(243, 166, 58, 0.28);
    }

    .expiry-summary-label {
        margin-bottom: 8px;
        color: #aab4ca;
        font-size: 14px;
    }

    .expiry-summary-value {
        font-size: 30px;
        font-weight: 700;
    }

    .expiry-summary-card.expired .expiry-summary-value {
        color: #ff7c86;
    }

    .expiry-summary-card.expiring .expiry-summary-value {
        color: #ffc46d;
    }

    .expiry-filter-card {
        margin-bottom: 22px;
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .expiry-filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .expiry-form-group label {
        display: block;
        margin-bottom: 7px;
        color: #aab4ca;
        font-size: 13px;
    }

    .expiry-control {
        width: 100%;
        min-height: 43px;
        padding: 9px 12px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    .expiry-filter-actions {
        display: flex;
        gap: 8px;
    }

    .expiry-filter-button,
    .expiry-clear-button {
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

    .expiry-filter-button {
        background: #2878dc;
    }

    .expiry-clear-button {
        background: #3a4967;
    }

    .expiry-table-card {
        overflow: hidden;
        border-radius: 10px;
        background: #1e2a44;
    }

    .expiry-table-wrapper {
        overflow-x: auto;
    }

    .expiry-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .expiry-table th,
    .expiry-table td {
        padding: 14px 13px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        vertical-align: middle;
    }

    .expiry-table th {
        background: #172036;
        color: #aab4ca;
        font-size: 13px;
    }

    .expiry-employee-name {
        font-weight: 600;
    }

    .expiry-employee-id {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .expiry-status {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 600;
    }

    .expiry-status-expired {
        background: rgba(236,87,98,0.16);
        color: #ff7c86;
    }

    .expiry-status-soon {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .expiry-days-expired {
        color: #ff7c86;
        font-weight: 600;
    }

    .expiry-days-soon {
        color: #ffc46d;
        font-weight: 600;
    }

    .expiry-action-button {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .expiry-empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #aab4ca;
    }

    .expiry-pagination {
        margin-top: 20px;
    }

    @media (max-width: 950px) {
        .expiry-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .expiry-page-header {
            flex-direction: column;
        }

        .expiry-summary-grid,
        .expiry-filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="expiry-page-header">

    <div>
        <h1>Expiring Documents</h1>

        <p>
            Review documents that expire within 30 days or
            have already expired.
        </p>
    </div>

    <a href="{{ route('company.hr.employees.index') }}"
       class="expiry-back-button">
        <i class="fas fa-arrow-left"></i>
        View Employees
    </a>

</div>

<div class="expiry-summary-grid">

    <div class="expiry-summary-card expiring">
        <div class="expiry-summary-label">
            Expiring within 30 days
        </div>

        <div class="expiry-summary-value">
            {{ $expiringCount }}
        </div>
    </div>

    <div class="expiry-summary-card expired">
        <div class="expiry-summary-label">
            Already expired
        </div>

        <div class="expiry-summary-value">
            {{ $expiredCount }}
        </div>
    </div>

</div>

<div class="expiry-filter-card">

    <form method="GET"
          action="{{ route(
              'company.hr.employees.expiring-documents'
          ) }}"
          class="expiry-filter-form">

        <div class="expiry-form-group">
            <label for="search">
                Search
            </label>

            <input
                id="search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="expiry-control"
                placeholder="Employee, ID, document or number"
            >
        </div>

        <div class="expiry-form-group">
            <label for="department">
                Department
            </label>

            <select
                id="department"
                name="department"
                class="expiry-control"
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

        <div class="expiry-form-group">
            <label for="expiry_status">
                Expiry status
            </label>

            <select
                id="expiry_status"
                name="expiry_status"
                class="expiry-control"
            >
                <option value="">All alerts</option>

                <option
                    value="expiring_soon"
                    {{ request('expiry_status') === 'expiring_soon'
                        ? 'selected'
                        : ''
                    }}
                >
                    Expiring soon
                </option>

                <option
                    value="expired"
                    {{ request('expiry_status') === 'expired'
                        ? 'selected'
                        : ''
                    }}
                >
                    Expired
                </option>
            </select>
        </div>

        <div class="expiry-filter-actions">
            <button type="submit"
                    class="expiry-filter-button">
                Filter
            </button>

            <a href="{{ route(
                    'company.hr.employees.expiring-documents'
                ) }}"
               class="expiry-clear-button">
                Clear
            </a>
        </div>

    </form>

</div>

<div class="expiry-table-card">

    <div class="expiry-table-wrapper">

        <table class="expiry-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Document</th>
                    <th>Document Number</th>
                    <th>Expiry Date</th>
                    <th>Time Remaining</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($documents as $document)

                    @php
                        $isExpired = $document
                            ->expiry_date
                            ->isPast();

                        $daysDifference = now()
                            ->startOfDay()
                            ->diffInDays(
                                $document->expiry_date,
                                false
                            );
                    @endphp

                    <tr>

                        <td>
                            <div class="expiry-employee-name">
                                {{ optional(
                                    $document->employee
                                )->full_name ?? 'Unknown employee' }}
                            </div>

                            <div class="expiry-employee-id">
                                {{ optional(
                                    $document->employee
                                )->employee_number ?? 'No ID' }}
                            </div>
                        </td>

                        <td>
                            {{ optional(
                                $document->employee
                            )->department ?? 'Not assigned' }}
                        </td>

                        <td>
                            {{ optional(
                                $document->documentType
                            )->name ?? $document->title }}
                        </td>

                        <td>
                            {{ $document->document_number ?: '—' }}
                        </td>

                        <td>
                            {{ $document->expiry_date
                                ->format('d M Y') }}
                        </td>

                        <td>
                            @if ($isExpired)
                                <span class="expiry-days-expired">
                                    {{ abs($daysDifference) }}
                                    days overdue
                                </span>
                            @else
                                <span class="expiry-days-soon">
                                    {{ $daysDifference }}
                                    days remaining
                                </span>
                            @endif
                        </td>

                        <td>
                            @if ($isExpired)
                                <span class="
                                    expiry-status
                                    expiry-status-expired
                                ">
                                    Expired
                                </span>
                            @else
                                <span class="
                                    expiry-status
                                    expiry-status-soon
                                ">
                                    Expiring Soon
                                </span>
                            @endif
                        </td>

                        <td>
                            @if ($document->employee)
                                <a href="{{ route(
                                        'company.hr.employees.documents.index',
                                        $document->employee
                                    ) }}"
                                   class="expiry-action-button">
                                    Open Documents
                                </a>
                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8">
                            <div class="expiry-empty-state">
                                <i class="fas fa-check-circle"
                                   style="
                                       display:block;
                                       margin-bottom:12px;
                                       font-size:32px;
                                       color:#59dc96;
                                   ">
                                </i>

                                No employee documents are expired
                                or expiring within 30 days.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="expiry-pagination">
    {{ $documents->links() }}
</div>

@endsection