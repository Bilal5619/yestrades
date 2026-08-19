@extends('company.layouts.auth')

@section('title', 'Reset Password')

@section('content')

<div
    style="
        max-width:440px;
        margin:auto;
    "
>

    <h2>
        Reset Password
    </h2>

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
            'company.password.update'
        ) }}"
    >
        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $token }}"
        >

        <label>Email</label>

        <input
            type="email"
            name="email"
            value="{{ old(
                'email',
                $email
            ) }}"
            required
            style="
                width:100%;
                padding:12px;
                margin:7px 0 16px;
                box-sizing:border-box;
            "
        >

        <label>
            New Password
        </label>

        <input
            type="password"
            name="password"
            required
            style="
                width:100%;
                padding:12px;
                margin:7px 0 16px;
                box-sizing:border-box;
            "
        >

        <label>
            Confirm New Password
        </label>

        <input
            type="password"
            name="password_confirmation"
            required
            style="
                width:100%;
                padding:12px;
                margin:7px 0 20px;
                box-sizing:border-box;
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
            Reset Password
        </button>

    </form>

</div>

@endsection