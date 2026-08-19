<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FALaravel\Facade
    as Google2FA;

class TwoFactorController extends Controller
{
    public function settings(
        Request $request
    ) {
        $user = Auth::guard(
            'company'
        )->user();

        $secret = null;
        $qrCode = null;

        if (
            !$user->two_factor_enabled
        ) {
            $secret = $request
                ->session()
                ->get(
                    'company_2fa_setup_secret'
                );

            if (!$secret) {
                $secret =
                    Google2FA::generateSecretKey();

                $request
                    ->session()
                    ->put(
                        'company_2fa_setup_secret',
                        $secret
                    );
            }

            Google2FA::setQRCodeBackend(
                'svg'
            );

            $qrCode =
                Google2FA::getQRCodeInline(
                    'Yes Trades',
                    $user->email,
                    $secret
                );
        }

        return view(
            'company.security.two-factor',
            compact(
                'user',
                'secret',
                'qrCode'
            )
        );
    }

    public function enable(
        Request $request
    ) {
        $validated = $request->validate([
            'code' => [
                'required',
                'digits:6',
            ],
        ]);

        $secret = $request
            ->session()
            ->get(
                'company_2fa_setup_secret'
            );

        if (!$secret) {
            return back()->withErrors([
                'code' =>
                    'Your 2FA setup session has expired. Please try again.',
            ]);
        }

        $valid =
            Google2FA::verifyKey(
                $secret,
                $validated['code']
            );

        if (!$valid) {
            return back()->withErrors([
                'code' =>
                    'The verification code is incorrect.',
            ]);
        }

        $user = Auth::guard(
            'company'
        )->user();

        $user->update([
            'two_factor_secret' =>
                $secret,

            'two_factor_enabled_at' =>
                now(),
        ]);

        $request
            ->session()
            ->forget(
                'company_2fa_setup_secret'
            );

        return redirect()
            ->route(
                'company.security.2fa'
            )
            ->with(
                'success',
                'Two-factor authentication has been enabled.'
            );
    }

    public function disable(
        Request $request
    ) {
        $validated =
            $request->validate([
                'password' => [
                    'required',
                    'string',
                ],
            ]);

        $user = Auth::guard(
            'company'
        )->user();

        if (
            !Hash::check(
                $validated['password'],
                $user->password
            )
        ) {
            return back()->withErrors([
                'password' =>
                    'Your password is incorrect.',
            ]);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled_at' => null,
        ]);

        return redirect()
            ->route(
                'company.security.2fa'
            )
            ->with(
                'success',
                'Two-factor authentication has been disabled.'
            );
    }

    public function showChallenge(
        Request $request
    ) {
        if (
            !$request
                ->session()
                ->has(
                    'company_2fa_user_id'
                )
        ) {
            return redirect()
                ->route(
                    'company.login'
                );
        }

        return view(
            'company.auth.two-factor-challenge'
        );
    }

    public function verifyChallenge(
        Request $request
    ) {
        $validated =
            $request->validate([
                'code' => [
                    'required',
                    'digits:6',
                ],
            ]);

        $userId = $request
            ->session()
            ->get(
                'company_2fa_user_id'
            );

        if (!$userId) {
            return redirect()
                ->route(
                    'company.login'
                );
        }

        $user =
            CompanyUser::find(
                $userId
            );

        if (
            !$user
            ||
            !$user->status
            ||
            !$user->two_factor_secret
        ) {
            $request
                ->session()
                ->forget([
                    'company_2fa_user_id',
                    'company_2fa_remember',
                ]);

            return redirect()
                ->route(
                    'company.login'
                )
                ->withErrors([
                    'email' =>
                        'Unable to verify this account.',
                ]);
        }

        $valid =
            Google2FA::verifyKey(
                $user->two_factor_secret,
                $validated['code']
            );

        if (!$valid) {
            return back()->withErrors([
                'code' =>
                    'The verification code is incorrect.',
            ]);
        }

        $remember = (bool) $request
            ->session()
            ->pull(
                'company_2fa_remember',
                false
            );

        $request
            ->session()
            ->forget(
                'company_2fa_user_id'
            );

        Auth::guard(
            'company'
        )->login(
            $user,
            $remember
        );

        $request
            ->session()
            ->regenerate();

        $user->update([
            'last_login_at' => now(),
        ]);

        return redirect()
            ->intended(
                route(
                    'company.dashboard'
                )
            );
    }
}