<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        Employment Contract
    </title>

    <style>
        @page {
            margin: 40px 45px;
        }

        body {
            margin: 0;
            color: #111111;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        h1 {
            margin: 0 0 24px;
            font-size: 24px;
        }

        h2 {
            margin-top: 22px;
            font-size: 17px;
        }

        h3 {
            margin-top: 18px;
            font-size: 14px;
        }

        p {
            margin: 0 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #dddddd;
            text-align: left;
        }

        .contract-footer {
            margin-top: 35px;
            padding-top: 12px;
            border-top: 1px solid #cccccc;
            color: #666666;
            font-size: 10px;
        }
    </style>
</head>

<body>

    {!! $content !!}

    <div class="contract-footer">
        Contract version {{ $contract->version }}
        — Generated {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>