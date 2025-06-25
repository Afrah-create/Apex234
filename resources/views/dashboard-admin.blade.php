<x-app-layout>
    <div x-data="window.sidebarOpenStore || (window.sidebarOpenStore = { sidebarOpen: Alpine.store('sidebarOpen', { open: false }) })" class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="{'block': $store.sidebarOpen.open, 'hidden': !$store.sidebarOpen.open, 'absolute inset-y-0 left-0 z-40': $store.sidebarOpen.open, 'md:static md:block': true}" class="w-64 bg-gray-900 text-white flex-shrink-0 p-6 space-y-6 hidden md:block transition-all duration-200 overflow-y-auto h-full">
            <div class="mb-8">
                <div class="text-2xl font-bold mb-2">Management Console</div>
                <div class="text-sm text-gray-300">System Administrator</div>
            </div>
            <nav class="flex flex-col space-y-2">
                <a href="{{ route('dashboard') }}" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h2a2 2 0 002-2v-7a2 2 0 00-.586-1.414l-8-8a2 2 0 00-2.828 0l-8 8A2 2 0 003 10v7a2 2 0 002 2h2"/></svg>
                    Executive Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    User Management
                </a>
               
                
                
                <a href="{{ route('admin.orders.index') }}" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h2l1 2h13l1-2h2M5 6h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>
                    Order Management
                </a>
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Inventory Control
                </a>
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    Logistics & Distribution
                </a>
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Quality Assurance
                </a>
               
                <a href="#" class="hover:bg-gray-800 px-4 py-2 rounded transition flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Analytics & Reports
                </a>
               
            </nav>
            
        </aside>
        <!-- Overlay for mobile -->
        <div x-show="$store.sidebarOpen.open" @click="$store.sidebarOpen.open = false" class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden" x-cloak></div>
        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50 w-full">
            <h1 class="text-2xl font-bold mb-6">Executive Dashboard</h1>
            <p class="mb-6 text-gray-600">Welcome to the Caramel Yogurt Management System. Monitor real-time production metrics, inventory analytics, and supply chain performance.</p>
            
            <!-- Inventory Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Stock Available</p>
                            <p class="text-2xl font-semibold text-gray-900" id="total-available">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Allocated Inventory</p>
                            <p class="text-2xl font-semibold text-gray-900" id="total-reserved">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Critical Stock Alerts</p>
                            <p class="text-2xl font-semibold text-gray-900" id="low-stock-items">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-gray-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">SKU Count</p>
                            <p class="text-2xl font-semibold text-gray-900" id="total-products">-</p>
                        </div>
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
    </div>

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
</x-app-layout> 