@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employee-dashboard.css') }}">
@endsection

@section('content')
<div class="employee-container">
    <!-- Header -->
    <div class="employee-header">
        <div class="employee-header-info">
            <h1>Sales Manager Dashboard</h1>
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

    <!-- Sales Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-blue">📊</div>
            <div class="stat-content">
                <p class="stat-label">Total Sales</p>
                <p class="stat-value">${{ number_format($totalSales, 2) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green">📈</div>
            <div class="stat-content">
                <p class="stat-label">Orders This Month</p>
                <p class="stat-value">{{ $monthlyOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-yellow">👥</div>
            <div class="stat-content">
                <p class="stat-label">Active Customers</p>
                <p class="stat-value">{{ $activeCustomers }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-purple">🎯</div>
            <div class="stat-content">
                <p class="stat-label">Conversion Rate</p>
                <p class="stat-value">{{ $conversionRate }}%</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="employee-card">
        <h3>Quick Actions</h3>
        <div class="quick-actions-grid">
            <button class="quick-action-btn action-green">
                <span class="quick-action-icon">📋</span>
                <div class="quick-action-title">Create Report</div>
                <div class="quick-action-desc">Generate sales report</div>
            </button>
            <button class="quick-action-btn action-blue">
                <span class="quick-action-icon">👥</span>
                <div class="quick-action-title">Customer Analysis</div>
                <div class="quick-action-desc">Analyze customer data</div>
            </button>
            <button class="quick-action-btn action-yellow">
                <span class="quick-action-icon">📊</span>
                <div class="quick-action-title">Sales Forecast</div>
                <div class="quick-action-desc">Predict future sales</div>
            </button>
            <button class="quick-action-btn action-purple">
                <span class="quick-action-icon">🎯</span>
                <div class="quick-action-title">Set Targets</div>
                <div class="quick-action-desc">Set sales targets</div>
            </button>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="employee-card">
        <h3>Recent Orders</h3>
        @if($recentOrders->count() > 0)
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="font-medium">#{{ $order->id }}</td>
                                <td>{{ $order->customer_name ?? 'N/A' }}</td>
                                <td>${{ number_format($order->total_amount ?? 0, 2) }}</td>
                                <td>
                                    <span class="status-badge {{ $order->status === 'completed' ? 'status-completed' : ($order->status === 'processing' ? 'status-progress' : 'status-pending') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="action-buttons">
                                    <button class="action-btn action-primary">View</button>
                                    <button class="action-btn action-secondary">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p>No recent orders</p>
            </div>
        @endif
    </div>

    <!-- Top Products -->
    <div class="employee-card">
        <h3>Top Selling Products</h3>
        @if($topProducts->count() > 0)
            <div>
                @foreach($topProducts as $product)
                    <div class="notification-item">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="notification-message">{{ $product->name ?? 'Product' }}</div>
                                <div class="notification-time">SKU: {{ $product->sku ?? 'N/A' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="notification-message">{{ $product->total_sold ?? 0 }} sold</div>
                                <div class="notification-time">${{ number_format($product->total_revenue ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p>No product data available</p>
            </div>
        @endif
    </div>

    <!-- Customer Insights -->
    <div class="data-grid">
        <div class="employee-card">
            <h3>Customer Segments</h3>
            <div>
                <div class="notification-item">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="notification-message">Premium Customers</div>
                            <div class="notification-time">High-value customers</div>
                        </div>
                        <div class="text-right">
                            <div class="notification-message">{{ $premiumCustomers ?? 0 }}</div>
                            <div class="notification-time">customers</div>
                        </div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="notification-message">Regular Customers</div>
                            <div class="notification-time">Medium-value customers</div>
                        </div>
                        <div class="text-right">
                            <div class="notification-message">{{ $regularCustomers ?? 0 }}</div>
                            <div class="notification-time">customers</div>
                        </div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="notification-message">New Customers</div>
                            <div class="notification-time">First-time buyers</div>
                        </div>
                        <div class="text-right">
                            <div class="notification-message">{{ $newCustomers ?? 0 }}</div>
                            <div class="notification-time">customers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="employee-card">
            <h3>Sales Performance</h3>
            <div>
                <div class="notification-item">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="notification-message">This Month</div>
                            <div class="notification-time">Current period</div>
                        </div>
                        <div class="text-right">
                            <div class="notification-message">${{ number_format($monthlySales ?? 0, 2) }}</div>
                            <div class="notification-time">{{ $monthlyGrowth ?? 0 }}% growth</div>
                        </div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="notification-message">Last Month</div>
                            <div class="notification-time">Previous period</div>
                        </div>
                        <div class="text-right">
                            <div class="notification-message">${{ number_format($lastMonthSales ?? 0, 2) }}</div>
                            <div class="notification-time">Previous total</div>
                        </div>
                    </div>
                </div>
                <div class="notification-item">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="notification-message">Target</div>
                            <div class="notification-time">Monthly goal</div>
                        </div>
                        <div class="text-right">
                            <div class="notification-message">${{ number_format($monthlyTarget ?? 0, 2) }}</div>
                            <div class="notification-time">{{ $targetProgress ?? 0 }}% achieved</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Information -->
    @if($employee->vendor)
        <div class="employee-card">
            <h3>Assigned Vendor</h3>
            <div class="info-grid">
                <div class="info-item">
                    <p class="info-label">Business Name</p>
                    <p class="info-value">{{ $employee->vendor->business_name }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Contact Person</p>
                    <p class="info-value">{{ $employee->vendor->contact_person ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Phone</p>
                    <p class="info-value">{{ $employee->vendor->phone_number ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Email</p>
                    <p class="info-value">{{ $employee->vendor->contact_email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 