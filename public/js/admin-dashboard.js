document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('adminChart');
    if (!ctx) return;
    const adminChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Add your time labels here
            datasets: [{
                label: 'Real-Time Metric',
                data: [], // Add your data points here
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { display: true, title: { display: true, text: 'Time' } },
                y: { display: true, title: { display: true, text: 'Value' } }
            }
        }
    });
    // To update the chart with real-time data, use:
    // adminChart.data.labels = [...];
    // adminChart.data.datasets[0].data = [...];
    // adminChart.update();
    // Example fetch (replace URL with your API endpoint):
    // fetch('/api/admin-metrics')
    //   .then(response => response.json())
    //   .then(data => {
    //     adminChart.data.labels = data.labels;
    //     adminChart.data.datasets[0].data = data.values;
    //     adminChart.update();
    //   });
}); 