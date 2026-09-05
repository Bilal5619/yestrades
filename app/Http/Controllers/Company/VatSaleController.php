<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\VatSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VatSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = VatSale::query();

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = (clone $query)
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $paidQuery = (clone $query)
            ->where('payment_status', 'paid');

        $totals = [
            'gross' => (clone $paidQuery)->sum('gross_amount'),
            'net' => (clone $paidQuery)->sum('net_amount'),
            'vat' => (clone $paidQuery)->sum('vat_amount'),
        ];

        return view(
            'company.vat.sales.index',
            compact('sales', 'totals')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],

            'gross_amount' => ['required', 'numeric', 'min:0.01'],
            'vat_rate' => ['required', 'numeric', 'in:0,5,20'],

            'sale_date' => ['required', 'date'],

            'payment_status' => [
                'required',
                'in:paid,unpaid'
            ],

            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $gross = round((float) $validated['gross_amount'], 2);
        $rate = (float) $validated['vat_rate'];

        if ($rate > 0) {
            $net = round(
                $gross / (1 + ($rate / 100)),
                2
            );

            $vat = round(
                $gross - $net,
                2
            );
        } else {
            $net = $gross;
            $vat = 0;
        }

        $validated['net_amount'] = $net;
        $validated['vat_amount'] = $vat;

        $validated['created_by'] =
            Auth::guard('company')->id();

        VatSale::create($validated);

        return back()->with(
            'success',
            'Sale recorded successfully.'
        );
    }

    public function update(
        Request $request,
        VatSale $sale
    ) {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],

            'gross_amount' => ['required', 'numeric', 'min:0.01'],
            'vat_rate' => ['required', 'numeric', 'in:0,5,20'],

            'sale_date' => ['required', 'date'],

            'payment_status' => [
                'required',
                'in:paid,unpaid'
            ],

            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $gross = round((float) $validated['gross_amount'], 2);
        $rate = (float) $validated['vat_rate'];

        if ($rate > 0) {
            $net = round(
                $gross / (1 + ($rate / 100)),
                2
            );

            $vat = round(
                $gross - $net,
                2
            );
        } else {
            $net = $gross;
            $vat = 0;
        }

        $validated['net_amount'] = $net;
        $validated['vat_amount'] = $vat;

        $sale->update($validated);

        return back()->with(
            'success',
            'Sale updated successfully.'
        );
    }

    public function destroy(VatSale $sale)
    {
        $sale->delete();

        return back()->with(
            'success',
            'Sale deleted successfully.'
        );
    }
}