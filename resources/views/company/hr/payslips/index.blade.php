@extends('company.layouts.app')

@section('title', 'Payslip Management')

@section('content')

<style>
    .payslip-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .payslip-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .payslip-header p {
        margin: 0;
        color: #aab4ca;
    }

    .payslip-upload-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .payslip-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .payslip-stat-card {
        padding: 19px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .payslip-stat-label {
        margin-bottom: 8px;
        color: #aab4ca;
        font-size: 13px;
    }

    .payslip-stat-value {
        font-size: 27px;
        font-weight: 700;
    }

    .payslip-filter-card,
    .payslip-table-card {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .payslip-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto auto;
        gap: 12px;
    }

    .payslip-filter-grid input,
    .payslip-filter-grid select {
        width: 100%;
        min-height: 42px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .payslip-filter-button,
    .payslip-clear-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .payslip-filter-button {
        background: #2878dc;
    }

    .payslip-clear-button {
        background: #3a4967;
    }

    .payslip-table-wrapper {
        overflow-x: auto;
    }

    .payslip-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: collapse;
    }

    .payslip-table th,
    .payslip-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .payslip-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .payslip-employee-number,
    .payslip-file-name {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .payslip-action {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .payslip-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 1000px) {
        .payslip-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .payslip-filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {
        .payslip-header {
            flex-direction: column;
        }

        .payslip-stats,
        .payslip-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="payslip-header">

    <div>
        <h1>Payslip Management</h1>

        <p>
            Upload, search and manage private employee payslips.
        </p>
    </div>

    @if (
        auth('company')->user()
            ->hasPermission('payslips.upload')
    )
       <a href="{{ route(
                'company.hr.payslips.create'
            ) }}"
        class="payslip-upload-button">

            <i class="fas fa-upload"></i>
            Upload Payslip
        </a>
    @endif

</div>

<div class="payslip-stats">

    <div class="payslip-stat-card">
        <div class="payslip-stat-label">
            Total Payslips
        </div>

        <div class="payslip-stat-value">
            {{ $stats['total'] }}
        </div>
    </div>

    <div class="payslip-stat-card">
        <div class="payslip-stat-label">
            This Month
        </div>

        <div class="payslip-stat-value">
            {{ $stats['this_month'] }}
        </div>
    </div>

    <div class="payslip-stat-card">
        <div class="payslip-stat-label">
            Employees With Payslips
        </div>

        <div class="payslip-stat-value">
            {{ $stats['employees'] }}
        </div>
    </div>

    <div class="payslip-stat-card">
        <div class="payslip-stat-label">
            This Year
        </div>

        <div class="payslip-stat-value">
            {{ $stats['this_year'] }}
        </div>
    </div>

</div>

<div class="payslip-filter-card">

    <form method="GET"
          action="{{ route(
              'company.hr.payslips.index'
          ) }}">

        <div class="payslip-filter-grid">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Employee, ID or pay period">

            <select name="employee_id">
                <option value="">All employees</option>

                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}"
                        {{ (string) request('employee_id') ===
                           (string) $employee->id
                            ? 'selected'
                            : ''
                        }}>

                        {{ trim(
                            $employee->first_name
                            . ' '
                            . $employee->last_name
                        ) }}

                        — {{ $employee->employee_number }}
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
                            : ''
                        }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="payslip-filter-button">
                Filter
            </button>

            <a href="{{ route(
                    'company.hr.payslips.index'
                ) }}"
               class="payslip-clear-button">
                Clear
            </a>

        </div>

    </form>

</div>

<div class="payslip-table-card">

    <div class="payslip-table-wrapper">

        <table class="payslip-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Pay Period</th>
                    <th>Payment Date</th>
                    <th>File</th>
                    <th>Uploaded By</th>
                    <th>Uploaded</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($payslips as $payslip)

                    <tr>
                        <td>
                            {{ trim(
                                $payslip->employee->first_name
                                . ' '
                                . $payslip->employee->last_name
                            ) }}

                            <div class="payslip-employee-number">
                                {{ $payslip->employee
                                    ->employee_number }}
                            </div>
                        </td>

                        <td>
                            {{ $payslip->pay_period }}
                        </td>

                        <td>
                            {{ optional(
                                $payslip->payment_date
                            )->format('d M Y') }}
                        </td>

                        <td>
                            Payslip PDF

                            <div class="payslip-file-name">
                                {{ $payslip->original_filename }}
                            </div>
                        </td>

                        <td>
                            @if ($payslip->uploadedByUser)
                                {{ trim(
                                    $payslip
                                        ->uploadedByUser
                                        ->first_name
                                    . ' '
                                    . $payslip
                                        ->uploadedByUser
                                        ->last_name
                                ) }}
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            {{ optional(
                                $payslip->created_at
                            )->format('d M Y H:i') }}
                        </td>

                        <td>
                           <div style="
    display:flex;
    flex-wrap:wrap;
    gap:6px;
">

    @if (
        auth('company')->user()
            ->hasPermission('payslips.download')
    )
        <a href="{{ route(
                'company.hr.payslips.download',
                $payslip
            ) }}"
           class="payslip-action">

            Download
        </a>
    @endif

    <a href="{{ route(
            'company.hr.payslips.history',
            $payslip->employee
        ) }}"
       class="payslip-action"
       style="background:#3a4967;">

        History
    </a>

    @if (
        auth('company')->user()
            ->hasPermission('payslips.upload')
    )
        <a href="{{ route(
                'company.hr.payslips.replace',
                $payslip
            ) }}"
           class="payslip-action"
           style="background:#d18b2c;">

            Replace
        </a>
    @endif

    @if (
        auth('company')->user()
            ->hasPermission('payslips.delete')
    )
        <form method="POST"
              action="{{ route(
                  'company.hr.payslips.destroy',
                  $payslip
              ) }}"
              onsubmit="
                  return confirm(
                      'Delete this payslip permanently?'
                  );
              ">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="payslip-action"
                    style="
                        border:0;
                        background:#b8404b;
                        cursor:pointer;
                    ">
                Delete
            </button>
        </form>
    @endif

</div>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="payslip-empty">
                                No payslips found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $payslips->links() }}
    </div>

</div>

@endsection