<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Company Login | Yes Trades</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                linear-gradient(
                    135deg,
                    rgba(12, 22, 42, 0.98),
                    rgba(26, 40, 68, 0.98)
                );
            color: #ffffff;
        }

        .company-login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .company-login-brand {
            padding: 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(12, 21, 39, 0.55);
        }

        .company-brand-logo {
            width: 85px;
            height: 85px;
            margin-bottom: 28px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #172036;
            font-size: 39px;
        }

        .company-login-brand h1 {
            margin: 0 0 15px;
            font-size: 42px;
        }

        .company-login-brand p {
            max-width: 500px;
            margin: 0;
            color: #b8c3d8;
            line-height: 1.7;
            font-size: 17px;
        }

        .company-login-area {
            padding: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .company-login-card {
            width: 100%;
            max-width: 450px;
            padding: 38px;
            border-radius: 14px;
            background: #ffffff;
            color: #172036;
            box-shadow: 0 20px 55px rgba(0, 0, 0, 0.28);
        }

        .company-login-card h2 {
            margin: 0 0 8px;
            font-size: 27px;
        }

        .company-login-subtitle {
            margin: 0 0 28px;
            color: #667085;
            font-size: 14px;
        }

        .company-form-group {
            margin-bottom: 20px;
        }

        .company-form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .company-input-wrapper {
            position: relative;
        }

        .company-input-wrapper i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #8b96a9;
        }

        .company-input {
            width: 100%;
            height: 48px;
            padding: 0 15px 0 44px;
            border: 1px solid #d5d9e2;
            border-radius: 7px;
            outline: none;
            font-size: 15px;
        }

        .company-input:focus {
            border-color: #2878dc;
            box-shadow: 0 0 0 3px rgba(40, 120, 220, 0.12);
        }

        .company-remember {
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 9px;
            color: #475467;
            font-size: 14px;
        }

        .company-login-button {
            width: 100%;
            height: 49px;
            border: 0;
            border-radius: 7px;
            background: #2878dc;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .company-login-button:hover {
            background: #1f67bd;
        }

        .company-alert {
            margin-bottom: 20px;
            padding: 13px 15px;
            border-radius: 7px;
            font-size: 14px;
        }

        .company-alert-error {
            background: #fee4e2;
            color: #b42318;
            border: 1px solid #fecdca;
        }

        .company-alert-success {
            background: #dcfae6;
            color: #067647;
            border: 1px solid #abefc6;
        }

        .company-field-error {
            display: block;
            margin-top: 7px;
            color: #b42318;
            font-size: 13px;
        }

        @media (max-width: 850px) {
            .company-login-page {
                grid-template-columns: 1fr;
            }

            .company-login-brand {
                display: none;
            }

            .company-login-area {
                min-height: 100vh;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="company-login-page">

    <section class="company-login-brand">

        <div class="company-brand-logo">
            <i class="fas fa-building"></i>
        </div>

        <h1>Yes Trades</h1>

        <p>
            Secure company management portal for HR, staff records,
            attendance, recruitment, training, reports and internal
            operations.
        </p>

    </section>

    <section class="company-login-area">

        <div class="company-login-card">

            <h2>Company Portal Login</h2>

            <p class="company-login-subtitle">
                Sign in using your authorised Yes Trades company account.
            </p>

            @if (session('success'))
                <div class="company-alert company-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="company-alert company-alert-error">
                    Please check your email address and password.
                </div>
            @endif

            <form method="POST"
                  action="{{ route('company.login.submit') }}">

                @csrf

                <div class="company-form-group">
                    <label for="email">
                        Email address
                    </label>

                    <div class="company-input-wrapper">
                        <i class="fas fa-envelope"></i>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="company-input"
                            value="{{ old('email') }}"
                            placeholder="Enter your company email"
                            autocomplete="email"
                            required
                            autofocus
                        >
                    </div>

                    @error('email')
                        <span class="company-field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="company-form-group">
                    <label for="password">
                        Password
                    </label>

                    <div class="company-input-wrapper">
                        <i class="fas fa-lock"></i>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="company-input"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    @error('password')
                        <span class="company-field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <label class="company-remember">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                    >

                    Keep me signed in
                </label>
<div
    style="
        text-align:right;
        margin-top:8px;
        margin-bottom:15px;
    "
>
    <a
        href="{{ route(
            'company.password.request'
        ) }}"
        style="
            color:#82b7ff;
            font-size:13px;
            text-decoration:none;
        "
    >
        Forgot password?
    </a>
</div>
                <button type="submit"
                        class="company-login-button">

                    <i class="fas fa-right-to-bracket"></i>
                    Sign in
                </button>

            </form>

        </div>

    </section>

</div>

</body>
</html>