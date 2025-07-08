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

    <!-- Notifications Section -->
    @php $notifications = auth()->user()->notifications; @endphp
    @if($notifications->count())
        <div class="employee-card">
            <h3>Recent Notifications</h3>
            <div>
                @foreach($notifications->take(3) as $notification)
                    <div class="notification-item">
                        <div class="notification-message">{{ $notification->data['message'] ?? 'You have a new notification.' }}</div>
                        <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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

    <!-- Product Availability -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-900">Product Availability</h3>
        @if($yogurtProducts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($yogurtProducts as $product)
                    <div class="p-4 border rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-medium text-gray-900">{{ $product->name ?? 'Product' }}</div>
                            <span class="px-2 py-1 text-xs rounded-full {{ ($product->stock ?? 0) > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ($product->stock ?? 0) > 10 ? 'In Stock' : 'Low Stock' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <div>Stock: {{ $product->stock ?? 0 }} units</div>
                            <div>Type: {{ $product->type ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No product data available</p>
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
@endsection 