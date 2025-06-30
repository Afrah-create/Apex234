@extends('layouts.app')

@section('content')
    <main class="main-content">
        <!-- Navigation Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Executive Dashboard</h1>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.inventory.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Detailed Analytics
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        User Management
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Order Management
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Advanced Reports
                    </a>
                </div>
            </div>
            <p class="text-gray-600">Welcome to the Caramel Yogurt Management System. Monitor real-time production metrics, inventory analytics, and supply chain performance.</p>
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
                    <p>Stock Available</p>
                    <p id="total-available">-</p>
                </div>
            </div>
            <div class="summary-card" style="--summary-card-border: #3b82f6;">
                <div class="icon" style="background: #dbeafe; color: #3b82f6;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="details">
                    <p>Allocated Inventory</p>
                    <p id="total-reserved">-</p>
                </div>
            </div>
            <div class="summary-card" style="--summary-card-border: #ef4444;">
                <div class="icon" style="background: #fee2e2; color: #ef4444;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="details">
                    <p>Critical Stock Alerts</p>
                    <p id="low-stock-items">-</p>
                </div>
            </div>
            <div class="summary-card" style="--summary-card-border: #6b7280;">
                <div class="icon" style="background: #f3f4f6; color: #6b7280;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="details">
                    <p>SKU Count</p>
                    <p id="total-products">-</p>
                </div>
            </div>
        </div>
        
        <!-- Inventory Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Inventory Analytics Dashboard</h2>
                <button onclick="refreshChart()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
            <div class="relative" style="height: 400px;">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
        
        <!-- User Statistics Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">User Distribution by Role</h2>
                    <button onclick="refreshUserChart()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">User Statistics Summary</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">Total System Users</span>
                        <span class="text-lg font-semibold text-gray-900" id="total-users">-</span>
                    </div>
                    <div id="role-breakdown" class="space-y-3">
                        <!-- Role breakdown will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional dashboard content can go here -->
    </main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let inventoryChart;
    let userChart;
    
    // Initialize inventory chart
    function initChart() {
        const ctx = document.getElementById('inventoryChart').getContext('2d');
        inventoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Inventory Stock Levels by Product Category'
                    }
                },
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
                }
            }
        });
    }
    
    // Initialize user chart
    function initUserChart() {
        const ctx = document.getElementById('userChart').getContext('2d');
        userChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',   // Blue for Admin
                        'rgba(34, 197, 94, 0.8)',    // Green for Vendor
                        'rgba(245, 158, 11, 0.8)',   // Orange for Retailer
                        'rgba(168, 85, 247, 0.8)',   // Purple for Supplier
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(168, 85, 247, 1)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'User Distribution by Role'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} users (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Load inventory chart data
    async function loadChartData() {
        try {
            const response = await fetch('{{ route("api.inventory.chart-data") }}');
            const data = await response.json();
            
            if (inventoryChart) {
                inventoryChart.data.labels = data.labels;
                inventoryChart.data.datasets = data.datasets;
                inventoryChart.update();
            }
        } catch (error) {
            console.error('Error loading chart data:', error);
        }
    }
    
    // Load user statistics data
    async function loadUserStatistics() {
        try {
            const response = await fetch('{{ route("api.inventory.user-statistics") }}');
            const data = await response.json();
            
            if (userChart) {
                userChart.data.labels = data.chart_data.labels;
                userChart.data.datasets[0].data = data.chart_data.datasets[0].data;
                userChart.update();
            }
            
            // Update summary statistics
            document.getElementById('total-users').textContent = data.summary.total_users || 0;
            
            // Update role breakdown
            const roleBreakdown = document.getElementById('role-breakdown');
            roleBreakdown.innerHTML = '';
            
            data.summary.role_breakdown.forEach(role => {
                const roleElement = document.createElement('div');
                roleElement.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                roleElement.innerHTML = `
                    <span class="text-sm font-medium text-gray-600">${role.role}</span>
                    <div class="text-right">
                        <span class="text-lg font-semibold text-gray-900">${role.count}</span>
                        <span class="text-sm text-gray-500 ml-2">(${role.percentage}%)</span>
                    </div>
                `;
                roleBreakdown.appendChild(roleElement);
            });
        } catch (error) {
            console.error('Error loading user statistics:', error);
        }
    }
    
    // Load summary data
    async function loadSummaryData() {
        try {
            const response = await fetch('{{ route("api.inventory.summary") }}');
            const data = await response.json();
            
            document.getElementById('total-available').textContent = data.total_available || 0;
            document.getElementById('total-reserved').textContent = data.total_reserved || 0;
            document.getElementById('low-stock-items').textContent = data.low_stock_items || 0;
            document.getElementById('total-products').textContent = data.total_products || 0;
        } catch (error) {
            console.error('Error loading summary data:', error);
        }
    }
    
    // Refresh inventory chart and summary
    function refreshChart() {
        loadChartData();
        loadSummaryData();
    }
    
    // Refresh user chart
    function refreshUserChart() {
        loadUserStatistics();
    }
    
    // Auto-refresh every 30 seconds
    function startAutoRefresh() {
        setInterval(() => {
            loadChartData();
            loadSummaryData();
            loadUserStatistics();
        }, 30000);
    }
    
    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initChart();
        initUserChart();
        loadChartData();
        loadSummaryData();
        loadUserStatistics();
        startAutoRefresh();
    });
</script>
@endsection 