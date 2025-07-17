@extends('layouts.app')

@section('content')
<main class="main-content bg-gradient-to-br from-blue-50 via-green-50 to-white min-h-screen py-8">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-4xl font-extrabold text-blue-700 mb-8 text-center tracking-tight drop-shadow">Order History</h2>
        <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-10">
            @if($orders && count($orders))
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm rounded-xl">
                    <thead class="bg-gradient-to-r from-blue-200 via-green-100 to-emerald-100">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Order Date
                                </span>
                            </th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                                    Product(s)
                                </span>
                            </th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 9V3H8v6M3 13h18M5 21h14a2 2 0 002-2v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2z"/></svg>
                                    Quantity
                                </span>
                            </th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Status
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @foreach($order->orderItems as $item)
                                <tr class="border-b hover:bg-blue-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d H:i') : '-' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap font-semibold text-orange-600">{{ $item->yogurtProduct->product_name ?? '-' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap font-bold text-green-700">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($order->order_status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->order_status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="text-center text-gray-500 py-12">
                    <svg class="mx-auto mb-4 w-16 h-16 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2M3 3h18v6H3V3zm0 8h18v10H3V11z"/></svg>
                    <div class="text-lg font-semibold">No orders found.</div>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection 