<?php
/**
 * Debug script to check MinIO file discovery
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debug MinIO Detection Archive Files ===\n\n";

try {
    // Create MinIO client
    $endpoint = config('storage.minio.endpoint');
    $accessKey = config('storage.minio.access_key');
    $secretKey = config('storage.minio.secret_key');
    $useSSL = config('storage.minio.use_ssl', false);
    $bucket = config('storage.minio.bucket', 'detection-archive');
    
    $client = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region' => config('storage.minio.region', 'us-east-1'),
        'endpoint' => ($useSSL ? 'https://' : 'http://') . $endpoint,
        'use_path_style_endpoint' => true,
        'credentials' => [
            'key' => $accessKey,
            'secret' => $secretKey,
        ],
    ]);

    echo "MinIO Configuration:\n";
    echo "- Endpoint: {$endpoint}\n";
    echo "- Bucket: {$bucket}\n";
    echo "- SSL: " . ($useSSL ? 'Yes' : 'No') . "\n\n";

    // Check specific path: camera001/2025/06/23/vehicle
    $specificPath = "camera001/2025/06/23/vehicle/";
    echo "Checking specific path: {$specificPath}\n";
    
    $result = $client->listObjectsV2([
        'Bucket' => $bucket,
        'Prefix' => $specificPath
    ]);
    
    if (isset($result['Contents']) && count($result['Contents']) > 0) {
        echo "✓ Found " . count($result['Contents']) . " files in {$specificPath}\n";
        foreach ($result['Contents'] as $object) {
            echo "  - {$object['Key']} (Size: " . number_format($object['Size']) . " bytes)\n";
        }
    } else {
        echo "✗ No files found in {$specificPath}\n";
    }
    
    echo "\n";

    // Check what happens with camera discovery for date 2025-06-23
    $year = '2025';
    $month = '06';
    $day = '23';
    
    echo "Testing camera discovery for date: {$year}-{$month}-{$day}\n";
    
    // List all top-level folders (camera IDs)
    $result = $client->listObjectsV2([
        'Bucket' => $bucket,
        'Delimiter' => '/',
        'MaxKeys' => 1000
    ]);
    
    $cameraIds = [];
    if (isset($result['CommonPrefixes'])) {
        echo "Found top-level folders (camera IDs):\n";
        foreach ($result['CommonPrefixes'] as $prefix) {
            $cameraId = rtrim($prefix['Prefix'], '/');
            echo "  - {$cameraId}\n";
            
            // Check if this camera has files for the target date
            $checkPath = "{$cameraId}/{$year}/{$month}/{$day}/";
            echo "    Checking path: {$checkPath}\n";
            
            try {
                $checkResult = $client->listObjectsV2([
                    'Bucket' => $bucket,
                    'Prefix' => $checkPath,
                    'MaxKeys' => 5 // Just check if any files exist
                ]);
                
                if (isset($checkResult['Contents']) && count($checkResult['Contents']) > 0) {
                    $cameraIds[] = $cameraId;
                    echo "    ✓ Has " . count($checkResult['Contents']) . " files for this date\n";
                    
                    // List first few files
                    foreach (array_slice($checkResult['Contents'], 0, 3) as $file) {
                        echo "      - {$file['Key']}\n";
                    }
                } else {
                    echo "    ✗ No files for this date\n";
                }
            } catch (\Exception $e) {
                echo "    ✗ Error checking: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "No top-level folders found\n";
    }
    
    echo "\nDiscovered camera IDs with files for {$year}-{$month}-{$day}: " . implode(', ', $cameraIds) . "\n\n";

    // Test detection type discovery for camera001 on 2025-06-23
    $basePath = "camera001/{$year}/{$month}/{$day}/";
    echo "Testing detection type discovery for path: {$basePath}\n";
    
    $result = $client->listObjectsV2([
        'Bucket' => $bucket,
        'Prefix' => $basePath,
        'Delimiter' => '/'
    ]);
    
    $detectionTypes = [];
    if (isset($result['CommonPrefixes'])) {
        echo "Found detection type folders:\n";
        foreach ($result['CommonPrefixes'] as $prefix) {
            $relativePath = str_replace($basePath, '', $prefix['Prefix']);
            $pathParts = explode('/', trim($relativePath, '/'));
            if (!empty($pathParts[0])) {
                $detectionTypes[] = $pathParts[0];
                echo "  - {$pathParts[0]} (full path: {$prefix['Prefix']})\n";
            }
        }
    } else {
        echo "No detection type folders found\n";
    }
    
    echo "\nDiscovered detection types: " . implode(', ', $detectionTypes) . "\n\n";

    // Test the controller logic
    echo "Testing controller logic simulation...\n";
    $cctvService = new App\Services\CctvService();
    $controller = new App\Http\Controllers\DetectionArchiveController($cctvService);
    
    // Create a mock request
    $request = new Illuminate\Http\Request();
    $request->merge([
        'camera' => 'all',
        'date' => '2025-06-23',
        'detection_type' => 'all',
        'time_range' => 'all'
    ]);
    
    echo "Simulating request with:\n";
    echo "- Camera: all\n";
    echo "- Date: 2025-06-23\n";
    echo "- Detection type: all\n";
    echo "- Time range: all\n\n";
    
    // This would normally call the controller, but we can't easily do that here
    // So let's just recommend checking the actual page
    
    echo "=== Recommendations ===\n";
    echo "1. Check the Detection Archive page with date set to 2025-06-23\n";
    echo "2. Make sure 'Show All Cameras' is selected\n";
    echo "3. Ensure all detection type and time range filters are set to 'All'\n";
    echo "4. Check browser console for any JavaScript errors\n";
    echo "5. Check Laravel logs for any errors: tail -f storage/logs/laravel.log\n\n";
    
    echo "Detection Archive URL: http://localhost:8000/admin/detection-archive?date=2025-06-23&camera=all\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
