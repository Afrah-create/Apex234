@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-blue-900 mb-2">Welcome, {{ Auth::user()->name }}!</h1>
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
  

    <!-- Notifications/Alerts -->
    

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-blue-900 mb-4">Recent Activity</h2>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Activity Type</th>
                    <th class="px-4 py-2 text-left">Material</th>
                    <th class="px-4 py-2 text-left">Quantity</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Notes</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentActivities as $activity)
                <tr>
                    <td class="px-4 py-2">{{ $activity['date'] ? \Carbon\Carbon::parse($activity['date'])->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-2">{{ $activity['type'] }}</td>
                    <td class="px-4 py-2">{{ $activity['material'] }}</td>
                    <td class="px-4 py-2">{{ $activity['quantity'] }}</td>
                    <td class="px-4 py-2">
                        @php
                            $status = strtolower($activity['status']);
                            $color = 'text-gray-600';
                            if ($status === 'delivered' || $status === 'available') $color = 'text-green-600 font-semibold';
                            elseif ($status === 'pending' || $status === 'processing' || $status === 'scheduled') $color = 'text-yellow-600 font-semibold';
                            elseif ($status === 'expired' || $status === 'disposed' || $status === 'cancelled') $color = 'text-red-600 font-semibold';
                        @endphp
                        <span class="{{ $color }}">{{ $activity['status'] }}</span>
                    </td>
                    <td class="px-4 py-2">{{ $activity['notes'] ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-2 text-gray-400">No recent activity found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 