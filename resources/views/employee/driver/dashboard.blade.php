@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/employee-dashboard.css') }}">
@endsection

@section('content')
<div class="employee-container">
    <!-- Header -->
    <div class="employee-header">
        <div class="employee-header-info">
            <h1>Driver Dashboard</h1>
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

    <!-- Delivery Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-blue">🚚</div>
            <div class="stat-content">
                <p class="stat-label">Total Deliveries</p>
                <p class="stat-value">{{ $assignedDeliveries->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green">✅</div>
            <div class="stat-content">
                <p class="stat-label">Completed</p>
                <p class="stat-value">{{ $completedDeliveries }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-yellow">⏳</div>
            <div class="stat-content">
                <p class="stat-label">Pending</p>
                <p class="stat-value">{{ $pendingDeliveries }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="employee-card">
        <h3>Quick Actions</h3>
        <div class="quick-actions-grid">
            <button class="quick-action-btn action-green">
                <span class="quick-action-icon">📋</span>
                <div class="quick-action-title">Start Delivery</div>
                <div class="quick-action-desc">Begin delivery route</div>
            </button>
            <button class="quick-action-btn action-blue">
                <span class="quick-action-icon">✅</span>
                <div class="quick-action-title">Complete Delivery</div>
                <div class="quick-action-desc">Mark delivery complete</div>
            </button>
            <button class="quick-action-btn action-yellow">
                <span class="quick-action-icon">⚠️</span>
                <div class="quick-action-title">Report Issue</div>
                <div class="quick-action-desc">Report delivery problem</div>
            </button>
            <button class="quick-action-btn action-purple">
                <span class="quick-action-icon">📊</span>
                <div class="quick-action-title">Delivery Report</div>
                <div class="quick-action-desc">View delivery history</div>
            </button>
        </div>
    </div>

    <!-- Assigned Deliveries -->
    <div class="employee-card">
        <h3>Assigned Deliveries</h3>
        @if($assignedDeliveries->count() > 0)
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Delivery ID</th>
                            <th>Customer</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedDeliveries as $delivery)
                            <tr>
                                <td class="font-medium">#{{ $delivery->id }}</td>
                                <td>{{ $delivery->customer_name ?? 'N/A' }}</td>
                                <td>{{ $delivery->delivery_address ?? 'N/A' }}</td>
                                <td>
                                    <span class="status-badge {{ $delivery->status === 'completed' ? 'status-completed' : ($delivery->status === 'in_progress' ? 'status-progress' : 'status-pending') }}">
                                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                    </span>
                                </td>
                                <td>{{ $delivery->created_at->format('M d, Y') }}</td>
                                <td class="action-buttons">
                                    @if($delivery->status === 'pending')
                                        <button class="action-btn action-primary">Start</button>
                                    @elseif($delivery->status === 'in_progress')
                                        <button class="action-btn action-success">Complete</button>
                                    @endif
                                    <button class="action-btn action-secondary">View</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p>No deliveries assigned yet</p>
            </div>
        @endif
    </div>

    <!-- Vehicle Information -->
    <div class="employee-card">
        <h3>Vehicle Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <p class="info-label">Vehicle Number</p>
                <p class="info-value">N/A</p>
            </div>
            <div class="info-item">
                <p class="info-label">License Plate</p>
                <p class="info-value">N/A</p>
            </div>
            <div class="info-item">
                <p class="info-label">Last Maintenance</p>
                <p class="info-value">N/A</p>
            </div>
            <div class="info-item">
                <p class="info-label">Fuel Level</p>
                <p class="info-value">N/A</p>
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