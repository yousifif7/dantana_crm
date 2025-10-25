@extends('emails.layout')

@section('content')
<h2>⚠️ Low Stock Alert</h2>

<p>Hello {{ $notifiable->first_name }},</p>

<p><strong>URGENT:</strong> An inventory item has reached its reorder level and requires immediate attention.</p>

<div class="error-box">
    <p><strong>Item Details:</strong></p>
    <p><strong>Item Name:</strong> {{ $item->name }}</p>
    <p><strong>Item Code:</strong> {{ $item->item_code }}</p>
    <p><strong>Current Stock:</strong> {{ $item->stock_quantity }} {{ $item->unit_of_measure }}</p>
    <p><strong>Reorder Level:</strong> {{ $item->reorder_level }} {{ $item->unit_of_measure }}</p>
    <p><strong>Status:</strong> <span style="color: #dc3545; font-weight: bold;">{{ strtoupper($item->status) }}</span></p>
</div>

<p>Please initiate a reorder for this item as soon as possible to avoid stockouts.</p>

<a href="{{ url('/inventory/' . $item->id) }}" class="btn">View Item Details</a>

<p>Best regards,<br>Dantata Foods UBMS</p>
@endsection
