@extends('layouts.app')

@section('content')
    <main class="main-content">
       

        <!-- Welcome Vendor Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-blue-900 text-center">
                WELCOME {{ strtoupper(Auth::user()->name) }}
            </h2>
        </div>

        <!-- Inventory Summary Cards -->
        <div class="summary-cards mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="summary-card" style="--summary-card-border: #22c55e;">
                <div class="icon" style="background: #bbf7d0; color: #22c55e;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div class="details">
                    <p>Stock Available</p>
                    <p id="vendor-total-available">-</p>
                </div>
            </div>
            <div class="summary-card" style="--summary-card-border: #3b82f6;">
                <div class="icon" style="background: #dbeafe; color: #3b82f6;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="details">
                    <p>Allocated Inventory</p>
                    <p id="vendor-total-reserved">-</p>
                </div>
            </div>
            <div class="summary-card" style="--summary-card-border: #ef4444;">
                <div class="icon" style="background: #fee2e2; color: #ef4444;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div class="details">
                    <p>Critical Stock Alerts</p>
                    <p id="vendor-low-stock-items">-</p>
                </div>
            </div>
            <div class="summary-card" style="--summary-card-border: #6b7280;">
                <div class="icon" style="background: #f3f4f6; color: #6b7280;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="details">
                    <p>SKU Count</p>
                    <p id="vendor-total-products">-</p>
                </div>
            </div>
        </div>

        <!-- Inventory Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Inventory Analytics</h2>
                <button onclick="refreshVendorInventoryChart()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh Data
                </button>
            </div>
            <div class="relative" style="height: 350px;">
                <canvas id="vendorInventoryChart"></canvas>
            </div>
        </div>

        <!-- Order Status Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Status Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div class="order-status-card bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-blue-600 font-bold">Pending</p>
                    <p id="vendor-orders-pending">-</p>
                </div>
                <div class="order-status-card bg-green-50 p-4 rounded-lg text-center">
                    <p class="text-green-600 font-bold">Confirmed</p>
                    <p id="vendor-orders-confirmed">-</p>
                </div>
                <div class="order-status-card bg-yellow-50 p-4 rounded-lg text-center">
                    <p class="text-yellow-600 font-bold">Shipped</p>
                    <p id="vendor-orders-shipped">-</p>
                </div>
                <div class="order-status-card bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-gray-600 font-bold">Delivered</p>
                    <p id="vendor-orders-delivered">-</p>
                </div>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="vendorOrderStatusChart"></canvas>
            </div>
        </div>

<<<<<<< HEAD
=======
        <!-- Raw Material Statistics -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Raw Material Statistics</h2>
            <div id="vendorRawMaterialDoughnuts" class="flex flex-wrap gap-6 justify-center"></div>
        </div>

>>>>>>> ea8a867b4687e9b8fd18e35a14a2bced025da181
        <!-- Production Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Production Summary</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div class="production-card bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-blue-600 font-bold">Batches Produced</p>
                    <p id="vendor-batches-produced">-</p>
                </div>
                <div class="production-card bg-green-50 p-4 rounded-lg text-center">
                    <p class="text-green-600 font-bold">Units Produced</p>
                    <p id="vendor-units-produced">-</p>
                </div>
                <div class="production-card bg-yellow-50 p-4 rounded-lg text-center">
                    <p class="text-yellow-600 font-bold">Units Sold</p>
                    <p id="vendor-units-sold">-</p>
                </div>
                <div class="production-card bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-gray-600 font-bold">Units in Inventory</p>
                    <p id="vendor-units-inventory">-</p>
                </div>
            </div>
            <div class="relative" style="height: 250px;">
                <canvas id="vendorProductionChart"></canvas>
            </div>
        </div>

        <!-- Assigned Employees Table -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Assigned Employees</h2>
            @if(isset($employees) && $employees->count())
                <table class="min-w-full bg-white border border-gray-200 rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Role</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td class="px-4 py-2">{{ $employee->name }}</td>
                                <td class="px-4 py-2">{{ $employee->role }}</td>
                                <td class="px-4 py-2">{{ ucfirst($employee->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">No employees assigned to this vendor yet.</p>
            @endif
        </div>
    </main>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Helper: fetch JSON
        async function fetchJSON(url) {
            const res = await fetch(url);
            return await res.json();
        }

        // Inventory Summary Cards
        async function loadVendorInventorySummary() {
            const data = await fetchJSON('/api/vendor/inventory-summary');
            document.getElementById('vendor-total-available').textContent = data.total_available;
            document.getElementById('vendor-total-reserved').textContent = data.total_reserved;
            document.getElementById('vendor-low-stock-items').textContent = data.low_stock_items;
            document.getElementById('vendor-total-products').textContent = data.total_products;
        }

        // Inventory Chart
        let vendorInventoryChart;
        async function loadVendorInventoryChart() {
            const data = await fetchJSON('/api/vendor/inventory-chart');
            const ctx = document.getElementById('vendorInventoryChart').getContext('2d');
            if (vendorInventoryChart) vendorInventoryChart.destroy();
            vendorInventoryChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Inventory Stock Levels by Product' }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Stock Quantity (Units)' } },
                        x: { title: { display: true, text: 'Product' } }
                    }
                }
            });
        }
        function refreshVendorInventoryChart() { loadVendorInventoryChart(); }

        // Order Status Cards & Chart
        let vendorOrderStatusChart;
        async function loadVendorOrderStatus() {
            const data = await fetchJSON('/api/vendor/order-status');
            document.getElementById('vendor-orders-pending').textContent = data.pending;
            document.getElementById('vendor-orders-confirmed').textContent = data.confirmed;
            document.getElementById('vendor-orders-shipped').textContent = data.shipped;
            document.getElementById('vendor-orders-delivered').textContent = data.delivered;
            // Chart
            const ctx = document.getElementById('vendorOrderStatusChart').getContext('2d');
            if (vendorOrderStatusChart) vendorOrderStatusChart.destroy();
            vendorOrderStatusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Shipped', 'Delivered'],
                    datasets: [{
                        data: [data.pending, data.confirmed, data.shipped, data.delivered],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(156, 163, 175, 0.8)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(34, 197, 94, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(156, 163, 175, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        title: { display: true, text: 'Order Status Distribution' }
                    }
                }
            });
        }

<<<<<<< HEAD
=======
        // Raw Material Stats Cards & Chart
        let vendorRawMaterialChart;
        async function loadVendorRawMaterialStats() {
            const data = await fetchJSON('/api/vendor/raw-material-stats');
            // Prepare data for grouped bar chart
            const materials = Object.keys(data);
            const statuses = ['available', 'in_use', 'expired', 'disposed'];
            const statusLabels = {
                available: 'Available',
                in_use: 'In Use',
                expired: 'Expired',
                disposed: 'Disposed'
            };
            const colorMap = {
                available: 'rgba(34, 197, 94, 0.8)',
                in_use: 'rgba(59, 130, 246, 0.8)',
                expired: 'rgba(239, 68, 68, 0.8)',
                disposed: 'rgba(156, 163, 175, 0.8)'
            };

            const datasets = statuses.map(status => ({
                label: statusLabels[status],
                data: materials.map(material => data[material][status] ?? 0),
                backgroundColor: colorMap[status]
            }));

            // Restore single canvas for bar chart
            const container = document.getElementById('vendorRawMaterialDoughnuts');
            container.innerHTML = '<canvas id="vendorRawMaterialChart"></canvas>';
            const ctx = document.getElementById('vendorRawMaterialChart').getContext('2d');
            if (vendorRawMaterialChart) vendorRawMaterialChart.destroy();
            vendorRawMaterialChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: materials,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Raw Material Status by Type' }
                    },
                    scales: {
                        x: { stacked: false, title: { display: true, text: 'Raw Material' } },
                        y: { stacked: false, beginAtZero: true, title: { display: true, text: 'Quantity' } }
                    }
                }
            });
        }

>>>>>>> ea8a867b4687e9b8fd18e35a14a2bced025da181
        // Production Summary Cards & Chart
        let vendorProductionChart;
        async function loadVendorProductionSummary() {
            const data = await fetchJSON('/api/vendor/production-summary');
            document.getElementById('vendor-batches-produced').textContent = data.batches_produced;
            document.getElementById('vendor-units-produced').textContent = data.units_produced;
            document.getElementById('vendor-units-sold').textContent = data.units_sold;
            document.getElementById('vendor-units-inventory').textContent = data.units_inventory;
            // Chart
            const ctx = document.getElementById('vendorProductionChart').getContext('2d');
            if (vendorProductionChart) vendorProductionChart.destroy();
            vendorProductionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Batches Produced', 'Units Produced', 'Units Sold', 'Units in Inventory'],
                    datasets: [{
                        label: 'Production Summary',
                        data: [data.batches_produced, data.units_produced, data.units_sold, data.units_inventory],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(156, 163, 175, 0.8)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(34, 197, 94, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(156, 163, 175, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: 'Production Summary' }
                    }
                }
            });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            loadVendorInventorySummary();
            loadVendorInventoryChart();
            loadVendorOrderStatus();
            loadVendorProductionSummary();
        });
    </script>
@endsection 