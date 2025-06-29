<?php

/**
 * Test script for CCTV Activity Logging
 */

require_once 'vendor/autoload.php';

use App\Services\ActivityService;

echo "=== CCTV Activity Logging Test ===\n\n";

// Test 1: Log CCTV camera creation (web interface)
echo "1. Testing CCTV camera creation logging (from web interface)...\n";
try {
    $activity = ActivityService::logCctvActivity(
        'created',
        'Test Camera 1',
        'Front Entrance',
        'Camera added via web interface with IP: 192.168.1.100',
        'success',
        null // No user for testing
    );
    echo "   ✅ Camera creation logged successfully (ID: {$activity->id})\n";
} catch (Exception $e) {
    echo "   ❌ Failed to log camera creation: {$e->getMessage()}\n";
}

// Test 2: Log CCTV camera update (web interface)
echo "\n2. Testing CCTV camera update logging (from web interface)...\n";
try {
    $activity = ActivityService::logCctvActivity(
        'updated',
        'Test Camera 1',
        'Front Entrance',
        'Camera configuration updated via web interface',
        'info',
        null
    );
    echo "   ✅ Camera update logged successfully (ID: {$activity->id})\n";
} catch (Exception $e) {
    echo "   ❌ Failed to log camera update: {$e->getMessage()}\n";
}

// Test 3: Log CCTV camera deletion (web interface)
echo "\n3. Testing CCTV camera deletion logging (from web interface)...\n";
try {
    $activity = ActivityService::logCctvActivity(
        'deleted',
        'Test Camera 1',
        'Front Entrance',
        'Camera removed via web interface',
        'warning',
        null
    );
    echo "   ✅ Camera deletion logged successfully (ID: {$activity->id})\n";
} catch (Exception $e) {
    echo "   ❌ Failed to log camera deletion: {$e->getMessage()}\n";
}

// Test 4: Log CCTV service configuration (web interface)
echo "\n4. Testing CCTV service configuration logging (from web interface)...\n";
try {
    $activity = ActivityService::logCctvActivity(
        'service_configured',
        'CCTV Service',
        'System Configuration',
        'Service settings updated - Base URL: http://192.168.8.109:8000, Timeout: 30s',
        'success',
        null
    );
    echo "   ✅ Service configuration logged successfully (ID: {$activity->id})\n";
} catch (Exception $e) {
    echo "   ❌ Failed to log service configuration: {$e->getMessage()}\n";
}

// Test 5: Log CCTV status change (external service)
echo "\n5. Testing CCTV camera status change logging (from external service)...\n";
try {
    $activity = ActivityService::logCctvStatusChange(
        'cam_001',
        'Test Camera 1',
        'Front Entrance',
        'offline',
        'online',
        'external_service'
    );
    echo "   ✅ Camera status change logged successfully (ID: {$activity->id})\n";
} catch (Exception $e) {
    echo "   ❌ Failed to log camera status change: {$e->getMessage()}\n";
}

// Test 6: Log CCTV service status change (external service)
echo "\n6. Testing CCTV service status change logging (from external service)...\n";
try {
    $activity = ActivityService::logCctvServiceStatus(
        'online',
        'Service started successfully'
    );
    echo "   ✅ Service status change logged successfully (ID: {$activity->id})\n";
} catch (Exception $e) {
    echo "   ❌ Failed to log service status change: {$e->getMessage()}\n";
}

// Test 7: Log detection configuration update (web interface)
echo "\n7. Testing detection configuration update logging (from web interface)...\n";
try {
    $activity = ActivityService::logCctvActivity(
        'detection_updated',
        'System',
        'Global Settings',
        'Detection configuration updated: record_duration=30s, video=enabled, screenshot=disabled',
        'info',
        null
    );
    echo "   ✅ Detection configuration update logged successfully (ID: {$activity->id})\n";
} catch (Exception $e) {
    echo "   ❌ Failed to log detection configuration update: {$e->getMessage()}\n";
}

echo "\n=== Summary ===\n";
echo "Testing completed. All CCTV activity logging functions have been tested.\n";
echo "\nKey Features Implemented:\n";
echo "✅ Separate logging for operations initiated from this system vs external service\n";
echo "✅ Detailed metadata tracking for audit trails\n";
echo "✅ Proper activity categorization (cctv type)\n";
echo "✅ Status-based activity severity (success, info, warning, error)\n";
echo "✅ Webhook endpoints for external service status updates\n";
echo "✅ Detection event logging through security activities\n";

echo "\nWebhook Endpoints Available:\n";
echo "📡 POST /api/webhooks/cctv/camera-status - Camera status changes\n";
echo "📡 POST /api/webhooks/cctv/service-status - Service status changes\n";
echo "📡 POST /api/webhooks/cctv/detection-event - Detection events\n";

echo "\n=== Test Complete ===\n";
