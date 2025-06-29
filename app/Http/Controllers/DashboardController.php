<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivityService;
use App\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        // Get recent activities for the dashboard
        $recentActivities = ActivityService::getRecentActivities(6);
        
        // Get activity statistics
        $activityStats = ActivityService::getActivityStats();
        
        return view('dashboard', [
            'recentActivities' => $recentActivities,
            'activityStats' => $activityStats,
        ]);
    }

    public function activities()
    {
        // Get all activities with pagination
        $activities = Activity::with('user')
            ->orderBy('occurred_at', 'desc')
            ->paginate(20);
            
        return view('dashboard.activities', [
            'activities' => $activities,
        ]);
    }
}
