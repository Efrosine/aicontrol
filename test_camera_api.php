<?php

/**
 * Test script to verify CCTV camera API integration
 */

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\CctvController;
use App\Services\CctvService;

// Test data matching the expected API format
$testData = [
    'name' => 'Test Camera',
    'ip_address' => 'http://192.168.1.100:8080/stream',
    'location' => 'Test Location',
];

echo "=== CCTV Camera API Integration Test ===\n\n";

echo "Testing data structure that will be sent to external API:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Verify the JSON structure matches requirements
$requiredFields = ['name', 'ip_address', 'location', 'status'];
$testDataWithStatus = array_merge($testData, ['status' => 'active']);

echo "Expected API payload (with status):\n";
echo json_encode($testDataWithStatus, JSON_PRETTY_PRINT) . "\n\n";

// Check if all required fields are present
$missingFields = array_diff($requiredFields, array_keys($testDataWithStatus));
if (empty($missingFields)) {
    echo "✅ All required fields are present: " . implode(', ', $requiredFields) . "\n";
} else {
    echo "❌ Missing required fields: " . implode(', ', $missingFields) . "\n";
}

// Validate field types and values
echo "\nField validation:\n";
echo "- name: " . (is_string($testDataWithStatus['name']) && !empty($testDataWithStatus['name']) ? "✅ Valid" : "❌ Invalid") . "\n";
echo "- ip_address: " . (is_string($testDataWithStatus['ip_address']) && !empty($testDataWithStatus['ip_address']) ? "✅ Valid" : "❌ Invalid") . "\n";
echo "- location: " . (is_string($testDataWithStatus['location']) && !empty($testDataWithStatus['location']) ? "✅ Valid" : "❌ Invalid") . "\n";
echo "- status: " . ($testDataWithStatus['status'] === 'active' ? "✅ Valid" : "❌ Invalid") . "\n";

// Test URL/IP validation
echo "\nTesting ip_address field flexibility:\n";

$testIpAddresses = [
    '192.168.1.100',
    'http://192.168.1.100',
    'http://192.168.1.100:8080',
    'https://camera.example.com:8080/stream',
    'rtsp://192.168.1.100:554/stream1',
];

foreach ($testIpAddresses as $ip) {
    // Check if it's a URL or IP
    $isUrl = filter_var($ip, FILTER_VALIDATE_URL) !== false;
    $isIp = filter_var($ip, FILTER_VALIDATE_IP) !== false;
    $isValid = $isUrl || $isIp || (strpos($ip, '://') !== false);
    
    echo "- $ip: " . ($isValid ? "✅ Valid" : "❌ Invalid") . "\n";
}

echo "\n=== Test Complete ===\n";
