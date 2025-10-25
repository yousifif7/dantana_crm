@extends('emails.layout')

@section('content')
<h2>Daily Operations Report - {{ $date }}</h2>

<p>Hello {{ $recipient->first_name }},</p>

<p>Here is your daily operations summary for {{ $date }}.</p>

<h3 style="color: #1e3a2e; margin-top: 30px;">Financial Summary</h3>
<table>
    <tr>
        <th>Metric</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td>Total Revenue</td>
        <td style="color: #28a745; font-weight: bold;">₦{{ number_format($financial['revenue'], 2) }}</td>
    </tr>
    <tr>
        <td>Total Expenses</td>
        <td style="color: #dc3545; font-weight: bold;">₦{{ number_format($financial['expenses'], 2) }}</td>
    </tr>
    <tr>
        <td>Net Profit</td>
        <td style="font-weight: bold;">₦{{ number_format($financial['net_profit'], 2) }}</td>
    </tr>
    <tr>
        <td>Profit Margin</td>
        <td>{{ number_format($financial['profit_margin'], 2) }}%</td>
    </tr>
</table>

<h3 style="color: #1e3a2e; margin-top: 30px;">Production Summary</h3>
<table>
    <tr>
        <th>Metric</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>Total Production</td>
        <td>{{ number_format($production['total_quantity'], 2) }} L</td>
    </tr>
    <tr>
        <td>Average Efficiency</td>
        <td>{{ number_format($production['avg_efficiency'], 2) }}%</td>
    </tr>
    <tr>
        <td>Total Batches</td>
        <td>{{ $production['total_batches'] }}</td>
    </tr>
    <tr>
        <td>Total Downtime</td>
        <td>{{ number_format($production['total_downtime'], 2) }} hrs</td>
    </tr>
</table>

@if(count($alerts) > 0)
<h3 style="color: #dc3545; margin-top: 30px;">⚠️ Alerts & Notifications</h3>
<div class="alert-box">
    <ul style="margin: 10px 0; padding-left: 20px;">
        @foreach($alerts as $alert)
            <li>{{ $alert }}</li>
        @endforeach
    </ul>
</div>
@endif

<a href="{{ url('/dashboard') }}" class="btn">View Full Dashboard</a>

<p>Best regards,<br>Dantata Foods UBMS</p>
@endsection