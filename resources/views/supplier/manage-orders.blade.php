@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 mb-1">Manage Incoming Orders</h1>
            <p class="text-gray-600">Review and process raw material orders from vendors. Check availability and update order status.</p>
        </div>
        <div class="mt-4 md:mt-0 flex justify-end">
            <a href="{{ route('supplier.drivers') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Manage Drivers</a>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="order-stats">
        <div class="bg-blue-100 text-blue-900 rounded-lg p-6 flex flex-col items-center shadow">
            <div class="text-2xl font-bold" id="total-orders">0</div>
            <div class="text-lg mt-1">Total Orders</div>
        </div>
        <div class="bg-yellow-100 text-yellow-900 rounded-lg p-6 flex flex-col items-center shadow">
            <div class="text-2xl font-bold" id="pending-orders">0</div>
            <div class="text-lg mt-1">Pending</div>
        </div>
        <div class="bg-green-100 text-green-900 rounded-lg p-6 flex flex-col items-center shadow">
            <div class="text-2xl font-bold" id="confirmed-orders">0</div>
            <div class="text-lg mt-1">Confirmed</div>
        </div>
        <div class="bg-red-100 text-red-900 rounded-lg p-6 flex flex-col items-center shadow">
            <div class="text-2xl font-bold" id="unavailable-orders">0</div>
            <div class="text-lg mt-1">Unavailable</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
        <select id="status-filter" class="w-full md:w-1/4 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
            <option value="unavailable">Unavailable</option>
        </select>
        <select id="material-filter" class="w-full md:w-1/4 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Materials</option>
            <option value="milk">Milk</option>
            <option value="sugar">Sugar</option>
            <option value="fruit">Fruit</option>
        </select>
        <input type="text" id="search-input" class="w-full md:w-1/3 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Search by vendor name or order ID...">
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Order ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left font-semibold">Material</th>
                        <th class="px-4 py-3 text-left font-semibold">Quantity</th>
                        <th class="px-4 py-3 text-left font-semibold">Total Amount</th>
                        <th class="px-4 py-3 text-left font-semibold">Order Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Expected Delivery</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-body">
                    <tr>
                        <td colspan="9" class="text-center py-12 text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold">Loading orders...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reject Order Modal -->
    <div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-40 z-40 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative mx-2">
            <button id="close-reject-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
            <h2 class="text-xl font-bold mb-4">Reject Order</h2>
            <form id="reject-form" class="space-y-4">
                <input type="hidden" id="reject-order-id">
                <div>
                    <label class="block font-semibold mb-1">Reason for Rejection</label>
                    <textarea id="reject-reason" class="w-full border rounded px-3 py-2 h-24" placeholder="Please provide a reason for rejecting this order..." required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancel-reject" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Reject Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delivery Creation Modal -->
    <div id="create-delivery-modal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative mx-2">
            <button id="close-create-delivery-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            <h2 class="text-xl font-bold mb-4">Create Delivery</h2>
            <form id="create-delivery-form" class="space-y-4">
                <input type="hidden" id="delivery-order-id" name="order_id">
                <div>
                    <label class="block font-semibold mb-1">Distribution Center ID</label>
                    <input type="number" id="distribution_center_id" name="distribution_center_id" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Vendor ID</label>
                    <input type="number" id="vendor_id" name="vendor_id" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Vendor Name</label>
                    <input type="text" id="vendor_name" name="vendor_name" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Vendor Address</label>
                    <input type="text" id="vendor_address" name="vendor_address" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Vendor Phone</label>
                    <input type="text" id="vendor_phone" name="vendor_phone" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Vehicle Number</label>
                    <input type="text" id="vehicle_number" name="vehicle_number" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Driver Name</label>
                    <input type="text" id="driver_name" name="driver_name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Driver Phone</label>
                    <input type="text" id="driver_phone" name="driver_phone" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Driver License</label>
                    <input type="text" id="driver_license" name="driver_license" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Scheduled Delivery Date</label>
                    <input type="date" id="scheduled_delivery_date" name="scheduled_delivery_date" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Scheduled Delivery Time</label>
                    <input type="time" id="scheduled_delivery_time" name="scheduled_delivery_time" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Delivery Address</label>
                    <input type="text" id="delivery_address" name="delivery_address" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Recipient Name</label>
                    <input type="text" id="recipient_name" name="recipient_name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Recipient Phone</label>
                    <input type="text" id="recipient_phone" name="recipient_phone" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancel-create-delivery" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Create Delivery</button>
                </div>
                <div id="create-delivery-error" class="text-red-600 text-sm mt-2 hidden"></div>
                <div id="create-delivery-success" class="text-green-600 text-sm mt-2 hidden"></div>
            </form>
        </div>
    </div>
</div>

<script>
let allOrders = [];
let filteredOrders = [];

// Load order statistics
async function loadOrderStats() {
    try {
        const res = await fetch('/api/supplier/orders/stats');
        const stats = await res.json();
        
        document.getElementById('total-orders').textContent = stats.total_orders;
        document.getElementById('pending-orders').textContent = stats.pending_orders;
        document.getElementById('confirmed-orders').textContent = stats.confirmed_orders;
        document.getElementById('unavailable-orders').textContent = stats.unavailable_orders;
    } catch (error) {
        console.error('Error loading order stats:', error);
    }
}

// Load incoming orders
async function loadIncomingOrders() {
    try {
        const res = await fetch('/api/supplier/orders/incoming');
        const data = await res.json();
        allOrders = data.orders || [];
        filteredOrders = allOrders;
        renderOrders();
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('orders-body').innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-12 text-gray-400">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span class="text-lg font-semibold">Error loading orders</span>
                    </div>
                </td>
            </tr>
        `;
    }
}

// Render orders table
function renderOrders() {
    const tbody = document.getElementById('orders-body');
    
    if (filteredOrders.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-12 text-gray-400">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <span class="text-lg font-semibold">No orders found</span>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = '';
    filteredOrders.forEach(order => {
        const tr = document.createElement('tr');
        tr.className = 'border-b hover:bg-gray-50';
        tr.innerHTML = `
            <td class="px-4 py-3 font-medium">#${order.id}</td>
            <td class="px-4 py-3">
                <div class="font-medium">${order.vendor_name}</div>
                <div class="text-xs text-gray-500">${order.vendor_email}</div>
            </td>
            <td class="px-4 py-3">
                <div class="font-medium">${order.material_name}</div>
                <div class="text-xs text-gray-500 capitalize">${order.material_type}</div>
            </td>
            <td class="px-4 py-3">${order.quantity} ${order.unit_of_measure}</td>
            <td class="px-4 py-3">UGX ${parseFloat(order.total_amount).toLocaleString()}</td>
            <td class="px-4 py-3">${new Date(order.order_date).toLocaleDateString()}</td>
            <td class="px-4 py-3">${order.expected_delivery_date || '-'}</td>
            <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs font-semibold ${getStatusColor(order.status)}">
                    ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                </span>
            </td>
            <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                    ${getActionButtons(order)}
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Get status color for badges
function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-blue-100 text-blue-800',
        'processing': 'bg-purple-100 text-purple-800',
        'shipped': 'bg-indigo-100 text-indigo-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'unavailable': 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

// Get action buttons based on order status
function getActionButtons(order) {
    let buttons = '';
    
    switch (order.status) {
        case 'pending':
            buttons += `<button onclick="confirmOrder(${order.id})" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">Confirm</button>`;
            buttons += `<button onclick="rejectOrder(${order.id})" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs">Reject</button>`;
            break;
        case 'confirmed':
            buttons += `<button onclick="processOrder(${order.id})" class="bg-purple-600 hover:bg-purple-700 text-white px-2 py-1 rounded text-xs">Process</button>`;
            buttons += `<button onclick="rejectOrder(${order.id})" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs">Reject</button>`;
            break;
        case 'processing':
            buttons += `<button onclick="shipOrder(${order.id})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded text-xs">Ship</button>`;
            break;
        case 'shipped':
            buttons += `<button onclick="deliverOrder(${order.id})" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">Deliver</button>`;
            buttons += `<a href="/supplier/delivery-form?order_id=${order.id}&distribution_center_id=${order.distribution_center_id || ''}&vendor_id=${order.vendor_id || ''}&vendor_name=${encodeURIComponent(order.vendor_name || '')}&vendor_address=${encodeURIComponent(order.vendor_address || '')}&vendor_phone=${encodeURIComponent(order.vendor_phone || '')}" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs ml-1">Create Delivery</a>`;
            break;
        default:
            buttons += `<span class="text-gray-400 text-xs">No actions</span>`;
    }
    
    return buttons;
}

// Apply filters
function applyFilters() {
    const statusFilter = document.getElementById('status-filter').value;
    const materialFilter = document.getElementById('material-filter').value;
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    filteredOrders = allOrders.filter(order => {
        const matchesStatus = !statusFilter || order.status === statusFilter;
        const matchesMaterial = !materialFilter || order.material_type === materialFilter;
        const matchesSearch = !searchTerm || 
            order.vendor_name.toLowerCase().includes(searchTerm) || 
            order.id.toString().includes(searchTerm);
        
        return matchesStatus && matchesMaterial && matchesSearch;
    });
    
    renderOrders();
}

// Order action functions
async function confirmOrder(orderId) {
    if (!confirm('Are you sure you want to confirm this order?')) return;
    
    try {
        const res = await fetch(`/api/supplier/orders/${orderId}/confirm`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            }
        });
        
        const data = await res.json();
        
        if (data.success) {
            showMessage('Order confirmed successfully!', 'success');
            loadIncomingOrders();
            loadOrderStats();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred while confirming the order.', 'error');
    }
}

async function processOrder(orderId) {
    if (!confirm('Are you sure you want to start processing this order?')) return;
    
    try {
        const res = await fetch(`/api/supplier/orders/${orderId}/process`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            }
        });
        
        const data = await res.json();
        
        if (data.success) {
            showMessage('Order processing started!', 'success');
            loadIncomingOrders();
            loadOrderStats();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred while processing the order.', 'error');
    }
}

async function shipOrder(orderId) {
    if (!confirm('Are you sure you want to mark this order as shipped?')) return;
    
    try {
        const res = await fetch(`/api/supplier/orders/${orderId}/ship`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            }
        });
        
        const data = await res.json();
        
        if (data.success) {
            showMessage('Order marked as shipped!', 'success');
            loadIncomingOrders();
            loadOrderStats();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred while shipping the order.', 'error');
    }
}

async function deliverOrder(orderId) {
    if (!confirm('Are you sure you want to mark this order as delivered?')) return;
    
    try {
        const res = await fetch(`/api/supplier/orders/${orderId}/deliver`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            }
        });
        
        const data = await res.json();
        
        if (data.success) {
            showMessage('Order marked as delivered!', 'success');
            loadIncomingOrders();
            loadOrderStats();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred while delivering the order.', 'error');
    }
}

function rejectOrder(orderId) {
    document.getElementById('reject-order-id').value = orderId;
    document.getElementById('reject-reason').value = '';
    document.getElementById('reject-modal').classList.remove('hidden');
}

// Handle reject form submission
document.getElementById('reject-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('reject-order-id').value;
    const reason = document.getElementById('reject-reason').value;
    
    try {
        const res = await fetch(`/api/supplier/orders/${orderId}/reject`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            },
            body: JSON.stringify({ reason })
        });
        
        const data = await res.json();
        
        if (data.success) {
            showMessage('Order rejected successfully!', 'success');
            document.getElementById('reject-modal').classList.add('hidden');
            loadIncomingOrders();
            loadOrderStats();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred while rejecting the order.', 'error');
    }
});

// Close reject modal
document.getElementById('close-reject-modal').addEventListener('click', function() {
    document.getElementById('reject-modal').classList.add('hidden');
});

document.getElementById('cancel-reject').addEventListener('click', function() {
    document.getElementById('reject-modal').classList.add('hidden');
});

// Show message function
function showMessage(message, type) {
    // Create a temporary message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

// Event listeners for filters
document.getElementById('status-filter').addEventListener('change', applyFilters);
document.getElementById('material-filter').addEventListener('change', applyFilters);
document.getElementById('search-input').addEventListener('input', applyFilters);

// Initial load
document.addEventListener('DOMContentLoaded', function() {
    loadOrderStats();
    loadIncomingOrders();
});

// Delivery Modal Logic
function showCreateDeliveryModal(orderId) {
    document.getElementById('delivery-order-id').value = orderId;
    // Autofill vendor_id and details
    const order = allOrders.find(o => o.id == orderId);
    if (order && order.vendor_id) {
        document.getElementById('vendor_id').value = order.vendor_id;
        document.getElementById('vendor_id').setAttribute('readonly', 'readonly');
        document.getElementById('vendor_name').value = order.vendor_name || '';
        document.getElementById('vendor_phone').value = order.vendor_phone || '';
        document.getElementById('vendor_address').value = order.vendor_address || '';
    } else {
        document.getElementById('vendor_id').value = '';
        document.getElementById('vendor_id').removeAttribute('readonly');
        document.getElementById('vendor_name').value = '';
        document.getElementById('vendor_phone').value = '';
        document.getElementById('vendor_address').value = '';
    }
    document.getElementById('create-delivery-modal').classList.remove('hidden');
}
document.getElementById('close-create-delivery-modal').addEventListener('click', function() {
    document.getElementById('create-delivery-modal').classList.add('hidden');
});
document.getElementById('cancel-create-delivery').addEventListener('click', function() {
    document.getElementById('create-delivery-modal').classList.add('hidden');
});
document.getElementById('create-delivery-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const payload = {
        order_id: form.order_id.value,
        distribution_center_id: form.distribution_center_id.value,
        vendor_id: form.vendor_id.value,
        vehicle_number: form.vehicle_number.value,
        driver_name: form.driver_name.value,
        driver_phone: form.driver_phone.value,
        driver_license: form.driver_license.value,
        scheduled_delivery_date: form.scheduled_delivery_date.value,
        scheduled_delivery_time: form.scheduled_delivery_time.value,
        delivery_address: form.delivery_address.value,
        recipient_name: form.recipient_name.value,
        recipient_phone: form.recipient_phone.value,
    };
    document.getElementById('create-delivery-error').classList.add('hidden');
    document.getElementById('create-delivery-success').classList.add('hidden');
    try {
        const res = await fetch('/api/deliveries', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('create-delivery-success').textContent = 'Delivery created successfully!';
            document.getElementById('create-delivery-success').classList.remove('hidden');
            form.reset();
            setTimeout(() => document.getElementById('create-delivery-modal').classList.add('hidden'), 1500);
        } else {
            throw new Error(data.message || 'Failed to create delivery.');
        }
    } catch (err) {
        document.getElementById('create-delivery-error').textContent = err.message;
        document.getElementById('create-delivery-error').classList.remove('hidden');
    }
});
</script>
@endsection 