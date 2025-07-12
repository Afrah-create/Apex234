<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Test</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .chart-container { height: 400px; margin: 20px 0; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; }
        button { padding: 10px 20px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Demand Forecasting Chart Test</h1>
        
        <button onclick="testMLAPI()">Test ML API</button>
        <button onclick="loadChartData()">Load Chart Data</button>
        <button onclick="clearChart()">Clear Chart</button>
        
        <div class="chart-container">
            <canvas id="testChart"></canvas>
        </div>
        
        <div id="debug" class="debug">
            <h3>Debug Information:</h3>
            <div id="debugContent"></div>
        </div>
    </div>

    <script>
        let testChart;
        
        // Initialize chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('testChart').getContext('2d');
            testChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Actual Demand',
                        data: [],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Predicted Demand',
                        data: [],
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
            
            updateDebug('Chart initialized');
        });
        
        function testMLAPI() {
            updateDebug('Testing ML API...');
            
            fetch('/test-ml')
                .then(response => response.json())
                .then(data => {
                    updateDebug('ML API Response: ' + JSON.stringify(data, null, 2));
                    
                    if (data.success && data.data) {
                        updateDebug('✅ ML API working correctly');
                        return data.data;
                    } else {
                        throw new Error('ML API returned error: ' + JSON.stringify(data));
                    }
                })
                .catch(error => {
                    updateDebug('❌ ML API Error: ' + error.message);
                });
        }
        
        function loadChartData() {
            updateDebug('Loading chart data...');
            
            fetch('/test-ml')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && data.data.historical && data.data.predicted) {
                        const historical = data.data.historical;
                        const predicted = data.data.predicted;
                        
                        // Extract data
                        const historicalMonths = historical.map(d => d.month);
                        const historicalValues = historical.map(d => d.actual_sales);
                        const predictedMonths = predicted.map(d => d.month);
                        const predictedValues = predicted.map(d => d.predicted_sales);
                        
                        // Combine for chart
                        const allMonths = [...historicalMonths, ...predictedMonths];
                        const allActuals = [...historicalValues, ...Array(predictedValues.length).fill(null)];
                        const allPredicted = [...Array(historicalValues.length).fill(null), ...predictedValues];
                        
                        // Update chart
                        testChart.data.labels = allMonths;
                        testChart.data.datasets[0].data = allActuals;
                        testChart.data.datasets[1].data = allPredicted;
                        testChart.update();
                        
                        updateDebug(`✅ Chart updated with ${historical.length} historical and ${predicted.length} predicted data points`);
                        updateDebug(`Historical: ${historicalValues.join(', ')}`);
                        updateDebug(`Predicted: ${predictedValues.join(', ')}`);
                    } else {
                        updateDebug('❌ Invalid data structure received');
                    }
                })
                .catch(error => {
                    updateDebug('❌ Error loading chart data: ' + error.message);
                });
        }
        
        function clearChart() {
            testChart.data.labels = [];
            testChart.data.datasets[0].data = [];
            testChart.data.datasets[1].data = [];
            testChart.update();
            updateDebug('Chart cleared');
        }
        
        function updateDebug(message) {
            const debugContent = document.getElementById('debugContent');
            const timestamp = new Date().toLocaleTimeString();
            debugContent.innerHTML += `<div>[${timestamp}] ${message}</div>`;
            debugContent.scrollTop = debugContent.scrollHeight;
        }
    </script>
</body>
</html> 