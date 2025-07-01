<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Log;

class TestDetectionArchive {
    
    private function getMinIOClient()
    {
        $endpoint = config('storage.minio.endpoint');
        $accessKey = config('storage.minio.access_key');
        $secretKey = config('storage.minio.secret_key');
        $useSSL = config('storage.minio.use_ssl', false);
        
        return new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => config('storage.minio.region', 'us-east-1'),
            'endpoint' => ($useSSL ? 'https://' : 'http://') . $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ]);
    }

    public function testMinIOConnection()
    {
        try {
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            echo "=== Testing MinIO Connection ===\n";
            echo "Bucket: $bucket\n";
            echo "Endpoint: " . config('storage.minio.endpoint') . "\n\n";
            
            // Test basic connection
            $result = $client->listObjectsV2([
                'Bucket' => $bucket,
                'MaxKeys' => 5
            ]);
            
            echo "✓ MinIO connection successful\n";
            echo "Found " . (isset($result['Contents']) ? count($result['Contents']) : 0) . " objects\n\n";
            
            // Test specific path
            $testPath = "camera001/2025/06/23/vehicle/";
            echo "=== Testing specific path: $testPath ===\n";
            
            $pathResult = $client->listObjectsV2([
                'Bucket' => $bucket,
                'Prefix' => $testPath
            ]);
            
            if (isset($pathResult['Contents'])) {
                echo "✓ Found " . count($pathResult['Contents']) . " files:\n";
                foreach ($pathResult['Contents'] as $file) {
                    echo "  - " . $file['Key'] . " (" . $file['Size'] . " bytes)\n";
                }
            } else {
                echo "✗ No files found in path\n";
            }
            
            // Test camera discovery
            echo "\n=== Testing camera discovery for 2025-06-23 ===\n";
            $cameraIds = $this->discoverAllCameraIds('2025', '06', '23');
            echo "Discovered cameras: " . implode(', ', $cameraIds) . "\n";
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    private function discoverAllCameraIds($year, $month, $day)
    {
        try {
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            $datePrefix = "{$year}/{$month}/{$day}/";
            $cameraIds = [];
            
            echo "Looking for cameras with date prefix: $datePrefix\n";
            
            $result = $client->listObjectsV2([
                'Bucket' => $bucket,
                'Delimiter' => '/',
                'MaxKeys' => 1000
            ]);
            
            if (isset($result['CommonPrefixes'])) {
                echo "Found " . count($result['CommonPrefixes']) . " top-level folders\n";
                
                foreach ($result['CommonPrefixes'] as $prefix) {
                    $cameraId = rtrim($prefix['Prefix'], '/');
                    echo "  Checking camera: $cameraId\n";
                    
                    $checkPath = "{$cameraId}/{$datePrefix}";
                    $checkResult = $client->listObjectsV2([
                        'Bucket' => $bucket,
                        'Prefix' => $checkPath,
                        'MaxKeys' => 1
                    ]);
                    
                    if (isset($checkResult['Contents']) && count($checkResult['Contents']) > 0) {
                        echo "    ✓ Has files for target date\n";
                        $cameraIds[] = $cameraId;
                    } else {
                        echo "    ✗ No files for target date\n";
                    }
                }
            }
            
            return $cameraIds;
            
        } catch (Exception $e) {
            echo "Error in camera discovery: " . $e->getMessage() . "\n";
            return [];
        }
    }
}

$test = new TestDetectionArchive();
$test->testMinIOConnection();
