<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Company Portal') | Yes Trades
    </title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #172036;
            color: #ffffff;
        }

        .company-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .company-sidebar {
            width: 270px;
            background: #1c263d;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        .company-main {
            margin-left: 270px;
            width: calc(100% - 270px);
            min-height: 100vh;
        }

        .company-content {
            padding: 32px;
        }

        .company-page-title {
            margin: 0 0 25px;
            font-size: 28px;
            font-weight: 600;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
        }

        .dashboard-card {
            min-height: 130px;
            border-radius: 8px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #22304d;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .dashboard-card h3 {
            margin: 0 0 10px;
            font-size: 16px;
            font-weight: 500;
        }

        .dashboard-card strong {
            font-size: 30px;
        }

        .dashboard-card i {
            font-size: 38px;
            opacity: 0.9;
        }

        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .company-sidebar {
                display: none;
            }

            .company-main {
                margin-left: 0;
                width: 100%;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .company-content {
                padding: 20px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="company-wrapper">

    @include('company.partials.sidebar')

    <div class="company-main">

        @include('company.partials.header')

        <main class="company-content">
            @yield('content')
        </main>

    </div>

</div>

@stack('scripts')

</body>
</html>