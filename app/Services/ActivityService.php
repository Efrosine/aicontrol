<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityService
{
    /**
     * Log a scraping activity
     */
    public static function logScrapingActivity(
        string $action,
        string $platform,
        int $count = 0,
        string $status = 'success',
        ?User $user = null
    ): Activity {
        $descriptions = [
            'started' => "Started scraping {$platform} data",
            'completed' => "Completed scraping {$platform} with {$count} items processed",
            'failed' => "Failed to scrape {$platform} data",
        ];

        return Activity::log(
            type: 'scraping',
            action: $action,
            title: ucfirst($action) . " {$platform} Scraping",
            description: $descriptions[$action] ?? "Scraping activity on {$platform}",
            status: $status,
            user: $user,
            metadata: ['platform' => $platform, 'count' => $count]
        );
    }

    /**
     * Log a security activity
     */
    public static function logSecurityActivity(
        string $action,
        string $location,
        string $details = '',
        string $status = 'warning',
        ?Model $related = null
    ): Activity {
        $titles = [
            'detected' => 'Motion Detection Alert',
            'offline' => 'Camera Offline',
            'online' => 'Camera Online',
            'alert' => 'Security Alert',
        ];

        $descriptions = [
            'detected' => "Suspicious activity detected at {$location}",
            'offline' => "Camera connection lost at {$location}",
            'online' => "Camera connection restored at {$location}",
            'alert' => "Security alert triggered at {$location}",
        ];

        if ($details) {
            $descriptions[$action] .= " - {$details}";
        }

        return Activity::log(
            type: 'security',
            action: $action,
            title: $titles[$action] ?? 'Security Event',
            description: $descriptions[$action] ?? "Security event at {$location}",
            status: $status,
            related: $related,
            metadata: ['location' => $location, 'details' => $details]
        );
    }

    /**
     * Log a user activity
     */
    public static function logUserActivity(
        string $action,
        string $subject,
        string $status = 'info',
        ?User $user = null,
        ?Model $related = null
    ): Activity {
        $titles = [
            'created' => "New {$subject} Created",
            'updated' => "{$subject} Updated",
            'deleted' => "{$subject} Deleted",
            'login' => 'User Login',
            'logout' => 'User Logout',
        ];

        $descriptions = [
            'created' => "A new {$subject} has been created",
            'updated' => "A {$subject} has been updated",
            'deleted' => "A {$subject} has been deleted",
            'login' => 'User logged into the system',
            'logout' => 'User logged out of the system',
        ];

        return Activity::log(
            type: 'user',
            action: $action,
            title: $titles[$action] ?? ucfirst($action) . " {$subject}",
            description: $descriptions[$action] ?? "User {$action} for {$subject}",
            status: $status,
            user: $user,
            related: $related,
            metadata: ['subject' => $subject]
        );
    }

    /**
     * Log a system activity
     */
    public static function logSystemActivity(
        string $action,
        string $component,
        string $details = '',
        string $status = 'info'
    ): Activity {
        $titles = [
            'updated' => "System {$component} Updated",
            'backup' => "System Backup",
            'maintenance' => "System Maintenance",
            'restart' => "System Restart",
            'error' => "System Error",
        ];

        $descriptions = [
            'updated' => "System {$component} has been updated",
            'backup' => "System backup completed",
            'maintenance' => "System maintenance performed",
            'restart' => "System has been restarted",
            'error' => "System error occurred",
        ];

        if ($details) {
            $descriptions[$action] = ($descriptions[$action] ?? "System {$action} for {$component}") . " - {$details}";
        }

        return Activity::log(
            type: 'system',
            action: $action,
            title: $titles[$action] ?? "System {$action}",
            description: $descriptions[$action] ?? "System {$action} for {$component}",
            status: $status,
            metadata: ['component' => $component, 'details' => $details]
        );
    }

    /**
     * Get recent activities for dashboard
     */
    public static function getRecentActivities(int $limit = 10)
    {
        return Activity::with('user')
            ->orderBy('occurred_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by type
     */
    public static function getActivitiesByType(string $type, int $limit = 20)
    {
        return Activity::with('user')
            ->where('type', $type)
            ->orderBy('occurred_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity statistics
     */
    public static function getActivityStats()
    {
        return [
            'total_today' => Activity::whereDate('occurred_at', today())->count(),
            'success_today' => Activity::whereDate('occurred_at', today())->where('status', 'success')->count(),
            'warnings_today' => Activity::whereDate('occurred_at', today())->where('status', 'warning')->count(),
            'errors_today' => Activity::whereDate('occurred_at', today())->where('status', 'error')->count(),
            'by_type' => Activity::selectRaw('type, count(*) as count')
                ->whereDate('occurred_at', today())
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }
}
