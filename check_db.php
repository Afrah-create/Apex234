<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking raw_materials table structure...\n";

try {
    $materialTypeColumn = DB::select("SHOW COLUMNS FROM raw_materials LIKE 'material_type'");
    echo "Material Type Column:\n";
    print_r($materialTypeColumn);
    
    $statusColumn = DB::select("SHOW COLUMNS FROM raw_materials LIKE 'status'");
    echo "\nStatus Column:\n";
    print_r($statusColumn);
    
    $qualityGradeColumn = DB::select("SHOW COLUMNS FROM raw_materials LIKE 'quality_grade'");
    echo "\nQuality Grade Column:\n";
    print_r($qualityGradeColumn);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 