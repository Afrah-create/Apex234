@extends('layouts.app')

@section('content')
<main class="main-content">
        <!-- Header -->
  

    <div class="order-management-container">
            <!-- Statistics Cards -->
        <div class="summary-cards">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-semibold text-gray-900" id="total-orders">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending Orders</p>
                            <p class="text-2xl font-semibold text-gray-900" id="pending-orders">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Delivered Orders</p>
                            <p class="text-2xl font-semibold text-gray-900" id="delivered-orders">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-semibold text-gray-900" id="total-revenue">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Order Management</h2>
                </div>
                
                <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 order-filters">
                        <div class="flex-1 min-w-0">
                            <input type="text" id="search-input" placeholder="Search orders..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select id="payment-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Payment Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                </div>

                <!-- Bulk Actions Bar -->
                <div id="bulk-actions-bar" class="flex space-x-2 mb-2" style="display:none;">
                    <button onclick="bulkDeleteOrders()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">Delete Selected</button>
                    <button onclick="bulkEditStatus()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Edit Status</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all-orders" onclick="toggleSelectAllOrders(this)">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retailer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Orders will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Loading and empty states -->
                <div id="loading-state" class="px-6 py-12 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="mt-2 text-sm text-gray-500">Loading orders...</p>
                </div>

                <div id="empty-state" class="px-6 py-12 text-center hidden">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No orders found</p>
                </div>
            </div>

    <!-- Raw Material Orders Table -->
    <div class="mt-12">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-semibold text-blue-800 mb-4">Raw Material Orders</h2>
            <div class="flex space-x-2">
                <a href="/admin/raw-material-orders/export-csv" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-200 text-sm" download>Export CSV</a>
                <a href="/admin/raw-material-orders/export-pdf" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition duration-200 text-sm" download>Export PDF</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Material</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="raw-material-orders-table-body" class="bg-white divide-y divide-gray-200">
                        <!-- Raw material orders will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div id="raw-material-orders-loading" class="px-6 py-12 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-500">Loading raw material orders...</p>
            </div>
            <div id="raw-material-orders-empty" class="px-6 py-12 text-center hidden">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No raw material orders found</p>
            </div>
            <div id="raw-material-orders-pagination" class="flex justify-center items-center py-4 space-x-4"></div>
        </div>
    </div>
        </div>
</main>

    <!-- Status Update Modal -->
    <div id="status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Update Order Status</h3>
                <form id="status-form">
                    <input type="hidden" id="order-id">
                    <div class="mb-4">
                        <label for="order-status" class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                        <select id="order-status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeStatusModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Inline Edit Modal -->
    <div id="editOrderModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000; align-items:center; justify-content:center;">
      <div class="modal-dialog" style="background:#fff; padding:2rem; border-radius:8px; min-width:320px; max-width:90vw;">
        <h4>Edit Order <span id="edit-order-id"></span></h4>
        <form id="editOrderForm">
          <input type="hidden" id="edit-order-id-input" name="order_id">
          <div class="form-group mb-2">
            <label for="edit-order-type">Order Type</label>
            <select id="edit-order-type" name="order_type" class="form-control">
              <option value="customer">Customer</option>
              <option value="rush">Rush</option>
              <option value="bulk">Bulk</option>
              <option value="regular">Regular</option>
            </select>
          </div>
          <div class="form-group mb-2">
            <label for="edit-order-status">Order Status</label>
            <select id="edit-order-status" name="order_status" class="form-control">
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="form-group mb-2">
            <label for="edit-delivery-address">Delivery Address</label>
            <input type="text" id="edit-delivery-address" name="delivery_address" class="form-control">
          </div>
          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditOrderModal()">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <script>
        let ordersData = [];
        let filteredData = [];

        // Load orders data
        async function loadOrdersData() {
            try {
                const response = await fetch('{{ route("admin.orders.api.orders-data") }}');
                ordersData = await response.json();
                filteredData = [...ordersData];
                renderOrdersTable();
            } catch (error) {
                console.error('Error loading orders data:', error);
            }
        }

        // Load order statistics
        async function loadOrderStatistics() {
            try {
                const response = await fetch('{{ route("admin.orders.api.order-statistics") }}');
                const data = await response.json();
                
                document.getElementById('total-orders').textContent = data.stats.total_orders || 0;
                document.getElementById('pending-orders').textContent = data.stats.pending_orders || 0;
                document.getElementById('delivered-orders').textContent = data.stats.delivered_orders || 0;
                document.getElementById('total-revenue').textContent = (data.stats.total_revenue || 0).toLocaleString() + ' UGX';
            } catch (error) {
                console.error('Error loading order statistics:', error);
            }
        }

        // Render orders table
        function renderOrdersTable() {
            const tbody = document.getElementById('orders-table-body');
            const loadingState = document.getElementById('loading-state');
            const emptyState = document.getElementById('empty-state');

            if (filteredData.length === 0) {
                loadingState.classList.add('hidden');
                emptyState.classList.remove('hidden');
                tbody.innerHTML = '';
                return;
            }

            loadingState.classList.add('hidden');
            emptyState.classList.add('hidden');

            // Sort orders by date/time descending
            const sorted = [...filteredData].sort((a, b) => new Date(b.order_date) - new Date(a.order_date));

            // Group orders by date
            const groups = {};
            const today = new Date();
            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);
            function formatDate(dateStr) {
                const d = new Date(dateStr);
                if (d.toDateString() === today.toDateString()) return 'Today';
                if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
                return d.toLocaleDateString();
            }
            sorted.forEach(order => {
                const group = formatDate(order.order_date);
                if (!groups[group]) groups[group] = [];
                groups[group].push(order);
            });

            // Render grouped orders
            let html = '';
            Object.keys(groups).forEach(group => {
                html += `<tr><td colspan="11" style="background:#f3f4f6; font-weight:600; color:#2563eb; padding:12px 0 6px 16px; font-size:1.08rem;">${group}</td></tr>`;
                groups[group].forEach(order => {
                    html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <input type="checkbox" class="order-checkbox" value="${order.id}" onchange="updateBulkActionsBar()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #${order.order_number}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${order.order_type || ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${order.customer_name || ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${order.retailer_name || ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${order.order_date ? new Date(order.order_date).toLocaleDateString() : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${order.delivery_address || ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${order.total_amount ? parseFloat(order.total_amount).toLocaleString() + ' UGX' : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusColor(order.order_status)}">
                                ${order.order_status ? order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1) : ''}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getPaymentStatusColor(order.payment_status)}">
                                ${order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : ''}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="viewOrder(${order.id})" class="text-blue-600 hover:text-blue-900">View</button>
                                <button onclick="openStatusModal(${order.id}, '${order.order_status}')" class="text-purple-600 hover:text-purple-900">Status</button>
                                <button onclick="deleteOrder(${order.id})" class="text-red-600 hover:text-red-900">Delete</button>
                            </div>
                        </td>
                    </tr>
                    `;
                });
            });
            tbody.innerHTML = html;
        }

        // Get status color
        function getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'confirmed': 'bg-blue-100 text-blue-800',
                'processing': 'bg-purple-100 text-purple-800',
                'shipped': 'bg-indigo-100 text-indigo-800',
                'delivered': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        // Get payment status color
        function getPaymentStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'paid': 'bg-green-100 text-green-800',
                'failed': 'bg-red-100 text-red-800',
                'refunded': 'bg-gray-100 text-gray-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        // Filter orders
        function filterOrders() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const paymentFilter = document.getElementById('payment-filter').value;

            filteredData = ordersData.filter(order => {
                // Combine all relevant fields into a single string for searching
                const combined = [
                    order.order_number,
                    order.customer_name,
                    order.retailer_name,
                    order.order_date ? new Date(order.order_date).toLocaleDateString() : '',
                    order.order_status,
                    order.payment_status,
                    order.delivery_address
                ].join(' ').toLowerCase();
                const matchesSearch = combined.includes(searchTerm);
                const matchesStatus = !statusFilter || order.order_status === statusFilter;
                const matchesPayment = !paymentFilter || order.payment_status === paymentFilter;
                return matchesSearch && matchesStatus && matchesPayment;
            });
            renderOrdersTable();
        }

        // Status modal functions
        function openStatusModal(orderId, currentStatus) {
            document.getElementById('order-id').value = orderId;
            document.getElementById('order-status').value = currentStatus;
            document.getElementById('status-modal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('status-modal').classList.add('hidden');
        }

        // Update order status
        async function updateOrderStatus(event) {
            event.preventDefault();
            
            const orderId = document.getElementById('order-id').value;
            const status = document.getElementById('order-status').value;

            try {
                const response = await fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ order_status: status })
                });

                if (response.ok) {
                    closeStatusModal();
                    loadOrdersData();
                    loadOrderStatistics();
                    showBulkActionMessage('Order status updated successfully', 'success');
                } else {
                    showBulkActionMessage('Error updating order status', 'error');
                }
            } catch (error) {
                console.error('Error updating order status:', error);
                showBulkActionMessage('Error updating order status', 'error');
            }
        }

        // Delete order
        async function deleteOrder(orderId) {
            (async function() {
                const confirmed = await showConfirmModal('Are you sure you want to delete this order?', 'Delete Order');
                if (!confirmed) return;
                try {
                    const response = await fetch(`/admin/orders/${orderId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        loadOrdersData();
                        loadOrderStatistics();
                        showBulkActionMessage('Order deleted successfully', 'success');
                    } else {
                        showBulkActionMessage('Error deleting order', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting order:', error);
                    showBulkActionMessage('Error deleting order', 'error');
                }
            })();
        }

        // Navigation functions
        function viewOrder(orderId) {
            window.location.href = `/admin/orders/${orderId}`;
        }

        function editOrder(orderId) {
            window.location.href = `/admin/orders/${orderId}/edit`;
        }

        function openEditOrderModal(order) {
            document.getElementById('edit-order-id').textContent = order.id;
            document.getElementById('edit-order-id-input').value = order.id;
            document.getElementById('edit-order-type').value = order.order_type;
            document.getElementById('edit-order-status').value = order.order_status;
            document.getElementById('edit-delivery-address').value = order.delivery_address;
            document.getElementById('editOrderModal').style.display = 'flex';
        }
        function closeEditOrderModal() {
            document.getElementById('editOrderModal').style.display = 'none';
        }

        // Handle form submit
        const editOrderForm = document.getElementById('editOrderForm');
        if (editOrderForm) {
            editOrderForm.onsubmit = async function(e) {
                e.preventDefault();
                const id = document.getElementById('edit-order-id-input').value;
                const order_type = document.getElementById('edit-order-type').value;
                const order_status = document.getElementById('edit-order-status').value;
                const delivery_address = document.getElementById('edit-delivery-address').value;
                try {
                    const response = await fetch(`/admin/orders/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ order_type, order_status, delivery_address })
                    });
                    if (response.ok) {
                        showBulkActionMessage('Order updated successfully', 'success');
                        closeEditOrderModal();
                        loadOrdersData();
                    } else {
                        showBulkActionMessage('Failed to update order', 'error');
                    }
                } catch (err) {
                    showBulkActionMessage('Error updating order', 'error');
                }
            }
        }

        // Refresh data
        function refreshData() {
            loadOrdersData();
            loadOrderStatistics();
        }

        // --- Raw Material Orders Section ---
        let rawMaterialOrdersData = [];
        let rawMaterialOrdersCurrentPage = 1;
        let rawMaterialOrdersLastPage = 1;

        async function loadRawMaterialOrdersData(page = 1) {
            try {
                const response = await fetch(`/api/admin/raw-material-orders?page=${page}`);
                const result = await response.json();
                rawMaterialOrdersData = result.data;
                rawMaterialOrdersCurrentPage = result.current_page;
                rawMaterialOrdersLastPage = result.last_page;
                renderRawMaterialOrdersTable();
                renderRawMaterialOrdersPagination();
            } catch (error) {
                console.error('Error loading raw material orders:', error);
            }
        }

        function renderRawMaterialOrdersTable() {
            const tbody = document.getElementById('raw-material-orders-table-body');
            const loadingState = document.getElementById('raw-material-orders-loading');
            const emptyState = document.getElementById('raw-material-orders-empty');
            tbody.innerHTML = '';
            if (!rawMaterialOrdersData.length) {
                loadingState.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }
            loadingState.classList.add('hidden');
            emptyState.classList.add('hidden');
            rawMaterialOrdersData.forEach(order => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-2 py-2 whitespace-nowrap">${order.id}</td>
                    <td class="px-2 py-2 whitespace-nowrap">${order.vendor_name || '-'}</td>
                    <td class="px-2 py-2 whitespace-nowrap">${order.supplier_name || '-'}</td>
                    <td class="px-2 py-2 whitespace-nowrap">${order.material_name} (${order.material_type})</td>
                    <td class="px-2 py-2 whitespace-nowrap">${order.quantity} ${order.unit_of_measure}</td>
                    <td class="px-2 py-2 whitespace-nowrap">${order.status}</td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        ${
                            order.archived === true
                                ? `<button class='bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded mr-2' onclick='unarchiveRawMaterialOrder(${order.id})'>Unarchive</button>`
                                : (
                            order.archived === false &&
                            (typeof order.status === 'string' && ['delivered', 'cancelled'].includes(order.status.trim().toLowerCase()))
                        )
                            ? `<button class='bg-blue-600 hover:bg-blue-800 text-white px-3 py-1 rounded mr-2 border border-blue-800' onclick='archiveRawMaterialOrder(${order.id})'>Archive</button>`
                            : '<span class="text-gray-400">No action</span>'
                        }
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function renderRawMaterialOrdersPagination() {
            const pagination = document.getElementById('raw-material-orders-pagination');
            pagination.innerHTML = '';
            if (rawMaterialOrdersLastPage <= 1) return;
            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
            prevBtn.disabled = rawMaterialOrdersCurrentPage === 1;
            prevBtn.onclick = () => loadRawMaterialOrdersData(rawMaterialOrdersCurrentPage - 1);
            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
            nextBtn.disabled = rawMaterialOrdersCurrentPage === rawMaterialOrdersLastPage;
            nextBtn.onclick = () => loadRawMaterialOrdersData(rawMaterialOrdersCurrentPage + 1);
            const pageInfo = document.createElement('span');
            pageInfo.textContent = `Page ${rawMaterialOrdersCurrentPage} of ${rawMaterialOrdersLastPage}`;
            pagination.appendChild(prevBtn);
            pagination.appendChild(pageInfo);
            pagination.appendChild(nextBtn);
        }

        async function archiveRawMaterialOrder(orderId) {
            (async function() {
                const confirmed = await showConfirmModal('Are you sure you want to archive this order?', 'Archive Order');
                if (!confirmed) return;
                try {
                    const response = await fetch(`/admin/raw-material-orders/${orderId}/archive`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                    const data = await response.json();
                    showBulkActionMessage(data.message, data.success ? 'success' : 'error');
                    loadRawMaterialOrdersData();
                } catch (error) {
                    showBulkActionMessage('Failed to archive order.', 'error');
                }
            })();
        }

        async function unarchiveRawMaterialOrder(orderId) {
            (async function() {
                const confirmed = await showConfirmModal('Are you sure you want to unarchive this order?', 'Unarchive Order');
                if (!confirmed) return;
                try {
                    const response = await fetch(`/admin/raw-material-orders/${orderId}/unarchive`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                    const data = await response.json();
                    showBulkActionMessage(data.message, data.success ? 'success' : 'error');
                    loadRawMaterialOrdersData();
                } catch (error) {
                    showBulkActionMessage('Failed to unarchive order.', 'error');
                }
            })();
        }

        // Bulk selection logic
        function toggleSelectAllOrders(master) {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(cb => { cb.checked = master.checked; });
            updateBulkActionsBar();
        }
        function updateBulkActionsBar() {
            const checked = document.querySelectorAll('.order-checkbox:checked');
            document.getElementById('bulk-actions-bar').style.display = checked.length > 0 ? 'flex' : 'none';
        }
        function getSelectedOrderIds() {
            return Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
        }
        async function bulkDeleteOrders() {
            const ids = getSelectedOrderIds();
            if (!ids.length) return;
            if (!confirm('Are you sure you want to delete the selected orders?')) return;
            for (const id of ids) {
                await deleteOrder(id);
            }
            updateBulkActionsBar();
            loadOrdersData();
            loadOrderStatistics();
            showBulkActionMessage('Selected orders deleted successfully', 'success');
        }
        function bulkEditStatus() {
            const ids = getSelectedOrderIds();
            if (!ids.length) return;
            showBulkActionMessage('Bulk status edit for order IDs: ' + ids.join(', '), 'info');
            // You can implement a modal to select new status and send a bulk update request
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            loadOrdersData();
            loadOrderStatistics();

            // Set up filters
            document.getElementById('search-input').addEventListener('input', filterOrders);
            document.getElementById('status-filter').addEventListener('change', filterOrders);
            document.getElementById('payment-filter').addEventListener('change', filterOrders);

            // Set up status form
            document.getElementById('status-form').addEventListener('submit', updateOrderStatus);

            // Call this on page load
            loadRawMaterialOrdersData();
        });
    </script>
@endsection 