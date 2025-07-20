@extends('layouts.app')

@section('content')
<a href="{{ route('admin.reports.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-semibold mb-6">&larr; Back to Reports</a>
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Inventory Report</h1>
            <p class="text-gray-500">Overview of current stock levels, low stock alerts, and inventory breakdowns.</p>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <div class="text-blue-700 text-sm mb-1">Total Products</div>
            <div class="text-2xl font-bold text-blue-700">{{ number_format($totalProducts) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <div class="text-blue-700 text-sm mb-1">Total Stock</div>
            <div class="text-2xl font-bold text-blue-700">{{ number_format($totalStock) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <div class="text-blue-700 text-sm mb-1">Low Stock Items</div>
            <div class="text-2xl font-bold text-blue-700">{{ number_format($lowStockItems) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col mt-8 md:mb-0">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventory by Product</h2>
            <hr class="mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left text-blue-700">Product</th>
                            <th class="px-3 py-2 text-right text-blue-700">Total Stock</th>
                            <th class="px-3 py-2 text-right text-blue-700">Low Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryByProduct as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row['product_name'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['total_stock']) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['low_stock']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventory by Distribution Center</h2>
            <hr class="mb-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left text-blue-700">Distribution Center</th>
                            <th class="px-3 py-2 text-right text-blue-700">Total Stock</th>
                            <th class="px-3 py-2 text-right text-blue-700">Low Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryByCenter as $row)
                            <tr>
                                <td class="px-3 py-2">{{ $row['center_name'] }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['total_stock']) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format($row['low_stock']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 