<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Deactive
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_email_verification', 'vendor_admin_approval', 'admin_approval_notice')->first();

    $vendor = Auth::guard('vendor')->user();
    if ($vendor->status == 0) {
      if ($request->routeIs('vendor.dashboard')) {
        return redirect()->route('vendor.account_setup');
      }

      if ($request->isMethod('POST') || $request->isMethod('PUT')) {
        // Don't tell the vendor to contact admin unless their verification was rejected.
        if ($vendor->verification_status === 'rejected') {
          $message = !empty($vendor->verification_rejection_reason)
            ? 'Your verification was rejected: ' . $vendor->verification_rejection_reason . '. Please re-upload your documents or contact the admin.'
            : 'Your verification was rejected. Please re-upload your documents or contact the admin.';
        } elseif ($vendor->verification_status === 'submitted') {
          $message = 'Waiting for approval. Your verification has been submitted successfully. Approval can take up to 24 hours.';
        } else {
          $message = 'Your account is pending approval. Please complete your Account Setup Tasks to get verified.';
        }
        session()->flash('warning', $message);
        return redirect()->back();
      }
    }
    if (Auth::guard('vendor')->user()->email_verified_at == null && $setting->vendor_email_verification == 1) {
      return redirect()->route('vendor.login');
    }
    return $next($request);
  }
}
