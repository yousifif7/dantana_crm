<!DOCTYPE html>
<html>
<head>
    <title>Production Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #1e3a2e; border-bottom: 3px solid #4a9d7e; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .header-info { margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header-info">
        <h1>Production Report</h1>
        <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <h2>Summary</h2>
    <table>
        <tr><td><strong>Total Batches</strong></td><td>{{ $report['summary']->total_batches ?? 0 }}</td></tr>
        <tr><td><strong>Total Quantity</strong></td><td>{{ number_format($report['summary']->total_quantity ?? 0, 2) }} L</td></tr>
        <tr><td><strong>Average Efficiency</strong></td><td>{{ number_format($report['summary']->avg_efficiency ?? 0, 1) }}%</td></tr>
        <tr><td><strong>Total Downtime</strong></td><td>{{ number_format($report['summary']->total_downtime ?? 0, 2) }} hrs</td></tr>
    </table>

    <h2>Production Records</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Batch</th>
                <th>Quantity (L)</th>
                <th>Efficiency</th>
                <th>Downtime (hrs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->production_date->format('Y-m-d') }}</td>
                <td>{{ $record->batch_number }}</td>
                <td>{{ number_format($record->quantity, 2) }}</td>
                <td>{{ $record->efficiency_percentage }}%</td>
                <td>{{ number_format($record->downtime_hours ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
        <p>Dantata Foods Ltd - Unified Business Management System</p>
        <p>&copy; {{ date('Y') }} All Rights Reserved</p>
    </div>
</body>
</html>
