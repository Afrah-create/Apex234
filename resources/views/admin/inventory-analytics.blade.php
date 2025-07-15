@extends('layouts.app')

@section('content')
<main class="main-content">
    <!-- Navigation Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Inventory Analytics Dashboard</h1>
            <div class="flex space-x-4">
                
                <button onclick="refreshData()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </div>
        <p class="text-gray-600">Real-time inventory analytics and insights for the Caramel Yogurt supply chain management system.</p>
    </div>

    <!-- Inventory Summary Stats -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8 flex flex-wrap gap-6 items-center justify-between">
        <div>
            <span class="block text-xs text-gray-500">Total Products</span>
            <span id="totalProducts" class="text-lg font-bold text-gray-900">-</span>
        </div>
        <div>
            <span class="block text-xs text-gray-500">Available</span>
            <span id="totalAvailable" class="text-lg font-bold text-green-700">-</span>
        </div>
        <div>
            <span class="block text-xs text-gray-500">Reserved</span>
            <span id="totalReserved" class="text-lg font-bold text-blue-700">-</span>
        </div>
        <div>
            <span class="block text-xs text-gray-500">Damaged</span>
            <span id="totalDamaged" class="text-lg font-bold text-red-700">-</span>
        </div>
        <div>
            <span class="block text-xs text-gray-500">Expired</span>
            <span id="totalExpired" class="text-lg font-bold text-gray-700">-</span>
        </div>
        <div>
            <span class="block text-xs text-gray-500">Low Stock Items</span>
            <span id="lowStockCount" class="text-lg font-bold text-yellow-700">-</span>
        </div>
        <div>
            <span class="block text-xs text-gray-500">Out of Stock Items</span>
            <span id="outOfStockCount" class="text-lg font-bold text-red-800">-</span>
        </div>
        <div>
            <span class="block text-xs text-gray-500">Critical Alerts</span>
            <span id="lowStockItems" class="text-lg font-bold text-orange-700">-</span>
        </div>
    </div>

    <!-- Inventory Chart -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Inventory Overview</h2>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                    Available
                </span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                    Reserved
                </span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span>
                    Damaged
                </span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                    Expired
                </span>
            </div>
        </div>
        <div class="relative" style="height: 400px;">
            <canvas id="inventoryChart"></canvas>
        </div>
        

        

    </div>

    <!-- User Statistics and Stock Alerts -->
    

    <!-- Real-time Inventory Table -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Real-time Inventory Data</h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Last updated:</span>
                <span class="text-sm font-medium text-gray-900" id="lastUpdated">-</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Damaged</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>

<!-- Distribution Center Vendor Inventory Table -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Distribution Center Inventory</h2>
    <div class="overflow-x-auto" id="dc-vendor-inventory-table">
        <!-- Data will be loaded dynamically -->
    </div>
</div>
</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let inventoryChart;

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        loadInventoryData();
        loadInventoryTable();
        
        // Auto-refresh every 30 seconds
        setInterval(function() {
            loadInventoryData();
            loadInventoryTable();
        }, 30000);
        loadAllDistributionCentersWithVendors();
    });

    function initializeCharts() {
        // Initialize Inventory Chart
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        inventoryChart = new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Stock Quantity (Units)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Product Categories'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Inventory Stock Levels by Product Category'
                    }
                }
            }
        });
    }

    function loadInventoryData() {
        fetch('{{ route("api.inventory.chart-data") }}')
            .then(response => response.json())
            .then(data => {
                inventoryChart.data.labels = data.labels;
                inventoryChart.data.datasets = data.datasets;
                inventoryChart.update();
            })
            .catch(error => console.error('Error loading inventory chart data:', error));

        fetch('{{ route("api.inventory.summary") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalProducts').textContent = data.total_products;
                document.getElementById('totalAvailable').textContent = data.total_available;
                document.getElementById('totalReserved').textContent = data.total_reserved;
                document.getElementById('totalDamaged').textContent = data.total_damaged;
                document.getElementById('totalExpired').textContent = data.total_expired;
                document.getElementById('lowStockCount').textContent = data.low_stock_items;
                document.getElementById('outOfStockCount').textContent = data.out_of_stock_items;
                document.getElementById('lowStockItems').textContent = data.low_stock_items;
            })
            .catch(error => console.error('Error loading inventory summary:', error));
    }

    function loadInventoryTable() {
        fetch('{{ route("api.inventory.chart-data") }}')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('inventoryTableBody');
                tbody.innerHTML = '';
                
                // Create a map of product names to their data
                const productMap = {};
                data.labels.forEach((productName, index) => {
                    productMap[productName] = {
                        product_name: productName,
                        total_available: data.datasets[0].data[index] || 0,
                        total_reserved: data.datasets[1].data[index] || 0,
                        total_damaged: data.datasets[2].data[index] || 0,
                        total_expired: data.datasets[3].data[index] || 0
                    };
                });
                
                // Convert map to array and display
                Object.values(productMap).forEach(item => {
                    const total = item.total_available + item.total_reserved + item.total_damaged + item.total_expired;
                    let status = 'Normal';
                    let statusClass = 'bg-green-100 text-green-800';
                    
                    if (item.total_available === 0) {
                        status = 'Out of Stock';
                        statusClass = 'bg-red-100 text-red-800';
                    } else if (item.total_available < 10) {
                        status = 'Low Stock';
                        statusClass = 'bg-yellow-100 text-yellow-800';
                    }
                    
                    const row = `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.product_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">${item.total_available}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${item.total_reserved}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">${item.total_damaged}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${item.total_expired}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${total}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">${status}</span>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
                
                // Update last updated timestamp
                document.getElementById('lastUpdated').textContent = new Date().toLocaleString();
            })
            .catch(error => console.error('Error loading inventory table:', error));
    }

    function refreshData() {
        loadInventoryData();
        loadInventoryTable();
        
        // Show refresh feedback
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Refreshed!';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
    }

    function loadAllDistributionCentersWithVendors() {
        fetch('/api/distribution-centers')
            .then(response => response.json())
            .then(centers => {
                const container = document.getElementById('dc-vendor-inventory-table');
                container.innerHTML = '';
                if (!centers.length) {
                    container.innerHTML = '<div class="text-gray-500">No distribution centers found.</div>';
                    return;
                }
                centers.forEach(center => {
                    // Fetch inventory stats for each center (not grouped by vendor)
                    fetch(`/admin/distribution-centers/${center.id}/inventory-stats`)
                        .then(res => res.json())
                        .then(data => {
                            // Center header
                            const dcHeader = document.createElement('h2');
                            dcHeader.className = 'text-lg font-bold mb-4 text-green-800';
                            dcHeader.textContent = `Distribution Center: ${data.distribution_center.center_name}`;
                            container.appendChild(dcHeader);
                            // Products table
                            const table = document.createElement('table');
                            table.className = 'min-w-full mb-4 divide-y divide-gray-200 border';
                            table.innerHTML = `
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Available Units</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Reserved Units</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-red-700 uppercase tracking-wider">Damaged Units</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Expired Units</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            `;
                            const tbody = table.querySelector('tbody');
                            if (!data.products || data.products.length === 0) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `<td class="px-6 py-2 text-gray-400" colspan="5">No products</td>`;
                                tbody.appendChild(tr);
                            } else {
                                data.products.forEach(product => {
                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td class="px-6 py-2">${product.product_name}</td>
                                        <td class="px-6 py-2">${product.available ?? 0}</td>
                                        <td class="px-6 py-2">${product.reserved ?? 0}</td>
                                        <td class="px-6 py-2">${product.damaged ?? 0}</td>
                                        <td class="px-6 py-2">${product.expired ?? 0}</td>
                                    `;
                                    tbody.appendChild(tr);
                                });
                            }
                            container.appendChild(table);
                        });
                });
            });
    }
</script>
@endsection 