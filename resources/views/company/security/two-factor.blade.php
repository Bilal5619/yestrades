@extends('company.layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')

<style>
    .security-card {
        max-width:700px;
        background:#1e2a44;
        border-radius:10px;
        padding:24px;
    }

    .security-muted {
        color:#aab4ca;
    }

    .security-success {
        padding:12px 15px;
        margin-bottom:20px;
        border-radius:7px;
        background:rgba(46,191,114,.15);
        color:#6fe6a4;
    }

    .security-error {
        padding:12px 15px;
        margin-bottom:20px;
        border-radius:7px;
        background:rgba(236,87,98,.15);
        color:#ff9aa2;
    }

    .security-code {
        width:220px;
        padding:12px;
        border-radius:7px;
        border:1px solid rgba(255,255,255,.15);
        background:#162138;
        color:white;
        font-size:20px;
        letter-spacing:5px;
        text-align:center;
    }

    .security-button {
        padding:11px 16px;
        border:0;
        border-radius:7px;
        background:#2878dc;
        color:white;
        cursor:pointer;
    }

    .security-danger {
        background:#b53b48;
    }

    .security-password {
        width:100%;
        max-width:350px;
        box-sizing:border-box;
        padding:11px;
        border-radius:7px;
        border:1px solid rgba(255,255,255,.15);
        background:#162138;
        color:white;
    }
</style>

<h1>
    Two-Factor Authentication
</h1>

<p class="security-muted">
    Protect your Company Portal account with
    an authenticator app.
</p>

@if (session('success'))

    <div class="security-success">
        {{ session('success') }}
    </div>

@endif

@if ($errors->any())

    <div class="security-error">
        {{ $errors->first() }}
    </div>

@endif

<div class="security-card">

    @if ($user->two_factor_enabled)

        <h3
            style="
                color:#6fe6a4;
                margin-top:0;
            "
        >
            <i class="fas fa-shield-alt"></i>
            Two-Factor Authentication Enabled
        </h3>

        <p class="security-muted">
            Your account requires an
            authenticator code when you sign in.
        </p>

        <hr
            style="
                border-color:
                    rgba(255,255,255,.08);
                margin:24px 0;
            "
        >

        <h4>
            Disable Two-Factor Authentication
        </h4>

        <p class="security-muted">
            Enter your password to disable 2FA.
        </p>

        <form
            method="POST"
            action="{{ route(
                'company.security.2fa.disable'
            ) }}"
        >
            @csrf

            <input
                type="password"
                name="password"
                placeholder="Current password"
                class="security-password"
                required
            >

            <div style="margin-top:12px;">

                <button
                    type="submit"
                    class="
                        security-button
                        security-danger
                    "
                >
                    Disable 2FA
                </button>

            </div>

        </form>

    @else

        <h3 style="margin-top:0;">
            Set Up Authenticator App
        </h3>

        <p class="security-muted">
            Scan this QR code using Microsoft
            Authenticator, Google Authenticator,
            Authy or another compatible
            authenticator app.
        </p>

        @if ($qrCode)

            <div
                style="
                    background:white;
                    padding:15px;
                    display:inline-block;
                    border-radius:8px;
                    margin:15px 0;
                "
            >
                <img
                    src="{{ $qrCode }}"
                    alt="2FA QR Code"
                    style="
                        width:200px;
                        height:200px;
                    "
                >
            </div>

        @endif

        <p class="security-muted">
            If you cannot scan the QR code,
            enter this setup key manually:
        </p>

        <div
            style="
                padding:12px;
                background:#162138;
                border-radius:7px;
                display:inline-block;
                font-family:monospace;
                margin-bottom:20px;
            "
        >
            {{ $secret }}
        </div>

        <h4>
            Verify Setup
        </h4>

        <p class="security-muted">
            Enter the 6-digit code shown in
            your authenticator app.
        </p>

        <form
            method="POST"
            action="{{ route(
                'company.security.2fa.enable'
            ) }}"
        >
            @csrf

            <input
                type="text"
                name="code"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                class="security-code"
                placeholder="000000"
                required
            >

            <div style="margin-top:15px;">

                <button
                    type="submit"
                    class="security-button"
                >
                    Enable 2FA
                </button>

            </div>

        </form>

    @endif

</div>

@endsection