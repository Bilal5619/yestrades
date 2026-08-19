@extends('company.layouts.auth')

@section('title', 'Forgot Password')

@section('content')

<div
    style="
        max-width:440px;
        margin:auto;
    "
>

    <h2>
        Forgot Password
    </h2>

    <p
        style="
            color:#aab4ca;
            margin-bottom:22px;
        "
    >
        Enter your Company Portal email address
        and we'll send you a password reset link.
    </p>

    @if (session('status'))

        <div
            style="
                background:rgba(46,191,114,.15);
                color:#6fe6a4;
                padding:12px;
                border-radius:7px;
                margin-bottom:16px;
            "
        >
            {{ session('status') }}
        </div>

    @endif

    @if ($errors->any())

        <div
            style="
                color:#ff9aa2;
                margin-bottom:16px;
            "
        >
            {{ $errors->first() }}
        </div>

    @endif

    <form
        method="POST"
        action="{{ route(
            'company.password.email'
        ) }}"
    >
        @csrf

        <label>
            Email Address
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            style="
                width:100%;
                padding:12px;
                margin-top:7px;
                margin-bottom:18px;
                box-sizing:border-box;
            "
        >

        <button
            type="submit"
            style="
                width:100%;
                padding:12px;
                background:#2878dc;
                color:white;
                border:0;
                border-radius:7px;
            "
        >
            Send Reset Link
        </button>

    </form>

    <div
        style="
            text-align:center;
            margin-top:18px;
        "
    >
        <a
            href="{{ route(
                'company.login'
            ) }}"
        >
            Back to Login
        </a>
    </div>

</div>

@endsection