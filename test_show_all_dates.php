<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Show All Dates Functionality ===\n\n";

use App\Http\Controllers\DetectionArchiveController;
use App\Services\CctvService;
use Illuminate\Http\Request;

try {
    // Test 1: Specific Date Mode
    echo "1. Testing Specific Date Mode (2025-06-23):\n";
    $request1 = new Request([
        'camera' => 'all',
        'date' => '2025-06-23',
        'detection_type' => 'all',
        'time_range' => 'all',
        'show_all_dates' => '0'
    ]);
    
    $cctvService = app(CctvService::class);
    $controller = new DetectionArchiveController($cctvService);
    
    $response1 = $controller->index($request1);
    $data1 = $response1->getData();
    
    echo "   - Files found: " . count($data1['detectionFiles']) . "\n";
    echo "   - Show all dates: " . ($data1['showAllDates'] ? 'true' : 'false') . "\n";
    echo "   - Cameras found: " . $data1['cameras']->count() . "\n\n";
    
    // Test 2: Show All Dates Mode
    echo "2. Testing Show All Dates Mode:\n";
    $request2 = new Request([
        'camera' => 'all',
        'date' => '2025-06-23', // This should be ignored
        'detection_type' => 'all',
        'time_range' => 'all',
        'show_all_dates' => '1'
    ]);
    
    $response2 = $controller->index($request2);
    $data2 = $response2->getData();
    
    echo "   - Files found: " . count($data2['detectionFiles']) . "\n";
    echo "   - Show all dates: " . ($data2['showAllDates'] ? 'true' : 'false') . "\n";
    echo "   - Cameras found: " . $data2['cameras']->count() . "\n\n";
    
    // Test 3: Compare results
    echo "3. Comparison:\n";
    $specificDateFiles = count($data1['detectionFiles']);
    $allDatesFiles = count($data2['detectionFiles']);
    
    echo "   - Specific date files: $specificDateFiles\n";
    echo "   - All dates files: $allDatesFiles\n";
    
    if ($allDatesFiles >= $specificDateFiles) {
        echo "   ✓ Show All Dates returned same or more files (expected)\n";
    } else {
        echo "   ✗ Show All Dates returned fewer files (unexpected)\n";
    }
    
    // Show some sample files if any found
    if (count($data2['detectionFiles']) > 0) {
        echo "\n4. Sample files from Show All Dates:\n";
        $sampleFiles = array_slice($data2['detectionFiles'], 0, 5);
        foreach ($sampleFiles as $file) {
            echo "   - " . $file['filename'] . " (Camera: " . $file['camera_name'] . ", Date: " . $file['date'] . ")\n";
        }
    }
    
    echo "\n✓ Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
