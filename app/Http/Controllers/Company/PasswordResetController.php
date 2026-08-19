<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view(
            'company.auth.forgot-password'
        );
    }

    public function sendResetLink(
        Request $request
    ) {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        $status = Password::broker(
            'company_users'
        )->sendResetLink(
            $request->only('email')
        );

        if (
            $status
            === Password::RESET_LINK_SENT
        ) {
            return back()->with(
                'status',
                'Password reset link sent. Please check your email.'
            );
        }

        return back()
            ->withInput()
            ->withErrors([
                'email' =>
                    'We could not send a reset link to that email address.',
            ]);
    }

    public function showResetForm(
        Request $request,
        $token
    ) {
        return view(
            'company.auth.reset-password',
            [
                'token' => $token,
                'email' =>
                    $request->query('email'),
            ]
        );
    }

    public function reset(
        Request $request
    ) {
        $validated = $request->validate([
            'token' => [
                'required',
            ],

            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $status = Password::broker(
            'company_users'
        )->reset(
            [
                'email' =>
                    $validated['email'],

                'password' =>
                    $validated['password'],

                'password_confirmation' =>
                    $request
                        ->password_confirmation,

                'token' =>
                    $validated['token'],
            ],
            function (
                $user,
                $password
            ) {
                $user->forceFill([
                    'password' =>
                        Hash::make(
                            $password
                        ),

                    'remember_token' =>
                        Str::random(60),
                ])->save();

                event(
                    new PasswordReset(
                        $user
                    )
                );
            }
        );

        if (
            $status
            === Password::PASSWORD_RESET
        ) {
            return redirect()
                ->route('company.login')
                ->with(
                    'success',
                    'Your password has been reset successfully.'
                );
        }

        return back()
            ->withInput(
                $request->only('email')
            )
            ->withErrors([
                'email' =>
                    'This password reset link is invalid or has expired.',
            ]);
    }
}