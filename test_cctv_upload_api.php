<?php

/**
 * Test script for CCTV Upload API Endpoint
 * 
 * This script demonstrates how third-party clients can upload files
 * to the CCTV detection archive system.
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "🎥 CCTV Upload API Test\n";
echo "=====================\n\n";

// Configuration
$baseUrl = 'http://localhost:8080'; // Adjust to your Laravel app URL
$uploadEndpoint = $baseUrl . '/api/cctv/upload';

echo "1. Testing Upload Endpoint Configuration...\n";
echo "   Endpoint: {$uploadEndpoint}\n";
echo "   Access: Public (no authentication required)\n\n";

echo "2. Supported File Types:\n";
echo "   Images: jpg, jpeg, png\n";
echo "   Videos: mp4, avi, mov\n";
echo "   Max Size: 100MB\n\n";

echo "3. Required Parameters:\n";
echo "   - file: Media file (multipart/form-data)\n";
echo "   - cctv_name: Camera name (string)\n";
echo "   - detection_type: person|vehicle|motion|face|package|animal|object\n";
echo "   - timestamp: ISO date string (optional)\n\n";

echo "4. Storage Format:\n";
echo "   Path: {camera-name}/{yyyy}/{mm}/{dd}/{detection_type}/{original_filename}.{ext}\n";
echo "   Example: camera001/2025/07/01/person/detection_20250701_143022.jpg\n\n";

echo "5. Response Format:\n";
echo "   Success (201):\n";
echo "   {\n";
echo "     \"success\": true,\n";
echo "     \"message\": \"File uploaded successfully\",\n";
echo "     \"data\": {\n";
echo "       \"storage_path\": \"camera001/2025/07/01/person/detection.jpg\",\n";
echo "       \"cctv_name\": \"Camera 001\",\n";
echo "       \"detection_type\": \"person\",\n";
echo "       \"timestamp\": \"2025-07-01T14:30:22.000000Z\",\n";
echo "       \"file_size\": \"2.4 MB\",\n";
echo "       \"original_filename\": \"detection.jpg\"\n";
echo "     }\n";
echo "   }\n\n";

echo "   Error (400/500):\n";
echo "   {\n";
echo "     \"success\": false,\n";
echo "     \"message\": \"Validation failed\",\n";
echo "     \"errors\": { ... }\n";
echo "   }\n\n";

echo "6. Example cURL Command:\n";
echo "   curl -X POST \\\n";
echo "     {$uploadEndpoint} \\\n";
echo "     -F \"file=@/path/to/detection.jpg\" \\\n";
echo "     -F \"cctv_name=Camera 001\" \\\n";
echo "     -F \"detection_type=person\" \\\n";
echo "     -F \"timestamp=2025-07-01T14:30:22Z\"\n\n";

echo "7. PHP Example:\n";
echo "   \$ch = curl_init();\n";
echo "   curl_setopt_array(\$ch, [\n";
echo "     CURLOPT_URL => '{$uploadEndpoint}',\n";
echo "     CURLOPT_POST => true,\n";
echo "     CURLOPT_POSTFIELDS => [\n";
echo "       'file' => new CURLFile('/path/to/detection.jpg'),\n";
echo "       'cctv_name' => 'Camera 001',\n";
echo "       'detection_type' => 'person',\n";
echo "       'timestamp' => '2025-07-01T14:30:22Z'\n";
echo "     ],\n";
echo "     CURLOPT_RETURNTRANSFER => true\n";
echo "   ]);\n";
echo "   \$response = curl_exec(\$ch);\n";
echo "   curl_close(\$ch);\n\n";

echo "8. JavaScript Example (using FormData):\n";
echo "   const formData = new FormData();\n";
echo "   formData.append('file', fileInput.files[0]);\n";
echo "   formData.append('cctv_name', 'Camera 001');\n";
echo "   formData.append('detection_type', 'person');\n";
echo "   formData.append('timestamp', new Date().toISOString());\n\n";
echo "   fetch('{$uploadEndpoint}', {\n";
echo "     method: 'POST',\n";
echo "     body: formData\n";
echo "   })\n";
echo "   .then(response => response.json())\n";
echo "   .then(data => console.log(data));\n\n";

// Try to test the endpoint if possible
echo "9. Testing Endpoint Availability...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => 5
        ]
    ]);
    
    $headers = @get_headers($baseUrl, 1, $context);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "   ✅ Laravel application is accessible at {$baseUrl}\n";
        echo "   ✅ Upload endpoint should be available at {$uploadEndpoint}\n";
    } else {
        echo "   ⚠️  Cannot reach Laravel application at {$baseUrl}\n";
        echo "   💡 Make sure your Laravel app is running (php artisan serve)\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Cannot test endpoint connectivity: " . $e->getMessage() . "\n";
}

echo "\n";

echo "10. Integration Notes:\n";
echo "   • No authentication required - public endpoint for third parties\n";
echo "   • Files are stored in MinIO with structured path format\n";
echo "   • Supports both images and videos up to 100MB\n";
echo "   • Automatic timestamp handling if not provided\n";
echo "   • Camera name is sanitized for safe file path usage\n";
echo "   • Comprehensive logging for debugging and monitoring\n";
echo "   • Returns detailed information about uploaded file\n\n";

echo "✅ CCTV Upload API is ready for third-party integration!\n";
