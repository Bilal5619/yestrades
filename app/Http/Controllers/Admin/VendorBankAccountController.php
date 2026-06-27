<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorBankAccount;
use Illuminate\Http\Request;

class VendorBankAccountController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('info');

    $bankAccounts = VendorBankAccount::with(['vendor.vendor_info'])
      ->when($search, function ($query, $search) {
        $query->where(function ($query) use ($search) {
          $query->where('bank_name', 'like', '%' . $search . '%')
            ->orWhere('account_holder_name', 'like', '%' . $search . '%')
            ->orWhere('account_number', 'like', '%' . $search . '%')
            ->orWhereHas('vendor', function ($query) use ($search) {
              $query->where('username', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        });
      })
      ->orderByDesc('is_active')
      ->orderByDesc('id')
      ->paginate(10);

    return view('admin.end-user.vendor-bank-accounts.index', compact('bankAccounts'));
  }

  public function vendorAccounts($id)
  {
    $vendor = Vendor::findOrFail($id);
    $bankAccounts = VendorBankAccount::where('vendor_id', $id)
      ->orderByDesc('is_active')
      ->orderByDesc('id')
      ->paginate(10);

    return view('admin.end-user.vendor-bank-accounts.vendor', compact('vendor', 'bankAccounts'));
  }
}
