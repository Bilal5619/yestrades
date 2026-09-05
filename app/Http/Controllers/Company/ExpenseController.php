<?php

namespace App\Http\Controllers\Company;

use App\Exports\ExpenseReportExport;
use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeExpense::query()->with(['employee', 'submittedBy', 'reviewedBy']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderByDesc('expense_date')->paginate(20)->withQueryString();
$totals = [
    'total' => (clone $query)
        ->where('expense_type', '!=', 'return')
        ->sum('amount'),

    'pending' => (clone $query)
        ->where('expense_type', '!=', 'return')
        ->where('status', 'pending')
        ->sum('amount'),

    'approved' => (clone $query)
        ->where('expense_type', '!=', 'return')
        ->where('status', 'approved')
        ->sum('amount'),

    'rejected' => (clone $query)
        ->where('expense_type', '!=', 'return')
        ->where('status', 'rejected')
        ->sum('amount'),

    'returns' => (clone $query)
        ->where('expense_type', 'return')
        ->sum('amount'),

    'vat_paid' => (clone $query)
        ->where('expense_type', 'expense')
        ->where('status', 'approved')
        ->sum('vat_amount'),
];
        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.expenses.index', compact('expenses', 'totals', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'category' => ['required', 'in:' . implode(',', EmployeeExpense::CATEGORIES)],
            'expense_type' => ['nullable', 'in:' . implode(',', EmployeeExpense::EXPENSE_TYPES)],
            'merchant' => ['nullable', 'string', 'max:255'],
           'amount' => ['required', 'numeric', 'min:0.01'],
'vat_rate' => ['required', 'numeric', 'in:0,5,20'],
'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expense-receipts', 'public');
        }

        $validated['expense_type'] = $validated['expense_type'] ?? 'expense';
        $grossAmount = round((float) $validated['amount'], 2);
$vatRate = (float) $validated['vat_rate'];

if ($vatRate > 0) {
    $vatAmount = round(
        $grossAmount * $vatRate / (100 + $vatRate),
        2
    );

    $netAmount = round(
        $grossAmount - $vatAmount,
        2
    );
} else {
    $netAmount = $grossAmount;
    $vatAmount = 0;
}

$validated['net_amount'] = $netAmount;
$validated['vat_amount'] = $vatAmount;
        $validated['status'] = 'pending';
        $validated['source'] = 'admin';
        $validated['submitted_by'] = Auth::guard('company')->id();

        EmployeeExpense::create($validated);

        return back()->with('success', 'Expense recorded successfully.');
    }

    public function update(Request $request, EmployeeExpense $expense)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'category' => ['required', 'in:' . implode(',', EmployeeExpense::CATEGORIES)],
            'expense_type' => ['nullable', 'in:' . implode(',', EmployeeExpense::EXPENSE_TYPES)],
            'merchant' => ['nullable', 'string', 'max:255'],
           'amount' => ['required', 'numeric', 'min:0.01'],
'vat_rate' => ['required', 'numeric', 'in:0,5,20'],
'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $validated['expense_type'] = $validated['expense_type'] ?? 'expense';
        $grossAmount = round((float) $validated['amount'], 2);
$vatRate = (float) $validated['vat_rate'];

if ($vatRate > 0) {
    $vatAmount = round(
        $grossAmount * $vatRate / (100 + $vatRate),
        2
    );

    $netAmount = round(
        $grossAmount - $vatAmount,
        2
    );
} else {
    $netAmount = $grossAmount;
    $vatAmount = 0;
}

$validated['net_amount'] = $netAmount;
$validated['vat_amount'] = $vatAmount;

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expense-receipts', 'public');
        }

        $expense->update($validated);

        return back()->with('success', 'Expense updated successfully.');
    }

    public function approve(EmployeeExpense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->withErrors(['expense' => 'Only pending expenses can be approved.']);
        }

        $expense->update([
            'status' => 'approved',
            'reviewed_by' => Auth::guard('company')->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Expense approved.');
    }

    public function reject(Request $request, EmployeeExpense $expense)
    {
        if ($expense->status !== 'pending') {
            return back()->withErrors(['expense' => 'Only pending expenses can be rejected.']);
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $expense->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::guard('company')->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Expense rejected.');
    }

    public function downloadReceipt(EmployeeExpense $expense)
    {
        abort_unless($expense->receipt_path, 404);

        return Storage::disk('public')->download($expense->receipt_path);
    }

    public function export(Request $request)
    {
        $query = EmployeeExpense::query()->with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderByDesc('expense_date')->get();

        return Excel::download(new ExpenseReportExport($expenses), 'expense-report-' . now()->format('Y-m-d') . '.xlsx');
    }
}
