<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\EmployeeExpense;
use App\Models\Company\VatSale;
use Illuminate\Http\Request;

class VatReportController extends Controller
{
    public function index(Request $request)
    {
        $salesQuery = VatSale::query()
            ->where('payment_status', 'paid');

        $expensesQuery = EmployeeExpense::query()
            ->where('expense_type', 'expense')
            ->where('status', 'approved');

        if ($request->filled('date_from')) {
            $salesQuery->whereDate(
                'sale_date',
                '>=',
                $request->date_from
            );

            $expensesQuery->whereDate(
                'expense_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $salesQuery->whereDate(
                'sale_date',
                '<=',
                $request->date_to
            );

            $expensesQuery->whereDate(
                'expense_date',
                '<=',
                $request->date_to
            );
        }

        $vatReceived = (clone $salesQuery)
            ->sum('vat_amount');

        $vatPaid = (clone $expensesQuery)
            ->sum('vat_amount');

        $grossSales = (clone $salesQuery)
            ->sum('gross_amount');

        $netSales = (clone $salesQuery)
            ->sum('net_amount');

        $grossExpenses = (clone $expensesQuery)
            ->sum('amount');

        $netExpenses = (clone $expensesQuery)
            ->sum('net_amount');

        $vatBalance = round(
            $vatReceived - $vatPaid,
            2
        );

        return view(
            'company.vat.report',
            compact(
                'vatReceived',
                'vatPaid',
                'vatBalance',
                'grossSales',
                'netSales',
                'grossExpenses',
                'netExpenses'
            )
        );
    }
}