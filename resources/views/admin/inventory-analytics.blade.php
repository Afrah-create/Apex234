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

    <!-- Inventory Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card" style="--summary-card-border: #22c55e;">
            <div class="icon" style="background: #bbf7d0; color: #22c55e;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div class="details">
                <p>Total Products</p>
                <p id="totalProducts">-</p>
            </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #3b82f6;">
            <div class="icon" style="background: #dbeafe; color: #3b82f6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="details">
                <p>Available Stock</p>
                <p id="totalAvailable">-</p>
            </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #f59e0b;">
            <div class="icon" style="background: #fef3c7; color: #f59e0b;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="details">
                <p>Reserved Inventory</p>
                <p id="totalReserved">-</p>
            </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #ef4444;">
            <div class="icon" style="background: #fee2e2; color: #ef4444;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="details">
                <p>Critical Alerts</p>
                <p id="lowStockItems">-</p>
            </div>
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
</script>
@endsection 