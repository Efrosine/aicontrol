<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        $activities = [
            [
                'type' => 'scraping',
                'action' => 'completed',
                'title' => 'Instagram Scraping Completed',
                'description' => 'Social media scraping completed for Instagram with 150 new posts analyzed',
                'status' => 'success',
                'user_id' => $admin?->id,
                'occurred_at' => Carbon::now()->subMinutes(2),
            ],
            [
                'type' => 'security',
                'action' => 'detected',
                'title' => 'Motion Detection Alert',
                'description' => 'Suspicious activity detected on Camera #3 in parking area',
                'status' => 'warning',
                'user_id' => null, // System generated
                'occurred_at' => Carbon::now()->subMinutes(15),
            ],
            [
                'type' => 'user',
                'action' => 'created',
                'title' => 'New Suspected Account Added',
                'description' => 'New suspected account added to monitoring system',
                'status' => 'info',
                'user_id' => $admin?->id,
                'occurred_at' => Carbon::now()->subHours(1),
            ],
            [
                'type' => 'system',
                'action' => 'updated',
                'title' => 'AI Analysis Engine Updated',
                'description' => 'System AI analysis engine updated to version 2.1.5',
                'status' => 'success',
                'user_id' => null,
                'occurred_at' => Carbon::now()->subHours(3),
            ],
            [
                'type' => 'scraping',
                'action' => 'completed',
                'title' => 'Twitter Data Scraping',
                'description' => 'Completed scraping of Twitter data for sentiment analysis',
                'status' => 'success',
                'user_id' => $admin?->id,
                'occurred_at' => Carbon::now()->subHours(5),
            ],
            [
                'type' => 'security',
                'action' => 'offline',
                'title' => 'Camera Connection Lost',
                'description' => 'Connection lost with parking lot camera #5',
                'status' => 'error',
                'user_id' => null,
                'occurred_at' => Carbon::now()->subHours(8),
            ],
            [
                'type' => 'system',
                'action' => 'backup',
                'title' => 'Daily Backup Completed',
                'description' => 'Daily automated backup of system data completed successfully',
                'status' => 'success',
                'user_id' => null,
                'occurred_at' => Carbon::now()->subDays(1),
            ],
            [
                'type' => 'user',
                'action' => 'login',
                'title' => 'Admin Login',
                'description' => 'Administrator logged into the system',
                'status' => 'info',
                'user_id' => $admin?->id,
                'occurred_at' => Carbon::now()->subDays(1)->addHours(2),
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
