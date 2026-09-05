@extends('company.layouts.app')

@section('title', 'Expenses')

@section('content')

<style>
    .expenses-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }
    .expenses-header h1 { margin: 0 0 7px; font-size: 28px; }
    .expenses-header p { margin: 0; color: #aab4ca; }
    .expenses-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-primary-sm, .btn-outline-sm {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 7px;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
        border: none;
    }
    .btn-primary-sm { background: #2878dc; color: #fff; }
    .btn-outline-sm { background: transparent; color: #c9d2e5; border: 1px solid rgba(255,255,255,0.16); }

    .expenses-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 18px;
        border-radius: 10px;
        background: #1e2a44;
    }
    .expenses-filter-field { display: flex; flex-direction: column; gap: 6px; }
    .expenses-filter-field label { color: #c9d2e5; font-size: 12px; font-weight: 600; }
    .expenses-filter-field input, .expenses-filter-field select {
        padding: 9px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }
    .expenses-filter-submit {
        min-height: 40px; padding: 0 16px; border: none; border-radius: 7px;
        background: #2878dc; color: #ffffff; cursor: pointer;
    }

    .expenses-stats {
        display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .expenses-stat-card { padding: 18px; border-radius: 10px; background: #1e2a44; }
    .expenses-stat-label { color: #aab4ca; font-size: 12px; margin-bottom: 8px; }
    .expenses-stat-value { font-size: 24px; font-weight: 700; }

    .expenses-card { padding: 20px; border-radius: 10px; background: #1e2a44; margin-bottom: 24px; }
    .expenses-table-wrapper { overflow-x: auto; }
    .expenses-table { width: 100%; min-width: 900px; border-collapse: collapse; }
    .expenses-table th, .expenses-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
        font-size: 13px;
    }
    .expenses-table th { color: #aab4ca; font-size: 11px; text-transform: uppercase; }
    .expenses-empty { padding: 25px; text-align: center; color: #aab4ca; }

    .expenses-badge { display: inline-flex; padding: 4px 9px; border-radius: 13px; font-size: 11px; }
    .expenses-badge-pending { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .expenses-badge-approved { background: rgba(89,220,150,0.16); color: #59dc96; }
    .expenses-badge-rejected { background: rgba(255,99,99,0.16); color: #ff8a92; }
    .expenses-badge-type-expense { background: rgba(111,168,238,0.16); color: #6fa8ee; }
    .expenses-badge-type-return { background: rgba(89,220,150,0.16); color: #59dc96; }
    .expenses-badge-type-personal { background: rgba(178,143,255,0.16); color: #b28fff; }

    .expenses-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 14px;
    }
    .expenses-form-field { display: flex; flex-direction: column; gap: 6px; }
    .expenses-form-field label { color: #c9d2e5; font-size: 12px; font-weight: 600; }
    .expenses-form-field input, .expenses-form-field select, .expenses-form-field textarea {
        padding: 9px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .expenses-action-form { display: inline; }
    .expenses-link-btn {
        background: none; border: none; cursor: pointer; font-size: 12px;
        padding: 4px 8px; border-radius: 5px; margin-right: 4px;
    }
    .expenses-link-approve { color: #59dc96; }
    .expenses-link-reject { color: #ff8a92; }
    .expenses-link-edit { color: #6fa8ee; }

    .expenses-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10,14,26,0.6);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .expenses-modal-backdrop.is-open { display: flex; }
    .expenses-modal {
        background: #1e2a44;
        border-radius: 12px;
        padding: 24px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .expenses-modal h2 { margin: 0 0 16px; font-size: 18px; }
    .expenses-modal-close {
        float: right;
        background: none;
        border: none;
        color: #c9d2e5;
        font-size: 18px;
        cursor: pointer;
    }
    @media (max-width: 900px) {
    .expenses-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .expenses-stats {
        grid-template-columns: 1fr;
    }
}
/* Professional expense entry form */
.expense-entry-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
}

.expense-entry-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.expense-entry-field label {
    color: #c9d2e5;
    font-size: 12px;
    font-weight: 600;
}

.expense-entry-field input,
.expense-entry-field select,
.expense-entry-field textarea {
    width: 100%;
    min-height: 40px;
    padding: 9px 11px;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 7px;
    background: #151f35;
    color: #ffffff;
    box-sizing: border-box;
}

.expense-entry-field textarea {
    min-height: 80px;
    resize: vertical;
}

.expense-entry-full {
    grid-column: 1 / -1;
}

.expense-vat-preview {
    background: rgba(111,168,238,0.06) !important;
    color: #dce8f8 !important;
}

.expense-form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 18px;
}

.expense-submit-btn {
    min-width: 150px;
    height: 42px;
    padding: 0 20px;
    border: none;
    border-radius: 7px;
    background: #2878dc;
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

.expense-submit-btn:hover {
    background: #216bc7;
}

@media (max-width: 1100px) {
    .expense-entry-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 650px) {
    .expense-entry-grid {
        grid-template-columns: 1fr;
    }

    .expense-form-actions {
        justify-content: stretch;
    }

    .expense-submit-btn {
        width: 100%;
    }
}
</style>

<div class="expenses-header">
    <div>
        <h1>Expenses</h1>
        <p>Record and review employee expense claims.</p>
    </div>
    <div class="expenses-actions">
        <a href="{{ route('company.hr.expenses.export', request()->query()) }}" class="btn-outline-sm">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
    </div>
</div>

@if (session('success'))
    <div class="expenses-card" style="background:rgba(89,220,150,0.1); border:1px solid rgba(89,220,150,0.3);">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="expenses-card" style="background:rgba(255,99,99,0.1); border:1px solid rgba(255,99,99,0.3);">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="expenses-stats">
    <div class="expenses-stat-card">
        <div class="expenses-stat-label">Total (Filtered)</div>
        <div class="expenses-stat-value">&pound;{{ number_format($totals['total'], 2) }}</div>
    </div>
    <div class="expenses-stat-card">
        <div class="expenses-stat-label">Pending</div>
        <div class="expenses-stat-value" style="color:#ffc46f;">&pound;{{ number_format($totals['pending'], 2) }}</div>
    </div>
    <div class="expenses-stat-card">
        <div class="expenses-stat-label">Approved</div>
        <div class="expenses-stat-value" style="color:#59dc96;">&pound;{{ number_format($totals['approved'], 2) }}</div>
    </div>
    <div class="expenses-stat-card">
        <div class="expenses-stat-label">Rejected</div>
        <div class="expenses-stat-value" style="color:#ff8a92;">&pound;{{ number_format($totals['rejected'], 2) }}</div>
    </div>
    <div class="expenses-stat-card">
    <div class="expenses-stat-label">Returns</div>
    <div class="expenses-stat-value" style="color:#59dc96;">
       &pound;{{ number_format($totals['returns'] ?? 0, 2) }}
    </div>
</div>
<div class="expenses-stat-card">
    <div class="expenses-stat-label">VAT Paid (Approved)</div>

    <div class="expenses-stat-value" style="color:#6fa8ee;">
        &pound;{{ number_format($totals['vat_paid'] ?? 0, 2) }}
    </div>
</div>
</div>

<div class="expenses-card">
    <h2 style="font-size:16px; margin:0 0 18px;">
        Record New Expense
    </h2>

    <form method="POST"
          action="{{ route('company.hr.expenses.store') }}"
          enctype="multipart/form-data">

        @csrf

        <div class="expense-entry-grid">

            <div class="expense-entry-field">
                <label>Employee</label>
                <select name="employee_id" required>
                    <option value="">Select employee</option>

                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="expense-entry-field">
                <label>Type</label>

                <select name="expense_type">
                    @foreach (\App\Models\Company\EmployeeExpense::EXPENSE_TYPES as $type)
                        <option value="{{ $type }}">
                            {{ $type === 'personal'
                                ? 'Personal Money'
                                : ($type === 'return'
                                    ? 'Return (money back)'
                                    : 'Expense') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="expense-entry-field">
                <label>Category</label>

                <select name="category" required>
                    @foreach (\App\Models\Company\EmployeeExpense::CATEGORIES as $category)
                        <option value="{{ $category }}">
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="expense-entry-field">
                <label>From (merchant/vendor)</label>
                <input
                    type="text"
                    name="merchant"
                    placeholder="e.g. Screwfix"
                >
            </div>

            <div class="expense-entry-field">
                <label>Total Amount Paid (£)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    name="amount"
                    id="expenseAmount"
                    placeholder="0.00"
                    required
                >
            </div>

            <div class="expense-entry-field">
                <label>VAT Rate</label>

                <select name="vat_rate" id="expenseVatRate" required>
                    <option value="20">20% VAT</option>
                    <option value="5">5% VAT</option>
                    <option value="0">No VAT / 0%</option>
                </select>
            </div>

            <div class="expense-entry-field">
                <label>Net Amount</label>
                <input
                    type="text"
                    id="expenseNetPreview"
                    class="expense-vat-preview"
                    value="£0.00"
                    readonly
                >
            </div>

            <div class="expense-entry-field">
                <label>VAT Paid</label>
                <input
                    type="text"
                    id="expenseVatPreview"
                    class="expense-vat-preview"
                    value="£0.00"
                    readonly
                >
            </div>

            <div class="expense-entry-field">
                <label>Date</label>
                <input
                    type="date"
                    name="expense_date"
                    required
                >
            </div>

            <div class="expense-entry-field">
                <label>Receipt / Invoice</label>
                <input
                    type="file"
                    name="receipt"
                    accept=".jpg,.jpeg,.png,.pdf"
                >
            </div>

            <div class="expense-entry-field expense-entry-full">
                <label>Description</label>

                <textarea
                    name="description"
                    maxlength="255"
                    placeholder="What was this expense for?"
                ></textarea>
            </div>

        </div>

        <div class="expense-form-actions">
            <button type="submit" class="expense-submit-btn">
                Record Expense
            </button>
        </div>

    </form>
</div>


<form method="GET" action="{{ route('company.hr.expenses.index') }}" class="expenses-filter-bar">
    <div class="expenses-filter-field">
        <label>Employee</label>
        <select name="employee_id">
            <option value="">All employees</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="expenses-filter-field">
        <label>Status</label>
        <select name="status">
            <option value="">All statuses</option>
            @foreach (\App\Models\Company\EmployeeExpense::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="expenses-filter-field">
        <label>Category</label>
        <select name="category">
            <option value="">All categories</option>
            @foreach (\App\Models\Company\EmployeeExpense::CATEGORIES as $category)
                <option value="{{ $category }}" @selected(request('category') == $category)>{{ ucfirst($category) }}</option>
            @endforeach
        </select>
    </div>
    <div class="expenses-filter-field">
        <label>Type</label>
        <select name="expense_type">
            <option value="">All types</option>
            @foreach (\App\Models\Company\EmployeeExpense::EXPENSE_TYPES as $type)
                <option value="{{ $type }}" @selected(request('expense_type') == $type)>{{ $type === 'personal' ? 'Personal Money' : ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="expenses-filter-field">
        <label>From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}">
    </div>
    <div class="expenses-filter-field">
        <label>To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}">
    </div>
    <button type="submit" class="expenses-filter-submit">Filter</button>
</form>

<div class="expenses-card">
    <div class="expenses-table-wrapper">
        <table class="expenses-table">
            <thead>
                <tr>
                   <th>Employee</th>
<th>Type</th>
<th>Category</th>
<th>From</th>
<th>Total</th>
<th>Net</th>
<th>VAT Rate</th>
<th>VAT Paid</th>
<th>Date</th>
  <th>Description</th><th>Source</th><th>Status</th><th>Receipt</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenses as $expense)
                    <tr>
                        <td>{{ optional($expense->employee)->full_name ?? 'Unknown' }}</td>
                        <td>
                            @php($type = $expense->expense_type ?? 'expense')
                            <span class="expenses-badge expenses-badge-type-{{ $type }}">
                                {{ $type === 'personal' ? 'Personal Money' : ucfirst($type) }}
                            </span>
                        </td>
                        <td>{{ ucfirst($expense->category) }}</td>
                        <td>{{ $expense->merchant ?? '—' }}</td>
                       <td>
    &pound;{{ number_format($expense->amount, 2) }}
</td>

<td>
    @if ($expense->net_amount !== null)
        &pound;{{ number_format($expense->net_amount, 2) }}
    @else
        —
    @endif
</td>

<td>
    @if ($expense->vat_rate !== null)
        {{ number_format($expense->vat_rate, 0) }}%
    @else
        —
    @endif
</td>

<td>
    @if ($expense->vat_amount !== null)
        &pound;{{ number_format($expense->vat_amount, 2) }}
    @else
        —
    @endif
</td>

<td>{{ $expense->expense_date->format('d-m-Y') }}</td>
 <td>{{ $expense->description ?? '—' }}</td>
                        <td>
                            @if ($expense->source === 'employee_app')
                                <span class="expenses-badge" style="background: rgba(111,168,238,0.16); color:#6fa8ee;">App</span>
                            @else
                                <span class="expenses-badge expenses-badge-pending" style="background: rgba(170,180,202,0.14); color:#c4ccdc;">Admin</span>
                            @endif
                        </td>
                        <td>
                            @if ($expense->status === 'pending')
                                <span class="expenses-badge expenses-badge-pending">Pending</span>
                            @elseif ($expense->status === 'approved')
                                <span class="expenses-badge expenses-badge-approved">Approved</span>
                            @else
                                <span class="expenses-badge expenses-badge-rejected" title="{{ $expense->rejection_reason }}">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if ($expense->receipt_path)
                                <a href="{{ route('company.hr.expenses.receipt', $expense) }}" style="color:#6fa8ee;">View</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <button type="button" class="expenses-link-btn expenses-link-edit"
                                data-update-url="{{ route('company.hr.expenses.update', $expense) }}"
                                data-employee-id="{{ $expense->employee_id }}"
                                data-category="{{ $expense->category }}"
                                data-expense-type="{{ $expense->expense_type ?? 'expense' }}"
                                data-merchant="{{ $expense->merchant }}"
                              data-amount="{{ $expense->amount }}"
data-vat-rate="{{ $expense->vat_rate ?? 0 }}"
data-expense-date="{{ $expense->expense_date->format('Y-m-d') }}"
  data-description="{{ $expense->description }}"
                                onclick="openEditExpenseModal(this)">Edit</button>
                            @if ($expense->status === 'pending')
                                <form method="POST" action="{{ route('company.hr.expenses.approve', $expense) }}" class="expenses-action-form">
                                    @csrf
                                    <button type="submit" class="expenses-link-btn expenses-link-approve">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('company.hr.expenses.reject', $expense) }}" class="expenses-action-form" onsubmit="return promptRejectReason(this);">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" value="">
                                    <button type="submit" class="expenses-link-btn expenses-link-reject">Reject</button>
                                </form>
                            @else
                                <span style="color:#7886a3; font-size:12px;">{{ optional($expense->reviewedBy)->full_name ?? '—' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="14" class="expenses-empty">No expenses recorded for this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $expenses->links() }}

<div class="expenses-modal-backdrop" id="editExpenseBackdrop">
    <div class="expenses-modal">
        <button type="button" class="expenses-modal-close" onclick="closeEditExpenseModal()">&times;</button>
        <h2>Edit Expense</h2>
        <form method="POST" action="" id="editExpenseForm" enctype="multipart/form-data">
            @csrf
            <div class="expenses-form-grid">
                <div class="expenses-form-field">
                    <label>Employee</label>
                    <select name="employee_id" id="editEmployeeId" required>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="expenses-form-field">
                    <label>Type</label>
                    <select name="expense_type" id="editExpenseType">
                        @foreach (\App\Models\Company\EmployeeExpense::EXPENSE_TYPES as $type)
                            <option value="{{ $type }}">{{ $type === 'personal' ? 'Personal Money' : ($type === 'return' ? 'Return (money back)' : 'Expense') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="expenses-form-field">
                    <label>Category</label>
                    <select name="category" id="editCategory" required>
                        @foreach (\App\Models\Company\EmployeeExpense::CATEGORIES as $category)
                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="expenses-form-field">
                    <label>From (merchant/vendor)</label>
                    <input type="text" name="merchant" id="editMerchant" placeholder="e.g. Shell Petrol Station">
                </div>
               <div class="expenses-form-field">
    <label>Total Amount Paid (&pound;)</label>
    <input
        type="number"
        step="0.01"
        min="0.01"
        name="amount"
        id="editAmount"
        required
    >
</div>

<div class="expenses-form-field">
    <label>VAT Rate</label>

    <select name="vat_rate" id="editVatRate" required>
        <option value="20">20% VAT</option>
        <option value="5">5% VAT</option>
        <option value="0">No VAT / 0%</option>
    </select>
</div>

<div class="expenses-form-field">
    <label>Net Amount</label>
    <input
        type="text"
        id="editNetPreview"
        value="£0.00"
        readonly
    >
</div>

<div class="expenses-form-field">
    <label>VAT Paid</label>
    <input
        type="text"
        id="editVatPreview"
        value="£0.00"
        readonly
    >
</div>
                <div class="expenses-form-field">
                    <label>Date</label>
                    <input type="date" name="expense_date" id="editExpenseDate" required>
                </div>
                <div class="expenses-form-field">
                    <label>Replace receipt (optional)</label>
                    <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf">
                </div>
            </div>
            <div class="expenses-form-field" style="margin-bottom:14px;">
                <label>Description</label>
                <textarea name="description" id="editDescription" rows="2" maxlength="255" placeholder="What was this expense for?"></textarea>
            </div>
            <button type="submit" class="btn-primary-sm">Save Changes</button>
        </form>
    </div>
</div>

<script>
    function promptRejectReason(form) {
        const reason = prompt('Reason for rejecting this expense:');
        if (!reason) { return false; }
        form.querySelector('input[name="rejection_reason"]').value = reason;
        return true;
    }

    function openEditExpenseModal(button) {
        document.getElementById('editExpenseForm').action = button.dataset.updateUrl;
        document.getElementById('editEmployeeId').value = button.dataset.employeeId;
        document.getElementById('editExpenseType').value = button.dataset.expenseType;
        document.getElementById('editCategory').value = button.dataset.category;
        document.getElementById('editMerchant').value = button.dataset.merchant || '';
      document.getElementById('editAmount').value = button.dataset.amount;
document.getElementById('editVatRate').value = button.dataset.vatRate || '0';

calculateEditVat();

document.getElementById('editExpenseDate').value = button.dataset.expenseDate;
 document.getElementById('editDescription').value = button.dataset.description || '';
        document.getElementById('editExpenseBackdrop').classList.add('is-open');
    }
function calculateVat(gross, rate) {
    gross = parseFloat(gross) || 0;
    rate = parseFloat(rate) || 0;

    if (gross <= 0) {
        return {
            net: 0,
            vat: 0
        };
    }

    if (rate <= 0) {
        return {
            net: gross,
            vat: 0
        };
    }

    const net = gross / (1 + (rate / 100));
    const vat = gross - net;

    return {
        net: net,
        vat: vat
    };
}

function calculateNewExpenseVat() {
    const gross = document.getElementById('expenseAmount').value;
    const rate = document.getElementById('expenseVatRate').value;

    const result = calculateVat(gross, rate);

    document.getElementById('expenseNetPreview').value =
        '£' + result.net.toFixed(2);

    document.getElementById('expenseVatPreview').value =
        '£' + result.vat.toFixed(2);
}

function calculateEditVat() {
    const gross = document.getElementById('editAmount').value;
    const rate = document.getElementById('editVatRate').value;

    const result = calculateVat(gross, rate);

    document.getElementById('editNetPreview').value =
        '£' + result.net.toFixed(2);

    document.getElementById('editVatPreview').value =
        '£' + result.vat.toFixed(2);
}
    function closeEditExpenseModal() {
        document.getElementById('editExpenseBackdrop').classList.remove('is-open');
    }
    document.getElementById('expenseAmount')
    ?.addEventListener('input', calculateNewExpenseVat);

document.getElementById('expenseVatRate')
    ?.addEventListener('change', calculateNewExpenseVat);

document.getElementById('editAmount')
    ?.addEventListener('input', calculateEditVat);

document.getElementById('editVatRate')
    ?.addEventListener('change', calculateEditVat);
</script>

@endsection
