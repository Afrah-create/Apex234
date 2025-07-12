@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Order Details</h1>
    <div class="bg-white shadow rounded p-6 mb-4">
        <p><strong>Order #:</strong> {{ $order->order_number }}</p>
        <p><strong>Date:</strong> {{ $order->order_date }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->order_status) }}</p>
        <p><strong>Total:</strong> {{ $order->total_amount ?? '-' }}</p>
        <p><strong>Delivery Address:</strong> {{ $order->delivery_address }}</p>
        <p><strong>Delivery Contact:</strong> {{ $order->delivery_contact }}</p>
        <p><strong>Delivery Phone:</strong> {{ $order->delivery_phone }}</p>
        <p><strong>Special Instructions:</strong> {{ $order->special_instructions ?? '-' }}</p>
        <p><strong>Notes:</strong> {{ $order->notes ?? '-' }}</p>
    </div>
    <a href="{{ route('customer.orders.index') }}" class="text-blue-600 hover:underline">Back to My Orders</a>
</div>
@endsection 