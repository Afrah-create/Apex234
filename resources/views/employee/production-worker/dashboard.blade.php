@extends('layouts.app')

@section('styles')
<!-- Employee Dashboard CSS -->
<link rel="stylesheet" href="{{ asset('css/employee-dashboard.css') }}">
@endsection

@section('content')
<div class="employee-container">
    <!-- Header -->
    <div class="employee-header">
        <div class="employee-header-info">
            <h1>Production Dashboard</h1>
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
                <span class="quick-action-icon">📋</span>
                <div class="quick-action-title">Quality Check</div>
                <div class="quick-action-desc">Record quality inspection</div>
            </button>
            <button class="quick-action-btn action-blue">
                <span class="quick-action-icon">📦</span>
                <div class="quick-action-title">Production Update</div>
                <div class="quick-action-desc">Update production status</div>
            </button>
            <button class="quick-action-btn action-yellow">
                <span class="quick-action-icon">⚠️</span>
                <div class="quick-action-title">Report Issue</div>
                <div class="quick-action-desc">Report production problem</div>
            </button>
            <button class="quick-action-btn action-purple">
                <span class="quick-action-icon">📊</span>
                <div class="quick-action-title">View Reports</div>
                <div class="quick-action-desc">Production reports</div>
            </button>
        </div>
    </div>

    <!-- Production Overview -->
    <div class="data-grid">
        <!-- Quality Checks -->
        <div class="employee-card">
            <h3>Recent Quality Checks</h3>
            @if($qualityChecks->count() > 0)
                <div>
                    @foreach($qualityChecks as $check)
                        <div class="notification-item">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="notification-message">Quality Check #{{ $check->id }}</div>
                                    <div class="notification-time">{{ $check->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <span class="status-badge {{ $check->status === 'passed' ? 'status-completed' : 'status-pending' }}">
                                    {{ ucfirst($check->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>No recent quality checks</p>
                </div>
            @endif
        </div>

        <!-- Raw Materials Status -->
        <div class="employee-card">
            <h3>Raw Materials Status</h3>
            @if($rawMaterials->count() > 0)
                <div>
                    @foreach($rawMaterials->take(5) as $material)
                        <div class="notification-item">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="notification-message">{{ $material->name }}</div>
                                    <div class="notification-time">{{ $material->type }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="notification-message">{{ $material->quantity ?? 'N/A' }}</div>
                                    <div class="notification-time">{{ $material->unit ?? 'units' }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>No raw materials data</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Vendor Information -->
    @if($employee->vendor)
        <div class="employee-card">
            <h3>Assigned Vendor</h3>
            <div class="vendor-info">
                <div class="vendor-info-item">
                    <div class="vendor-info-label">Business Name</div>
                    <div class="vendor-info-value">{{ $employee->vendor->business_name }}</div>
                </div>
                <div class="vendor-info-item">
                    <div class="vendor-info-label">Contact Person</div>
                    <div class="vendor-info-value">{{ $employee->vendor->contact_person ?? 'N/A' }}</div>
                </div>
                <div class="vendor-info-item">
                    <div class="vendor-info-label">Phone</div>
                    <div class="vendor-info-value">{{ $employee->vendor->phone_number ?? 'N/A' }}</div>
                </div>
                <div class="vendor-info-item">
                    <div class="vendor-info-label">Email</div>
                    <div class="vendor-info-value">{{ $employee->vendor->contact_email ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 