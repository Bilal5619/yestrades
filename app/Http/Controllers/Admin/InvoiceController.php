<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Services\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  public function index(Request $request)
  {
    $invoiceNumber  = $request->input('invoice_number');
    $invoiceStatus  = $request->input('invoice_status');
    $paymentStatus  = $request->input('payment_status');

    $information['invoices'] = Invoice::with(['booking', 'vendor'])
      ->when($invoiceNumber, function ($query, $invoiceNumber) {
        return $query->where('invoice_number', 'like', '%' . $invoiceNumber . '%');
      })
      ->when($invoiceStatus, function ($query, $invoiceStatus) {
        return $query->where('invoice_status', $invoiceStatus);
      })
      ->when($paymentStatus, function ($query, $paymentStatus) {
        return $query->where('payment_status', $paymentStatus);
      })
      ->orderByDesc('id')
      ->paginate(15);

    return view('admin.invoice.index', $information);
  }

  public function show($id)
  {
    $information['invoice'] = Invoice::with(['booking.serviceContent', 'vendor'])
      ->findOrFail($id);

    return view('admin.invoice.show', $information);
  }

  public function approve($id)
  {
    $invoice = Invoice::findOrFail($id);

    if ($invoice->invoice_status === 'approved') {
      return redirect()->back()->with('warning', 'Invoice is already approved.');
    }

    $approvedAt = Carbon::now();

    $invoice->update([
      'invoice_status'   => 'approved',
      'approved_at'      => $approvedAt,
      'payment_due_date' => $approvedAt->copy()->addHours(48),
    ]);

    return redirect()->back()->with('success', 'Invoice approved. Payment is due within 48 hours.');
  }

  public function reject($id)
  {
    $invoice = Invoice::findOrFail($id);

    $invoice->update([
      'invoice_status'   => 'rejected',
      'approved_at'      => null,
      'payment_due_date' => null,
    ]);

    return redirect()->back()->with('success', 'Invoice rejected.');
  }

  public function markPaid(Request $request, $id)
  {
    $invoice = Invoice::findOrFail($id);

    if ($invoice->invoice_status !== 'approved') {
      return redirect()->back()->with('warning', 'Only approved invoices can be marked as paid.');
    }

    $request->validate([
      'paid_date'         => ['required', 'date'],
      'payment_reference' => ['nullable', 'string', 'max:191'],
    ]);

    $invoice->update([
      'payment_status'    => 'paid',
      'paid_date'         => $request->paid_date,
      'payment_reference' => $request->payment_reference,
    ]);

    return redirect()->back()->with('success', 'Invoice marked as paid.');
  }
}
