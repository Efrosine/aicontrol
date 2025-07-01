<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

// Test URL parameters
echo "=== Testing URL Parameters ===\n";

// Simulate the exact request that would come from the browser
$request = Request::create('/admin/security/detection-archive', 'GET', [
    'date' => '2025-06-23',
    'camera' => 'all',
    'detection_type' => 'vehicle',
    'time_range' => 'all'
]);

echo "Request URL: " . $request->fullUrl() . "\n";
echo "Parameters:\n";
echo "- date: " . $request->get('date', 'not set') . "\n";
echo "- camera: " . $request->get('camera', 'not set') . "\n";
echo "- detection_type: " . $request->get('detection_type', 'not set') . "\n";
echo "- time_range: " . $request->get('time_range', 'not set') . "\n";

// Test date parsing
$selectedDate = $request->get('date', date('Y-m-d'));
echo "\nDate handling:\n";
echo "- Selected date: $selectedDate\n";
echo "- Default date: " . date('Y-m-d') . "\n";

$dateObj = \DateTime::createFromFormat('Y-m-d', $selectedDate);
if ($dateObj) {
    $year = $dateObj->format('Y');
    $month = $dateObj->format('m');
    $day = $dateObj->format('d');
    echo "- Parsed: year=$year, month=$month, day=$day\n";
    echo "- Expected MinIO path prefix: {camera_id}/$year/$month/$day/\n";
    echo "- Example path: camera001/$year/$month/$day/vehicle/\n";
} else {
    echo "- ERROR: Could not parse date\n";
}
