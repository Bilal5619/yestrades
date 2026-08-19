@extends('company.layouts.app')

@section('title', 'Two-Factor Authentication')

@section('content')

<div
    style="
        max-width:420px;
        margin:auto;
        text-align:center;
    "
>

    <h2>
        Two-Factor Authentication
    </h2>

    <p
        style="
            color:#aab4ca;
            margin-bottom:22px;
        "
    >
        Open your authenticator app and enter
        the 6-digit verification code.
    </p>

    @if ($errors->any())

        <div
            style="
                color:#ff9aa2;
                margin-bottom:15px;
            "
        >
            {{ $errors->first() }}
        </div>

    @endif

    <form
        method="POST"
        action="{{ route(
            'company.2fa.verify'
        ) }}"
    >
        @csrf

        <input
            type="text"
            name="code"
            maxlength="6"
            inputmode="numeric"
            autocomplete="one-time-code"
            placeholder="000000"
            required
            autofocus
            style="
                width:100%;
                box-sizing:border-box;
                padding:14px;
                text-align:center;
                font-size:24px;
                letter-spacing:8px;
                margin-bottom:18px;
            "
        >

        <button
            type="submit"
            style="
                width:100%;
                padding:12px;
                border:0;
                border-radius:7px;
                background:#2878dc;
                color:white;
            "
        >
            Verify & Continue
        </button>

    </form>

</div>

@endsection