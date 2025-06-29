<?php

/**
 * CCTV Service Integration Test
 * 
 * This script tests the integration with the external CCTV service
 * to ensure all functionality is working correctly.
 */

use App\Services\CctvService;

// Test the CCTV service integration
echo "=== CCTV Service Integration Test ===\n\n";

$cctvService = new CctvService();

// Test 1: Check service connection
echo "1. Testing service connection...\n";
$status = $cctvService->getServiceStatus();
echo "   Status: " . $status['status'] . "\n";
if (isset($status['response_time'])) {
    echo "   Response time: " . number_format($status['response_time'] * 1000, 2) . "ms\n";
}
if (isset($status['error'])) {
    echo "   Error: " . $status['error'] . "\n";
}
echo "\n";

// Test 2: Get all cameras
echo "2. Testing camera retrieval...\n";
$cameras = $cctvService->getAllCameras();
if ($cameras !== null) {
    echo "   Successfully retrieved " . count($cameras) . " cameras\n";
    foreach ($cameras as $index => $camera) {
        echo "   Camera " . ($index + 1) . ": " . ($camera['name'] ?? 'Unknown') . 
             " at " . ($camera['location'] ?? 'Unknown location') . "\n";
    }
} else {
    echo "   Failed to retrieve cameras from the service\n";
}
echo "\n";

// Test 3: Test detection configuration
echo "3. Testing detection configuration...\n";
$detectionConfig = $cctvService->getDetectionConfig();
if ($detectionConfig !== null) {
    echo "   Successfully retrieved detection configuration\n";
    foreach ($detectionConfig as $key => $value) {
        echo "   $key: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
    }
} else {
    echo "   Failed to retrieve detection configuration\n";
}
echo "\n";

// Test 4: Stream URL generation
if (!empty($cameras) && is_array($cameras)) {
    echo "4. Testing stream URL generation...\n";
    $firstCamera = $cameras[0];
    if (isset($firstCamera['id'])) {
        $streamUrl = $cctvService->getStreamUrl($firstCamera['id']);
        echo "   Stream URL for camera '" . ($firstCamera['name'] ?? 'Unknown') . 
             "': " . $streamUrl . "\n";
    } else {
        echo "   No camera ID found to test stream URL\n";
    }
    echo "\n";
}

// Configuration information
echo "=== Configuration Information ===\n";
echo "CCTV Service Base URL: " . config('cctv.service.base_url') . "\n";
echo "Request Timeout: " . config('cctv.service.timeout') . "s\n";
echo "Retry Attempts: " . config('cctv.service.retry_attempts') . "\n";
echo "Connect Timeout: " . config('cctv.service.connect_timeout') . "s\n";
echo "\n";

echo "=== Test Complete ===\n";
