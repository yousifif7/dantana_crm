@extends('emails.layout')

@section('content')
<h2>Item Escalated to You</h2>

<p>Hello {{ $notifiable->first_name }},</p>

<p>A {{ class_basename($escalation->escalatable_type) }} has been escalated to you and requires your attention.</p>

<div class="alert-box">
    <p><strong>Escalation Details:</strong></p>
    <p><strong>Reason:</strong> {{ $escalation->reason }}</p>
    <p><strong>From:</strong> {{ $escalation->fromUser->full_name }} ({{ $escalation->fromUser->role->display_name }})</p>
    <p><strong>Escalated At:</strong> {{ $escalation->escalated_at->format('F j, Y \a\t g:i A') }}</p>
    @if($escalation->description)
        <p><strong>Description:</strong> {{ $escalation->description }}</p>
    @endif
</div>

<p>Please review this item and take appropriate action as soon as possible.</p>

<a href="{{ url('/escalations/' . $escalation->id) }}" class="btn">View Escalation Details</a>

<p>If you have any questions, please contact the person who escalated this item.</p>

<p>Best regards,<br>Dantata Foods UBMS</p>
@endsection
