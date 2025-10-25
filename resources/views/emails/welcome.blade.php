@extends('emails.layout')

@section('content')
<h2>Welcome to Dantata Foods UBMS!</h2>

<p>Hello {{ $notifiable->first_name }} {{ $notifiable->last_name }},</p>

<p>Welcome to Dantata Foods! Your account has been successfully created in our Unified Business Management System (UBMS).</p>

<div class="success-box">
    <p><strong>Your Account Details:</strong></p>
    <p><strong>Employee ID:</strong> {{ $notifiable->employee_id }}</p>
    <p><strong>Email:</strong> {{ $notifiable->email }}</p>
    <p><strong>Role:</strong> {{ $notifiable->role->display_name }}</p>
    <p><strong>Department:</strong> {{ $notifiable->department?->name ?? 'Not assigned' }}</p>
</div>

@if($temporaryPassword)
<div class="alert-box">
    <p><strong>⚠️ Important Security Notice:</strong></p>
    <p><strong>Temporary Password:</strong> <code style="background: #fff; padding: 5px 10px; border-radius: 3px;">{{ $temporaryPassword }}</code></p>
    <p style="margin-top: 10px;"><strong>IMPORTANT:</strong> Please change your password immediately after your first login for security purposes.</p>
</div>
@endif

<a href="{{ url('/login') }}" class="btn">Login to UBMS</a>

<div class="info-box" style="margin-top: 30px;">
    <p><strong>Getting Started:</strong></p>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Log in using your email and temporary password</li>
        <li>Complete your profile information</li>
        <li>Familiarize yourself with your role's dashboard</li>
        <li>Review your assigned tasks and processes</li>
    </ul>
</div>

<p>If you experience any issues accessing your account or have questions about the system, please contact the IT department at <a href="mailto:ict@dantatafoods.com">ict@dantatafoods.com</a>.</p>

<p>We're excited to have you on the team!</p>

<p>Best regards,<br>Dantata Foods IT Team</p>
@endsection
