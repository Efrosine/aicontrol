<?php

// Test that shows the actual API response
$baseUrl = 'http://localhost:8080'; // Adjust this to your Laravel app URL
$endpoint = '/api/archive/fetch/camera001/2025/07/01/person/test_image.jpg';

$url = $baseUrl . $endpoint;

echo "Testing download endpoint: " . $url . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
echo "Response Body:\n" . $response . "\n";

if ($httpCode == 404) {
    echo "✅ Endpoint is working (404 expected for non-existent file)\n";
} elseif ($httpCode == 200) {
    echo "✅ File download successful\n";
} else {
    echo "❌ Unexpected response\n";
}
