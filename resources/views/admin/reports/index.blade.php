@extends('layouts.app')

@section('content')
<main class="main-content">
    <!-- Navigation Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Advanced Reports & Analytics</h1>
            <div class="flex space-x-4">
                <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
                <button onclick="refreshReports()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        <p class="text-gray-600">Generate custom reports, schedule automated reports, and export data in multiple formats.</p>
    </div>

    <!-- Report Templates -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Report Templates</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="report-templates">
            <!-- Templates will be loaded here -->
        </div>
    </div>

    <!-- Custom Report Builder -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Custom Report Builder</h2>
        
        <form id="custom-report-form" class="space-y-6">
            <!-- Report Type Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                    <select id="report-type" name="report_type" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Report Type</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select id="date-range" name="date_range" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="year">This year</option>
                        <option value="custom">Custom range</option>
                    </select>
                </div>
                <!-- Add Format Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select id="export-format" name="export_format" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
            </div>

            <!-- Custom Date Range -->
            <div id="custom-date-range" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" id="date-from" name="date_from" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" id="date-to" name="date_to" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Filters -->
            <div id="report-filters" class="space-y-4">
                <!-- Dynamic filters will be loaded here -->
            </div>

            <!-- Group By and Sort -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Group By</label>
                    <select id="group-by" name="group_by" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">No grouping</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sort-by" name="sort_by" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="created_at">Date Created</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <select id="sort-order" name="sort_order" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Generate Report
                </button>
                
                <button type="button" onclick="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Report
                </button>
                
                <button type="button" onclick="scheduleReport()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Schedule Report
                </button>
            </div>
        </form>
    </div>

    <!-- Report Results -->
    <div id="report-results" class="bg-white rounded-lg shadow-md p-6 mb-8" style="display: none;">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Report Results</h2>
            <div class="flex space-x-2">
                <button onclick="exportCurrentReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    Export
                </button>
                <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    Print
                </button>
            </div>
        </div>
        
        <!-- Report Summary -->
        <div id="report-summary" class="summary-cards mb-6">
            <!-- Summary cards will be populated here -->
        </div>
        
        <!-- Report Chart -->
        <div id="report-chart" class="mb-6">
            <div class="relative" style="height: 400px;">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
        
        <!-- Report Table -->
        <div id="report-table" class="overflow-x-auto">
            <table class="user-table">
                <thead id="report-table-head">
                    <!-- Table headers will be populated here -->
                </thead>
                <tbody id="report-table-body">
                    <!-- Table data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scheduled Reports -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Scheduled Reports</h2>
        <div id="scheduled-reports" class="space-y-4">
            <!-- Scheduled reports will be loaded here -->
        </div>
    </div>
</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let currentReportData = null;
let reportChart = null;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadReportTemplates();
    populateReportTypeDropdown();
    loadReportFilters();
    setupEventListeners();
    loadScheduledReports(); // Ensure this is called on page load

    // Add toggle button above scheduled reports section
    const scheduledReportsSection = document.getElementById('scheduled-reports');
    const toggleBtn = document.createElement('button');
    toggleBtn.id = 'toggle-scheduled-reports-btn';
    toggleBtn.className = 'bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mb-4';
    toggleBtn.textContent = 'Hide Scheduled Reports';
    scheduledReportsSection.parentNode.insertBefore(toggleBtn, scheduledReportsSection);
    let scheduledReportsVisible = true;
    toggleBtn.addEventListener('click', function() {
        scheduledReportsVisible = !scheduledReportsVisible;
        scheduledReportsSection.style.display = scheduledReportsVisible ? '' : 'none';
        toggleBtn.textContent = scheduledReportsVisible ? 'Hide Scheduled Reports' : 'Show Scheduled Reports';
    });
});

// Populate the report type dropdown on page load
async function populateReportTypeDropdown() {
    try {
        const response = await fetch('{{ route("api.reports.templates") }}');
        const templates = await response.json();
        const reportTypeDropdown = document.getElementById('report-type');
        if (!reportTypeDropdown) return;

        // Clear existing options except the first
        reportTypeDropdown.innerHTML = '<option value="">Select Report Type</option>';

        templates.forEach(template => {
            const option = document.createElement('option');
            option.value = template.id;
            option.textContent = template.name;
            reportTypeDropdown.appendChild(option);
        });
    } catch (error) {
        console.error('Error populating report type dropdown:', error);
    }
}

// Load report templates
async function loadReportTemplates() {
    try {
        const response = await fetch('{{ route("api.reports.templates") }}');
        const templates = await response.json();
        
        const container = document.getElementById('report-templates');
        container.innerHTML = '';
        
        templates.forEach(template => {
            const templateCard = createTemplateCard(template);
            container.appendChild(templateCard);
        });
    } catch (error) {
        console.error('Error loading report templates:', error);
    }
}

// Create template card
function createTemplateCard(template) {
    const card = document.createElement('div');
    card.className = 'product-card cursor-pointer';
    card.onclick = () => selectTemplate(template);
    
    card.innerHTML = `
        <div class="text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">${template.name}</h3>
            <p class="text-sm text-gray-600 mb-3">${template.description}</p>
            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">${template.category}</span>
        </div>
    `;
    
    return card;
}

// Select template
function selectTemplate(template) {
    document.getElementById('report-type').value = template.id;
    generateReport();
}

// Load report filters
async function loadReportFilters() {
    try {
        const response = await fetch('{{ route("api.reports.filters") }}');
        const filters = await response.json();
        
        // Populate date range options
        const dateRangeSelect = document.getElementById('date-range');
        dateRangeSelect.innerHTML = '';
        filters.date_ranges.forEach(range => {
            const option = document.createElement('option');
            option.value = range.value;
            option.textContent = range.label;
            dateRangeSelect.appendChild(option);
        });
        
        // Populate group by options
        const groupBySelect = document.getElementById('group-by');
        groupBySelect.innerHTML = '<option value="">No grouping</option>';
        filters.group_by_options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.value;
            opt.textContent = option.label;
            groupBySelect.appendChild(opt);
        });
    } catch (error) {
        console.error('Error loading report filters:', error);
    }
}

// Setup event listeners
function setupEventListeners() {
    // Date range change
    document.getElementById('date-range').addEventListener('change', function() {
        const customRange = document.getElementById('custom-date-range');
        if (this.value === 'custom') {
            customRange.style.display = 'grid';
        } else {
            customRange.style.display = 'none';
            updateDateInputs(this.value);
        }
    });
    
    // Form submission
    document.getElementById('custom-report-form').addEventListener('submit', function(e) {
        e.preventDefault();
        generateReport();
    });
}

// Update date inputs based on range
function updateDateInputs(range) {
    const dateFrom = document.getElementById('date-from');
    const dateTo = document.getElementById('date-to');
    
    const today = new Date();
    let fromDate = new Date();
    
    switch(range) {
        case '7':
            fromDate.setDate(today.getDate() - 7);
            break;
        case '30':
            fromDate.setDate(today.getDate() - 30);
            break;
        case '90':
            fromDate.setDate(today.getDate() - 90);
            break;
        case 'year':
            fromDate = new Date(today.getFullYear(), 0, 1);
            break;
    }
    
    dateFrom.value = fromDate.toISOString().split('T')[0];
    dateTo.value = today.toISOString().split('T')[0];
}

// Generate report
async function generateReport() {
    const form = document.getElementById('custom-report-form');
    const formData = new FormData(form);
    
    // Get date range
    const dateRange = formData.get('date_range');
    if (dateRange !== 'custom') {
        updateDateInputs(dateRange);
    }
    
    const reportData = {
        report_type: formData.get('report_type'),
        date_from: document.getElementById('date-from').value,
        date_to: document.getElementById('date-to').value,
        filters: {}, // Add filter logic here
        group_by: formData.get('group_by'),
        sort_by: formData.get('sort_by'),
        sort_order: formData.get('sort_order')
    };
    
    try {
        const response = await fetch('{{ route("api.reports.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(reportData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentReportData = result.data;
            displayReportResults(result.data);
        } else {
            alert('Error generating report: ' + result.message);
        }
    } catch (error) {
        console.error('Error generating report:', error);
        alert('Error generating report. Please try again.');
    }
}

// Display report results
function displayReportResults(data) {
    const resultsDiv = document.getElementById('report-results');
    resultsDiv.style.display = 'block';
    
    // Add section headings
    document.getElementById('report-summary').innerHTML = '<h3 style="font-size:1.25rem; font-weight:600; margin-bottom:1rem;">Summary</h3>';
    document.getElementById('report-chart').insertAdjacentHTML('afterbegin', '<h3 style="font-size:1.25rem; font-weight:600; margin-bottom:1rem;">Visualization</h3>');
    document.getElementById('report-table').insertAdjacentHTML('afterbegin', '<h3 style="font-size:1.25rem; font-weight:600; margin-bottom:1rem;">Detailed Data</h3>');

    // Check if report has data
    if (!data.data || data.data.length === 0) {
        displayEmptyReport(data.summary);
        return;
    }
    
    // Display summary
    displayReportSummary(data.summary);
    
    // Display chart
    displayReportChart(data.chart_data);
    
    // Display table
    displayReportTable(data.data);
    
    // Scroll to results
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}

// Display empty report message
function displayEmptyReport(summary) {
    const resultsDiv = document.getElementById('report-results');
    resultsDiv.style.display = 'block';
    
    // Display empty message
    const summaryDiv = document.getElementById('report-summary');
    summaryDiv.innerHTML = `
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-medium text-yellow-800 mb-2">No Data Found</h3>
            <p class="text-yellow-700">${summary.message || 'No data available for the selected criteria.'}</p>
            <p class="text-sm text-yellow-600 mt-2">Report Type: ${summary.report_type || 'Unknown'}</p>
            <p class="text-sm text-yellow-600">Date Range: ${summary.date_range || 'Unknown'}</p>
        </div>
    `;
    
    // Hide chart and table
    document.getElementById('report-chart').style.display = 'none';
    document.getElementById('report-table').style.display = 'none';
    
    // Scroll to results
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}

// Display report summary
function displayReportSummary(summary) {
    const summaryDiv = document.getElementById('report-summary');
    summaryDiv.innerHTML = '';
    
    Object.entries(summary).forEach(([key, value]) => {
        const card = document.createElement('div');
        card.className = 'summary-card';
        card.style.setProperty('--summary-card-border', '#3b82f6');
        
        card.innerHTML = `
            <div class="icon" style="background: #dbeafe; color: #3b82f6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="details">
                <p>${key.replace(/_/g, ' ').toUpperCase()}</p>
                <p>${typeof value === 'number' ? value.toLocaleString() : value}</p>
            </div>
        `;
        
        summaryDiv.appendChild(card);
    });
}

// Display report chart
function displayReportChart(chartData) {
    const canvas = document.getElementById('reportChart');
    const ctx = canvas.getContext('2d');
    
    if (reportChart) {
        reportChart.destroy();
    }
    
    reportChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Report Data Visualization'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Display report table
function displayReportTable(data) {
    const tableContainer = document.getElementById('report-table');
    
    if (!data || data.length === 0) {
        tableContainer.innerHTML = `
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-500">No data available for display</p>
            </div>
        `;
        return;
    }
    
    const tableHead = document.getElementById('report-table-head');
    const tableBody = document.getElementById('report-table-body');
    
    // Clear existing content
    tableHead.innerHTML = '';
    tableBody.innerHTML = '';
    
    // Create headers
    const headerRow = document.createElement('tr');
    Object.keys(data[0]).forEach(key => {
        const th = document.createElement('th');
        th.textContent = key.replace(/_/g, ' ').toUpperCase();
        headerRow.appendChild(th);
    });
    tableHead.appendChild(headerRow);
    
    // Create rows
    data.forEach(row => {
        const tr = document.createElement('tr');
        Object.values(row).forEach(value => {
            const td = document.createElement('td');
            if (typeof value === 'object' && value !== null) {
                // Handle nested objects (like role_metrics)
                td.textContent = JSON.stringify(value);
            } else if (typeof value === 'number') {
                td.textContent = value.toLocaleString();
            } else {
                td.textContent = value || '';
            }
            tr.appendChild(td);
        });
        tableBody.appendChild(tr);
    });
}

// Export report
async function exportReport() {
    const form = document.getElementById('custom-report-form');
    const formData = new FormData(form);
    // Get selected format from dropdown
    const selectedFormat = formData.get('export_format') || 'pdf';
    const exportData = {
        report_type: formData.get('report_type'),
        format: selectedFormat,
        date_from: document.getElementById('date-from').value,
        date_to: document.getElementById('date-to').value,
        filters: {}
    };
    
    try {
        const response = await fetch('{{ route("api.reports.export") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(exportData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.open(result.download_url, '_blank');
        } else {
            alert('Error exporting report: ' + result.message);
        }
    } catch (error) {
        console.error('Error exporting report:', error);
        alert('Error exporting report. Please try again.');
    }
}

// Export current report
function exportCurrentReport() {
    if (!currentReportData) {
        alert('No report data to export');
        return;
    }

    // Optionally, get the format from the dropdown or default to PDF
    const format = document.getElementById('export-format')?.value || 'pdf';

    fetch('{{ route("api.reports.export") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            report_data: currentReportData,
            format: format
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.open(result.download_url, '_blank');
        } else {
            alert('Error exporting report: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error exporting report:', error);
        alert('Error exporting report. Please try again.');
    });
}

// Print report
function printReport() {
    const reportContent = document.getElementById('report-results');
    if (!reportContent) {
        alert('No report to print!');
        return;
    }
    // Gather report metadata
    const reportType = document.getElementById('report-type').selectedOptions[0]?.textContent || 'Report';
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    const generatedAt = new Date().toLocaleString();
    // Build header
    const headerHtml = `
        <div style="text-align:center; margin-bottom: 2rem;">
            <h1 style="font-size:2rem; font-weight:bold; margin-bottom:0.5rem;">${reportType}</h1>
            <div style="font-size:1rem; color:#555;">Date Range: <b>${dateFrom}</b> to <b>${dateTo}</b></div>
            <div style="font-size:1rem; color:#555;">Generated: <b>${generatedAt}</b></div>
        </div>
    `;
    // Print only the report section with header
    const printWindow = window.open('', '', 'width=900,height=650');
    printWindow.document.write(`
        <html>
        <head>
            <title>Print Report</title>
            <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        </head>
        <body>
            ${headerHtml}
            ${reportContent.innerHTML}
        </body>
        </html>
    `);
    injectPrintStyles(printWindow);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

// Schedule report
function scheduleReport() {
    // Implementation for scheduling reports
    alert('Report scheduling feature coming soon!');
}

// Refresh reports
function refreshReports() {
    loadReportTemplates();
    loadReportFilters();
    loadScheduledReports();
}

// Load scheduled reports
async function loadScheduledReports() {
    try {
        const response = await fetch('{{ route("api.reports.scheduled") }}');
        const result = await response.json();
        
        if (result.success) {
            displayScheduledReports(result.data);
        } else {
            console.error('Error loading scheduled reports:', result.message);
        }
    } catch (error) {
        console.error('Error loading scheduled reports:', error);
    }
}

// Display scheduled reports
function displayScheduledReports(reports) {
    console.log('Scheduled reports received:', reports); // Debug log
    const container = document.getElementById('scheduled-reports');
    container.innerHTML = '';
    
    if (reports.length === 0) {
        container.innerHTML = `
            <div class="bg-gray-50 rounded-lg p-8 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Scheduled Reports</h3>
                <p class="text-gray-600 mb-4">You haven't created any scheduled reports yet.</p>
                <p class="text-sm text-gray-500">Use the "Schedule Report" button above to create your first automated report.</p>
            </div>
        `;
        return;
    }
    
    reports.forEach(report => {
        const reportCard = createScheduledReportCard(report);
        container.appendChild(reportCard);
    });
}

// Create scheduled report card
function createScheduledReportCard(report) {
    const card = document.createElement('div');
    card.className = 'bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition duration-200';
    
    const statusBadge = report.is_active 
        ? '<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">Active</span>'
        : '<span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">Inactive</span>';
    
    const nextRun = report.next_generation_at 
        ? new Date(report.next_generation_at).toLocaleString()
        : 'Not scheduled';
    
    card.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900 text-lg">${report.name}</h3>
            ${statusBadge}
        </div>
        <p class="text-sm text-gray-600 mb-2"><b>Receivers:</b> ${(Array.isArray(report.recipients) ? report.recipients.join(', ') : (report.recipients || 'N/A'))}</p>
        <p class="text-sm text-gray-600 mb-4 leading-relaxed">${report.description || 'No description provided'}</p>
        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div class="bg-gray-50 p-2 rounded">
                <span class="font-medium text-gray-700">Type:</span>
                <span class="text-gray-600 ml-1">${report.report_type.replace(/_/g, ' ').toUpperCase()}</span>
            </div>
            <div class="bg-gray-50 p-2 rounded">
                <span class="font-medium text-gray-700">Frequency:</span>
                <span class="text-gray-600 ml-1">${report.frequency}</span>
            </div>
            <div class="bg-gray-50 p-2 rounded">
                <span class="font-medium text-gray-700">Format:</span>
                <span class="text-gray-600 ml-1">${report.format.toUpperCase()}</span>
            </div>
            <div class="bg-gray-50 p-2 rounded">
                <span class="font-medium text-gray-700">Next Run:</span>
                <span class="text-gray-600 ml-1">${nextRun}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="editScheduledReport(${report.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </button>
            <button onclick="toggleScheduledReport(${report.id})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                ${report.is_active ? 'Disable' : 'Enable'}
            </button>
            <button onclick="triggerScheduledReport(${report.id})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Run Now
            </button>
            <button onclick="deleteScheduledReport(${report.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete
            </button>
        </div>
    `;
    
    return card;
}

// Edit scheduled report
function editScheduledReport(id) {
    // Implementation for editing scheduled report
    alert('Edit scheduled report functionality coming soon!');
}

// Toggle scheduled report status
async function toggleScheduledReport(id) {
    try {
        const response = await fetch(`{{ route('api.reports.scheduled.toggle', ['id' => ':id']) }}`.replace(':id', id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadScheduledReports();
        } else {
            alert('Error updating report status: ' + result.message);
        }
    } catch (error) {
        console.error('Error toggling scheduled report:', error);
        alert('Error updating report status. Please try again.');
    }
}

// Trigger scheduled report
async function triggerScheduledReport(id) {
    try {
        const response = await fetch(`{{ route('api.reports.scheduled.trigger', ['id' => ':id']) }}`.replace(':id', id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Report generated and delivered successfully!');
        } else {
            alert('Error triggering report: ' + result.message);
        }
    } catch (error) {
        console.error('Error triggering scheduled report:', error);
        alert('Error triggering report. Please try again.');
    }
}

// Delete scheduled report
async function deleteScheduledReport(id) {
    (async function() {
        const confirmed = await showConfirmModal('Are you sure you want to delete this scheduled report?', 'Delete Scheduled Report');
        if (!confirmed) return;
        try {
            const response = await fetch(`{{ route('api.reports.scheduled.delete', ['id' => ':id']) }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                loadScheduledReports();
            } else {
                alert('Error deleting report: ' + result.message);
            }
        } catch (error) {
            console.error('Error deleting scheduled report:', error);
            alert('Error deleting report. Please try again.');
        }
    })();
}

// Enhanced schedule report function
function scheduleReport() {
    const form = document.getElementById('custom-report-form');
    const formData = new FormData(form);
    
    // Get date range
    const dateRange = formData.get('date_range');
    if (dateRange !== 'custom') {
        updateDateInputs(dateRange);
    }
    
    const reportConfig = {
        report_type: formData.get('report_type'),
        date_from: document.getElementById('date-from').value,
        date_to: document.getElementById('date-to').value,
        filters: {}, // Add filter logic here
        group_by: formData.get('group_by'),
        sort_by: formData.get('sort_by'),
        sort_order: formData.get('sort_order')
    };
    
    // Show scheduling modal
    showSchedulingModal(reportConfig);
}

// Show scheduling modal
function showSchedulingModal(reportConfig) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.id = 'scheduling-modal';
    
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Report</h3>
                <form id="schedule-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Name</label>
                        <input type="text" id="schedule-name" name="name" class="w-full p-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="schedule-description" name="description" class="w-full p-2 border border-gray-300 rounded-lg" rows="2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                        <select id="schedule-frequency" name="frequency" class="w-full p-2 border border-gray-300 rounded-lg" required>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div id="weekly-options" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Day of Week</label>
                        <select id="schedule-day-of-week" name="day_of_week" class="w-full p-2 border border-gray-300 rounded-lg">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    <div id="monthly-options" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Day of Month</label>
                        <input type="number" id="schedule-day-of-month" name="day_of_month" min="1" max="31" class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                        <input type="time" id="schedule-time" name="time" class="w-full p-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipients (comma-separated emails)</label>
                        <input type="text" id="schedule-recipients" name="recipients" class="w-full p-2 border border-gray-300 rounded-lg" placeholder="email1@example.com, email2@example.com" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                        <select id="schedule-format" name="format" class="w-full p-2 border border-gray-300 rounded-lg" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Schedule Report
                        </button>
                        <button type="button" onclick="closeSchedulingModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Setup form event listeners
    setupSchedulingForm(reportConfig);
}

// Setup scheduling form
function setupSchedulingForm(reportConfig) {
    const form = document.getElementById('schedule-form');
    const frequencySelect = document.getElementById('schedule-frequency');
    const weeklyOptions = document.getElementById('weekly-options');
    const monthlyOptions = document.getElementById('monthly-options');
    
    // Set default time
    document.getElementById('schedule-time').value = '09:00';
    
    // Handle frequency change
    frequencySelect.addEventListener('change', function() {
        weeklyOptions.style.display = this.value === 'weekly' ? 'block' : 'none';
        monthlyOptions.style.display = this.value === 'monthly' || this.value === 'quarterly' || this.value === 'yearly' ? 'block' : 'none';
    });
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitScheduledReport(reportConfig);
    });
}

// Submit scheduled report
async function submitScheduledReport(reportConfig) {
    const form = document.getElementById('schedule-form');
    const formData = new FormData(form);
    
    const recipients = formData.get('recipients').split(',').map(email => email.trim()).filter(email => email);
    
    const scheduleData = {
        name: formData.get('name'),
        description: formData.get('description'),
        report_type: reportConfig.report_type,
        report_config: reportConfig,
        frequency: formData.get('frequency'),
        day_of_week: formData.get('day_of_week'),
        day_of_month: formData.get('day_of_month'),
        time: formData.get('time'),
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        recipients: recipients,
        format: formData.get('format')
    };
    
    try {
        const response = await fetch('{{ route("api.reports.scheduled.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(scheduleData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Report scheduled successfully!');
            closeSchedulingModal();
            loadScheduledReports();
        } else {
            alert('Error scheduling report: ' + result.message);
        }
    } catch (error) {
        console.error('Error scheduling report:', error);
        alert('Error scheduling report. Please try again.');
    }
}

// Close scheduling modal
function closeSchedulingModal() {
    const modal = document.getElementById('scheduling-modal');
    if (modal) {
        modal.remove();
    }
}

// Initialize date inputs
updateDateInputs('30');
</script>

<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection 