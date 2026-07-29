<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CompanyPermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::guard('company')->user();

        if (!$user) {
            return redirect()->route('company.login');
        }

        if (!$user->status) {
            Auth::guard('company')->logout();

            return redirect()
                ->route('company.login')
                ->withErrors([
                    'email' => 'Your company account has been disabled.',
                ]);
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}