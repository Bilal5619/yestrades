@extends('company.layouts.app')

@section('title', 'VAT Sales')

@section('content')

<style>
    .vat-page-header {
        margin-bottom: 24px;
    }

    .vat-page-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .vat-page-header p {
        margin: 0;
        color: #aab4ca;
    }

    .vat-cards {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .vat-card {
        background: #1e2a44;
        border-radius: 10px;
        padding: 20px;
    }

    .vat-card-label {
        color: #aab4ca;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .vat-card-value {
        font-size: 25px;
        font-weight: 700;
    }

    .vat-panel {
        background: #1e2a44;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .vat-form-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .vat-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .vat-field label {
        color: #c9d2e5;
        font-size: 12px;
        font-weight: 600;
    }

    .vat-field input,
    .vat-field select,
    .vat-field textarea {
        width: 100%;
        min-height: 40px;
        padding: 9px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: white;
    }

    .vat-full {
        grid-column: 1 / -1;
    }

    .vat-preview {
        background: rgba(111,168,238,0.06) !important;
    }

    .vat-submit-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }

    .vat-btn {
        height: 42px;
        padding: 0 20px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: white;
        cursor: pointer;
    }

    .vat-filter {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
    }

    .vat-table-wrap {
        overflow-x: auto;
    }

    .vat-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    .vat-table th,
    .vat-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
        font-size: 13px;
    }

    .vat-table th {
        color: #aab4ca;
        font-size: 11px;
        text-transform: uppercase;
    }

    .vat-paid {
        color: #59dc96;
    }

    .vat-unpaid {
        color: #ffc46f;
    }

    @media (max-width: 1100px) {
        .vat-form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .vat-cards,
        .vat-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="vat-page-header">
    <h1>Sales</h1>

    <p>
        Record customer sales and VAT received.
    </p>
</div>


<div class="vat-cards">

    <div class="vat-card">
        <div class="vat-card-label">
            Gross Sales
        </div>

        <div class="vat-card-value">
            £{{ number_format($totals['gross'], 2) }}
        </div>
    </div>

    <div class="vat-card">
        <div class="vat-card-label">
            Net Sales
        </div>

        <div class="vat-card-value">
            £{{ number_format($totals['net'], 2) }}
        </div>
    </div>

    <div class="vat-card">
        <div class="vat-card-label">
            VAT Received
        </div>

        <div
            class="vat-card-value"
            style="color:#6fa8ee;"
        >
            £{{ number_format($totals['vat'], 2) }}
        </div>
    </div>

</div>


<div class="vat-panel">

    <h2 style="font-size:16px; margin:0 0 18px;">
        Record New Sale
    </h2>

    <form
        method="POST"
        action="{{ route('company.vat.sales.store') }}"
    >

        @csrf

        <div class="vat-form-grid">

            <div class="vat-field">
                <label>Customer</label>

                <input
                    type="text"
                    name="customer_name"
                    placeholder="Customer name"
                >
            </div>

            <div class="vat-field">
                <label>Invoice / Reference</label>

                <input
                    type="text"
                    name="reference"
                    placeholder="e.g. INV-1001"
                >
            </div>

            <div class="vat-field">
                <label>Source</label>

                <input
                    type="text"
                    name="source"
                    placeholder="e.g. 11Done"
                >
            </div>

            <div class="vat-field">
                <label>Status</label>

                <select name="payment_status">
                    <option value="paid">
                        Paid
                    </option>

                    <option value="unpaid">
                        Unpaid
                    </option>
                </select>
            </div>

            <div class="vat-field">
                <label>Total Received / Invoice Total (£)</label>

                <input
                    type="number"
                    name="gross_amount"
                    id="saleGross"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    required
                >
            </div>

            <div class="vat-field">
                <label>VAT Rate</label>

                <select
                    name="vat_rate"
                    id="saleVatRate"
                    required
                >
                    <option value="20">
                        20% VAT
                    </option>

                    <option value="5">
                        5% VAT
                    </option>

                    <option value="0">
                        No VAT / 0%
                    </option>
                </select>
            </div>

            <div class="vat-field">
                <label>Net Amount</label>

                <input
                    type="text"
                    id="saleNet"
                    class="vat-preview"
                    value="£0.00"
                    readonly
                >
            </div>

            <div class="vat-field">
                <label>VAT Received</label>

                <input
                    type="text"
                    id="saleVat"
                    class="vat-preview"
                    value="£0.00"
                    readonly
                >
            </div>

            <div class="vat-field">
                <label>Sale Date</label>

                <input
                    type="date"
                    name="sale_date"
                    required
                >
            </div>

            <div class="vat-field vat-full">
                <label>Description</label>

                <textarea
                    name="description"
                    maxlength="255"
                    placeholder="Optional notes"
                ></textarea>
            </div>

        </div>

        <div class="vat-submit-row">
            <button
                type="submit"
                class="vat-btn"
            >
                Record Sale
            </button>
        </div>

    </form>

</div>


<div class="vat-panel">

    <form
        method="GET"
        action="{{ route('company.vat.sales.index') }}"
        class="vat-filter"
    >

        <div class="vat-field">
            <label>Status</label>

            <select name="payment_status">
                <option value="">
                    All
                </option>

                <option
                    value="paid"
                    @selected(request('payment_status') === 'paid')
                >
                    Paid
                </option>

                <option
                    value="unpaid"
                    @selected(request('payment_status') === 'unpaid')
                >
                    Unpaid
                </option>
            </select>
        </div>

        <div class="vat-field">
            <label>Source</label>

            <input
                type="text"
                name="source"
                value="{{ request('source') }}"
            >
        </div>

        <div class="vat-field">
            <label>From</label>

            <input
                type="date"
                name="date_from"
                value="{{ request('date_from') }}"
            >
        </div>

        <div class="vat-field">
            <label>To</label>

            <input
                type="date"
                name="date_to"
                value="{{ request('date_to') }}"
            >
        </div>

        <button class="vat-btn">
            Filter
        </button>

    </form>

</div>


<div class="vat-panel">

    <div class="vat-table-wrap">

        <table class="vat-table">

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Reference</th>
                    <th>Source</th>
                    <th>Gross</th>
                    <th>Net</th>
                    <th>VAT Rate</th>
                    <th>VAT Received</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($sales as $sale)

                    <tr>

                        <td>
                            {{ $sale->sale_date->format('d-m-Y') }}
                        </td>

                        <td>
                            {{ $sale->customer_name ?? '—' }}
                        </td>

                        <td>
                            {{ $sale->reference ?? '—' }}
                        </td>

                        <td>
                            {{ $sale->source ?? '—' }}
                        </td>

                        <td>
                            £{{ number_format($sale->gross_amount, 2) }}
                        </td>

                        <td>
                            £{{ number_format($sale->net_amount, 2) }}
                        </td>

                        <td>
                            {{ number_format($sale->vat_rate, 0) }}%
                        </td>

                        <td>
                            £{{ number_format($sale->vat_amount, 2) }}
                        </td>

                        <td>
                            <span class="{{ $sale->payment_status === 'paid'
                                ? 'vat-paid'
                                : 'vat-unpaid'
                            }}">
                                {{ ucfirst($sale->payment_status) }}
                            </span>
                        </td>

                        <td>
                            {{ $sale->description ?? '—' }}
                        </td>

                        <td>

                            <form
                                method="POST"
                                action="{{ route(
                                    'company.vat.sales.destroy',
                                    $sale
                                ) }}"
                                onsubmit="return confirm(
                                    'Delete this sale?'
                                );"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    style="
                                        border:0;
                                        background:none;
                                        color:#ff8a92;
                                        cursor:pointer;
                                    "
                                >
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="11"
                            style="
                                text-align:center;
                                padding:25px;
                                color:#aab4ca;
                            "
                        >
                            No sales recorded.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{ $sales->links() }}


<script>
    function calculateSaleVat() {
        const gross =
            parseFloat(
                document.getElementById('saleGross').value
            ) || 0;

        const rate =
            parseFloat(
                document.getElementById('saleVatRate').value
            ) || 0;

        let net = gross;
        let vat = 0;

        if (rate > 0 && gross > 0) {
            net = gross / (1 + (rate / 100));
            vat = gross - net;
        }

        document.getElementById('saleNet').value =
            '£' + net.toFixed(2);

        document.getElementById('saleVat').value =
            '£' + vat.toFixed(2);
    }

    document
        .getElementById('saleGross')
        ?.addEventListener(
            'input',
            calculateSaleVat
        );

    document
        .getElementById('saleVatRate')
        ?.addEventListener(
            'change',
            calculateSaleVat
        );
</script>

@endsection