@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold">Track Deliveries</h2>
            <a href="{{ route('supplier.drivers') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Manage Drivers</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm mb-2">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Delivery #</th>
                        <th class="px-4 py-2 text-left">Order ID</th>
                        <th class="px-4 py-2 text-left">Scheduled Date</th>
                        <th class="px-4 py-2 text-left">Scheduled Time</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Recipient</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($deliveries as $delivery)
                    <tr>
                        <td class="px-4 py-2">{{ $delivery->delivery_number }}</td>
                        <td class="px-4 py-2">{{ $delivery->order_id }}</td>
                        <td class="px-4 py-2">{{ $delivery->scheduled_delivery_date }}</td>
                        <td class="px-4 py-2">{{ $delivery->scheduled_delivery_time }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($delivery->delivery_status == 'scheduled') bg-yellow-100 text-yellow-800
                                @elseif($delivery->delivery_status == 'in_transit') bg-blue-100 text-blue-800
                                @elseif($delivery->delivery_status == 'out_for_delivery') bg-purple-100 text-purple-800
                                @elseif($delivery->delivery_status == 'delivered') bg-green-100 text-green-800
                                @elseif($delivery->delivery_status == 'failed' || $delivery->delivery_status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($delivery->delivery_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $delivery->recipient_name }}</td>
                        <td class="px-4 py-2">
                            <a href="#" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-gray-500">No deliveries found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 