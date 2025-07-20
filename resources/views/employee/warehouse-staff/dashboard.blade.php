@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employee-dashboard.css') }}">
@endsection

@section('content')
<div class="employee-container">
    <!-- Header -->
    <div class="employee-header">
        <div class="employee-header-info">
            <h1>Warehouse Dashboard</h1>
            <p>Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="employee-header-info">
            <p>Role: {{ $employee->role }}</p>
            <p>Status: <span class="employee-status">{{ ucfirst($employee->status) }}</span></p>
        </div>
    </div>

    <div class="stats-grid mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-blue">📦</div>
            <div class="stat-content">
                <p class="stat-label">Total Orders</p>
                <p class="stat-value" id="ws-total-orders">{{ $totalOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-yellow">⏳</div>
            <div class="stat-content">
                <p class="stat-label">Pending</p>
                <p class="stat-value" id="ws-pending-orders">{{ $pendingOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-purple">🔄</div>
            <div class="stat-content">
                <p class="stat-label">Processing</p>
                <p class="stat-value" id="ws-processing-orders">{{ $processingOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green">🚚</div>
            <div class="stat-content">
                <p class="stat-label">Shipped</p>
                <p class="stat-value" id="ws-shipped-orders">{{ $shippedOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green">✅</div>
            <div class="stat-content">
                <p class="stat-label">Delivered</p>
                <p class="stat-value" id="ws-delivered-orders">{{ $deliveredOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-blue">📦</div>
            <div class="stat-content">
                <p class="stat-label">Total Deliveries</p>
                <p class="stat-value" id="ws-total-deliveries">{{ $totalDeliveries }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="employee-card">
        <h3>Quick Actions</h3>
        <div class="quick-actions-grid">
            <button class="quick-action-btn action-green">
                <span class="quick-action-icon">📦</span>
                <div class="quick-action-title">Update Inventory</div>
                <div class="quick-action-desc">Record stock changes</div>
            </button>
            <button class="quick-action-btn action-blue">
                <span class="quick-action-icon">📋</span>
                <div class="quick-action-title">Stock Count</div>
                <div class="quick-action-desc">Physical inventory count</div>
            </button>
            <button class="quick-action-btn action-yellow">
                <span class="quick-action-icon">⚠️</span>
                <div class="quick-action-title">Report Damage</div>
                <div class="quick-action-desc">Report damaged goods</div>
            </button>
            <button class="quick-action-btn action-purple">
                <span class="quick-action-icon">📊</span>
                <div class="quick-action-title">Inventory Report</div>
                <div class="quick-action-desc">Generate reports</div>
            </button>
        </div>
    </div>

    <!-- Inventory Overview -->
    <div class="data-grid">
        <!-- Current Inventory -->
        <div class="employee-card">
            <h3>Current Inventory</h3>
            @if($inventory->count() > 0)
                <div>
                    @foreach($inventory->take(5) as $item)
                        <div class="notification-item">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="notification-message">{{ $item->product_name ?? 'Product' }}</div>
                                    <div class="notification-time">SKU: {{ $item->sku ?? 'N/A' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="notification-message">{{ $item->quantity ?? 0 }}</div>
                                    <div class="notification-time">in stock</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>No inventory data available</p>
                </div>
            @endif
        </div>

        <!-- Recent Deliveries -->
        <div class="employee-card">
            <h3>Recent Deliveries</h3>
            @if($recentDeliveries->count() > 0)
                <div>
                    @foreach($recentDeliveries as $delivery)
                        <div class="notification-item">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="notification-message">Delivery #{{ $delivery->id }}</div>
                                    <div class="notification-time">{{ $delivery->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <span class="status-badge {{ $delivery->status === 'completed' ? 'status-completed' : 'status-pending' }}">
                                    {{ ucfirst($delivery->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>No recent deliveries</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Orders to Process (Packing & Shipping) -->
    <div class="employee-card">
        <h3>Orders to Process</h3>
        @php
            $ordersToProcess = \App\Models\Order::whereIn('order_status', ['confirmed', 'processing'])
                ->orderBy('created_at', 'asc')->get();
        @endphp
        @if($ordersToProcess->count() > 0)
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Status</th>
                            <th>Customer</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordersToProcess as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ ucfirst($order->order_status) }}</td>
                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($order->order_status === 'confirmed')
                                        <form method="POST" action="{{ route('dashboard.employee.order.packed', $order->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="action-btn action-green">Mark as Packed</button>
                                        </form>
                                    @elseif($order->order_status === 'processing')
                                        <form method="POST" action="{{ route('dashboard.employee.order.shipped', $order->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="action-btn action-blue">Mark as Shipped</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p>No orders to process at this time.</p>
            </div>
        @endif
    </div>

    <!-- Vendor Information -->
    @if($employee->vendor)
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900">Assigned Vendor</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Business Name</p>
                    <p class="font-medium text-gray-900">{{ $employee->vendor->business_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Contact Person</p>
                    <p class="font-medium text-gray-900">{{ $employee->vendor->contact_person ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Phone</p>
                    <p class="font-medium text-gray-900">{{ $employee->vendor->phone_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium text-gray-900">{{ $employee->vendor->contact_email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
function updateWarehouseSummaryCards(data) {
    if (data.totalOrders !== undefined) document.getElementById('ws-total-orders').textContent = data.totalOrders;
    if (data.pendingOrders !== undefined) document.getElementById('ws-pending-orders').textContent = data.pendingOrders;
    if (data.processingOrders !== undefined) document.getElementById('ws-processing-orders').textContent = data.processingOrders;
    if (data.shippedOrders !== undefined) document.getElementById('ws-shipped-orders').textContent = data.shippedOrders;
    if (data.deliveredOrders !== undefined) document.getElementById('ws-delivered-orders').textContent = data.deliveredOrders;
    if (data.totalDeliveries !== undefined) document.getElementById('ws-total-deliveries').textContent = data.totalDeliveries;
}
function fetchWarehouseSummaryStats() {
    fetch('/api/warehouse-summary-stats')
        .then(res => res.json())
        .then(data => updateWarehouseSummaryCards(data));
}
document.addEventListener('DOMContentLoaded', function() {
    fetchWarehouseSummaryStats();
    setInterval(fetchWarehouseSummaryStats, 30000);
});
</script>
@endsection 