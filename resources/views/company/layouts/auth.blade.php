<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Company Portal')
        - Yes Trades
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

            background: #172036;
            color: #ffffff;

            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 460px;
            padding: 24px;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .auth-logo strong {
            display: block;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .auth-logo span {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #aab4ca;
        }

        .auth-card {
            background: #1e2a44;
            border-radius: 12px;
            padding: 28px;

            box-shadow:
                0 12px 35px
                rgba(0, 0, 0, .22);
        }

        .auth-card h1,
        .auth-card h2 {
            margin-top: 0;
        }

        .auth-card a {
            color: #82b7ff;
        }

        .auth-card input {
            border-radius: 7px;
            border:
                1px solid
                rgba(255, 255, 255, .15);

            background: #162138;
            color: #ffffff;
        }

        .auth-card button {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">

        <div class="auth-logo">
            <strong>YES TRADES</strong>

            <span>
                COMPANY PORTAL
            </span>
        </div>

        <div class="auth-card">
            @yield('content')
        </div>

    </div>

</body>

</html>