@extends('emails.layout')

@section('content')
<h2>⚠️ Process Overdue - Action Required</h2>

<p>Hello {{ $notifiable->first_name }},</p>

<p><strong>URGENT:</strong> A process assigned to you is overdue and requires immediate attention.</p>

<div class="error-box">
    <p><strong>Process Details:</strong></p>
    <p><strong>Process:</strong> {{ $process->name }}</p>
    <p><strong>Process Number:</strong> {{ $process->process_number }}</p>
    <p><strong>Due Date:</strong> {{ $process->due_date->format('F j, Y') }}</p>
    <p><strong>Days Overdue:</strong> {{ now()->diffInDays($process->due_date) }} days</p>
    <p><strong>Priority:</strong> {{ $process->priority === 1 ? 'Critical' : ($process->priority === 2 ? 'High' : 'Medium') }}</p>
    <p><strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $process->status)) }}</p>
</div>

@if($process->description)
<div class="info-box">
    <p><strong>Description:</strong></p>
    <p>{{ $process->description }}</p>
</div>
@endif

<p>Please complete this process immediately to avoid further delays.</p>

<a href="{{ url('/processes/' . $process->id) }}" class="btn">View Process</a>

<p>If you need assistance or have any issues completing this process, please contact your supervisor immediately.</p>

<p>Best regards,<br>Dantata Foods UBMS</p>
@endsection