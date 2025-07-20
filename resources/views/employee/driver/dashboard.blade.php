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

    <!-- Delivery Statistics -->
    <div class="stats-grid mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-blue">📦</div>
            <div class="stat-content">
                <p class="stat-label">Total Orders</p>
                <p class="stat-value">{{ $totalOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green">✅</div>
            <div class="stat-content">
                <p class="stat-label">Delivered Orders</p>
                <p class="stat-value">{{ $deliveredOrders }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-yellow">⏳</div>
            <div class="stat-content">
                <p class="stat-label">Pending Deliveries</p>
                <p class="stat-value">{{ $pendingDeliveries }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-green">🚚</div>
            <div class="stat-content">
                <p class="stat-label">Completed Deliveries</p>
                <p class="stat-value">{{ $completedDeliveries }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="employee-card">
        <h3>Quick Actions</h3>
        <div class="quick-actions-grid">
            <!-- Remove Start Delivery and Complete Delivery buttons -->
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
                <tbody id="deliveries-table-body">
                    @foreach($assignedDeliveries as $delivery)
                        <tr>
                            <td class="font-medium">#{{ $delivery->id }}</td>
                            <td>
                                @if($delivery->order && $delivery->order->customer)
                                    {{ $delivery->order->customer->name }}
                                @elseif($delivery->retailer)
                                    {{ $delivery->retailer->store_name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $delivery->delivery_address ?? 'N/A' }}</td>
                            <td>{{ ucfirst($delivery->delivery_status) }}</td>
                            <td>{{ $delivery->created_at ? $delivery->created_at->format('M d, Y') : '' }}</td>
                            <td class="action-buttons">
                                <button class="action-btn action-secondary" onclick="toggleOrderDetails({{ $delivery->id }})">View</button>
                                @if(in_array($delivery->delivery_status, ['scheduled', 'out_for_delivery']))
                                    <form method="POST" action="{{ route('dashboard.employee.delivery.delivered', $delivery->id) }}" enctype="multipart/form-data" style="display:inline;">
                                        @csrf
                                        <input type="file" name="proof_photo" accept="image/*" style="display:inline-block; margin-right:8px;" required>
                                        <button type="submit" class="action-btn action-green">Mark as Delivered</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        <tr id="order-details-{{ $delivery->id }}" style="display:none; background:#f9f9f9;">
                            <td colspan="6">
                                <div style="padding:16px;">
                                    <strong>Order Details</strong><br>
                                    <ul>
                                        <li><b>Retailer:</b> {{ $delivery->retailer->store_name ?? 'N/A' }}</li>
                                        <li><b>Address:</b> {{ $delivery->delivery_address }}</li>
                                        <li><b>Contact:</b> {{ $delivery->recipient_name ?? 'N/A' }} ({{ $delivery->recipient_phone ?? 'N/A' }})</li>
                                        <li><b>Order Number:</b> {{ $delivery->order->order_number ?? 'N/A' }}</li>
                                        <li><b>Order Items:</b>
                                            <ul>
                                            @foreach($delivery->order && $delivery->order->orderItems ? $delivery->order->orderItems : [] as $item)
                                                <li>{{ $item->yogurtProduct->product_name ?? 'Product' }} - {{ $item->quantity }} x {{ $item->unit_price }} UGX</li>
                                            @endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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

<script>
function fetchDeliveries() {
    fetch("{{ route('driver.assigned-deliveries') }}")
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('deliveries-table-body');
            tbody.innerHTML = '';
            if (!data.deliveries || data.deliveries.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">No deliveries assigned yet</td></tr>';
            } else {
                data.deliveries.forEach(delivery => {
                    let customerName = 'N/A';
                    if (delivery.order && delivery.order.customer) {
                        customerName = delivery.order.customer.name;
                    } else if (delivery.retailer) {
                        customerName = delivery.retailer.store_name;
                    }
                    let actionButtons = `<button class="action-btn action-secondary" onclick="toggleOrderDetails(${delivery.id})">View</button>`;
                    if (['scheduled', 'out_for_delivery'].includes(delivery.delivery_status)) {
                        actionButtons += `
                            <form method="POST" action="/dashboard/employee/delivery/${delivery.id}/delivered" enctype="multipart/form-data" style="display:inline;">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').getAttribute('content')}">
                                <input type="file" name="proof_photo" accept="image/*" style="display:inline-block; margin-right:8px;" required>
                                <button type="submit" class="action-btn action-green">Mark as Delivered</button>
                            </form>
                        `;
                    }
                    tbody.innerHTML += `
                        <tr>
                            <td class="font-medium">#${delivery.id}</td>
                            <td>${customerName}</td>
                            <td>${delivery.delivery_address ?? 'N/A'}</td>
                            <td>${delivery.delivery_status ? delivery.delivery_status.charAt(0).toUpperCase() + delivery.delivery_status.slice(1) : ''}</td>
                            <td>${delivery.created_at ? new Date(delivery.created_at).toLocaleDateString() : ''}</td>
                            <td class="action-buttons">${actionButtons}</td>
                        </tr>
                        <tr id="order-details-${delivery.id}" style="display:none; background:#f9f9f9;">
                            <td colspan="6">
                                <div style="padding:16px;">
                                    <strong>Order Details</strong><br>
                                    <ul>
                                        <li><b>Retailer:</b> ${delivery.retailer ? (delivery.retailer.store_name ?? 'N/A') : 'N/A'}</li>
                                        <li><b>Address:</b> ${delivery.delivery_address}</li>
                                        <li><b>Contact:</b> ${delivery.recipient_name ?? 'N/A'} (${delivery.recipient_phone ?? 'N/A'})</li>
                                        <li><b>Order Number:</b> ${delivery.order ? (delivery.order.order_number ?? 'N/A') : 'N/A'}</li>
                                        <li><b>Order Items:</b>
                                            <ul>
                                                ${(delivery.order && delivery.order.order_items) ? delivery.order.order_items.map(item =>
                                                    `<li>${item.yogurt_product ? (item.yogurt_product.product_name ?? 'Product') : 'Product'} - ${item.quantity} x ${item.unit_price} UGX</li>`
                                                ).join('') : ''}
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
        });
}
fetchDeliveries();
setInterval(fetchDeliveries, 30000);

function toggleOrderDetails(id) {
    const row = document.getElementById('order-details-' + id);
    if (row) {
        row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
    }
}
</script> 