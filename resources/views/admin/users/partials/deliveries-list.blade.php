<div class="action-buttons">
    <button type="button" class="btn btn-secondary btn-action" onclick="reloadDeliveriesContent()">
        <i class="fas fa-sync"></i> Refresh
    </button>
</div>

<div class="loading-spinner" id="deliveries-loading">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order ID</th>
                <th>Driver</th>
                <th>Distribution Center</th>
                <th>Status</th>
                <th>Delivery Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveries as $delivery)
            <tr>
                <td>{{ $delivery->id }}</td>
                <td>{{ $delivery->order_id ?? 'N/A' }}</td>
                <td>{{ $delivery->driver->name ?? 'N/A' }}</td>
                <td>{{ $delivery->distributionCenter->name ?? 'N/A' }}</td>
                <td>
                    <select class="form-select form-select-sm status-select" data-delivery-id="{{ $delivery->id }}" onchange="updateDeliveryStatus({{ $delivery->id }}, this.value)">
                        <option value="pending" {{ $delivery->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_transit" {{ $delivery->status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="delivered" {{ $delivery->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $delivery->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </td>
                <td>{{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') : 'N/A' }}</td>
                <td>
                    <button class="btn btn-sm btn-info btn-view" data-url="{{ route('admin.deliveries.show', $delivery->id) }}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary btn-edit" data-url="{{ route('admin.deliveries.edit', $delivery->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function reloadDeliveriesContent() {
    const contentDiv = document.getElementById('deliveries-content');
    const loadingDiv = document.getElementById('deliveries-loading');
    
    if (loadingDiv) {
        loadingDiv.classList.add('show');
    }
    
    fetch('/admin/user-management/deliveries/content')
    .then(response => response.text())
    .then(html => {
        contentDiv.innerHTML = html;
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    })
    .catch(error => {
        console.error('Error reloading deliveries content:', error);
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    });
}

function updateDeliveryStatus(deliveryId, status) {
    const loadingDiv = document.getElementById('deliveries-loading');
    
    if (loadingDiv) {
        loadingDiv.classList.add('show');
    }
    
    fetch(`/admin/deliveries/${deliveryId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
        
        if (data.success) {
            showToast('Success', data.message || 'Delivery status updated successfully', 'success');
        } else {
            showToast('Error', data.message || 'Failed to update delivery status', 'error');
        }
    })
    .catch(error => {
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
        showToast('Error', 'An error occurred while updating delivery status', 'error');
        console.error('Error:', error);
    });
}
</script> 