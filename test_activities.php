<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test creating an activity
$activity = \App\Services\ActivityService::logSystemActivity(
    'test',
    'Activity System',
    'Testing the activity logging functionality',
    'success'
);

echo "Activity created with ID: " . $activity->id . "\n";

// Test getting recent activities
$recent = \App\Services\ActivityService::getRecentActivities(5);
echo "Found " . $recent->count() . " recent activities\n";

foreach ($recent as $activity) {
    echo "- " . $activity->title . " (" . $activity->time_ago . ")\n";
}
