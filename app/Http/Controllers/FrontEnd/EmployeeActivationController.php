<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use Illuminate\Http\Request;

/**
 * Public page (emailed link) where a newly-created employee sets their
 * password to activate their HR mobile-app account. This does not log
 * them into the Company/HR web portal — that access is intentionally
 * separate and is never granted here.
 */
class EmployeeActivationController extends Controller
{
  public function show(string $token)
  {
    $employee = Employee::query()->where('activation_token', $token)->first();

    $expired = !$employee || !$employee->activation_token_expires_at || $employee->activation_token_expires_at->isPast();

    return view('frontend.employee.activate', [
      'token' => $token,
      'employee' => $employee,
      'expired' => $expired,
    ]);
  }

  public function store(Request $request, string $token)
  {
    $employee = Employee::query()
      ->where('activation_token', $token)
      ->whereNotNull('activation_token_expires_at')
      ->where('activation_token_expires_at', '>=', now())
      ->first();

    if (!$employee) {
      return redirect()
        ->route('frontend.employee.activate.show', $token)
        ->with('error', 'This activation link is invalid or has expired. Please ask HR to send you a new one.');
    }

    $data = $request->validate([
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $employee->activateWithPassword($data['password']);

    return view('frontend.employee.activated', [
      'employee' => $employee,
    ]);
  }
}
