<?php

namespace App\Http\Controllers\Admin\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Admin\Transaction;
use App\Models\BasicSettings\Basic;
use App\Models\Vendor;
use DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
  public function index(Request $request)
  {
    $transaction = $request->transaction_id;
    $data['transactions'] = Transaction::query()->when(
      $transaction,
      function ($query, $transaction) {
        return $query->where('transaction_id', 'like', '%' . $transaction . '%');
      }
    )
      ->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.transaction.index', $data);
  }

  public function destroy($id)
  {
    $transaction = Transaction::find($id);

    if (!$transaction) {
      return redirect()->back()->with('warning', 'Transaction not found.');
    }

    $transaction->delete();

    return redirect()->back()->with('success', 'Transaction deleted successfully.');
  }

  public function bulkDestroy(Request $request)
  {
    $ids = $request->input('ids', []);

    if (!is_array($ids) || count($ids) === 0) {
      return redirect()->back()->with('warning', 'Please select at least one transaction to delete.');
    }

    Transaction::whereIn('id', $ids)->delete();

    return redirect()->back()->with('success', 'Selected transactions deleted successfully.');
  }

  public function destroyAll()
  {
    Transaction::query()->delete();

    return redirect()->back()->with('success', 'All transactions deleted successfully.');
  }


  public function storeTransaction($data)
  {
    $vendorId = (int) ($data['vendor_id'] ?? 0);
    $customerPaid = (float) ($data['customer_paid'] ?? 0);
    $isRefund = isset($data['type']) && $data['type'] == 'refund';
    $vendor = $vendorId != 0 ? Vendor::find($vendorId) : null;
    $commissionPercent = $vendor && $vendor->fixed_commission_percent !== null
      ? (float) $vendor->fixed_commission_percent
      : 0;
    $adminCommission = $vendor ? round(($customerPaid * $commissionPercent) / 100, 2) : 0;
    $vendorEarning = $vendor ? round($customerPaid - $adminCommission, 2) : 0;

    // Check if there is any existing data for the user_id
    $existingTransaction = Transaction::where('vendor_id', $vendorId)->latest()->first();

    //update admin balance
    $admin_profit = Basic::pluck('admin_profit')->first();

    if ($vendorId == 0) {
      if ($isRefund) {
        $adminProfit = $admin_profit - $customerPaid;
      } else {
        $adminProfit = $admin_profit + $customerPaid;
      }
    } else {
      $adminProfit = $isRefund ? $admin_profit - $adminCommission : $admin_profit + $adminCommission;
    }

    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'admin_profit' => $adminProfit
      ]
    );

    //update vendor balance
    if ($vendorId != 0 && $vendor) {
      $vendor_balance = $vendor->amount;
      if ($isRefund) {
        $vendor_new_balance = $vendor_balance - $vendorEarning;
      } else {
        $vendor_new_balance = $vendor_balance + $vendorEarning;
      }

      DB::table('vendors')->updateOrInsert(
        ['id' => $vendorId],
        [
          'amount' => $vendor_new_balance
        ]
      );
    }

    // Calculate pre_balance based on existing data or set to 0 if no data found
    $pre_balance = $existingTransaction ? $existingTransaction->after_balance : 0;

    if ($isRefund) {
      $transactionType = 'booking_refund';
      $afterBalance = $pre_balance - ($vendorId == 0 ? $customerPaid : $vendorEarning);
      $refund_amount = $vendorId == 0 ? $customerPaid : $adminCommission;
      $actaulTotal = NULL;
    } else {
      $transactionType = 'service_booking';
      $afterBalance = $pre_balance + ($vendorId == 0 ? $customerPaid : $vendorEarning);
    }

    $transaction = Transaction::create([
      'transaction_id' => uniqid(),
      'vendor_id' => $vendorId,
      'admin_profit' => $vendorId == 0 ? $customerPaid : $adminCommission,
      'transaction_type' => $transactionType,
      'pre_balance' => $vendorId == 0 ? NULL : $pre_balance,
      'actual_total' => $transactionType == 'booking_refund' ? $actaulTotal : $customerPaid,
      'after_balance' => $vendorId == 0 ? NULL : $afterBalance,
      'payment_method' => $data['paymentMethod'],
      'currency_symbol' => $data['currencySymbol'],
      'currency_symbol_position' => $data['currencySymbolPosition'],
      'payment_status' => $data['paymentStatus'],
      'refund_amount' => $transactionType == 'booking_refund' ? $refund_amount : NULL,
    ]);
    return $transaction;
  }
}
