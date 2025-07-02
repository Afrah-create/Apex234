<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\AdminReportController;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Reports API...\n";

try {
    // Test 1: Get report templates
    echo "\n1. Testing getReportTemplates...\n";
    $controller = new AdminReportController();
    $response = $controller->getReportTemplates();
    echo "Templates response: " . $response->getContent() . "\n";
    
    // Test 2: Generate a simple report
    echo "\n2. Testing generateCustomReport...\n";
    $request = new Request();
    $request->merge([
        'report_type' => 'sales_summary',
        'date_from' => '2025-01-01',
        'date_to' => '2025-12-31',
        'filters' => [],
        'group_by' => '',
        'sort_by' => 'created_at',
        'sort_order' => 'desc'
    ]);
    
    $response = $controller->generateCustomReport($request);
    echo "Generate response: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n"; 