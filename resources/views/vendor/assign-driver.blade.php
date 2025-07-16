@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Assign Drivers to Orders</h1>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if($orders->isEmpty())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            No orders currently need driver assignment.
        </div>
    @else
        <table class="min-w-full bg-white border border-gray-200 rounded mb-6">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Order ID</th>
                    <th class="px-4 py-2 text-left">Retailer</th>
                    <th class="px-4 py-2 text-left">Product(s)</th>
                    <th class="px-4 py-2 text-left">Assign Driver</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td class="px-4 py-2">{{ $order->id }}</td>
                        <td class="px-4 py-2">{{ $order->retailer->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            @foreach($order->orderItems as $item)
                                <div>{{ $item->product_name }} (x{{ $item->quantity }})</div>
                            @endforeach
                        </td>
                        <td class="px-4 py-2">
                            <form action="{{ route('vendor.orders.assignDriver', $order->id) }}" method="POST">
                                @csrf
                                <select name="driver_id" class="border rounded px-2 py-1 mr-2" required>
                                    <option value="">Select Driver</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">
                                            {{ $driver->name }}
                                            @if($driver->user)
                                                - {{ $driver->user->email }}
                                            @endif
                                            @if($driver->status)
                                                ({{ ucfirst($driver->status) }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Assign</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @if(!$drivers->isEmpty())
        <h2 class="text-xl font-semibold mt-8 mb-2">Available Drivers</h2>
        <table class="min-w-full bg-white border border-gray-200 rounded mb-6">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($drivers as $driver)
                    <tr>
                        <td class="px-4 py-2">{{ $driver->name }}</td>
                        <td class="px-4 py-2">{{ $driver->user->email ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ ucfirst($driver->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <a href="{{ route('dashboard.vendor') }}" class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Back to Dashboard</a>
</div>
@endsection 