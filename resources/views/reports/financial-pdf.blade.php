<!DOCTYPE html>
<html>
<head>
    <title>Financial Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #1e3a2e; border-bottom: 3px solid #4a9d7e; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }
        .header-info { margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header-info">
        <h1>Financial Report</h1>
        <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <h2>Summary</h2>
    <table>
        <tr>
            <td><strong>Total Revenue</strong></td>
            <td style="text-align: right;">₦{{ number_format($summary['revenue'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total Expenses</strong></td>
            <td style="text-align: right;">₦{{ number_format($summary['expenses'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Net Profit</strong></td>
            <td style="text-align: right;">₦{{ number_format($summary['net_profit'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Profit Margin</strong></td>
            <td style="text-align: right;">{{ number_format($summary['profit_margin'], 2) }}%</td>
        </tr>
    </table>

    <h2>Revenue Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Client</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenue as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->client_name ?? '-' }}</td>
                <td style="text-align: right;">₦{{ number_format($transaction->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Expense Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->category ?? '-' }}</td>
                <td style="text-align: right;">₦{{ number_format($transaction->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
        <p>Dantata Foods Ltd - Unified Business Management System</p>
        <p>© {{ date('Y') }} All Rights Reserved</p>
    </div>
</body>
</html>
