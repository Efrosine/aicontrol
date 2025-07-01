<?php

// Simple test script to check if the download endpoint works
// Run this with: php test_download_endpoint.php

$baseUrl = 'http://localhost:8080'; // Adjust this to your Laravel app URL
$endpoint = '/api/archive/fetch/camera001/2025/07/01/person/test_image.jpg';

$url = $baseUrl . $endpoint;

echo "Testing download endpoint: " . $url . "\n";

// Test if the endpoint is accessible
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request to check if endpoint exists
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "CURL Error: " . $error . "\n";
    exit(1);
}

echo "HTTP Status Code: " . $httpCode . "\n";

if ($httpCode == 404) {
    echo "✅ Endpoint is accessible (404 is expected for non-existent file)\n";
} elseif ($httpCode == 200) {
    echo "✅ Endpoint is accessible and file exists\n";
} elseif ($httpCode == 500) {
    echo "❌ Server error - check logs\n";
    echo "Response headers:\n" . $response . "\n";
} else {
    echo "Response headers:\n" . $response . "\n";
}

echo "\nTo test with an actual file, first upload one using the upload endpoint:\n";
echo "curl -X POST " . $baseUrl . "/api/cctv/upload \\\n";
echo "  -F \"file=@your_test_file.jpg\" \\\n";
echo "  -F \"cctv_name=camera001\" \\\n";
echo "  -F \"detection_type=person\"\n";

echo "\nThen download it using:\n";
echo "curl -O " . $url . "\n";
