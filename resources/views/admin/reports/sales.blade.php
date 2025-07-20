@extends('layouts.app')

@section('content')
<a href="{{ route('admin.reports.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-semibold mb-6">&larr; Back to Reports</a>
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Sales Report</h1>
            <p class="text-gray-500">Overview of sales performance, trends, and breakdowns by product and vendor.</p>
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

    <div class="flex flex-col md:flex-row gap-6 mb-10">
        <div class="flex-1 bg-white rounded-lg shadow p-6 flex flex-col items-center justify-center">
            <div class="text-gray-500 text-sm mb-1">Total Sales</div>
            <div class="text-3xl font-bold text-blue-700">UGX {{ number_format($totalSales, 0) }}</div>
        </div>
    </div>

    <div class="mb-16">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Sales Over Time</h2>
            <hr class="mb-4">
            <canvas id="salesOverTimeChart" height="80"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col mt-8 md:mb-0">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Sales by Product</h2>
            <hr class="mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left">Product</th>
                            <th class="px-3 py-2 text-right">Units Sold</th>
                            <th class="px-3 py-2 text-right">Total Sales (UGX)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByProduct as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row['product_name'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['units_sold']) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['total_sales'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Sales by Distribution Center</h2>
            <hr class="mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left">Distribution Center</th>
                            <th class="px-3 py-2 text-right">Units Sold</th>
                            <th class="px-3 py-2 text-right">Total Sales (UGX)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByDistributionCenter as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row['center_name'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['units_sold']) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['total_sales'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesOverTimeChart').getContext('2d');
        const salesData = @json($salesOverTime);
        const labels = salesData.map(row => row.period);
        const data = salesData.map(row => row.total_sales);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Sales (UGX)',
                    data: data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#2563eb',
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
@endsection 