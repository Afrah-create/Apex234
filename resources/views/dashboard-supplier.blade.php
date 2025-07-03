@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-blue-900 mb-2">Welcome, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-700">This is your supplier dashboard. Here you can manage your supplies, deliveries, and monitor your performance at a glance.</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-2xl font-bold text-blue-900">{{ $totalSupplied }}</span>
            <span class="text-gray-600 mt-2">Total Raw Materials Supplied</span>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-2xl font-bold text-yellow-600">{{ $pendingDeliveries }}</span>
            <span class="text-gray-600 mt-2">Pending Deliveries</span>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-2xl font-bold text-green-600">{{ $deliveredBatches }}</span>
            <span class="text-gray-600 mt-2">Delivered Batches</span>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <span class="text-2xl font-bold text-purple-700">
                Milk: {{ $inventorySummary['milk']['qty'] ?? 0 }}{{ $inventorySummary['milk']['unit'] ?? 'L' }}<br>
                Sugar: {{ $inventorySummary['sugar']['qty'] ?? 0 }}{{ $inventorySummary['sugar']['unit'] ?? 'kg' }}<br>
                Fruits: {{ $inventorySummary['fruit']['qty'] ?? 0 }}{{ $inventorySummary['fruit']['unit'] ?? 'kg' }}
            </span>
            <span class="text-gray-600 mt-2">Current Inventory</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8 flex flex-wrap gap-4">
        <a href="{{ route('supplier.raw-material-inventory') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded shadow font-semibold transition">Raw Material Inventory</a>
        <a href="{{ route('supplier.manage-orders') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded shadow font-semibold transition">Manage Orders</a>
        <a href="{{ route('supplier.add-raw-material') }}" class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded shadow font-semibold transition">Add New Supply</a>
        <a href="{{ route('supplier.profile') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded shadow font-semibold transition">Profile & Settings</a>
    </div>

    <!-- Notifications/Alerts -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-blue-900 mb-2">Notifications & Alerts</h2>
        <ul class="list-disc pl-6 text-red-600">
            <li>Low stock: Sugar below 10kg!</li>
            <li>2 pending quality checks for recent milk batches.</li>
        </ul>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-blue-900 mb-4">Recent Activity</h2>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Material</th>
                    <th class="px-4 py-2 text-left">Quantity</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-4 py-2">2024-06-27</td>
                    <td class="px-4 py-2">Milk</td>
                    <td class="px-4 py-2">100L</td>
                    <td class="px-4 py-2"><span class="text-green-600 font-semibold">Delivered</span></td>
                </tr>
                <tr>
                    <td class="px-4 py-2">2024-06-26</td>
                    <td class="px-4 py-2">Sugar</td>
                    <td class="px-4 py-2">20kg</td>
                    <td class="px-4 py-2"><span class="text-yellow-600 font-semibold">Pending</span></td>
                </tr>
                <tr>
                    <td class="px-4 py-2">2024-06-25</td>
                    <td class="px-4 py-2">Fruits</td>
                    <td class="px-4 py-2">15kg</td>
                    <td class="px-4 py-2"><span class="text-green-600 font-semibold">Delivered</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection 