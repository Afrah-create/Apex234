@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Raw Material Order Details</h1>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4"><strong>Order ID:</strong> {{ $order->id }}</div>
        <div class="mb-2"><strong>Material Type:</strong> {{ $order->material_type }}</div>
        <div class="mb-2"><strong>Material Name:</strong> {{ $order->material_name }}</div>
        <div class="mb-2"><strong>Quantity:</strong> {{ $order->quantity }} {{ $order->unit_of_measure }}</div>
        <div class="mb-2"><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
        <div class="mb-2"><strong>Order Date:</strong> {{ $order->order_date ? $order->order_date->format('M d, Y H:i') : '-' }}</div>
        <div class="mb-2"><strong>Expected Delivery Date:</strong> {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('M d, Y') : '-' }}</div>
        <div class="mb-2"><strong>Actual Delivery Date:</strong> {{ $order->actual_delivery_date ? $order->actual_delivery_date->format('M d, Y') : '-' }}</div>
        <div class="mb-2"><strong>Notes:</strong> {{ $order->notes ?? '-' }}</div>
        <div class="mb-2"><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'pending') }}</div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('supplier.raw-material-orders.updatePaymentStatus', $order->id) }}" class="mt-4">
            @csrf
            @method('PATCH')
            <label for="payment_status" class="block mb-1 font-semibold">Update Payment Status</label>
            <select name="payment_status" id="payment_status" class="w-full border rounded px-3 py-2 mb-2">
                <option value="pending" @if($order->payment_status == 'pending') selected @endif>Pending</option>
                <option value="paid" @if($order->payment_status == 'paid') selected @endif>Paid</option>
                <option value="failed" @if($order->payment_status == 'failed') selected @endif>Failed</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update Payment Status</button>
        </form>
    </div>
    <a href="{{ url()->previous() }}" class="inline-block mt-6 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Back</a>
</div>
@endsection 