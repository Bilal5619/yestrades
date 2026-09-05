@extends('company.layouts.app')

@section('title', 'VAT Report')

@section('content')

<style>
    .vat-report-header {
        margin-bottom: 24px;
    }

    .vat-report-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .vat-report-header p {
        margin: 0;
        color: #aab4ca;
    }

    .vat-report-filter {
        background: #1e2a44;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 24px;

        display: flex;
        align-items: end;
        gap: 14px;
        flex-wrap: wrap;
    }

    .vat-report-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .vat-report-field label {
        color: #c9d2e5;
        font-size: 12px;
        font-weight: 600;
    }

    .vat-report-field input {
        min-height: 40px;
        padding: 9px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: white;
    }

    .vat-report-button {
        height: 40px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: white;
        cursor: pointer;
    }

    .vat-report-cards {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .vat-report-card {
        background: #1e2a44;
        border-radius: 10px;
        padding: 22px;
    }

    .vat-report-label {
        font-size: 12px;
        color: #aab4ca;
        margin-bottom: 9px;
    }

    .vat-report-value {
        font-size: 28px;
        font-weight: 700;
    }

    .vat-report-main {
        background: #22304d;
        border-radius: 12px;
        padding: 28px;
        margin-bottom: 24px;
    }

    .vat-report-main-label {
        color: #c9d2e5;
        margin-bottom: 10px;
    }

    .vat-report-main-value {
        font-size: 38px;
        font-weight: 700;
    }

    .vat-breakdown {
        background: #1e2a44;
        border-radius: 10px;
        padding: 20px;
    }

    .vat-breakdown-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.07);
    }

    @media (max-width: 800px) {
        .vat-report-cards {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="vat-report-header">

    <h1>VAT Report</h1>

    <p>
        VAT received minus VAT paid.
    </p>

</div>


<form
    method="GET"
    action="{{ route('company.vat.report') }}"
    class="vat-report-filter"
>

    <div class="vat-report-field">
        <label>From</label>

        <input
            type="date"
            name="date_from"
            value="{{ request('date_from') }}"
        >
    </div>

    <div class="vat-report-field">
        <label>To</label>

        <input
            type="date"
            name="date_to"
            value="{{ request('date_to') }}"
        >
    </div>

    <button class="vat-report-button">
        Update Report
    </button>

</form>


<div class="vat-report-cards">

    <div class="vat-report-card">

        <div class="vat-report-label">
            VAT Received
        </div>

        <div
            class="vat-report-value"
            style="color:#6fa8ee;"
        >
            £{{ number_format($vatReceived, 2) }}
        </div>

    </div>


    <div class="vat-report-card">

        <div class="vat-report-label">
            VAT Paid
        </div>

        <div
            class="vat-report-value"
            style="color:#59dc96;"
        >
            £{{ number_format($vatPaid, 2) }}
        </div>

    </div>


    <div class="vat-report-card">

        <div class="vat-report-label">
            VAT Balance
        </div>

        <div
            class="vat-report-value"
            style="color:
                {{ $vatBalance >= 0
                    ? '#ffc46f'
                    : '#59dc96'
                }};
            "
        >
            £{{ number_format(abs($vatBalance), 2) }}
        </div>

    </div>

</div>


<div class="vat-report-main">

    @if ($vatBalance >= 0)

        <div class="vat-report-main-label">
            Estimated VAT to Pay HMRC
        </div>

        <div
            class="vat-report-main-value"
            style="color:#ffc46f;"
        >
            £{{ number_format($vatBalance, 2) }}
        </div>

    @else

        <div class="vat-report-main-label">
            VAT Credit Position
        </div>

        <div
            class="vat-report-main-value"
            style="color:#59dc96;"
        >
            £{{ number_format(abs($vatBalance), 2) }}
        </div>

    @endif

</div>


<div class="vat-breakdown">

    <h2 style="font-size:16px; margin-top:0;">
        Breakdown
    </h2>

    <div class="vat-breakdown-row">
        <span>Gross Sales</span>

        <strong>
            £{{ number_format($grossSales, 2) }}
        </strong>
    </div>

    <div class="vat-breakdown-row">
        <span>Net Sales</span>

        <strong>
            £{{ number_format($netSales, 2) }}
        </strong>
    </div>

    <div class="vat-breakdown-row">
        <span>VAT Received</span>

        <strong>
            £{{ number_format($vatReceived, 2) }}
        </strong>
    </div>

    <div class="vat-breakdown-row">
        <span>Gross Approved Expenses</span>

        <strong>
            £{{ number_format($grossExpenses, 2) }}
        </strong>
    </div>

    <div class="vat-breakdown-row">
        <span>Net Approved Expenses</span>

        <strong>
            £{{ number_format($netExpenses, 2) }}
        </strong>
    </div>

    <div class="vat-breakdown-row">
        <span>VAT Paid</span>

        <strong>
            £{{ number_format($vatPaid, 2) }}
        </strong>
    </div>

</div>

@endsection