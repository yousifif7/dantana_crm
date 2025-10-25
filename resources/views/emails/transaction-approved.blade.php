@extends('emails.layout')

@section('content')
<h2>Transaction Approved</h2>

<p>Hello {{ $notifiable->first_name }},</p>

<p>Good news! Your transaction has been approved.</p>

<div class="success-box">
    <p><strong>Transaction Details:</strong></p>
    <p><strong>Transaction Number:</strong> {{ $transaction->transaction_number }}</p>
    <p><strong>Description:</strong> {{ $transaction->description }}</p>
    <p><strong>Amount:</strong> ₦{{ number_format($transaction->amount, 2) }}</p>
    <p><strong>Type:</strong> {{ ucfirst($transaction->type) }}</p>
    <p><strong>Date:</strong> {{ $transaction->transaction_date->format('F j, Y') }}</p>
    <p><strong>Approved By:</strong> {{ $transaction->approver->full_name }}</p>
    <p><strong>Approved At:</strong> {{ $transaction->approved_at->format('F j, Y \a\t g:i A') }}</p>
</div>

<a href="{{ url('/transactions/' . $transaction->id) }}" class="btn">View Transaction</a>

<p>Best regards,<br>Dantata Foods UBMS</p>
@endsection