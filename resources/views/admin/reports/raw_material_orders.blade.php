@extends('layouts.app')

@section('content')
<a href="{{ route('admin.reports.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-semibold mb-6">&larr; Back to Reports</a>
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Raw Material Orders Report</h1>
            <p class="text-gray-500">Track raw material orders, supplier performance, and procurement spend.</p>
        </div>
        <form method="GET" class="flex flex-col md:flex-row gap-2 items-end bg-white rounded-lg shadow px-4 py-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="border rounded px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="border rounded px-3 py-2" />
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold mt-2 md:mt-0">Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <div class="text-purple-700 text-sm mb-1">Total Orders</div>
            <div class="text-2xl font-bold text-purple-700">{{ number_format($totalOrders) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <div class="text-yellow-700 text-sm mb-1">Pending Orders</div>
            <div class="text-2xl font-bold text-yellow-700">{{ number_format($pendingOrders) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <div class="text-green-700 text-sm mb-1">Delivered Orders</div>
            <div class="text-2xl font-bold text-green-700">{{ number_format($deliveredOrders) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <div class="text-blue-700 text-sm mb-1">Total Spend</div>
            <div class="text-2xl font-bold text-blue-700">UGX{{ number_format($totalSpend, 2) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-10">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Raw Material Orders Over Time</h2>
        <hr class="mb-4">
        <canvas id="ordersOverTimeChart" height="80"></canvas>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Raw Material Orders</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-3 py-2 text-left text-purple-700">Supplier</th>
                        <th class="px-3 py-2 text-left text-blue-700">Vendor</th>
                        <th class="px-3 py-2 text-left">Material</th>
                        <th class="px-3 py-2 text-right">Quantity</th>
                        <th class="px-3 py-2 text-right">Unit Price</th>
                        <th class="px-3 py-2 text-right">Total Cost</th>
                        <th class="px-3 py-2 text-center">Status</th>
                        <th class="px-3 py-2 text-center">Order Date</th>
                        <th class="px-3 py-2 text-center">Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-3 py-2">{{ $order->supplier ? $order->supplier->business_name ?? $order->supplier->user->name ?? 'Unknown' : 'Unknown' }}</td>
                            <td class="px-3 py-2">{{ $order->vendor ? $order->vendor->business_name ?? 'Unknown' : 'Unknown' }}</td>
                            <td class="px-3 py-2">{{ $order->material_name }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($order->quantity, 2) }} {{ $order->unit_of_measure }}</td>
                            <td class="px-3 py-2 text-right">UGX{{ number_format($order->unit_price, 2) }}</td>
                            <td class="px-3 py-2 text-right">UGX{{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-3 py-2 text-center">{!! $order->status_badge !!}</td>
                            <td class="px-3 py-2 text-center">{{ $order->order_date ? $order->order_date->format('Y-m-d') : '-' }}</td>
                            <td class="px-3 py-2 text-center">{{ $order->actual_delivery_date ? $order->actual_delivery_date->format('Y-m-d') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-gray-400">No raw material orders found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('ordersOverTimeChart').getContext('2d');
        const ordersData = @json($ordersOverTime);
        const labels = ordersData.map(row => row.period);
        const data = ordersData.map(row => row.total_spend);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Spend (UGX)',
                    data: data,
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124,58,237,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#7c3aed',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
</script> 