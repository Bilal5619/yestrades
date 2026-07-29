@extends('company.layouts.app')

@section('title', 'Employee Payslip History')

@section('content')

<style>
    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .history-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .history-header p {
        margin: 0;
        color: #aab4ca;
    }

    .history-button {
        display: inline-flex;
        align-items: center;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .history-card {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .history-filters {
        display: grid;
        grid-template-columns: 2fr 1fr auto auto;
        gap: 12px;
    }

    .history-filters input,
    .history-filters select {
        min-height: 42px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .history-table-wrapper {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }

    .history-table th,
    .history-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .history-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .history-download {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    @media (max-width: 700px) {
        .history-header {
            flex-direction: column;
        }

        .history-filters {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="history-header">

    <div>
        <h1>Employee Payslip History</h1>

        <p>
            {{ $employee->first_name }}
            {{ $employee->last_name }}
            — {{ $employee->employee_number }}
        </p>
    </div>

    <div style="
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    ">
        @if (
            auth('company')->user()
                ->hasPermission('payslips.upload')
        )
            <a href="{{ route(
                    'company.hr.payslips.create',
                    [
                        'employee_id' =>
                            $employee->id,
                    ]
                ) }}"
               class="history-button">

                Upload Payslip
            </a>
        @endif

        <a href="{{ route(
                'company.hr.payslips.index'
            ) }}"
           class="history-button"
           style="background:#3a4967;">

            All Payslips
        </a>
    </div>

</div>

<div class="history-card">

    <form method="GET">

        <div class="history-filters">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Pay period or filename">

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
                    class="history-button"
                    style="border:0;cursor:pointer;">
                Filter
            </button>

            <a href="{{ route(
                    'company.hr.payslips.history',
                    $employee
                ) }}"
               class="history-button"
               style="background:#3a4967;">
                Clear
            </a>

        </div>

    </form>

</div>

<div class="history-card">

    <div class="history-table-wrapper">

        <table class="history-table">

            <thead>
                <tr>
                    <th>Pay Period</th>
                    <th>Payment Date</th>
                    <th>Filename</th>
                    <th>Uploaded By</th>
                    <th>Uploaded</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($payslips as $payslip)

                    <tr>
                        <td>{{ $payslip->pay_period }}</td>

                        <td>
                            {{ optional(
                                $payslip->payment_date
                            )->format('d M Y') }}
                        </td>

                        <td>
                            {{ $payslip->original_filename }}
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
                            @if (
                                auth('company')->user()
                                    ->hasPermission(
                                        'payslips.download'
                                    )
                            )
                                <a href="{{ route(
                                        'company.hr.payslips.download',
                                        $payslip
                                    ) }}"
                                   class="history-download">
                                    Download
                                </a>
                            @endif
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="6"
                            style="
                                padding:35px;
                                text-align:center;
                                color:#aab4ca;
                            ">
                            No payslips found for this employee.
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