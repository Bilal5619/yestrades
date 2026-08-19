<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (
            Auth::guard('company')->check()
        ) {
            return redirect()
                ->route(
                    'company.dashboard'
                );
        }

        return view(
            'company.auth.login'
        );
    }

    public function login(
        Request $request
    ) {
        $credentials =
            $request->validate([
                'email' => [
                    'required',
                    'email',
                ],

                'password' => [
                    'required',
                    'string',
                ],
            ]);

        $remember =
            $request->boolean(
                'remember'
            );

        if (
            !Auth::guard(
                'company'
            )->attempt(
                $credentials,
                $remember
            )
        ) {
            return back()
                ->withInput(
                    $request->only(
                        'email'
                    )
                )
                ->withErrors([
                    'email' =>
                        'The email address or password is incorrect.',
                ]);
        }

        $companyUser =
            Auth::guard(
                'company'
            )->user();

        if (!$companyUser->status) {
            Auth::guard(
                'company'
            )->logout();

            $request
                ->session()
                ->invalidate();

            $request
                ->session()
                ->regenerateToken();

            return back()
                ->withErrors([
                    'email' =>
                        'Your company account has been disabled.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2FA challenge
        |--------------------------------------------------------------------------
        */

        if (
            $companyUser
                ->two_factor_enabled
        ) {
            $request
                ->session()
                ->put(
                    'company_2fa_user_id',
                    $companyUser->id
                );

            $request
                ->session()
                ->put(
                    'company_2fa_remember',
                    $remember
                );

            Auth::guard(
                'company'
            )->logout();

            $request
                ->session()
                ->regenerate();

            return redirect()
                ->route(
                    'company.2fa.challenge'
                );
        }

        $request
            ->session()
            ->regenerate();

        $companyUser->update([
            'last_login_at' => now(),
        ]);

        return redirect()
            ->intended(
                route(
                    'company.dashboard'
                )
            );
    }

    public function logout(
        Request $request
    ) {
        Auth::guard(
            'company'
        )->logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return redirect()
            ->route(
                'company.login'
            )
            ->with(
                'success',
                'You have been logged out successfully.'
            );
    }
}