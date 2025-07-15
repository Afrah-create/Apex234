@extends('layouts.app')

@section('content')
<main class="main-content">
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Machine Learning Insights</h1>
    </div>
    <!-- Demand Forecasting Chart -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Demand Forecasting</h2>
        <div class="relative" style="height: 300px;">
            <canvas id="demandForecastChart"></canvas>
        </div>

    </div>
    <!-- Sales Prediction (Next 30 Days) -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Prediction</h3>
        <div id="salesPrediction" class="space-y-4"></div>
        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">Confidence Level: <span id="salesConfidence" class="font-semibold text-gray-900">-</span></p>
        </div>
    </div>
    <!-- Inventory Optimization -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory Optimization</h3>
        <div id="inventoryOptimization" class="space-y-4"></div>
    </div>
    <!-- Risk Assessment -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Risk Assessment</h3>
        <div id="riskAssessment" class="space-y-4"></div>
    </div>
    <!-- Customer Segmentation -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Segmentation</h3>
        <div class="relative" style="height: 300px;">
            <canvas id="customerSegmentationChart"></canvas>
        </div>
        
    </div>
</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let demandForecastChart, customerSegmentationChart;

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're on the analytics page
        const demandChart = document.getElementById('demandForecastChart');
        const segmentationChart = document.getElementById('customerSegmentationChart');
        
        if (demandChart && segmentationChart) {
        initializeCharts();
        loadAnalyticsData();
        
        // Auto-refresh every 5 minutes
        setInterval(function() {
            loadAnalyticsData();
        }, 300000);
        }
    });

    function initializeCharts() {
        // Demand Forecasting Chart
        const demandCtx = document.getElementById('demandForecastChart');
        if (!demandCtx) return;
        
        demandForecastChart = new Chart(demandCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Actual Demand',
                    data: [], // Will be filled by API
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Predicted Demand',
                    data: [], // Will be filled by API
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
        const segmentationCtx = document.getElementById('customerSegmentationChart');
        if (!segmentationCtx) return;
        
        customerSegmentationChart = new Chart(segmentationCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [], // Will be filled dynamically
                datasets: [{
                    data: [], // Will be filled by API
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return `${label}: ${value} customers`;
                            }
                        }
                    }
                }
            }
        });
    }

    function loadAnalyticsData() {
        // Load ML predictions
        fetch('/api/analytics/sales-predictions')
            .then(response => response.json())
            .then(data => {
                // Update demand forecast chart
                if (demandForecastChart && data.years && data.months) {
                    const datasets = [];
                    const currentYear = new Date().getFullYear();
                    // Softer, pastel color palette
                    const colorPalette = [
                        '#7fa7e6', // soft blue
                        '#ffd699', // soft orange
                        '#a7e6b8', // soft green
                        '#f7a6a6', // soft red
                        '#c7b6e6', // soft purple
                        '#c2b280', // soft brown
                        '#f7b6d2', // soft pink
                        '#cccccc', // soft gray
                        '#e6e6a7', // soft olive
                        '#a7e6e6'  // soft cyan
                    ];
                    data.years.forEach((yearObj, idx) => {
                        const colorIdx = idx % colorPalette.length;
                        // Only show actual for past/current years
                        if (yearObj.year <= currentYear) {
                            datasets.push({
                                label: yearObj.year + ' Actual',
                                data: yearObj.actual,
                                borderColor: colorPalette[colorIdx],
                                backgroundColor: colorPalette[colorIdx] + '11', // very light fill
                                tension: 0.4,
                                borderWidth: 2,
                                spanGaps: true,
                                fill: false
                            });
                        }
                        // Only show predicted for current/future years
                        if (yearObj.year >= currentYear) {
                            datasets.push({
                                label: yearObj.year + ' Predicted',
                                data: yearObj.predicted,
                                borderColor: colorPalette[colorIdx],
                                backgroundColor: colorPalette[colorIdx] + '11',
                                borderDash: [6, 5],
                                tension: 0.4,
                                borderWidth: 2,
                                spanGaps: true,
                                fill: false,
                                pointStyle: 'rectRot',
                                pointRadius: 4
                            });
                        }
                    });
                    demandForecastChart.data.labels = data.months;
                    demandForecastChart.data.datasets = datasets;
                    demandForecastChart.update();
                }

                // Update sales prediction section (show only for the latest year with predictions)
                const salesPrediction = document.getElementById('salesPrediction');
                if (salesPrediction) {
                    salesPrediction.innerHTML = '';
                    if (data.years && data.years.length > 0) {
                        // Find the latest year with predictions
                        const latestPred = data.years.slice().reverse().find(y => y.predicted.some(v => v !== null));
                        if (latestPred) {
                            // Create a grid container for two-column layout
                            const gridContainer = document.createElement('div');
                            gridContainer.className = 'grid grid-cols-2 gap-4';
                            
                            data.months.forEach((month, idx) => {
                                if (latestPred.predicted[idx] !== null) {
                                    const div = document.createElement('div');
                                    div.className = 'flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200';
                                    div.innerHTML = `
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-green-800">${month} ${latestPred.year}</span>
                                            <span class="text-xs text-green-600">Predicted Demand</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-green-900">${latestPred.predicted[idx]}</span>
                                            <span class="text-xs text-green-600 block">units</span>
                                        </div>
                                    `;
                                    gridContainer.appendChild(div);
                                }
                            });
                            
                            salesPrediction.appendChild(gridContainer);
                        }
                        const confidenceElement = document.getElementById('salesConfidence');
                        if (confidenceElement) {
                            const confidence = data.confidence_level ? Math.round(data.confidence_level * 100) : 0;
                            confidenceElement.textContent = confidence + '%';
                        }
                    } else {
                        salesPrediction.innerHTML = '<span class="text-gray-500">No sales prediction data available.</span>';
                        const confidenceElement = document.getElementById('salesConfidence');
                        if (confidenceElement) {
                            confidenceElement.textContent = '-';
                        }
                    }
                }


            })
            .catch(error => {
                if (demandForecastChart) {
                demandForecastChart.data.datasets[1].data = [];
                demandForecastChart.update();
                }
                
                const salesPrediction = document.getElementById('salesPrediction');
                if (salesPrediction) {
                    salesPrediction.innerHTML = '<span class="text-red-500">Error loading sales predictions.</span>';
                }
                
                const confidenceElement = document.getElementById('salesConfidence');
                if (confidenceElement) {
                    confidenceElement.textContent = 'Error';
                }
            });

        // Load inventory optimization
        fetch('/api/analytics/inventory-optimization')
            .then(response => response.json())
            .then(data => {
                const inventoryDiv = document.getElementById('inventoryOptimization');
                if (!inventoryDiv) return;
                
                inventoryDiv.innerHTML = '';
                if (data && data.detailed_recommendations && data.detailed_recommendations.length > 0) {
                    // Group recommendations by urgency
                    const highUrgency = data.detailed_recommendations.filter(item => item.urgency === 'high');
                    const mediumUrgency = data.detailed_recommendations.filter(item => item.urgency === 'medium');
                    const lowUrgency = data.detailed_recommendations.filter(item => item.urgency === 'low');
                    
                    // Create urgency sections
                    if (highUrgency.length > 0) {
                        const highSection = document.createElement('div');
                        highSection.className = 'mb-6';
                        highSection.innerHTML = `
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <h4 class="text-sm font-semibold text-red-800">High Priority - Immediate Action Required</h4>
                                <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">${highUrgency.length} items</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        `;
                        
                        highUrgency.forEach(item => {
                            highSection.innerHTML += `
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-red-900 text-sm">${item.product_name || 'Unknown Product'}</h5>
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-medium">${(item.recommendation || '').replace('_', ' ')}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-xs">
                                        <div class="text-center">
                                            <div class="font-semibold text-red-700">Current</div>
                                            <div class="text-red-900">${item.current_stock || 0}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-semibold text-red-700">Optimal</div>
                                            <div class="text-red-900">${item.optimal_stock || 0}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-semibold text-red-700">Reorder</div>
                                            <div class="text-red-900">${item.reorder_point || 0}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        highSection.innerHTML += '</div>';
                        inventoryDiv.appendChild(highSection);
                    }
                    
                    if (mediumUrgency.length > 0) {
                        const mediumSection = document.createElement('div');
                        mediumSection.className = 'mb-6';
                        mediumSection.innerHTML = `
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <h4 class="text-sm font-semibold text-yellow-800">Medium Priority - Plan Reorder</h4>
                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">${mediumUrgency.length} items</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        `;
                        
                        mediumUrgency.forEach(item => {
                            mediumSection.innerHTML += `
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-yellow-900 text-sm">${item.product_name || 'Unknown Product'}</h5>
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">${(item.recommendation || '').replace('_', ' ')}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-xs">
                                        <div class="text-center">
                                            <div class="font-semibold text-yellow-700">Current</div>
                                            <div class="text-yellow-900">${item.current_stock || 0}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-semibold text-yellow-700">Optimal</div>
                                            <div class="text-yellow-900">${item.optimal_stock || 0}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-semibold text-yellow-700">Reorder</div>
                                            <div class="text-yellow-900">${item.reorder_point || 0}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        mediumSection.innerHTML += '</div>';
                        inventoryDiv.appendChild(mediumSection);
                    }
                    
                    if (lowUrgency.length > 0) {
                        const lowSection = document.createElement('div');
                        lowSection.className = 'mb-6';
                        lowSection.innerHTML = `
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <h4 class="text-sm font-semibold text-green-800">Low Priority - Stock Levels Good</h4>
                                <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">${lowUrgency.length} items</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        `;
                        
                        lowUrgency.forEach(item => {
                            lowSection.innerHTML += `
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-green-900 text-sm">${item.product_name || 'Unknown Product'}</h5>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">${(item.recommendation || '').replace('_', ' ')}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-xs">
                                        <div class="text-center">
                                            <div class="font-semibold text-green-700">Current</div>
                                            <div class="text-green-900">${item.current_stock || 0}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-semibold text-green-700">Optimal</div>
                                            <div class="text-green-900">${item.optimal_stock || 0}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-semibold text-green-700">Reorder</div>
                                            <div class="text-green-900">${item.reorder_point || 0}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        lowSection.innerHTML += '</div>';
                        inventoryDiv.appendChild(lowSection);
                    }
                } else {
                    inventoryDiv.innerHTML = '<span class="text-gray-500">No inventory optimization data available.</span>';
                }
            })
            .catch(error => {
                const inventoryDiv = document.getElementById('inventoryOptimization');
                if (inventoryDiv) {
                    inventoryDiv.innerHTML = '<span class="text-red-500">Error loading inventory optimization.</span>';
                }
                console.error('Error loading inventory optimization:', error);
            });

        // Load risk assessment
        fetch('/api/analytics/risk-assessment')
            .then(response => response.json())
            .then(data => {
                const riskDiv = document.getElementById('riskAssessment');
                if (!riskDiv) return;
                
                riskDiv.innerHTML = '';
                if (data) {
                    // Display overall risk score first
                    if (data.overall_risk_score !== undefined) {
                        const overallRiskCard = document.createElement('div');
                        const overallScore = Math.round(data.overall_risk_score * 100);
                        const riskLevel = overallScore >= 60 ? 'high' : (overallScore >= 30 ? 'medium' : 'low');
                        const riskColor = riskLevel === 'high' ? 'red' : (riskLevel === 'medium' ? 'yellow' : 'green');
                        
                        overallRiskCard.className = `bg-${riskColor}-50 border border-${riskColor}-200 rounded-lg p-4 mb-4`;
                        overallRiskCard.innerHTML = `
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-lg font-semibold text-${riskColor}-800">Overall Business Risk</h4>
                                <span class="text-2xl font-bold text-${riskColor}-900">${overallScore}%</span>
                            </div>
                            <div class="text-sm text-${riskColor}-700">
                                Risk Level: <span class="font-semibold capitalize">${riskLevel}</span>
                            </div>
                        `;
                        riskDiv.appendChild(overallRiskCard);
                    }
                    
                    // Display individual risk categories
                    Object.keys(data).forEach(riskType => {
                        if (riskType === 'overall_risk_score') return; // Skip overall score as it's displayed above
                        
                        const risk = data[riskType];
                        if (!risk || typeof risk !== 'object') return;
                        
                        const riskLevel = risk.risk_level || 'low';
                        const riskScore = risk.risk_score || 0;
                        const riskLevelClass = riskLevel === 'high' ? 'bg-red-50 border-red-200' : 
                                             riskLevel === 'medium' ? 'bg-yellow-50 border-yellow-200' : 
                                             'bg-green-50 border-green-200';
                        const textClass = riskLevel === 'high' ? 'text-red-800' : 
                                        riskLevel === 'medium' ? 'text-yellow-800' : 
                                        'text-green-800';
                        const textClassSmall = riskLevel === 'high' ? 'text-red-600' : 
                                             riskLevel === 'medium' ? 'text-yellow-600' : 
                                             'text-green-600';
                        const textClassBold = riskLevel === 'high' ? 'text-red-900' : 
                                            riskLevel === 'medium' ? 'text-yellow-900' : 
                                            'text-green-900';
                        
                        const div = document.createElement('div');
                        div.className = `border rounded-lg p-4 mb-3 ${riskLevelClass}`;
                        
                        // Create risk header
                        const riskHeader = `
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h5 class="text-sm font-semibold ${textClass} capitalize">
                                        ${riskType.replace(/_/g, ' ')} Risk
                                    </h5>
                                    <div class="text-xs ${textClassSmall}">
                                        Risk Score: ${riskScore}/100
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold ${textClassBold} capitalize">${riskLevel}</span>
                                </div>
                            </div>
                        `;
                        
                        // Create metrics section
                        let metricsSection = '';
                        if (risk.metrics && Object.keys(risk.metrics).length > 0) {
                            metricsSection = `
                                <div class="mb-3">
                                    <h6 class="text-xs font-semibold ${textClass} mb-2">Key Metrics:</h6>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                            `;
                            
                            Object.entries(risk.metrics).forEach(([key, value]) => {
                                const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                const displayValue = typeof value === 'number' ? 
                                    (key.includes('percentage') ? `${value}%` : 
                                     key.includes('amount') || key.includes('revenue') || key.includes('cost') ? 
                                     `UGX ${value.toLocaleString()}` : value.toLocaleString()) : 
                                    value;
                                
                                metricsSection += `
                                    <div class="bg-white bg-opacity-50 rounded p-2">
                                        <div class="font-medium ${textClassSmall}">${displayKey}</div>
                                        <div class="font-bold ${textClassBold}">${displayValue}</div>
                                    </div>
                                `;
                            });
                            
                            metricsSection += `
                                    </div>
                                </div>
                            `;
                        }
                        
                        // Create factors section
                        let factorsSection = '';
                        if (risk.factors && Object.keys(risk.factors).length > 0) {
                            factorsSection = `
                                <div class="mb-3">
                                    <h6 class="text-xs font-semibold ${textClass} mb-2">Risk Factors:</h6>
                                    <div class="text-xs ${textClassSmall}">
                            `;
                            
                            Object.entries(risk.factors).forEach(([key, value]) => {
                                const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                factorsSection += `<div><span class="font-medium">${displayKey}:</span> ${value}</div>`;
                            });
                            
                            factorsSection += `
                                    </div>
                                </div>
                            `;
                        }
                        
                        // Create recommendations section
                        let recommendationsSection = '';
                        if (risk.recommendations && risk.recommendations.length > 0) {
                            recommendationsSection = `
                                <div>
                                    <h6 class="text-xs font-semibold ${textClass} mb-2">Recommendations:</h6>
                                    <ul class="text-xs ${textClassSmall} space-y-1">
                            `;
                            
                            risk.recommendations.forEach(rec => {
                                recommendationsSection += `<li class="flex items-start">
                                    <span class="mr-2 mt-1">•</span>
                                    <span>${rec}</span>
                                </li>`;
                            });
                            
                            recommendationsSection += `
                                    </ul>
                                </div>
                            `;
                        }
                        
                        div.innerHTML = riskHeader + metricsSection + factorsSection + recommendationsSection;
                        riskDiv.appendChild(div);
                    });
                } else {
                    riskDiv.innerHTML = '<span class="text-gray-500">No risk assessment data available.</span>';
                }
            })
            .catch(error => {
                const riskDiv = document.getElementById('riskAssessment');
                if (riskDiv) {
                    riskDiv.innerHTML = '<span class="text-red-500">Error loading risk assessment.</span>';
                }
                console.error('Error loading risk assessment:', error);
            });

        // Load customer segmentation data
        fetch('/api/analytics/customer-segmentation')
            .then(response => response.json())
            .then(data => {
                console.log('Customer segmentation data received:', data);
                
                if (customerSegmentationChart && data.segments) {
                    // Use the actual segment keys and capitalize for labels
                    const labels = Object.keys(data.segments).map(key =>
                        key.charAt(0).toUpperCase() + key.slice(1) + ' Customers'
                    );
                    const values = Object.values(data.segments);
                    customerSegmentationChart.data.labels = labels;
                    customerSegmentationChart.data.datasets[0].data = values;
                    customerSegmentationChart.update();
                }
            })
            .catch(error => {
                console.error('Error loading customer segmentation data:', error);
                
                if (customerSegmentationChart) {
                    customerSegmentationChart.data.labels = [];
                    customerSegmentationChart.data.datasets[0].data = [];
                    customerSegmentationChart.update();
                }
            });
    }

    function refreshAllData() {
        loadAnalyticsData();
        
        // Show refresh feedback
        const button = event.target;
        if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Refreshed!';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
        }
    }


</script>
@endsection 