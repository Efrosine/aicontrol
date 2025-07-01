<?php

// Simple test to check MinIO connection
// Run this with: php test_minio_connection.php

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Testing MinIO Connection...\n";
echo "Endpoint: " . ($_ENV['MINIO_ENDPOINT'] ?? 'Not set') . "\n";
echo "Access Key: " . ($_ENV['MINIO_ACCESS_KEY_ID'] ?? 'Not set') . "\n";
echo "Bucket: " . ($_ENV['MINIO_BUCKET'] ?? 'Not set') . "\n\n";

try {
    // Check if AWS SDK is available
    if (!class_exists('Aws\S3\S3Client')) {
        echo "❌ AWS S3 SDK not found. Please install it with:\n";
        echo "composer require aws/aws-sdk-php\n";
        exit(1);
    }
    
    $endpoint = $_ENV['MINIO_ENDPOINT'] ?? 'localhost:9000';
    $accessKey = $_ENV['MINIO_ACCESS_KEY_ID'] ?? 'minioadmin';
    $secretKey = $_ENV['MINIO_SECRET_ACCESS_KEY'] ?? 'minioadmin123';
    $bucket = $_ENV['MINIO_BUCKET'] ?? 'detection-archive';
    
    // Create S3 client for MinIO
    $client = new Aws\S3\S3Client([
        'version' => 'latest',
        'region' => 'us-east-1',
        'endpoint' => 'http://' . $endpoint,
        'use_path_style_endpoint' => true,
        'credentials' => [
            'key' => $accessKey,
            'secret' => $secretKey,
        ],
    ]);
    
    echo "✅ S3 Client created successfully\n";
    
    // Test connection by listing buckets
    echo "Testing connection by listing buckets...\n";
    $result = $client->listBuckets();
    echo "✅ Connection successful!\n";
    
    $buckets = $result['Buckets'] ?? [];
    echo "Found " . count($buckets) . " buckets:\n";
    foreach ($buckets as $bucket) {
        echo "  - " . $bucket['Name'] . "\n";
    }
    
    // Check if our target bucket exists
    $bucketExists = false;
    foreach ($buckets as $bucketInfo) {
        if ($bucketInfo['Name'] === $bucket) {
            $bucketExists = true;
            break;
        }
    }
    
    if ($bucketExists) {
        echo "✅ Target bucket '$bucket' exists\n";
    } else {
        echo "⚠️ Target bucket '$bucket' does not exist\n";
        echo "Creating bucket...\n";
        $client->createBucket(['Bucket' => $bucket]);
        echo "✅ Bucket created successfully\n";
    }
    
    echo "\nMinIO setup is working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure MinIO is running on " . $endpoint . "\n";
}
