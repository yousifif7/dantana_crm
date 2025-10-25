@extends('emails.layout')

@section('content')
<h2>Transaction Rejected</h2>

<p>Hello {{ $notifiable->first_name }},</p>

<p>Your transaction has been rejected.</p>

<div class="error-box">
    <p><strong>Transaction Details:</strong></p>
    <p><strong>Transaction Number:</strong> {{ $transaction->transaction_number }}</p>
    <p><strong>Description:</strong> {{ $transaction->description }}</p>
    <p><strong>Amount:</strong> ₦{{ number_format($transaction->amount, 2) }}</p>
    <p><strong>Type:</strong> {{ ucfirst($transaction->type) }}</p>
    @if($reason)
        <p><strong>Rejection Reason:</strong> {{ $reason }}</p>
    @endif
</div>

<p>Please review the transaction details and resubmit with corrections if necessary.</p>

<a href="{{ url('/transactions/' . $transaction->id) }}" class="btn">View Transaction</a>

<p>If you have questions about this rejection, please contact your supervisor or the approver.</p>

<p>Best regards,<br>Dantata Foods UBMS</p>
@endsection