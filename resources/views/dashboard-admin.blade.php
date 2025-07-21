@extends('layouts.app')

@php
    // Set default values for variables that might not be passed
    $recentOrders = $recentOrders ?? collect();
    $newVendorApplicants = $newVendorApplicants ?? collect();
    $userStatistics = $userStatistics ?? ['total_users' => 0, 'role_breakdown' => collect()];
@endphp

@section('content')
    <main class="main-content">
        <!-- Navigation Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Executive Dashboard</h1>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.reports.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Advanced Reports
                    </a>
                </div>
            </div>
            <p class="text-gray-600">Welcome to the Caramel Yogurt Management System. Monitor real-time production metrics, inventory analytics, and supply chain performance.</p>
        </div>

        <!-- Recent Orders Widget -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Order Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Retailer</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Order Date</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Delivery Address</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ $order->order_number ?? $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($order->order_type ?? '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $order->customer_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $order->retailer_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @php
                                    $date = '-';
                                    if (isset($order->order_date) && $order->order_date) {
                                        $date = \Carbon\Carbon::parse($order->order_date)->format('Y-m-d');
                                    } elseif (isset($order->created_at) && $order->created_at) {
                                        $date = is_string($order->created_at) ? \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') : $order->created_at->format('Y-m-d');
                                    }
                                @endphp
                                {{ $date }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $order->delivery_address ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">UGX {{ number_format($order->total_amount ?? $order->total, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                    @if(($order->order_status ?? $order->status) === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif(($order->order_status ?? $order->status) === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif(($order->order_status ?? $order->status) === 'processing') bg-purple-100 text-purple-800
                                    @elseif(($order->order_status ?? $order->status) === 'shipped') bg-indigo-100 text-indigo-800
                                    @elseif(($order->order_status ?? $order->status) === 'delivered') bg-green-100 text-green-800
                                    @elseif(($order->order_status ?? $order->status) === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->order_status ?? $order->status ?? '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                    @if(($order->payment_status ?? null) === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif(($order->payment_status ?? null) === 'paid') bg-green-100 text-green-800
                                    @elseif(($order->payment_status ?? null) === 'failed') bg-red-100 text-red-800
                                    @elseif(($order->payment_status ?? null) === 'refunded') bg-gray-100 text-gray-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->payment_status ?? '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-400">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($recentOrders, 'links'))
                <div class="mt-4 flex justify-center">
                    {{ $recentOrders->links() }}
                </div>
            @endif
        </div>
        
        <!-- Recent Deliveries Table -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Recent Deliveries</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:underline">View All Orders</a>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Delivery ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Driver</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Scheduled Date</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Delivery Address</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Recipient</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @php $recentDeliveries = $recentDeliveries ?? \App\Models\Delivery::with(['order'])->latest()->take(10)->get(); @endphp
                        @forelse($recentDeliveries as $delivery)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $delivery->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->order_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->driver_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold 
                                    @if($delivery->delivery_status === 'scheduled') bg-blue-100 text-blue-800
                                    @elseif($delivery->delivery_status === 'in_transit') bg-yellow-100 text-yellow-800
                                    @elseif($delivery->delivery_status === 'out_for_delivery') bg-purple-100 text-purple-800
                                    @elseif($delivery->delivery_status === 'delivered') bg-green-100 text-green-800
                                    @elseif($delivery->delivery_status === 'failed') bg-red-100 text-red-800
                                    @elseif($delivery->delivery_status === 'cancelled') bg-gray-100 text-gray-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($delivery->delivery_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->scheduled_delivery_date ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->delivery_address ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->recipient_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($delivery->order)
                                    <a href="{{ route('admin.orders.show', $delivery->order_id) }}" class="text-blue-500 hover:underline">View Order</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">No recent deliveries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- New Vendor Applicants Table -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">New Vendor Applicants Awaiting Approval</h2>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Visit Date</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Validation Message</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($newVendorApplicants as $applicant)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $applicant->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->company_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->visit_date ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $applicant->validation_message ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form method="POST" action="{{ route('admin.vendor-applicants.approve', $applicant->id) }}">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Approve</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No new vendor applicants awaiting approval.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
        const canvas = document.getElementById('inventoryChart');
        if (!canvas) return; // Prevent error if element is missing
        const ctx = canvas.getContext('2d');
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
        const canvas = document.getElementById('userChart');
        if (!canvas) return; // Prevent error if element is missing
        const ctx = canvas.getContext('2d');
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
    
    // Refresh inventory chart and summary
    function refreshChart() {
        loadChartData();
        loadUserStatistics();
    }
    
    // Refresh user chart
    function refreshUserChart() {
        loadUserStatistics();
    }
    
    // Auto-refresh every 30 seconds
    function startAutoRefresh() {
        setInterval(() => {
            loadChartData();
            loadUserStatistics();
        }, 30000);
    }
    
    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initChart();
        initUserChart();
        loadChartData();
        loadUserStatistics();
        startAutoRefresh();
    });
</script>

<!-- Floating Chat Icon -->
<!-- Remove the floating chat icon at the bottom right -->

<!-- Chat Icon Button (link to /chat) for admin dashboard -->
<a href="/chat" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-500 hover:bg-green-600 focus:outline-none transition ease-in-out duration-150 mr-1" title="Chat">
    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 0a8 8 0 0 0-6.32 12.906L0 16l3.234-1.678A8 8 0 1 0 8 0zm3.993 11.403c-.2.56-1.17 1.1-1.6 1.17-.41.07-.92.1-1.48-.09-.34-.11-.78-.25-1.34-.49-2.36-.97-3.9-3.34-4.02-3.5-.12-.17-.96-1.28-.96-2.44 0-1.16.61-1.73.83-1.96.22-.23.48-.29.64-.29.16 0 .32.01.46.01.15 0 .34-.05.53.41.2.47.68 1.62.74 1.74.06.12.1.26.02.42-.08.16-.12.26-.23.4-.11.14-.23.31-.33.42-.11.11-.22.23-.1.45.12.22.54.89 1.16 1.44.8.71 1.47.93 1.7 1.03.23.1.36.09.49-.05.13-.14.56-.65.71-.87.15-.22.3-.18.5-.11.2.07 1.28.6 1.5.71.22.11.36.17.41.27.05.1.05.57-.15 1.13z"/>
    </svg>
    <span id="chat-badge" style="display:none;position:absolute;top:2px;right:2px;background:#e3342f;color:#fff;font-size:0.85em;font-weight:bold;border-radius:50%;padding:2px 7px;min-width:22px;text-align:center;z-index:10;box-shadow:0 1px 4px rgba(0,0,0,0.12);">0</span>
</a>

<script>
(function(){
    function updateChatBadge() {
        fetch('/chat/unread-total')
            .then(res => res.json())
            .then(data => {
                var badge = document.getElementById('chat-badge');
                if (badge) {
                    var count = data.unread_count || 0;
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
    }
    setInterval(updateChatBadge, 5000);
    document.addEventListener('DOMContentLoaded', updateChatBadge);
})();
</script>

@endsection 