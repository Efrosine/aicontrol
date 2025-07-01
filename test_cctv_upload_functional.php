<?php

/**
 * Functional test for CCTV Upload API with actual file upload
 */

echo "🎥 CCTV Upload API - Functional Test\n";
echo "====================================\n\n";

// Configuration
$uploadUrl = 'http://localhost:8080/api/cctv/upload';

// Create a simple test image (1x1 pixel PNG)
$testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
$testImagePath = tempnam(sys_get_temp_dir(), 'cctv_test_') . '.png';
file_put_contents($testImagePath, $testImageData);

echo "1. Creating test image: " . basename($testImagePath) . "\n";
echo "   Size: " . filesize($testImagePath) . " bytes\n\n";

// Test data
$testCases = [
    [
        'name' => 'Valid upload with all parameters',
        'data' => [
            'cctv_name' => 'Test Camera 001',
            'detection_type' => 'person',
            'timestamp' => '2025-07-01T14:30:22Z'
        ]
    ],
    [
        'name' => 'Valid upload without timestamp (should use server time)',
        'data' => [
            'cctv_name' => 'Entrance Camera',
            'detection_type' => 'vehicle'
        ]
    ],
    [
        'name' => 'Invalid detection type (should fail)',
        'data' => [
            'cctv_name' => 'Test Camera',
            'detection_type' => 'invalid_type'
        ]
    ],
    [
        'name' => 'Missing required field (should fail)',
        'data' => [
            'cctv_name' => 'Test Camera'
            // missing detection_type
        ]
    ]
];

// Function to test upload
function testUpload($url, $imagePath, $data, $testName) {
    echo "Testing: {$testName}\n";
    echo "----------------------------------------\n";
    
    // Check if we can reach the server first
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $baseUrl = dirname($url);
    
    if (!@file_get_contents($baseUrl, false, $context)) {
        echo "❌ Cannot reach server at {$baseUrl}\n";
        echo "💡 Make sure Laravel is running: php artisan serve\n\n";
        return false;
    }
    
    $ch = curl_init();
    
    // Build form data
    $formData = ['file' => new CURLFile($imagePath, 'image/png', 'test_detection.png')];
    $formData = array_merge($formData, $data);
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $formData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        echo "❌ cURL Error: {$curlError}\n\n";
        return false;
    }
    
    echo "📋 Request Data:\n";
    foreach ($data as $key => $value) {
        echo "   {$key}: {$value}\n";
    }
    echo "   file: test_detection.png (test image)\n\n";
    
    echo "📤 HTTP Response Code: {$httpCode}\n";
    echo "📤 Response Body:\n";
    
    $responseData = json_decode($response, true);
    if ($responseData) {
        echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
        
        if ($responseData['success'] ?? false) {
            echo "✅ Upload successful!\n";
            if (isset($responseData['data']['storage_path'])) {
                echo "📁 Stored at: " . $responseData['data']['storage_path'] . "\n";
            }
        } else {
            echo "❌ Upload failed as expected\n";
        }
    } else {
        echo $response . "\n";
        echo "⚠️  Could not parse JSON response\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n\n";
    return true;
}

// Run tests
foreach ($testCases as $i => $testCase) {
    echo "Test " . ($i + 1) . "/{" . count($testCases) . "}\n";
    
    if (!testUpload($uploadUrl, $testImagePath, $testCase['data'], $testCase['name'])) {
        echo "⚠️  Skipping remaining tests due to server connectivity issues.\n";
        break;
    }
    
    // Small delay between tests
    sleep(1);
}

// Cleanup
unlink($testImagePath);

echo "🏁 Test completed!\n";
echo "\n📋 Expected behavior:\n";
echo "   • Tests 1-2: Should succeed (HTTP 201)\n";
echo "   • Tests 3-4: Should fail validation (HTTP 400)\n";
echo "   • Files should be stored in MinIO with path format:\n";
echo "     {sanitized-camera-name}/{yyyy}/{mm}/{dd}/{detection_type}/test_detection.png\n";

?>
