@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl py-8">
    <h1 class="text-2xl font-bold mb-6">Order Details (Admin)</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif
    <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-2">Order #{{ $order->order_number ?? $order->id }}</h2>
        <p><strong>Status:</strong> {{ ucfirst($order->order_status) }}</p>
        <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
        <p><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
        <p><strong>Order Date:</strong> {{ $order->order_date ? $order->order_date->format('Y-m-d H:i') : $order->created_at->format('Y-m-d H:i') }}</p>
        <p><strong>Total Amount:</strong> UGX {{ number_format($order->total_amount, 0) }}</p>
    </div>
    <div class="bg-white rounded shadow p-6 mb-6">
        <h3 class="text-md font-semibold mb-2">Order Items</h3>
        <table class="min-w-full divide-y divide-gray-200 mb-2">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($order->orderItems as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->yogurtProduct->product_name ?? 'Product' }}</td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">UGX {{ number_format($item->unit_price, 0) }}</td>
                        <td class="px-4 py-2">UGX {{ number_format($item->total_price, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded shadow p-6">
        <h3 class="text-md font-semibold mb-4">Update Payment Status</h3>
        <form method="POST" action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                <select name="payment_status" id="payment_status" class="w-full border rounded px-3 py-2">
                    <option value="pending" @if($order->payment_status == 'pending') selected @endif>Pending</option>
                    <option value="paid" @if($order->payment_status == 'paid') selected @endif>Paid</option>
                    <option value="failed" @if($order->payment_status == 'failed') selected @endif>Failed</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update Payment Status</button>
        </form>
    </div>
</div>
@endsection 