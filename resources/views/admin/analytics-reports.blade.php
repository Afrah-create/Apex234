@extends('layouts.app')

@section('content')
<main class="main-content">
    <!-- Navigation Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Analytics & Reports Dashboard</h1>
            <div class="flex space-x-4">
              
                <button onclick="refreshAllData()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </div>
        <p class="text-gray-600">Comprehensive analytics, machine learning insights, and decision-making tools for strategic management.</p>
    </div>

    <!-- Key Performance Indicators -->
    <div class="summary-cards mb-8">
        <div class="summary-card" style="--summary-card-border: #22c55e;">
            <div class="icon" style="background: #bbf7d0; color: #22c55e;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <div class="details">
                <p>Revenue Growth</p>
                <p id="revenueGrowth">+12.5%</p>
            </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #3b82f6;">
            <div class="icon" style="background: #dbeafe; color: #3b82f6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="details">
                <p>Order Volume</p>
                <p id="orderVolume">1,247</p>
            </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #f59e0b;">
            <div class="icon" style="background: #fef3c7; color: #f59e0b;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div class="details">
                <p>Profit Margin</p>
                <p id="profitMargin">23.8%</p>
            </div>
        </div>
        <div class="summary-card" style="--summary-card-border: #ef4444;">
            <div class="icon" style="background: #fee2e2; color: #ef4444;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="details">
                <p>Customer Satisfaction</p>
                <p id="customerSatisfaction">94.2%</p>
            </div>
        </div>
    </div>

    <!-- Predictive Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Sales Prediction -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Prediction (Next 30 Days)</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-green-800">Greek Vanilla Yoghurt</span>
                    <span class="text-lg font-bold text-green-900">2,450 units</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-blue-800">Low Fat Blueberry Yoghurt</span>
                    <span class="text-lg font-bold text-blue-900">1,890 units</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm font-medium text-yellow-800">Organic Strawberry Yoghurt</span>
                    <span class="text-lg font-bold text-yellow-900">1,230 units</span>
                </div>
            </div>
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Confidence Level: <span class="font-semibold text-gray-900">87%</span></p>
            </div>
        </div>

        <!-- Inventory Optimization -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory Optimization</h3>
            <div class="space-y-4">
                <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-red-800">Low Stock Alert</span>
                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">3 items</span>
                    </div>
                    <p class="text-xs text-red-700">Greek Vanilla, Low Fat Blueberry, Organic Strawberry</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-yellow-800">Reorder Suggested</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">5 items</span>
                    </div>
                    <p class="text-xs text-yellow-700">Based on ML predictions</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-green-800">Optimal Stock</span>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">12 items</span>
                    </div>
                    <p class="text-xs text-green-700">Well-maintained levels</p>
                </div>
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Risk Assessment</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-red-800">Supply Chain Risk</p>
                        <p class="text-xs text-red-600">Raw material shortage</p>
                    </div>
                    <span class="text-lg font-bold text-red-900">High</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Market Competition</p>
                        <p class="text-xs text-yellow-600">New entrants</p>
                    </div>
                    <span class="text-lg font-bold text-yellow-900">Medium</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-green-800">Financial Stability</p>
                        <p class="text-xs text-green-600">Strong cash flow</p>
                    </div>
                    <span class="text-lg font-bold text-green-900">Low</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Trend Analysis -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Trend Analysis</h2>
            <div class="relative" style="height: 300px;">
                <canvas id="trendAnalysisChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-800">Growth Rate</p>
                    <p class="text-lg font-bold text-blue-900">+8.5%</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <p class="text-sm font-medium text-green-800">Market Share</p>
                    <p class="text-lg font-bold text-green-900">12.3%</p>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Performance Metrics</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Customer Acquisition Cost</p>
                        <p class="text-xs text-gray-500">Last 30 days</p>
                    </div>
                    <span class="text-lg font-bold text-gray-900">85,750 UGX</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Customer Lifetime Value</p>
                        <p class="text-xs text-gray-500">Average per customer</p>
                    </div>
                    <span class="text-lg font-bold text-gray-900">548,800 UGX</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Conversion Rate</p>
                        <p class="text-xs text-gray-500">Website to purchase</p>
                    </div>
                    <span class="text-lg font-bold text-gray-900">3.2%</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Churn Rate</p>
                        <p class="text-xs text-gray-500">Monthly</p>
                    </div>
                    <span class="text-lg font-bold text-gray-900">2.1%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Decision Support Tools -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Decision Support Tools</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">Scenario Analysis</h3>
                <div class="space-y-2">
                    <button onclick="runScenario('optimistic')" class="w-full text-left p-2 bg-green-50 hover:bg-green-100 rounded text-sm">
                        Optimistic Scenario (+20% growth)
                    </button>
                    <button onclick="runScenario('realistic')" class="w-full text-left p-2 bg-blue-50 hover:bg-blue-100 rounded text-sm">
                        Realistic Scenario (+10% growth)
                    </button>
                    <button onclick="runScenario('pessimistic')" class="w-full text-left p-2 bg-red-50 hover:bg-red-100 rounded text-sm">
                        Pessimistic Scenario (-5% growth)
                    </button>
                </div>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">What-If Analysis</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price Change (%)</label>
                        <input type="number" id="priceChange" class="w-full p-2 border border-gray-300 rounded text-sm" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marketing Budget (UGX)</label>
                        <input type="number" id="marketingBudget" class="w-full p-2 border border-gray-300 rounded text-sm" placeholder="35000000">
                    </div>
                    <button onclick="runWhatIfAnalysis()" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm">
                        Analyze Impact
                    </button>
                </div>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">Recommendations</h3>
                <div id="recommendations" class="space-y-2">
                    <div class="p-2 bg-blue-50 rounded text-sm">
                        <strong>Inventory:</strong> Increase Greek Vanilla Yoghurt stock by 25%
                    </div>
                    <div class="p-2 bg-green-50 rounded text-sm">
                        <strong>Pricing:</strong> Consider 5% price increase for premium products
                    </div>
                    <div class="p-2 bg-yellow-50 rounded text-sm">
                        <strong>Marketing:</strong> Focus on Low Fat Blueberry Yoghurt promotions
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Reports Link -->
    
</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let demandForecastChart, customerSegmentationChart, trendAnalysisChart;

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        loadAnalyticsData();
        
        // Auto-refresh every 5 minutes
        setInterval(function() {
            loadAnalyticsData();
        }, 300000);
    });

    function initializeCharts() {
        // Demand Forecasting Chart
        const demandCtx = document.getElementById('demandForecastChart').getContext('2d');
        demandForecastChart = new Chart(demandCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Actual Demand',
                    data: [1200, 1350, 1400, 1550, 1600, 1750, 1800, 1700, 1650, 1500, 1400, 1300],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Predicted Demand',
                    data: [null, null, null, null, null, null, null, null, null, null, 1450, 1600],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderDash: [5, 5],
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Demand (Units)'
                        }
                    }
                }
            }
        });

        // Customer Segmentation Chart
        const segmentationCtx = document.getElementById('customerSegmentationChart').getContext('2d');
        customerSegmentationChart = new Chart(segmentationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Premium Buyers', 'Regular Consumers', 'Occasional Buyers'],
                datasets: [{
                    data: [23, 45, 32],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(245, 158, 11, 0.8)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(245, 158, 11, 1)'
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
                    }
                }
            }
        });

        // Trend Analysis Chart
        const trendCtx = document.getElementById('trendAnalysisChart').getContext('2d');
        trendAnalysisChart = new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                datasets: [{
                    label: 'Revenue',
                    data: [45000, 52000, 48000, 61000],
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }, {
                    label: 'Profit',
                    data: [12000, 14000, 13000, 18000],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (UGX)'
                        }
                    }
                }
            }
        });
    }

    function loadAnalyticsData() {
        // Load KPI data
        fetch('{{ route("api.analytics.kpi") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('revenueGrowth').textContent = data.revenue_growth;
                document.getElementById('orderVolume').textContent = data.order_volume;
                document.getElementById('profitMargin').textContent = data.profit_margin;
                document.getElementById('customerSatisfaction').textContent = data.customer_satisfaction;
            })
            .catch(error => console.error('Error loading KPI data:', error));

        // Load ML predictions
        fetch('{{ route("api.analytics.predictions") }}')
            .then(response => response.json())
            .then(data => {
                // Update demand forecast chart
                demandForecastChart.data.datasets[1].data = data.demand_forecast;
                demandForecastChart.update();
            })
            .catch(error => console.error('Error loading predictions:', error));

        // Load trend analysis data
        fetch('{{ route("api.analytics.trend-analysis") }}')
            .then(response => response.json())
            .then(data => {
                // Update trend analysis chart with real data
                trendAnalysisChart.data.labels = data.quarters;
                trendAnalysisChart.data.datasets[0].data = data.revenue;
                trendAnalysisChart.data.datasets[1].data = data.profit;
                trendAnalysisChart.update();
                
                // Update growth rate and market share
                document.querySelector('.bg-blue-50 .text-lg').textContent = data.growth_rate + '%';
                document.querySelector('.bg-green-50 .text-lg').textContent = data.market_share + '%';
            })
            .catch(error => console.error('Error loading trend analysis data:', error));
    }

    function refreshAllData() {
        loadAnalyticsData();
        
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

    function runScenario(type) {
        // Simulate scenario analysis
        const scenarios = {
            optimistic: { growth: '+20%', revenue: '262,500,000 UGX', risk: 'Low' },
            realistic: { growth: '+10%', revenue: '227,500,000 UGX', risk: 'Medium' },
            pessimistic: { growth: '-5%', revenue: '192,500,000 UGX', risk: 'High' }
        };
        
        const scenario = scenarios[type];
        alert(`${type.charAt(0).toUpperCase() + type.slice(1)} Scenario:\nGrowth: ${scenario.growth}\nRevenue: ${scenario.revenue}\nRisk: ${scenario.risk}`);
    }

    function runWhatIfAnalysis() {
        const priceChange = document.getElementById('priceChange').value;
        const marketingBudget = document.getElementById('marketingBudget').value;
        
        if (priceChange || marketingBudget) {
            // Simulate what-if analysis
            const impact = Math.random() * 20 - 10; // Random impact between -10% and +10%
            alert(`What-If Analysis Results:\nPrice Change: ${priceChange}%\nMarketing Budget: ${marketingBudget} UGX\nPredicted Impact: ${impact.toFixed(1)}%`);
        } else {
            alert('Please enter values for analysis');
        }
    }
</script>
@endsection 