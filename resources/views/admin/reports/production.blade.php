@extends('layouts.app')

@section('content')
<a href="{{ route('admin.reports.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-semibold mb-6">&larr; Back to Reports</a>
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Production Report</h1>
            <p class="text-gray-500">Overview of production batches, units produced, and trends by product.</p>
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
            <div class="text-blue-700 text-sm mb-1">Total Batches</div>
            <div class="text-3xl font-bold text-green-700">{{ number_format($totalBatches) }}</div>
        </div>
        <div class="flex-1 bg-white rounded-lg shadow p-6 flex flex-col items-center justify-center">
            <div class="text-blue-700 text-sm mb-1">Total Units Produced</div>
            <div class="text-3xl font-bold text-green-700">{{ number_format($totalUnits) }}</div>
        </div>
    </div>

    <div class="mb-16">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Production Over Time</h2>
            <hr class="mb-4">
            <canvas id="productionOverTimeChart" height="80"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col mt-8 md:mb-0">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Production by Product</h2>
            <hr class="mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left text-blue-700">Product</th>
                            <th class="px-3 py-2 text-right text-blue-700">Batches</th>
                            <th class="px-3 py-2 text-right text-blue-700">Units Produced</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productionByProduct as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row['product_name'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['batches']) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['units_produced']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Add more production breakdowns here in the future -->
        <div class="bg-white rounded-lg shadow p-6 flex flex-col mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Production by Vendor</h2>
            <hr class="mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left text-blue-700">Vendor</th>
                            <th class="px-3 py-2 text-right text-blue-700">Batches</th>
                            <th class="px-3 py-2 text-right text-blue-700">Units Produced</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productionByVendor as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row['vendor_name'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['batches']) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['units_produced']) }}</td>
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
        const ctx = document.getElementById('productionOverTimeChart').getContext('2d');
        const prodData = @json($productionOverTime);
        const labels = prodData.map(row => row.period);
        const data = prodData.map(row => row.total_units);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Units Produced',
                    data: data,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#16a34a',
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