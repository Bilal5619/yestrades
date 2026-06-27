<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BankAccountController extends Controller
{
  public function index()
  {
    $bankAccounts = VendorBankAccount::where('vendor_id', Auth::guard('vendor')->id())
      ->orderByDesc('is_active')
      ->orderByDesc('id')
      ->paginate(10);

    return view('vendors.bank-accounts.index', compact('bankAccounts'));
  }

  public function store(Request $request)
  {
    $data = $this->validatedData($request);
    $vendorId = Auth::guard('vendor')->id();

    DB::transaction(function () use ($data, $vendorId, $request) {
      $makeActive = $request->boolean('is_active') || !VendorBankAccount::where('vendor_id', $vendorId)->exists();

      if ($makeActive) {
        VendorBankAccount::where('vendor_id', $vendorId)->update(['is_active' => 0]);
      }

      $data['vendor_id'] = $vendorId;
      $data['is_active'] = $makeActive ? 1 : 0;

      VendorBankAccount::create($data);
    });

    Session::flash('success', 'Bank account added successfully!');
    return redirect()->route('vendor.bank_accounts.index');
  }

  public function update(Request $request, $id)
  {
    $bankAccount = $this->vendorBankAccount($id);
    $data = $this->validatedData($request);

    $bankAccount->update($data);

    Session::flash('success', 'Bank account updated successfully!');
    return redirect()->route('vendor.bank_accounts.index');
  }

  public function activate($id)
  {
    $bankAccount = $this->vendorBankAccount($id);

    DB::transaction(function () use ($bankAccount) {
      VendorBankAccount::where('vendor_id', Auth::guard('vendor')->id())->update(['is_active' => 0]);
      $bankAccount->update(['is_active' => 1]);
    });

    Session::flash('success', 'Active bank account updated successfully!');
    return redirect()->route('vendor.bank_accounts.index');
  }

  public function destroy($id)
  {
    $bankAccount = $this->vendorBankAccount($id);
    $wasActive = $bankAccount->is_active;

    DB::transaction(function () use ($bankAccount, $wasActive) {
      $vendorId = Auth::guard('vendor')->id();
      $bankAccount->delete();

      if ($wasActive) {
        $nextAccount = VendorBankAccount::where('vendor_id', $vendorId)->latest()->first();
        if ($nextAccount) {
          $nextAccount->update(['is_active' => 1]);
        }
      }
    });

    Session::flash('success', 'Bank account deleted successfully!');
    return redirect()->route('vendor.bank_accounts.index');
  }

  private function validatedData(Request $request)
  {
    return $request->validate([
      'account_holder_name' => 'required|string|max:255',
      'bank_name' => 'required|string|max:255',
      'account_number' => 'required|string|max:255',
      'iban' => 'nullable|string|max:255',
      'branch_name' => 'nullable|string|max:255',
      'swift_code' => 'nullable|string|max:255',
      'routing_number' => 'nullable|string|max:255',
      'notes' => 'nullable|string|max:1000',
    ]);
  }

  private function vendorBankAccount($id)
  {
    return VendorBankAccount::where('vendor_id', Auth::guard('vendor')->id())->findOrFail($id);
  }
}
