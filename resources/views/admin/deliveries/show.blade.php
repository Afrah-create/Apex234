@extends('layouts.app')

@section('content')
<div class="main-content p-6">
    <h2 class="text-2xl font-bold mb-6">Delivery Details</h2>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p><strong>Delivery #:</strong> {{ $delivery->delivery_number }}</p>
        <p><strong>Status:</strong> {{ ucfirst($delivery->delivery_status) }}</p>
        <p><strong>Order #:</strong> {{ $delivery->order_id }}</p>
        <p><strong>Driver:</strong> {{ $delivery->driver_name ?? 'Unassigned' }}</p>
        <p><strong>Scheduled Date:</strong> {{ $delivery->scheduled_delivery_date }}</p>
        <p><strong>Scheduled Time:</strong> {{ $delivery->scheduled_delivery_time }}</p>
        <p><strong>Delivery Address:</strong> {{ $delivery->delivery_address }}</p>
        <p><strong>Recipient Name:</strong> {{ $delivery->recipient_name }}</p>
        <p><strong>Recipient Phone:</strong> {{ $delivery->recipient_phone }}</p>
        <p><strong>Notes:</strong> {{ $delivery->delivery_notes }}</p>
        <!-- Add more fields as needed -->
    </div>
</div>
@endsection 